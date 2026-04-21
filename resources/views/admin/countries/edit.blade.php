<x-admin-layout title="Edit Country">
    <x-slot name="breadcrumb"><a href="{{ route('admin.countries.index') }}" style="color:var(--accent);text-decoration:none">Countries</a> / {{ $country->name }}</x-slot>

    <div style="max-width:520px">
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <form method="POST" action="{{ route('admin.countries.update', $country) }}">
                @csrf @method('PUT')

                <div class="admin-form-group">
                    <label class="admin-label">Country Name *</label>
                    <input type="text" name="name" class="admin-input" value="{{ old('name', $country->name) }}" required>
                    @error('name')<div style="color:#ef4444;font-size:.75rem;margin-top:.3rem">{{ $message }}</div>@enderror
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">ISO Code</label>
                    <input type="text" name="code" class="admin-input" value="{{ old('code', $country->code) }}" maxlength="3" placeholder="e.g. GB">
                    @error('code')<div style="color:#ef4444;font-size:.75rem;margin-top:.3rem">{{ $message }}</div>@enderror
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Flag Image URL</label>
                    <input type="url" name="flag_url" class="admin-input" value="{{ old('flag_url', $country->flag_url) }}" placeholder="https://...">
                    @error('flag_url')<div style="color:#ef4444;font-size:.75rem;margin-top:.3rem">{{ $message }}</div>@enderror
                </div>

                <div class="admin-form-group" style="display:flex;align-items:center;gap:.75rem">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $country->is_active) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--accent)">
                    <label for="is_active" class="admin-label" style="margin:0">Active</label>
                </div>

                <div style="display:flex;gap:.75rem;margin-top:1.25rem">
                    <button type="submit" class="btn-primary">Update Country</button>
                    <a href="{{ route('admin.countries.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
