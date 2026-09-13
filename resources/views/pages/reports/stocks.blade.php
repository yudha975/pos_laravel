<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Aset & Valuasi Stok') }}
        </h2>
    </x-slot>

    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Daftar Aset Barang</h3>
                <p class="text-sm text-gray-500 print-hidden">Menampilkan nilai uang Anda yang saat ini tertahan (mengendap) dalam bentuk barang.</p>
            </div>
            <div class="mt-4 md:mt-0 print-hidden">
                <button type="button" onclick="window.print()" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-md shadow-sm text-sm border border-gray-300 transition-colors flex items-center justify-center w-full md:w-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Laporan
                </button>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-xl shadow-inner p-6 mb-8 text-white flex flex-col md:flex-row items-center justify-between">
            <div>
                <p class="text-blue-200 text-sm font-semibold uppercase tracking-wider mb-1">Total Nilai Aset Seluruh Toko</p>
                <p class="text-4xl font-black">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</p>
            </div>
            <div class="mt-4 md:mt-0 opacity-80">
                *Dihitung dari: Sisa Stok Fisik x Harga Modal (HPP)
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto border rounded-lg border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU / Barcode</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Stok</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Modal (Rp)</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Aset (Rp)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($products as $p)
                        @php 
                            $assetPerItem = $p->stock * $p->cost_price;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $p->sku }}
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $p->name }}
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-bold {{ $p->stock <= $p->min_stock ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $p->stock }}
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-right text-sm text-gray-500">
                                {{ number_format($p->cost_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium text-blue-700">
                                {{ number_format($assetPerItem, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada produk dalam master data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
