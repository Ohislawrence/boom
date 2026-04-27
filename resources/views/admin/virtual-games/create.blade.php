<x-admin-layout title="Add Virtual Game">
    <x-slot name="breadcrumb">Create a new JS game upload</x-slot>

    <form method="POST" action="{{ route('admin.virtual-games.store') }}" enctype="multipart/form-data" style="max-width:860px">
        @csrf
        <div style="display:grid;gap:1.25rem">
            <div class="admin-form-group">
                <label class="admin-label">Name *</label>
                <input type="text" name="name" class="admin-input" value="{{ old('name') }}" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-label">Tagline</label>
                <input type="text" name="tagline" class="admin-input" value="{{ old('tagline') }}">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="admin-form-group">
                    <label class="admin-label">Provider</label>
                    <input type="text" name="provider" class="admin-input" value="{{ old('provider') }}">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Volatility</label>
                    <input type="text" name="volatility" class="admin-input" value="{{ old('volatility') }}">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="admin-form-group">
                    <label class="admin-label">RTP</label>
                    <input type="text" name="rtp" class="admin-input" value="{{ old('rtp') }}" placeholder="98.0%">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Icon</label>
                    <input type="text" name="icon" class="admin-input" value="{{ old('icon') }}" placeholder="🎲">
                </div>
            </div>
            <div class="admin-form-group">
                <label class="admin-label">Color gradient</label>
                <input type="text" name="color" class="admin-input" value="{{ old('color') }}" placeholder="linear-gradient(135deg, #7c3aed, #2563eb)">
            </div>
            <div class="admin-form-group">
                <label class="admin-label">Description</label>
                <textarea name="description" class="admin-textarea">{{ old('description') }}</textarea>
            </div>
            <div class="admin-form-group">
                <label class="admin-label">Game script (JS)</label>
                <input type="file" name="script" class="admin-input">
                <p style="font-size:.78rem;color:var(--muted);margin-top:.35rem">Uploaded JS should expose <code>window.initVirtualGame(canvasId)</code> and accept a canvas ID.</p>
            </div>
            <div class="admin-form-group">
                <label class="admin-label">Game package (ZIP)</label>
                <input type="file" name="package" class="admin-input">
                <p style="font-size:.78rem;color:var(--muted);margin-top:.35rem">Upload a ZIP bundle containing your JS entry file and any required assets.</p>
            </div>
            <div class="admin-form-group">
                <label class="admin-label">Game assets</label>
                <input type="file" name="assets[]" class="admin-input" multiple>
                <p style="font-size:.78rem;color:var(--muted);margin-top:.35rem">Upload images, sounds, JSON, or other asset files. These are stored alongside the script so relative imports in JS work.</p>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="admin-form-group">
                    <label class="admin-label">Sort order</label>
                    <input type="number" name="sort_order" class="admin-input" value="{{ old('sort_order', 0) }}">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Active</label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                </div>
            </div>
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center">
                <button type="submit" class="btn-primary">Save Game</button>
                <a href="{{ route('admin.virtual-games.index') }}" class="btn-secondary btn-sm">Cancel</a>
            </div>
        </div>
    </form>
</x-admin-layout>
