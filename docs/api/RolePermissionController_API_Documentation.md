# Documentación API - RolePermissionController

## Descripción General
El `RolePermissionController` gestiona la asignación de permisos a roles en el sistema RBAC. Permite listar, asignar, sincronizar y remover permisos de roles específicos.

## Endpoints

### GET /api/rbac/roles/{roleId}/permissions
Lista todos los permisos asignados a un rol específico.

#### Parámetros de Ruta
- `roleId` (integer, requerido): ID del rol

#### Parámetros de Query
- `guard` (string, opcional): Guard name (default: web, solo permite 'web')

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
    },
    {
      "id": 2,
      "name": "edit-users",
      "guard_name": "web",
      "created_at": "2025-09-19T22:00:00.000000Z",
      "updated_at": "2025-09-19T22:00:00.000000Z"
    }
  ],
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

### POST /api/rbac/roles/{roleId}/permissions/attach
Asigna permisos adicionales a un rol sin remover los existentes.

#### Parámetros de Ruta
- `roleId` (integer, requerido): ID del rol

#### Parámetros del Body
- `permissions` (array, requerido): Lista de permisos a asignar
- `mode` (string, opcional): Modo de identificación ('by_id' o 'by_name', default: 'by_id')
- `guard_name` (string, opcional): Guard name (default: web, solo permite 'web')

#### Modos de Identificación
- **by_id**: Los valores en `permissions` son IDs de permisos (enteros)
- **by_name**: Los valores en `permissions` son nombres de permisos (strings)

#### Validaciones
- `permissions`: Array requerido con al menos 1 elemento
- `mode`: Debe ser 'by_id' o 'by_name'
- `guard_name`: Debe ser 'web'

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
    "message": "Permisos asignados"
  }
}
```

#### Respuesta de Error (422)
```json
{
  "success": false,
  "data": null,
  "meta": {
    "message": "Algunos permisos no existen para el guard especificado",
    "errors": {
      "missing": [999, 888]
    }
  }
}
```

### POST /api/rbac/roles/{roleId}/permissions/sync
Sincroniza los permisos de un rol (remueve permisos no especificados y asigna los nuevos).

#### Parámetros de Ruta
- `roleId` (integer, requerido): ID del rol

#### Parámetros del Body
- `permissions` (array, requerido): Lista completa de permisos que debe tener el rol
- `mode` (string, opcional): Modo de identificación ('by_id' o 'by_name', default: 'by_id')
- `guard_name` (string, opcional): Guard name (default: web, solo permite 'web')

#### Comportamiento
- Remueve todos los permisos actuales del rol
- Asigna únicamente los permisos especificados
- Es una operación "reemplazar todo"

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
    "message": "Permisos sincronizados"
  }
}
```

### POST /api/rbac/roles/{roleId}/permissions/detach
Remueve permisos específicos de un rol.

#### Parámetros de Ruta
- `roleId` (integer, requerido): ID del rol

#### Parámetros del Body
- `permissions` (array, requerido): Lista de permisos a remover
- `mode` (string, opcional): Modo de identificación ('by_id' o 'by_name', default: 'by_id')
- `guard_name` (string, opcional): Guard name (default: web, solo permite 'web')

#### Comportamiento
- Operación idempotente: intentar remover un permiso no asignado no genera error
- Solo remueve los permisos especificados, mantiene los demás

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": [
    {
      "id": 2,
      "name": "edit-users",
      "guard_name": "web",
      "created_at": "2025-09-19T22:00:00.000000Z",
      "updated_at": "2025-09-19T22:00:00.000000Z"
    }
  ],
  "meta": {
    "message": "Permisos removidos"
  }
}
```

## Consideraciones Generales
- Todos los endpoints requieren autenticación de administrador
- Utiliza el paquete Spatie Permission para gestión de relaciones rol-permiso
- Implementa mirroring entre guards usando el servicio `RbacMirror`
- Solo soporta el guard 'web' actualmente
- Las operaciones son atómicas y mantienen la integridad referencial
- Incluye validación completa y mensajes de error detallados

## Ejemplos de Uso

### Asignar permisos por ID
```bash
curl -X POST http://localhost:8000/api/rbac/roles/1/permissions/attach \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "permissions": [1, 2, 3],
    "mode": "by_id",
    "guard_name": "web"
  }'
```

### Asignar permisos por nombre
```bash
curl -X POST http://localhost:8000/api/rbac/roles/1/permissions/attach \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "permissions": ["create-users", "edit-users"],
    "mode": "by_name",
    "guard_name": "web"
  }'
```

### Sincronizar permisos (reemplazar todos)
```bash
curl -X POST http://localhost:8000/api/rbac/roles/1/permissions/sync \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "permissions": ["view-users", "create-users"],
    "mode": "by_name"
  }'
```

### Remover permisos específicos
```bash
curl -X POST http://localhost:8000/api/rbac/roles/1/permissions/detach \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "permissions": ["delete-users"],
    "mode": "by_name"
  }'
```

### Listar permisos de un rol
```bash
curl -X GET http://localhost:8000/api/rbac/roles/1/permissions \
  -H "Authorization: Bearer {token}"