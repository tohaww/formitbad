<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormBuilderUIController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormFieldController;
use App\Http\Controllers\FormIntegrationController;
use App\Http\Controllers\PublicFormController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// =========================================================================
// 1. PUBLIC ROUTES (Tanpa Autentikasi)
// =========================================================================

// Root Route: Jika sudah login redirect ke dashboard, jika belum tampilkan landing page publik resmi
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('home');

// Guest Only Login Page
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/auth/dev-login', [LoginController::class, 'devLogin'])->name('auth.dev_login');
});

// Legal & Compliance Pages (Google OAuth Verification)
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');

// Google OAuth 2.0 Web Authentication Flow
Route::prefix('auth/google')->group(function () {
    Route::get('/', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

// Public Form Short URL
Route::get('/f/{slug}', [PublicFormController::class, 'renderView'])->name('forms.public.short');

// =========================================================================
// 2. PROTECTED WEB ROUTES (Auth Middleware Guard)
// =========================================================================
Route::middleware('auth')->group(function () {

    // User Logout
    Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [GoogleAuthController::class, 'logout'])->name('logout.get');

    // 1. Dashboard Beranda ("My Forms" Jotform-Style)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Form Management & Lifecycle
    Route::post('/forms', [DashboardController::class, 'storeForm'])->name('forms.store_web');
    Route::get('/forms/{id}/edit', [FormBuilderUIController::class, 'show'])->name('forms.edit');
    Route::delete('/forms/{id}', [DashboardController::class, 'destroyForm'])->name('forms.destroy_web');
    Route::get('/forms/{id}/submissions', [FormBuilderUIController::class, 'submissions'])->name('forms.submissions');
    Route::post('/api/submissions/{id}/resync', [\App\Http\Controllers\SubmissionController::class, 'resync'])->name('web.submissions.resync');
    Route::get('/api/forms/{id}/export', [FormController::class, 'export'])->name('web.forms.export');

    // 3. Builder Workspace Aliases
    Route::get('/builder', [FormBuilderUIController::class, 'index'])->name('builder.index');
    Route::get('/builder/{id}', [FormBuilderUIController::class, 'show'])->name('builder.show');
    Route::get('/builder/{id}/submissions', [FormBuilderUIController::class, 'submissions'])->name('builder.submissions');

    // 4. Form Builder API Endpoints (Web Session Persistence)
    Route::put('/api/dashboard/forms/{id}/fields', [FormFieldController::class, 'syncFields'])->name('web.dashboard.forms.fields.sync');
    Route::put('/api/dashboard/forms/{id}', [FormController::class, 'update'])->name('web.dashboard.forms.update');

    // 5. Form Integrations Hub
    Route::get('/api/dashboard/forms/{id}/integrations', [FormIntegrationController::class, 'index'])->name('web.dashboard.forms.integrations.index');
    Route::get('/dashboard/integrations/google-sheets/connect', [FormIntegrationController::class, 'connectGoogleSheetsOAuth'])->name('web.dashboard.integrations.sheets.connect');
    Route::post('/api/dashboard/forms/{id}/integrations/google-sheets', [FormIntegrationController::class, 'saveGoogleSheets'])->name('web.dashboard.forms.integrations.sheets.save');
    Route::post('/api/dashboard/forms/{id}/integrations/google-sheets/create-auto', [FormIntegrationController::class, 'createAutoSpreadsheet'])->name('web.dashboard.forms.integrations.sheets.create_auto');
    Route::post('/api/dashboard/forms/{id}/integrations/google-sheets/test', [FormIntegrationController::class, 'testGoogleSheets'])->name('web.dashboard.forms.integrations.sheets.test');
    Route::delete('/api/dashboard/integrations/google-sheets/disconnect-user', [FormIntegrationController::class, 'disconnectGoogleAccount'])->name('web.dashboard.integrations.sheets.disconnect_user');
    Route::post('/api/dashboard/forms/{id}/integrations/webhooks', [FormIntegrationController::class, 'saveWebhooks'])->name('web.dashboard.forms.integrations.webhooks.save');
    Route::post('/api/dashboard/forms/{id}/integrations/webhooks/test', [FormIntegrationController::class, 'testWebhooks'])->name('web.dashboard.forms.integrations.webhooks.test');
    Route::delete('/api/dashboard/forms/{id}/integrations/{provider}', [FormIntegrationController::class, 'disconnect'])->name('web.dashboard.forms.integrations.disconnect');
});
