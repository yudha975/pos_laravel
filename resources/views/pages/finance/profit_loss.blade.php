<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Laba Rugi (Profit & Loss)') }}
        </h2>
    </x-slot>

    <div class="mt-6 max-w-4xl mx-auto">
        
        <!-- Filter Bulan -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-6 flex items-center justify-between">
            <div>
                <h3 class="font-medium text-gray-700">Periode Laporan</h3>
                <p class="text-sm text-gray-500">Tampilkan laporan berdasarkan bulan.</p>
            </div>
            <form method="GET" action="{{ route('finance.profit_loss') }}" class="flex items-center space-x-2">
                <input type="month" name="month" value="{{ $month }}" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-700 transition-colors">Filter</button>
            </form>
        </div>

        <!-- Kertas Laporan -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-blue-600 p-6 text-center text-white">
                <h1 class="text-2xl font-bold uppercase tracking-wider">Laporan Laba Rugi</h1>
                <p class="text-blue-100 mt-1">Periode: {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</p>
            </div>

            <div class="p-8">
                <!-- Pendapatan -->
                <div class="mb-6">
                    <h4 class="text-lg font-bold text-gray-800 border-b-2 border-gray-100 pb-2 mb-4">1. PENDAPATAN</h4>
                    <div class="flex justify-between items-center text-gray-700 mb-2">
                        <span>Total Penjualan (Kotor)</span>
                        <span class="font-medium">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- HPP -->
                <div class="mb-6">
                    <h4 class="text-lg font-bold text-gray-800 border-b-2 border-gray-100 pb-2 mb-4">2. HARGA POKOK PENJUALAN (HPP)</h4>
                    <div class="flex justify-between items-center text-gray-700 mb-2">
                        <span>Total Modal Barang Terjual</span>
                        <span class="font-medium text-red-600">( Rp {{ number_format($cogs, 0, ',', '.') }} )</span>
                    </div>
                </div>

                <!-- Laba Kotor -->
                <div class="bg-gray-50 p-4 rounded-lg mb-8 border border-gray-200 flex justify-between items-center">
                    <span class="font-bold text-gray-900 text-lg">LABA KOTOR (Gross Profit)</span>
                    <span class="font-bold text-blue-600 text-xl">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                </div>

                <!-- Biaya Operasional -->
                <div class="mb-6">
                    <h4 class="text-lg font-bold text-gray-800 border-b-2 border-gray-100 pb-2 mb-4">3. BIAYA OPERASIONAL & PENGELUARAN KAS</h4>
                    <div class="flex justify-between items-center text-gray-700 mb-2">
                        <span>Total Pengeluaran Kas Manual</span>
                        <span class="font-medium text-red-600">( Rp {{ number_format($operatingExpenses, 0, ',', '.') }} )</span>
                    </div>
                </div>

                <!-- Pendapatan Lain -->
                <div class="mb-6">
                    <h4 class="text-lg font-bold text-gray-800 border-b-2 border-gray-100 pb-2 mb-4">4. PENDAPATAN LAIN-LAIN</h4>
                    <div class="flex justify-between items-center text-gray-700 mb-2">
                        <span>Total Pemasukan Kas Manual</span>
                        <span class="font-medium text-green-600">Rp {{ number_format($otherIncome, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Laba Bersih -->
                <div class="{{ $netProfit >= 0 ? 'bg-green-600' : 'bg-red-600' }} p-6 rounded-xl shadow-inner flex flex-col sm:flex-row justify-between items-center text-white mt-8">
                    <div>
                        <span class="block font-bold text-xl mb-1">LABA BERSIH (Net Profit)</span>
                        <span class="text-sm opacity-90">Margin Keuntungan: {{ $margin }}%</span>
                    </div>
                    <div class="text-3xl font-black mt-4 sm:mt-0">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </div>
                </div>
                
                @if($netProfit < 0)
                    <p class="text-center text-sm text-red-500 mt-4 font-medium">⚠️ Bisnis Anda mengalami kerugian pada bulan ini. Kurangi biaya operasional atau tingkatkan penjualan.</p>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
