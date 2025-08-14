<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserTicketController extends Controller
{
    /** Max active purchases per user */
    private int $maxAllowed = 5;

    /**
     * Show the logged-in user's ticket purchase requests (with ticket info).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $purchases = TicketPurchase::with(['ticket:id,name,serial,image_path'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(12);

        return view('users.ticketstatus.index', compact('purchases'));
    }

    /**
     * Inline preview of the uploaded proof (opens in browser).
     * Serves the file from storage so you don't need the /storage symlink.
     */
    public function proofShow(TicketPurchase $purchase)
    {
        $user = request()->user();
        abort_unless($purchase->user_id === $user->id, 403);

        $path = $purchase->proof_image_path;
        abort_unless($path && Storage::disk('public')->exists($path), 404);

        $absolute = storage_path('app/public/' . $path);
        return response()->file($absolute); // show inline
    }

    /**
     * Force download of the uploaded proof.
     */
    public function proofDownload(TicketPurchase $purchase)
    {
        $user = request()->user();
        abort_unless($purchase->user_id === $user->id, 403);

        $path = $purchase->proof_image_path;
        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path);
    }

    /**
     * Handle "Buy Now" — create a TicketPurchase in pending status.
     */
    public function buy(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        // Blocked users cannot buy
        if (($user->is_blocked ?? false) === true) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is blocked.']);
        }

        // Validate inputs
        $validated = $request->validate([
            'account_number' => ['required', 'string', 'max:40'],
            'phone'          => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'proof'          => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Enforce per-user active limit
        $activeCount = TicketPurchase::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->count();

        if ($activeCount >= $this->maxAllowed) {
            return back()
                ->with('error', "You’ve reached the {$this->maxAllowed} ticket limit.")
                ->with('buy_ticket_id', $ticket->id);
        }

        // Prevent buying a ticket that already has an active purchase
        $hasActive = TicketPurchase::where('ticket_id', $ticket->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        if ($hasActive) {
            return back()
                ->with('error', 'This ticket is unavailable right now.')
                ->with('buy_ticket_id', $ticket->id);
        }

        // Save required proof
        $proofPath = $request->file('proof')->store('purchases', 'public');

        // Remember phone for next time (optional)
        if (!empty($validated['phone'])) {
            $user->phone = $validated['phone'];
            $user->save();
        }

        // Create purchase
        TicketPurchase::create([
            'ticket_id'        => $ticket->id,
            'user_id'          => $user->id,
            'account_number'   => $validated['account_number'],
            'phone'            => $validated['phone'] ?? $user->phone,
            'proof_image_path' => $proofPath,
            'status'           => 'pending',
        ]);

        return back()->with('success', 'Your ticket request was submitted and is pending review.');
    }
}
