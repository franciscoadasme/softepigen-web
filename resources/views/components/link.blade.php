@props([
    'href',
])

<a
    {{ $attributes->merge(['class' => 'inline-block hover:text-secondary hover:underline active:text-[oklch(from_var(--color-secondary)_calc(l-0.1)_c_h)]']) }}
    href="{{ $href }}"
>
    {{ $slot }}
</a>
