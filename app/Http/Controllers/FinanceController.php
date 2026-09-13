<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashTransaction;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    // Halaman Arus Kas (Input Kas Masuk/Keluar)
    public function cashIndex()
    {
        $branchId = auth()->check() ? (auth()->user()->branch_id ?? 1) : 1;
        
        $transactions = CashTransaction::with('user')
                            ->where('branch_id', $branchId)
                            ->latest()->paginate(15);
        
        $totalIn = CashTransaction::where('branch_id', $branchId)->where('type', 'in')->sum('amount');
        $totalOut = CashTransaction::where('branch_id', $branchId)->where('type', 'out')->sum('amount');
        $balance = $totalIn - $totalOut;

        return view('pages.finance.cash', compact('transactions', 'totalIn', 'totalOut', 'balance'));
    }

    public function cashStore(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        CashTransaction::create([
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'user_id' => Auth::id() ?? 1,
            'branch_id' => auth()->check() ? (auth()->user()->branch_id ?? 1) : 1,
        ]);

        return back()->with('success', 'Transaksi kas berhasil dicatat!');
    }

    // Laporan Laba Rugi (Profit & Loss)
    public function profitLoss(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $branchId = auth()->check() ? (auth()->user()->branch_id ?? 1) : 1;

        // 1. PENDAPATAN
        $totalSales = Sale::where('branch_id', $branchId)
                          ->whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');

        // 2. HPP
        $cogs = SaleItem::whereHas('sale', function($q) use ($startDate, $endDate, $branchId) {
            $q->where('branch_id', $branchId)->whereBetween('created_at', [$startDate, $endDate]);
        })->join('products', 'sale_items.product_id', '=', 'products.id')
          ->selectRaw('SUM(sale_items.quantity * products.cost_price) as total_cogs')
          ->value('total_cogs') ?? 0;

        // 3. Laba Kotor (Gross Profit)
        $grossProfit = $totalSales - $cogs;

        // 4. Biaya Operasional (Kas Keluar)
        $operatingExpenses = CashTransaction::where('branch_id', $branchId)
                                            ->where('type', 'out')
                                            ->whereBetween('date', [$startDate, $endDate])
                                            ->sum('amount');

        // 5. Pendapatan Lain-lain (Kas Masuk manual)
        $otherIncome = CashTransaction::where('branch_id', $branchId)
                                      ->where('type', 'in')
                                      ->whereBetween('date', [$startDate, $endDate])
                                      ->sum('amount');

        // 6. Laba Bersih (Net Profit)
        $netProfit = $grossProfit - $operatingExpenses + $otherIncome;

        // Margin dalam %
        $margin = $totalSales > 0 ? round(($netProfit / $totalSales) * 100, 2) : 0;

        return view('pages.finance.profit_loss', compact(
            'month', 'totalSales', 'cogs', 'grossProfit', 'operatingExpenses', 'otherIncome', 'netProfit', 'margin'
        ));
    }
}
