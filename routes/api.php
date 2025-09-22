<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediaAssetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSectionController;
use App\Http\Controllers\CourseLessonController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\CourseProgressController;
use App\Http\Controllers\LessonProgressController;
use App\Http\Controllers\CourseReviewController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\UserSubscriptionController;
use App\Http\Controllers\PaymentTransactionController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\WishlistController;


Route::post('/login', [AuthController::class, 'apiLogin']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:web,api');

// User management routes
Route::middleware(['auth:web,api'])->prefix('users')->group(function () {
    // Perfil del usuario autenticado
    Route::get('/profile', [UserController::class, 'profile']);

    // Cursos inscritos del usuario autenticado
    Route::get('/enrolled-courses', [UserController::class, 'enrolledCourses']);

    // Progreso de cursos del usuario autenticado
    Route::get('/courses-progress', [UserController::class, 'coursesProgress']);

    // Certificados del usuario autenticado
    Route::get('/certificates', [UserController::class, 'certificates']);

    // Ver un usuario específico (propio o si es admin)
    Route::get('/{id}', [UserController::class, 'show']);

    // Cursos inscritos de un usuario específico
    Route::get('/{id}/enrolled-courses', [UserController::class, 'enrolledCourses']);

    // Progreso de cursos de un usuario específico
    Route::get('/{id}/courses-progress', [UserController::class, 'coursesProgress']);

    // Certificados de un usuario específico
    Route::get('/{id}/certificates', [UserController::class, 'certificates']);

    // Actualizar usuario (propio o si es admin)
    Route::put('/{id}', [UserController::class, 'update']);
});

// Admin-only user management routes
Route::middleware(['auth:web,api', 'admin'])->prefix('admin/users')->group(function () {
    // Listar todos los usuarios con filtros
    Route::get('/', [UserController::class, 'index']);

    // Crear nuevo usuario
    Route::post('/', [UserController::class, 'store']);

    // Eliminar usuario
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

// RBAC protected routes (require authentication and admin role)
Route::middleware(['auth:web,api', 'admin'])->prefix('rbac')->group(function () {
    Route::apiResource('roles', RoleController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::apiResource('permissions', PermissionController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::get('roles/{roleId}/permissions', [RolePermissionController::class, 'index']);
    Route::post('roles/{roleId}/permissions/attach', [RolePermissionController::class, 'attach']);
    Route::post('roles/{roleId}/permissions/sync', [RolePermissionController::class, 'sync']);
    Route::post('roles/{roleId}/permissions/detach', [RolePermissionController::class, 'detach']);
});


// =======================
// MEDIA (protegido por Passport)
// =======================
Route::middleware(['auth:web,api'])->prefix('media')->group(function () {
    Route::get('/', [MediaAssetController::class, 'index']);      // GET /api/media
    Route::get('/{id}', [MediaAssetController::class, 'show']);   // GET /api/media/{id}
    Route::post('/', [MediaAssetController::class, 'store']);     // POST /api/media
    Route::patch('/{id}', [MediaAssetController::class, 'update']);// PATCH /api/media/{id}
    Route::delete('/{id}', [MediaAssetController::class, 'destroy']);// DELETE /api/media/{id}
    });

// =======================
// CATEGORIES (protegido por Passport)
// =======================
Route::middleware(['auth:web,api'])->prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);      // GET /api/categories
    Route::get('/{id}', [CategoryController::class, 'show']);   // GET /api/categories/{id}
    Route::post('/', [CategoryController::class, 'store']);     // POST /api/categories
    Route::put('/{id}', [CategoryController::class, 'update']); // PUT /api/categories/{id}
    Route::delete('/{id}', [CategoryController::class, 'destroy']);// DELETE /api/categories/{id}
});

// =======================
// TAGS (protegido por Passport)
// =======================
Route::middleware(['auth:web,api'])->prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'index']);      // GET /api/tags
    Route::get('/{id}', [TagController::class, 'show']);   // GET /api/tags/{id}
    Route::post('/', [TagController::class, 'store']);     // POST /api/tags
    Route::put('/{id}', [TagController::class, 'update']); // PUT /api/tags/{id}
    Route::delete('/{id}', [TagController::class, 'destroy']);// DELETE /api/tags/{id}
});

