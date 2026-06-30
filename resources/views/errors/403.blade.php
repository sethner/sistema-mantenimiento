<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 – Acceso Denegado | Sistema AIP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .fade-in { animation: fadeIn 0.7s ease both; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .float {
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-14px); }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-rose-50 via-white to-red-50 flex items-center justify-center p-6">

    <div class="text-center max-w-lg w-full fade-in">

        <!-- Código de error animado -->
        <div class="float mb-8 select-none">
            <span class="text-[120px] font-black leading-none bg-gradient-to-br from-rose-500 to-red-600 bg-clip-text text-transparent drop-shadow-md">
                403
            </span>
        </div>

        <!-- Ícono -->
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-rose-100 rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-10 h-10 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-3">Acceso Denegado</h1>
        <p class="text-gray-500 text-base mb-8 leading-relaxed">
            No tienes los permisos necesarios para acceder a esta sección.<br>
            Contacta al administrador si crees que esto es un error.
        </p>

        <!-- Acciones -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver atrás
            </a>
            <a href="{{ url('/') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-rose-500 to-red-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-[1.02] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
                Ir al inicio
            </a>
        </div>

        <!-- Footer -->
        <p class="mt-10 text-xs text-gray-400">© {{ date('Y') }} I.E. Jorge Chávez – Sistema AIP</p>
    </div>
</body>
</html>