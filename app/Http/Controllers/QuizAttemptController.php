<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class QuizAttemptController extends Controller
{
    /**
     * Mostrar lista de intentos de un quiz
     */
    public function index(Request $request, $courseId, $sectionId, $lessonId, $quizId)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->findOrFail($quizId);
        $user = $request->user();

        $query = $quiz->attempts()->with('user');

        // Filtros
        if ($request->has('user_id')) {
            if (!$user->hasRole('admin') && $request->user_id != $user->id && !$course->instructors()->where('users.id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'meta' => [
                        'message' => 'No tienes permisos para ver intentos de otros usuarios'
                    ]
                ], 403);
            }
            $query->where('user_id', $request->user_id);
        } else {
            // Mostrar solo intentos del usuario autenticado si no es instructor/admin
            if (!$user->hasRole('admin') && !$course->instructors()->where('users.id', $user->id)->exists()) {
                $query->where('user_id', $user->id);
            }
        }

        $attempts = $query->orderBy('started_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $attempts,
            'meta' => [
                'message' => 'Intentos obtenidos exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar un intento específico
     */
    public function show($courseId, $sectionId, $lessonId, $quizId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->findOrFail($quizId);
        $attempt = $quiz->attempts()->with(['user', 'answers.question'])->findOrFail($id);
        $user = request()->user();

        if (!$user->hasRole('admin') &&
            $attempt->user_id !== $user->id &&
            !$course->instructors()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para ver este intento'
                ]
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $attempt,
            'meta' => [
                'message' => 'Intento obtenido exitosamente'
            ]
        ]);
    }

    /**
     * Iniciar un nuevo intento
     */
    public function store(Request $request, $courseId, $sectionId, $lessonId, $quizId)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->findOrFail($quizId);
        $user = $request->user();

        // Verificar si el usuario está inscrito en el curso
        if (!$course->students()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Debes estar inscrito en el curso para realizar el quiz'
                ]
            ], 403);
        }

        // Verificar límite de intentos
        $attemptsCount = $quiz->attempts()->where('user_id', $user->id)->count();
        if ($quiz->attempts_allowed && $attemptsCount >= $quiz->attempts_allowed) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Has alcanzado el límite de intentos para este quiz'
                ]
            ], 422);
        }

        $data = [
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'started_at' => Carbon::now(),
        ];

        $attempt = QuizAttempt::create($data);

        return response()->json([
            'success' => true,
            'data' => $attempt->load('user'),
            'meta' => [
                'message' => 'Intento iniciado exitosamente'
            ]
        ], 201);
    }

    /**
     * Enviar respuestas y calcular resultado
     */
    public function update(Request $request, $courseId, $sectionId, $lessonId, $quizId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->findOrFail($quizId);
        $attempt = $quiz->attempts()->findOrFail($id);
        $user = $request->user();

        if ($attempt->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para actualizar este intento'
                ]
            ], 403);
        }

        if ($attempt->submitted_at) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Este intento ya ha sido enviado'
                ]
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:quiz_questions,id',
            'answers.*.option_ids' => 'nullable|array',
            'answers.*.option_ids.*' => 'exists:quiz_options,id',
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

        // Guardar respuestas
        $totalScore = 0;
        $maxScore = $quiz->questions()->sum('points');

        foreach ($request->answers as $answerData) {
            $question = $quiz->questions()->find($answerData['question_id']);
            if (!$question) continue;

            $isCorrect = false;
            $selectedOptions = $answerData['option_ids'] ?? [];

            if ($question->type === 'single_choice' || $question->type === 'true_false') {
                $correctOptions = $question->options()->where('is_correct', true)->pluck('id')->toArray();
                $isCorrect = count($selectedOptions) === 1 && in_array($selectedOptions[0], $correctOptions);
            } elseif ($question->type === 'multiple_choice') {
                $correctOptions = $question->options()->where('is_correct', true)->pluck('id')->toArray();
                $isCorrect = count(array_diff($correctOptions, $selectedOptions)) === 0 && count(array_diff($selectedOptions, $correctOptions)) === 0;
            }

            if ($isCorrect) {
                $totalScore += $question->points;
            }

            $attempt->answers()->create([
                'question_id' => $question->id,
                'selected_option_ids' => json_encode($selectedOptions),
                'is_correct' => $isCorrect,
            ]);
        }

        $passed = $totalScore >= ($maxScore * $quiz->passing_score / 100);

        $attempt->update([
            'submitted_at' => Carbon::now(),
            'score' => $totalScore,
            'passed' => $passed,
        ]);

        return response()->json([
            'success' => true,
            'data' => $attempt->fresh(['user', 'answers.question']),
            'meta' => [
                'message' => 'Intento enviado exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar un intento (solo admin)
     */
    public function destroy(Request $request, $courseId, $sectionId, $lessonId, $quizId, $id)
    {
        $course = Course::findOrFail($courseId);
        $section = $course->sections()->findOrFail($sectionId);
        $lesson = $section->lessons()->findOrFail($lessonId);
        $quiz = $lesson->quizzes()->findOrFail($quizId);
        $attempt = $quiz->attempts()->findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Solo administradores pueden eliminar intentos'
                ]
            ], 403);
        }

        $attempt->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Intento eliminado exitosamente'
            ]
        ]);
    }
}