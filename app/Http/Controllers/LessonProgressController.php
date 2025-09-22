<?php

namespace App\Http\Controllers;

use App\Models\LessonProgress;
use App\Models\CourseLesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LessonProgressController extends Controller
{
    /**
     * Mostrar lista de progreso de lecciones
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = LessonProgress::query()->with(['user', 'lesson.section.course']);

        // Filtros
        if ($request->has('user_id')) {
            if (!$user->hasRole('admin') && $request->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'meta' => [
                        'message' => 'No tienes permisos para ver progreso de otros usuarios'
                    ]
                ], 403);
            }
            $query->where('user_id', $request->user_id);
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->has('lesson_id')) {
            $query->where('lesson_id', $request->lesson_id);
        }

        if ($request->has('course_id')) {
            $query->whereHas('lesson.section.course', function ($q) use ($request) {
                $q->where('id', $request->course_id);
            });
        }

        $progress = $query->orderBy('updated_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $progress,
            'meta' => [
                'message' => 'Progreso de lecciones obtenido exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar progreso específico
     */
    public function show(Request $request, $id)
    {
        $progress = LessonProgress::with(['user', 'lesson.section.course'])->findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin') &&
            $progress->user_id !== $user->id &&
            !$progress->lesson->section->course->instructors()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para ver este progreso'
                ]
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $progress,
            'meta' => [
                'message' => 'Progreso obtenido exitosamente'
            ]
        ]);
    }

    /**
     * Crear registro de progreso de lección
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'lesson_id' => 'required|exists:course_lessons,id',
            'progress_pct' => 'nullable|numeric|min:0|max:100',
            'seconds_watched' => 'nullable|integer|min:0',
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

        $lesson = CourseLesson::findOrFail($request->lesson_id);

        // Verificar permisos
        if (!$user->hasRole('admin') && $request->user_id != $user->id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para crear progreso para otros usuarios'
                ]
            ], 403);
        }

        // Verificar si ya existe
        if (LessonProgress::where('user_id', $request->user_id)->where('lesson_id', $request->lesson_id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Ya existe un registro de progreso para esta lección y usuario'
                ]
            ], 422);
        }

        $data = $request->only(['user_id', 'lesson_id', 'progress_pct', 'seconds_watched']);
        if ($request->progress_pct == 100) {
            $data['completed_at'] = Carbon::now();
        }

        $progress = LessonProgress::create($data);

        return response()->json([
            'success' => true,
            'data' => $progress->load(['user', 'lesson.section.course']),
            'meta' => [
                'message' => 'Progreso de lección creado exitosamente'
            ]
        ], 201);
    }

    /**
     * Actualizar progreso de lección
     */
    public function update(Request $request, $id)
    {
        $progress = LessonProgress::findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin') &&
            $progress->user_id !== $user->id &&
            !$progress->lesson->section->course->instructors()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para actualizar este progreso'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'progress_pct' => 'sometimes|numeric|min:0|max:100',
            'seconds_watched' => 'sometimes|integer|min:0',
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

        $data = $request->only(['progress_pct', 'seconds_watched']);
        if (isset($data['progress_pct'])) {
            if ($data['progress_pct'] == 100 && !$progress->completed_at) {
                $data['completed_at'] = Carbon::now();
            } elseif ($data['progress_pct'] < 100) {
                $data['completed_at'] = null;
            }
        }

        $progress->update($data);

        return response()->json([
            'success' => true,
            'data' => $progress->fresh(['user', 'lesson.section.course']),
            'meta' => [
                'message' => 'Progreso de lección actualizado exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar progreso de lección
     */
    public function destroy(Request $request, $id)
    {
        $progress = LessonProgress::findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Solo administradores pueden eliminar registros de progreso'
                ]
            ], 403);
        }

        $progress->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Progreso de lección eliminado exitosamente'
            ]
        ]);
    }
}