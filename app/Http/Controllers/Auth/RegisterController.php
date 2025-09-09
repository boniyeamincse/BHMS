<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        // Validate request is handled by RegisterRequest
        // Do not auto-assign hospitals in SaaS application
        // Require hospital selection during registration

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // REMOVED: auto hospital assignment for security
            // hospital_id should be set during hospital subscription/onboarding
        ]);

        // Regenerate session token for security before login
        $request->session()->regenerate();

        Auth::login($user);

        // Log successful registration
        \Log::info('New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip()
        ]);

        return redirect()->intended(route('hospital.dashboard'));
    }
}