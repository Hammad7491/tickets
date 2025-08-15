<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function pending()
    {
        $purchases = TicketPurchase::with([
                'user:id,name,email,phone',
                'ticket:id,name,image_path,quantity',
            ])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.reviews.pending', compact('purchases'));
    }

    public function accepted()
    {
        $purchases = TicketPurchase::with([
                'user:id,name,email,phone',
                'ticket:id,name,image_path,quantity',
            ])
            ->where('status', 'accepted')
            ->latest()
            ->paginate(20);

        return view('admin.reviews.accepted', compact('purchases'));
    }

    /**
     * Accept a purchase respecting ticket quantity.
     * If stock is already full, return with a friendly error.
     * Serial is already on the purchase; if ever missing, we backfill once.
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

        try {
            DB::transaction(function () use ($purchase) {
                $acceptedCount = TicketPurchase::where('ticket_id', $purchase->ticket_id)
                    ->where('status', 'accepted')
                    ->lockForUpdate()
                    ->count();

                $qty = (int) optional($purchase->ticket)->quantity ?: 1;

                if ($acceptedCount >= $qty) {
                    throw new \RuntimeException('This ticket is already sold out (another request is accepted).');
                }

                // Backfill serial only if somehow missing
                if (empty($purchase->serial)) {
                    $purchase->serial = $this->makeSerial();
                }

                $purchase->status = 'accepted';
                $purchase->save();

                // If stock now full, reject all remaining pendings
                if ($acceptedCount + 1 >= $qty) {
                    TicketPurchase::where('ticket_id', $purchase->ticket_id)
                        ->where('status', 'pending')
                        ->where('id', '!=', $purchase->id)
                        ->update(['status' => 'rejected']);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Unable to accept this purchase. Please try again.');
        }

        return redirect()
            ->route('admin.reviews.accepted')
            ->with('success', 'Purchase accepted.');
    }

    public function reject(TicketPurchase $purchase)
    {
        if ($purchase->status === 'rejected') {
            return redirect()
                ->route('admin.reviews.pending')
                ->with('success', 'This purchase is already rejected.');
        }

        // If you never want to reject after accept, block here:
        // if ($purchase->status === 'accepted') {
        //     return back()->with('error', 'Cannot reject an already accepted purchase.');
        // }

        $purchase->update(['status' => 'rejected']);

        return redirect()
            ->route('admin.reviews.pending')
            ->with('success', 'Purchase rejected.');
    }

    public function proofShow(TicketPurchase $purchase)
    {
        $path = $purchase->proof_image_path;
        abort_unless($path && Storage::disk('public')->exists($path), 404);
        return Storage::disk('public')->response($path);
    }

    public function proofDownload(TicketPurchase $purchase)
    {
        $path = $purchase->proof_image_path;
        abort_unless($path && Storage::disk('public')->exists($path), 404);
        return Storage::disk('public')->download($path);
    }

    /** Same generator used here as a fallback */
    private function makeSerial(): string
    {
        do {
            $serial = 'PK' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (TicketPurchase::where('serial', $serial)->exists());

        return $serial;
    }
}
