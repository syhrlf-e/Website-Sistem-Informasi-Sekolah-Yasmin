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
    Route::get('/ppdb', 'ppdb')->name('ppdb');
    Route::get('/ppdb/landing', 'ppdbLanding')->name('ppdb.landing');
    Route::get('/ppdb/daftar', 'ppdbDaftar')->name('ppdb.daftar');
    Route::get('/ppdb/sukses', 'ppdbSukses')->name('ppdb.sukses');
    Route::get('/ppdb/status', 'ppdbStatus')->name('ppdb.status');
    Route::get('/guru', 'guru')->name('guru');
});

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