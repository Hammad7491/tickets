<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketPurchase;
use Illuminate\Http\Request;                 // ✅ Import Request
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

    /** Single delete */
    public function destroy(TicketPurchase $purchase)
    {
        if ($purchase->proof_image_path) {
            Storage::disk('public')->delete($purchase->proof_image_path);
        }

        $purchase->delete();

        return back()->with('success', 'Purchase deleted.');
    }

    /** Bulk delete */
    public function bulkDelete(Request $request)        // ✅ Works now
    {
        $ids = collect(explode(',', (string) $request->input('ids')))
            ->filter()
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return back()->with('error', 'No rows selected.');
        }

        $rows = TicketPurchase::whereIn('id', $ids)->get();

        foreach ($rows as $p) {
            if ($p->proof_image_path) {
                Storage::disk('public')->delete($p->proof_image_path);
            }
            $p->delete();
        }

        return back()->with('success', 'Deleted '.$ids->count().' purchase(s).');
    }

    /** Accept while respecting stock */
    public function accept(TicketPurchase $purchase)
    {
        if ($purchase->status === 'accepted') {
            return redirect()->route('admin.reviews.accepted')
                ->with('success', 'This purchase was already accepted.');
        }

        if ($purchase->status === 'rejected') {
            return redirect()->route('admin.reviews.pending')
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

                // backfill serial if missing
                if (empty($purchase->serial)) {
                    $purchase->serial = $this->makeSerial();
                }

                $purchase->status = 'accepted';
                $purchase->save();

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

        return redirect()->route('admin.reviews.accepted')
            ->with('success', 'Purchase accepted.');
    }

    public function reject(TicketPurchase $purchase)
    {
        if ($purchase->status === 'rejected') {
            return redirect()->route('admin.reviews.pending')
                ->with('success', 'This purchase is already rejected.');
        }

        $purchase->update(['status' => 'rejected']);

        return redirect()->route('admin.reviews.pending')
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

    private function makeSerial(): string
    {
        do {
            $serial = 'PK' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (TicketPurchase::where('serial', $serial)->exists());

        return $serial;
    }
}
