<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CertificateController extends Controller
{
    /**
     * Mostrar lista de certificados
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Certificate::query()->with(['user', 'course', 'template']);

        // Filtros
        if ($request->has('user_id')) {
            if (!$user->hasRole('admin') && $request->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'meta' => [
                        'message' => 'No tienes permisos para ver certificados de otros usuarios'
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

        $certificates = $query->orderBy('issued_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $certificates,
            'meta' => [
                'message' => 'Certificados obtenidos exitosamente'
            ]
        ]);
    }

    /**
     * Mostrar un certificado específico
     */
    public function show(Request $request, $id)
    {
        $certificate = Certificate::with(['user', 'course', 'template'])->findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin') && $certificate->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para ver este certificado'
                ]
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $certificate,
            'meta' => [
                'message' => 'Certificado obtenido exitosamente'
            ]
        ]);
    }

    /**
     * Generar un nuevo certificado
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'template_id' => 'nullable|exists:certificate_templates,id',
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

        // Verificar si el usuario completó el curso
        $progress = $course->progress()->where('user_id', $user->id)->first();
        if (!$progress || !$progress->completed_at) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No has completado este curso aún'
                ]
            ], 422);
        }

        // Verificar si ya tiene certificado
        if (Certificate::where('user_id', $user->id)->where('course_id', $request->course_id)->exists()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Ya tienes un certificado para este curso'
                ]
            ], 422);
        }

        // Usar template por defecto si no se especifica
        $templateId = $request->template_id ?? CertificateTemplate::first()->id;

        $data = [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'template_id' => $templateId,
            'code' => strtoupper(Str::random(10)),
            'issued_at' => Carbon::now(),
            'grade' => $progress->progress_pct,
            'public_url' => null, // Generar URL aquí si es necesario
        ];

        $certificate = Certificate::create($data);

        return response()->json([
            'success' => true,
            'data' => $certificate->load(['user', 'course', 'template']),
            'meta' => [
                'message' => 'Certificado generado exitosamente'
            ]
        ], 201);
    }

    /**
     * Actualizar un certificado (solo admin)
     */
    public function update(Request $request, $id)
    {
        $certificate = Certificate::findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Solo administradores pueden actualizar certificados'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'template_id' => 'nullable|exists:certificate_templates,id',
            'code' => 'nullable|string|max:255|unique:certificates,code,' . $certificate->id,
            'grade' => 'nullable|numeric|min:0|max:100',
            'expires_at' => 'nullable|date',
            'public_url' => 'nullable|url',
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

        $data = $request->only(['template_id', 'code', 'grade', 'expires_at', 'public_url']);
        $certificate->update($data);

        return response()->json([
            'success' => true,
            'data' => $certificate->fresh(['user', 'course', 'template']),
            'meta' => [
                'message' => 'Certificado actualizado exitosamente'
            ]
        ]);
    }

    /**
     * Eliminar un certificado (solo admin)
     */
    public function destroy(Request $request, $id)
    {
        $certificate = Certificate::findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Solo administradores pueden eliminar certificados'
                ]
            ], 403);
        }

        $certificate->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Certificado eliminado exitosamente'
            ]
        ]);
    }
}