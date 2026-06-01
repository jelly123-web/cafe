<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
                Auth::login($user);
            } else {
                // If user doesn't exist, we might want to create them or deny access.
                // For a cafe system, usually users are created by admin.
                // Let's create a new staff user by default if not found, 
                // OR we could redirect back with error if only existing users can login.
                // Let's go with redirecting back with error for security, 
                // as this is a specific business app.
                return redirect()->route('login')->with('error', 'Akun tidak terdaftar. Silakan hubungi admin.');
            }

            return redirect()->intended('dashboard');
            
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }
    }
}
