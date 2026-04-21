<button {{ $attributes->merge(['type' => 'submit', 'class' => 'scout-btn scout-btn-danger']) }}>
    {{ $slot }}
</button>
