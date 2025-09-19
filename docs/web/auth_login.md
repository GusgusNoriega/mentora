# Formulario de Inicio de Sesión (auth/login.blade.php)

## Descripción
Esta vista proporciona el formulario de autenticación para que los usuarios puedan acceder al sistema Mentora. Es una página independiente con diseño limpio y funcional.

## Funcionalidad Principal
- **Autenticación de usuarios**: Formulario para ingresar correo electrónico y contraseña.
- **Validación de errores**: Muestra mensajes de error si la autenticación falla.
- **Recordar sesión**: Opción para mantener la sesión activa.
- **Navegación visual**: Enlace de retorno a la página principal.

## Estructura del Formulario
- **Campo de email**: Input requerido con validación de formato.
- **Campo de contraseña**: Input de tipo password con toggle para mostrar/ocultar.
- **Checkbox "Recordarme"**: Para mantener la sesión activa.
- **Botón de envío**: Envía los datos al endpoint de login.

## Características Técnicas
- **Tailwind CDN**: Carga de Tailwind desde CDN para independencia.
- **Configuración personalizada**: Paleta de colores brand personalizada.
- **JavaScript vanilla**: Toggle de visibilidad de contraseña sin frameworks.
- **Responsive design**: Adaptable a móviles y desktop.
- **Accesibilidad**: Labels apropiados y atributos ARIA.

## Elementos Interactivos
- **Toggle de contraseña**: Botón para mostrar/ocultar el texto de la contraseña.
- **Validación en tiempo real**: Feedback visual en campos enfocados.
- **Manejo de errores**: Display de errores de validación del backend.

## Tecnologías Utilizadas
- HTML5 con formularios semánticos.
- Tailwind CSS desde CDN.
- JavaScript ES6 para interactividad.
- Blade templating de Laravel.
- CSRF protection integrado.

## Ruta Asociada
Accesible en `/login` o a través de `route('login')`.

## Seguridad
- Protección CSRF con token automático.
- Validación de entrada en backend.
- Autocomplete apropiado para accesibilidad.
- No almacenamiento de contraseñas en frontend.