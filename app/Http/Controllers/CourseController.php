<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Mostrar lista de cursos
     */
    public function index(Request $request)
    {
        $query = Course::query()->with(['creator', 'instructors', 'categories', 'tags'])->withCount(['sections', 'lessons', 'enrollments', 'reviews']);

        // Filtros opcionales
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        if ($request->has('language')) {
            $query->where('language', $request->language);
        }

        if ($request->has('access_mode')) {
            $query->where('access_mode', $request->access_mode);
        }

        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        if ($request->has('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        if ($request->has('instructor_id')) {
            $query->whereHas('instructors', function ($q) use ($request) {
                $q->where('users.id', $request->instructor_id);
            });
        }

        if ($request->has('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        // Ordenar por defecto por created_at desc
        $query->orderBy('created_at', 'desc');

        $courses = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $courses,
            'meta' => [
                'message' => 'Cursos obtenidos exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar un curso específico
     */
    public function show($id)
    {
        $course = Course::with([
            'creator',
            'instructors',
            'categories',
            'tags',
            'sections' => function ($query) {
                $query->ordered()->with(['lessons' => function ($q) {
                    $q->ordered()->with(['media', 'quiz']);
                }]);
            },
            'reviews' => function ($query) {
                $query->with('user');
            },
            'plans'
        ])->withCount(['enrollments', 'reviews'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $course,
            'meta' => [
                'message' => 'Curso obtenido exitosamente'
            ]
        ]);
    }

    /**
     * Crear un nuevo curso
     */
    public function store(Request $request)
    {
        // Verificar permisos: admin o instructor
        $user = $request->user();
        if (!$user->hasRole(['admin', 'instructor'])) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para crear cursos'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses',
            'summary' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'thumbnail_url' => 'nullable|url',
            'level' => 'required|in:beginner,intermediate,advanced',
            'language' => 'required|string|max:10',
            'status' => 'required|in:draft,published,archived',
            'access_mode' => 'required|in:free,paid,subscription',
            'price_cents' => 'nullable|integer|min:0',
            'currency' => 'nullable|string|max:3',
            'published_at' => 'nullable|date',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'instructor_ids' => 'nullable|array',
            'instructor_ids.*' => 'exists:users,id',
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
            'title', 'summary', 'description', 'thumbnail_url', 'level',
            'language', 'status', 'access_mode', 'price_cents', 'currency', 'published_at'
        ]);
        $data['slug'] = $request->slug ?? Str::slug($request->title);
        $data['created_by'] = $user->id;

        // Verificar unicidad del slug
        if (Course::where('slug', $data['slug'])->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'El slug ya existe, elige otro título'
                ]
            ], 422);
        }

        $course = Course::create($data);

        // Asociar categorías, tags, instructores
        if ($request->has('category_ids')) {
            $course->categories()->attach($request->category_ids);
        }
        if ($request->has('tag_ids')) {
            $course->tags()->attach($request->tag_ids);
        }
        if ($request->has('instructor_ids')) {
            $course->instructors()->attach($request->instructor_ids);
        }

        return response()->json([
            'success' => true,
            'data' => $course->load(['creator', 'instructors', 'categories', 'tags']),
            'meta' => [
                'message' => 'Curso creado exitosamente'
            ]
        ], 201);
    }

    /**
     * Actualizar un curso
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $user = $request->user();

        // Verificar permisos: creador, instructor del curso, o admin
        if (!$user->hasRole('admin') && $course->created_by !== $user->id && !$course->instructors()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para actualizar este curso'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:courses,slug,' . $course->id],
            'summary' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'thumbnail_url' => 'nullable|url',
            'level' => 'sometimes|required|in:beginner,intermediate,advanced',
            'language' => 'sometimes|required|string|max:10',
            'status' => 'sometimes|required|in:draft,published,archived',
            'access_mode' => 'sometimes|required|in:free,paid,subscription',
            'price_cents' => 'nullable|integer|min:0',
            'currency' => 'nullable|string|max:3',
            'published_at' => 'nullable|date',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'instructor_ids' => 'nullable|array',
            'instructor_ids.*' => 'exists:users,id',
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
            'title', 'summary', 'description', 'thumbnail_url', 'level',
            'language', 'status', 'access_mode', 'price_cents', 'currency', 'published_at'
        ]);

        if ($request->has('slug')) {
            $data['slug'] = $request->slug;
        } elseif ($request->has('title')) {
            $data['slug'] = Str::slug($request->title);
            // Verificar unicidad
            if (Course::where('slug', $data['slug'])->where('id', '!=', $course->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'meta' => [
                        'message' => 'El slug generado ya existe, especifica uno manualmente'
                    ]
                ], 422);
            }
        }

        $course->update($data);

        // Actualizar asociaciones
        if ($request->has('category_ids')) {
            $course->categories()->sync($request->category_ids);
        }
        if ($request->has('tag_ids')) {
            $course->tags()->sync($request->tag_ids);
        }
        if ($request->has('instructor_ids')) {
            $course->instructors()->sync($request->instructor_ids);
        }

        return response()->json([
            'success' => true,
            'data' => $course->fresh(['creator', 'instructors', 'categories', 'tags']),
            'meta' => [
                'message' => 'Curso actualizado exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar un curso
     */
    public function destroy(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $user = $request->user();

        // Verificar permisos: creador o admin
        if (!$user->hasRole('admin') && $course->created_by !== $user->id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para eliminar este curso'
                ]
            ], 403);
        }

        // Verificar si tiene inscripciones activas
        if ($course->enrollments()->whereNull('expires_at')->orWhere('expires_at', '>', now())->count() > 0) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No se puede eliminar un curso que tiene estudiantes inscritos activos'
                ]
            ], 422);
        }

        // Verificar si tiene secciones o lecciones
        if ($course->sections()->count() > 0) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No se puede eliminar un curso que tiene secciones. Elimina las secciones primero.'
                ]
            ], 422);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Curso eliminado exitosamente'
            ]
        ]);
    }
}