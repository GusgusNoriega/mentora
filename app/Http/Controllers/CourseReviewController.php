<?php

namespace App\Http\Controllers;

use App\Models\CourseReview;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CourseReviewController extends Controller
{
    public function index(Request $request, $courseId = null)
    {
        $query = CourseReview::query()->with(['user', 'course'])->where('is_public', true);

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'meta' => [
                'message' => 'Reseñas obtenidas exitosamente'
            ]
        ]);
    }

    public function show($id)
    {
        $review = CourseReview::with(['user', 'course'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $review,
            'meta' => [
                'message' => 'Reseña obtenida exitosamente'
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
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

        // Verificar si ya reseñó
        if (CourseReview::where('user_id', $user->id)->where('course_id', $request->course_id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Ya has reseñado este curso'
                ]
            ], 422);
        }

        // Verificar si completó el curso
        $progress = $course->progress()->where('user_id', $user->id)->first();
        if (!$progress || !$progress->completed_at) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Debes completar el curso para reseñarlo'
                ]
            ], 422);
        }

        $data = $request->only(['course_id', 'rating', 'comment', 'is_public']);
        $data['user_id'] = $user->id;
        $data['created_at'] = Carbon::now();

        $review = CourseReview::create($data);

        return response()->json([
            'success' => true,
            'data' => $review->load(['user', 'course']),
            'meta' => [
                'message' => 'Reseña creada exitosamente'
            ]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $review = CourseReview::findOrFail($id);
        $user = $request->user();

        if ($review->user_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para actualizar esta reseña'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
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

        $data = $request->only(['rating', 'comment', 'is_public']);
        $review->update($data);

        return response()->json([
            'success' => true,
            'data' => $review->fresh(['user', 'course']),
            'meta' => [
                'message' => 'Reseña actualizada exitosamente'
            ]
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $review = CourseReview::findOrFail($id);
        $user = $request->user();

        if ($review->user_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para eliminar esta reseña'
                ]
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Reseña eliminada exitosamente'
            ]
        ]);
    }
}