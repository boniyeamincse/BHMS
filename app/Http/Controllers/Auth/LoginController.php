<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Regenerate session BEFORE authentication to prevent session fixation
        $request->session()->regenerate();

        $remember = $request->boolean('remember', false);

        if (Auth::attempt($request->only('email', 'password'), $remember)) {
            $user = Auth::user();

            // Log successful login
            Log::info('User logged in', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'remember' => $remember
            ]);

            // Check if user is Super Admin and redirect to SaaS dashboard
            if ($user->roles()->where('name', 'Super Admin')->exists()) {
                return redirect()->intended(route('saas.dashboard'));
            }

            // Default to hospital dashboard for all other users
            return redirect()->intended(route('hospital.dashboard'));
        }

        // Log failed login attempt
        Log::info('Failed login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}