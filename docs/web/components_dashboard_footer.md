# Pie de Página del Dashboard (components/dashboard/footer.blade.php)

## Descripción
Este componente representa el pie de página utilizado en las páginas del dashboard de la aplicación Mentora. Es un componente simple y minimalista.

## Funcionalidad Principal
- **Información de copyright**: Muestra un mensaje simple de "Hecho con ❤️".
- **Separación visual**: Proporciona cierre visual a las páginas del dashboard.

## Estructura
- Contenedor con padding responsivo (`px-4 md:px-6 py-6`).
- Texto centrado con clases de Tailwind.
- Color de texto muted para discreción.

## Características Técnicas
- **Componente reutilizable**: Incluido en el layout del dashboard.
- **Responsive**: Ajusta padding en diferentes tamaños de pantalla.
- **Atributo data-component**: Facilita identificación y testing.

## Tecnologías Utilizadas
- Blade templating de Laravel.
- Tailwind CSS para estilos.
- Diseño minimalista y limpio.

## Ubicación en Layout
Se incluye automáticamente en todas las páginas que usan el layout `layouts.dashboard`.

## Personalización
El contenido es estático pero puede ser fácilmente modificado para incluir:
- Enlaces adicionales.
- Información de versión.
- Enlaces a redes sociales.
- Información de contacto.