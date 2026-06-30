@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
            <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-white" />
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Configuración del Sistema</h2>
            <p class="text-sm text-gray-500">Administra los datos de la institución y ajustes generales.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    
    @include('components.session.success')


    <form action="{{ route('configuracion.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            {{-- LOGO DE LA INSTITUCIÓN --}}
            <div class="md:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Logo</h3>
                    
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-32 h-32 rounded-2xl bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden group relative">
                            @if($config->logo_path)
                                <img src="{{ asset('storage/' . $config->logo_path) }}" class="w-full h-full object-contain" id="logo_preview">
                            @else
                                <x-heroicon-o-photo class="w-10 h-10 text-gray-300" id="logo_placeholder" />
                            @endif
                        </div>
                        
                        <label class="w-full">
                            <span class="block w-full text-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl text-xs font-bold cursor-pointer hover:bg-indigo-100 transition">
                                Cambiar Logo
                            </span>
                            <input type="file" name="logo" class="hidden" onchange="previewImage(this)">
                        </label>
                        <p class="text-[10px] text-gray-400 text-center">PNG, JPG hasta 2MB</p>
                    </div>
                </div>
            </div>

            {{-- DATOS GENERALES --}}
            <div class="md:col-span-2 space-y-6">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Datos de la Institución</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nombre de la Institución</label>
                            <input type="text" name="nombre_institucion" value="{{ old('nombre_institucion', $config->nombre_institucion) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition bg-gray-50/50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nombre del Director</label>
                            <input type="text" name="director_nombre" value="{{ old('director_nombre', $config->director_nombre) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition bg-gray-50/50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">RUC</label>
                            <input type="text" name="ruc" value="{{ old('ruc', $config->ruc) }}" maxlength="11"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition bg-gray-50/50">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Dirección Fiscal</label>
                            <input type="text" name="direccion" value="{{ old('direccion', $config->direccion) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition bg-gray-50/50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Teléfono / Celular</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $config->telefono) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition bg-gray-50/50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Correo Electrónico</label>
                            <input type="email" name="email" value="{{ old('email', $config->email) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition bg-gray-50/50">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-10 py-3 bg-gray-900 text-white rounded-xl text-sm font-bold shadow-xl hover:bg-black transition flex items-center gap-2">
                        <x-heroicon-s-check class="w-5 h-5" />
                        Guardar Cambios
                    </button>
                </div>

            </div>

        </div>
    </form>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('logo_preview');
                var placeholder = document.getElementById('logo_placeholder');
                
                if (preview) {
                    preview.src = e.target.result;
                } else {
                    // Create img if not exists
                    var img = document.createElement('img');
                    img.id = 'logo_preview';
                    img.src = e.target.result;
                    img.className = 'w-full h-full object-contain';
                    placeholder.parentNode.appendChild(img);
                    placeholder.remove();
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
