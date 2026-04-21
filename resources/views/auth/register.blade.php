<x-guest-layout>
<x-slot name="subtitle">{{ request('role') === 'tipster' ? 'Join as a Tipster' : 'Create your free account' }}</x-slot>

@php $activeRole = old('role', request('role', 'bettor')); @endphp

{{-- Role tabs --}}
<div style="display:flex;border-bottom:1px solid var(--border);margin-bottom:1.5rem;gap:0">
    <a href="{{ route('register', ['role' => 'bettor']) }}"
       style="flex:1;text-align:center;padding:.6rem;font-size:.8rem;font-weight:600;letter-spacing:.05em;text-decoration:none;border-bottom:2px solid {{ $activeRole === 'bettor' ? 'var(--accent)' : 'transparent' }};color:{{ $activeRole === 'bettor' ? 'var(--accent)' : 'var(--muted)' }};transition:color .15s">
         Get more tips!
    </a>
    <a href="{{ route('register', ['role' => 'tipster']) }}"
       style="flex:1;text-align:center;padding:.6rem;font-size:.8rem;font-weight:600;letter-spacing:.05em;text-decoration:none;border-bottom:2px solid {{ $activeRole === 'tipster' ? 'var(--accent)' : 'transparent' }};color:{{ $activeRole === 'tipster' ? 'var(--accent)' : 'var(--muted)' }};transition:color .15s">
         TIPSTER -Share your predictions!
    </a>
</div>

{{-- Role description --}}
@if($activeRole === 'tipster')
<div style="background:rgba(0,229,160,.06);border:1px solid rgba(0,229,160,.25);border-radius:6px;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.78rem;color:var(--muted);line-height:1.6">
    <strong style="color:var(--accent)">Tipster Account</strong> — Share your football predictions with the SCOUT community.
    Your account will be reviewed by our admin team before you can publish tips.
</div>
@else
<div style="background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:6px;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.78rem;color:var(--muted);line-height:1.6">
    <strong style="color:var(--text)">Bettor Account</strong> — Get instant access to AI-powered football tips, confidence scores, and match analysis.
</div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf
    <input type="hidden" name="role" value="{{ $activeRole }}">

    {{-- Name --}}
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    {{-- Email --}}
    <div class="mt-4">
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    {{-- Password --}}
    <div class="mt-4">
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    {{-- Confirm Password --}}
    <div class="mt-4">
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    {{-- Tipster-only fields --}}
    @if($activeRole === 'tipster')
    <div class="mt-4">
        <x-input-label for="tipster_speciality" value="Your Speciality" />
        <x-text-input id="tipster_speciality" class="block mt-1 w-full" type="text" name="tipster_speciality"
            :value="old('tipster_speciality')" required placeholder="e.g. Premier League, Over/Under" />
        <p style="font-size:.7rem;color:var(--muted);margin-top:.3rem">Markets or leagues you specialise in</p>
        <x-input-error :messages="$errors->get('tipster_speciality')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="tipster_bio" value="Short Bio" />
        <textarea id="tipster_bio" name="tipster_bio" rows="3" required maxlength="500"
            style="width:100%;background:var(--surface);border:1px solid var(--border);border-radius:6px;color:var(--text);padding:.5rem .75rem;font-size:.85rem;font-family:inherit;resize:vertical"
            placeholder="Tell us about your betting experience and approach...">{{ old('tipster_bio') }}</textarea>
        <p style="font-size:.7rem;color:var(--muted);margin-top:.3rem">Max 500 characters. Your profile will be reviewed before approval.</p>
        <x-input-error :messages="$errors->get('tipster_bio')" class="mt-2" />
    </div>
    @endif

    <div class="flex items-center justify-end mt-6">
        <a style="font-size:.79rem;color:var(--muted);text-decoration:underline" href="{{ route('login') }}">
            {{ __('Already registered?') }}
        </a>
        <x-primary-button class="ms-4">
            @if($activeRole === 'tipster') Apply as Tipster @else Create Account @endif
        </x-primary-button>
    </div>
</form>

</x-guest-layout>

