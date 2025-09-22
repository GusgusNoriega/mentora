<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    /**
     * Mostrar lista de inscripciones
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Enrollment::query()->with(['user', 'course']);

        // Filtros
        if ($request->has('user_id')) {
            // Solo admin o el propio usuario
            if (!$user->hasRole('admin') && $request->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'meta' => [
                        'message' => 'No tienes permisos para ver inscripciones de otros usuarios'
                    ]
                ], 403);
            }
            $query->where('user_id', $request->user_id);
        } else {
            // Si no especifica user_id, mostrar solo las del usuario autenticado
            $query->where('user_id', $user->id);
        }

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('source')) {
            $query->where('source', $request->source);
        }

        $enrollments = $query->orderBy('enrolled_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $enrollments,
            'meta' => [
                'message' => 'Inscripciones obtenidas exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar una inscripción específica
     */
    public function show(Request $request, $id)
    {
        $enrollment = Enrollment::with(['user', 'course'])->findOrFail($id);
        $user = $request->user();

        // Verificar permisos: propio usuario, instructor del curso, o admin
        if (!$user->hasRole('admin') &&
            $enrollment->user_id !== $user->id &&
            !$enrollment->course->instructors()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para ver esta inscripción'
                ]
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $enrollment,
            'meta' => [
                'message' => 'Inscripción obtenida exitosamente'
            ]
        ]);
    }

    /**
     * Crear una nueva inscripción
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'source' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
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
        $enrollUser = User::findOrFail($request->user_id);

        // Verificar permisos: el usuario autenticado puede inscribirse a sí mismo, o admin puede inscribir a otros
        if (!$user->hasRole('admin') && $request->user_id != $user->id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para inscribir a otros usuarios'
                ]
            ], 403);
        }

        // Verificar si ya está inscrito
        if (Enrollment::where('user_id', $request->user_id)->where('course_id', $request->course_id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'El usuario ya está inscrito en este curso'
                ]
            ], 422);
        }

        // Verificar si el curso está publicado
        if ($course->status !== 'published') {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'El curso no está disponible para inscripción'
                ]
            ], 422);
        }

        // Para cursos pagos, verificar si hay transacción de pago (simplificado)
        if ($course->access_mode === 'paid') {
            // Aquí se podría verificar PaymentTransaction, pero por simplicidad asumimos que se maneja externamente
            // O agregar lógica para verificar pago
        }

        $data = $request->only(['user_id', 'course_id', 'source', 'expires_at']);
        $data['enrolled_at'] = Carbon::now();

        $enrollment = Enrollment::create($data);

        return response()->json([
            'success' => true,
            'data' => $enrollment->load(['user', 'course']),
            'meta' => [
                'message' => 'Inscripción creada exitosamente'
            ]
        ], 201);
    }

    /**
     * Actualizar una inscripción
     */
    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $user = $request->user();

        // Verificar permisos: admin o instructor del curso
        if (!$user->hasRole('admin') && !$enrollment->course->instructors()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para actualizar esta inscripción'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'source' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
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

        $data = $request->only(['source', 'expires_at']);
        $enrollment->update($data);

        return response()->json([
            'success' => true,
            'data' => $enrollment->fresh(['user', 'course']),
            'meta' => [
                'message' => 'Inscripción actualizada exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar una inscripción
     */
    public function destroy(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $user = $request->user();

        // Verificar permisos: el propio usuario, instructor del curso, o admin
        if (!$user->hasRole('admin') &&
            $enrollment->user_id !== $user->id &&
            !$enrollment->course->instructors()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para eliminar esta inscripción'
                ]
            ], 403);
        }

        $enrollment->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Inscripción eliminada exitosamente'
            ]
        ]);
    }
}