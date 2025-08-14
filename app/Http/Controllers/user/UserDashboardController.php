<?php
// app/Http/Controllers/User/UserDashboardController.php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
 public function index(Request $request)
{
    $user = $request->user();

    // Blocked users cannot access
    if (($user->is_blocked ?? false) === true) {
        Auth::logout();
        return redirect()->route('login')
            ->withErrors(['email' => 'Your account is blocked.']);
    }

    // My active (pending + accepted) count
    $myActivePurchases = TicketPurchase::where('user_id', $user->id)
        ->whereIn('status', ['pending', 'accepted'])
        ->count();

    $tickets = Ticket::select('id', 'name', 'serial', 'image_path', 'created_at')

        // Count ANY active purchase for the card (so you can still show "Unavailable" when someone else has it)
        ->withCount([
            'purchases as active_purchase_count' => function ($q) {
                $q->whereIn('status', ['pending', 'accepted']);
            },
        ])

        // Load *my* latest purchase (if any) for button text like “Buy Again” when last was rejected
        ->with([
            'purchases' => function ($q) use ($user) {
                $q->where('user_id', $user->id)->latest()->limit(1);
            },
        ])

        // HIDE ONLY tickets where *I* have an active (pending/accepted) purchase.
        // If mine was rejected, it will show again.
        ->whereDoesntHave('purchases', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->whereIn('status', ['pending', 'accepted']);
        })

        ->latest()
        ->paginate(24);

    return view('users.dashboard', [
        'user'              => $user,
        'tickets'           => $tickets,
        'myActivePurchases' => $myActivePurchases,
        'maxAllowed'        => 5,
    ]);
}



    public function buy(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        // Enforce per-user cap of 5 active (pending/accepted)
        $myActivePurchases = TicketPurchase::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->count();

        if ($myActivePurchases >= 5) {
            return back()->with('error', 'Purchase limit reached (max 5 tickets).');
        }

        // If anyone currently holds this ticket (pending/accepted), block
        $held = TicketPurchase::where('ticket_id', $ticket->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        if ($held) {
            // allow buy again ONLY if this user’s last for this ticket was rejected
            $myLast = TicketPurchase::where('ticket_id', $ticket->id)
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            if (!($myLast && $myLast->status === 'rejected')) {
                return back()->with('error', 'Sorry, this ticket is already reserved/purchased.');
            }
        }

        TicketPurchase::create([
            'user_id'   => $user->id,
            'ticket_id' => $ticket->id,
            'status'    => 'pending', // admin will accept/reject later
        ]);

        return back()->with('success', 'Ticket reserved. Please complete the next steps in your dashboard.');
    }
}
