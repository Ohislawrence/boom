<x-admin-layout title="Edit: {{ $league->name }}">
    <x-slot name="breadcrumb">Leagues / {{ $league->name }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.leagues.index') }}" class="btn-secondary">← Back</a>
    </x-slot>

    <div style="max-width:600px">
        <form method="POST" action="{{ route('admin.leagues.update', $league) }}">
            @csrf @method('PUT')

            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1rem">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="admin-form-group">
                        <label class="admin-label">Name *</label>
                        <input type="text" name="name" class="admin-input" value="{{ old('name', $league->name) }}" required>
                        @error('name')<div style="font-size:.72rem;color:#ef4444;margin-top:.25rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Country</label>
                        <select name="country_id" class="admin-select">
                            <option value="">— Select country —</option>
                            @foreach($countries as $c)
                            <option value="{{ $c->id }}" {{ old('country_id', $league->country_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                            @endforeach
                        </select>
                        @if($league->country && !$league->country_id)
                        <div style="font-size:.7rem;color:var(--accent2);margin-top:.3rem">API value: "{{ $league->country }}" (unmatched)</div>
                        @endif
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="admin-form-group">
                        <label class="admin-label">Season</label>
                        <input type="text" name="season" class="admin-input" value="{{ old('season', $league->season) }}">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">API-Football League ID</label>
                        <input type="number" name="api_football_id" class="admin-input" value="{{ old('api_football_id', $league->api_football_id) }}">
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Logo URL</label>
                    <input type="url" name="logo_url" class="admin-input" value="{{ old('logo_url', $league->logo_url) }}">
                </div>

                <div class="admin-form-group" style="display:flex;align-items:center;gap:.6rem">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ $league->is_active ? 'checked' : '' }}
                           style="accent-color:var(--accent);width:16px;height:16px">
                    <label for="is_active" style="font-size:.85rem;color:var(--text);cursor:pointer">Active (included in daily analysis)</label>
                </div>
            </div>

            <div style="display:flex;gap:.75rem">
                <button type="submit" class="btn-primary">Update League</button>
                <a href="{{ route('admin.leagues.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</x-admin-layout>
