<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatisticsController extends Controller
{
    public function index(Request $request, StatisticsService $service)
    {
        $year  = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $summary = $service->getMonthlySummary(auth()->user(), (int) $year, (int) $month);

        return Inertia::render('Statistics', [
            'summary'      => $summary,
            'currentYear'  => (int) $year,
            'currentMonth' => (int) $month,
        ]);
    }
}
