<img src="{{ $config->logo_path ? asset('storage/' . $config->logo_path) : asset('img/logo.jpg') }}" {{ $attributes->merge(['class' => 'object-cover rounded-full']) }} alt="Sistema AIP">
