<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    /**
     * Mostrar lista de quizzes de una lección
     */
    public function index(Request $request, $courseId, $sectionId, $lessonId)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);

        $quizzes = $lesson->quizzes()->with(['questions' => function ($query) {
            $query->with('options');
        }])->withCount('attempts')->get();

        return response()->json([
            'success' => true,
            'data' => $quizzes,
            'meta' => [
                'message' => 'Quizzes obtenidos exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar un quiz específico
     */
    public function show($courseId, $sectionId, $lessonId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->with(['questions' => function ($query) {
            $query->with('options');
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $quiz,
            'meta' => [
                'message' => 'Quiz obtenido exitosamente'
            ]
        ]);
    }

    /**
     * Crear un nuevo quiz
     */
    public function store(Request $request, $courseId, $sectionId, $lessonId)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
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
            'passing_score' => 'required|integer|min:0|max:100',
            'attempts_allowed' => 'nullable|integer|min:1',
            'time_limit_minutes' => 'nullable|integer|min:1',
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

        // Verificar si la lección ya tiene un quiz
        if ($lesson->quiz) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Esta lección ya tiene un quiz asociado'
                ]
            ], 422);
        }

        $data = $request->only(['title', 'passing_score', 'attempts_allowed', 'time_limit_minutes']);
        $data['lesson_id'] = $lesson->id;

        $quiz = Quiz::create($data);

        return response()->json([
            'success' => true,
            'data' => $quiz->load(['questions' => function ($query) {
                $query->with('options');
            }]),
            'meta' => [
                'message' => 'Quiz creado exitosamente'
            ]
        ], 201);
    }

    /**
     * Actualizar un quiz
     */
    public function update(Request $request, $courseId, $sectionId, $lessonId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->findOrFail($id);
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
            'passing_score' => 'sometimes|required|integer|min:0|max:100',
            'attempts_allowed' => 'nullable|integer|min:1',
            'time_limit_minutes' => 'nullable|integer|min:1',
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

        $data = $request->only(['title', 'passing_score', 'attempts_allowed', 'time_limit_minutes']);
        $quiz->update($data);

        return response()->json([
            'success' => true,
            'data' => $quiz->fresh(['questions' => function ($query) {
                $query->with('options');
            }]),
            'meta' => [
                'message' => 'Quiz actualizado exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar un quiz
     */
    public function destroy(Request $request, $courseId, $sectionId, $lessonId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->findOrFail($id);
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

        // Verificar si tiene intentos
        if ($quiz->attempts()->count() > 0) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No se puede eliminar un quiz que tiene intentos realizados'
                ]
            ], 422);
        }

        $quiz->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Quiz eliminado exitosamente'
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