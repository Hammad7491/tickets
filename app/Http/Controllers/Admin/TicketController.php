<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::orderByDesc('id')->paginate(20);
        return view('admin.tickets.index', compact('tickets'));
    }

    public function create()
    {
        // same blade used for create & edit
        return view('admin.tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            // PK + up to 6 digits (total max 8 chars), e.g. PK000040
            'serial' => ['required', 'string', 'max:8', 'regex:/^PK\d{0,6}$/', 'unique:tickets,serial'],
            'image'  => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $ticket = new Ticket();
        $ticket->name   = $validated['name'];
        $ticket->serial = $validated['serial'];

        if ($request->hasFile('image')) {
            // stores to storage/app/public/tickets/xxxxx.jpg
            $path = $request->file('image')->store('tickets', 'public');
            // Save relative path only
            $ticket->image_path = $path;
        }

        $ticket->save();

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'Ticket created successfully.');
    }

    public function edit(Ticket $ticket)
    {
        // reuse the same view; pass $ticket to switch to edit mode
        return view('admin.tickets.create', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'serial' => [
                'required', 'string', 'max:8', 'regex:/^PK\d{0,6}$/',
                Rule::unique('tickets', 'serial')->ignore($ticket->id),
            ],
            'image'  => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $ticket->name   = $validated['name'];
        $ticket->serial = $validated['serial'];

        // If a new image is uploaded, delete the old one and save the new path
        if ($request->hasFile('image')) {
            if (!empty($ticket->image_path) && Storage::disk('public')->exists($ticket->image_path)) {
                Storage::disk('public')->delete($ticket->image_path);
            }
            $path = $request->file('image')->store('tickets', 'public');
            $ticket->image_path = $path;
        }

        $ticket->save();

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        // delete stored image if exists
        if (!empty($ticket->image_path) && Storage::disk('public')->exists($ticket->image_path)) {
            Storage::disk('public')->delete($ticket->image_path);
        }

        $ticket->delete();

        return back()->with('success', 'Ticket deleted successfully.');
    }

    /**
     * Inline preview the image (served from storage/app/public).
     * Route uses a wildcard to capture slashes.
     */
    public function image(string $path)
    {
        $path = urldecode($path);

        // basic safety
        if (! str_starts_with($path, 'tickets/') || str_contains($path, '..')) {
            abort(404);
        }
        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $absolute = storage_path('app/public/'.$path);
        return response()->file($absolute); // open inline in browser
    }

    /**
     * Force download the stored image.
     */
    public function download(string $path)
    {
        $path = urldecode($path);

        if (! str_starts_with($path, 'tickets/') || str_contains($path, '..')) {
            abort(404);
        }
        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->download($path);
    }
}
