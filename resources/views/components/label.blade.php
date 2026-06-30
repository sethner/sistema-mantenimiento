@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray/90']) }}>
    {{ $value ?? $slot }}
</label>
