<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Penjualan') }}
        </h2>
    </x-slot>

    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        
        <!-- Filter Form -->
        <form method="GET" action="{{ route('reports.sales') }}" class="flex flex-col md:flex-row md:items-end gap-4 mb-8 print-hidden">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md shadow-sm text-sm transition-colors w-full md:w-auto">
                    Filter Laporan
                </button>
            </div>
            <div class="md:ml-auto">
                <button type="button" onclick="window.print()" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-md shadow-sm text-sm border border-gray-300 transition-colors flex items-center justify-center w-full md:w-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak
                </button>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                <p class="text-sm font-medium text-blue-800 mb-1">Total Penjualan (Kotor)</p>
                <p class="text-2xl font-bold text-blue-900">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                <p class="text-sm font-medium text-green-800 mb-1">Total Uang Diterima</p>
                <p class="text-2xl font-bold text-green-900">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
            </div>
            <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                <p class="text-sm font-medium text-red-800 mb-1">Total Piutang Belum Tertagih</p>
                <p class="text-2xl font-bold text-red-900">Rp {{ number_format($totalReceivables, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto border rounded-lg border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Faktur</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Metode Bayar</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $sale->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $sale->invoice_number }}
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $sale->customer->name ?? 'Umum' }}
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-right text-sm text-gray-500 uppercase">
                                {{ $sale->payment_method }}
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada data penjualan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
