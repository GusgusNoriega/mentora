<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $courses = $user->wishlistedCourses()->with(['creator', 'categories', 'tags'])->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $courses,
            'meta' => [
                'message' => 'Cursos en wishlist obtenidos exitosamente'
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $course = Course::findOrFail($request->course_id);

        if (!$user->wishlistedCourses()->where('courses.id', $course->id)->exists()) {
            $user->wishlistedCourses()->attach($course->id, ['created_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'data' => $course,
            'meta' => [
                'message' => 'Curso agregado a wishlist'
            ]
        ], 201);
    }

    public function destroy(Request $request, $courseId)
    {
        $user = $request->user();
        $user->wishlistedCourses()->detach($courseId);

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Curso removido de wishlist'
            ]
        ]);
    }
}