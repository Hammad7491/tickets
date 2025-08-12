<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;  // make sure this import exists
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Optional: block check
        if (($user->is_blocked ?? false) === true) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is blocked.']);
        }

        $tickets = Ticket::select('code')->latest()->take(60)->get();

        return view('users.dashboard', [
            'user'    => $user,
            'tickets' => $tickets,
            'kpis'    => ['totalTickets' => $tickets->count()],
        ]);
    }
}
