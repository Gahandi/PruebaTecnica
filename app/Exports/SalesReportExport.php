<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        return Order::where('status', 'completed')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with(['event', 'user'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Orden',
            'Evento',
            'Usuario',
            'Email',
            'Total',
            'Estado',
            'Fecha',
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->event->name ?? 'N/A',
            $order->user->name ?? 'N/A',
            $order->user->email ?? 'N/A',
            '$' . number_format($order->total, 2),
            ucfirst($order->status),
            $order->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
