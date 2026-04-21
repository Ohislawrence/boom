<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BetMarket;
use App\Models\Tip;
use App\Models\TipResult;
use Illuminate\Http\Request;

class TipController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $query  = Tip::with(['fixture.league', 'betMarket', 'submittedBy'])
                     ->orderByDesc('created_at');
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        $tips = $query->paginate(40);
        return view('admin.tips.index', compact('tips', 'status'));
    }

    public function show(Tip $tip)
    {
        $tip->load(['fixture.league', 'betMarket', 'tipResult', 'submittedBy']);
        return view('admin.tips.show', compact('tip'));
    }

    public function edit(Tip $tip)
    {
        $tip->load(['fixture.league', 'betMarket', 'tipResult']);
        $betMarkets = BetMarket::orderBy('name')->get();
        return view('admin.tips.edit', compact('tip', 'betMarkets'));
    }

    public function update(Request $request, Tip $tip)
    {
        $data = $request->validate([
            'market'        => 'required|string|max:100',
            'selection'     => 'required|string|max:100',
            'odds'          => 'nullable|numeric|min:1',
            'confidence'    => 'required|integer|min:0|max:100',
            'bet_market_id' => 'nullable|exists:bet_markets,id',
            'is_value_bet'  => 'boolean',
            'reasoning'     => 'nullable|string|max:5000',
            'status'        => 'required|in:pending,published,rejected',
        ]);

        $data['is_value_bet'] = $request->boolean('is_value_bet');

        $tip->update($data);

        return redirect()->route('admin.tips.show', $tip)
            ->with('success', 'Tip updated.');
    }

    public function setResult(Request $request, Tip $tip)
    {
        $data = $request->validate([
            'result'       => 'required|in:win,loss,void',
            'closing_odds' => 'nullable|numeric|min:1',
            'notes'        => 'nullable|string|max:1000',
        ]);

        // Calculate P&L (1 unit stake basis)
        $profitLoss = match ($data['result']) {
            'win'  => round(($tip->odds ?? 1) - 1, 2),
            'loss' => -1.00,
            'void' => 0.00,
        };

        TipResult::updateOrCreate(
            ['tip_id' => $tip->id],
            [
                'result'       => $data['result'],
                'closing_odds' => $data['closing_odds'] ?? null,
                'profit_loss'  => $profitLoss,
                'notes'        => $data['notes'] ?? null,
                'resolved_at'  => now(),
            ]
        );

        // Keep tip result field in sync
        $tip->update(['result' => $data['result']]);

        return redirect()->route('admin.tips.show', $tip)
            ->with('success', 'Result set to ' . strtoupper($data['result']) . '.');
    }

    public function publish(Tip $tip)
    {
        $tip->update(['status' => 'published']);
        return back()->with('success', 'Tip published.');
    }

    public function reject(Tip $tip)
    {
        $tip->update(['status' => 'rejected']);
        return back()->with('success', 'Tip rejected.');
    }

    public function destroy(Tip $tip)
    {
        $tip->delete();
        return redirect()->route('admin.tips.index')->with('success', 'Tip deleted.');
    }
}