// =======================
// COURSES (protegido por Passport)
// =======================
Route::middleware(['auth:web,api'])->prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'index']);      // GET /api/courses
    Route::get('/{id}', [CourseController::class, 'show']);   // GET /api/courses/{id}
    Route::post('/', [CourseController::class, 'store']);     // POST /api/courses
    Route::put('/{id}', [CourseController::class, 'update']); // PUT /api/courses/{id}
    Route::delete('/{id}', [CourseController::class, 'destroy']);// DELETE /api/courses/{id}

    // Course Sections
    Route::get('/{courseId}/sections', [CourseSectionController::class, 'index']);      // GET /api/courses/{courseId}/sections
    Route::get('/{courseId}/sections/{id}', [CourseSectionController::class, 'show']);   // GET /api/courses/{courseId}/sections/{id}
    Route::post('/{courseId}/sections', [CourseSectionController::class, 'store']);     // POST /api/courses/{courseId}/sections
    Route::put('/{courseId}/sections/{id}', [CourseSectionController::class, 'update']); // PUT /api/courses/{courseId}/sections/{id}
    Route::delete('/{courseId}/sections/{id}', [CourseSectionController::class, 'destroy']);// DELETE /api/courses/{courseId}/sections/{id}

    // Course Lessons
    Route::get('/{courseId}/sections/{sectionId}/lessons', [CourseLessonController::class, 'index']);      // GET /api/courses/{courseId}/sections/{sectionId}/lessons
    Route::get('/{courseId}/sections/{sectionId}/lessons/{id}', [CourseLessonController::class, 'show']);   // GET /api/courses/{courseId}/sections/{sectionId}/lessons/{id}
    Route::post('/{courseId}/sections/{sectionId}/lessons', [CourseLessonController::class, 'store']);     // POST /api/courses/{courseId}/sections/{sectionId}/lessons
    Route::put('/{courseId}/sections/{sectionId}/lessons/{id}', [CourseLessonController::class, 'update']); // PUT /api/courses/{courseId}/sections/{sectionId}/lessons/{id}
    Route::delete('/{courseId}/sections/{sectionId}/lessons/{id}', [CourseLessonController::class, 'destroy']);// DELETE /api/courses/{courseId}/sections/{sectionId}/lessons/{id}

    // Quizzes
    Route::get('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes', [QuizController::class, 'index']);      // GET /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes
    Route::get('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{id}', [QuizController::class, 'show']);   // GET /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{id}
    Route::post('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes', [QuizController::class, 'store']);     // POST /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes
    Route::put('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{id}', [QuizController::class, 'update']); // PUT /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{id}
    Route::delete('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{id}', [QuizController::class, 'destroy']);// DELETE /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{id}

    // Quiz Questions
    Route::get('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/questions', [QuizQuestionController::class, 'index']);      // GET /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/questions
    Route::get('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/questions/{id}', [QuizQuestionController::class, 'show']);   // GET /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/questions/{id}
    Route::post('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/questions', [QuizQuestionController::class, 'store']);     // POST /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/questions
    Route::put('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/questions/{id}', [QuizQuestionController::class, 'update']); // PUT /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/questions/{id}
    Route::delete('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/questions/{id}', [QuizQuestionController::class, 'destroy']);// DELETE /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/questions/{id}

    // Quiz Attempts
    Route::get('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/attempts', [QuizAttemptController::class, 'index']);      // GET /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/attempts
    Route::get('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/attempts/{id}', [QuizAttemptController::class, 'show']);   // GET /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/attempts/{id}
    Route::post('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/attempts', [QuizAttemptController::class, 'store']);     // POST /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/attempts
    Route::put('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/attempts/{id}', [QuizAttemptController::class, 'update']); // PUT /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/attempts/{id}
    Route::delete('/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/attempts/{id}', [QuizAttemptController::class, 'destroy']);// DELETE /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/quizzes/{quizId}/attempts/{id}

    // Course Progress
    Route::get('/{courseId}/progress', [CourseProgressController::class, 'index']);      // GET /api/courses/{courseId}/progress
    Route::get('/{courseId}/progress/{id}', [CourseProgressController::class, 'show']);   // GET /api/courses/{courseId}/progress/{id}
    Route::post('/{courseId}/progress', [CourseProgressController::class, 'store']);     // POST /api/courses/{courseId}/progress
    Route::put('/{courseId}/progress/{id}', [CourseProgressController::class, 'update']); // PUT /api/courses/{courseId}/progress/{id}
    Route::delete('/{courseId}/progress/{id}', [CourseProgressController::class, 'destroy']);// DELETE /api/courses/{courseId}/progress/{id}

    // Lesson Progress
    Route::get('/{courseId}/sections/{sectionId}/lessons/{lessonId}/progress', [LessonProgressController::class, 'index']);      // GET /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/progress
    Route::get('/{courseId}/sections/{sectionId}/lessons/{lessonId}/progress/{id}', [LessonProgressController::class, 'show']);   // GET /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/progress/{id}
    Route::post('/{courseId}/sections/{sectionId}/lessons/{lessonId}/progress', [LessonProgressController::class, 'store']);     // POST /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/progress
    Route::put('/{courseId}/sections/{sectionId}/lessons/{lessonId}/progress/{id}', [LessonProgressController::class, 'update']); // PUT /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/progress/{id}
    Route::delete('/{courseId}/sections/{sectionId}/lessons/{lessonId}/progress/{id}', [LessonProgressController::class, 'destroy']);// DELETE /api/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/progress/{id}

    // Course Reviews
    Route::get('/{courseId}/reviews', [CourseReviewController::class, 'index']);      // GET /api/courses/{courseId}/reviews
    Route::get('/{courseId}/reviews/{id}', [CourseReviewController::class, 'show']);   // GET /api/courses/{courseId}/reviews/{id}
    Route::post('/{courseId}/reviews', [CourseReviewController::class, 'store']);     // POST /api/courses/{courseId}/reviews
    Route::put('/{courseId}/reviews/{id}', [CourseReviewController::class, 'update']); // PUT /api/courses/{courseId}/reviews/{id}
    Route::delete('/{courseId}/reviews/{id}', [CourseReviewController::class, 'destroy']);// DELETE /api/courses/{courseId}/reviews/{id}
});

