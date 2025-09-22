<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Mostrar lista de etiquetas
     */
    public function index(Request $request)
    {
        $query = Tag::query()->ordered();

        // Filtros opcionales
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $tags = $query->withCount('courses')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $tags,
            'meta' => [
                'message' => 'Etiquetas obtenidas exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar una etiqueta específica
     */
    public function show($id)
    {
        $tag = Tag::with(['courses'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $tag,
            'meta' => [
                'message' => 'Etiqueta obtenida exitosamente'
            ]
        ]);
    }

    /**
     * Crear una nueva etiqueta
     */
    public function store(Request $request)
    {
        // Solo administradores pueden crear etiquetas
        if (!$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para crear etiquetas'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tags',
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

        $data = $request->only(['name']);
        $data['slug'] = $request->slug ?? Str::slug($request->name);

        // Verificar unicidad del slug
        if (Tag::where('slug', $data['slug'])->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'El slug ya existe, elige otro nombre'
                ]
            ], 422);
        }

        $tag = Tag::create($data);

        return response()->json([
            'success' => true,
            'data' => $tag->load(['courses']),
            'meta' => [
                'message' => 'Etiqueta creada exitosamente'
            ]
        ], 201);
    }

    /**
     * Actualizar una etiqueta
     */
    public function update(Request $request, $id)
    {
        // Solo administradores pueden actualizar etiquetas
        if (!$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para actualizar etiquetas'
                ]
            ], 403);
        }

        $tag = Tag::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:tags,slug,' . $tag->id],
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

        $data = $request->only(['name', 'slug']);

        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            // Verificar unicidad
            if (Tag::where('slug', $data['slug'])->where('id', '!=', $tag->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'meta' => [
                        'message' => 'El slug generado ya existe, especifica uno manualmente'
                    ]
                ], 422);
            }
        }

        $tag->update($data);

        return response()->json([
            'success' => true,
            'data' => $tag->fresh(['courses']),
            'meta' => [
                'message' => 'Etiqueta actualizada exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar una etiqueta
     */
    public function destroy(Request $request, $id)
    {
        // Solo administradores pueden eliminar etiquetas
        if (!$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para eliminar etiquetas'
                ]
            ], 403);
        }

        $tag = Tag::findOrFail($id);

        // Verificar si tiene cursos asociados
        if ($tag->courses()->count() > 0) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No se puede eliminar una etiqueta que tiene cursos asociados'
                ]
            ], 422);
        }

        $tag->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Etiqueta eliminada exitosamente'
            ]
        ]);
    }
}