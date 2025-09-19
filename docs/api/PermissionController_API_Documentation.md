# Documentación API - PermissionController

## Descripción General
El `PermissionController` gestiona los permisos del sistema RBAC (Role-Based Access Control) utilizando el paquete Spatie Permission de Laravel. Permite operaciones CRUD completas sobre permisos.

## Endpoints

### GET /api/rbac/permissions
Lista todos los permisos con filtros, búsqueda y paginación.

#### Parámetros de Query
- `guard` (string, opcional): Guard name (default: web, solo permite 'web')
- `page` (integer, opcional): Página de paginación (mín: 1)
- `per_page` (integer, opcional): Elementos por página (1-100, default: 15)
- `sort` (string, opcional): Campo para ordenar (solo 'name')
- `order` (string, opcional): Orden (asc, desc, default: asc)
- `q` (string, opcional): Búsqueda por nombre (máx: 255 caracteres)

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "create-users",
      "guard_name": "web",
      "created_at": "2025-09-19T22:00:00.000000Z",
      "updated_at": "2025-09-19T22:00:00.000000Z"
    }
  ],
  "meta": {
    "message": "",
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 1,
      "last_page": 1
    }
  }
}
```

#### Respuesta de Error (422)
```json
{
  "success": false,
  "data": null,
  "meta": {
    "message": "Validación fallida",
    "errors": {
      "per_page": ["El campo per_page debe ser al menos 1."]
    }
  }
}
```

### POST /api/rbac/permissions
Crea un nuevo permiso.

#### Parámetros del Body
- `name` (string, requerido): Nombre del permiso (máx: 255 caracteres)
- `guard_name` (string, opcional): Guard name (default: web, solo permite 'web')

#### Validaciones
- `name`: Requerido, string, máximo 255 caracteres, único por guard
- `guard_name`: Opcional, debe ser 'web'

#### Respuesta Exitosa (201)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "create-users",
    "guard_name": "web",
    "created_at": "2025-09-19T22:00:00.000000Z",
    "updated_at": "2025-09-19T22:00:00.000000Z"
  },
  "meta": {
    "message": "Permiso creado"
  }
}
```

#### Respuesta de Error (409)
```json
{
  "success": false,
  "data": null,
  "meta": {
    "message": "El permiso ya existe para el guard especificado",
    "errors": []
  }
}
```

### GET /api/rbac/permissions/{id}
Obtiene un permiso específico por ID.

#### Parámetros de Ruta
- `id` (integer, requerido): ID del permiso

#### Parámetros de Query
- `guard` (string, opcional): Guard name (default: web)

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "create-users",
    "guard_name": "web",
    "created_at": "2025-09-19T22:00:00.000000Z",
    "updated_at": "2025-09-19T22:00:00.000000Z"
  },
  "meta": {
    "message": ""
  }
}
```

#### Respuesta de Error (404)
```json
{
  "success": false,
  "data": null,
  "meta": {
    "message": "Permiso no encontrado",
    "errors": []
  }
}
```

### PUT /api/rbac/permissions/{id}
Actualiza un permiso existente.

#### Parámetros de Ruta
- `id` (integer, requerido): ID del permiso

#### Parámetros del Body
- `name` (string, opcional): Nuevo nombre del permiso
- `guard_name` (string, opcional): Nuevo guard name

#### Validaciones
- `name`: Opcional, string, máximo 255 caracteres, único por guard (excluyendo el permiso actual)
- `guard_name`: Opcional, debe ser 'web'

#### Comportamiento
- Si no se envía `name`, mantiene el nombre actual
- Si no se envía `guard_name`, mantiene el guard actual
- Verifica unicidad del nombre dentro del guard especificado

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "edit-users",
    "guard_name": "web",
    "created_at": "2025-09-19T22:00:00.000000Z",
    "updated_at": "2025-09-19T22:05:00.000000Z"
  },
  "meta": {
    "message": "Permiso actualizado"
  }
}
```

#### Respuesta de Error (409)
```json
{
  "success": false,
  "data": null,
  "meta": {
    "message": "Ya existe un permiso con ese nombre en el guard especificado",
    "errors": []
  }
}
```

### DELETE /api/rbac/permissions/{id}
Elimina un permiso.

#### Parámetros de Ruta
- `id` (integer, requerido): ID del permiso

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": null,
  "meta": {
    "message": "Permiso eliminado"
  }
}
```

#### Respuesta de Error (404)
```json
{
  "success": false,
  "data": null,
  "meta": {
    "message": "Permiso no encontrado",
    "errors": []
  }
}
```

## Consideraciones Generales
- Todos los endpoints requieren autenticación de administrador
- Utiliza el paquete Spatie Permission para gestión de permisos
- Implementa mirroring entre guards usando el servicio `RbacMirror`
- Solo soporta el guard 'web' actualmente
- Los permisos son únicos por nombre dentro de cada guard
- Incluye validación completa y mensajes de error detallados

## Ejemplos de Uso

### Crear un permiso
```bash
curl -X POST http://localhost:8000/api/rbac/permissions \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "manage-courses",
    "guard_name": "web"
  }'
```

### Listar permisos con búsqueda
```bash
curl -X GET "http://localhost:8000/api/rbac/permissions?q=user&per_page=10" \
  -H "Authorization: Bearer {token}"
```

### Actualizar un permiso
```bash
curl -X PUT http://localhost:8000/api/rbac/permissions/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "manage-users"
  }'
```

### Eliminar un permiso
```bash
curl -X DELETE http://localhost:8000/api/rbac/permissions/1 \
  -H "Authorization: Bearer {token}"