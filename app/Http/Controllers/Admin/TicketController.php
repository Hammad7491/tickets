<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::latest()->paginate(12);
        return view('admin.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('admin.tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:120'],
            'code'     => ['nullable','digits:4','unique:tickets,code'], // auto-generate if null
            'price'    => ['required','numeric','min:0'],
            'notes'    => ['nullable','string','max:2000'],
        ]);

        if (blank($data['code'] ?? null)) {
            $data['code'] = Ticket::generateUniqueCode();
        }

        // force quantity = 1 always
        $data['quantity'] = 1;

        Ticket::create($data);

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket created.');
    }

    public function show(Ticket $ticket)
    {
        return view('admin.tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        return view('admin.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:120'],
            'code'     => ['required','digits:4','unique:tickets,code,' . $ticket->id],
            'price'    => ['required','numeric','min:0'],
            'notes'    => ['nullable','string','max:2000'],
        ]);

        // keep it 1
        $data['quantity'] = 1;

        $ticket->update($data);

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket updated.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin.tickets.index')->with('success', 'Ticket deleted.');
    }
}
