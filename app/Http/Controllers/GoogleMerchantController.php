<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class GoogleMerchantController extends Controller
{
    /**
     * Genera el feed XML de Google Merchant para eventos
     */
    public function feed()
    {
        // Obtener todos los eventos activos con sus relaciones necesarias
        $events = Event::where('active', true)
            ->where('date', '>=', now())
            ->with(['space', 'ticketTypes', 'tags', 'type_event'])
            ->orderBy('date', 'asc')
            ->get();

        // Generar el XML
        $xml = view('feeds.google-merchant', compact('events'))->render();

        return Response::make($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Feed específico por espacio (subdomain)
     */
    public function feedBySpace($subdomain)
    {
        $events = Event::whereHas('space', function($query) use ($subdomain) {
                $query->where('subdomain', $subdomain);
            })
            ->where('active', true)
            ->where('date', '>=', now())
            ->with(['space', 'ticketTypes', 'tags', 'type_event'])
            ->orderBy('date', 'asc')
            ->get();

        $xml = view('feeds.google-merchant', compact('events'))->render();

        return Response::make($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
