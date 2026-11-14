<?php
/**
 * MK Securex – one-time web installer for hosting without SSH/Composer (e.g. Hostinger Git deploy).
 *
 * 1. Checks server requirements.
 * 2. Tests the MySQL connection.
 * 3. Writes .env (with a fresh APP_KEY) outside the web root.
 * 4. Runs migrations + seeders and creates the first admin user.
 * 5. Locks itself (storage/app/installed.lock) so it can never run again.
 */

declare(strict_types=1);

$base = dirname(__DIR__);
$lock = $base.'/storage/app/installed.lock';
$envFile = $base.'/.env';

session_start();
header('X-Robots-Tag: noindex, nofollow');
header('X-Frame-Options: DENY');

function h($v): string { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

function envValue(string $v): string
{
    if ($v === '') return '';
    if (preg_match('/^[A-Za-z0-9_.:\/@+\-]+$/', $v)) return $v;
    return '"'.str_replace(['\\', '"', '$'], ['\\\\', '\\"', '\\$'], $v).'"';
}

function setEnv(string $env, string $key, string $value): string
{
    $line = $key.'='.envValue($value);
    if (preg_match('/^#?\s*'.preg_quote($key, '/').'=.*$/m', $env)) {
        return preg_replace('/^#?\s*'.preg_quote($key, '/').'=.*$/m', str_replace(['\\', '$'], ['\\\\', '\\$'], $line), $env, 1);
    }
    return rtrim($env)."\n".$line."\n";
}

$installed = file_exists($lock);
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ? 'https' : 'http';
$guessUrl = $scheme.'://'.($_SERVER['HTTP_HOST'] ?? 'localhost');

// ---------- Requirements ----------
$checks = [
    'PHP 8.2 or newer (current: '.PHP_VERSION.')' => version_compare(PHP_VERSION, '8.2.0', '>='),
    'PDO MySQL extension' => extension_loaded('pdo_mysql'),
    'Mbstring extension' => extension_loaded('mbstring'),
    'OpenSSL extension' => extension_loaded('openssl'),
    'Fileinfo extension' => extension_loaded('fileinfo'),
    'Vendor folder present' => file_exists($base.'/vendor/autoload.php'),
    'storage/ writable' => is_writable($base.'/storage'),
    'bootstrap/cache/ writable' => is_writable($base.'/bootstrap/cache'),
    'public/uploads/ writable' => is_writable(__DIR__.'/uploads'),
    'Project folder writable (for .env)' => is_writable($base) || (file_exists($envFile) && is_writable($envFile)),
];
$optional = ['GD extension (image WebP optimisation)' => function_exists('imagewebp')];
$allOk = !in_array(false, $checks, true);

$errors = [];
$done = false;
$log = '';
$old = $_POST;

if (!$installed && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['install_token'] ?? '', $_POST['_token'] ?? '')) {
        $errors[] = 'Session expired. Please submit the form again.';
    }
    $in = fn ($k, $d = '') => trim((string) ($_POST[$k] ?? $d));
    $appUrl = rtrim($in('app_url', $guessUrl), '/');
    $db = ['host' => $in('db_host', 'localhost'), 'port' => $in('db_port', '3306'), 'name' => $in('db_name'), 'user' => $in('db_user'), 'pass' => (string) ($_POST['db_pass'] ?? '')];
    $adminEmail = $in('admin_email');
    $adminPass = (string) ($_POST['admin_password'] ?? '');

    if (!$allOk) $errors[] = 'Fix the server requirements first.';
    if (!filter_var($appUrl, FILTER_VALIDATE_URL)) $errors[] = 'Website URL is not valid.';
    if ($db['name'] === '' || $db['user'] === '') $errors[] = 'Database name and username are required.';
    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Admin email is not valid.';
    if (strlen($adminPass) < 8 || !preg_match('/[A-Za-z]/', $adminPass) || !preg_match('/\d/', $adminPass)) $errors[] = 'Admin password must be at least 8 characters with letters and numbers.';
    if ($adminPass !== (string) ($_POST['admin_password_confirmation'] ?? '')) $errors[] = 'Admin passwords do not match.';

    if (!$errors) {
        try {
            new PDO("mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4", $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 8]);
        } catch (Throwable $e) {
            $errors[] = 'Could not connect to the database: '.$e->getMessage();
        }
    }

    if (!$errors) {
        try {
            $env = file_exists($base.'/.env.example') ? file_get_contents($base.'/.env.example') : '';
            $values = [
                'APP_NAME' => 'MK Securex',
                'APP_ENV' => 'production',
                'APP_KEY' => 'base64:'.base64_encode(random_bytes(32)),
                'APP_DEBUG' => 'false',
                'APP_URL' => $appUrl,
                'LOG_LEVEL' => 'error',
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $db['host'], 'DB_PORT' => $db['port'], 'DB_DATABASE' => $db['name'], 'DB_USERNAME' => $db['user'], 'DB_PASSWORD' => $db['pass'],
                'SESSION_DRIVER' => 'database', 'CACHE_STORE' => 'database', 'QUEUE_CONNECTION' => 'sync',
                'SESSION_SECURE_COOKIE' => str_starts_with($appUrl, 'https://') ? 'true' : 'false',
            ];
            if ($in('mail_host') !== '') {
                $values += ['MAIL_MAILER' => 'smtp', 'MAIL_HOST' => $in('mail_host'), 'MAIL_PORT' => $in('mail_port', '465'),
                    'MAIL_USERNAME' => $in('mail_username'), 'MAIL_PASSWORD' => (string) ($_POST['mail_password'] ?? ''),
                    'MAIL_FROM_ADDRESS' => $in('mail_from', $in('mail_username'))];
            } else {
                $values['MAIL_MAILER'] = 'log';
            }
            foreach ($values as $k => $v) {
                $env = setEnv($env, $k, $v);
            }
            // admin credentials are only used for seeding – never stored in .env
            $env = preg_replace('/^(# First admin account.*|ADMIN_EMAIL=.*|ADMIN_PASSWORD=.*)\R?/m', '', $env);
            if (file_put_contents($envFile, $env) === false) {
                throw new RuntimeException('Could not write the .env file.');
            }
            @chmod($envFile, 0640);

            foreach (['ADMIN_EMAIL' => $adminEmail, 'ADMIN_PASSWORD' => $adminPass] as $k => $v) {
                putenv("$k=$v"); $_ENV[$k] = $v; $_SERVER[$k] = $v;
            }

            require $base.'/vendor/autoload.php';
            $app = require $base.'/bootstrap/app.php';
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();

            $kernel->call('migrate', ['--force' => true]);
            $log .= $kernel->output();
            $kernel->call('db:seed', ['--force' => true]);
            $log .= $kernel->output();

            // Make sure the chosen admin can log in (also when the user already existed).
            $super = App\Models\Role::where('slug', 'super-admin')->first();
            $user = App\Models\User::firstOrNew(['email' => $adminEmail]);
            $user->fill(['name' => $user->name ?: 'Super Admin', 'password' => $adminPass, 'role_id' => $super?->id, 'is_active' => true])->save();
            App\Models\Setting::put('admin_email', $adminEmail, 'general');
            Illuminate\Support\Facades\Cache::flush();

            file_put_contents($lock, 'Installed '.date('c').' from '.($_SERVER['REMOTE_ADDR'] ?? '?'));
            unset($_SESSION['install_token']);
            $done = true;
            $installed = true;
        } catch (Throwable $e) {
            $errors[] = 'Installation failed: '.$e->getMessage();
        }
    }
}

