<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\BudgetService;
use App\Services\StatisticsService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(StatisticsService $statisticsService, TransactionService $transactionService, BudgetService $budgetService)
    {
        $user = auth()->user();

        $summary      = $statisticsService->getDashboardSummary($user);
        $weeklyTrend  = $statisticsService->getWeeklyTrend($user);
        $recent       = $transactionService->getRecentTransactions($user, 5);
        $categories   = Category::forUser($user->id)->get();

        // Budget widget: max 3 yang mendekati/melampaui budget
        $budgetWidgets = $budgetService->getBudgetsWithSpending($user, now()->month, now()->year)
            ->sortByDesc('percentage')
            ->take(3)
            ->values();

        return Inertia::render('Dashboard', [
            'summary'            => $summary,
            'weeklyTrend'        => $weeklyTrend,
            'recentTransactions' => $recent,
            'categories'         => $categories,
            'budgetWidgets'      => $budgetWidgets,
        ]);
    }
}
