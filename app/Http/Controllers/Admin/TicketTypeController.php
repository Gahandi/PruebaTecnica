<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class TicketTypeController extends Controller
{
    public function index()
    {
        $ticketTypes = TicketType::withCount('tickets')->get();
        return view('admin.ticket_types.index', compact('ticketTypes'));
    }

    public function create()
    {
        return view('admin.ticket_types.create');
    }

    public function edit(TicketType $ticketType)
    {
        return view('admin.ticket_types.edit', compact('ticketType'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $ticketType = TicketType::create($validated);

        ActivityLog::log('created', 'Created Ticket Type: ' . $ticketType->name);

        return redirect()->route('admin.ticket_types.index')->with('success', 'Tipo de boleto creado correctamente.');
    }

    public function update(Request $request, TicketType $ticketType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $ticketType->update($validated);

        ActivityLog::log('updated', 'Updated Ticket Type: ' . $ticketType->name);

        return redirect()->route('admin.ticket_types.index')->with('success', 'Tipo de boleto actualizado correctamente.');
    }

    public function destroy(TicketType $ticketType)
    {
        $name = $ticketType->name;
        $ticketType->delete();

        ActivityLog::log('deleted', 'Deleted Ticket Type: ' . $name);

        return redirect()->route('admin.ticket_types.index')->with('success', 'Tipo de boleto eliminado correctamente.');
    }
}
