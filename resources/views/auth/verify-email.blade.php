<x-guest-layout>
    <p style="margin-bottom:1.2rem;font-size:.82rem;color:var(--muted)">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </p>
        <p style="margin-bottom:1rem;font-size:.78rem;color:var(--accent);font-family:var(--fm)">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </p>

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" style="background:none;border:none;cursor:pointer;font-family:var(--fm);font-size:.75rem;color:var(--muted);text-decoration:underline">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
