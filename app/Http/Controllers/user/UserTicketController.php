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
    public function index(Request $request)
    {
        $user = $request->user();

        $purchases = TicketPurchase::with([
                'ticket:id,name,image_path',
            ])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(12);

        return view('users.ticketstatus.index', compact('purchases'));
    }

    public function proofShow(TicketPurchase $purchase)
    {
        $user = request()->user();
        abort_unless($purchase->user_id === $user->id, 403);

        $path = $purchase->proof_image_path;
        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return response()->file(storage_path('app/public/'.$path));
    }

    public function proofDownload(TicketPurchase $purchase)
    {
        $user = request()->user();
        abort_unless($purchase->user_id === $user->id, 403);

        $path = $purchase->proof_image_path;
        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path);
    }

    /**
     * Users can submit unlimited requests for the same ticket.
     * We only block when active (pending+accepted) >= ticket quantity.
     * A serial is generated now (admin can see instantly); user will see it only after acceptance.
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

        // ✅ Stock check: allow while active < quantity
        $activeCount = TicketPurchase::where('ticket_id', $ticket->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->count();

        if ($activeCount >= (int) $ticket->quantity) {
            return back()
                ->with('error', 'This ticket is unavailable right now.')
                ->with('buy_ticket_id', $ticket->id);
        }

        // Save required proof
        $proofPath = $request->file('proof')->store('purchases', 'public');

        // Remember phone (optional)
        if (!empty($validated['phone'])) {
            $user->phone = $validated['phone'];
            $user->save();
        }

        // Create purchase (pending) with a generated serial.
        TicketPurchase::create([
            'ticket_id'        => $ticket->id,
            'user_id'          => $user->id,
            'account_number'   => $validated['account_number'],
            'phone'            => $validated['phone'] ?? $user->phone,
            'proof_image_path' => $proofPath,
            'status'           => 'pending',
            'serial'           => $this->makeSerial(), // ← generate now
        ]);

        return back()->with('success', 'Your ticket request was submitted and is pending review.');
    }

    /** Generate PK + 6 digits (e.g., PK123456) and ensure uniqueness. */
    private function makeSerial(): string
    {
        do {
            $serial = 'PK' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (TicketPurchase::where('serial', $serial)->exists());

        return $serial;
    }
}
