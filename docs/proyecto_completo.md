# Documentación Completa del Proyecto Mentora

## Resumen del Proyecto

Mentora es un sistema de gestión de aprendizaje (LMS) desarrollado en Laravel 12. Es una plataforma completa para la creación, gestión y consumo de cursos en línea, con funcionalidades avanzadas de autenticación, roles y permisos, progreso de aprendizaje, evaluaciones, certificación, monetización y multimedia.

### Tecnologías Principales
- **Framework**: Laravel 12.0
- **PHP**: Versión 8.3+
- **Base de Datos**: SQLite (por defecto), compatible con MySQL/MariaDB/PostgreSQL
- **Autenticación**: Laravel Passport (OAuth2)
- **Roles y Permisos**: Spatie Laravel Permission
- **Frontend**: Blade templates con componentes
- **Desarrollo**: Vite para assets, Composer para dependencias

### Dependencias Clave
- `laravel/framework: ^12.0`
- `laravel/passport: ^13.0`
- `spatie/laravel-permission: ^6.21`
- `laravel/tinker: ^2.10.1`

## Estructura del Proyecto

### Directorios Principales
```
app/
├── Http/Controllers/          # Controladores de la aplicación
├── Models/                    # Modelos Eloquent
├── Services/                  # Servicios de negocio
├── Http/Middleware/           # Middleware personalizado
├── Providers/                 # Service Providers
bootstrap/
├── app.php                    # Configuración de aplicación
├── providers.php              # Registro de providers
config/                        # Archivos de configuración
database/
├── migrations/                # Migraciones de base de datos
├── seeders/                   # Seeders para datos iniciales
├── factories/                 # Factories para pruebas
public/                        # Assets públicos
resources/
├── views/                     # Vistas Blade
├── css/                       # Estilos CSS
├── js/                        # JavaScript
routes/
├── web.php                    # Rutas web
├── api.php                    # Rutas API
docs/                          # Documentación del proyecto
```

## Base de Datos

### Modelos Eloquent

#### Usuario (User)
- **Tabla**: `users`
- **Campos principales**: id, name, email, password, email_verified_at
- **Relaciones**:
  - `createdCourses()`: HasMany (cursos creados)
  - `instructorCourses()`: BelongsToMany (cursos como instructor)
  - `enrolledCourses()`: BelongsToMany (cursos inscritos)
  - `lessonsProgress()`: BelongsToMany (progreso en lecciones)
  - `coursesProgress()`: BelongsToMany (progreso en cursos)
  - `subscriptions()`: HasMany (suscripciones)
  - `certificates()`: HasMany (certificados)
  - `mediaAssets()`: HasMany (assets multimedia)

#### Curso (Course)
- **Tabla**: `courses`
- **Campos principales**: id, title, slug, summary, description, thumbnail_url, level, language, status, access_mode, price_cents, currency, created_by, published_at
- **Relaciones**:
  - `creator()`: BelongsTo (usuario creador)
  - `sections()`: HasMany (secciones)
  - `lessons()`: HasManyThrough (lecciones)
  - `instructors()`: BelongsToMany (instructores)
  - `categories()`: BelongsToMany (categorías)
  - `tags()`: BelongsToMany (etiquetas)
  - `students()`: BelongsToMany (estudiantes)
  - `enrollments()`: HasMany (inscripciones)
  - `progress()`: HasMany (progreso)
  - `reviews()`: HasMany (reseñas)
  - `certificates()`: HasMany (certificados)
  - `paymentTransactions()`: HasMany (transacciones)
  - `wishlistedBy()`: BelongsToMany (lista de deseos)
  - `coupons()`: BelongsToMany (cupones)

#### Otras Entidades Principales
- **Category**: Categorías de cursos
- **Tag**: Etiquetas para clasificación
- **CourseSection**: Secciones dentro de cursos
- **CourseLesson**: Lecciones individuales
- **Enrollment**: Inscripciones de usuarios a cursos
- **LessonProgress**: Progreso por lección
- **CourseProgress**: Progreso general por curso
- **Quiz**: Cuestionarios de evaluación
- **Certificate**: Certificados emitidos
- **MediaAsset**: Assets multimedia
- **SubscriptionPlan**: Planes de suscripción
- **PaymentTransaction**: Transacciones de pago
- **Coupon**: Cupones de descuento

