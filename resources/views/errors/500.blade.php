<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 – Error del Servidor | Sistema AIP</title>
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
        .pulse-ring {
            animation: pulse-ring 2s ease-out infinite;
        }
        @keyframes pulse-ring {
            0%   { transform: scale(0.9); opacity: 0.7; }
            70%  { transform: scale(1.1); opacity: 0; }
            100% { transform: scale(1.1); opacity: 0; }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50 flex items-center justify-center p-6">

    <div class="text-center max-w-lg w-full fade-in">

        <!-- Código de error animado -->
        <div class="float mb-8 select-none">
            <span class="text-[120px] font-black leading-none bg-gradient-to-br from-amber-500 to-orange-600 bg-clip-text text-transparent drop-shadow-md">
                500
            </span>
        </div>

        <!-- Ícono con anillo de pulso -->
        <div class="flex justify-center mb-6 relative">
            <div class="absolute w-20 h-20 bg-amber-200 rounded-full pulse-ring"></div>
            <div class="w-20 h-20 bg-amber-100 rounded-2xl flex items-center justify-center shadow-inner relative z-10">
                <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-3">Error del Servidor</h1>
        <p class="text-gray-500 text-base mb-2 leading-relaxed">
            Ocurrió un error interno en el servidor.<br>
            Nuestro equipo ha sido notificado y trabajará para solucionarlo.
        </p>
        <p class="text-gray-400 text-sm mb-8">
            Intenta recargar la página o vuelve más tarde.
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
            <button onclick="window.location.reload()"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-amber-300 text-amber-700 font-semibold rounded-xl hover:bg-amber-50 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Recargar
            </button>
            <a href="{{ url('/') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-[1.02] transition-all duration-200">
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