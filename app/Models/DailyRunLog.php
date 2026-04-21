<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRunLog extends Model
{
    protected $fillable = [
        'run_date',
        'fixtures_fetched',
        'tips_generated',
        'status',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'run_date'     => 'date',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ── Helpers ──

    /**
     * A running log is considered stale if it has been running for longer than
     * the maximum possible batch duration (all fixtures × job timeout).
     * After 6 hours the worker has certainly died and the log will never self-resolve.
     */
    public function isStale(): bool
    {
        return $this->status === 'running'
            && $this->started_at !== null
            && $this->started_at->lt(now()->subHours(6));
    }

    public function markCompleted(int $fixtures, int $tips): void
    {
        $this->update([
            'status'           => 'completed',
            'fixtures_fetched' => $fixtures,
            'tips_generated'   => $tips,
            'completed_at'     => now(),
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status'        => 'failed',
            'error_message' => $error,
            'completed_at'  => now(),
        ]);
    }
}
