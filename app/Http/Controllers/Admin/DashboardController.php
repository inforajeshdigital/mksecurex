<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Models\Enquiry;
use App\Models\Industry;
use App\Models\Page;
use App\Models\Service;

class DashboardController extends AdminController
{
    public function index()
    {
        $base = Enquiry::where('is_spam', false);
        $stats = [
            'total' => (clone $base)->count(),
            'new' => (clone $base)->where('status', 'new')->count(),
            'contacted' => (clone $base)->whereIn('status', ['contacted', 'converted'])->count(),
            'services' => Service::count(),
            'industries' => Industry::count(),
            'pages' => Page::published()->count(),
        ];

        // Enquiries over the last 14 days for the mini chart
        $days = collect(range(13, 0))->map(fn ($d) => now()->subDays($d)->format('Y-m-d'));
        $counts = (clone $base)->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')->groupBy('d')->pluck('c', 'd');
        $chart = $days->map(fn ($d) => ['label' => date('d M', strtotime($d)), 'value' => (int) ($counts[$d] ?? 0)]);

        return view('admin.dashboard', [
            'stats' => $stats,
            'chart' => $chart,
            'recent' => (clone $base)->latest()->limit(8)->get(),
            'activity' => ActivityLog::with('user')->latest()->limit(10)->get(),
        ]);
    }

    public function activity()
    {
        return view('admin.activity', ['logs' => ActivityLog::with('user')->latest()->paginate(30)]);
    }
}
