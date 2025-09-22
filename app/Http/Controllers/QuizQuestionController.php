<?php

namespace App\Http\Controllers;

use App\Models\QuizQuestion;
use App\Models\Quiz;
use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizQuestionController extends Controller
{
    /**
     * Mostrar lista de preguntas de un quiz
     */
    public function index(Request $request, $courseId, $sectionId, $lessonId, $quizId)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->findOrFail($quizId);

        $questions = $quiz->questions()->with('options')->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'data' => $questions,
            'meta' => [
                'message' => 'Preguntas obtenidas exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar una pregunta específica
     */
    public function show($courseId, $sectionId, $lessonId, $quizId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->findOrFail($quizId);
        $question = $quiz->questions()->with('options')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $question,
            'meta' => [
                'message' => 'Pregunta obtenida exitosamente'
            ]
        ]);
    }

    /**
     * Crear una nueva pregunta
     */
    public function store(Request $request, $courseId, $sectionId, $lessonId, $quizId)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->findOrFail($quizId);
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
            'type' => 'required|in:single_choice,multiple_choice,true_false',
            'text' => 'required|string',
            'points' => 'required|integer|min:1',
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

        $data = $request->only(['type', 'text', 'points']);
        $data['quiz_id'] = $quiz->id;

        $question = QuizQuestion::create($data);

        return response()->json([
            'success' => true,
            'data' => $question->load('options'),
            'meta' => [
                'message' => 'Pregunta creada exitosamente'
            ]
        ], 201);
    }

    /**
     * Actualizar una pregunta
     */
    public function update(Request $request, $courseId, $sectionId, $lessonId, $quizId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->findOrFail($quizId);
        $question = $quiz->questions()->findOrFail($id);
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
            'type' => 'sometimes|required|in:single_choice,multiple_choice,true_false',
            'text' => 'sometimes|required|string',
            'points' => 'sometimes|required|integer|min:1',
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

        $data = $request->only(['type', 'text', 'points']);
        $question->update($data);

        return response()->json([
            'success' => true,
            'data' => $question->fresh('options'),
            'meta' => [
                'message' => 'Pregunta actualizada exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar una pregunta
     */
    public function destroy(Request $request, $courseId, $sectionId, $lessonId, $quizId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->findOrFail($quizId);
        $question = $quiz->questions()->findOrFail($id);
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

        // Verificar si tiene respuestas
        if ($question->answers()->count() > 0) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No se puede eliminar una pregunta que tiene respuestas registradas'
                ]
            ], 422);
        }

        $question->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Pregunta eliminada exitosamente'
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