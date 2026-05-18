<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::forUser(auth()->id())
            ->withCount('transactions')
            ->get();

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'color' => ['required', 'string', 'max:7'],
            'icon'  => ['nullable', 'string', 'max:10'],
        ]);

        // Check max 10 custom categories
        $customCount = Category::where('user_id', auth()->id())->count();
        if ($customCount >= 10) {
            return back()->withErrors(['name' => 'Maksimal 10 kategori custom.']);
        }

        Category::create([
            'user_id'    => auth()->id(),
            'name'       => $request->name,
            'color'      => $request->color,
            'icon'       => $request->icon ?? '📌',
            'is_default' => false,
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category)
    {
        abort_if($category->is_default, 403, 'Kategori default tidak bisa diubah.');
        abort_if($category->user_id !== auth()->id(), 403);

        $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'color' => ['required', 'string', 'max:7'],
        ]);

        $category->update($request->only(['name', 'color']));

        return back()->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        abort_if($category->is_default, 403, 'Kategori default tidak bisa dihapus.');
        abort_if($category->user_id !== auth()->id(), 403);

        $transactionCount = $category->transactions()->count();

        if ($transactionCount > 0) {
            return back()->withErrors([
                'category' => "Kategori ini digunakan oleh {$transactionCount} transaksi. Pindahkan transaksi ke kategori lain terlebih dahulu.",
            ]);
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus!');
    }
}
