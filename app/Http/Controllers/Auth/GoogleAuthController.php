<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\GoogleAuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function __construct(
        protected GoogleAuthService $googleAuthService
    ) {}

    /**
     * Redirect the user to Google OAuth authentication page.
     */
    public function redirect(): RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Get redirect URL for frontend clients (SPAs/Mobile apps).
     */
    public function getAuthUrl(): JsonResponse
    {
        $targetUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();

        return response()->json([
            'status' => 'success',
            'url' => $targetUrl,
        ]);
    }

    /**
     * Obtain the user information from Google and authenticate.
     */
    public function callback(Request $request): JsonResponse|RedirectResponse
    {
        // Check for custom OAuth actions (e.g. Google Sheets integration user consent)
        $rawState = $request->input('state');
        if ($rawState) {
            $decodedState = json_decode(base64_decode($rawState), true);
            if (is_array($decodedState) && ($decodedState['action'] ?? null) === 'connect_sheets') {
                return $this->handleSheetsOAuthCallback($request, $decodedState);
            }
        }

        try {
            // Support both standard session and stateless OAuth flows
            $socialiteDriver = Socialite::driver('google');

            // If session state is empty or stateless is requested, fallback gracefully
            $googleUser = $request->has('stateless') || !$request->session()->has('state')
                ? $socialiteDriver->stateless()->user()
                : $socialiteDriver->user();

            $user = $this->googleAuthService->findOrCreateUser($googleUser);

            // Log in via standard session
            Auth::login($user, true);

            // Create Sanctum API token
            $token = $user->createToken('google-auth-token')->plainTextToken;

            // If JSON requested
            if ($request->wantsJson() || $request->query('format') === 'json') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Autentikasi Google berhasil.',
                    'token' => $token,
                    'user' => $user,
                ]);
            }

            // If SPA frontend redirect requested explicitly
            if ($request->has('redirect_to_frontend')) {
                $frontendUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000')), '/');
                $redirectTarget = "{$frontendUrl}/auth/callback?token={$token}&user_id={$user->id}";
                return redirect()->away($redirectTarget);
            }

            // Clear stale intended URLs and redirect directly to dashboard
            $request->session()->forget('url.intended');
            return redirect()->route('dashboard');
        } catch (Exception $e) {
            $errorMessage = $e instanceof \Illuminate\Validation\ValidationException
                ? ($e->validator->errors()->first() ?: $e->getMessage())
                : $e->getMessage();

            if ($request->wantsJson() || $request->query('format') === 'json') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal autentikasi Google: ' . $errorMessage,
                ], 422);
            }

            if ($request->has('redirect_to_frontend')) {
                $frontendUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000')), '/');
                return redirect()->away("{$frontendUrl}/login?error=" . urlencode($errorMessage));
            }

            return redirect()->route('login')->with('error', $errorMessage);
        }
    }

    /**
     * Get current authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'user' => $request->user(),
        ]);
    }

    /**
     * Log out current authenticated user.
     */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->user()) {
            // Revoke current token if using Sanctum
            $request->user()->currentAccessToken()?->delete();
        }

        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil logout.',
            ]);
        }

        return redirect()->route('login')->with('message', 'Anda telah berhasil logout.');
    }

    /**
     * Handle OAuth callback specifically for Google Sheets integration consent.
     */
    protected function handleSheetsOAuthCallback(Request $request, array $stateData): RedirectResponse
    {
        $formId = $stateData['form_id'] ?? null;
        $userId = $stateData['user_id'] ?? null;
        $code = $request->input('code');

        $redirectBase = $formId ? route('forms.edit', ['id' => $formId]) : route('dashboard');

        if (!$code) {
            return redirect($redirectBase)->with('error', 'Otorisasi Google Sheets dibatalkan.');
        }

        try {
            $client = new \Google\Client();
            $client->setClientId(config('services.google.client_id', env('GOOGLE_CLIENT_ID')));
            $client->setClientSecret(config('services.google.client_secret', env('GOOGLE_CLIENT_SECRET')));
            $client->setRedirectUri(route('auth.google.callback'));

            $token = $client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                throw new Exception($token['error_description'] ?? $token['error']);
            }

            // Extract email & name from id_token or OAuth payload
            $email = null;
            $name = null;
            if (!empty($token['id_token'])) {
                $payload = $client->verifyIdToken($token['id_token']);
                if ($payload) {
                    $email = $payload['email'] ?? null;
                    $name = $payload['name'] ?? null;
                }
            }

            // Identify user
            $user = $userId ? \App\Models\User::find($userId) : Auth::user();
            if (!$user && Auth::check()) {
                $user = Auth::user();
            }

            if ($user) {
                $user->update([
                    'google_sheets_access_token' => $token['access_token'] ?? null,
                    'google_sheets_refresh_token' => $token['refresh_token'] ?? $user->google_sheets_refresh_token,
                    'google_sheets_token_expires_at' => isset($token['expires_in']) ? now()->addSeconds((int)$token['expires_in']) : now()->addHour(),
                    'google_sheets_email' => $email ?? $user->email,
                    'google_sheets_name' => $name ?? $user->name,
                ]);

                if (!Auth::check()) {
                    Auth::login($user, true);
                }
            }

            $targetUrl = $formId
                ? route('forms.edit', ['id' => $formId]) . '?tab=settings&integration=sheets&status=connected'
                : route('dashboard');

            return redirect($targetUrl)->with('success', 'Akun Google Sheets berhasil terhubung! Anda sekarang dapat memilih atau membuat spreadsheet otomatis.');
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Sheets OAuth callback error: ' . $e->getMessage());
            return redirect($redirectBase)->with('error', 'Gagal menghubungkan Google Sheets: ' . $e->getMessage());
        }
    }
}

