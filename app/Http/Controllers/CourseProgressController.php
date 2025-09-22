<?php

namespace App\Http\Controllers;

use App\Models\CourseProgress;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CourseProgressController extends Controller
{
    /**
     * Mostrar lista de progreso de cursos
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = CourseProgress::query()->with(['user', 'course']);

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

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $progress = $query->orderBy('updated_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $progress,
            'meta' => [
                'message' => 'Progreso de cursos obtenido exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar progreso específico
     */
    public function show(Request $request, $id)
    {
        $progress = CourseProgress::with(['user', 'course'])->findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin') &&
            $progress->user_id !== $user->id &&
            !$progress->course->instructors()->where('users.id', $user->id)->exists()) {
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
     * Crear registro de progreso
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'progress_pct' => 'nullable|numeric|min:0|max:100',
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

        $course = Course::findOrFail($request->course_id);

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
        if (CourseProgress::where('user_id', $request->user_id)->where('course_id', $request->course_id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Ya existe un registro de progreso para este usuario y curso'
                ]
            ], 422);
        }

        $data = $request->only(['user_id', 'course_id', 'progress_pct']);
        if ($request->progress_pct == 100) {
            $data['completed_at'] = Carbon::now();
        }

        $progress = CourseProgress::create($data);

        return response()->json([
            'success' => true,
            'data' => $progress->load(['user', 'course']),
            'meta' => [
                'message' => 'Progreso creado exitosamente'
            ]
        ], 201);
    }

    /**
     * Actualizar progreso
     */
    public function update(Request $request, $id)
    {
        $progress = CourseProgress::findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin') &&
            $progress->user_id !== $user->id &&
            !$progress->course->instructors()->where('users.id', $user->id)->exists()) {
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

        $data = $request->only(['progress_pct']);
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
            'data' => $progress->fresh(['user', 'course']),
            'meta' => [
                'message' => 'Progreso actualizado exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar progreso
     */
    public function destroy(Request $request, $id)
    {
        $progress = CourseProgress::findOrFail($id);
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
                'message' => 'Progreso eliminado exitosamente'
            ]
        ]);
    }
}