// =======================
// ENROLLMENTS (protegido por Passport)
// =======================
Route::middleware(['auth:web,api'])->prefix('enrollments')->group(function () {
    Route::get('/', [EnrollmentController::class, 'index']);      // GET /api/enrollments
    Route::get('/{id}', [EnrollmentController::class, 'show']);   // GET /api/enrollments/{id}
    Route::post('/', [EnrollmentController::class, 'store']);     // POST /api/enrollments
    Route::put('/{id}', [EnrollmentController::class, 'update']); // PUT /api/enrollments/{id}
    Route::delete('/{id}', [EnrollmentController::class, 'destroy']);// DELETE /api/enrollments/{id}
});

// =======================
// CERTIFICATES (protegido por Passport)
// =======================
Route::middleware(['auth:web,api'])->prefix('certificates')->group(function () {
    Route::get('/', [CertificateController::class, 'index']);      // GET /api/certificates
    Route::get('/{id}', [CertificateController::class, 'show']);   // GET /api/certificates/{id}
    Route::post('/', [CertificateController::class, 'store']);     // POST /api/certificates
    Route::put('/{id}', [CertificateController::class, 'update']); // PUT /api/certificates/{id}
    Route::delete('/{id}', [CertificateController::class, 'destroy']);// DELETE /api/certificates/{id}
});

// =======================
// SUBSCRIPTION PLANS (protegido por Passport)
// =======================
Route::middleware(['auth:web,api'])->prefix('subscription-plans')->group(function () {
    Route::get('/', [SubscriptionPlanController::class, 'index']);      // GET /api/subscription-plans
    Route::get('/{id}', [SubscriptionPlanController::class, 'show']);   // GET /api/subscription-plans/{id}
    Route::post('/', [SubscriptionPlanController::class, 'store']);     // POST /api/subscription-plans
    Route::put('/{id}', [SubscriptionPlanController::class, 'update']); // PUT /api/subscription-plans/{id}
    Route::delete('/{id}', [SubscriptionPlanController::class, 'destroy']);// DELETE /api/subscription-plans/{id}
});

// =======================
// USER SUBSCRIPTIONS (protegido por Passport)
// =======================
Route::middleware(['auth:web,api'])->prefix('user-subscriptions')->group(function () {
    Route::get('/', [UserSubscriptionController::class, 'index']);      // GET /api/user-subscriptions
    Route::get('/{id}', [UserSubscriptionController::class, 'show']);   // GET /api/user-subscriptions/{id}
    Route::post('/', [UserSubscriptionController::class, 'store']);     // POST /api/user-subscriptions
    Route::put('/{id}', [UserSubscriptionController::class, 'update']); // PUT /api/user-subscriptions/{id}
    Route::delete('/{id}', [UserSubscriptionController::class, 'destroy']);// DELETE /api/user-subscriptions/{id}
});

// =======================
// PAYMENT TRANSACTIONS (protegido por Passport)
// =======================
Route::middleware(['auth:web,api'])->prefix('payment-transactions')->group(function () {
    Route::get('/', [PaymentTransactionController::class, 'index']);      // GET /api/payment-transactions
    Route::get('/{id}', [PaymentTransactionController::class, 'show']);   // GET /api/payment-transactions/{id}
    Route::post('/', [PaymentTransactionController::class, 'store']);     // POST /api/payment-transactions
    Route::put('/{id}', [PaymentTransactionController::class, 'update']); // PUT /api/payment-transactions/{id}
    Route::delete('/{id}', [PaymentTransactionController::class, 'destroy']);// DELETE /api/payment-transactions/{id}
});

// =======================
// COUPONS (protegido por Passport)
// =======================
Route::middleware(['auth:web,api'])->prefix('coupons')->group(function () {
    Route::get('/', [CouponController::class, 'index']);      // GET /api/coupons
    Route::get('/{id}', [CouponController::class, 'show']);   // GET /api/coupons/{id}
    Route::post('/', [CouponController::class, 'store']);     // POST /api/coupons
    Route::put('/{id}', [CouponController::class, 'update']); // PUT /api/coupons/{id}
    Route::delete('/{id}', [CouponController::class, 'destroy']);// DELETE /api/coupons/{id}
});

// =======================
// WISHLIST (protegido por Passport)
// =======================
Route::middleware(['auth:web,api'])->prefix('wishlist')->group(function () {
    Route::get('/', [WishlistController::class, 'index']);      // GET /api/wishlist
    Route::get('/{id}', [WishlistController::class, 'show']);   // GET /api/wishlist/{id}
    Route::post('/', [WishlistController::class, 'store']);     // POST /api/wishlist
    Route::put('/{id}', [WishlistController::class, 'update']); // PUT /api/wishlist/{id}
    Route::delete('/{id}', [WishlistController::class, 'destroy']);// DELETE /api/wishlist/{id}
});