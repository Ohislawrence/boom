<x-tipster-layout title="Submit a Tip">

    <div style="max-width:640px">
        <form method="POST" action="{{ route('tipster.tips.store') }}">
            @csrf

            {{-- Step 1: Pick fixture --}}
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1rem">
                <div style="font-family:var(--fh);font-size:.9rem;letter-spacing:.08em;color:var(--text);margin-bottom:.9rem">1 · Choose Match</div>

                <div class="ts-form-group">
                    <label class="ts-label">Upcoming Fixture *</label>
                    <select name="fixture_id" class="ts-select" required
                            onchange="document.getElementById('market-section').style.display='block'">
                        <option value="">— select match —</option>
                        @foreach($fixtures as $fixture)
                        <option value="{{ $fixture->id }}" {{ old('fixture_id') == $fixture->id ? 'selected' : '' }}>
                            {{ $fixture->match_date->format('d M H:i') }} · {{ $fixture->home_team }} vs {{ $fixture->away_team }}
                            @if($fixture->league) ({{ $fixture->league->name }})@endif
                        </option>
                        @endforeach
                    </select>
                    @if($fixtures->isEmpty())
                    <div style="font-size:.75rem;color:var(--muted);margin-top:.4rem">No upcoming fixtures in the database yet. An admin needs to run the fixture import first.</div>
                    @endif
                    @error('fixture_id')<div style="font-size:.72rem;color:#ef4444;margin-top:.25rem">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Step 2: Tip details --}}
            <div id="market-section" style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1rem">
                <div style="font-family:var(--fh);font-size:.9rem;letter-spacing:.08em;color:var(--text);margin-bottom:.9rem">2 · Tip Details</div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="ts-form-group">
                        <label class="ts-label">Market *</label>
                        <select name="bet_market_id" class="ts-select"
                                onchange="if(this.value) document.querySelector('[name=market]').value = this.options[this.selectedIndex].text">
                            <option value="">— select preset or type below —</option>
                            @foreach($markets as $market)
                            <option value="{{ $market->id }}" {{ old('bet_market_id') == $market->id ? 'selected' : '' }}>
                                {{ $market->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ts-form-group">
                        <label class="ts-label">Market Label *</label>
                        <input type="text" name="market" class="ts-input"
                               value="{{ old('market') }}"
                               placeholder="e.g. Match Result, Both Teams to Score"
                               required>
                        @error('market')<div style="font-size:.72rem;color:#ef4444;margin-top:.25rem">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="ts-form-group">
                    <label class="ts-label">Selection *</label>
                    <input type="text" name="selection" class="ts-input"
                           value="{{ old('selection') }}"
                           placeholder="e.g. Arsenal Win, Both Teams to Score – Yes, Over 2.5"
                           required>
                    @error('selection')<div style="font-size:.72rem;color:#ef4444;margin-top:.25rem">{{ $message }}</div>@enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="ts-form-group">
                        <label class="ts-label">Best Available Odds</label>
                        <input type="number" name="odds" class="ts-input" step="0.01" min="1.01"
                               value="{{ old('odds') }}" placeholder="e.g. 2.10">
                        @error('odds')<div style="font-size:.72rem;color:#ef4444;margin-top:.25rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="ts-form-group">
                        <label class="ts-label">Confidence (1–100) *</label>
                        <input type="number" name="confidence" class="ts-input" min="1" max="100"
                               value="{{ old('confidence', 70) }}" required>
                        <div style="font-size:.7rem;color:var(--muted);margin-top:.2rem">70+ = high confidence</div>
                        @error('confidence')<div style="font-size:.72rem;color:#ef4444;margin-top:.25rem">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="ts-form-group" style="display:flex;align-items:center;gap:.6rem">
                    <input type="hidden" name="is_value_bet" value="0">
                    <input type="checkbox" name="is_value_bet" id="is_value_bet" value="1"
                           {{ old('is_value_bet') ? 'checked' : '' }}
                           style="accent-color:var(--accent2);width:16px;height:16px">
                    <label for="is_value_bet" style="font-size:.85rem;color:var(--text);cursor:pointer">
                        Value bet — odds are better than my calculated probability
                    </label>
                </div>
            </div>

            {{-- Step 3: Reasoning --}}
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1rem">
                <div style="font-family:var(--fh);font-size:.9rem;letter-spacing:.08em;color:var(--text);margin-bottom:.9rem">3 · Reasoning <span style="font-size:.65rem;color:var(--muted);font-family:var(--fp)">(optional but recommended)</span></div>

                <div class="ts-form-group">
                    <textarea name="reasoning" class="ts-textarea" style="min-height:140px"
                              placeholder="Explain your reasoning: form, injuries, head-to-head, tactical notes...">{{ old('reasoning') }}</textarea>
                    <div style="font-size:.7rem;color:var(--muted);margin-top:.3rem">Good reasoning improves approval chances and builds your reputation.</div>
                    @error('reasoning')<div style="font-size:.72rem;color:#ef4444;margin-top:.25rem">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:flex;gap:.75rem;align-items:center">
                <button type="submit" class="btn-submit">Submit for Review</button>
                <a href="{{ route('tipster.dashboard') }}" class="btn-secondary">Cancel</a>
                <span style="font-size:.72rem;color:var(--muted)">Tips are reviewed by an admin before publishing.</span>
            </div>
        </form>
    </div>

</x-tipster-layout>
