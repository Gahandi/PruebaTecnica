<?php

namespace App\Exports;

use App\Models\Checkin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CheckinsReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Checkin::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with(['ticket.order.event', 'ticket.order.user', 'user'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Ticket ID',
            'Evento',
            'Usuario',
            'Email',
            'Escaneado Por',
            'Fecha Check-in',
        ];
    }

    public function map($checkin): array
    {
        return [
            $checkin->id,
            $checkin->ticket_id,
            $checkin->ticket->order->event->name ?? 'N/A',
            $checkin->ticket->order->user->name ?? 'N/A',
            $checkin->ticket->order->user->email ?? 'N/A',
            $checkin->user->name ?? 'Sistema',
            $checkin->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
