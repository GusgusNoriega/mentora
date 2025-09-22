<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseSectionController extends Controller
{
    /**
     * Mostrar lista de secciones de un curso
     */
    public function index(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $sections = $course->sections()->ordered()->with(['lessons' => function ($query) {
            $query->ordered()->withCount('mediaAssets');
        }])->withCount('lessons')->get();

        return response()->json([
            'success' => true,
            'data' => $sections,
            'meta' => [
                'message' => 'Secciones obtenidas exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar una sección específica
     */
    public function show($courseId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->with(['lessons' => function ($query) {
            $query->ordered()->with(['mediaAssets', 'quiz']);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $section,
            'meta' => [
                'message' => 'Sección obtenida exitosamente'
            ]
        ]);
    }

    /**
     * Crear una nueva sección
     */
    public function store(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = $request->user();

        // Verificar permisos
        if (!$this->canManageCourse($user, $course)) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para gestionar este curso'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'position' => 'nullable|integer|min:1',
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

        $data = $request->only(['title']);
        $data['course_id'] = $course->id;

        // Determinar posición
        if ($request->has('position')) {
            $data['position'] = $request->position;
        } else {
            $maxPosition = $course->sections()->max('position') ?? 0;
            $data['position'] = $maxPosition + 1;
        }

        $section = CourseSection::create($data);

        return response()->json([
            'success' => true,
            'data' => $section->load(['lessons' => function ($query) {
                $query->ordered();
            }]),
            'meta' => [
                'message' => 'Sección creada exitosamente'
            ]
        ], 201);
    }

    /**
     * Actualizar una sección
     */
    public function update(Request $request, $courseId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($id);
        $user = $request->user();

        // Verificar permisos
        if (!$this->canManageCourse($user, $course)) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para gestionar este curso'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'position' => 'sometimes|integer|min:1',
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

        $data = $request->only(['title', 'position']);
        $section->update($data);

        return response()->json([
            'success' => true,
            'data' => $section->fresh(['lessons' => function ($query) {
                $query->ordered();
            }]),
            'meta' => [
                'message' => 'Sección actualizada exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar una sección
     */
    public function destroy(Request $request, $courseId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($id);
        $user = $request->user();

        // Verificar permisos
        if (!$this->canManageCourse($user, $course)) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para gestionar este curso'
                ]
            ], 403);
        }

        // Verificar si tiene lecciones
        if ($section->lessons()->count() > 0) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No se puede eliminar una sección que tiene lecciones. Elimina las lecciones primero.'
                ]
            ], 422);
        }

        $section->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Sección eliminada exitosamente'
            ]
        ]);
    }

    /**
     * Verificar si el usuario puede gestionar el curso
     */
    private function canManageCourse($user, $course)
    {
        return $user->hasRole('admin') ||
               $course->created_by === $user->id ||
               $course->instructors()->where('users.id', $user->id)->exists();
    }
}