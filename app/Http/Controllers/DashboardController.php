<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\StatisticsService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(StatisticsService $statisticsService, TransactionService $transactionService)
    {
        $user = auth()->user();

        $summary      = $statisticsService->getDashboardSummary($user);
        $weeklyTrend  = $statisticsService->getWeeklyTrend($user);
        $recent       = $transactionService->getRecentTransactions($user, 5);
        $categories   = Category::forUser($user->id)->get();

        return Inertia::render('Dashboard', [
            'summary'            => $summary,
            'weeklyTrend'        => $weeklyTrend,
            'recentTransactions' => $recent,
            'categories'         => $categories,
        ]);
    }
}
