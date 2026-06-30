<select {{ $attributes->merge([
    'class' => 'w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500',
]) }}>
    {{ $slot }}
</select>
