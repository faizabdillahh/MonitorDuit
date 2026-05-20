<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Services\BudgetService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BudgetController extends Controller
{
    public function __construct(
        private readonly BudgetService $budgetService
    ) {}

    public function index(Request $request)
    {
        $user  = $request->user();
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        $budgets = $this->budgetService->getBudgetsWithSpending($user, $month, $year);

        $categories = \App\Models\Category::forUser($user->id)->get();

        return Inertia::render('Budgets/Index', [
            'budgets'    => $budgets->values(),
            'categories' => $categories,
            'filters'    => [
                'month' => $month,
                'year'  => $year,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'name'         => ['nullable', 'string', 'max:100'],
            'amount'       => ['required', 'numeric', 'min:1', 'max:9999999999999'],
            'is_recurring' => ['boolean'],
            'month'        => ['nullable', 'integer', 'between:1,12'],
            'year'         => ['nullable', 'integer', 'min:2024'],
        ], [
            'amount.max' => 'Batas budget maksimal Rp 9.999.999.999.999.',
        ]);

        $this->budgetService->createBudget($request->user(), $request->all());

        return back()->with('success', 'Budget berhasil disimpan!');
    }

    public function update(Request $request, Budget $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);

        $request->validate([
            'name'         => ['nullable', 'string', 'max:100'],
            'amount'       => ['required', 'numeric', 'min:1', 'max:9999999999999'],
            'is_recurring' => ['boolean'],
            'month'        => ['nullable', 'integer', 'between:1,12'],
            'year'         => ['nullable', 'integer', 'min:2024'],
        ], [
            'amount.max' => 'Batas budget maksimal Rp 9.999.999.999.999.',
        ]);

        $data = $request->only(['name', 'amount', 'is_recurring', 'month', 'year']);

        if ($data['is_recurring'] ?? $budget->is_recurring) {
            $data['month'] = null;
            $data['year']  = null;
        }

        $budget->update($data);

        return back()->with('success', 'Budget berhasil diperbarui!');
    }

    public function destroy(Budget $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);

        $budget->delete();

        return back()->with('success', 'Budget berhasil dihapus!');
    }
}
