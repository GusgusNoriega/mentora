# Barra Superior del Dashboard (components/dashboard/topbar.blade.php)

## Descripción
Este componente representa la barra superior del dashboard de Mentora, proporcionando acceso rápido a funcionalidades comunes y navegación móvil.

## Funcionalidad Principal
- **Navegación móvil**: Botón hamburguesa para abrir/cerrar la barra lateral en dispositivos móviles.
- **Búsqueda global**: Campo de búsqueda para encontrar contenido en el sistema.
- **Acciones rápidas**: Botones para crear nuevo contenido y filtrar resultados.

## Estructura
- **Botón menú móvil**: Visible solo en pantallas pequeñas, controla la visibilidad de la sidebar.
- **Campo de búsqueda**: Input con icono de lupa, placeholder descriptivo.
- **Botones de acción**: "Nuevo" y "Filtrar" para acciones comunes.

## Características Técnicas
- **Posicionamiento sticky**: Se mantiene fija en la parte superior al hacer scroll.
- **Responsive**: Elementos se adaptan según el tamaño de pantalla.
- **Backdrop blur**: Efecto de desenfoque en el fondo para mejor legibilidad.
- **Transiciones suaves**: Animaciones CSS para estados hover y focus.

## Elementos Interactivos
- **Botón hamburguesa**: Abre/cerrar sidebar (requiere JavaScript).
- **Campo de búsqueda**: Input funcional (requiere implementación backend).
- **Botones de acción**: Funcionalidad placeholder (requiere implementación).

## Tecnologías Utilizadas
- Blade templating de Laravel.
- Tailwind CSS para estilos y responsividad.
- SVG inline para iconos.
- Diseño accesible con atributos ARIA.

## Ubicación en Layout
Se incluye automáticamente en el layout `layouts.dashboard` como parte del área principal.

## Personalización
Puede ser extendido con:
- Más botones de acción.
- Menú desplegable de notificaciones.
- Selector de idioma.
- Información de usuario adicional.