<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Show all pending purchases for admin review.
     */
    public function pending()
    {
        $purchases = TicketPurchase::with([
                'user:id,name,email,phone',
                'ticket:id,name,serial',
            ])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.reviews.pending', compact('purchases'));
    }

    /**
     * Show all accepted purchases.
     */
    public function accepted()
    {
        $purchases = TicketPurchase::with([
                'user:id,name,email,phone',
                'ticket:id,name,serial',
            ])
            ->where('status', 'accepted')
            ->latest()
            ->paginate(20);

        return view('admin.reviews.accepted', compact('purchases'));
    }

    /**
     * Accept a pending purchase and auto-reject other pending ones
     * for the same ticket, then go to the Accepted page.
     */
    public function accept(TicketPurchase $purchase)
    {
        
        if ($purchase->status === 'accepted') {
            return redirect()
                ->route('admin.reviews.accepted')
                ->with('success', 'This purchase was already accepted.');
        }

        if ($purchase->status === 'rejected') {
            return redirect()
                ->route('admin.reviews.pending')
                ->with('error', 'This purchase was already rejected.');
        }

        DB::transaction(function () use ($purchase) {
            // prevent double-accept for same ticket
            $alreadyAccepted = TicketPurchase::where('ticket_id', $purchase->ticket_id)
                ->where('status', 'accepted')
                ->where('id', '!=', $purchase->id)
                ->exists();

            if ($alreadyAccepted) {
                abort(409, 'Another purchase for this ticket is already accepted.');
            }

            // accept current
            $purchase->update(['status' => 'accepted']);

            // auto-reject other pendings on same ticket
            TicketPurchase::where('ticket_id', $purchase->ticket_id)
                ->where('status', 'pending')
                ->where('id', '!=', $purchase->id)
                ->update(['status' => 'rejected']);
        });

        // redirect to Accepted list
        return redirect()
            ->route('admin.reviews.accepted')
            ->with('success', 'Purchase accepted.');
    }

    /**
     * Reject a purchase (shows as Rejected on user side, no serial; disappears from Pending).
     */
    public function reject(TicketPurchase $purchase)
    {
        if ($purchase->status === 'rejected') {
            return redirect()
                ->route('admin.reviews.pending')
                ->with('success', 'This purchase is already rejected.');
        }

        // If you want to forbid rejecting after accepted, uncomment:
        // if ($purchase->status === 'accepted') {
        //     abort(409, 'Cannot reject an already accepted purchase.');
        // }

        $purchase->update(['status' => 'rejected']);

        return redirect()
            ->route('admin.reviews.pending')
            ->with('success', 'Purchase rejected.');
    }

    /**
     * Admin preview of proof (opens in new tab).
     */
    public function proofShow(TicketPurchase $purchase)
    {
        $path = $purchase->proof_image_path;

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }

    /**
     * Admin download of proof.
     */
    public function proofDownload(TicketPurchase $purchase)
    {
        $path = $purchase->proof_image_path;

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path);
    }
}
