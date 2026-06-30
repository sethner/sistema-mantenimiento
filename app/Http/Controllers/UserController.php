<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Mantenimiento;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Clase UserController
 * Controla el mantenimiento CRUD y administración de perfiles de usuario del sistema.
 */
class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios con paginación y filtros de búsqueda por nombre/correo o rol.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Búsqueda aproximada por nombre o correo
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro exacto por Rol
        if ($request->filled('rol')) {
            $rol = strtolower($request->rol);

            $query->whereHas('roles', function ($q) use ($rol) {
                $q->whereRaw('LOWER(nombre) = ?', [$rol]);
            });
        }

        $usuarios = $query->latest()->paginate(10)->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::orderBy('nombre')->get();

        return view('usuarios.create', compact('roles'));
    }

    /**
     * Procesa y registra un nuevo usuario en el sistema.
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // Hashear la contraseña por seguridad
        ];

        // Guardar foto de perfil si se proporciona
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->storeFoto($request);
        }

        $user = User::create($data);
        // Vincular el rol asignado
        $user->roles()->attach($validated['role_id']);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     *
     * @param User $usuario
     * @return \Illuminate\View\View
     */
    public function edit(User $usuario)
    {
        $roles = Role::orderBy('nombre', 'asc')->get();
        $usuario->load('roles');

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Actualiza la información del usuario en la base de datos.
     *
     * @param UpdateUserRequest $request
     * @param User $usuario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $usuario)
    {
        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Hashear e integrar la nueva contraseña si se ingresó
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        // Si se sube una nueva foto, se elimina la anterior del servidor
        if ($request->hasFile('foto')) {
            $this->deleteFoto($usuario);
            $data['foto'] = $this->storeFoto($request);
        }

        $usuario->update($data);
        // Sincronizar el nuevo rol
        $usuario->roles()->sync([$validated['role_id']]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Elimina el usuario y su foto del servidor.
     *
     * @param User $usuario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $usuario)
    {
        // Evitar que se eliminen usuarios con rol de administrador
        if ($usuario->hasRole('administrador')) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No se puede eliminar a un usuario administrador.');
        }

        $this->deleteFoto($usuario);
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente');
    }

    /**
     * Visualiza el perfil del usuario y el histórico de sus tareas de mantenimiento.
     *
     * @param User $usuario
     * @return \Illuminate\View\View
     */
    public function show(User $usuario)
    {
        $usuario->load('roles');

        $mantenimientos = Mantenimiento::with('equipo')
            ->where('user_id', $usuario->id)
            ->latest()
            ->get();

        return view('usuarios.show', compact('usuario', 'mantenimientos'));
    }

    /**
     * Helper para guardar físicamente la foto de perfil en la carpeta pública 'img/'.
     */
    private function storeFoto(Request $request): string
    {
        $file = $request->file('foto');
        $nombre = time().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('img'), $nombre);

        return 'img/'.$nombre;
    }

    /**
     * Helper para borrar físicamente la foto anterior de la carpeta pública 'img/'.
     */
    private function deleteFoto(User $usuario): void
    {
        if ($usuario->foto && file_exists(public_path($usuario->foto))) {
            unlink(public_path($usuario->foto));
        }
    }
}
