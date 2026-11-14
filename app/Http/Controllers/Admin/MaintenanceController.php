<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends AdminController
{
    public function index()
    {
        return view('admin.maintenance', [
            'info' => [
                'Laravel' => app()->version(),
                'PHP' => PHP_VERSION,
                'Environment' => app()->environment(),
                'Debug mode' => config('app.debug') ? 'ON (disable in production!)' : 'Off',
                'Database' => config('database.default').' / '.DB::connection()->getDatabaseName(),
                'Cache driver' => config('cache.default'),
                'Mail driver' => config('mail.default'),
                'GD / WebP' => function_exists('imagewebp') ? 'Available' : 'Not available',
                'Uploads folder writable' => is_writable(public_path('uploads')) ? 'Yes' : 'No',
            ],
        ]);
    }

    public function clearCache()
    {
        Cache::flush();
        Setting::flush();
        foreach (['view:clear', 'route:clear', 'config:clear'] as $cmd) {
            try {
                Artisan::call($cmd);
            } catch (\Throwable $e) {
            }
        }
        $this->log('maintenance', 'Cleared application cache');

        return back()->with('success', 'All caches cleared.');
    }

    /** Apply pending database migrations after a Git deploy (no SSH needed). */
    public function migrate()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $out = trim(Artisan::output());
            Cache::flush();
            Setting::flush();
            $this->log('maintenance', 'Ran database migrations');

            return back()->with('success', str_contains($out, 'Nothing to migrate') ? 'Database is already up to date.' : 'Database updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Migration failed: '.$e->getMessage());
        }
    }

    public function toggle()
    {
        $on = ! setting_on('maintenance_mode');
        Setting::put('maintenance_mode', $on ? '1' : '0', 'maintenance');
        $this->log('maintenance', 'Maintenance mode '.($on ? 'enabled' : 'disabled'));

        return back()->with('success', 'Maintenance mode '.($on ? 'enabled' : 'disabled').'.');
    }

    /** Streams a plain SQL dump of the application tables (structure + data). */
    public function backup()
    {
        $this->log('maintenance', 'Downloaded database backup');
        $pdo = DB::connection()->getPdo();
        $driver = DB::connection()->getDriverName();

        return response()->streamDownload(function () use ($pdo, $driver) {
            echo '-- MK Securex database backup '.now()->toDateTimeString()."\n";
            echo "SET FOREIGN_KEY_CHECKS=0;\n\n";
            $tables = $driver === 'sqlite'
                ? array_column(DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"), 'name')
                : array_map(fn ($r) => array_values((array) $r)[0], DB::select('SHOW TABLES'));

            foreach ($tables as $table) {
                if (in_array($table, ['sessions', 'cache', 'cache_locks', 'jobs', 'failed_jobs', 'job_batches'], true)) {
                    continue;
                }
                if ($driver !== 'sqlite') {
                    $create = (array) DB::selectOne("SHOW CREATE TABLE `$table`");
                    echo "DROP TABLE IF EXISTS `$table`;\n".array_values($create)[1].";\n\n";
                }
                foreach (DB::table($table)->cursor() as $row) {
                    $vals = array_map(fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v), (array) $row);
                    echo "INSERT INTO `$table` (`".implode('`,`', array_keys((array) $row)).'`) VALUES ('.implode(',', $vals).");\n";
                }
                echo "\n";
            }
            echo "SET FOREIGN_KEY_CHECKS=1;\n";
        }, 'mksecurex-backup-'.now()->format('Y-m-d-His').'.sql', ['Content-Type' => 'application/sql']);
    }
}
