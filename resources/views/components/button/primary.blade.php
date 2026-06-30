@props(['type' => 'button'])

<button type="{{ $type }}"
    {{ $attributes->merge([
        'class' =>
            'px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 cursor-pointer',
    ]) }}>
    {{ $slot }}
</button>
