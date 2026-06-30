<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso al Sistema AIP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .fade-in { animation: fadeIn 0.6s ease; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="w-full max-w-5xl shadow-2xl rounded-2xl overflow-hidden flex fade-in">
        <div class="w-1/2 relative hidden md:flex flex-col justify-between p-10 text-white bg-cover bg-center" style="background-image: linear-gradient(to bottom right, rgba(30, 58, 138, 0.9), rgba(37, 99, 235, 0.7)), url('{{ asset('img/Colegio.jpg') }}');">
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_top,white,transparent_70%)]"></div>

            <div class="relative z-10">
                <div class="w-16 h-1 bg-white mb-6 rounded"></div>
                <h1 class="text-4xl font-bold mb-4 leading-tight">
                    Sistema de Gestión de Equipos AIP
                </h1>
                <p class="text-blue-100 text-sm leading-relaxed max-w-md">
                    Plataforma institucional para controlar inventario, mantenimientos y estado de los equipos informáticos del área AIP.
                </p>
            </div>

           
        </div>

        <div class="w-full md:w-1/2 bg-white p-10">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('img/logo.jpg') }}"
                     class="w-20 h-20 rounded-full shadow border"
                     alt="Sistema AIP">
            </div>

            <h2 class="text-center text-2xl font-bold text-gray-800 mb-1">Bienvenido</h2>
            <p class="text-center text-gray-500 text-sm mb-6">Inicia sesión para continuar</p>

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-600">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-600 transition" placeholder="ejemplo@institucion.edu.pe">
                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-600">Contraseña</label>
                    <div class="relative mt-1">
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 transition pr-10">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition">
                            <svg id="eyeIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-6 text-sm">
                    <label class="flex items-center text-gray-600">
                        <input type="checkbox" name="remember" class="mr-2">
                        Recordarme
                    </label>

                    <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <button type="submit"
                    class="w-full bg-blue-700 text-white py-2 rounded-lg font-semibold hover:bg-blue-800 transition duration-300 shadow">
                    Ingresar al sistema
                </button>
            </form>

            <p class="text-center text-gray-400 text-xs mt-6">
                © 2026 I.E. Jorge Chávez
            </p>
           
        </div>
    </div>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>
</body>
</html>
