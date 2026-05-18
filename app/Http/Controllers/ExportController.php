<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function download(Request $request, ExportService $service)
    {
        $filters = $request->only(['date_from', 'date_to', 'category']);
        $format  = $request->get('format', 'xlsx');

        $data = $service->getExportData(auth()->user(), $filters);

        $dateRange = ($filters['date_from'] ?? 'all') . '_' . ($filters['date_to'] ?? now()->format('Y-m-d'));
        $filename  = "monitorduit_{$dateRange}";

        if ($format === 'csv') {
            return Excel::download(new TransactionsExport($data), "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new TransactionsExport($data), "{$filename}.xlsx");
    }
}
