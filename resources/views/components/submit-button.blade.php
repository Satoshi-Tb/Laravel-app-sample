@php
    $colorClass = $attributes->get('color', 'blue');

    $base = [
        'p-3',
        'text-sm',
        'text-white',
        'rounded-sm',
        'shadow-md',
        'shadow-gray-300',
        'cursor-pointer',
        'transition',
        'duration-300',
        'ease-in-out',
    ];

    $colorMap = [
        'blue' => ['bg-blue-500', 'hover:bg-blue-600'],
        'red' => ['bg-red-500', 'hover:bg-red-600'],
        'gray' => ['bg-gray-400', 'hover:bg-gray-500'],
    ];

    $colorClasses = $colorMap[$colorClass] ?? $colorMap['blue'];

    $classes = implode(' ', array_merge($base, $colorClasses));
@endphp

<button type="submit"
    {{
        $attributes->except('color')->merge([
            'class' => $classes,
        ])
    }}
>
    {{ $slot }}
</button>
