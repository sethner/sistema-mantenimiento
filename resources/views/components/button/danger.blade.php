@props(['type' => 'button'])

<button type="{{ $type }}" {{ $attributes->merge([
    'class' => 'px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 cursor-pointer'
]) }}>
    {{ $slot }}
</button>
