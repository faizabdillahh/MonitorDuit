<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(
        private Collection $data
    ) {}

    public function collection(): Collection
    {
        return $this->data;
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-',
            $transaction->merchant_name ?? '-',
            $transaction->category ? $transaction->category->name : 'Tanpa Kategori',
            (float) $transaction->total_amount,
            $transaction->notes ?? '-',
            ucfirst(strtolower($transaction->source?->value ?? 'Manual')),
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Merchant / Toko',
            'Kategori',
            'Total Pengeluaran (Rp)',
            'Catatan',
            'Metode Input'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk Header (Baris 1)
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color'    => ['argb' => 'FF10B981'], // Warna Emerald-500
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color'       => ['argb' => 'FF059669'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Wrap text untuk baris catatan agar tidak melebar terlalu jauh
        $sheet->getStyle('E')->getAlignment()->setWrapText(true);

        // Alignment rata tengah untuk kolom tertentu
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function columnFormats(): array
    {
        return [
            // Format angka ribuan dengan pemisah koma (atau titik tergantung regional excel user)
            'D' => '#,##0',
        ];
    }
}
