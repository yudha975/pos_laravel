<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('POS (Point of Sale)') }}
        </h2>
    </x-slot>

    <!-- Alpine.js Application -->
    <div x-data="posApp()" class="flex flex-col md:flex-row min-h-screen md:min-h-0 md:h-[calc(100vh-10rem)] gap-4 mt-4">
        
        <!-- Left Side: Product Grid -->
        <div class="w-full md:w-2/3 flex flex-col bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden" style="min-height: 50vh;">
            <!-- Search & Filter Bar -->
            <div class="p-4 border-b border-gray-100 flex gap-2">
                <input type="text" x-model="search" placeholder="Cari nama produk, SKU, barcode..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <select x-model="categoryId" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm w-48">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Product Grid -->
            <div class="flex-1 p-4 overflow-y-auto bg-gray-50">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="product in filteredProducts()" :key="product.id">
                        <div @click="addToCart(product)" class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 cursor-pointer hover:border-blue-500 hover:shadow-md transition-all flex flex-col justify-between h-full select-none">
                            <div>
                                <div class="text-xs text-gray-400 mb-1" x-text="product.sku"></div>
                                <h3 class="text-sm font-semibold text-gray-800 leading-tight mb-2 line-clamp-2" x-text="product.name"></h3>
                            </div>
                            <div class="mt-2">
                                <div class="text-lg font-bold text-blue-600" x-text="formatRupiah(product.retail_price)"></div>
                                <div class="text-xs mt-1" :class="product.stock > 10 ? 'text-green-600' : 'text-red-600'" x-text="'Stok: ' + product.stock"></div>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="filteredProducts().length === 0" class="col-span-full py-12 text-center text-gray-500">
                        Produk tidak ditemukan.
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Cart & Checkout -->
        <div class="w-full md:w-1/3 flex flex-col bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <!-- Cart Header -->
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Keranjang</h3>
                <button @click="clearCart()" x-show="cart.length > 0" class="text-xs text-red-600 hover:text-red-800">Kosongkan</button>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4">
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-gray-400">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                        <p class="text-sm">Belum ada produk</p>
                    </div>
                </template>

                <div class="space-y-3">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="flex flex-col border-b border-gray-100 pb-3">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm font-medium text-gray-800 line-clamp-2 pr-2" x-text="item.name"></span>
                                <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-500" x-text="formatRupiah(item.price)"></div>
                                <div class="flex items-center space-x-2">
                                    <button @click="decreaseQty(index)" class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-gray-200">-</button>
                                    <input type="number" x-model.number="item.qty" min="1" :max="item.stock" class="w-12 h-6 text-center text-sm p-0 border-gray-300 rounded" @change="updateQty(index, $event.target.value)">
                                    <button @click="increaseQty(index)" class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-gray-200">+</button>
                                </div>
                            </div>
                            <!-- Price Type Selector -->
                            <div class="mt-2">
                                <select x-model="item.priceType" @change="changePriceType(index)" class="w-full text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                    <option value="retail">Eceran: <span x-text="formatRupiah(item.retail_price)"></span></option>
                                    <option value="reseller" x-show="item.reseller_price > 0">Reseller: <span x-text="formatRupiah(item.reseller_price)"></span></option>
                                    <option value="technician" x-show="item.technician_price > 0">Teknisi: <span x-text="formatRupiah(item.technician_price)"></span></option>
                                    <option value="wholesale" x-show="item.wholesale_price > 0">Grosir: <span x-text="formatRupiah(item.wholesale_price)"></span></option>
                                    <option value="custom">Harga Custom</option>
                                </select>
                            </div>
                            <div x-show="item.priceType === 'custom'" class="mt-1">
                                <input type="number" x-model.number="item.price" placeholder="Masukkan harga" @input="autoFillPaidAmount()" class="w-full text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 p-1">
                            </div>
                            <div class="text-right text-xs font-semibold text-gray-700 mt-1" x-text="'Subtotal: ' + formatRupiah(item.price * item.qty)"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Checkout Section -->
            <div class="p-4 border-t border-gray-100 bg-gray-50 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium" x-text="formatRupiah(subtotal())"></span>
                </div>
                
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Diskon (Rp)</span>
                    <input type="number" x-model.number="discount" min="0" class="w-32 h-8 text-right text-sm border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-blue-500 p-1">
                </div>
                
                <div class="flex justify-between items-center text-lg font-bold border-t border-gray-200 pt-4 mt-4">
                    <span>Total Tagihan</span>
                    <span class="text-blue-600 text-xl" x-text="formatRupiah(totalAmount())"></span>
                </div>

                <div class="mt-6 space-y-4 border-t border-gray-200 pt-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Pilih Pelanggan</label>
                        <select x-model="customerId" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Pelanggan Umum</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->type }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Metode Bayar</label>
                            <select x-model="paymentMethod" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                                <option value="e-wallet">e-Wallet</option>
                                <option value="kredit">Kredit / Bon</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider text-right">DP / Nominal Bayar (Rp)</label>
                            <input type="number" x-model.number="paidAmount" :placeholder="paymentMethod === 'kredit' ? '0 = Belum Bayar' : 'Nominal'" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 text-right text-lg font-semibold text-gray-800">
                        </div>
                    </div>
                </div>

                <div x-show="paymentMethod === 'cash'" class="flex justify-between items-center text-lg mt-4 border-t border-gray-200 pt-4">
                    <span class="text-gray-600 font-medium">Kembalian</span>
                    <span class="font-bold text-2xl" :class="returnAmount() < 0 ? 'text-red-500' : 'text-green-600'" x-text="formatRupiah(returnAmount())"></span>
                </div>
                <div x-show="paymentMethod === 'kredit'" class="flex justify-between items-center text-sm mt-4 border-t border-gray-200 pt-4 bg-yellow-50 rounded p-2">
                    <span class="text-yellow-700 font-medium">Sisa Piutang</span>
                    <span class="font-bold text-yellow-700" x-text="formatRupiah(Math.max(0, totalAmount() - (paidAmount || 0)))"></span>
                </div>

                <button @click="processCheckout()" 
                        :disabled="cart.length === 0 || isProcessing || (paymentMethod === 'cash' && returnAmount() < 0)"
                        class="w-full mt-4 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-3 px-4 rounded-lg shadow transition-colors flex items-center justify-center">
                    <span x-show="!isProcessing">Proses Pembayaran</span>
                    <span x-show="isProcessing" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Alpine.js Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posApp', () => ({
                products: @json($products),
                cart: [],
                search: '',
                categoryId: '',
                customerId: '',
                paymentMethod: 'cash',
                discount: 0,
                paidAmount: '',
                isProcessing: false,

                filteredProducts() {
                    return this.products.filter(p => {
                        const matchSearch = p.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                            (p.sku && p.sku.toLowerCase().includes(this.search.toLowerCase())) ||
                                            (p.barcode && p.barcode.toLowerCase().includes(this.search.toLowerCase()));
                        const matchCategory = this.categoryId === '' || p.category_id == this.categoryId;
                        return matchSearch && matchCategory;
                    });
                },

                addToCart(product) {
                    const existing = this.cart.find(item => item.id === product.id);
                    if (existing) {
                        if (existing.qty < product.stock) {
                            existing.qty++;
                        } else {
                            alert('Stok tidak mencukupi!');
                        }
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price: product.retail_price,
                            retail_price: product.retail_price,
                            reseller_price: product.reseller_price || 0,
                            technician_price: product.technician_price || 0,
                            wholesale_price: product.wholesale_price || 0,
                            priceType: 'retail',
                            stock: product.stock,
                            qty: 1
                        });
                    }
                    this.autoFillPaidAmount();
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.autoFillPaidAmount();
                },

                increaseQty(index) {
                    if (this.cart[index].qty < this.cart[index].stock) {
                        this.cart[index].qty++;
                        this.autoFillPaidAmount();
                    } else {
                        alert('Stok tidak mencukupi!');
                    }
                },

                decreaseQty(index) {
                    if (this.cart[index].qty > 1) {
                        this.cart[index].qty--;
                        this.autoFillPaidAmount();
                    } else {
                        this.removeFromCart(index);
                    }
                },

                updateQty(index, value) {
                    let val = parseInt(value);
                    if (isNaN(val) || val < 1) val = 1;
                    if (val > this.cart[index].stock) {
                        val = this.cart[index].stock;
                        alert('Stok maksimal adalah ' + val);
                    }
                    this.cart[index].qty = val;
                    this.autoFillPaidAmount();
                },

                changePriceType(index) {
                    const item = this.cart[index];
                    const type = item.priceType;
                    if (type === 'retail') item.price = item.retail_price;
                    else if (type === 'reseller') item.price = item.reseller_price;
                    else if (type === 'technician') item.price = item.technician_price;
                    else if (type === 'wholesale') item.price = item.wholesale_price;
                    // 'custom' => user types manually
                    this.autoFillPaidAmount();
                },

                clearCart() {
                    if(confirm('Kosongkan keranjang?')) {
                        this.cart = [];
                        this.discount = 0;
                        this.paidAmount = '';
                    }
                },

                subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },

                totalAmount() {
                    return Math.max(0, this.subtotal() - (this.discount || 0));
                },

                returnAmount() {
                    if(this.paidAmount === '') return 0;
                    return (this.paidAmount || 0) - this.totalAmount();
                },

                autoFillPaidAmount() {
                    // Kredit: jangan auto-fill, biarkan kasir isi DP manual
                    // Cash: auto-fill jika kosong
                    // Lainnya (transfer, qris, dll): auto-fill dengan total
                    if (this.paymentMethod === 'kredit') {
                        // Biarkan user isi manual, default 0
                        if (this.paidAmount === '') this.paidAmount = 0;
                    } else if (this.paymentMethod !== 'cash' || this.paidAmount === '') {
                        this.paidAmount = this.totalAmount();
                    }
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
                },

                init() {
                    this.$watch('paymentMethod', value => {
                        if (value === 'kredit') {
                            this.paidAmount = 0; // Default DP = 0 untuk kredit
                        } else if (value !== 'cash') {
                            this.paidAmount = this.totalAmount();
                        }
                    });
                    this.$watch('discount', () => this.autoFillPaidAmount());
                },

                async processCheckout() {
                    if (this.cart.length === 0) return;
                    
                    if (this.paymentMethod === 'cash' && this.returnAmount() < 0) {
                        alert('Jumlah uang kurang!');
                        return;
                    }

                    this.isProcessing = true;

                    try {
                        const response = await fetch('{{ route('pos.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                customer_id: this.customerId || null,
                                payment_method: this.paymentMethod,
                                discount: this.discount || 0,
                                paid_amount: this.paidAmount || this.totalAmount(),
                                items: this.cart.map(item => ({
                                    id: item.id,
                                    quantity: item.qty,
                                    price: item.price
                                }))
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            // Success! 
                            this.cart = [];
                            this.discount = 0;
                            this.paidAmount = '';
                            
                            // Buka struk di tab baru
                            window.open(data.receipt_url, '_blank');
                            
                            // Reload produk untuk update stok (bisa pakai window.location.reload() atau fetch ulang)
                            window.location.reload();
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
