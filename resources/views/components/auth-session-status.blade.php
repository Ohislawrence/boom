@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'scout-info-box']) }}>
        {{ $status }}
    </div>
@endif
