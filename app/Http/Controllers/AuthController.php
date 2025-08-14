<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function logout()
    {
        Auth::logout();
        return redirect()->route('loginform');
    }

    public function loginform()
    {
        return view('auth.login');
    }

    public function registerform()
    {
        return view('auth.register');
    }

    public function error403()
    {
        return view('auth.errors.error403');
    }

    /**
     * Handle registration (manual or social data).
     */
    public function register(Request $request)
    {
        // If social data is present, firstOrCreate and then assign default “Client” role
        if ($request->has('google_user_data') || $request->has('facebook_user_data')) {
            $socialKey = $request->has('google_user_data') ? 'google_user_data' : 'facebook_user_data';
            $data      = $request->input($socialKey);

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['name'],
                    'avatar'    => $data['avatar'] ?? null,
                    // store whichever social ID you need
                    $socialKey === 'google_user_data'
                        ? 'google_id'
                        : 'facebook_id'
                    => $data[$socialKey === 'google_user_data' ? 'google_id' : 'facebook_id'],
                    'password'  => Hash::make(Str::random(16)),
                ]
            );

            // ensure they have at least the “Client” role
            if (! $user->hasRole('Client')) {
                $user->assignRole('Client');
            }

            Auth::login($user, true);
            return redirect()->route('user.dashboard');
        }

        // Manual registration
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:users,name',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // create & assign default Client role
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('user');

        return redirect()->route('loginform')->with('success', 'Registration successful');
    }

    /**
     * Handle login and redirect based on dynamic roles.
     */
 public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('user')) {
            return redirect()->route('user.dashboard');
        }

        Auth::logout();
        return redirect()->route('login')->withErrors(['role' => 'No valid role assigned.']);
    }

    return back()->withErrors(['email' => 'Invalid credentials.']);
}

}
