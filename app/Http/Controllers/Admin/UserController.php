<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private const ALLOWED_ROLES = ['admin','user'];

    public function index()
    {
        // Ensure roles exist (lowercase, guard web)
        Role::firstOrCreate(['name'=>'admin','guard_name'=>'web']);
        Role::firstOrCreate(['name'=>'user','guard_name'=>'web']);

        $users = User::select('id','name','email','phone','is_blocked')
            ->with(['roles' => fn($q) =>
                $q->whereIn('name', self::ALLOWED_ROLES)->select('id','name')
            ])
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /** Users list page (Name, Email, Phone, Status, Roles) */
    public function show()
    {
        $users = User::with('roles:id,name')
            ->select('id','name','email','phone','is_blocked')
            ->orderByDesc('id')
            ->get();

        return view('admin.users.show', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', self::ALLOWED_ROLES)
            ->orderByRaw("FIELD(name,'admin','user')")
            ->get(['name']);

        return view('admin.users.create', ['roles'=>$roles, 'userRoles'=>[]]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','confirmed','min:8'],
            'phone'    => ['nullable','string','max:30'],
            'roles'    => ['required','array', 'min:1'],
            'roles.*'  => ['string'],
        ]);

        // Normalize and whitelist roles
        $picked = collect($data['roles'] ?? [])
            ->map(fn($r) => strtolower(trim($r)))
            ->intersect(self::ALLOWED_ROLES)
            ->values()
            ->all();

        // Fallback if nothing valid came through (shouldn't happen due to 'required')
        if (empty($picked)) {
            $picked = ['admin']; // default when creating via Admin panel
        }

        // Ensure the roles exist in the DB with guard web
        foreach ($picked as $r) {
            Role::firstOrCreate(['name'=>$r,'guard_name'=>'web']);
        }

        $user = User::create([
            'name'     => trim($data['name']),
            'email'    => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
        ]);

        // Apply selected roles
        $user->syncRoles($picked);

        return redirect()->route('admin.users.index')->with('success','User created.');
    }

    public function edit(User $user)
    {
        $roles = Role::whereIn('name', self::ALLOWED_ROLES)
            ->orderByRaw("FIELD(name,'admin','user')")
            ->get(['name']);

        $userRoles = $user->roles()->pluck('name')->map(fn($r)=>strtolower($r))->toArray();

        return view('admin.users.create', compact('user','roles','userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email,'.$user->id],
            'password' => ['nullable','confirmed','min:8'],
            'phone'    => ['nullable','string','max:30'],
            'roles'    => ['required','array','min:1'],
            'roles.*'  => ['string'],
        ]);

        $user->name  = trim($data['name']);
        $user->email = strtolower(trim($data['email']));
        $user->phone = $data['phone'] ?? null;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        // Normalize + whitelist
        $picked = collect($data['roles'] ?? [])
            ->map(fn($r) => strtolower(trim($r)))
            ->intersect(self::ALLOWED_ROLES)
            ->values()
            ->all();

        if (empty($picked)) {
            // Keep at least one role (fallback to existing or 'admin')
            $existing = $user->roles()->pluck('name')->map(fn($r)=>strtolower($r))->toArray();
            $picked = !empty($existing) ? $existing : ['admin'];
        }

        foreach ($picked as $r) {
            Role::firstOrCreate(['name'=>$r,'guard_name'=>'web']);
        }

        $user->syncRoles($picked);

        return redirect()->route('admin.users.index')->with('success','User updated.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('success','You cannot delete your own account.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success','User deleted.');
    }

    public function block(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('success','You cannot block yourself.');
        }
        $user->is_blocked = true;
        $user->save();

        if (config('session.driver') === 'database' && Schema::hasTable('sessions')) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return back()->with('success','User blocked.');
    }

    public function unblock(User $user)
    {
        $user->is_blocked = false;
        $user->save();

        return back()->with('success','User unblocked.');
    }
}
