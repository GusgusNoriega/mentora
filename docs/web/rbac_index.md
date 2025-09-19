# Gestión de Roles y Permisos (rbac/index.blade.php)

## Descripción
Esta vista proporciona una interfaz completa para administrar el sistema de control de acceso basado en roles (RBAC) de la aplicación Mentora. Permite gestionar roles, permisos y sus relaciones de manera intuitiva.

## Funcionalidad Principal
- **Gestión de roles**: Crear, editar, eliminar y listar roles del sistema.
- **Gestión de permisos**: Crear, editar, eliminar y listar permisos disponibles.
- **Asignación de permisos**: Vincular/desvincular permisos a roles específicos.
- **Búsqueda y filtrado**: Buscar roles y permisos por nombre.
- **Paginación**: Navegación por páginas de resultados.

## Estructura
- **Sección de roles**: Lista de roles con acciones de edición y eliminación.
- **Sección de permisos**: Lista de permisos con mismas funcionalidades.
- **Sección de asignación**: Interfaz para gestionar permisos de un rol seleccionado.
- **Formularios inline**: Creación/edición sin modales separados.

## Características Técnicas
- **JavaScript vanilla**: Lógica completa sin frameworks adicionales.
- **API REST**: Comunicación con endpoints de RBAC.
- **Estado reactivo**: Actualización automática de la interfaz.
- **Debounced search**: Búsqueda optimizada con delay.
- **Toast notifications**: Feedback al usuario.

## Elementos Interactivos
- **Botones de acción**: Crear, editar, eliminar para roles y permisos.
- **Campos de búsqueda**: Input con debounce para filtrado en tiempo real.
- **Paginación**: Navegación anterior/siguiente.
- **Asignación múltiple**: Checkboxes para permisos con acciones masivas.
- **Selección de rol**: Click para cargar permisos asociados.

## Tecnologías Utilizadas
- Blade templating de Laravel.
- Tailwind CSS para estilos.
- JavaScript ES6 con async/await.
- Fetch API para peticiones HTTP.

## API Endpoints Utilizados
- `GET /api/rbac/roles`: Listar roles con paginación.
- `POST /api/rbac/roles`: Crear rol.
- `PUT /api/rbac/roles/{id}`: Actualizar rol.
- `DELETE /api/rbac/roles/{id}`: Eliminar rol.
- `GET /api/rbac/permissions`: Listar permisos.
- `POST /api/rbac/permissions`: Crear permiso.
- `PUT /api/rbac/permissions/{id}`: Actualizar permiso.
- `DELETE /api/rbac/permissions/{id}`: Eliminar permiso.
- `GET /api/rbac/roles/{id}/permissions`: Obtener permisos de un rol.
- `POST /api/rbac/roles/{id}/permissions/attach`: Asignar permisos.
- `POST /api/rbac/roles/{id}/permissions/detach`: Quitar permisos.
- `POST /api/rbac/roles/{id}/permissions/sync`: Sincronizar permisos.

## Operaciones de Permisos
- **Attach**: Asignar permisos adicionales a un rol.
- **Detach**: Quitar permisos específicos de un rol.
- **Sync**: Reemplazar completamente los permisos de un rol.

## Seguridad
- **Autenticación**: Bearer tokens para API.
- **CSRF protection**: Tokens incluidos en headers.
- **Validación**: Control de permisos en backend.
- **Confirmaciones**: Diálogos para acciones destructivas.

## Rutas Asociadas
- `/rbac` - Página principal de gestión RBAC.

## Funcionalidades Avanzadas
- **Carga paginada**: Optimización para grandes cantidades de datos.
- **Estado persistente**: Mantenimiento de selecciones durante navegación.
- **Feedback visual**: Indicadores de carga y estados de error.
- **Responsive design**: Adaptable a diferentes tamaños de pantalla.