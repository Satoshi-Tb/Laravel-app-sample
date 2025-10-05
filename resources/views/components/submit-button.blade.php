<button type="submit"
    {{
        $attributes->merge([
            'class' => 'p-3 text-sm text-white bg-blue-500 rounded-sm shadow-md shadow-gray-300 cursor-pointer hover:bg-blue-600 transition duration-300 ease-in-out'
        ])
    }}
>
    {{ $slot }}
</button>
