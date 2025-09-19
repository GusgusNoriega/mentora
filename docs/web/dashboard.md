# Dashboard (dashboard.blade.php)

## Descripción
Esta vista representa el panel principal del sistema LMS (Learning Management System) llamado Mentora. Es la página de inicio que se muestra después de iniciar sesión, proporcionando una visión general del estado del sistema y del progreso del usuario.

## Funcionalidad Principal
- **Métricas rápidas**: Muestra estadísticas clave como cursos activos, usuarios nuevos, tasa de finalización e ingresos.
- **Actividad reciente**: Lista de eventos recientes como completación de cursos, nuevos cursos publicados y suscripciones.
- **Progreso personal**: Barra de progreso de los cursos en los que el usuario está inscrito.

## Estructura
- Utiliza el layout `layouts.dashboard` para mantener consistencia con otras páginas del dashboard.
- Incluye secciones organizadas con clases CSS de Tailwind para diseño responsivo.
- Implementa componentes reutilizables con atributos `data-component` para facilitar el mantenimiento.

## Elementos Interactivos
- Botón "Ver todo" en la sección de actividad reciente (actualmente sin funcionalidad).
- Barras de progreso visuales para mostrar el avance en cursos.

## Tecnologías Utilizadas
- Blade templating engine de Laravel.
- Tailwind CSS para estilos.
- Diseño responsivo con clases de Tailwind.
- Soporte para modo oscuro con clases `dark:`.

## Ruta Asociada
Generalmente accesible en `/dashboard` después de autenticación.