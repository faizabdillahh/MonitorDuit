<?php

namespace App\Http\Controllers;

use App\Enums\RecurringFrequency;
use App\Models\Category;
use App\Models\RecurringTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RecurringTransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $recurrings = RecurringTransaction::where('user_id', $user->id)
            ->with('category')
            ->orderByDesc('is_active')
            ->orderBy('next_run_date')
            ->get();

        $categories = Category::forUser($user->id)->get();

        return Inertia::render('Recurring/Index', [
            'recurrings'  => $recurrings,
            'categories'  => $categories,
            'frequencies' => collect(RecurringFrequency::cases())->map(fn($f) => [
                'value' => $f->value,
                'label' => $f->label(),
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'merchant_name'=> ['nullable', 'string', 'max:255'],
            'amount'       => ['required', 'numeric', 'min:1'],
            'frequency'    => ['required', 'in:weekly,biweekly,monthly,yearly'],
            'start_date'   => ['required', 'date'],
            'end_date'     => ['nullable', 'date', 'after:start_date'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        $data['user_id']       = auth()->id();
        $data['next_run_date'] = $data['start_date'];
        $data['is_active']     = true;

        RecurringTransaction::create($data);

        // Process immediately if the start_date was in the past
        app(\App\Services\RecurringTransactionService::class)->processUserDue(auth()->user());

        return back()->with('success', 'Recurring transaction berhasil ditambahkan!');
    }

    public function update(Request $request, RecurringTransaction $recurring)
    {
        abort_if($recurring->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'merchant_name'=> ['nullable', 'string', 'max:255'],
            'amount'       => ['required', 'numeric', 'min:1'],
            'frequency'    => ['required', 'in:weekly,biweekly,monthly,yearly'],
            'end_date'     => ['nullable', 'date'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        $recurring->update($data);

        // Process immediately if it falls behind
        app(\App\Services\RecurringTransactionService::class)->processUserDue(auth()->user());

        return back()->with('success', 'Recurring transaction berhasil diperbarui!');
    }

    public function toggle(RecurringTransaction $recurring)
    {
        abort_if($recurring->user_id !== auth()->id(), 403);

        $recurring->update(['is_active' => !$recurring->is_active]);

        if ($recurring->is_active) {
            app(\App\Services\RecurringTransactionService::class)->processUserDue(auth()->user());
        }

        return back()->with('success', $recurring->is_active ? 'Diaktifkan' : 'Dinonaktifkan');
    }

    public function destroy(RecurringTransaction $recurring)
    {
        abort_if($recurring->user_id !== auth()->id(), 403);

        $recurring->delete();

        return back()->with('success', 'Recurring transaction berhasil dihapus!');
    }
}
