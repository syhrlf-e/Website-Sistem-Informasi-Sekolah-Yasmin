<?php
/**
 * API Routes - SMA Yasmin CMS
 * 
 * Routes ini menangani:
 * - Public API (tanpa auth)
 * - Admin API (auth:sanctum + is_admin_api middleware)
 */


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminCategoryApiController;
use App\Http\Controllers\Api\AdminPostApiController;
use App\Http\Controllers\Api\adminUserApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\AdminNewsController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\PrestasiController;
use App\Http\Controllers\Api\EkstrakurikulerController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\AdminEkstrakurikulerRegistrationController;
use App\Http\Controllers\Api\EkstrakurikulerRegistrationController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\AdminTestimonialController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\AdminDocumentController;
use App\Http\Controllers\Api\AdminGuruController;
use App\Http\Controllers\Api\PpdbRegistrationController;
use App\Http\Controllers\Api\AdminPpdbController;
use App\Http\Controllers\Api\AdminPpdbWaveController;
use App\Http\Controllers\Api\SekolahController;

/*
|--------------------------------------------------------------------------
| PUBLIC API ROUTES
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/login', [AuthController::class, 'login']);

// Ekskul Registration (public) - Rate limited to prevent spam
Route::post('/ekstrakurikuler/register', [EkstrakurikulerRegistrationController::class, 'store'])
    ->middleware('throttle:3,1'); // Max 3 requests per minute per IP

// Berita API
Route::prefix('berita')->group(function () {
    Route::get('/featured', [NewsController::class, 'featured']);
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/{slug}', [NewsController::class, 'show']);
});

// Announcement API
Route::get('announcement/active', [AnnouncementController::class, 'getActive']);

// Prestasi API
Route::prefix('prestasi')->group(function () {
    Route::get('/', [PrestasiController::class, 'index']);
    Route::get('/{slug}', [PrestasiController::class, 'show']);
});

// Ekstrakurikuler API
Route::prefix('ekstrakurikuler')->group(function () {
    Route::get('/', [EkstrakurikulerController::class, 'index']);
    Route::get('/{id}', [EkstrakurikulerController::class, 'show']);
});

// Galeri API
Route::get('/galeri', [GalleryController::class, 'index']);
Route::get('/galeri/grid', [GalleryController::class, 'getGridGalleries']);

// Testimonial API
Route::get('/testimonials', [TestimonialController::class, 'index']);
Route::post('/testimonials/submit', [AdminTestimonialController::class, 'submitPublic']);

// Document API
Route::get('/documents', [DocumentController::class, 'index']);
Route::get('/documents/{id}/download', [DocumentController::class, 'download']);

// Guru API
Route::get('/guru', [App\Http\Controllers\Api\GuruController::class, 'index']);

// PPDB Public API
Route::prefix('ppdb')->group(function () {
    Route::get('/wave/active', [PpdbRegistrationController::class, 'getActiveWave']);
    Route::post('/register', [PpdbRegistrationController::class, 'store'])
        ->middleware('throttle:3,1'); // Max 3 registrations per minute per IP
    Route::post('/check-status', [PpdbRegistrationController::class, 'checkStatus']);
    Route::get('/landing-info', [AdminPpdbController::class, 'getLandingSettings']); // Public landing info
});

// Sekolah Search API (proxy to Kemendikdasmen)
Route::get('/sekolah/search', [SekolahController::class, 'search']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

/*
|--------------------------------------------------------------------------
| ADMIN API ROUTES (Protected)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'is_admin_api'])->prefix('yasmin-panel')->group(function () {

    // Resources
    Route::apiResource('categories', AdminCategoryApiController::class);
    Route::apiResource('posts', AdminPostApiController::class);
    Route::apiResource('announcements', AnnouncementController::class);

    // Users
    Route::get('users/check-super-admin', [AdminUserApiController::class, 'checkSuperAdmin']);
    Route::apiResource('users', adminUserApiController::class);

    // Settings
    Route::get('settings', [SettingController::class, 'index']);
    Route::post('settings', [SettingController::class, 'update']);
    Route::post('settings/logo', [SettingController::class, 'uploadLogo']);
    Route::delete('settings/logo', [SettingController::class, 'deleteLogo']);

    // Dashboard
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/pendaftar', [DashboardController::class, 'pendaftar']);

    // News (with trash)
    Route::prefix('news')->group(function () {
        Route::get('/trash', [AdminNewsController::class, 'trash']);
        Route::post('/{id}/restore', [AdminNewsController::class, 'restore']);
        Route::delete('/{id}/force', [AdminNewsController::class, 'forceDelete']);
        Route::get('/', [AdminNewsController::class, 'index']);
        Route::post('/', [AdminNewsController::class, 'store']);
        Route::get('/{id}', [AdminNewsController::class, 'show']);
        Route::post('/{id}', [AdminNewsController::class, 'update']);
        Route::delete('/{id}', [AdminNewsController::class, 'destroy']);
    });

    // Prestasi
    Route::prefix('prestasi')->group(function () {
        Route::get('/', [PrestasiController::class, 'index']);
        Route::post('/', [PrestasiController::class, 'store']);
        Route::get('/{id}', [PrestasiController::class, 'show']);
        Route::put('/{id}', [PrestasiController::class, 'update']);
        Route::delete('/{id}', [PrestasiController::class, 'destroy']);
    });

    // Ekskul Registrations (must be before /ekstrakurikuler/{id})
    Route::prefix('ekstrakurikuler/registrations')->group(function () {
        Route::get('/', [AdminEkstrakurikulerRegistrationController::class, 'index']);
        Route::get('/{id}', [AdminEkstrakurikulerRegistrationController::class, 'show']);
        Route::put('/{id}/approve', [AdminEkstrakurikulerRegistrationController::class, 'approve']);
        Route::put('/{id}/reject', [AdminEkstrakurikulerRegistrationController::class, 'reject']);
        Route::delete('/{id}', [AdminEkstrakurikulerRegistrationController::class, 'destroy']);
    });

    // Ekstrakurikuler
    Route::prefix('ekstrakurikuler')->group(function () {
        Route::get('/', [EkstrakurikulerController::class, 'index']);
        Route::post('/', [EkstrakurikulerController::class, 'store']);
        Route::get('/{id}', [EkstrakurikulerController::class, 'show']);
        Route::post('/{id}', [EkstrakurikulerController::class, 'update']);
        Route::post('/{id}/generate-token', [EkstrakurikulerController::class, 'generateToken']);
        Route::put('/{id}/toggle-registration', [EkstrakurikulerController::class, 'toggleRegistration']);
        Route::delete('/{id}', [EkstrakurikulerController::class, 'destroy']);
    });

    // Galeri (grid system + original)
    Route::prefix('galeri')->group(function () {
        Route::get('/grid', [GalleryController::class, 'adminGridIndex']);
        Route::get('/grid/available-slots', [GalleryController::class, 'getAvailableSlots']);
        Route::post('/grid', [GalleryController::class, 'storeGridGallery']);
        Route::post('/grid/{gallery}', [GalleryController::class, 'updateGridGallery']);
        Route::post('/grid/{gallery}/add-image', [GalleryController::class, 'addImageToSlot']);
        Route::delete('/grid/{gallery}/image', [GalleryController::class, 'deleteSlotImage']);
        Route::delete('/grid/{gallery}', [GalleryController::class, 'destroyGridGallery']);
        Route::get('/', [GalleryController::class, 'adminIndex']);
        Route::post('/', [GalleryController::class, 'store']);
        Route::put('/{gallery}', [GalleryController::class, 'update']);
        Route::delete('/{gallery}', [GalleryController::class, 'destroy']);
    });

    // Testimonials
    Route::prefix('testimonials')->group(function () {
        Route::get('/', [AdminTestimonialController::class, 'index']);
        Route::post('/', [AdminTestimonialController::class, 'store']);
        Route::get('/{id}', [AdminTestimonialController::class, 'show']);
        Route::put('/{id}', [AdminTestimonialController::class, 'update']);
        Route::delete('/{id}', [AdminTestimonialController::class, 'destroy']);
        Route::put('/{id}/toggle', [AdminTestimonialController::class, 'toggleActive']);
        Route::post('/reorder', [AdminTestimonialController::class, 'updateOrder']);
    });

    // Documents
    Route::prefix('documents')->group(function () {
        Route::get('/', [AdminDocumentController::class, 'index']);
        Route::post('/', [AdminDocumentController::class, 'store']);
        Route::get('/{id}', [AdminDocumentController::class, 'show']);
        Route::put('/{id}', [AdminDocumentController::class, 'update']);
        Route::delete('/{id}', [AdminDocumentController::class, 'destroy']);
        Route::put('/{id}/toggle', [AdminDocumentController::class, 'toggleActive']);
    });

    // Guru
    Route::prefix('guru')->group(function () {
        Route::get('/', [AdminGuruController::class, 'index']);
        Route::post('/', [AdminGuruController::class, 'store']);
        Route::get('/{id}', [AdminGuruController::class, 'show']);
        Route::post('/{id}', [AdminGuruController::class, 'update']);
        Route::delete('/{id}', [AdminGuruController::class, 'destroy']);
    });

    // Activity Logs (Super Admin only recommended)
    Route::prefix('activity-logs')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ActivityLogController::class, 'index']);
        Route::get('/actions', [\App\Http\Controllers\Api\ActivityLogController::class, 'actions']);
        Route::get('/users', [\App\Http\Controllers\Api\ActivityLogController::class, 'users']);
    });

    // PPDB Admin Routes
    Route::prefix('ppdb')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminPpdbController::class, 'dashboard']);
        Route::get('/status-options', [AdminPpdbController::class, 'getStatusOptions']);

        // Landing Page Settings
        Route::get('/landing-settings', [AdminPpdbController::class, 'getLandingSettings']);
        Route::post('/landing-settings', [AdminPpdbController::class, 'updateLandingSettings']);

        // Registrations (Pendaftar)
        Route::get('/registrations', [AdminPpdbController::class, 'index']);
        Route::get('/registrations/{registration}', [AdminPpdbController::class, 'show']);
        Route::put('/registrations/{registration}/status', [AdminPpdbController::class, 'updateStatus']);
        Route::post('/registrations/bulk-status', [AdminPpdbController::class, 'bulkUpdateStatus']);
        Route::delete('/registrations/{registration}', [AdminPpdbController::class, 'destroy']);
        Route::get('/export', [AdminPpdbController::class, 'export']);

        // Waves (Gelombang)
        Route::get('/waves', [AdminPpdbWaveController::class, 'index']);
        Route::post('/waves', [AdminPpdbWaveController::class, 'store']);
        Route::get('/waves/open', [AdminPpdbWaveController::class, 'getOpenWave']);
        Route::get('/waves/{wave}', [AdminPpdbWaveController::class, 'show']);
        Route::put('/waves/{wave}', [AdminPpdbWaveController::class, 'update']);
        Route::put('/waves/{wave}/toggle', [AdminPpdbWaveController::class, 'toggleActive']);
        Route::delete('/waves/{wave}', [AdminPpdbWaveController::class, 'destroy']);
    });
});