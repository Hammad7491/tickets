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
     * - New signups are regular "user" role by default.
     * - We still check roles after login in case an admin creates an account elsewhere
     *   and then logs in (role-based redirect will work).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone'    => ['nullable', 'string', 'max:30'],
        ]);

        $user = User::create([
            'name'     => $request->string('name')->trim(),
            'email'    => strtolower($request->string('email')->trim()),
            'password' => Hash::make($request->password),
            'phone'    => $request->input('phone'),
        ]);

        // Fire the "Registered" event
        event(new Registered($user));

        // Make sure base roles exist, then assign "user" to self-registered accounts
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
