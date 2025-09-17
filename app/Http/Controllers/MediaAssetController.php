<?php

namespace App\Http\Controllers;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MediaAssetController extends Controller
{
    /**
     * Listar todos los archivos multimedia del usuario autenticado.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user() ?? User::first();
        if (!$user) {
            return response()->json(['message' => 'No users available'], 500);
        }

        $query = MediaAsset::where('owner_id', $user->id);

        // Filtros opcionales
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('provider')) {
            $query->where('provider', $request->provider);
        }

        $mediaAssets = $query->orderBy('created_at', 'desc')->get();

        return response()->json($mediaAssets);
    }

    /**
     * Mostrar un archivo multimedia específico.
     */
    public function show($id): JsonResponse
    {
        $user = Auth::user() ?? User::first();
        if (!$user) {
            return response()->json(['message' => 'No users available'], 500);
        }

        $mediaAsset = MediaAsset::where('id', $id)
            ->where('owner_id', $user->id)
            ->first();

        if (!$mediaAsset) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        return response()->json($mediaAsset);
    }

    /**
     * Subir un nuevo archivo multimedia.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file',
            'type' => 'nullable|string',
            'provider' => 'nullable|string',
        ]);

        $user = Auth::user() ?? User::first(); // Use first user if not authenticated
        if (!$user) {
            return response()->json(['message' => 'No users available'], 500);
        }
        $file = $request->file('file');

        // Generar path único
        $path = $file->store('uploads/' . $user->id . '/' . date('Y/m'), 'public');

        // Crear registro en BD
        $mediaAsset = MediaAsset::create([
            'owner_id' => $user->id,
            'type' => $request->type,
            'provider' => $request->provider ?? 'local',
            'url' => Storage::disk('public')->url($path),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'duration_seconds' => $this->getDuration($file, $request->type),
            'created_at' => now(),
        ]);

        return response()->json($mediaAsset, 201);
    }

    /**
     * Actualizar un archivo multimedia.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = Auth::user() ?? User::first();
        if (!$user) {
            return response()->json(['message' => 'No users available'], 500);
        }

        $mediaAsset = MediaAsset::where('id', $id)
            ->where('owner_id', $user->id)
            ->first();

        if (!$mediaAsset) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        $request->validate([
            'type' => 'sometimes|string|in:video,audio,image,document',
            'provider' => 'sometimes|string',
        ]);

        $mediaAsset->update($request->only(['type', 'provider']));

        return response()->json($mediaAsset);
    }

    /**
     * Eliminar un archivo multimedia.
     */
    public function destroy($id): JsonResponse
    {
        $user = Auth::user() ?? User::first();
        if (!$user) {
            return response()->json(['message' => 'No users available'], 500);
        }

        $mediaAsset = MediaAsset::where('id', $id)
            ->where('owner_id', $user->id)
            ->first();

        if (!$mediaAsset) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        // Eliminar archivo del storage
        if ($mediaAsset->storage_path && Storage::disk('public')->exists($mediaAsset->storage_path)) {
            Storage::disk('public')->delete($mediaAsset->storage_path);
        }

        $mediaAsset->delete();

        return response()->json(['message' => 'Archivo eliminado']);
    }

    /**
     * Obtener duración para videos/audio (simplificado, en producción usar FFmpeg).
     */
    private function getDuration($file, $type): ?int
    {
        if (in_array($type, ['video', 'audio'])) {
            // Aquí se podría integrar FFmpeg para obtener duración real
            // Por ahora, devolver null
            return null;
        }
        return null;
    }
}