### Migraciones Principales

#### Usuarios y Autenticación
- `create_users_table`: Usuarios, tokens de reset, sesiones
- `create_oauth_*_table`: Tablas de Passport para OAuth2

#### Contenido del LMS
- `create_courses_table`: Cursos principales
- `create_course_sections_table`: Secciones de cursos
- `create_course_lessons_table`: Lecciones individuales
- `create_categories_table`: Categorías
- `create_tags_table`: Etiquetas
- `create_course_category_table`: Relación cursos-categorías
- `create_course_tag_table`: Relación cursos-etiquetas

#### Gestión de Aprendizaje
- `create_enrollments_table`: Inscripciones
- `create_lesson_progress_table`: Progreso por lección
- `create_course_progress_table`: Progreso por curso

#### Evaluaciones
- `create_quizzes_table`: Cuestionarios
- `create_quiz_questions_table`: Preguntas
- `create_quiz_options_table`: Opciones de respuesta
- `create_quiz_attempts_table`: Intentos de quiz
- `create_quiz_answers_table`: Respuestas

#### Monetización
- `create_subscription_plans_table`: Planes de suscripción
- `create_user_subscriptions_table`: Suscripciones de usuarios
- `create_payment_transactions_table`: Transacciones
- `create_coupons_table`: Cupones
- `create_coupon_redemptions_table`: Redenciones

#### Multimedia y Social
- `create_media_assets_table`: Assets multimedia
- `create_lesson_media_table`: Relación lección-media
- `create_course_reviews_table`: Reseñas
- `create_wishlists_table`: Lista de deseos

## Controladores

### AuthController
- `showLogin()`: Muestra formulario de login
- `login(Request $request)`: Procesa login
- `logout(Request $request)`: Procesa logout
- `apiLogin(Request $request)`: Login vía API

### UserController
- `index(Request $request)`: Lista usuarios (admin)
- `show(Request $request, $id)`: Muestra usuario específico
- `store(Request $request)`: Crea nuevo usuario
- `update(Request $request, $id)`: Actualiza usuario
- `destroy(Request $request, $id)`: Elimina usuario
- `profile(Request $request)`: Perfil del usuario autenticado
- `enrolledCourses(Request $request, $id)`: Cursos inscritos
- `coursesProgress(Request $request, $id)`: Progreso en cursos
- `certificates(Request $request, $id)`: Certificados

### RoleController
- CRUD completo para roles con validaciones y respuestas JSON

### PermissionController
- CRUD completo para permisos con validaciones y respuestas JSON

### RolePermissionController
- `index(Request $request, int $roleId)`: Lista permisos de un rol
- `attach(Request $request, int $roleId)`: Asigna permisos a rol
- `sync(Request $request, int $roleId)`: Sincroniza permisos de rol
- `detach(Request $request, int $roleId)`: Remueve permisos de rol

### MediaAssetController
- `index(Request $request)`: Lista assets multimedia
- `show($id)`: Muestra asset específico
- `store(Request $request)`: Sube nuevo asset
- `update(Request $request, $id)`: Actualiza asset
- `destroy($id)`: Elimina asset
- Métodos auxiliares para determinar tipo MIME y duración

## Rutas

### Rutas Web
```php
// Autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Zona protegida
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', ...);
    Route::view('/rbac', 'rbac.index');
    Route::view('/users', 'users.index');
    Route::view('/media', 'media.index');
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

### Rutas API
```php
// Autenticación
Route::post('/login', [AuthController::class, 'apiLogin']);
Route::get('/user', ...)->middleware('auth:web,api');

// Gestión de Usuarios
Route::middleware(['auth:web,api'])->prefix('users')->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);
    Route::get('/enrolled-courses', [UserController::class, 'enrolledCourses']);
    Route::get('/courses-progress', [UserController::class, 'coursesProgress']);
    Route::get('/certificates', [UserController::class, 'certificates']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
});

