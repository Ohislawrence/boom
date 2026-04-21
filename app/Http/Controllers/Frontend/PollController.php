<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class PollController extends Controller
{
    /**
     * Vote on a fixture poll.
     *
     * Bot protection layers:
     *   1. CSRF token (middleware — rejects all non-browser/non-JS fetch requests)
     *   2. Rate limiting — 5 votes per IP per minute across all polls
     *   3. Honeypot field — bots fill it, humans don't
     *   4. Voter hash — SHA-256(IP + UA + fixture_id) stored in fixture_poll_votes;
     *      one vote per browser fingerprint per fixture forever
     */
    public function vote(Request $request, Fixture $fixture): JsonResponse
    {
        // ── Honeypot ───────────────────────────────────────────────────────────
        // The "website" field is hidden from real users (CSS display:none + aria-hidden).
        // Bots that fill in forms indiscriminately will trigger this.
        if ($request->filled('website')) {
            return response()->json(['error' => 'rejected'], 422);
        }

        // ── Rate limiting ──────────────────────────────────────────────────────
        $limiterKey = 'poll:' . $request->ip();
        if (RateLimiter::tooManyAttempts($limiterKey, 5)) {
            return response()->json(['error' => 'Too many requests. Try again in a minute.'], 429);
        }
        RateLimiter::hit($limiterKey, 60);

        // ── Input validation ───────────────────────────────────────────────────
        $validated = $request->validate([
            'choice' => ['required', 'in:home,draw,away'],
        ]);

        $choice = $validated['choice'];

        // ── Duplicate-vote prevention ──────────────────────────────────────────
        // Hash is deterministic but irreversible — we never store raw IPs.
        $voterHash = hash('sha256',
            $request->ip() .
            $request->userAgent() .
            $fixture->id
        );

        $alreadyVoted = DB::table('fixture_poll_votes')
            ->where('fixture_id', $fixture->id)
            ->where('voter_hash', $voterHash)
            ->exists();

        if ($alreadyVoted) {
            // Still return current counts so the UI can show results
            return response()->json(array_merge(
                ['already_voted' => true],
                $this->counts($fixture->id)
            ));
        }

        // ── Record vote atomically ─────────────────────────────────────────────
        DB::transaction(function () use ($fixture, $choice, $voterHash) {
            // Upsert the aggregate row
            DB::table('fixture_polls')->upsert(
                ['fixture_id' => $fixture->id, $choice . '_votes' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['fixture_id'],
                [$choice . '_votes' => DB::raw($choice . '_votes + 1'), 'updated_at' => now()]
            );

            // Record the individual vote hash
            DB::table('fixture_poll_votes')->insert([
                'fixture_id' => $fixture->id,
                'voter_hash' => $voterHash,
                'choice'     => $choice,
                'voted_at'   => now(),
            ]);
        });

        return response()->json(array_merge(
            ['success' => true, 'your_choice' => $choice],
            $this->counts($fixture->id)
        ));
    }

    /**
     * Return current poll counts for a fixture (no auth, read-only).
     */
    public function results(Fixture $fixture): JsonResponse
    {
        return response()->json($this->counts($fixture->id));
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function counts(int $fixtureId): array
    {
        $row = DB::table('fixture_polls')
            ->where('fixture_id', $fixtureId)
            ->first();

        $home  = (int) ($row->home_votes ?? 0);
        $draw  = (int) ($row->draw_votes ?? 0);
        $away  = (int) ($row->away_votes ?? 0);
        $total = $home + $draw + $away;

        return [
            'home'       => $home,
            'draw'       => $draw,
            'away'       => $away,
            'total'      => $total,
            'home_pct'   => $total ? round($home / $total * 100) : 33,
            'draw_pct'   => $total ? round($draw / $total * 100) : 34,
            'away_pct'   => $total ? round($away / $total * 100) : 33,
        ];
    }
}
