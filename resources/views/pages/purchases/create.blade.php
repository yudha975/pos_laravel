<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Pembelian / Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="mt-6" x-data="purchasingApp()">
        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Left Side: Rincian Barang -->
            <div class="w-full lg:w-2/3 space-y-6">
                <!-- Data Faktur -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Data Faktur & Supplier</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Faktur / INV Supplier <span class="text-red-500">*</span></label>
                            <input type="text" x-model="invoiceNumber" placeholder="Contoh: INV-SUP-001" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                            <select x-model="supplierId" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Daftar Item -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-medium text-gray-900">Rincian Barang</h3>
                        <button @click="addItem()" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 py-1 px-3 rounded border border-gray-300">
                            + Tambah Baris
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex flex-col sm:flex-row gap-3 items-end border-b sm:border-0 pb-4 sm:pb-0 border-gray-100">
                                <div class="w-full sm:w-2/5">
                                    <label class="block text-xs font-medium text-gray-500 mb-1 sm:hidden">Produk</label>
                                    <select x-model="item.id" @change="productSelected(index)" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="">-- Pilih Produk --</option>
                                        <template x-for="p in products" :key="p.id">
                                            <option :value="p.id" x-text="p.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="w-full sm:w-1/5">
                                    <label class="block text-xs font-medium text-gray-500 mb-1 sm:hidden">Harga Beli Baru</label>
                                    <input type="number" x-model.number="item.cost_price" placeholder="HPP" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                                <div class="w-full sm:w-1/5">
                                    <label class="block text-xs font-medium text-gray-500 mb-1 sm:hidden">Qty</label>
                                    <input type="number" x-model.number="item.qty" min="1" placeholder="Qty" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-center">
                                </div>
                                <div class="w-full sm:w-1/5 flex justify-between items-center sm:block">
                                    <div class="text-sm font-semibold text-gray-700" x-text="formatRupiah(item.cost_price * item.qty)"></div>
                                    <button @click="removeItem(index)" class="text-red-500 hover:text-red-700 sm:mt-1">Hapus</button>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="items.length === 0" class="text-center py-4 text-gray-500 text-sm">
                            Belum ada barang yang ditambahkan. Klik "Tambah Baris".
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Pembayaran & Simpan -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 sticky top-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Pembayaran</h3>
                    
                    <div class="flex justify-between items-center text-xl font-bold mb-6">
                        <span>Total Tagihan</span>
                        <span class="text-blue-600" x-text="formatRupiah(totalAmount())"></span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                            <select x-model="paymentStatus" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="lunas">Lunas (Bayar Penuh)</option>
                                <option value="sebagian">Bayar Sebagian (Hutang)</option>
                                <option value="belum_bayar">Belum Bayar (Hutang Penuh)</option>
                            </select>
                        </div>

                        <div x-show="paymentStatus !== 'belum_bayar'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Bayar</label>
                            <select x-model="paymentMethod" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>

                        <div x-show="paymentStatus === 'sebagian'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Dibayarkan (Rp)</label>
                            <input type="number" x-model.number="paidAmount" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-right font-bold">
                        </div>

                        <div x-show="paymentStatus === 'sebagian'" class="flex justify-between text-sm text-red-600 font-medium pt-2">
                            <span>Sisa Hutang:</span>
                            <span x-text="formatRupiah(totalAmount() - paidAmount)"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea x-model="notes" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button @click="submitPurchase()" 
                                :disabled="isProcessing || items.length === 0 || !invoiceNumber || !supplierId"
                                class="w-full bg-gray-800 hover:bg-gray-900 disabled:bg-gray-400 text-white font-bold py-3 px-4 rounded-lg shadow transition-colors flex items-center justify-center">
                            <span x-show="!isProcessing">Simpan Pembelian</span>
                            <span x-show="isProcessing">Menyimpan...</span>
                        </button>
                        <p class="text-xs text-center text-gray-500 mt-2">Menyimpan otomatis akan menambah stok produk di inventaris.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Script AlpineJS -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('purchasingApp', () => ({
                products: @json($products),
                invoiceNumber: '',
                supplierId: '',
                items: [
                    { id: '', cost_price: 0, qty: 1 }
                ],
                paymentStatus: 'lunas',
                paymentMethod: 'cash',
                paidAmount: 0,
                notes: '',
                isProcessing: false,

                addItem() {
                    this.items.push({ id: '', cost_price: 0, qty: 1 });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },

                productSelected(index) {
                    let pId = this.items[index].id;
                    if(pId) {
                        let prod = this.products.find(p => p.id == pId);
                        if(prod) {
                            // Set default harga beli dengan HPP terakhir
                            this.items[index].cost_price = parseFloat(prod.cost_price) || 0;
                        }
                    }
                },

                totalAmount() {
                    return this.items.reduce((sum, item) => sum + (item.cost_price * item.qty), 0);
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
                },

                async submitPurchase() {
                    // Validasi
                    const validItems = this.items.filter(i => i.id !== '' && i.qty > 0 && i.cost_price >= 0);
                    if(validItems.length === 0) {
                        alert('Pilih minimal 1 produk dengan qty > 0');
                        return;
                    }

                    // Tentukan paidAmount
                    let finalPaidAmount = 0;
                    if(this.paymentStatus === 'lunas') {
                        finalPaidAmount = this.totalAmount();
                    } else if (this.paymentStatus === 'sebagian') {
                        finalPaidAmount = this.paidAmount;
                        if(finalPaidAmount >= this.totalAmount()) {
                            alert('Pembayaran sebagian tidak boleh lebih besar atau sama dengan total tagihan.');
                            return;
                        }
                    } else {
                        finalPaidAmount = 0;
                    }

                    this.isProcessing = true;

                    try {
                        const response = await fetch('{{ route('purchases.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                invoice_number: this.invoiceNumber,
                                supplier_id: this.supplierId,
                                payment_method: this.paymentStatus === 'belum_bayar' ? 'kredit' : this.paymentMethod,
                                paid_amount: finalPaidAmount,
                                notes: this.notes,
                                items: validItems.map(item => ({
                                    id: item.id,
                                    quantity: item.qty,
                                    cost_price: item.cost_price
                                }))
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            window.location.href = data.redirect_url;
                        } else {
                            alert('Gagal: ' + (data.message || 'Terjadi kesalahan sistem'));
                        }
                    } catch (error) {
                        alert('Error koneksi jaringan.');
                    } finally {
                        this.isProcessing = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>
