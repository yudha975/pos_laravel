<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $branchId = auth()->check() ? (auth()->user()->branch_id ?? 1) : 1;
        $isSuperadmin = auth()->check() && auth()->user()->hasRole('superadmin');

        $query = Sale::with(['customer', 'user'])
                     ->whereDate('created_at', '>=', $startDate)
                     ->whereDate('created_at', '<=', $endDate);

        // Superadmin lihat semua cabang, yang lain filter cabangnya
        if (!$isSuperadmin) {
            $query->where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        // Menghitung ringkasan
        $totalSales = (clone $query)->sum('total_amount');
        // Total uang yang benar-benar diterima (tidak boleh melebihi total transaksi per invoice)
        $totalPaid = (clone $query)->get()->sum(function($sale) {
            return min($sale->paid_amount, $sale->total_amount);
        });
        // Piutang hanya dari transaksi kredit yang belum/kurang bayar
        $totalReceivables = (clone $query)->get()->sum(function($sale) {
            $sisa = $sale->total_amount - $sale->paid_amount;
            return $sisa > 0 ? $sisa : 0;
        });

        $sales = $query->latest()->get();

        return view('pages.reports.sales', compact('sales', 'startDate', 'endDate', 'totalSales', 'totalPaid', 'totalReceivables'));
    }

    public function purchasesReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $branchId = auth()->check() ? (auth()->user()->branch_id ?? 1) : 1;
        $isSuperadmin = auth()->check() && auth()->user()->hasRole('superadmin');

        $query = Purchase::with(['supplier', 'user'])
                         ->whereDate('created_at', '>=', $startDate)
                         ->whereDate('created_at', '<=', $endDate);

        // Superadmin lihat semua, yang lain filter cabang (termasuk data lama null branch_id)
        if (!$isSuperadmin) {
            $query->where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        // Menghitung ringkasan
        $totalPurchases = (clone $query)->sum('total_amount');
        $totalPaid = (clone $query)->sum('paid_amount');
        $totalDebt = $totalPurchases - $totalPaid;

        $purchases = $query->latest()->get();

        return view('pages.reports.purchases', compact('purchases', 'startDate', 'endDate', 'totalPurchases', 'totalPaid', 'totalDebt'));
    }

    public function stockReport()
    {
        // Ambil semua produk aktif
        $products = Product::where('is_active', true)->orderBy('name', 'asc')->get();

        // Hitung Total Nilai Aset (Stok * Harga Modal)
        $totalAssetValue = 0;
        foreach ($products as $product) {
            $totalAssetValue += ($product->stock * $product->cost_price);
        }

        return view('pages.reports.stocks', compact('products', 'totalAssetValue'));
    }
}
