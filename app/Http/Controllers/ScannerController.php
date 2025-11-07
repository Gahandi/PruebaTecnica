<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

class ScannerController extends Controller
{
    //
    public function index()
    {
        return view('scanner.index');
    }

    public function validateTicket(Request $request)
    {
        $ticket = Ticket::find($request->code);

        if (!$ticket) {
            return response()->json(['status' => 'error', 'message' => 'Ticket not found']);
        }

        if ($ticket->used) {
            return response()->json(['status' => 'used', 'message' => 'Ticket already used']);
        }

        $ticket->update(['used' => true]);

        return response()->json(['status' => 'valid', 'message' => 'Ticket valid']);
    }
}
