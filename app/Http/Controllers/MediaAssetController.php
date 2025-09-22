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
     * GET /api/media
     * Listar con filtros + paginación.
     * Query params: type, provider, q, page, per_page (default 20)
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user() ?? User::first();
        if (!$user) {
            return response()->json(['message' => 'No users available'], 500);
        }

        $perPage = (int)($request->integer('per_page') ?: 20);
        $perPage = max(1, min(10, $perPage));

        $query = MediaAsset::query();
        if (!$user->hasPermissionTo('view.all.media')) {
            $query->where('owner_id', $user->id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('provider')) {
            $query->where('provider', $request->string('provider'));
        }

        // === BÚSQUEDA unificada ===
        // Acepta q y/o name, pero los combina como OR contra múltiples columnas.
        $q = $request->filled('q') ? (string)$request->string('q') : null;
        $name = $request->filled('name') ? (string)$request->string('name') : null;

        if ($q !== null || $name !== null) {
            $termQ = $q ?? $name;      // prioriza q si viene; si no, usa name
            $termName = $name ?? $q;   // prioriza name si viene; si no, usa q

            $query->where(function ($qb) use ($termQ, $termName) {
                // Coincidencias por q (url, storage_path, mime_type)
                if ($termQ !== null) {
                    $qb->where(function ($qBuilder) use ($termQ) {
                        $qBuilder->where('url', 'like', "%{$termQ}%")
                                ->orWhere('storage_path', 'like', "%{$termQ}%")
                                ->orWhere('mime_type', 'like', "%{$termQ}%");
                    });
                }

                // OR por name (campo de BD)
                if ($termName !== null) {
                    $qb->orWhere('name', 'like', "%{$termName}%");
                }
            });
        }

        // Filtro por lista de IDs
        if ($request->has('ids')) {
            $ids = $request->input('ids');
            if (is_array($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        
        if ($request->filled('alt')) {
            $query->where('alt', 'like', "%{$request->string('alt')}%");
        }

        $mediaAssets = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($mediaAssets);
    }


    /**
     * GET /api/media/{id}
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
     * POST /api/media
     * Crea un media local (archivo) O externo (url).
     *
     * Body (archivo):
     * - file: required_without:url | file | max:51200
     * - type: nullable|in:image,video,audio,document (si no se envía se infiere)
     * - provider: nullable (default: local)
     *
     * Body (externo):
     * - url: required_without:file | url
     * - type: required_with:url|in:image,video,audio,document
     * - provider: required_with:url (ej. vimeo, youtube, external)
     * - duration_seconds: nullable|integer
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required_without:url|file|max:51200',
            'url' => 'required_without:file|url',
            'type' => 'nullable|string|in:image,video,audio,document',
            'provider' => 'nullable|string',
            'duration_seconds' => 'nullable|integer|min:0',
            'name' => 'nullable|string|max:255',
            'alt' => 'nullable|string|max:255',
        ], [
            'file.required_without' => 'Debes enviar un archivo o una url.',
            'url.required_without' => 'Debes enviar un archivo o una url.',
        ]);

        $user = Auth::user() ?? User::first();
        if (!$user) {
            return response()->json(['message' => 'No users available'], 500);
        }

        // Caso 1: archivo local
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Seguridad: evitar "Path cannot be empty"
            if (!$file->isValid()) {
                return response()->json(['message' => 'Archivo inválido o no recibido correctamente'], 422);
            }

            $mimeType = $file->getMimeType() ?? $file->getClientMimeType();
            $type = $request->type ?? $this->determineTypeFromMime((string)$mimeType);

            $allowedMimes = $this->allowedMimes();
            if (!isset($allowedMimes[$type]) || !in_array($mimeType, $allowedMimes[$type], true)) {
                return response()->json(['message' => 'Tipo de archivo no permitido'], 422);
            }

            // Guardar en disco
            $disk = 'public';
            $dir = 'uploads/' . $user->id . '/' . date('Y/m');
            $path = $file->store($dir, $disk);

            $mediaAsset = MediaAsset::create([
                'owner_id' => $user->id,
                'type' => $type,
                'provider' => $request->provider ?? 'local',
                'url' => Storage::disk($disk)->url($path),
                'storage_path' => $path,
                'mime_type' => $mimeType,
                'size_bytes' => $file->getSize(),
                'duration_seconds' => $this->getDuration($file, $type),
                'name' => $request->name,
                'alt' => $request->alt,
                'created_at' => now(),
            ]);

            return response()->json($mediaAsset, 201);
        }

        // Caso 2: externo por URL (p. ej., Vimeo)
        // Para URL, exigir type y provider si no vinieron en el body.
        $request->validate([
            'type' => 'required|in:image,video,audio,document',
            'provider' => 'required|string',
        ]);

        $mediaAsset = MediaAsset::create([
            'owner_id' => $user->id,
            'type' => $request->string('type'),
            'provider' => $request->string('provider'),
            'url' => $request->string('url'),
            'storage_path' => null,
            'mime_type' => null,
            'size_bytes' => null,
            'duration_seconds' => $request->has('duration_seconds') ? $request->integer('duration_seconds') : null,
            'name' => $request->name,
            'alt' => $request->alt,
            'created_at' => now(),
        ]);

        return response()->json($mediaAsset, 201);
    }

    /**
     * PATCH /api/media/{id}
     * Actualiza metadatos y permite:
     * - Reemplazar archivo (borra el anterior si era local).
     * - Cambiar a URL externa (borra archivo local si existía).
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = Auth::user() ?? User::first();
        if (!$user) {
            return response()->json(['message' => 'No users available'], 500);
        }

        $query = MediaAsset::where('id', $id);
        if (!$user->hasPermissionTo('view.all.media')) {
            $query->where('owner_id', $user->id);
        }
        $mediaAsset = $query->first();

        if (!$mediaAsset) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        $request->validate([
            'file' => 'sometimes|file|max:51200',
            'url' => 'sometimes|url',
            'type' => 'sometimes|string|in:image,video,audio,document',
            'provider' => 'sometimes|string',
            'duration_seconds' => 'sometimes|integer|min:0',
            'name' => 'sometimes|string|max:255',
            'alt' => 'sometimes|string|max:255',
        ]);

        $disk = 'public';

        // Si viene un nuevo archivo, reemplazamos el anterior (si era local)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if (!$file->isValid()) {
                return response()->json(['message' => 'Archivo inválido'], 422);
            }

            $mimeType = $file->getMimeType() ?? $file->getClientMimeType();
            $type = $request->type ?? $this->determineTypeFromMime((string)$mimeType);

            $allowedMimes = $this->allowedMimes();
            if (!isset($allowedMimes[$type]) || !in_array($mimeType, $allowedMimes[$type], true)) {
                return response()->json(['message' => 'Tipo de archivo no permitido'], 422);
            }

            // Borrar el archivo anterior si era local
            if ($mediaAsset->storage_path && Storage::disk($disk)->exists($mediaAsset->storage_path)) {
                Storage::disk($disk)->delete($mediaAsset->storage_path);
            }

            $dir = 'uploads/' . $user->id . '/' . date('Y/m');
            $path = $file->store($dir, $disk);

            $mediaAsset->fill([
                'type' => $type,
                'provider' => $request->provider ?? 'local',
                'url' => Storage::disk($disk)->url($path),
                'storage_path' => $path,
                'mime_type' => $mimeType,
                'size_bytes' => $file->getSize(),
                'duration_seconds' => $this->getDuration($file, $type),
            ]);
        }

        // Si viene una URL, lo convertimos a externo (y borramos el archivo local si existía)
        if ($request->filled('url')) {
            if ($mediaAsset->storage_path && Storage::disk($disk)->exists($mediaAsset->storage_path)) {
                Storage::disk($disk)->delete($mediaAsset->storage_path);
            }
            $mediaAsset->fill([
                'url' => $request->string('url'),
                'storage_path' => null,
                'mime_type' => null,
                'size_bytes' => null,
                'provider' => $request->provider ?? $mediaAsset->provider ?? 'external',
            ]);

            // Si cambia a URL y no se envía type, mantenemos el existente.
            if ($request->filled('type')) {
                $mediaAsset->type = $request->string('type');
            }
        }

        // Metadatos sueltos
        if ($request->filled('type')) {
            $mediaAsset->type = $request->string('type');
        }
        if ($request->filled('provider')) {
            $mediaAsset->provider = $request->string('provider');
        }
        if ($request->has('duration_seconds')) {
            $mediaAsset->duration_seconds = $request->integer('duration_seconds');
        }
        if ($request->has('name')) {
            $mediaAsset->name = $request->string('name');
        }
        if ($request->has('alt')) {
            $mediaAsset->alt = $request->string('alt');
        }

        $mediaAsset->save();

        return response()->json($mediaAsset);
    }

    /**
     * DELETE /api/media/{id}
     * Borra registro y archivo local si existe.
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

        if ($mediaAsset->storage_path && Storage::disk('public')->exists($mediaAsset->storage_path)) {
            Storage::disk('public')->delete($mediaAsset->storage_path);
        }

        $mediaAsset->delete();

        return response()->json(['message' => 'Archivo eliminado']);
    }

    /**
     * Helpers
     */
    private function determineTypeFromMime(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } elseif (in_array($mimeType, [
            'application/pdf',
            'text/plain',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
        ], true)) {
            return 'document';
        }
        return 'document';
    }

    private function allowedMimes(): array
    {
        return [
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'video' => ['video/mp4', 'video/avi', 'video/quicktime', 'video/mov'],
            'audio' => ['audio/mpeg', 'audio/wav', 'audio/mp3'],
            'document' => [
                'application/pdf',
                'text/plain',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
            ],
        ];
    }

    /**
     * En producción puedes integrar FFmpeg y calcular duración real.
     * Aquí devolvemos null por simplicidad.
     */
    private function getDuration($file, $type): ?int
    {
        if (in_array($type, ['video', 'audio'], true)) {
            return null;
        }
        return null;
    }
}