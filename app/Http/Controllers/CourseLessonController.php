<?php

namespace App\Http\Controllers;

use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Models\Course;
use App\Models\MediaAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseLessonController extends Controller
{
    /**
     * Mostrar lista de lecciones de una sección
     */
    public function index(Request $request, $courseId, $sectionId)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);

        $lessons = $section->lessons()->ordered()->with(['media', 'quiz'])->withCount('progressRecords')->get();

        return response()->json([
            'success' => true,
            'data' => $lessons,
            'meta' => [
                'message' => 'Lecciones obtenidas exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar una lección específica
     */
    public function show($courseId, $sectionId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->with(['media', 'quiz' => function ($query) {
            $query->with(['questions' => function ($q) {
                $q->with('options');
            }]);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $lesson,
            'meta' => [
                'message' => 'Lección obtenida exitosamente'
            ]
        ]);
    }

    /**
     * Crear una nueva lección
     */
    public function store(Request $request, $courseId, $sectionId)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
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
            'content_type' => 'required|in:video,text,pdf,external',
            'content_url' => 'nullable|url',
            'duration_seconds' => 'nullable|integer|min:0',
            'is_preview' => 'boolean',
            'position' => 'nullable|integer|min:1',
            'media_ids' => 'nullable|array',
            'media_ids.*' => 'exists:media_assets,id',
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

        $data = $request->only([
            'title', 'content_type', 'content_url', 'duration_seconds', 'is_preview'
        ]);
        $data['section_id'] = $section->id;

        // Determinar posición
        if ($request->has('position')) {
            $data['position'] = $request->position;
        } else {
            $maxPosition = $section->lessons()->max('position') ?? 0;
            $data['position'] = $maxPosition + 1;
        }

        $lesson = CourseLesson::create($data);

        // Asociar medios
        if ($request->has('media_ids')) {
            $lesson->media()->attach($request->media_ids);
        }

        return response()->json([
            'success' => true,
            'data' => $lesson->load(['media', 'quiz']),
            'meta' => [
                'message' => 'Lección creada exitosamente'
            ]
        ], 201);
    }

    /**
     * Actualizar una lección
     */
    public function update(Request $request, $courseId, $sectionId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($id);
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
            'content_type' => 'sometimes|required|in:video,text,pdf,external',
            'content_url' => 'nullable|url',
            'duration_seconds' => 'nullable|integer|min:0',
            'is_preview' => 'boolean',
            'position' => 'sometimes|integer|min:1',
            'media_ids' => 'nullable|array',
            'media_ids.*' => 'exists:media_assets,id',
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

        $data = $request->only([
            'title', 'content_type', 'content_url', 'duration_seconds', 'is_preview', 'position'
        ]);
        $lesson->update($data);

        // Actualizar medios
        if ($request->has('media_ids')) {
            $lesson->media()->sync($request->media_ids);
        }

        return response()->json([
            'success' => true,
            'data' => $lesson->fresh(['media', 'quiz']),
            'meta' => [
                'message' => 'Lección actualizada exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar una lección
     */
    public function destroy(Request $request, $courseId, $sectionId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($id);
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

        // Verificar si tiene progreso de usuarios
        if ($lesson->progressRecords()->count() > 0) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No se puede eliminar una lección que tiene registros de progreso.'
                ]
            ], 422);
        }

        $lesson->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Lección eliminada exitosamente'
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