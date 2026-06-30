<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

/**
 * Clase NotificacionController
 * Gestiona el buzón de notificaciones (leídas, no leídas, limpieza de alertas y peticiones asíncronas para el contador del header).
 */
class NotificacionController extends Controller
{
    /**
     * Muestra la vista principal con todas las notificaciones recibidas por el usuario.
     * Al entrar, marca automáticamente todas las notificaciones no leídas como leídas.
     */
    public function index(Request $request)
    {
        $notificaciones = $request->user()
            ->notificaciones()
            ->latest()
            ->paginate(20);

        // Marcar todas como leídas al visitar la página
        $request->user()
            ->notificaciones()
            ->where('leida', false)
            ->update(['leida' => true]);

        return view('notificaciones.index', compact('notificaciones'));
    }

    /**
     * Marca una única notificación seleccionada como leída (petición AJAX).
     */
    public function marcarLeida(Request $request, Notificacion $notificacion)
    {
        // Seguridad: validar que la notificación pertenezca al usuario autenticado
        abort_if($notificacion->user_id !== $request->user()->id, 403);
        
        $notificacion->update(['leida' => true]);
        return response()->json(['ok' => true]);
    }

    /**
     * Marca todas las notificaciones del usuario logueado como leídas.
     */
    public function marcarTodasLeidas(Request $request)
    {
        $request->user()
            ->notificacionesNoLeidas()
            ->update(['leida' => true]);
            
        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }

    /**
     * Retorna la cantidad total de notificaciones no leídas en formato JSON para refrescar el navbar.
     */
    public function conteo(Request $request)
    {
        $count = $request->user()->notificacionesNoLeidas()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Borra todo el historial de notificaciones del usuario logueado.
     */
    public function limpiar(Request $request)
    {
        $request->user()->notificaciones()->delete();
        return back()->with('success', 'Historial de notificaciones eliminado correctamente.');
    }

    /**
     * Obtiene las 5 notificaciones no leídas más recientes en formato JSON para el menú desplegable (dropdown).
     */
    public function recientes(Request $request)
    {
        $items = $request->user()
            ->notificaciones()
            ->where('leida', false)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($n) => [
                'id'     => $n->id,
                'tipo'   => $n->tipo,
                'titulo' => $n->titulo,
                'mensaje'=> $n->mensaje,
                'enlace' => $n->enlace,
                'leida'  => $n->leida,
                'hace'   => $n->created_at->diffForHumans(), // Tiempo transcurrido legible (ej: hace 5 minutos)
            ]);

        return response()->json($items);
    }
}
