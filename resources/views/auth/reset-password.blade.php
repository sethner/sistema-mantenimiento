<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer Contraseña - Sistema AIP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .fade-in { animation: fadeIn 0.6s ease; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-100 via-gray-50 to-blue-100 p-4">

    <div class="w-full max-w-5xl shadow-2xl rounded-2xl overflow-hidden flex fade-in">

        {{-- Panel izquierdo (decorativo) --}}
        <div class="w-1/2 hidden md:flex flex-col justify-between p-10 text-white bg-cover bg-center relative"
             style="background-image: linear-gradient(to bottom right, rgba(30,58,138,0.92), rgba(99,102,241,0.75)), url('{{ asset('img/Colegio.jpg') }}');">
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_top,white,transparent_70%)]"></div>
            <div class="relative z-10">
                <div class="w-16 h-1 bg-white mb-6 rounded"></div>
                <h1 class="text-4xl font-bold mb-4 leading-tight">
                    Restablece tu<br>Contraseña
                </h1>
                <p class="text-blue-100 text-sm leading-relaxed max-w-xs">
                    Elige una nueva contraseña segura para proteger tu cuenta del Sistema AIP.
                </p>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-3 backdrop-blur-sm border border-white/20">
                    <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white">Acceso Seguro</p>
                        <p class="text-[11px] text-blue-200">Tu información está protegida</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel derecho (formulario) --}}
        <div class="w-full md:w-1/2 bg-white p-10">

            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center shadow-inner">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
            </div>

            <h2 class="text-center text-2xl font-bold text-gray-800 mb-1">Crear Nueva Contraseña</h2>
            <p class="text-center text-gray-500 text-sm mb-8">Completa los campos para restablecer tu acceso.</p>

            {{-- Errores --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 text-sm flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf

                {{-- Token --}}
                <input type="hidden" name="token" value="{{ $request->route('token') ?? $request->token }}">

                {{-- Correo --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}"
                           required autofocus placeholder="ejemplo@institucion.edu.pe"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm">
                    @error('email')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nueva contraseña --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nueva Contraseña</label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                               required autocomplete="new-password" placeholder="Mínimo 8 caracteres"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm pr-10">
                        <button type="button" onclick="togglePass('password', this)"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-indigo-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmar contraseña --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirmar Contraseña</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               required autocomplete="new-password" placeholder="Repite la nueva contraseña"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm pr-10">
                        <button type="button" onclick="togglePass('password_confirmation', this)"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-indigo-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botón --}}
                <button type="submit"
                        class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 text-white py-3 rounded-xl font-bold hover:shadow-lg hover:scale-[1.02] transition-all duration-200 mt-2">
                    Restablecer Contraseña
                </button>

                <p class="text-center text-sm mt-4">
                    <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">
                        ← Volver al inicio de sesión
                    </a>
                </p>
            </form>

            <p class="text-center text-gray-400 text-xs mt-8">© {{ date('Y') }} I.E. Jorge Chávez</p>
        </div>
    </div>

    <script>
        function togglePass(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.querySelector('svg').style.opacity = isPassword ? '0.5' : '1';
        }
    </script>
</body>
</html>
