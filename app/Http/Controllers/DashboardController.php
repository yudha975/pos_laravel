<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Total Omset Penjualan Hari Ini
        $todaySales = Sale::whereDate('created_at', $today)->sum('total_amount');
        
        // Total Pengeluaran (Pembelian) Hari Ini
        $todayPurchases = Purchase::whereDate('created_at', $today)->sum('total_amount');
        
        // Total Hutang Pelanggan (Piutang)
        $totalReceivables = Sale::whereColumn('paid_amount', '<', 'total_amount')
                                ->selectRaw('SUM(total_amount - paid_amount) as total')
                                ->value('total') ?? 0;

        // Stok Menipis (Kurang dari atau sama dengan Minimum Stok)
        $branchId = auth()->check() ? (auth()->user()->branch_id ?? 1) : 1;
        $lowStockProducts = Product::where('is_active', true)
            ->whereHas('branches', function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->whereColumn('product_branches.stock', '<=', 'products.min_stock');
            })
            ->with(['branches' => function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }])
            ->take(10)
            ->get();

        // 5 Transaksi Penjualan Terakhir
        $recentSales = Sale::with('customer')->latest()->take(5)->get();

        return view('dashboard', compact(
            'todaySales', 
            'todayPurchases', 
            'totalReceivables', 
            'lowStockProducts', 
            'recentSales'
        ));
    }
}
