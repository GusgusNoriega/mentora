# Documentación API - RoleController

## Descripción General
El `RoleController` gestiona los roles del sistema RBAC (Role-Based Access Control) utilizando el paquete Spatie Permission de Laravel. Permite operaciones CRUD completas sobre roles.

## Endpoints

### GET /api/rbac/roles
Lista todos los roles con filtros, búsqueda y paginación.

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
      "name": "admin",
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

### POST /api/rbac/roles
Crea un nuevo rol.

#### Parámetros del Body
- `name` (string, requerido): Nombre del rol (máx: 255 caracteres)
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
    "name": "admin",
    "guard_name": "web",
    "created_at": "2025-09-19T22:00:00.000000Z",
    "updated_at": "2025-09-19T22:00:00.000000Z"
  },
  "meta": {
    "message": "Rol creado"
  }
}
```

#### Respuesta de Error (409)
```json
{
  "success": false,
  "data": null,
  "meta": {
    "message": "El rol ya existe para el guard especificado",
    "errors": []
  }
}
```

### GET /api/rbac/roles/{id}
Obtiene un rol específico por ID.

#### Parámetros de Ruta
- `id` (integer, requerido): ID del rol

#### Parámetros de Query
- `guard` (string, opcional): Guard name (default: web)

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "admin",
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
    "message": "Rol no encontrado",
    "errors": []
  }
}
```

### PUT /api/rbac/roles/{id}
Actualiza un rol existente.

#### Parámetros de Ruta
- `id` (integer, requerido): ID del rol

#### Parámetros del Body
- `name` (string, opcional): Nuevo nombre del rol
- `guard_name` (string, opcional): Nuevo guard name

#### Validaciones
- `name`: Opcional, string, máximo 255 caracteres, único por guard (excluyendo el rol actual)
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
    "name": "super-admin",
    "guard_name": "web",
    "created_at": "2025-09-19T22:00:00.000000Z",
    "updated_at": "2025-09-19T22:05:00.000000Z"
  },
  "meta": {
    "message": "Rol actualizado"
  }
}
```

#### Respuesta de Error (409)
```json
{
  "success": false,
  "data": null,
  "meta": {
    "message": "Ya existe un rol con ese nombre en el guard especificado",
    "errors": []
  }
}
```

### DELETE /api/rbac/roles/{id}
Elimina un rol.

#### Parámetros de Ruta
- `id` (integer, requerido): ID del rol

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": null,
  "meta": {
    "message": "Rol eliminado"
  }
}
```

#### Respuesta de Error (404)
```json
{
  "success": false,
  "data": null,
  "meta": {
    "message": "Rol no encontrado",
    "errors": []
  }
}
```

## Consideraciones Generales
- Todos los endpoints requieren autenticación de administrador
- Utiliza el paquete Spatie Permission para gestión de roles
- Implementa mirroring entre guards usando el servicio `RbacMirror`
- Solo soporta el guard 'web' actualmente
- Los roles son únicos por nombre dentro de cada guard
- Incluye validación completa y mensajes de error detallados

## Ejemplos de Uso

### Crear un rol
```bash
curl -X POST http://localhost:8000/api/rbac/roles \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "instructor",
    "guard_name": "web"
  }'
```

### Listar roles con búsqueda
```bash
curl -X GET "http://localhost:8000/api/rbac/roles?q=admin&per_page=10" \
  -H "Authorization: Bearer {token}"
```

### Actualizar un rol
```bash
curl -X PUT http://localhost:8000/api/rbac/roles/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "administrator"
  }'
```

### Eliminar un rol
```bash
curl -X DELETE http://localhost:8000/api/rbac/roles/1 \
  -H "Authorization: Bearer {token}"