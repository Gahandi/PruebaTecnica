<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class TicketPurchaseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $tickets;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->load(['tickets.ticketType', 'tickets.eventTicket', 'user', 'payments']);
        $this->tickets = $this->order->tickets;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $customerName = $this->order->user ? $this->order->user->name : 'Cliente';
        $eventName = $this->tickets->first()?->eventTicket?->name ?? 'Evento';
        
        return new Envelope(
            subject: "Tus boletos para {$eventName} - Orden #{$this->order->id}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-purchase',
            with: [
                'order' => $this->order,
                'tickets' => $this->tickets,
                'customerName' => $this->order->user ? $this->order->user->name : 'Cliente',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        // Adjuntar PDF de cada ticket
        foreach ($this->tickets as $ticket) {
            $pdfPath = storage_path("app/tickets_pdf/ticket_{$ticket->id}.pdf");
            if (file_exists($pdfPath)) {
                $attachments[] = Attachment::fromPath($pdfPath)
                    ->as("boleto_{$ticket->id}.pdf")
                    ->withMime('application/pdf');
            }
        }
        
        return $attachments;
    }
}

