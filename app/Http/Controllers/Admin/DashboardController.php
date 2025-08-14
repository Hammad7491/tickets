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

    // Logged-in users
    $onlineUsers = 0;
    if (config('session.driver') === 'database' && Schema::hasTable('sessions')) {
        $onlineUsers = DB::table('sessions')
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');
    }

    // Tickets where the latest purchase is NOT accepted
    $tickets = Ticket::select('tickets.id', 'tickets.name', 'tickets.serial', 'tickets.image_path')
        ->leftJoin('ticket_purchases', 'tickets.id', '=', 'ticket_purchases.ticket_id')
        ->where(function ($query) {
            $query->whereNull('ticket_purchases.status')
                  ->orWhereIn('ticket_purchases.status', ['pending', 'rejected']);
        })
        ->latest('tickets.id')
        ->distinct()
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
