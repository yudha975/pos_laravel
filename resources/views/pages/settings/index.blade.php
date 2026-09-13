<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Perusahaan') }}
        </h2>
    </x-slot>

    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
        
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('settings.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-6">
                <!-- Identitas Toko -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Identitas Toko</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Perusahaan / Toko</label>
                            <input type="text" name="company_name" value="{{ $settings['company_name'] ?? 'POS NET' }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alamat Pusat</label>
                            <textarea name="company_address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $settings['company_address'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telepon / WhatsApp</label>
                            <input type="text" name="company_phone" value="{{ $settings['company_phone'] ?? '' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Logo Perusahaan</label>
                            @if(isset($settings['company_logo']))
                                <div class="mt-2 mb-2">
                                    <img src="{{ $settings['company_logo'] }}" alt="Logo" class="h-16 rounded border">
                                </div>
                            @endif
                            <input type="file" name="company_logo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Favicon</label>
                            @if(isset($settings['company_favicon']))
                                <div class="mt-2 mb-2">
                                    <img src="{{ $settings['company_favicon'] }}" alt="Favicon" class="h-8 rounded border">
                                </div>
                            @endif
                            <input type="file" name="company_favicon" accept=".ico,image/png,image/jpeg" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>

                <!-- Kasir & Struk -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4 mt-8">Kasir & Struk Termal</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pesan Bawah Struk (Footer)</label>
                            <textarea name="receipt_footer" rows="2" placeholder="Contoh: Terima kasih telah berbelanja, barang yang dibeli tidak dapat ditukar." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $settings['receipt_footer'] ?? 'Terima Kasih Telah Berbelanja!' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md shadow-sm text-sm transition-colors">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
