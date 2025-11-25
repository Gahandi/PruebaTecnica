<?php

namespace App\Jobs;

use App\Models\Order;
use App\Mail\TicketPurchaseMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;

class SendTicketPurchaseEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Cargar relaciones necesarias
            $this->order->load(['tickets.ticketType', 'tickets.eventTicket', 'user', 'payments']);
            
            // Generar PDFs para cada ticket
            $this->generateTicketPDFs();
            
            // Obtener email del cliente
            $customerEmail = $this->order->user?->email;
            
            if (!$customerEmail) {
                // Si no hay usuario asociado, no podemos enviar el correo
                Log::warning('No se puede enviar correo: Orden sin usuario ni email', [
                    'order_id' => $this->order->id
                ]);
                return;
            }
            
            // Enviar correo
            Mail::to($customerEmail)->send(new TicketPurchaseMail($this->order));
            
            Log::info('Correo de compra enviado exitosamente', [
                'order_id' => $this->order->id,
                'email' => $customerEmail
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al enviar correo de compra', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-lanzar la excepción para que el job falle y pueda reintentarse
            throw $e;
        }
    }

    /**
     * Generar PDFs para cada ticket
     */
    private function generateTicketPDFs(): void
    {
        // Asegurar que el directorio existe
        $pdfDirectory = storage_path('app/tickets_pdf');
        if (!file_exists($pdfDirectory)) {
            mkdir($pdfDirectory, 0755, true);
        }
        
        foreach ($this->order->tickets as $ticket) {
            try {
                $ticket->load(['ticketType', 'eventTicket', 'order.user']);
                
                $event = $ticket->eventTicket;
                $ticketType = $ticket->ticketType;
                $order = $ticket->order;
                $user = $order->user;
                
                // Obtener QR como base64 para el PDF
                $qrBase64 = null;
                if ($ticket->qr_url) {
                    try {
                        $qrBinary = file_get_contents($ticket->qr_url);
                        $qrBase64 = 'data:image/png;base64,' . base64_encode($qrBinary);
                    } catch (\Exception $e) {
                        Log::warning('No se pudo obtener QR para PDF', [
                            'ticket_id' => $ticket->id,
                            'qr_url' => $ticket->qr_url
                        ]);
                    }
                }
                
                // Generar PDF
                $pdf = PDF::loadView('pdf.ticket', [
                    'ticket' => $ticket,
                    'event' => $event,
                    'ticketType' => $ticketType,
                    'order' => $order,
                    'user' => $user,
                    'qrUrl' => $qrBase64 ?: $ticket->qr_url,
                ]);
                
                // Guardar PDF
                $pdfPath = $pdfDirectory . "/ticket_{$ticket->id}.pdf";
                $pdf->save($pdfPath);
                
                Log::info('PDF de ticket generado', [
                    'ticket_id' => $ticket->id,
                    'path' => $pdfPath
                ]);
                
            } catch (\Exception $e) {
                Log::error('Error al generar PDF de ticket', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage()
                ]);
                // Continuar con el siguiente ticket aunque falle uno
            }
        }
    }
}

