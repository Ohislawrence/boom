<x-admin-layout title="Edit Tip">
    <x-slot name="breadcrumb">
        <a href="{{ route('admin.tips.index') }}" style="color:var(--accent);text-decoration:none">Tips</a>
        / <a href="{{ route('admin.tips.show', $tip) }}" style="color:var(--accent);text-decoration:none">{{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}</a>
        / Edit
    </x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.tips.show', $tip) }}" class="btn-secondary btn-sm">← View</a>
    </x-slot>

    <div style="display:grid;grid-template-columns:1fr 380px;gap:1.25rem;max-width:1000px" class="admin-two-col">

        {{-- ── Left: Edit prediction ── --}}
        <div>
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1rem">
                <div style="font-family:var(--fh);font-size:.95rem;letter-spacing:.07em;color:var(--text);margin-bottom:1rem;padding-bottom:.6rem;border-bottom:1px solid var(--border)">
                    Edit Prediction
                    @if($tip->is_ai_generated)<span class="badge badge-green" style="margin-left:.5rem">AI</span>@endif
                </div>

                <form method="POST" action="{{ route('admin.tips.update', $tip) }}">
                    @csrf @method('PUT')

                    {{-- Match context (read-only) --}}
                    <div style="background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.82rem;color:var(--muted)">
                        ⚽ <strong style="color:var(--text)">{{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}</strong>
                        &nbsp;·&nbsp; {{ $tip->fixture->local_match_date->format('D d M Y, H:i') }}
                        @if($tip->fixture->league)
                        &nbsp;·&nbsp; {{ $tip->fixture->league->name }}
                        @endif
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                        <div class="admin-form-group">
                            <label class="admin-label">Market *</label>
                            <input type="text" name="market" class="admin-input" value="{{ old('market', $tip->market) }}" required placeholder="e.g. Match Result">
                            @error('market')<div style="color:#ef4444;font-size:.72rem;margin-top:.25rem">{{ $message }}</div>@enderror
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-label">Bet Market</label>
                            <select name="bet_market_id" class="admin-select">
                                <option value="">— None —</option>
                                @foreach($betMarkets as $bm)
                                <option value="{{ $bm->id }}" {{ old('bet_market_id', $tip->bet_market_id) == $bm->id ? 'selected' : '' }}>{{ $bm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label">Selection *</label>
                        <input type="text" name="selection" class="admin-input" value="{{ old('selection', $tip->selection) }}" required placeholder="e.g. Home Win / Over 2.5">
                        @error('selection')<div style="color:#ef4444;font-size:.72rem;margin-top:.25rem">{{ $message }}</div>@enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
                        <div class="admin-form-group">
                            <label class="admin-label">Odds</label>
                            <input type="number" name="odds" class="admin-input" step="0.01" min="1" value="{{ old('odds', $tip->odds) }}" placeholder="e.g. 1.85">
                            @error('odds')<div style="color:#ef4444;font-size:.72rem;margin-top:.25rem">{{ $message }}</div>@enderror
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-label">Confidence (0–100)</label>
                            <input type="number" name="confidence" class="admin-input" min="0" max="100" value="{{ old('confidence', $tip->confidence) }}" required>
                            @error('confidence')<div style="color:#ef4444;font-size:.72rem;margin-top:.25rem">{{ $message }}</div>@enderror
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-label">Status</label>
                            <select name="status" class="admin-select">
                                @foreach(['pending' => 'Pending', 'published' => 'Published', 'rejected' => 'Rejected'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $tip->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="admin-form-group" style="display:flex;align-items:center;gap:.75rem">
                        <input type="hidden" name="is_value_bet" value="0">
                        <input type="checkbox" name="is_value_bet" id="is_value_bet" value="1" {{ old('is_value_bet', $tip->is_value_bet) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--accent2)">
                        <label for="is_value_bet" class="admin-label" style="margin:0">Flag as Value Bet</label>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label">AI Reasoning</label>
                        <textarea name="reasoning" class="admin-textarea" rows="6" placeholder="AI-generated reasoning...">{{ old('reasoning', $tip->reasoning) }}</textarea>
                        @error('reasoning')<div style="color:#ef4444;font-size:.72rem;margin-top:.25rem">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn-primary">Save Changes</button>
                </form>
            </div>
        </div>

        {{-- ── Right: Set Result ── --}}
        <div>
            {{-- Current result --}}
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.2rem;margin-bottom:1rem">
                <div style="font-family:var(--fh);font-size:.95rem;letter-spacing:.07em;color:var(--text);margin-bottom:.85rem;padding-bottom:.6rem;border-bottom:1px solid var(--border)">
                    Set Result
                </div>

                @if($tip->tipResult)
                @php $res = $tip->tipResult; @endphp
                <div style="background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:.7rem;margin-bottom:.85rem;display:flex;align-items:center;gap:.75rem">
                    <span style="font-size:1.4rem">{{ $res->result === 'win' ? '✅' : ($res->result === 'loss' ? '❌' : '↩️') }}</span>
                    <div>
                        <div style="font-family:var(--fm);font-weight:700;color:var(--text)">{{ strtoupper($res->result) }}</div>
                        @if($res->profit_loss !== null)
                        <div style="font-size:.75rem;color:{{ $res->profit_loss >= 0 ? 'var(--accent)' : '#ef4444' }}">P&L: {{ $res->profit_loss >= 0 ? '+' : '' }}{{ number_format($res->profit_loss,2) }} units</div>
                        @endif
                        <div style="font-size:.7rem;color:var(--muted)">Resolved {{ $res->resolved_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
                @else
                <div style="font-size:.8rem;color:var(--muted);margin-bottom:.85rem">No result set yet — match may still be upcoming or in progress.</div>
                @endif

                <form method="POST" action="{{ route('admin.tips.set-result', $tip) }}">
                    @csrf @method('PATCH')

                    <div class="admin-form-group">
                        <label class="admin-label">Result</label>
                        <div style="display:flex;gap:.5rem">
                            @foreach(['win' => ['badge-green','✓ Win'], 'loss' => ['badge-red','✗ Loss'], 'void' => ['badge-gray','↩ Void']] as $val => [$cls, $lbl])
                            <label style="flex:1;cursor:pointer">
                                <input type="radio" name="result" value="{{ $val }}" {{ old('result', $tip->tipResult?->result) === $val ? 'checked' : '' }} style="display:none" class="result-radio">
                                <div class="badge {{ $cls }} result-btn" style="display:block;text-align:center;padding:.4rem;cursor:pointer;border-radius:5px;font-size:.8rem">{{ $lbl }}</div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label">Closing Odds (optional)</label>
                        <input type="number" name="closing_odds" class="admin-input" step="0.01" min="1" value="{{ old('closing_odds', $tip->tipResult?->closing_odds) }}" placeholder="{{ $tip->odds ? number_format($tip->odds,2) : '—' }}">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label">Notes</label>
                        <textarea name="notes" class="admin-textarea" rows="2" placeholder="Optional override note...">{{ old('notes', $tip->tipResult?->notes) }}</textarea>
                    </div>

                    <button type="submit" class="btn-primary" style="width:100%">Set Result</button>
                </form>
            </div>

            {{-- Quick fixture score --}}
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1rem">
                <div style="font-size:.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:.6rem">Fixture Score</div>
                @if(!is_null($tip->fixture->score_home))
                <div style="font-family:var(--fm);font-size:1.5rem;text-align:center;color:var(--text)">
                    {{ $tip->fixture->score_home }} – {{ $tip->fixture->score_away }}
                </div>
                <div style="text-align:center;font-size:.72rem;color:var(--muted);margin-top:.25rem">Final Score</div>
                @else
                <div style="font-size:.82rem;color:var(--muted);text-align:center">Score not available yet</div>
                @endif
                <div style="font-size:.72rem;color:var(--muted);text-align:center;margin-top:.5rem">
                    Status: <span class="badge {{ $tip->fixture->status === 'FT' ? 'badge-green' : 'badge-gray' }}">{{ $tip->fixture->status }}</span>
                </div>
            </div>
        </div>

    </div>

    <style>
        .result-radio:checked + .result-btn { outline: 2px solid var(--accent); opacity: 1; }
        .result-btn { opacity: .6; transition: opacity .15s; }
        .result-btn:hover { opacity: 1; }
    </style>

</x-admin-layout>
