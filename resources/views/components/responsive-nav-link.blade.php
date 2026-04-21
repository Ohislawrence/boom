@props(['active'])

@php
$classes = ($active ?? false)
            ? 'scout-mobile-item active'
            : 'scout-mobile-item';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
