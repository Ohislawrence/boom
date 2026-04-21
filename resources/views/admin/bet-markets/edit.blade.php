<x-admin-layout title="Edit Bet Market">
    <x-slot name="breadcrumb"><a href="{{ route('admin.bet-markets.index') }}" style="color:var(--accent);text-decoration:none">Bet Markets</a> / {{ $betMarket->name }}</x-slot>

    <div style="max-width:560px">
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <form method="POST" action="{{ route('admin.bet-markets.update', $betMarket) }}">
                @csrf @method('PUT')

                <div class="admin-form-group">
                    <label class="admin-label">Name *</label>
                    <input type="text" name="name" class="admin-input" value="{{ old('name', $betMarket->name) }}" required>
                    @error('name')<div style="color:#ef4444;font-size:.75rem;margin-top:.3rem">{{ $message }}</div>@enderror
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Slug *</label>
                    <input type="text" name="slug" class="admin-input" value="{{ old('slug', $betMarket->slug) }}" required>
                    @error('slug')<div style="color:#ef4444;font-size:.75rem;margin-top:.3rem">{{ $message }}</div>@enderror
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Category</label>
                    <input type="text" name="category" class="admin-input" value="{{ old('category', $betMarket->category) }}" placeholder="e.g. Match, Goals, Cards">
                    @error('category')<div style="color:#ef4444;font-size:.75rem;margin-top:.3rem">{{ $message }}</div>@enderror
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Description</label>
                    <textarea name="description" class="admin-textarea" rows="3">{{ old('description', $betMarket->description) }}</textarea>
                    @error('description')<div style="color:#ef4444;font-size:.75rem;margin-top:.3rem">{{ $message }}</div>@enderror
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Sort Order</label>
                    <input type="number" name="sort_order" class="admin-input" value="{{ old('sort_order', $betMarket->sort_order) }}" min="0" style="width:120px">
                </div>

                <div class="admin-form-group" style="display:flex;align-items:center;gap:.75rem">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $betMarket->is_active) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--accent)">
                    <label for="is_active" class="admin-label" style="margin:0">Active</label>
                </div>

                <div style="display:flex;gap:.75rem;margin-top:1.25rem">
                    <button type="submit" class="btn-primary">Update Market</button>
                    <a href="{{ route('admin.bet-markets.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
