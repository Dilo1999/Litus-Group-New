<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\SiteController;

// Serve storage files when symlink doesn't work (e.g. shared hosting / cPanel)
Route::match(['get', 'head'], 'storage/{path}', function () {
    $requestPath = ltrim(request()->path(), '/');
    if (!str_starts_with($requestPath, 'storage/')) {
        abort(404);
    }
    $path = substr($requestPath, 8); // strip 'storage/'
    if (empty($path) || str_contains($path, '..')) {
        abort(404);
    }

    $fullPath = storage_path('app/public/' . $path);
    $realPath = realpath($fullPath);
    $storageRoot = realpath(storage_path('app/public'));

    if (!$realPath || !is_file($realPath)) {
        abort(404);
    }
    if ($storageRoot && !Str::startsWith($realPath, $storageRoot)) {
        abort(404);
    }

    return response()->file($realPath);
})->where('path', '.*')->name('storage.serve');

Route::get('/', [SiteController::class, 'home'])->name('site.home');
Route::get('/our-companies', [SiteController::class, 'ourCompanies'])->name('site.our-companies');
Route::get('/our-companies/{slug}', [SiteController::class, 'company'])->name('site.company');

Route::get('/about', [SiteController::class, 'about'])->name('site.about');
Route::get('/team', [SiteController::class, 'team'])->name('site.team');
Route::get('/careers', [SiteController::class, 'careers'])->name('site.careers');

Route::get('/blogs', [SiteController::class, 'blogs'])->name('site.blogs');
Route::get('/events/{slug}', [SiteController::class, 'eventGallery'])->name('site.event');

Route::get('/contact', [SiteController::class, 'contact'])->name('site.contact');
Route::post('/contact', [SiteController::class, 'contactSubmit'])->name('site.contact.submit');
