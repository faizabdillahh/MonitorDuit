<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;

class StatisticsService
{
    public function getMonthlySummary(User $user, int $year, int $month): array
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $transactions = Transaction::query()
            ->where('user_id', $user->id)
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->with('category')
            ->get();

        $totalAmount   = $transactions->sum('total_amount');
        $totalCount    = $transactions->count();
        $daysInMonth   = $startOfMonth->daysInMonth;
        
        $now = now();
        if ($year === $now->year && $month === $now->month) {
            $daysToDivideBy = max($now->day, 1);
        } else {
            $daysToDivideBy = $daysInMonth;
        }
        
        $dailyAverage  = $daysToDivideBy > 0 ? $totalAmount / $daysToDivideBy : 0;

        // Group transactions by formatted date string
        $transactionsByDate = $transactions->groupBy(function($t) {
            return $t->transaction_date->format('Y-m-d');
        });

        // Daily totals for chart
        $dailyTotals = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startOfMonth->copy()->day($day)->format('Y-m-d');
            $dailyTotals[] = [
                'date'   => $date,
                'day'    => $day,
                'amount' => isset($transactionsByDate[$date]) ? $transactionsByDate[$date]->sum('total_amount') : 0,
            ];
        }

        // Category breakdown for donut chart
        $categoryBreakdown = $transactions->groupBy('category_id')->map(function ($group) {
            $category = $group->first()->category;
            return [
                'category_id'   => $category->id,
                'category_name' => $category->name,
                'category_icon' => $category->icon,
                'category_color'=> $category->color,
                'total_amount'  => $group->sum('total_amount'),
                'count'         => $group->count(),
            ];
        })->sortByDesc('total_amount')->values()->toArray();

        // Top category
        $topCategory = !empty($categoryBreakdown) ? $categoryBreakdown[0]['category_name'] : null;

        // Previous month comparison
        $prevMonth     = $startOfMonth->copy()->subMonth();
        $prevTotal     = Transaction::query()
            ->where('user_id', $user->id)
            ->whereBetween('transaction_date', [$prevMonth->startOfMonth(), $prevMonth->endOfMonth()])
            ->sum('total_amount');

        $changePercent = $prevTotal > 0 ? round((($totalAmount - $prevTotal) / $prevTotal) * 100, 1) : null;

        return [
            'year'               => $year,
            'month'              => $month,
            'month_name'         => $startOfMonth->translatedFormat('F Y'),
            'total_amount'       => round($totalAmount, 2),
            'total_count'        => $totalCount,
            'daily_average'      => round($dailyAverage, 2),
            'top_category'       => $topCategory,
            'daily_totals'       => $dailyTotals,
            'category_breakdown' => $categoryBreakdown,
            'prev_month_total'   => round($prevTotal, 2),
            'change_percent'     => $changePercent,
        ];
    }

    public function getWeeklyTrend(User $user): array
    {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $total = Transaction::query()
                ->where('user_id', $user->id)
                ->whereDate('transaction_date', $date)
                ->sum('total_amount');

            $days[] = [
                'date'   => $date,
                'label'  => now()->subDays($i)->translatedFormat('D'),
                'amount' => round($total, 2),
            ];
        }

        return $days;
    }

    public function getDashboardSummary(User $user): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth   = $now->copy()->endOfMonth();

        $monthlyTotal = Transaction::query()
            ->where('user_id', $user->id)
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        $monthlyCount = Transaction::query()
            ->where('user_id', $user->id)
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->count();

        $daysElapsed  = max($now->day, 1);
        $dailyAverage = $monthlyTotal / $daysElapsed;

        return [
            'monthly_total'   => round($monthlyTotal, 2),
            'monthly_count'   => $monthlyCount,
            'daily_average'   => round($dailyAverage, 2),
        ];
    }
}
