<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kartu Stok / Riwayat Transaksi') }}
            </h2>
            <a href="{{ route('inventory.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md shadow-sm text-sm transition-colors">
                + Input Stok Manual
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                <div class="flex-1 max-w-sm">
                    <select name="product_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">-- Semua Produk --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} (Stok: {{ $p->stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <select name="type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">-- Semua Tipe --</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Masuk</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Keluar</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Penyesuaian (Opname)</option>
                        <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Penjualan</option>
                        <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Pembelian</option>
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md shadow-sm text-sm transition-colors">Filter</button>
                @if(request('product_id') || request('type'))
                    <a href="{{ route('inventory.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-md shadow-sm text-sm transition-colors text-center">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty / Stok Akhir</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referensi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($transactions as $trx)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $trx->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $trx->product->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($trx->type === 'in' || $trx->type === 'purchase')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Masuk</span>
                                @elseif($trx->type === 'out' || $trx->type === 'sale')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Keluar</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Opname</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($trx->type === 'adjustment')
                                    <div class="text-sm font-bold text-gray-900">Menjadi: {{ $trx->quantity }}</div>
                                @else
                                    <div class="text-sm font-bold {{ in_array($trx->type, ['in', 'purchase']) ? 'text-green-600' : 'text-red-600' }}">
                                        {{ in_array($trx->type, ['in', 'purchase']) ? '+' : '-' }}{{ $trx->quantity }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $trx->reference ?: '-' }}
                                @if($trx->notes)
                                    <div class="text-xs text-gray-400 mt-1">{{ $trx->notes }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $trx->user->name ?? 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Data transaksi tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
