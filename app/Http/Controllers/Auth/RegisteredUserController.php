    <?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Models\User;
    use App\Providers\RouteServiceProvider;
    use Illuminate\Auth\Events\Registered;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Validation\Rules;
    use Spatie\Permission\Models\Role;

    class RegisteredUserController extends Controller
    {
        /**
         * Show the registration view.
         */
        public function create()
        {
            return view('auth.register');
        }


        




        /**
         * Handle an incoming registration request.
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
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'phone'    => $request->phone,
            ]);

            // Fire "Registered" event (keeps Breeze/Jetstream flows intact)
            event(new Registered($user));

            // Ensure roles table has the "user" role, then assign it
            Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
            $user->assignRole('user');

            // Login and redirect
          Auth::login($user);

// redirect by role
if ($user->hasRole('admin')) {
    return redirect()->intended(route('dashboard'));       // your admin dashboard route
}
return redirect()->intended(route('users.dashboard'));  
    }
