<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle a new user registration.
     *
     * - Phone is required and stored as digits-only.
     * - Email is unique; phone is NOT unique (per your requirement).
     * - Newly registered users get the "user" role by default.
     */
    public function store(Request $request)
    {
        // Normalize phone to digits BEFORE validation
        $normalizedPhone = preg_replace('/\D+/', '', (string) $request->input('phone'));
        $request->merge(['phone' => $normalizedPhone]);

        // Validate inputs
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            // phone required, exactly 11 digits, NOT unique
            'phone'    => ['required', 'digits:11'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create user explicitly (no mass-assignment ambiguity)
        $user = new User();
        $user->name     = trim((string) $request->input('name'));
        $user->email    = strtolower(trim((string) $request->input('email')));
        $user->phone    = $request->input('phone'); // already normalized to digits
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Fire the "Registered" event
        event(new Registered($user));

        // Ensure roles exist (guard: web) and assign default role
        Role::firstOrCreate(['name' => 'user',  'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user->assignRole('user');

        // Log them in
        Auth::login($user);

        // Redirect by role (if this account already has admin role, send to admin dashboard)
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('users.dashboard'));
    }
}
