<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * AuthController - Handle login, register, logout, password changes
 */
class AuthController extends BaseController
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('books.index');
        }
        return view('auth.login');
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('books.index');
        }
        return view('auth.register');
    }

    /**
     * Register new member with validation
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|confirmed',
            ], [
                'email.unique' => 'This email is already registered. Please sign in instead.',
                'password.confirmed' => 'The password confirmation does not match.',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'member',
                'can_borrow' => true,
            ]);

            $this->logAction('New member registered: ' . $user->name . ' (' . $user->email . ')');
            Auth::login($user);
            
            return redirect()->route('books.index')->with('success', 'Account created successfully! Welcome to our library.');
        } catch (\Exception $e) {
            return $this->handleError($e, 'register', 'Error creating account');
        }
    }

    /**
     * Authenticate user
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                $this->logAction('User logged in: ' . Auth::user()->email);
                return redirect()->route('books.index')->with('success', 'Welcome back!');
            }

            return back()->withErrors(['email' => 'Invalid credentials.']);
        } catch (\Exception $e) {
            return $this->handleError($e, 'login', 'Error during login');
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $this->logAction('User logged out: ' . Auth::user()->email);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    /**
     * Show password reset form
     */
    public function showResetPassword()
    {
        return view('auth.reset-password');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ], [
                'password.confirmed' => 'The new password confirmation does not match.',
            ]);

            $user = Auth::user();

            // Check current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            // Update password
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            $this->logAction('User changed password: ' . $user->email);
            return redirect()->route('books.index')->with('success', 'Password updated successfully!');
        } catch (\Exception $e) {
            return $this->handleError($e, 'reset-password', 'Error updating password');
        }
    }
}
