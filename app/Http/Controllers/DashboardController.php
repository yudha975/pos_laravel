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
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
                                   ->where('is_active', true)
                                   ->orderBy('stock', 'asc')
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
