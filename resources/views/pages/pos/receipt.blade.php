<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .receipt-container {
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .border-b { border-bottom: 1px dashed #000; padding-bottom: 8px; margin-bottom: 8px; }
        .border-t { border-top: 1px dashed #000; padding-top: 8px; margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 0; vertical-align: top; }
        .item-name { max-width: 150px; }
        
        @media print {
            body { background: none; }
            .receipt-container { width: 100%; border: none; padding: 0; margin: 0; }
            @page { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt-container">
        <!-- Header -->
        <div class="text-center border-b">
            <h2 class="font-bold mb-2" style="font-size: 16px; margin-top:0;">TOKO POS LITE</h2>
            <p style="margin: 0;">Jl. Contoh Alamat No. 123</p>
            <p style="margin: 0;">Telp: 08123456789</p>
        </div>

        <!-- Meta -->
        <div class="border-b" style="display: flex; justify-content: space-between;">
            <div>
                <div>INV: {{ $sale->invoice_number }}</div>
                <div>Ksr: {{ $sale->user->name ?? 'Admin' }}</div>
            </div>
            <div class="text-right">
                <div>{{ $sale->created_at->format('d/m/Y') }}</div>
                <div>{{ $sale->created_at->format('H:i') }}</div>
            </div>
        </div>
        @if($sale->customer)
        <div class="border-b">
            Pelanggan: {{ $sale->customer->name }}
        </div>
        @endif

        <!-- Items -->
        <div class="mb-4">
            <table>
                @foreach($sale->items as $item)
                <tr>
                    <td class="item-name">
                        {{ $item->product_name }}<br>
                        {{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}
                    </td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <!-- Summary -->
        <div class="border-t">
            <table style="width: 100%;">
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">{{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($sale->discount > 0)
                <tr>
                    <td>Diskon</td>
                    <td class="text-right">-{{ number_format($sale->discount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr>
                    <td class="font-bold" style="font-size: 14px;">TOTAL</td>
                    <td class="text-right font-bold" style="font-size: 14px;">{{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Metode ({{ strtoupper($sale->payment_method) }})</td>
                    <td class="text-right">{{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                </tr>
                @if($sale->payment_method == 'cash')
                <tr>
                    <td>Kembali</td>
                    <td class="text-right">{{ number_format($sale->return_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 border-t pt-4">
            <p style="margin: 0;">Terima kasih atas kunjungan Anda!</p>
            <p style="margin: 0;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
        </div>
    </div>
</body>
</html>
