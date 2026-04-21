<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->get('role', 'all');

        $query = User::with('roles')->latest();

        if ($role !== 'all') {
            $query->role($role);
        }

        $users = $query->paginate(30);
        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.users.index', compact('users', 'roles', 'role'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|exists:roles,name']);

        $user->syncRoles([$request->role]);

        return back()->with('success', "Role updated for {$user->name}.");
    }
}
