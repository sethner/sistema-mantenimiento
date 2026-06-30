@props(['type' => 'button'])

<button type="{{ $type }}"
    {{ $attributes->merge([
        'class' =>
            'px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 cursor-pointer',
    ]) }}>
    {{ $slot }}
</button>
