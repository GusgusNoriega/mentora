# Selector de Archivos Múltiple (media/manager.blade.php)

## Descripción
Esta vista implementa un sistema avanzado de selección de archivos multimedia con capacidad para selección única o múltiple. Utiliza una clase JavaScript `MediaFinder` para gestionar la funcionalidad.

## Funcionalidad Principal
- **Selector único**: Campo para seleccionar un archivo individual.
- **Selector múltiple**: Campo para seleccionar múltiples archivos con límite configurable.
- **Biblioteca modal**: Modal completo con búsqueda, filtros y paginación.
- **Preview de selección**: Miniaturas de archivos seleccionados.
- **Editor integrado**: Panel lateral para editar metadatos.

## Modos de Selección
- **Single**: Selección de un solo archivo.
- **Multiple**: Selección múltiple con límite máximo configurable.

## Características Técnicas
- **Clase MediaFinder**: Arquitectura orientada a objetos para gestión de estado.
- **API REST**: Comunicación completa con backend.
- **Responsive**: Adaptable a móviles y desktop.
- **Modo oscuro**: Soporte completo.
- **AbortController**: Cancelación de peticiones para mejor UX.

## Elementos Interactivos
- **Campos de entrada**: Inputs con data attributes para configuración.
- **Botones de apertura**: Triggers para abrir el modal de selección.
- **Modal de biblioteca**: Interfaz completa de gestión de medios.
- **Panel de seleccionados**: Vista previa de archivos elegidos.
- **Editor de metadatos**: Formulario para editar propiedades.

## Tecnologías Utilizadas
- Blade templating de Laravel.
- Tailwind CSS para estilos.
- JavaScript ES6 con clases y async/await.
- Fetch API para peticiones HTTP.
- FormData para subida de archivos.

## Configuración por Data Attributes
- `data-filepicker`: "single" o "multiple".
- `data-fp-max`: Límite máximo de selección.
- `data-fp-preview`: Selector CSS para contenedor de preview.
- `data-fp-per-page`: Elementos por página.

## Funcionalidades Avanzadas
- **Debounced search**: Búsqueda con delay para optimización.
- **Skeleton loading**: Estados de carga visuales.
- **Toast notifications**: Feedback al usuario.
- **Confirmaciones**: Diálogos para acciones destructivas.
- **Formato automático**: Conversión de tamaños de archivo.

## API Endpoints
- `GET /api/media`: Listado con filtros y paginación.
- `POST /api/media`: Subida y creación.
- `PATCH /api/media/{id}`: Actualización.
- `DELETE /api/media/{id}`: Eliminación.

## Seguridad
- **Autenticación**: Bearer tokens para API.
- **CSRF protection**: Tokens incluidos en headers.
- **Validación**: Control de tipos y tamaños de archivo.

## Eventos y Callbacks
- **Doble click**: Abre modal desde input.
- **Botón click**: Trigger manual de apertura.
- **Escape key**: Cierra modal activo.
- **Aplicación**: Confirma selección y actualiza inputs.

## Integración
Puede ser integrado en cualquier formulario mediante:
1. Campo input con data attributes.
2. Contenedor para preview.
3. Botón trigger (opcional).
4. Scope container con `data-fp-scope`.

## Rutas Asociadas
- `/media/manager` - Página de demostración.
- Utilizable en cualquier página del dashboard.