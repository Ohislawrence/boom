@props(['active'])

@php
$classes = ($active ?? false)
            ? 'scout-nav-link active'
            : 'scout-nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
