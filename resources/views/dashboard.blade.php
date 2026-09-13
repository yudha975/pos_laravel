<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Utama') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome / Hero -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-2xl font-bold">Selamat Datang, {{ Auth::user()->name ?? 'Admin' }}! 👋</h3>
                <p class="text-blue-100 mt-2">Berikut adalah ringkasan aktivitas toko Anda hari ini.</p>
            </div>

            <!-- Top Metric Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card 1: Omset -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                    <div class="p-4 rounded-full bg-green-100 text-green-600 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Penjualan (Hari Ini)</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($todaySales, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Card 2: Pengeluaran -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                    <div class="p-4 rounded-full bg-red-100 text-red-600 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Belanja (Hari Ini)</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($todayPurchases, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Card 3: Piutang -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                    <div class="p-4 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Piutang Pelanggan</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalReceivables, 0, ',', '.') }}</p>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Stok Kritis -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h4 class="font-bold text-gray-800">⚠️ Peringatan Stok Menipis</h4>
                        <a href="{{ route('inventory.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="p-0">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Stok</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Min Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($lowStockProducts as $product)
                                <tr>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-bold rounded-md {{ $product->stock == 0 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-center text-sm text-gray-500">{{ $product->min_stock }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">Semua stok produk dalam kondisi aman.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Penjualan Terakhir -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h4 class="font-bold text-gray-800">🛍️ Penjualan Terakhir</h4>
                        <a href="{{ route('sales.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Histori</a>
                    </div>
                    <div class="p-0">
                        <ul class="divide-y divide-gray-100">
                            @forelse($recentSales as $sale)
                            <li class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $sale->invoice_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $sale->created_at->diffForHumans() }} &bull; {{ $sale->customer->name ?? 'Pelanggan Umum' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                                    @if($sale->paid_amount >= $sale->total_amount)
                                        <p class="text-xs text-green-600 font-medium">Lunas</p>
                                    @else
                                        <p class="text-xs text-red-600 font-medium">Kredit</p>
                                    @endif
                                </div>
                            </li>
                            @empty
                            <li class="px-6 py-8 text-center text-sm text-gray-500">Belum ada transaksi penjualan.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
