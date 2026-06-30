@props(['href' => '#'])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-900',
    ]) }}>
    {{ $slot }}
</a>
