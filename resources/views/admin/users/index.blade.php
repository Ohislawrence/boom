<x-admin-layout title="User Management">
    <x-slot name="breadcrumb">All registered users</x-slot>

    <x-slot name="actions">
        <div style="display:flex;gap:.4rem">
            @foreach(['all' => 'All', 'admin' => 'Admins', 'tipster' => 'Tipsters', 'bettor' => 'Bettors'] as $r => $label)
            <a href="{{ route('admin.users.index', ['role' => $r]) }}"
               class="{{ $role === $r ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </x-slot>

    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role(s)</th>
                    <th>Verified</th>
                    <th>Joined</th>
                    <th>Change Role</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="font-family:var(--fm);font-size:.75rem;color:var(--muted)">{{ $user->id }}</td>
                    <td>
                        <div style="font-weight:600;font-size:.88rem">{{ $user->name }}</div>
                    </td>
                    <td style="font-size:.83rem;color:var(--muted)">{{ $user->email }}</td>
                    <td>
                        @forelse($user->roles as $r)
                            @if($r->name === 'admin')
                            <span class="badge badge-green">{{ $r->name }}</span>
                            @elseif($r->name === 'tipster')
                            <span class="badge badge-yellow">{{ $r->name }}</span>
                            @else
                            <span class="badge badge-gray">{{ $r->name }}</span>
                            @endif
                        @empty
                            <span class="badge badge-gray">—</span>
                        @endforelse
                    </td>
                    <td>
                        @if($user->email_verified_at)
                        <span class="badge badge-green">✓</span>
                        @else
                        <span class="badge badge-red">✗</span>
                        @endif
                    </td>
                    <td style="font-size:.78rem;color:var(--muted)">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        @if(auth()->id() !== $user->id)
                        <form method="POST" action="{{ route('admin.users.role', $user) }}" style="display:flex;gap:.35rem;align-items:center">
                            @csrf @method('PATCH')
                            <select name="role" class="admin-select" style="padding:.25rem .5rem;font-size:.77rem;width:auto">
                                @foreach($roles as $r)
                                <option value="{{ $r }}" {{ $user->hasRole($r) ? 'selected' : '' }}>{{ $r }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn-primary btn-sm">Save</button>
                        </form>
                        @else
                        <span style="font-size:.75rem;color:var(--muted)">You</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div style="margin-top:1rem">{{ $users->appends(request()->query())->links() }}</div>
    @endif

</x-admin-layout>
