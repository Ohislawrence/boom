@props(['value'])

<label {{ $attributes->merge(['class' => 'scout-label']) }}>
    {{ $value ?? $slot }}
</label>
