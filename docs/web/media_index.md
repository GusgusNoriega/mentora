# Administrador de Archivos (media/index.blade.php)

## Descripción
Esta vista proporciona una interfaz completa para gestionar archivos multimedia en el sistema Mentora. Incluye funcionalidades de subida, edición, eliminación y organización de medios.

## Funcionalidad Principal
- **Biblioteca de medios**: Visualización en grid de todos los archivos.
- **Subida de archivos**: Modal para subir archivos desde el dispositivo.
- **Agregar URLs**: Modal para agregar videos externos o enlaces.
- **Edición de metadatos**: Panel lateral para editar nombre, alt y URL.
- **Búsqueda y filtros**: Búsqueda por nombre y filtrado por tipo.
- **Paginación**: Navegación por páginas de resultados.
- **Selección múltiple**: Checkbox para acciones masivas.

## Estructura
- **Header**: Título y botones de acción principales.
- **Toolbar**: Controles de búsqueda, filtros y paginación.
- **Grid principal**: Visualización de archivos en formato de tarjetas.
- **Panel editor**: Sidebar derecho para edición de archivos.
- **Modales**: Para subida de archivos y URLs.

## Características Técnicas
- **JavaScript vanilla**: Lógica completa sin frameworks adicionales.
- **API integration**: Comunicación con endpoints REST.
- **Responsive design**: Adaptable a diferentes tamaños de pantalla.
- **Modo oscuro**: Soporte completo para temas.
- **Toast notifications**: Mensajes de feedback al usuario.

## Elementos Interactivos
- **Botones de acción**: Subir archivo, agregar URL, actualizar.
- **Campos de búsqueda**: Input con debounce para búsqueda en tiempo real.
- **Filtros**: Select para filtrar por tipo de medio.
- **Paginación**: Botones anterior/siguiente.
- **Selección múltiple**: Checkbox con barra de acciones masivas.
- **Editor**: Formulario para editar metadatos.

## Tecnologías Utilizadas
- Blade templating de Laravel.
- Tailwind CSS para estilos.
- JavaScript ES6 con fetch API.
- API REST para operaciones CRUD.
- FormData para subida de archivos.

## API Endpoints Utilizados
- `GET /api/media`: Listar medios con paginación y filtros.
- `POST /api/media`: Subir archivo o agregar URL.
- `PATCH /api/media/{id}`: Actualizar metadatos.
- `DELETE /api/media/{id}`: Eliminar archivo.

## Funcionalidades Avanzadas
- **Skeleton loading**: Estados de carga con placeholders.
- **Formato de bytes**: Conversión automática de tamaños de archivo.
- **Validación**: Verificación de tipos de archivo y URLs.
- **Confirmaciones**: Diálogos de confirmación para acciones destructivas.

## Seguridad
- **CSRF tokens**: Protección contra ataques CSRF.
- **Bearer tokens**: Autenticación API con JWT.
- **Validación de archivos**: Control de tipos y tamaños permitidos.

## Tipos de Medios Soportados
- **Imágenes**: JPG, PNG, GIF, WebP.
- **Videos**: MP4, WebM, URLs embebidas.
- **Audios**: MP3, WAV, URLs externas.
- **Documentos**: PDF, DOC, XLS, etc.

## Rutas Asociadas
- `/media` - Página principal del administrador.
- `/media/manager` - Versión avanzada con selector múltiple.