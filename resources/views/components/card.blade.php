<div
    {{ $attributes->merge(['class' => 'mx-auto w-[52rem] rounded-xl border border-slate-200 bg-white p-5']) }}
>
    @isset($header)
        {{ $header }}
    @endisset

    <h2 class="mb-6 text-2xl font-semibold">{{ $title }}</h2>
    {{ $slot }}

    @isset($footer)
        <p class="mt-5 text-center text-sm text-slate-400">
            {{ $footer }}
        </p>
    @endisset
</div>