if (!$installed) {
    $_SESSION['install_token'] = $_SESSION['install_token'] ?? bin2hex(random_bytes(20));
}
if ($installed && !$done) {
    http_response_code(403);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>MK Securex – Website Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #06173a, #0b3d91); min-height: 100vh; font-family: system-ui, sans-serif; padding: 40px 16px; }
        .box { max-width: 760px; margin: 0 auto; background: #fff; border-radius: 18px; box-shadow: 0 30px 80px rgba(0,0,0,.35); overflow: hidden; }
        .box-head { padding: 24px 28px; border-bottom: 4px solid #e8a317; display: flex; gap: 16px; align-items: center; }
        .box-head img { height: 60px; }
        .box-body { padding: 28px; }
        h1 { font-size: 1.4rem; margin: 0; color: #06173a; }
        h2 { font-size: 1rem; text-transform: uppercase; letter-spacing: .08em; color: #0b3d91; margin: 26px 0 12px; }
        .btn-go { background: linear-gradient(135deg, #ffc93c, #e8a317); border: 0; color: #1d1300; font-weight: 700; padding: 12px; }
        .ok { color: #198754; } .bad { color: #dc3545; }
    </style>
</head>
<body>
<div class="box">
    <div class="box-head">
        <img src="images/logo.jpg" alt="MK Securex">
        <div><h1>Website Installer</h1><div class="text-muted small">MK Securex Pvt. Ltd. – Laravel CMS</div></div>
    </div>
    <div class="box-body">
    <?php if ($done): ?>
        <div class="alert alert-success"><strong>Installation complete!</strong> The database is ready and your admin account has been created. This installer is now locked.</div>
        <p class="d-flex gap-2 flex-wrap">
            <a class="btn btn-primary" href="admin/login">Open Admin Panel</a>
            <a class="btn btn-outline-primary" href="./">View Website</a>
        </p>
        <p class="small text-muted mb-0">Next: in the admin panel update Contact Information, WhatsApp number and upload real photos.</p>
    <?php elseif ($installed): ?>
        <div class="alert alert-info mb-3">The website is already installed. For security this installer is locked.</div>
        <a class="btn btn-primary" href="admin/login">Go to Admin Login</a>
    <?php else: ?>
        <?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= h($e) ?></div><?php endforeach; ?>

        <h2 class="mt-0">1. Server check</h2>
        <ul class="list-unstyled small mb-0">
            <?php foreach ($checks as $label => $ok): ?><li class="<?= $ok ? 'ok' : 'bad' ?>"><?= $ok ? '✔' : '✘' ?> <?= h($label) ?></li><?php endforeach; ?>
            <?php foreach ($optional as $label => $ok): ?><li class="<?= $ok ? 'ok' : 'text-warning' ?>"><?= $ok ? '✔' : '!' ?> <?= h($label) ?> <?= $ok ? '' : '(optional)' ?></li><?php endforeach; ?>
        </ul>
        <?php if (!$allOk): ?><div class="alert alert-warning small mt-2 mb-0">Fix the items marked ✘ (e.g. select PHP 8.2+ in hPanel → Advanced → PHP Configuration) and reload this page.</div><?php endif; ?>

        <form method="post" autocomplete="off">
            <input type="hidden" name="_token" value="<?= h($_SESSION['install_token']) ?>">
            <h2>2. Website</h2>
            <label class="form-label">Website URL</label>
            <input class="form-control" name="app_url" value="<?= h($old['app_url'] ?? $guessUrl) ?>" required>
            <div class="form-text">Use https:// once SSL is active on your domain.</div>

            <h2>3. MySQL database <small class="text-muted text-lowercase">(hPanel → Databases → MySQL Databases)</small></h2>
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label">Host</label><input class="form-control" name="db_host" value="<?= h($old['db_host'] ?? 'localhost') ?>" required></div>
                <div class="col-md-4"><label class="form-label">Port</label><input class="form-control" name="db_port" value="<?= h($old['db_port'] ?? '3306') ?>" required></div>
                <div class="col-md-12"><label class="form-label">Database name</label><input class="form-control" name="db_name" value="<?= h($old['db_name'] ?? '') ?>" placeholder="u123456789_mksecurex" required></div>
                <div class="col-md-6"><label class="form-label">Database username</label><input class="form-control" name="db_user" value="<?= h($old['db_user'] ?? '') ?>" required></div>
                <div class="col-md-6"><label class="form-label">Database password</label><input class="form-control" type="password" name="db_pass" autocomplete="new-password"></div>
            </div>

            <h2>4. Admin account</h2>
            <div class="row g-3">
                <div class="col-md-12"><label class="form-label">Admin email</label><input class="form-control" type="email" name="admin_email" value="<?= h($old['admin_email'] ?? '') ?>" required></div>
                <div class="col-md-6"><label class="form-label">Admin password</label><input class="form-control" type="password" name="admin_password" minlength="8" required autocomplete="new-password"></div>
                <div class="col-md-6"><label class="form-label">Confirm password</label><input class="form-control" type="password" name="admin_password_confirmation" minlength="8" required autocomplete="new-password"></div>
            </div>

            <h2>5. Email for enquiry notifications <small class="text-muted text-lowercase">(optional – can be added later)</small></h2>
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label">SMTP host</label><input class="form-control" name="mail_host" value="<?= h($old['mail_host'] ?? '') ?>" placeholder="smtp.hostinger.com"></div>
                <div class="col-md-4"><label class="form-label">Port</label><input class="form-control" name="mail_port" value="<?= h($old['mail_port'] ?? '465') ?>"></div>
                <div class="col-md-6"><label class="form-label">SMTP username (email)</label><input class="form-control" name="mail_username" value="<?= h($old['mail_username'] ?? '') ?>"></div>
                <div class="col-md-6"><label class="form-label">SMTP password</label><input class="form-control" type="password" name="mail_password" autocomplete="new-password"></div>
                <div class="col-md-12"><label class="form-label">From address</label><input class="form-control" name="mail_from" value="<?= h($old['mail_from'] ?? '') ?>" placeholder="info@yourdomain.com"></div>
            </div>

            <button class="btn btn-go w-100 mt-4" <?= $allOk ? '' : 'disabled' ?> onclick="this.innerText='Installing… please wait'">Install Website</button>
        </form>
    <?php endif; ?>
    </div>
</div>
</body>
</html>
