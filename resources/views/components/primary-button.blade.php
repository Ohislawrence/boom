<button {{ $attributes->merge(['type' => 'submit', 'class' => 'scout-btn scout-btn-primary']) }}>
    {{ $slot }}
</button>
