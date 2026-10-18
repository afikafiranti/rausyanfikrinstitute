<button {{ $attributes->merge(['type' => 'submit', 'class' => 'rf-btn-danger']) }}>
    {{ $slot }}
</button>
