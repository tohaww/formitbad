<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the application's clean login page.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Developer quick login bypass for local testing.
     */
    public function devLogin(Request $request)
    {
        if (!app()->environment('local')) {
            abort(403, 'Akses bypass pengembang hanya diperbolehkan pada lingkungan local.');
        }

        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@itbad.ac.id'],
            [
                'name' => 'Admin Kampus ITB AD',
                'google_id' => 'google-dev-12345',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Admin+ITB+AD&background=ff6100&color=fff',
            ]
        );

        Auth::login($user, true);

        return redirect()->route('dashboard')->with('success', 'Berhasil masuk dalam mode pengembang (Local Dev).');
    }
}

