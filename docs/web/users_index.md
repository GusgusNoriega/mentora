# Gestión de Usuarios (users/index.blade.php)

## Descripción
Esta vista proporciona una interfaz completa para la administración de usuarios en el sistema Mentora. Incluye funcionalidades diferenciadas según el rol del usuario actual (admin vs. usuario regular).

## Funcionalidad Principal
- **Vista de administrador**: Listado completo de usuarios con CRUD.
- **Vista de usuario**: Edición del propio perfil.
- **Creación/edición de usuarios**: Modal con validación.
- **Búsqueda y filtros**: Por nombre, email y rol.
- **Paginación**: Navegación eficiente por resultados.

## Modos de Visualización
- **Administrador**: Acceso completo a gestión de usuarios.
- **Usuario regular**: Solo edición de perfil personal.

## Estructura
- **Header**: Título y botón de crear usuario (solo admin).
- **Filtros**: Controles de búsqueda (solo admin).
- **Tabla**: Listado de usuarios con acciones (solo admin).
- **Perfil**: Formulario de edición personal (solo usuario).
- **Modal**: Creación/edición de usuarios.

## Características Técnicas
- **JavaScript vanilla**: Lógica completa sin frameworks.
- **API REST**: Comunicación con endpoints de usuarios.
- **Estado condicional**: UI diferente según permisos.
- **Validación**: Control de contraseñas y emails.
- **Toast notifications**: Feedback al usuario.

## Elementos Interactivos
- **Botón crear**: Abrir modal de nuevo usuario.
- **Botones editar**: Modificar usuario existente.
- **Botones eliminar**: Borrar usuario con confirmación.
- **Filtros**: Aplicar búsqueda por criterios.
- **Paginación**: Navegar por páginas.
- **Formulario perfil**: Actualizar datos personales.

## Tecnologías Utilizadas
- Blade templating de Laravel.
- Tailwind CSS para estilos.
- JavaScript ES6 con async/await.
- Fetch API para peticiones HTTP.

## API Endpoints Utilizados
- `GET /api/admin/users`: Listar usuarios (admin).
- `POST /api/admin/users`: Crear usuario (admin).
- `DELETE /api/admin/users/{id}`: Eliminar usuario (admin).
- `GET /api/users/profile`: Obtener perfil propio.
- `GET /api/users/{id}`: Obtener usuario específico.
- `PUT /api/users/{id}`: Actualizar usuario.

## Seguridad
- **Control de acceso**: Verificación de permisos admin.
- **Autenticación**: Bearer tokens para API.
- **CSRF protection**: Tokens incluidos en headers.
- **Validación**: Control de contraseñas y datos.
- **Confirmaciones**: Diálogos para acciones destructivas.

## Funcionalidades de Administrador
- **Listado paginado**: Usuarios con roles y acciones.
- **Creación**: Nuevo usuario con rol opcional.
- **Edición**: Modificar datos y rol.
- **Eliminación**: Borrar usuario permanentemente.
- **Búsqueda**: Filtrar por nombre/email/rol.

## Funcionalidades de Usuario
- **Perfil personal**: Editar nombre, email y contraseña.
- **Validación**: Confirmación de contraseña.
- **Feedback**: Notificaciones de cambios guardados.

## Rutas Asociadas
- `/users` - Página principal de gestión de usuarios.

## Funcionalidades Avanzadas
- **Responsive design**: Adaptable a móviles y desktop.
- **Modo oscuro**: Soporte completo.
- **Estados de carga**: Indicadores visuales.
- **Manejo de errores**: Mensajes específicos por tipo de error.
- **Paginación inteligente**: Ajuste automático de página tras eliminación.