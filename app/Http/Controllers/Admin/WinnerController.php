<?php
// app/Http/Controllers/Admin/WinnerController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WinnerController extends Controller
{
 public function index()
{
    // Oldest first so the first person added is "Winner 1"
    $winners = \App\Models\Winner::orderBy('created_at', 'asc')->paginate(12);

    // If you already pass $isAdminView elsewhere, keep it;
    // otherwise we’ll infer it in the blade.
    return view('admin.winner.index', compact('winners'));
}

    public function create()
    {
        $winner = new Winner();
        $isEdit = false;
        return view('admin.winner.create', compact('winner', 'isEdit'));
    }

    public function edit(Winner $winner)
    {
        $isEdit = true;
        return view('admin.winner.create', compact('winner', 'isEdit'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:120'],
            'serial_number' => ['required', 'string', 'max:60', 'unique:winners,serial_number'],
            'price'         => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        Winner::create($data);

        return redirect()
            ->route('admin.winners.index')
            ->with('success', 'Winner created.');
    }

    public function update(Request $request, Winner $winner)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:120'],
            'serial_number' => [
                'required', 'string', 'max:60',
                Rule::unique('winners', 'serial_number')->ignore($winner->id),
            ],
            'price'         => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $winner->update($data);

        return redirect()
            ->route('admin.winners.index')
            ->with('success', 'Winner updated.');
    }

    public function destroy(Winner $winner)
    {
        $winner->delete();

        return back()->with('success', 'Winner deleted.');
    }
}
