<a
    {{
        $attributes->merge([
            'class' => 'transition duration-300 ease-in-out'
        ])
    }}
>
    {{ $slot }}
</a>
