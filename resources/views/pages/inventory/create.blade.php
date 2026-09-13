<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Stok Manual') }}
        </h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden max-w-2xl mx-auto mt-6">
        <div class="p-6">
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('inventory.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Pilih Produk <span class="text-red-500">*</span></label>
                    <select name="product_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} (Sisa Stok: {{ $p->stock }})</option>
                        @endforeach
                    </select>
                    @error('product_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tipe Transaksi <span class="text-red-500">*</span></label>
                    <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Stok Masuk (Menambah Stok)</option>
                        <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Stok Keluar (Mengurangi Stok)</option>
                        <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>Stock Opname (Menimpa Stok Akhir)</option>
                    </select>
                    @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Kuantitas / Jumlah Stok Baru <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" value="{{ old('quantity') }}" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Jika Stock Opname, isi dengan jumlah stok fisik yang benar.</p>
                    @error('quantity')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nomor Referensi (Opsional)</label>
                    <input type="text" name="reference" value="{{ old('reference') }}" placeholder="Contoh: BAST-001, RETUR-002" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Catatan/Alasan</label>
                    <textarea name="notes" rows="2" placeholder="Contoh: Barang rusak di gudang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4">
                    <a href="{{ route('inventory.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Batal</a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
