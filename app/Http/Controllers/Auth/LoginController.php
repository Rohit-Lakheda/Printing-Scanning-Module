<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if ($user) {
            // Check if password in database is plain text (not hashed)
            $storedPassword = $user->password;
            $isPlainText = strlen($storedPassword) < 60 || !str_starts_with($storedPassword, '$2y$');

            if ($isPlainText) {
                // Password is stored as plain text
                // Check if provided password matches
                if ($storedPassword === $credentials['password']) {
                    // Hash the password and update in database
                    $user->password = bcrypt($credentials['password']);
                    $user->save();
                    
                    // Now login the user
                    Auth::login($user, $request->boolean('remember'));
                    $request->session()->regenerate();
                    
                    if ($user->isAdmin()) {
                        return redirect()->route('admin.dashboard');
                    }
                    
                    return redirect()->route('operator.home');
                }
            } else {
                // Password is already hashed, use normal authentication
                if (Auth::attempt($credentials, $request->boolean('remember'))) {
                    $request->session()->regenerate();
                    
                    if (Auth::user()->isAdmin()) {
                        return redirect()->route('admin.dashboard');
                    }
                    
                    return redirect()->route('operator.badge.search');
                }
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
