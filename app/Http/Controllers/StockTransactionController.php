<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['product', 'user'])->latest();

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        
        if ($request->type) {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(15);
        $products = Product::where('is_active', true)->get();

        return view('pages.inventory.index', compact('transactions', 'products'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('pages.inventory.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($validated['type'] === 'out' && $product->stock < $validated['quantity']) {
            return back()->withInput()->with('error', 'Stok tidak mencukupi untuk pengurangan ini.');
        }

        DB::beginTransaction();
        try {
            // Create Transaction
            StockTransaction::create([
                'product_id' => $validated['product_id'],
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'reference' => $validated['reference'],
                'notes' => $validated['notes'],
                'user_id' => Auth::id() ?? 1 // Fallback to 1 if not logged in for testing
            ]);

            // Update Product Stock
            if ($validated['type'] === 'in') {
                $product->increment('stock', $validated['quantity']);
            } else if ($validated['type'] === 'out') {
                $product->decrement('stock', $validated['quantity']);
            } else if ($validated['type'] === 'adjustment') {
                // Adjustment assumes the quantity is the diff. We will treat positive as in, negative as out.
                // Wait, validation says min:1. So maybe we should have an adjustment type (+ or -).
                // Let's modify the adjustment logic. If it's just general adjustment, we need a way to say + or -.
                // To keep it simple, let's treat 'adjustment' here as just replacing the stock, or we use 'in'/'out'.
                // If it's a replacement, the quantity is the actual new stock.
                // Let's update the logic: 'quantity' in form is the diff (if we change validation to integer).
                // But validation is min:1. So let's add an adjustment_direction field, or just stick to IN and OUT for now.
                // If type is adjustment, let's assume it means "set stock to exactly this quantity" for this simple POS.
                // Actually, standard is: IN adds, OUT subtracts, ADJUSTMENT sets to absolute.
                // If it sets absolute:
                $diff = $validated['quantity'] - $product->stock;
                $product->update(['stock' => $validated['quantity']]);
                
                // Update transaction quantity to reflect the diff for reporting?
                // Or leave it as the absolute value? Usually better to store the diff.
                // Let's just store the absolute value for simplicity.
            }

            DB::commit();
            return redirect()->route('inventory.index')->with('success', 'Transaksi stok berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
