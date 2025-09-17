<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\RbacMirror;

class UserController extends Controller
{
    protected RbacMirror $rbacMirror;

    public function __construct()
    {
        $this->rbacMirror = app(RbacMirror::class);
    }
    /**
     * Mostrar lista de usuarios (solo para administradores)
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtros opcionales
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->with(['roles', 'subscriptionPlans'])->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $users,
            'meta' => [
                'message' => 'Usuarios obtenidos exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar un usuario específico
     */
    public function show(Request $request, $id)
    {
        $user = User::with([
            'roles',
            'enrolledCourses',
            'coursesProgress',
            'subscriptions',
            'certificates',
            'courseReviews'
        ])->findOrFail($id);

        // Verificar si el usuario puede ver este perfil
        if ($request->user()->id !== $user->id && !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para ver este perfil'
                ]
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
            'meta' => [
                'message' => 'Usuario obtenido exitosamente'
            ]
        ]);
    }

    /**
     * Crear un nuevo usuario
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['nullable', 'string', Rule::exists('roles', 'name')->where(function ($query) {
                $query->where('guard_name', 'web');
            })],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        // Nota: El modelo User tiene cast 'hashed' en password; no volver a hashear aquí
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Asignar rol por defecto si se especifica:
        // Sincroniza el rol en ambos guards (web y api)
        if ($request->filled('role')) {
            $roleName = trim((string) $request->input('role'));
            $this->rbacMirror->syncUserRolesBothGuardsByNames($user, [$roleName]);
        }

        return response()->json([
            'success' => true,
            'data' => $user->load('roles'),
            'meta' => [
                'message' => 'Usuario creado exitosamente'
            ]
        ], 201);
    }

    /**
     * Actualizar un usuario
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Verificar permisos: propio perfil o admin
        if ($request->user()->id !== $user->id && !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para actualizar este usuario'
                ]
            ], 403);
        }

        // Si intenta actualizar el rol, requiere ser admin
        if ($request->has('role') && !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Solo los administradores pueden actualizar roles de usuario'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|required|string|min:8|confirmed',
            'role' => [
                'sometimes',
                'string',
                Rule::exists('roles', 'name')->where(function ($q) {
                    $q->where('guard_name', 'web');
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        $updateData = $request->only(['name', 'email']);

        if ($request->has('password')) {
            // Con el cast 'hashed' en el modelo, asignar en claro
            $updateData['password'] = $request->password;
        }

        $user->update($updateData);

        // Actualizar rol si fue proporcionado (solo admin)
        if ($request->filled('role')) {
            $newRole = trim($request->input('role'));

            // Evitar que un admin se quite a sí mismo el rol admin
            if ($request->user()->id === $user->id && $user->hasRole('admin') && strtolower($newRole) !== 'admin') {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'meta' => [
                        'message' => 'No puedes removerte a ti mismo el rol admin'
                    ]
                ], 403);
            }

            // Sincroniza el(los) rol(es) en ambos guards (web y api)
            $this->rbacMirror->syncUserRolesBothGuardsByNames($user, [$newRole]);
        }

        return response()->json([
            'success' => true,
            'data' => $user->fresh(['roles']),
            'meta' => [
                'message' => 'Usuario actualizado exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar un usuario
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Solo administradores pueden eliminar usuarios
        if (!$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para eliminar usuarios'
                ]
            ], 403);
        }

        // No permitir eliminar al propio usuario
        if ($request->user()->id === $user->id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No puedes eliminar tu propio usuario'
                ]
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Usuario eliminado exitosamente'
            ]
        ]);
    }

    /**
     * Obtener perfil del usuario autenticado
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load([
            'roles',
            'enrolledCourses',
            'coursesProgress',
            'subscriptions',
            'certificates',
            'courseReviews',
            'wishlistCourses'
        ]);

        return response()->json([
            'success' => true,
            'data' => $user,
            'meta' => [
                'message' => 'Perfil obtenido exitosamente'
            ]
        ]);
    }

    /**
     * Obtener cursos inscritos del usuario
     */
    public function enrolledCourses(Request $request, $id = null)
    {
        $userId = $id ?? $request->user()->id;

        if ($id && $request->user()->id !== (int)$id && !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para ver estos cursos'
                ]
            ], 403);
        }

        $user = User::with(['enrolledCourses' => function ($query) {
            $query->with(['category', 'instructor'])->withPivot(['enrolled_at', 'expires_at']);
        }])->findOrFail($userId);

        return response()->json([
            'success' => true,
            'data' => $user->enrolledCourses,
            'meta' => [
                'message' => 'Cursos inscritos obtenidos exitosamente'
            ]
        ]);
    }

    /**
     * Obtener progreso de cursos del usuario
     */
    public function coursesProgress(Request $request, $id = null)
    {
        $userId = $id ?? $request->user()->id;

        if ($id && $request->user()->id !== (int)$id && !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para ver este progreso'
                ]
            ], 403);
        }

        $user = User::with(['coursesProgress' => function ($query) {
            $query->withPivot(['progress_pct', 'completed_at']);
        }])->findOrFail($userId);

        return response()->json([
            'success' => true,
            'data' => $user->coursesProgress,
            'meta' => [
                'message' => 'Progreso de cursos obtenido exitosamente'
            ]
        ]);
    }

    /**
     * Obtener certificados del usuario
     */
    public function certificates(Request $request, $id = null)
    {
        $userId = $id ?? $request->user()->id;

        if ($id && $request->user()->id !== (int)$id && !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para ver estos certificados'
                ]
            ], 403);
        }

        $user = User::with(['certificates' => function ($query) {
            $query->with(['course', 'template']);
        }])->findOrFail($userId);

        return response()->json([
            'success' => true,
            'data' => $user->certificates,
            'meta' => [
                'message' => 'Certificados obtenidos exitosamente'
            ]
        ]);
    }
}