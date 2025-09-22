<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Mostrar lista de categorías
     */
    public function index(Request $request)
    {
        $query = Category::query()->ordered();

        // Filtros opcionales
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        $categories = $query->with(['parent', 'children'])->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $categories,
            'meta' => [
                'message' => 'Categorías obtenidas exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar una categoría específica
     */
    public function show($id)
    {
        $category = Category::with(['parent', 'children', 'courses'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category,
            'meta' => [
                'message' => 'Categoría obtenida exitosamente'
            ]
        ]);
    }

    /**
     * Crear una nueva categoría
     */
    public function store(Request $request)
    {
        // Solo administradores pueden crear categorías
        if (!$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para crear categorías'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories',
            'parent_id' => 'nullable|exists:categories,id',
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

        $data = $request->only(['name', 'parent_id']);
        $data['slug'] = $request->slug ?? Str::slug($request->name);

        // Verificar unicidad del slug
        if (Category::where('slug', $data['slug'])->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'El slug ya existe, elige otro nombre'
                ]
            ], 422);
        }

        $category = Category::create($data);

        return response()->json([
            'success' => true,
            'data' => $category->load(['parent', 'children']),
            'meta' => [
                'message' => 'Categoría creada exitosamente'
            ]
        ], 201);
    }

    /**
     * Actualizar una categoría
     */
    public function update(Request $request, $id)
    {
        // Solo administradores pueden actualizar categorías
        if (!$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para actualizar categorías'
                ]
            ], 403);
        }

        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:categories,slug,' . $category->id],
            'parent_id' => 'nullable|exists:categories,id',
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

        $data = $request->only(['name', 'slug', 'parent_id']);

        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            // Verificar unicidad
            if (Category::where('slug', $data['slug'])->where('id', '!=', $category->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'meta' => [
                        'message' => 'El slug generado ya existe, especifica uno manualmente'
                    ]
                ], 422);
            }
        }

        $category->update($data);

        return response()->json([
            'success' => true,
            'data' => $category->fresh(['parent', 'children']),
            'meta' => [
                'message' => 'Categoría actualizada exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar una categoría
     */
    public function destroy(Request $request, $id)
    {
        // Solo administradores pueden eliminar categorías
        if (!$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para eliminar categorías'
                ]
            ], 403);
        }

        $category = Category::findOrFail($id);

        // Verificar si tiene subcategorías
        if ($category->children()->count() > 0) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No se puede eliminar una categoría que tiene subcategorías'
                ]
            ], 422);
        }

        // Verificar si tiene cursos asociados
        if ($category->courses()->count() > 0) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No se puede eliminar una categoría que tiene cursos asociados'
                ]
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Categoría eliminada exitosamente'
            ]
        ]);
    }
}