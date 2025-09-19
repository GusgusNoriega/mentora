# Documentación API - AuthController

## Descripción General
El `AuthController` maneja la autenticación de usuarios para la API. Utiliza Laravel Passport para la gestión de tokens de acceso.

## Endpoints

### POST /api/login
Endpoint para autenticar usuarios y obtener un token de acceso.

#### Parámetros de Entrada
- `email` (string, requerido): Correo electrónico del usuario.
- `password` (string, requerido): Contraseña del usuario (mínimo 6 caracteres).

#### Validaciones
- `email`: Debe ser un formato de email válido.
- `password`: Mínimo 6 caracteres.

#### Respuesta Exitosa (200)
```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "Usuario",
    "email": "usuario@example.com",
    // ... otros campos del usuario
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "token_type": "Bearer"
}
```

#### Respuesta de Error (401)
```json
{
  "success": false,
  "message": "Credenciales inválidas."
}
```

#### Gestión de Tokens
- El sistema mantiene un máximo de 2 tokens por usuario.
- Si se supera el límite, se elimina el token más antiguo.
- Los tokens se crean con el nombre "API Token".

#### Ejemplo de Uso
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "usuario@example.com",
    "password": "password123"
  }'
```

#### Notas
- Este endpoint es público (no requiere autenticación previa).
- El token devuelto debe incluirse en el header `Authorization: Bearer {token}` para futuras peticiones autenticadas.