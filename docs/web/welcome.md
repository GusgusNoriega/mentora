# Página de Bienvenida (welcome.blade.php)

## Descripción
Esta es la página de bienvenida por defecto de Laravel, personalizada para el proyecto Mentora. Sirve como landing page inicial cuando los usuarios visitan la aplicación sin estar autenticados.

## Funcionalidad Principal
- **Presentación de la aplicación**: Muestra el logo de Laravel y el nombre del proyecto Mentora.
- **Enlaces de navegación**: Proporciona acceso a documentación externa y recursos de aprendizaje.
- **Información de inicio**: Texto introductorio sobre el ecosistema de Laravel.
- **Navegación de autenticación**: Enlaces para login y registro (si están disponibles).

## Estructura
- **Header**: Navegación superior con enlaces de autenticación condicionales.
- **Contenido principal**: Layout de dos columnas con texto informativo y logo visual.
- **Footer**: Espacio reservado para elementos adicionales.

## Características Técnicas
- **Responsive design**: Utiliza clases de Tailwind para adaptarse a diferentes tamaños de pantalla.
- **Modo oscuro**: Soporte completo para tema claro y oscuro.
- **Animaciones**: Transiciones suaves con clases de Tailwind.
- **Vite integration**: Carga de assets CSS y JS a través de Vite.

## Elementos Interactivos
- Enlaces externos a documentación de Laravel y Laracasts.
- Navegación condicional basada en rutas disponibles.
- Animaciones de entrada con delays.

## Tecnologías Utilizadas
- HTML5 semántico.
- Tailwind CSS v4.0.7 con configuración personalizada.
- Blade templating de Laravel.
- Vite para gestión de assets.
- Fuentes personalizadas (Instrument Sans).

## Ruta Asociada
Generalmente accesible en la ruta raíz `/` cuando no hay usuario autenticado.