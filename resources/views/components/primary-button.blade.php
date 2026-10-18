<button {{ $attributes->merge(['type' => 'submit', 'class' => 'rf-btn']) }}>
    {{ $slot }}
</button>
