# Barra Lateral del Dashboard (components/dashboard/sidebar.blade.php)

## Descripción
Este componente representa la barra lateral de navegación principal del dashboard de Mentora. Proporciona acceso a todas las secciones principales del sistema administrativo.

## Funcionalidad Principal
- **Navegación principal**: Enlaces a todas las secciones del sistema.
- **Perfil de usuario**: Muestra información básica del usuario actual.
- **Cambio de tema**: Botón para alternar entre modo claro y oscuro.
- **Cierre de sesión**: Formulario para terminar la sesión.

## Secciones de Navegación
- **Dashboard**: Página principal con métricas.
- **Cursos**: Gestión de cursos (enlace placeholder).
- **Usuarios**: Gestión de usuarios del sistema.
- **Roles & Permisos**: Administración de RBAC.
- **Ajustes**: Configuración del sistema (enlace placeholder).
- **Media**: Gestión de archivos multimedia.
- **Media Manager**: Administrador avanzado de medios.

## Características Técnicas
- **Responsive**: Se oculta en móviles con transformaciones CSS.
- **Transiciones suaves**: Animaciones de entrada/salida.
- **Modo oscuro**: Soporte completo con clases `dark:`.
- **Iconos SVG**: Iconografía consistente con Lucide icons.
- **Posicionamiento fijo**: Sticky en desktop, fixed en mobile.

## Elementos Interactivos
- **Enlaces de navegación**: Con estados hover y focus.
- **Botón de tema**: Toggle para modo claro/oscuro (requiere JavaScript).
- **Formulario de logout**: Envío POST seguro con CSRF.

## Tecnologías Utilizadas
- Blade templating de Laravel.
- Tailwind CSS para estilos y animaciones.
- SVG inline para iconos.
- JavaScript para funcionalidad del toggle de tema.

## Estructura
- **Header**: Perfil de usuario con avatar y nombre.
- **Navegación**: Lista de enlaces con iconos.
- **Footer**: Botón de cambio de tema.

## Rutas Asociadas
- `route('users.index')` - Gestión de usuarios.
- `route('rbac.index')` - Roles y permisos.
- `route('media.index')` - Biblioteca de medios.
- `route('media.manager')` - Administrador de medios.
- `route('logout')` - Cierre de sesión.

## Personalización
Puede ser extendido con:
- Más secciones de navegación.
- Submenús desplegables.
- Notificaciones o indicadores de estado.
- Búsqueda integrada.