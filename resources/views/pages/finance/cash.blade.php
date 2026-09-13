<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buku Kas (Arus Kas)') }}
        </h2>
    </x-slot>

    <div class="mt-6" x-data="{ showModal: false, type: 'out', amount: 0, description: '', date: '{{ date('Y-m-d') }}' }">
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <p class="text-sm font-medium text-gray-500 mb-1">Total Kas Masuk</p>
                <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalIn, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <p class="text-sm font-medium text-gray-500 mb-1">Total Kas Keluar (Biaya)</p>
                <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalOut, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <p class="text-sm font-medium text-gray-500 mb-1">Saldo Kas Aktual</p>
                <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    Rp {{ number_format($balance, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Riwayat Transaksi Kas</h3>
                <div class="space-x-2">
                    <button @click="showModal = true; type = 'out'; description = ''; amount = 0;" class="bg-red-50 hover:bg-red-100 text-red-700 px-4 py-2 rounded-md text-sm font-medium border border-red-200 transition-colors">
                        - Kas Keluar
                    </button>
                    <button @click="showModal = true; type = 'in'; description = ''; amount = 0;" class="bg-green-50 hover:bg-green-100 text-green-700 px-4 py-2 rounded-md text-sm font-medium border border-green-200 transition-colors">
                        + Kas Masuk
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diinput Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($transactions as $t)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $t->date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $t->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    @if($t->type === 'in')
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700">Masuk</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700">Keluar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold {{ $t->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $t->type === 'in' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $t->user->name ?? 'Admin' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada transaksi kas yang dicatat.</td>
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

        <!-- Modal Input Kas -->
        <div x-show="showModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div x-show="showModal" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showModal = false"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form method="POST" action="{{ route('finance.cash.store') }}">
                        @csrf
                        <input type="hidden" name="type" x-model="type">
                        
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-bold mb-4" :class="type === 'in' ? 'text-green-600' : 'text-red-600'">
                                <span x-text="type === 'in' ? 'Input Kas Masuk' : 'Input Kas Keluar (Biaya)'"></span>
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                    <input type="date" name="date" x-model="date" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Keterangan / Tujuan</label>
                                    <input type="text" name="description" x-model="description" required placeholder="Contoh: Bayar Listrik, Gaji Pegawai" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                                    <input type="number" name="amount" x-model="amount" min="1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan
                            </button>
                            <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
