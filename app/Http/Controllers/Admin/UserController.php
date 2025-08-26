<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Only these roles are assignable via the Admin UI.
     */
    private const ALLOWED_ROLES = ['admin', 'user'];

    /**
     * List users (Name, Email, Phone, Role(s), Status).
     */
    public function index()
    {
        // Ensure base roles exist (guard: web)
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user',  'guard_name' => 'web']);

        $users = User::select('id', 'name', 'email', 'phone', 'is_blocked')
            ->with([
                'roles' => fn ($q) => $q
                    ->whereIn('name', self::ALLOWED_ROLES)
                    ->select('id', 'name')
            ])
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * (Optional extra page) Simple latest-first listing.
     */
    public function show()
    {
        $users = User::with('roles:id,name')
            ->select('id', 'name', 'email', 'phone', 'is_blocked')
            ->orderByDesc('id')
            ->get();

        return view('admin.users.show', compact('users'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $roles = Role::whereIn('name', self::ALLOWED_ROLES)
            ->orderByRaw("FIELD(name, 'admin', 'user')")
            ->get(['name']);

        return view('admin.users.create', [
            'roles'     => $roles,
            'userRoles' => [],
        ]);
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        // Normalize phone to digits-only before validating
        $phone = $this->sanitizePhone($request->input('phone'));
        $request->merge(['phone' => $phone]);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            // phone required, exactly 11 digits, NOT unique
            'phone'    => ['required', 'digits:11'],
            'roles'    => ['required', 'array', 'min:1'],
            'roles.*'  => ['string'],
        ]);

        // Normalize + whitelist roles
        $picked = collect($data['roles'] ?? [])
            ->map(fn ($r) => strtolower(trim($r)))
            ->intersect(self::ALLOWED_ROLES)
            ->values()
            ->all();

        if (empty($picked)) {
            $picked = ['admin'];
        }

        // Ensure roles exist (guard: web)
        foreach ($picked as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        $user = new User();
        $user->name     = trim($data['name']);
        $user->email    = strtolower(trim($data['email']));
        $user->password = Hash::make($data['password']);
        $user->phone    = $data['phone']; // normalized digits
        $user->save();

        $user->syncRoles($picked);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created.');
    }

    /**
     * Show edit form.
     */
    public function edit(User $user)
    {
        $roles = Role::whereIn('name', self::ALLOWED_ROLES)
            ->orderByRaw("FIELD(name, 'admin', 'user')")
            ->get(['name']);

        $userRoles = $user->roles()
            ->pluck('name')
            ->map(fn ($r) => strtolower($r))
            ->toArray();

        return view('admin.users.create', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update a user.
     */
    public function update(Request $request, User $user)
    {
        // Normalize phone to digits-only before validating
        $phone = $this->sanitizePhone($request->input('phone'));
        $request->merge(['phone' => $phone]);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', 'min:8'],
            // phone required, exactly 11 digits, NOT unique
            'phone'    => ['required', 'digits:11'],
            'roles'    => ['required', 'array', 'min:1'],
            'roles.*'  => ['string'],
        ]);

        $user->name  = trim($data['name']);
        $user->email = strtolower(trim($data['email']));
        $user->phone = $data['phone']; // normalized digits

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        $picked = collect($data['roles'] ?? [])
            ->map(fn ($r) => strtolower(trim($r)))
            ->intersect(self::ALLOWED_ROLES)
            ->values()
            ->all();

        if (empty($picked)) {
            $existing = $user->roles()->pluck('name')->map(fn ($r) => strtolower($r))->toArray();
            $picked   = !empty($existing) ? $existing : ['admin'];
        }

        foreach ($picked as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        $user->syncRoles($picked);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated.');
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('success', 'You cannot delete your own account.');
        }

        $this->invalidateRememberToken($user);
        $this->killSessionsOf($user);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted.');
    }

    /**
     * Block a user and immediately log them out everywhere.
     */
    public function block(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('success', 'You cannot block yourself.');
        }

        $user->is_blocked = true;
        $user->save();

        $this->killSessionsOf($user);
        $this->invalidateRememberToken($user);

        return back()->with('success', 'User blocked and logged out.');
    }

    /**
     * Unblock a user.
     */
    public function unblock(User $user)
    {
        $user->is_blocked = false;
        $user->save();

        return back()->with('success', 'User unblocked.');
    }

    /* -----------------------------------------------------------
     | Helpers
     |------------------------------------------------------------
     */

    /**
     * Keep digits only. Returns null if empty (controller requires phone so it won't be null after validate).
     */
    private function sanitizePhone(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);
        return $digits !== '' ? $digits : null;
    }

    /**
     * Kill all sessions for a user when using the database session driver.
     * Falls back to no-op if not applicable.
     */
    private function killSessionsOf(User $user): void
    {
        if (config('session.driver') === 'database' && Schema::hasTable('sessions')) {
            DB::table('sessions')
                ->where('user_id', (string) $user->getAuthIdentifier())
                ->delete();
        }
    }

    /**
     * Invalidate remember_token if the column exists (helps kick persistent logins).
     */
    private function invalidateRememberToken(User $user): void
    {
        if (Schema::hasColumn('users', 'remember_token')) {
            $user->setRememberToken(Str::random(60));
            $user->save();
        }
    }
}
