<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Produk Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('products.store') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Informasi Dasar -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Informasi Dasar</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="sku" :value="__('SKU')" />
                                    <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full" :value="old('sku', $generatedSku ?? '')" required autofocus />
                                    <x-input-error class="mt-2" :messages="$errors->get('sku')" />
                                    <p class="text-xs text-gray-500 mt-1">Otomatis di-generate, atau ubah secara manual.</p>
                                </div>
                                
                                <div>
                                    <x-input-label for="barcode" :value="__('Barcode')" />
                                    <x-text-input id="barcode" name="barcode" type="text" class="mt-1 block w-full" :value="old('barcode')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('barcode')" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="name" :value="__('Nama Produk')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <div>
                                    <x-input-label for="category_id" :value="__('Kategori')" />
                                    <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                                </div>

                                <div>
                                    <x-input-label for="brand_id" :value="__('Brand (Opsional)')" />
                                    <select id="brand_id" name="brand_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">-- Pilih Brand --</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('brand_id')" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="description" :value="__('Deskripsi')" />
                                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                                </div>
                            </div>
                        </div>

                        <!-- Harga & Stok -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Manajemen Harga & Stok</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="cost_price" :value="__('Harga Modal (HPP)')" />
                                    <x-text-input id="cost_price" name="cost_price" type="number" class="mt-1 block w-full" :value="old('cost_price', 0)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('cost_price')" />
                                </div>

                                <div>
                                    <x-input-label for="retail_price" :value="__('Harga Jual Eceran')" />
                                    <x-text-input id="retail_price" name="retail_price" type="number" class="mt-1 block w-full" :value="old('retail_price', 0)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('retail_price')" />
                                </div>

                                <div>
                                    <x-input-label for="wholesale_price" :value="__('Harga Jual Grosir')" />
                                    <x-text-input id="wholesale_price" name="wholesale_price" type="number" class="mt-1 block w-full" :value="old('wholesale_price', 0)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('wholesale_price')" />
                                </div>

                                <div>
                                    <x-input-label for="reseller_price" :value="__('Harga Jual Reseller')" />
                                    <x-text-input id="reseller_price" name="reseller_price" type="number" class="mt-1 block w-full" :value="old('reseller_price', 0)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('reseller_price')" />
                                </div>

                                <div>
                                    <x-input-label for="technician_price" :value="__('Harga Jual Teknisi')" />
                                    <x-text-input id="technician_price" name="technician_price" type="number" class="mt-1 block w-full" :value="old('technician_price', 0)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('technician_price')" />
                                </div>

                                <div class="col-span-full border-t my-2 pt-4"></div>

                                <div>
                                    <x-input-label for="stock" :value="__('Stok Saat Ini')" />
                                    <x-text-input id="stock" name="stock" type="number" class="mt-1 block w-full bg-gray-100" :value="old('stock', 0)" readonly title="Stok hanya dapat diubah melalui fitur Stock Adjustment/Receiving" />
                                    <p class="text-xs text-gray-500 mt-1">Gunakan fitur Inventory untuk input stok.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('stock')" />
                                </div>

                                <div>
                                    <x-input-label for="min_stock" :value="__('Stok Minimum (Peringatan)')" />
                                    <x-text-input id="min_stock" name="min_stock" type="number" class="mt-1 block w-full" :value="old('min_stock', 5)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('min_stock')" />
                                </div>

                                <div>
                                    <x-input-label for="location" :value="__('Lokasi Rak/Gudang')" />
                                    <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('location')" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <label for="is_active" class="inline-flex items-center mr-6">
                                <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-600">{{ __('Produk Aktif') }}</span>
                            </label>

                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Produk') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
