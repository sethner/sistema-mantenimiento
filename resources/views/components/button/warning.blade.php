@props(['type' => 'button'])

<button type="{{ $type }}"
    {{ $attributes->merge([
        'class' =>
            'px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 cursor-pointer',
    ]) }}>
    {{ $slot }}
</button>
