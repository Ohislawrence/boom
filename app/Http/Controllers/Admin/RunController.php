<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\RunAnalysisJob;
use App\Models\DailyRunLog;
use App\Models\Tip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class RunController extends Controller
{
    public function index()
    {
        $logs = DailyRunLog::orderByDesc('run_date')->paginate(30);

        $pendingCount  = Tip::where('status', 'published')->where('result', 'pending')
                            ->whereHas('fixture', fn ($q) => $q->where('match_date', '<=', now()))
                            ->count();

        return view('admin.run-control.index', compact('logs', 'pendingCount'));
    }

    public function triggerAnalysis(Request $request)
    {
        $request->validate([
            'date'       => 'nullable|date_format:Y-m-d',
            'days_ahead' => 'nullable|integer|min:0|max:7',
            'force'      => 'nullable|boolean',
        ]);

        $date  = $request->filled('date')
            ? $request->date
            : now()->addDays((int) ($request->days_ahead ?? 1))->toDateString();

        $force = $request->boolean('force');

        RunAnalysisJob::dispatch($date, false, false, $force);

        return back()->with('success', "Full analysis for {$date} has been queued.");
    }

    public function triggerFetch(Request $request)
    {
        $request->validate([
            'date'       => 'nullable|date_format:Y-m-d',
            'days_ahead' => 'nullable|integer|min:0|max:7',
        ]);

        $date = $request->filled('date')
            ? $request->date
            : now()->addDays((int) ($request->days_ahead ?? 1))->toDateString();

        RunAnalysisJob::dispatch($date, true, false, false);

        return back()->with('success', "Fixture fetch for {$date} has been queued.");
    }

    public function triggerAnalyseOnly(Request $request)
    {
        $request->validate([
            'date'       => 'nullable|date_format:Y-m-d',
            'days_ahead' => 'nullable|integer|min:0|max:7',
            'force'      => 'nullable|boolean',
        ]);

        $date  = $request->filled('date')
            ? $request->date
            : now()->addDays((int) ($request->days_ahead ?? 1))->toDateString();

        $force = $request->boolean('force');

        RunAnalysisJob::dispatch($date, false, true, $force);

        return back()->with('success', "AI analysis for {$date} has been queued.");
    }

    public function triggerResolve(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:14',
        ]);

        $exitCode = Artisan::call('scout:resolve-results', ['--days' => $request->days]);
        $output   = Artisan::output();

        $status = $exitCode === 0 ? 'success' : 'error';
        $msg    = $exitCode === 0
            ? "Result resolution completed (last {$request->days} days)."
            : "Resolution command returned errors — check logs.";

        return back()->with($status, $msg)->with('artisan_output', $output);
    }
}
