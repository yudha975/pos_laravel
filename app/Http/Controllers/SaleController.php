<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        // Load sales with customer and user, order by latest
        $sales = Sale::with(['customer', 'user'])->latest()->paginate(15);
        
        return view('pages.sales.index', compact('sales'));
    }

    public function updatePayment(Request $request, Sale $sale)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $newPaidAmount = $sale->paid_amount + $request->amount;
        
        // Cek apakah sudah lunas
        if ($newPaidAmount >= $sale->total_amount) {
            $sale->paid_amount = $sale->total_amount;
            $sale->return_amount = $newPaidAmount - $sale->total_amount; // Kembalian (opsional jika dilunasi lebih)
        } else {
            $sale->paid_amount = $newPaidAmount;
        }

        $sale->save();

        return back()->with('success', 'Pembayaran piutang pelanggan berhasil diperbarui!');
    }
}