// Admin - Usuarios
Route::middleware(['auth:web,api', 'admin'])->prefix('admin/users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

// RBAC
Route::middleware(['auth:web,api', 'admin'])->prefix('rbac')->group(function () {
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::get('roles/{roleId}/permissions', [RolePermissionController::class, 'index']);
    Route::post('roles/{roleId}/permissions/attach', [RolePermissionController::class, 'attach']);
    Route::post('roles/{roleId}/permissions/sync', [RolePermissionController::class, 'sync']);
    Route::post('roles/{roleId}/permissions/detach', [RolePermissionController::class, 'detach']);
});

// Multimedia
Route::middleware(['auth:web,api'])->prefix('media')->group(function () {
    Route::get('/', [MediaAssetController::class, 'index']);
    Route::get('/{id}', [MediaAssetController::class, 'show']);
    Route::post('/', [MediaAssetController::class, 'store']);
    Route::patch('/{id}', [MediaAssetController::class, 'update']);
    Route::delete('/{id}', [MediaAssetController::class, 'destroy']);
});
```

## Vistas y Componentes

### Estructura de Vistas
```
resources/views/
├── dashboard.blade.php          # Dashboard principal
├── welcome.blade.php            # Página de bienvenida
├── auth/
│   └── login.blade.php          # Formulario de login
├── components/
│   └── dashboard/
│       ├── footer.blade.php     # Footer del dashboard
│       ├── sidebar.blade.php    # Sidebar de navegación
│       └── topbar.blade.php     # Barra superior
├── layouts/
│   └── dashboard.blade.php      # Layout base del dashboard
├── media/
│   ├── index.blade.php          # Gestión de multimedia
│   ├── manager.blade.php        # Manager de archivos
│   └── test.blade.php           # Vista de pruebas
├── rbac/
│   └── index.blade.php          # Gestión de RBAC
└── users/
    └── index.blade.php          # Gestión de usuarios
```

### Componentes Principales
- **Sidebar**: Navegación lateral con menú de secciones
- **Topbar**: Barra superior con información del usuario
- **Footer**: Pie de página del dashboard

## Middleware y Servicios

### Middleware Personalizado

#### CheckAdminRole
- Verifica autenticación del usuario
- Valida que el usuario tenga rol 'admin'
- Retorna respuestas JSON estandarizadas para errores

### Servicios Personalizados

#### RbacMirror
Servicio avanzado para sincronización de roles y permisos entre guards (web y api):

- `mirrorPermissionCreated()`: Crea permiso en guard espejo
- `mirrorPermissionUpdated()`: Actualiza permiso en ambos guards
- `mirrorPermissionDeleted()`: Elimina permiso de ambos guards
- `mirrorRoleCreated()`: Crea rol con permisos en guard espejo
- `mirrorRoleUpdated()`: Actualiza rol en ambos guards
- `attachPermissions()`: Asigna permisos a rol en ambos guards
- `syncPermissions()`: Sincroniza permisos de rol
- `detachPermissions()`: Remueve permisos de rol
- `syncUserRolesBothGuardsByNames()`: Sincroniza roles de usuario en ambos guards

## Configuraciones

### Configuración de Aplicación (config/app.php)
- **Nombre**: Laravel (configurable via .env)
- **Entorno**: production (configurable)
- **Debug**: false (configurable)
- **URL**: http://localhost (configurable)
- **Timezone**: UTC
- **Locale**: en (configurable)

### Configuración de Base de Datos (config/database.php)
- **Conexión por defecto**: sqlite
- **Conexiones soportadas**: SQLite, MySQL, MariaDB, PostgreSQL, SQL Server
- **Configuración de Redis**: Para cache y sesiones

### Configuración de Autenticación (config/auth.php)
- **Guard por defecto**: web
- **Guards disponibles**:
  - `web`: Sesiones
  - `api`: Passport (OAuth2)
- **Provider**: users (Eloquent User model)

## Seeders y Factories

### Seeders

#### DatabaseSeeder
Orquesta la ejecución de todos los seeders:
- RbacSeeder
- DefaultUserSeeder
- PassportKeysSeeder
- CleanUploadsSeeder

#### RbacSeeder
Crea estructura base de RBAC:
- **Permisos**: users.view, users.create, users.edit, users.delete, posts.view, posts.create, posts.edit, posts.delete, settings.manage
- **Roles**:
  - `admin`: Todos los permisos
  - `editor`: Permisos de posts
  - `viewer`: Permisos de lectura

#### DefaultUserSeeder
Crea usuarios por defecto para desarrollo

#### PassportKeysSeeder
Configura claves de Passport para OAuth2

#### CleanUploadsSeeder
Limpia directorio de uploads

### Factories

#### UserFactory
- Genera usuarios con datos faker
- Email único y verificado
- Password hasheado (por defecto 'password')

## Documentación Adicional

### Plan de Desarrollo Modular
Archivo: `docs/plan_desarrollo_modulos.md`

Contiene:
- Plan de desarrollo por fases
- Arquitectura modular
- Dependencias entre módulos
- Roadmap de implementación
- Tareas técnicas detalladas

### API RBAC
Archivo: `docs/rbac_api.md`

Documentación específica de la API de roles y permisos:
- Endpoints detallados
- Estructuras de respuesta
- Ejemplos de uso con curl
- Reglas de validación
- Convenciones de implementación

## Instalación y Configuración

### Requisitos
- PHP 8.2+
- Composer
- Node.js y npm (para assets)
- Base de datos (SQLite por defecto)

### Pasos de Instalación
1. `composer install`
2. `cp .env.example .env`
3. `php artisan key:generate`
4. `php artisan migrate`
5. `php artisan db:seed`
6. `php artisan passport:install`
7. `npm install && npm run dev`

### Comandos Útiles
- `php artisan serve`: Inicia servidor de desarrollo
- `php artisan migrate:fresh --seed`: Reinicia BD con seeders
- `php artisan passport:client --password`: Crea cliente OAuth2
- `composer run dev`: Inicia desarrollo con hot reload

## Arquitectura y Patrones

### Patrón MVC
- **Modelos**: Eloquent con relaciones complejas
- **Vistas**: Blade templates con componentes
- **Controladores**: Lógica de negocio y respuestas API

### Servicios
- Separación de lógica de negocio
- Inyección de dependencias
- Reutilización de código

### Middleware
- Autenticación y autorización
- Validación de roles
- Control de acceso basado en permisos

### Eventos y Listeners
- Desacoplamiento de módulos
- Comunicación asíncrona
- Extensibilidad del sistema

## Seguridad

### Autenticación
- Laravel Passport para OAuth2
- Guards web y api
- Tokens de acceso y refresh

### Autorización
- Spatie Laravel Permission
- Roles y permisos granulares
- Middleware de verificación

### Validación
- Form Requests
- Reglas de validación robustas
- Sanitización de datos

## Próximos Pasos

Según el plan de desarrollo modular:

### Fase 1: Usuarios y Autenticación ✅
- Implementar endpoints completos de auth
- Verificación de email
- Reset de contraseña

### Fase 2: Roles y Permisos ✅
- API completa de RBAC
- Middleware de autorización
- Seeds base

### Fase 3: Cursos y Estructura
- CRUD de cursos, secciones y lecciones
- Gestión de instructores
- Publicación de contenido

### Fase 4: Clasificación
- Sistema de categorías y tags
- Filtrado y búsqueda

### Fase 5: Inscripciones
- Alta a cursos
- Gestión de expiraciones

### Fase 6: Progreso
- Tracking de aprendizaje
- Métricas de completion

### Fase 7: Evaluaciones
- Sistema de quizzes
- Calificación automática

### Fase 8: Certificación
- Emisión de certificados
- Plantillas personalizables

### Fase 9: Monetización
- Planes de suscripción
- Sistema de pagos
- Cupones de descuento

### Fase 10: Social
- Reseñas y ratings
- Lista de deseos
- Comunidad

---

*Documentación generada automáticamente el 2025-09-19*