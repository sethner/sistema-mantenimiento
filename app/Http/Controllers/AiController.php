<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Equipo;

/**
 * Clase AiController
 * Controla las peticiones de integración con Inteligencia Artificial (Groq/LLaMA) para diagnósticos y análisis predictivos.
 */
class AiController extends Controller
{
    /**
     * Sugiere un diagnóstico basado en la descripción textual del problema reportado por el técnico.
     * Envía una petición estructurada a la API de Groq.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestDiagnosis(Request $request)
    {
        // Validar la entrada de datos
        $request->validate([
            'descripcion' => ['required', 'string', 'min:5'],
        ]);

        $problema = trim($request->descripcion);
        $apiKey   = config('services.groq.key');

        // Validar si la llave de la API está configurada en las variables de entorno
        if (empty($apiKey)) {
            return response()->json(['error' => 'API Key de IA no configurada.'], 500);
        }

        // Prompt del sistema que modela el formato estricto de respuesta
        $systemPrompt = <<<'PROMPT'
Eres un asistente técnico experto en mantenimiento de equipos informáticos (hardware y software).
Cuando el usuario te dé la descripción de un problema, responde SIEMPRE en español y EXACTAMENTE con este formato, sin texto adicional fuera de las secciones:

Diagnóstico probable:
- [causa 1]
- [causa 2]
- [causa 3]

Nivel de urgencia: [Bajo / Medio / Alto]

Posibles soluciones:
- [acción 1]
- [acción 2]
- [acción 3]

Componentes sugeridos:
- [componente 1]
- [componente 2]

Prioriza hardware. Responde con EXACTAMENTE 3 causas, 3 acciones y 2 componentes. 
IMPORTANTE: Si el usuario proporciona una descripción vaga o una acción general (ej: "Revisar", "Limpieza"), asume que se requiere un mantenimiento preventivo general y sugiere tareas estándar de revisión técnica. Nunca respondas con excusas o texto fuera de formato.
PROMPT;

        try {
            // Envío de petición HTTP POST a la API de Groq
            $response = Http::withToken($apiKey)
                ->timeout(25)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => 'llama-3.1-8b-instant',
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => "El técnico reporta este problema:\n\n{$problema}"],
                    ],
                    'temperature' => 0.3,
                    'max_tokens'  => 600,
                ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Error al conectar con el servicio de IA: ' . $response->status(),
                ], 502);
            }

            // Obtener el contenido de la respuesta de la IA
            $content = $response->json('choices.0.message.content', '');

            return response()->json(['diagnostico' => $content]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Genera un análisis predictivo de fallas futuras y recomendaciones sobre un equipo
     * basado en su historial previo de mantenimientos e incidencias.
     *
     * @param Equipo $equipo
     * @return \Illuminate\Http\JsonResponse
     */
    public function predictiveAnalysis(Equipo $equipo)
    {
        // Cargar las relaciones necesarias del equipo
        $equipo->load(['mantenimientos', 'historialFallas']);
        
        // Mapear historial de mantenimientos para simplificar el prompt
        $historial = $equipo->mantenimientos->map(fn($m) => [
            'fecha' => $m->fecha,
            'tipo' => $m->tipo,
            'accion' => $m->accion,
            'diagnostico' => $m->diagnostico
        ]);

        // Mapear historial de fallas registradas
        $fallas = $equipo->historialFallas->map(fn($f) => [
            'fecha' => $f->fecha,
            'descripcion' => $f->descripcion
        ]);

        $apiKey = config('services.groq.key');
        if (empty($apiKey)) {
            return response()->json(['error' => 'IA no configurada.'], 500);
        }

        // Construcción de la consulta detallada para el LLM
        $prompt = "Analiza el siguiente historial de un equipo informático ({$equipo->marca} {$equipo->modelo}) y predice qué fallas podría tener en el futuro cercano. También sugiere acciones preventivas.\n\n";
        $prompt .= "Historial de mantenimientos: " . json_encode($historial) . "\n";
        $prompt .= "Historial de fallas: " . json_encode($fallas) . "\n";

        try {
            // Envío de petición HTTP POST a la API de Groq para análisis predictivo
            $response = Http::withToken($apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.1-8b-instant',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Eres un experto en mantenimiento predictivo basado en Machine Learning (Aprendizaje Automático). Responde en español de forma técnica pero comprensible, explicando cómo los patrones históricos son analizados por el modelo de Machine Learning.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            return response()->json(['analisis' => $response->json('choices.0.message.content')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
