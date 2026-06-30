@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800">Nuevo Usuario</h2>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded text-sm mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('usuarios.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- FOTO -->
            <div class="flex flex-col items-center gap-3">

                <div class="relative">
                    <!-- PREVIEW -->
                    <img id="preview"
                        onclick="document.getElementById('foto').click()"
                        class="w-28 h-28 rounded-full object-cover border-4 border-white shadow hidden cursor-pointer hover:opacity-80 transition"
                        alt="Vista previa">

                    <!-- AVATAR DEFAULT -->
                    <div id="avatarDefault"
                        onclick="document.getElementById('foto').click()"
                        class="w-28 h-28 rounded-full bg-blue-700 flex items-center justify-center text-white text-3xl font-bold shadow cursor-pointer hover:opacity-80 transition">
                        ?
                    </div>

                    <!-- ICONO CAMARA -->
                   <div onclick="document.getElementById('foto').click()"
                    class="absolute bottom-1 right-1 bg-white border border-gray-300 w-8 h-8 rounded-full flex items-center justify-center shadow cursor-pointer hover:bg-gray-100 transition">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 text-gray-700"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7h4l2-2h6l2 2h4v12H3V7z"/>
                        <circle cx="12" cy="13" r="3" />
                    </svg>
                        </div>
                </div>

                <label class="text-sm text-gray-500 cursor-pointer hover:text-blue-600">
                    Cambiar foto
                    <input type="file" name="foto" id="foto" class="hidden" accept="image/*">
                </label>

            </div>

            <!-- DATOS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Nombre</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Juan Pérez">
                </div>

                <div>
                    <label class="text-sm font-medium">Correo</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- ROL -->
            <div>
                <label class="text-sm font-medium">Rol</label>
                <select name="role_id"
                    class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>
                            {{ ucfirst($r->nombre) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- PASSWORD -->
            <div>
                <label class="text-sm font-medium">Contraseña</label>
                <input type="password" name="password"
                    class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="Debe incluir mayúscula, minúscula, número y símbolo">
            </div>

            <div>
                <label class="text-sm font-medium">Confirmar contraseña</label>
                <input type="password" name="password_confirmation"
                    class="w-full mt-1 px-4 py-2 border rounded-lg">
            </div>

            <!-- BOTONES -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('usuarios.index') }}"
                    class="px-5 py-2 border rounded-lg hover:bg-gray-100">
                    Cancelar
                </a>

                <button
                    class="px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    Guardar
                </button>
            </div>

        </form>
    </div>
</div>

<!-- SCRIPT -->
<script>
const inputFoto = document.getElementById('foto');
const preview = document.getElementById('preview');
const avatar = document.getElementById('avatarDefault');
const inputName = document.getElementById('name');

// 📷 PREVISUALIZAR IMAGEN
inputFoto.addEventListener('change', function(e) {
    const file = e.target.files[0];

    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        avatar.classList.add('hidden');
    }
});

// 🔤 INICIAL DEL NOMBRE
inputName.addEventListener('input', function(e) {
    avatar.textContent = e.target.value.charAt(0).toUpperCase() || '?';
});
</script>

@endsection