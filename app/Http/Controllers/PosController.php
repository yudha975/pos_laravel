<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->get();
        $customers = Customer::all();
        // Load products with basic info for the POS grid
        $products = Product::where('is_active', true)
                           ->where('stock', '>', 0)
                           ->get(['id', 'name', 'sku', 'barcode', 'category_id', 'retail_price', 'reseller_price', 'technician_price', 'wholesale_price', 'stock']);

        return view('pages.pos.index', compact('categories', 'customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,transfer,kredit,qris,e-wallet',
            'discount' => 'numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            
            // Verify stock and calculate subtotal
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok tidak cukup untuk produk: {$product->name}");
                }
                
                $subtotal += ($item['quantity'] * $item['price']);
            }

            $discount = $validated['discount'] ?? 0;
            $totalAmount = $subtotal - $discount;
            $returnAmount = $validated['paid_amount'] - $totalAmount;

            if ($validated['payment_method'] === 'cash' && $returnAmount < 0) {
                throw new \Exception("Jumlah uang yang dibayarkan kurang dari total belanja.");
            }

            // Generate Invoice Number (e.g. INV-YYYYMMDD-XXXX)
            $datePrefix = date('Ymd');
            $lastSale = Sale::where('invoice_number', 'like', "INV-{$datePrefix}-%")->orderBy('id', 'desc')->first();
            $sequence = $lastSale ? (intval(substr($lastSale->invoice_number, -4)) + 1) : 1;
            $invoiceNumber = "INV-{$datePrefix}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            $branchId = auth()->check() ? (auth()->user()->branch_id ?? 1) : 1;

            // Create Sale
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $validated['customer_id'],
                'user_id' => Auth::id() ?? 1,
                'branch_id' => $branchId,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'paid_amount' => $validated['paid_amount'],
                'return_amount' => max(0, $returnAmount),
                'notes' => $validated['notes'] ?? null
            ]);

            // Create Items & Update Stock
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                $itemSubtotal = $item['quantity'] * $item['price'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $itemSubtotal
                ]);

                // Deduct stock for current branch
                DB::table('product_branches')
                    ->where('product_id', $product->id)
                    ->where('branch_id', $branchId)
                    ->decrement('stock', $item['quantity']);

                // Create stock transaction history
                StockTransaction::create([
                    'product_id' => $product->id,
                    'branch_id' => $branchId,
                    'type' => 'sale',
                    'quantity' => $item['quantity'],
                    'reference' => $invoiceNumber,
                    'notes' => 'Penjualan POS',
                    'user_id' => Auth::id() ?? 1
                ]);
            }

            // Create Audit Log
            AuditLog::create([
                'user_id' => Auth::id() ?? 1,
                'branch_id' => $branchId,
                'action' => 'create',
                'model_type' => 'Sale',
                'model_id' => $sale->id,
                'details' => "Penjualan POS sejumlah Rp " . number_format($totalAmount, 0, ',', '.'),
                'ip_address' => request()->ip()
            ]);

            DB::commit();

            // Return success response with receipt URL
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil.',
                'receipt_url' => route('pos.receipt', $sale->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['items', 'customer', 'user']);
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        return view('pages.pos.receipt', compact('sale', 'settings'));
    }
}
