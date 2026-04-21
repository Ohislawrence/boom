<x-app-layout>
    <x-slot name="header">
        {{ __('Profile') }}
    </x-slot>

    <div class="scout-wrap" style="max-width:860px">
        <div class="scout-card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="scout-card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="scout-card">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>

