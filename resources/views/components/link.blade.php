@props([
    'href',
])

<a
    {{ $attributes->merge(['class' => 'inline-block hover:text-blue-500 hover:underline active:text-blue-600']) }}
    href="{{ $href }}"
>
    {{ $slot }}
</a>
