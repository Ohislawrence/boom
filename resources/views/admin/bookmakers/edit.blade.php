<x-admin-layout title="Edit: {{ $bookmaker->name }}">
    <x-slot name="breadcrumb">Bookmakers / {{ $bookmaker->name }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.bookmakers.index') }}" class="btn-secondary">← Back</a>
    </x-slot>

    <div style="max-width:760px">
        <form method="POST" action="{{ route('admin.bookmakers.update', $bookmaker) }}">
            @csrf @method('PUT')

            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1rem">
                <div style="font-family:var(--fh);font-size:1rem;letter-spacing:.06em;color:var(--text);margin-bottom:1rem">Basic Info</div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="admin-form-group">
                        <label class="admin-label">Name *</label>
                        <input type="text" name="name" class="admin-input" value="{{ old('name', $bookmaker->name) }}" required>
                        @error('name')<div style="font-size:.72rem;color:#ef4444;margin-top:.25rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Affiliate URL *</label>
                        <input type="url" name="affiliate_url" class="admin-input" value="{{ old('affiliate_url', $bookmaker->affiliate_url) }}" required>
                        @error('affiliate_url')<div style="font-size:.72rem;color:#ef4444;margin-top:.25rem">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="admin-form-group">
                        <label class="admin-label">Welcome Offer</label>
                        <input type="text" name="welcome_offer" class="admin-input" value="{{ old('welcome_offer', $bookmaker->welcome_offer) }}">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Bonus Text</label>
                        <input type="text" name="bonus_text" class="admin-input" value="{{ old('bonus_text', $bookmaker->bonus_text) }}">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="admin-form-group">
                        <label class="admin-label">Rating (0–5)</label>
                        <input type="number" name="rating" class="admin-input" value="{{ old('rating', $bookmaker->rating) }}" min="0" max="5" step="0.1" required>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Sort Order</label>
                        <input type="number" name="sort_order" class="admin-input" value="{{ old('sort_order', $bookmaker->sort_order) }}">
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Full Review</label>
                    <textarea name="review" class="admin-textarea" style="min-height:140px">{{ old('review', $bookmaker->review) }}</textarea>
                </div>

                <div class="admin-form-group" style="display:flex;align-items:center;gap:.6rem">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ $bookmaker->is_active ? 'checked' : '' }}
                           style="accent-color:var(--accent);width:16px;height:16px">
                    <label for="is_active" style="font-size:.85rem;color:var(--text);cursor:pointer">Active (visible on site)</label>
                </div>
            </div>

            {{-- Bet markets --}}
            @if($markets->isNotEmpty())
            @php $selectedMarkets = $bookmaker->betMarkets->pluck('id')->toArray(); @endphp
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1rem">
                <div style="font-family:var(--fh);font-size:1rem;letter-spacing:.06em;color:var(--text);margin-bottom:.85rem">Supported Bet Markets</div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.5rem">
                    @foreach($markets as $market)
                    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;padding:.35rem .5rem;border-radius:4px;border:1px solid {{ in_array($market->id, $selectedMarkets) ? 'var(--accent)' : 'var(--border)' }};font-size:.82rem;color:var(--text)">
                        <input type="checkbox" name="markets[]" value="{{ $market->id }}"
                               style="accent-color:var(--accent)"
                               {{ in_array($market->id, old('markets', $selectedMarkets)) ? 'checked' : '' }}>
                        {{ $market->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <div style="display:flex;gap:.75rem;align-items:center">
                <button type="submit" class="btn-primary">Update Bookmaker</button>
                <a href="{{ route('admin.bookmakers.index') }}" class="btn-secondary">Cancel</a>
                <form method="POST" action="{{ route('admin.bookmakers.destroy', $bookmaker) }}" style="margin-left:auto" onsubmit="return confirm('Delete {{ $bookmaker->name }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger">Delete Bookmaker</button>
                </form>
            </div>
        </form>
    </div>

</x-admin-layout>
