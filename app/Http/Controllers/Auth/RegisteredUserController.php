<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'bettor') === 'tipster' ? 'tipster' : 'bettor';

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($role === 'tipster') {
            $rules['tipster_bio']         = ['required', 'string', 'max:500'];
            $rules['tipster_speciality']  = ['required', 'string', 'max:150'];
        }

        $request->validate($rules);

        $user = User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'password'           => Hash::make($request->password),
            'registration_role'  => $role,
            'tipster_bio'        => $role === 'tipster' ? $request->tipster_bio : null,
            'tipster_speciality' => $role === 'tipster' ? $request->tipster_speciality : null,
            'approval_status'    => $role === 'tipster' ? 'pending' : 'approved',
        ]);

        // Assign Spatie role
        $user->assignRole($role);

        event(new Registered($user));

        // Send welcome notification
        $user->notify(new WelcomeNotification($role));

        Auth::login($user);

        if ($role === 'tipster') {
            return redirect(route('dashboard'))
                ->with('info', 'Your tipster application has been submitted and is awaiting admin approval. You\'ll be notified once reviewed.');
        }

        return redirect(route('dashboard'))
            ->with('success', 'Welcome to SCOUT! Your account is ready.');
    }
}
