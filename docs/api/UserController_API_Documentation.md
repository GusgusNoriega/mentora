# Documentación API - UserController

## Descripción General
El `UserController` gestiona las operaciones CRUD de usuarios, perfiles y datos relacionados como cursos inscritos, progreso y certificados. Incluye control de permisos basado en roles.

## Endpoints

### GET /api/users
Lista todos los usuarios con filtros y paginación (solo administradores).

#### Parámetros de Query
- `search` (string, opcional): Búsqueda por nombre o email
- `role` (string, opcional): Filtrar por rol específico
- `page` (integer, opcional): Página de paginación

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Usuario Admin",
        "email": "admin@example.com",
        "roles": [
          {
            "id": 1,
            "name": "admin",
            "guard_name": "web"
          }
        ],
        "subscription_plans": []
      }
    ],
    "per_page": 15,
    "total": 1
  },
  "meta": {
    "message": "Usuarios obtenidos exitosamente"
  }
}
```

#### Respuesta de Error (403)
```json
{
  "success": false,
  "data": null,
  "meta": {
    "message": "No tienes permisos para acceder a esta información"
  }
}
```

### GET /api/users/profile
Obtiene el perfil completo del usuario autenticado.

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Usuario",
    "email": "usuario@example.com",
    "roles": [...],
    "enrolled_courses": [...],
    "courses_progress": [...],
    "subscriptions": [...],
    "certificates": [...],
    "course_reviews": [...],
    "wishlist_courses": [...]
  },
  "meta": {
    "message": "Perfil obtenido exitosamente"
  }
}
```

### GET /api/users/{id}
Obtiene información detallada de un usuario específico.

#### Parámetros de Ruta
- `id` (integer, requerido): ID del usuario

#### Permisos
- Usuario propio o administrador

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Usuario",
    "email": "usuario@example.com",
    "roles": [...],
    "enrolled_courses": [...],
    "courses_progress": [...],
    "subscriptions": [...],
    "certificates": [...],
    "course_reviews": [...]
  },
  "meta": {
    "message": "Usuario obtenido exitosamente"
  }
}
```

### GET /api/users/{id}/enrolled-courses
Obtiene los cursos inscritos de un usuario.

#### Parámetros de Ruta
- `id` (integer, opcional): ID del usuario (si no se proporciona, usa el usuario autenticado)

#### Permisos
- Usuario propio o administrador

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Curso de Laravel",
      "category": {...},
      "instructor": {...},
      "pivot": {
        "enrolled_at": "2025-09-19T22:00:00.000000Z",
        "expires_at": null
      }
    }
  ],
  "meta": {
    "message": "Cursos inscritos obtenidos exitosamente"
  }
}
```

### GET /api/users/{id}/courses-progress
Obtiene el progreso de cursos de un usuario.

#### Parámetros de Ruta
- `id` (integer, opcional): ID del usuario (si no se proporciona, usa el usuario autenticado)

#### Permisos
- Usuario propio o administrador

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Curso de Laravel",
      "pivot": {
        "progress_pct": 75.5,
        "completed_at": null
      }
    }
  ],
  "meta": {
    "message": "Progreso de cursos obtenido exitosamente"
  }
}
```

### GET /api/users/{id}/certificates
Obtiene los certificados de un usuario.

#### Parámetros de Ruta
- `id` (integer, opcional): ID del usuario (si no se proporciona, usa el usuario autenticado)

#### Permisos
- Usuario propio o administrador

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "course": {
        "id": 1,
        "title": "Curso de Laravel"
      },
      "template": {...},
      "issued_at": "2025-09-19T22:00:00.000000Z",
      "certificate_number": "CERT-001"
    }
  ],
  "meta": {
    "message": "Certificados obtenidos exitosamente"
  }
}
```

### POST /api/admin/users
Crea un nuevo usuario (solo administradores).

#### Parámetros del Body
- `name` (string, requerido): Nombre del usuario
- `email` (string, requerido): Email único del usuario
- `password` (string, requerido): Contraseña (mínimo 8 caracteres)
- `password_confirmation` (string, requerido): Confirmación de contraseña
- `role` (string, opcional): Rol a asignar (debe existir en la tabla roles)

#### Validaciones
- `name`: Requerido, string, máximo 255 caracteres
- `email`: Requerido, email válido, único en users
- `password`: Requerido, mínimo 8 caracteres, debe coincidir con confirmación
- `role`: Opcional, debe existir en roles con guard_name 'web'

#### Respuesta Exitosa (201)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Nuevo Usuario",
    "email": "nuevo@example.com",
    "roles": [
      {
        "id": 2,
        "name": "student",
        "guard_name": "web"
      }
    ]
  },
  "meta": {
    "message": "Usuario creado exitosamente"
  }
}
```

### PUT /api/users/{id}
Actualiza un usuario existente.

#### Parámetros de Ruta
- `id` (integer, requerido): ID del usuario

#### Parámetros del Body
- `name` (string, opcional): Nuevo nombre
- `email` (string, opcional): Nuevo email
- `password` (string, opcional): Nueva contraseña
- `password_confirmation` (string, opcional): Confirmación de nueva contraseña
- `role` (string, opcional): Nuevo rol (solo administradores)

#### Permisos
- Usuario propio o administrador
- Solo administradores pueden cambiar roles
- Un admin no puede quitarse a sí mismo el rol admin

#### Validaciones
- `name`: Opcional, string, máximo 255 caracteres
- `email`: Opcional, email válido, único (ignorando el usuario actual)
- `password`: Opcional, mínimo 8 caracteres, debe coincidir con confirmación
- `role`: Opcional, debe existir en roles (solo admin)

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Usuario Actualizado",
    "email": "actualizado@example.com",
    "roles": [...]
  },
  "meta": {
    "message": "Usuario actualizado exitosamente"
  }
}
```

### DELETE /api/admin/users/{id}
Elimina un usuario (solo administradores).

#### Parámetros de Ruta
- `id` (integer, requerido): ID del usuario

#### Permisos
- Solo administradores
- No se puede eliminar el propio usuario

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": null,
  "meta": {
    "message": "Usuario eliminado exitosamente"
  }
}
```

## Consideraciones Generales
- Todos los endpoints requieren autenticación
- Los endpoints de administración requieren rol 'admin'
- Los usuarios pueden ver/editar su propio perfil
- Los administradores pueden gestionar todos los usuarios
- Implementa mirroring de roles entre guards usando `RbacMirror`
- Incluye validación completa y mensajes de error detallados
- Maneja relaciones con cursos, progreso, certificados y suscripciones

## Ejemplos de Uso

### Crear un usuario (admin)
```bash
curl -X POST http://localhost:8000/api/admin/users \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "student"
  }'
```

### Actualizar perfil propio
```bash
curl -X PUT http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Juan Pérez Actualizado",
    "email": "juan.nuevo@example.com"
  }'
```

### Obtener cursos inscritos
```bash
curl -X GET http://localhost:8000/api/users/1/enrolled-courses \
  -H "Authorization: Bearer {token}"
```

### Listar usuarios (admin)
```bash
curl -X GET "http://localhost:8000/api/users?search=juan&role=student" \
  -H "Authorization: Bearer {token}"
```

### Eliminar usuario (admin)
```bash
curl -X DELETE http://localhost:8000/api/admin/users/2 \
  -H "Authorization: Bearer {token}"