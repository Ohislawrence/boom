<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="scout-wrap">
        <div class="scout-card">
            <div class="scout-inner-card">
                {{ __("You're logged in!") }}
            </div>
        </div>
    </div>
</x-app-layout>

