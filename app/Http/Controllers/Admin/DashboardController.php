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
        $totalTickets = Ticket::count(); // number of ticket items

        // Logged-in users (needs database sessions)
        $onlineUsers = 0;
        if (config('session.driver') === 'database' && Schema::hasTable('sessions')) {
            $onlineUsers = DB::table('sessions')
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id');
        }

        // Show ALL tickets.
        // held_count counts PENDING + ACCEPTED purchases (so Remaining drops on buy, rises back on reject)
        $tickets = Ticket::select('id', 'name', 'image_path', 'quantity')
            ->withCount([
                'purchases as held_count' => function ($q) {
                    $q->whereIn('status', ['pending', 'accepted']);
                },
            ])
            ->latest('id')
            ->take(200)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'onlineUsers',
            'totalTickets',
            'tickets'
        ));
    }
}
