<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    /**
     * Show user dashboard with tickets that still have stock.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $tickets = Ticket::select('id','name','image_path','quantity','created_at')
            ->withCount([
                'purchases as held_count' => function ($q) {
                    $q->whereIn('status', ['pending','accepted']);
                },
            ])
            ->orderByDesc('id')
            ->paginate(24);

        return view('users.dashboard', [
            'user'    => $user,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Reserve (buy) a ticket – stock-safe.
     */
    public function buy(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if (($user->is_blocked ?? false) === true) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is blocked.']);
        }

        $data = $request->validate([
            'account_number' => ['required','string','max:40'],
            'phone'          => ['nullable','string','max:30','regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'proof'          => ['required','file','mimes:jpg,jpeg,png,webp,pdf','max:5120'],
        ]);

        $proofPath = $request->file('proof')->store('purchases', 'public');

        if (!empty($data['phone'])) {
            $user->phone = $data['phone'];
            $user->save();
        }

        try {
            DB::transaction(function () use ($user, $ticket, $data, $proofPath) {
                $held = TicketPurchase::where('ticket_id', $ticket->id)
                    ->whereIn('status', ['pending','accepted'])
                    ->lockForUpdate()
                    ->count();

                if ($held >= (int)$ticket->quantity) {
                    abort(409, 'Out of stock for this ticket.');
                }

                TicketPurchase::create([
                    'user_id'          => $user->id,
                    'ticket_id'        => $ticket->id,
                    'status'           => 'pending',
                    'account_number'   => $data['account_number'],
                    'phone'            => $data['phone'] ?? $user->phone,
                    'proof_image_path' => $proofPath,
                ]);
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage() ?: 'Unable to reserve ticket.');
        }

        return back()->with('success', 'Ticket reserved. Please wait for admin review.');
    }
}
