<a
    {{
        $attributes->merge([
            'class' => 'transition duration-300 ease-in-out hover:scale-[1.2] hover:text-blue-500'
        ])
    }}
>
    {{ $slot }}
</a>
