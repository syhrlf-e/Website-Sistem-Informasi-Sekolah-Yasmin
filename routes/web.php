<?php
/**
 * Web Routes - SMA Yasmin CMS
 * 
 * Routes ini menangani:
 * - Public pages dengan Inertia (SEO optimized)
 * - Admin panel SPA
 * - Authentication
 */

use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| PUBLIC PAGES (Inertia with SEO Meta Tags)
|--------------------------------------------------------------------------
*/

Route::controller(PublicController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/profil', 'profil')->name('profil');
    Route::get('/news', 'news')->name('news.index');
    Route::get('/news/{slug}', 'newsDetail')->name('news.show');
    Route::get('/prestasi', 'prestasi')->name('prestasi');
    Route::get('/ppdb', 'ppdb')->name('ppdb'); // Info page on main domain
    Route::get('/guru', 'guru')->name('guru');
});

/*
|--------------------------------------------------------------------------
| PPDB ROUTES
|--------------------------------------------------------------------------
| Path-based routes always available for internal navigation.
| Subdomain routes added in production for direct access.
*/

// Path-based PPDB routes (always available - for internal navigation)
Route::controller(PublicController::class)->prefix('ppdb')->group(function () {
    Route::get('/landing', 'ppdbLanding')->name('ppdb.landing');
    Route::get('/daftar', 'ppdbDaftar')->name('ppdb.daftar');
    Route::get('/sukses', 'ppdbSukses')->name('ppdb.sukses');
    Route::get('/status', 'ppdbStatus')->name('ppdb.status');
});

// Subdomain routes (production only - for direct access & SEO)
$appDomain = env('APP_DOMAIN');
if ($appDomain) {
    Route::domain('ppdb.' . $appDomain)->controller(PublicController::class)->group(function () {
        Route::get('/', 'ppdbLanding')->name('ppdb.subdomain.landing');
        Route::get('/daftar', 'ppdbDaftar')->name('ppdb.subdomain.daftar');
        Route::get('/sukses', 'ppdbSukses')->name('ppdb.subdomain.sukses');
        Route::get('/status', 'ppdbStatus')->name('ppdb.subdomain.status');
    });
}

// Sitemap for SEO
Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('sitemap');

/*
|--------------------------------------------------------------------------
| ADMIN PANEL (Vue SPA with Inertia)
|--------------------------------------------------------------------------
*/

Route::post('/yasmin-panel/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/yasmin-panel/login');
})->name('admin.logout');

require __DIR__ . '/auth.php';

// Admin SPA fallback - semua /yasmin-panel/* routes di-handle oleh Vue Router
Route::get('/yasmin-panel/{any?}', function () {
    return Inertia::render('Admin/App');
})->where('any', '.*')->name('admin.spa');