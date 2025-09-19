# Layout del Dashboard (layouts/dashboard.blade.php)

## Descripción
Este archivo define el layout principal del dashboard de la aplicación Mentora, proporcionando la estructura base para todas las páginas del panel administrativo.

## Funcionalidad Principal
- **Estructura base**: Layout HTML completo con head, body y componentes principales.
- **Sistema de temas**: Soporte completo para modo claro y oscuro.
- **Navegación integrada**: Sidebar y topbar incluidas automáticamente.
- **Gestión de assets**: Carga de CSS, JS y meta tags.

## Estructura del Layout
- **Head**: Configuración de Tailwind, variables CSS, meta tags.
- **Sidebar**: Barra lateral de navegación (sticky en desktop).
- **Topbar**: Barra superior con búsqueda y acciones.
- **Contenido principal**: Área donde se renderiza el contenido de cada página.
- **Footer**: Pie de página simple.

## Características Técnicas
- **Tailwind CDN**: Carga desde CDN con configuración personalizada.
- **Variables CSS**: Sistema de colores brand y surface personalizado.
- **JavaScript vanilla**: Funcionalidad de temas y navegación móvil.
- **Responsive design**: Adaptable a todos los tamaños de pantalla.
- **Modo oscuro**: Toggle automático con persistencia en localStorage.

## Sistema de Colores
- **Brand colors**: Paleta azul personalizada (50-900).
- **Surface colors**: Fondos claros/oscuros según tema.
- **Semantic colors**: Colores para estados (muted, accent, ring).

## Elementos Interactivos
- **Toggle de sidebar**: Abre/cierra navegación en móvil.
- **Cambio de tema**: Botón para alternar modo claro/oscuro.
- **Overlay**: Capa oscura para modal en móvil.

## Tecnologías Utilizadas
- HTML5 semántico.
- Tailwind CSS v4.0.7 con configuración avanzada.
- JavaScript ES6 para interactividad.
- Blade templating de Laravel.
- Meta tags para CSRF y API tokens.

## Secciones Extensibles
- `@yield('title')`: Título de página personalizado.
- `@yield('content')`: Contenido principal de cada página.
- `@stack('head')`: Assets adicionales en head.
- `@stack('scripts')`: Scripts adicionales al final.

## Seguridad
- **CSRF protection**: Token incluido automáticamente.
- **API tokens**: Meta tag para autenticación API.
- **Headers seguros**: Configuración automática de headers.

## Rutas Asociadas
Utilizado por todas las rutas del dashboard que requieren autenticación.