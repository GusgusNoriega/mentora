# Documentación API - MediaAssetController

## Descripción General
El `MediaAssetController` gestiona los activos multimedia del sistema, incluyendo imágenes, videos, audio y documentos. Soporta tanto archivos locales como URLs externas.

## Endpoints

### GET /api/media
Lista los activos multimedia del usuario con filtros y paginación.

#### Parámetros de Query
- `per_page` (integer, opcional): Número de elementos por página (1-10, default: 20)
- `type` (string, opcional): Filtrar por tipo (image, video, audio, document)
- `provider` (string, opcional): Filtrar por proveedor (local, vimeo, youtube, etc.)
- `q` (string, opcional): Búsqueda unificada en url, storage_path, mime_type
- `name` (string, opcional): Búsqueda por nombre
- `ids` (array, opcional): Filtrar por lista de IDs
- `alt` (string, opcional): Búsqueda en texto alternativo
- `page` (integer, opcional): Página de paginación

#### Respuesta Exitosa (200)
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "owner_id": 1,
      "type": "image",
      "provider": "local",
      "url": "http://localhost:8000/storage/uploads/1/2025/09/file.jpg",
      "storage_path": "uploads/1/2025/09/file.jpg",
      "mime_type": "image/jpeg",
      "size_bytes": 12345,
      "duration_seconds": null,
      "name": "Mi imagen",
      "alt": "Texto alternativo",
      "created_at": "2025-09-19T22:00:00.000000Z",
      "updated_at": "2025-09-19T22:00:00.000000Z"
    }
  ],
  "per_page": 20,
  "total": 1
}
```

### GET /api/media/{id}
Obtiene un activo multimedia específico.

#### Parámetros de Ruta
- `id` (integer, requerido): ID del activo multimedia

#### Respuesta Exitosa (200)
```json
{
  "id": 1,
  "owner_id": 1,
  "type": "image",
  "provider": "local",
  "url": "http://localhost:8000/storage/uploads/1/2025/09/file.jpg",
  "storage_path": "uploads/1/2025/09/file.jpg",
  "mime_type": "image/jpeg",
  "size_bytes": 12345,
  "duration_seconds": null,
  "name": "Mi imagen",
  "alt": "Texto alternativo",
  "created_at": "2025-09-19T22:00:00.000000Z",
  "updated_at": "2025-09-19T22:00:00.000000Z"
}
```

#### Respuesta de Error (404)
```json
{
  "message": "Archivo no encontrado"
}
```

### POST /api/media
Crea un nuevo activo multimedia.

#### Caso 1: Archivo Local
**Parámetros del Body (form-data):**
- `file` (file, requerido): Archivo a subir (máx. 50MB)
- `type` (string, opcional): Tipo de archivo (image, video, audio, document) - se infiere si no se proporciona
- `provider` (string, opcional): Proveedor (default: local)
- `name` (string, opcional): Nombre del archivo
- `alt` (string, opcional): Texto alternativo
- `duration_seconds` (integer, opcional): Duración en segundos (para video/audio)

#### Caso 2: URL Externa
**Parámetros del Body (JSON):**
- `url` (string, requerido): URL del activo externo
- `type` (string, requerido): Tipo de archivo (image, video, audio, document)
- `provider` (string, requerido): Proveedor (vimeo, youtube, external, etc.)
- `name` (string, opcional): Nombre del archivo
- `alt` (string, opcional): Texto alternativo
- `duration_seconds` (integer, opcional): Duración en segundos

#### Tipos MIME Permitidos
- **image**: image/jpeg, image/png, image/gif, image/webp
- **video**: video/mp4, video/avi, video/quicktime, video/mov
- **audio**: audio/mpeg, audio/wav, audio/mp3
- **document**: application/pdf, text/plain, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel

#### Respuesta Exitosa (201)
```json
{
  "id": 1,
  "owner_id": 1,
  "type": "image",
  "provider": "local",
  "url": "http://localhost:8000/storage/uploads/1/2025/09/file.jpg",
  "storage_path": "uploads/1/2025/09/file.jpg",
  "mime_type": "image/jpeg",
  "size_bytes": 12345,
  "duration_seconds": null,
  "name": "Mi imagen",
  "alt": "Texto alternativo",
  "created_at": "2025-09-19T22:00:00.000000Z",
  "updated_at": "2025-09-19T22:00:00.000000Z"
}
```

#### Respuesta de Error (422)
```json
{
  "message": "Tipo de archivo no permitido"
}
```

### PATCH /api/media/{id}
Actualiza un activo multimedia existente.

#### Parámetros de Ruta
- `id` (integer, requerido): ID del activo multimedia

#### Parámetros del Body
Todos los parámetros son opcionales y pueden combinarse:
- `file` (file, opcional): Nuevo archivo para reemplazar el existente
- `url` (string, opcional): Nueva URL externa
- `type` (string, opcional): Nuevo tipo
- `provider` (string, opcional): Nuevo proveedor
- `name` (string, opcional): Nuevo nombre
- `alt` (string, opcional): Nuevo texto alternativo
- `duration_seconds` (integer, opcional): Nueva duración

#### Comportamiento
- Si se envía un nuevo archivo, se reemplaza el archivo anterior (si existía)
- Si se envía una URL, se convierte a externo y se elimina el archivo local (si existía)
- Los archivos se almacenan en `storage/app/public/uploads/{user_id}/{Y}/{m}/`

#### Respuesta Exitosa (200)
```json
{
  "id": 1,
  "owner_id": 1,
  "type": "video",
  "provider": "youtube",
  "url": "https://youtube.com/watch?v=...",
  "storage_path": null,
  "mime_type": null,
  "size_bytes": null,
  "duration_seconds": 3600,
  "name": "Mi video",
  "alt": "Texto alternativo",
  "created_at": "2025-09-19T22:00:00.000000Z",
  "updated_at": "2025-09-19T22:05:00.000000Z"
}
```

### DELETE /api/media/{id}
Elimina un activo multimedia.

#### Parámetros de Ruta
- `id` (integer, requerido): ID del activo multimedia

#### Comportamiento
- Elimina el registro de la base de datos
- Si es un archivo local, también elimina el archivo físico del almacenamiento

#### Respuesta Exitosa (200)
```json
{
  "message": "Archivo eliminado"
}
```

## Consideraciones Generales
- Todos los endpoints requieren autenticación (`Authorization: Bearer {token}`)
- Los activos multimedia están asociados al usuario autenticado (`owner_id`)
- Los archivos locales se almacenan en el disco `public` de Laravel
- La paginación por defecto es de 20 elementos por página
- Los tipos de archivo se determinan automáticamente por MIME type cuando no se especifican
- Se permite un máximo de 10 elementos por página en la paginación

## Ejemplos de Uso

### Subir un archivo local
```bash
curl -X POST http://localhost:8000/api/media \
  -H "Authorization: Bearer {token}" \
  -F "file=@imagen.jpg" \
  -F "name=Mi imagen" \
  -F "alt=Descripción de la imagen"
```

### Crear un activo externo
```bash
curl -X POST http://localhost:8000/api/media \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://youtube.com/watch?v=abc123",
    "type": "video",
    "provider": "youtube",
    "name": "Mi video de YouTube",
    "duration_seconds": 3600
  }'
```

### Listar con filtros
```bash
curl -X GET "http://localhost:8000/api/media?type=image&per_page=5&q=logo" \
  -H "Authorization: Bearer {token}"