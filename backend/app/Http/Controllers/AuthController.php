<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ConnectionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('login', $request->login)
                    ->where('is_active', true)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Log failed login attempt
            $this->logConnection($request->login, $request, false);

            return response()->json([
                'error' => 'Identifiants incorrects ou compte désactivé'
            ], 401);
        }

        // Log successful connection
        $this->logConnection($user->email, $request, true);

        // Create session for the user
        Auth::login($user, true);

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name
            ]
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function me(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $user = Auth::user();
        
        return response()->json(['user' => $user]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success' => true]);
    }

    /**
     * Submit a password reset request.
     */
    public function passwordResetRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        \App\Models\PasswordResetRequest::create([
            'user_email' => $request->email,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return response()->json([
            'message' => 'Demande de réinitialisation envoyée. Un administrateur la traitera prochainement.'
        ]);
    }

    /**
     * Log connection attempt.
     */
    private function logConnection($email, Request $request, bool $success)
    {
        ConnectionLog::create([
            'user_email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at' => now(),
            'success' => $success,
        ]);
    }
}