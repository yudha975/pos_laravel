<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'user'])->latest()->paginate(15);
        return view('pages.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        // Hanya muat produk yang aktif, ambil kolom ID, Nama, SKU, dan HPP
        $products = Product::where('is_active', true)->get(['id', 'name', 'sku', 'cost_price']);
        
        return view('pages.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:purchases,invoice_number|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_method' => 'required|in:cash,transfer,kredit',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            
            // Hitung total dari items
            foreach ($validated['items'] as $item) {
                $totalAmount += ($item['quantity'] * $item['cost_price']);
            }

            // Tentukan status pembayaran
            $paymentStatus = 'belum_bayar';
            if ($validated['paid_amount'] >= $totalAmount) {
                $paymentStatus = 'lunas';
                $validated['paid_amount'] = $totalAmount; // Jangan lebih dari total
            } elseif ($validated['paid_amount'] > 0) {
                $paymentStatus = 'sebagian';
            }

            $branchId = auth()->check() ? (auth()->user()->branch_id ?? 1) : 1;

            // Create Purchase Header
            $purchase = Purchase::create([
                'invoice_number' => $validated['invoice_number'],
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id() ?? 1,
                'branch_id' => $branchId,
                'total_amount' => $totalAmount,
                'paid_amount' => $validated['paid_amount'],
                'payment_status' => $paymentStatus,
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null
            ]);

            // Create Items & Update Stock + HPP
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                $subtotal = $item['quantity'] * $item['cost_price'];

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'subtotal' => $subtotal
                ]);

                // Tambah stok ke cabang & Update Harga Modal (HPP)
                $pivot = $product->branches()->where('branch_id', $branchId)->first();
                if ($pivot) {
                    $product->branches()->updateExistingPivot($branchId, ['stock' => $pivot->pivot->stock + $item['quantity']]);
                } else {
                    $product->branches()->attach($branchId, ['stock' => $item['quantity']]);
                }

                $product->cost_price = $item['cost_price']; // Update HPP dengan harga beli terbaru
                $product->save();

                // Catat di Kartu Stok
                StockTransaction::create([
                    'product_id' => $product->id,
                    'branch_id' => $branchId,
                    'type' => 'purchase',
                    'quantity' => $item['quantity'],
                    'reference' => $validated['invoice_number'],
                    'notes' => 'Pembelian dari Supplier',
                    'user_id' => Auth::id() ?? 1
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pembelian berhasil disimpan.',
                'redirect_url' => route('purchases.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 422);
        }
    }

    public function updatePayment(Request $request, Purchase $purchase)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $newPaidAmount = $purchase->paid_amount + $request->amount;
        
        // Cek apakah sudah lunas
        if ($newPaidAmount >= $purchase->total_amount) {
            $purchase->paid_amount = $purchase->total_amount; // Cap di total
            $purchase->payment_status = 'lunas';
        } else {
            $purchase->paid_amount = $newPaidAmount;
            $purchase->payment_status = 'sebagian';
        }

        $purchase->save();

        return back()->with('success', 'Pembayaran hutang berhasil diperbarui!');
    }
}
