<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs
        $totalUsers   = User::count();
        $totalTickets = Ticket::count();

        // Logged-in users (requires SESSION_DRIVER=database)
        $onlineUsers = 0;
        if (config('session.driver') === 'database' && Schema::hasTable('sessions')) {
            $onlineUsers = DB::table('sessions')
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id');
        }

        // Show latest 200 codes (client-side search will filter)
        $tickets = Ticket::select('code')->latest()->take(200)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'onlineUsers',
            'totalTickets',
            'tickets'
        ));
    }
}
