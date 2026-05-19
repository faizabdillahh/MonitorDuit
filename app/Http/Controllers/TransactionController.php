<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\CurrencyService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private CurrencyService $currencyService,
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $filters = $request->only(['category', 'search', 'date_from', 'date_to', 'amount_min', 'amount_max']);

        $transactions = $this->transactionService->getFilteredTransactions($user, $filters);
        $categories   = Category::forUser($user->id)->get();

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'categories'   => $categories,
            'filters'      => $filters,
        ]);
    }

    public function create()
    {
        $categories = Category::forUser(auth()->id())->get();

        return Inertia::render('Transactions/Create', [
            'categories'          => $categories,
            'supportedCurrencies' => $this->currencyService->getSupportedCurrencies(),
            'defaultCurrency'     => auth()->user()->default_currency ?? 'IDR',
        ]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();

        // Handle receipt image upload
        if ($request->hasFile('receipt_image')) {
            $data['receipt_image_path'] = $request->file('receipt_image')
                ->store('receipts/' . auth()->id(), 'local');
        }

        $this->transactionService->store(auth()->user(), $data);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil disimpan!');
    }

    public function show(Transaction $transaction)
    {
        $this->authorizeUser($transaction);

        $transaction->load('category');

        return Inertia::render('Transactions/Show', [
            'transaction' => $transaction,
            'categories'  => Category::forUser(auth()->id())->get(),
            'receiptUrl'  => $transaction->receipt_image_path
                ? route('receipts.image', $transaction->id)
                : null,
        ]);
    }

    public function edit(Transaction $transaction)
    {
        $this->authorizeUser($transaction);
        $transaction->load('category');

        return Inertia::render('Transactions/Show', [
            'transaction' => $transaction,
            'categories'  => Category::forUser(auth()->id())->get(),
            'editing'     => true,
            'receiptUrl'  => $transaction->receipt_image_path
                ? route('receipts.image', $transaction->id)
                : null,
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $this->authorizeUser($transaction);

        $this->transactionService->update($transaction, $request->validated());

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeUser($transaction);

        $this->transactionService->destroy($transaction);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }

    private function authorizeUser(Transaction $transaction): void
    {
        abort_if($transaction->user_id !== auth()->id(), 403);
    }
}
