@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'scout-input']) }}>
