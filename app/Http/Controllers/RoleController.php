<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private function jsonError(string $message, int $status = 400, array $errors = [])
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'meta' => [
                'message' => $message,
                'errors' => $errors,
            ],
        ], $status);
    }

    private function jsonSuccess($data = null, string $message = '', array $pagination = null, int $status = 200)
    {
        $meta = ['message' => $message];
        if ($pagination !== null) {
            $meta['pagination'] = $pagination;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => $meta,
        ], $status);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'guard' => ['sometimes','in:web'],
            'page' => ['sometimes','integer','min:1'],
            'per_page' => ['sometimes','integer','min:1','max:100'],
            'sort' => ['sometimes','in:name'],
            'order' => ['sometimes','in:asc,desc'],
            'q' => ['sometimes','string','max:255'],
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validación fallida', 422, $validator->errors()->toArray());
        }

        $guard = $request->query('guard', 'web');
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min(100, $perPage));
        $sort = $request->query('sort', 'name');
        $order = $request->query('order', 'asc');
        $q = $request->query('q');

        $query = Role::query()->where('guard_name', $guard);

        if ($q) {
            $query->where('name', 'like', '%'.$q.'%');
        }

        $query->orderBy($sort, $order);

        $paginator = $query->paginate($perPage);

        $data = $paginator->items();

        $pagination = [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];

        return $this->jsonSuccess($data, '', $pagination);
    }

    public function store(Request $request)
    {
        $payload = [
            'name' => trim((string) $request->input('name', '')),
            'guard_name' => $request->input('guard_name', 'web'),
        ];

        $validator = Validator::make($payload, [
            'name' => ['required','string','max:255'],
            'guard_name' => ['sometimes','in:web'],
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validación fallida', 422, $validator->errors()->toArray());
        }

        $guard = $payload['guard_name'] ?? 'web';

        $duplicate = Role::where('name', $payload['name'])->where('guard_name', $guard)->exists();
        if ($duplicate) {
            return $this->jsonError('El rol ya existe para el guard especificado', 409);
        }

        $role = Role::create([
            'name' => $payload['name'],
            'guard_name' => $guard,
        ]);

        return $this->jsonSuccess($role, 'Rol creado', null, 201);
    }

    public function show(Request $request, int $id)
    {
        $guard = $request->query('guard', 'web');

        $role = Role::where('id', $id)->where('guard_name', $guard)->first();

        if (!$role) {
            return $this->jsonError('Rol no encontrado', 404);
        }

        return $this->jsonSuccess($role);
    }

    public function update(Request $request, int $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return $this->jsonError('Rol no encontrado', 404);
        }

        $payload = [
            'name' => $request->has('name') ? trim((string) $request->input('name')) : $role->name,
            'guard_name' => $request->input('guard_name', $role->guard_name),
        ];

        $validator = Validator::make($payload, [
            'name' => [
                'required','string','max:255',
                Rule::unique('roles', 'name')
                    ->where(fn ($q) => $q->where('guard_name', $payload['guard_name']))
                    ->ignore($id),
            ],
            'guard_name' => ['sometimes','in:web'],
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validación fallida', 422, $validator->errors()->toArray());
        }

        // Extra chequeo para conflictos de unicidad
        $exists = Role::where('id', '!=', $id)
            ->where('guard_name', $payload['guard_name'])
            ->where('name', $payload['name'])
            ->exists();

        if ($exists) {
            return $this->jsonError('Ya existe un rol con ese nombre en el guard especificado', 409);
        }

        $role->name = $payload['name'];
        $role->guard_name = $payload['guard_name'];
        $role->save();

        return $this->jsonSuccess($role, 'Rol actualizado');
    }

    public function destroy(Request $request, int $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return $this->jsonError('Rol no encontrado', 404);
        }

        $role->delete();

        return $this->jsonSuccess(null, 'Rol eliminado');
    }
}