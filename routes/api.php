<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormFieldController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

// ========================================================
// 1. PUBLIC ENDPOINTS (Form Rendering & Submission Engine)
// ========================================================
Route::prefix('public/forms')->group(function () {
    Route::get('{slug}', [PublicFormController::class, 'show'])->name('api.public.forms.show');
    Route::post('{slug}/submit', [PublicFormController::class, 'submit'])->name('api.public.forms.submit');
    Route::get('{slug}/fields/{field_id}/remote-options', [PublicFormController::class, 'getRemoteOptions'])->name('api.public.forms.fields.remote_options');
});

// ========================================================
// 2. AUTHENTICATION (Google OAuth & Session)
// ========================================================
Route::prefix('auth/google')->group(function () {
    Route::get('url', [GoogleAuthController::class, 'getAuthUrl'])->name('api.auth.google.url');
    Route::get('callback', [GoogleAuthController::class, 'callback'])->name('api.auth.google.callback');
});

// ========================================================
// 3. PROTECTED DASHBOARD API (Requires Sanctum Token)
// ========================================================
Route::middleware('auth:sanctum')->group(function () {
    // Current User
    Route::get('auth/me', [GoogleAuthController::class, 'me'])->name('api.auth.me');
    Route::post('auth/logout', [GoogleAuthController::class, 'logout'])->name('api.auth.logout');

    // Forms Management CRUD
    Route::apiResource('forms', FormController::class);
    Route::post('forms/{id}/test-webhook', [FormController::class, 'testWebhook'])->name('api.forms.test_webhook');
    Route::get('forms/{id}/export', [FormController::class, 'export'])->name('api.forms.export');

    // Form Fields Management
    Route::put('dashboard/forms/{id}/fields', [FormFieldController::class, 'syncFields'])->name('api.dashboard.forms.fields.sync');
    Route::put('forms/{id}/fields', [FormFieldController::class, 'syncFields'])->name('api.forms.fields.sync');
    Route::post('forms/{formId}/fields', [FormFieldController::class, 'store'])->name('api.forms.fields.store');
    Route::put('forms/{formId}/fields/reorder', [FormFieldController::class, 'reorder'])->name('api.forms.fields.reorder');
    Route::put('fields/{fieldId}', [FormFieldController::class, 'update'])->name('api.fields.update');
    Route::delete('fields/{fieldId}', [FormFieldController::class, 'destroy'])->name('api.fields.destroy');

    // Submissions
    Route::get('forms/{formId}/submissions', [SubmissionController::class, 'index'])->name('api.forms.submissions.index');
    Route::post('submissions/{id}/resync', [SubmissionController::class, 'resync'])->name('api.submissions.resync');

    // Integrations Hub (Google Sheets & Webhooks)
    Route::get('dashboard/forms/{id}/integrations', [\App\Http\Controllers\FormIntegrationController::class, 'index'])->name('api.dashboard.forms.integrations.index');
    Route::post('dashboard/forms/{id}/integrations/google-sheets', [\App\Http\Controllers\FormIntegrationController::class, 'saveGoogleSheets'])->name('api.dashboard.forms.integrations.sheets.save');
    Route::post('dashboard/forms/{id}/integrations/google-sheets/test', [\App\Http\Controllers\FormIntegrationController::class, 'testGoogleSheets'])->name('api.dashboard.forms.integrations.sheets.test');
    Route::post('dashboard/forms/{id}/integrations/webhooks', [\App\Http\Controllers\FormIntegrationController::class, 'saveWebhooks'])->name('api.dashboard.forms.integrations.webhooks.save');
    Route::post('dashboard/forms/{id}/integrations/webhooks/test', [\App\Http\Controllers\FormIntegrationController::class, 'testWebhooks'])->name('api.dashboard.forms.integrations.webhooks.test');
    Route::delete('dashboard/forms/{id}/integrations/{provider}', [\App\Http\Controllers\FormIntegrationController::class, 'disconnect'])->name('api.dashboard.forms.integrations.disconnect');
});
