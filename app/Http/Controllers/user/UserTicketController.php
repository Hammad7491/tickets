<?php
// app/Http/Controllers/User/UserTicketController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserTicketController extends Controller
{
    // Max active purchases per user
    private int $maxAllowed = 5;

    public function buy(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        // Blocked users can’t buy
        if (($user->is_blocked ?? false) === true) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is blocked.']);
        }

        // Validate phone + optional proof image
        $validated = $request->validate([
            'phone' => ['nullable','string','max:30','regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'proof' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ]);

        // Enforce per-user active limit
        $activeCount = TicketPurchase::where('user_id', $user->id)
            ->whereIn('status', ['pending','accepted'])
            ->count();

        if ($activeCount >= $this->maxAllowed) {
            return back()
                ->with('error', "You’ve reached the {$this->maxAllowed} ticket limit.")
                ->with('buy_ticket_id', $ticket->id); // re-open modal
        }

        // Prevent buying a ticket that already has an active purchase
        $hasActive = TicketPurchase::where('ticket_id', $ticket->id)
            ->whereIn('status', ['pending','accepted'])
            ->exists();

        if ($hasActive) {
            return back()
                ->with('error', 'This ticket is unavailable right now.')
                ->with('buy_ticket_id', $ticket->id);
        }

        // Save proof if provided
        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('purchases', 'public');
        }

        // Optionally update user phone (so it’s remembered next time)
        if (!empty($validated['phone'])) {
            $user->phone = $validated['phone'];
            $user->save();
        }

        // Create purchase (default pending)
        TicketPurchase::create([
            'ticket_id'        => $ticket->id,
            'user_id'          => $user->id,
            'phone'            => $validated['phone'] ?? $user->phone,
            'proof_image_path' => $proofPath,
            'status'           => 'pending',
        ]);

        return back()->with('success', 'Your ticket request was submitted and is pending review.');
    }
}
