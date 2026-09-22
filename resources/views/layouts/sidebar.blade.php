<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed sm:relative inset-y-0 left-0 z-30 w-64 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-300 ease-in-out sm:translate-x-0 h-full shadow-lg">
    @php
        $appSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $logo = $appSettings['company_logo'] ?? null;
        $appName = $appSettings['company_name'] ?? 'POS';
    @endphp
    <!-- Brand -->
    <div class="py-4 flex flex-col items-center justify-center border-b border-slate-800 relative">
        <div class="flex items-center space-x-2">
            @if($logo)
                <img src="{{ $logo }}" alt="Logo" class="h-8 w-auto">
            @endif
            <span class="font-bold text-xl tracking-wider text-white">POS</span>
        </div>
        <div class="mt-1 text-xs text-slate-400 text-center font-medium px-2">
            {{ $appName }}
        </div>
        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" class="absolute top-4 right-4 sm:hidden text-slate-400 hover:text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 px-4 mt-4">Utama</div>
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <div x-data="{ open: {{ request()->routeIs('pos.*', 'sales.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mt-6 mb-2 hover:text-slate-300 focus:outline-none">
                <span>POS & Penjualan</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="open" class="space-y-2">
                <a href="{{ route('pos.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('pos.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                    <span class="font-medium">Kasir</span>
                </a>
                <a href="{{ route('sales.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('sales.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="font-medium">Histori Penjualan</span>
                </a>
            </div>
        </div>

        @role('superadmin|admin_cabang')
        <div x-data="{ open: {{ request()->routeIs('purchases.*', 'inventory.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mt-6 mb-2 hover:text-slate-300 focus:outline-none">
                <span>Inventory & Pembelian</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="open" class="space-y-2">
                <a href="{{ route('purchases.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('purchases.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                    <span class="font-medium">Pembelian Barang</span>
                </a>
                <a href="{{ route('inventory.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('inventory.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                    <span class="font-medium">Kartu Stok</span>
                </a>
            </div>
        </div>
        @endrole

        @role('superadmin|admin_cabang')
        <div x-data="{ open: {{ request()->routeIs('finance.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mt-6 mb-2 hover:text-slate-300 focus:outline-none">
                <span>Keuangan (Finance)</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="open" class="space-y-2">
                <a href="{{ route('finance.cash') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('finance.cash') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="font-medium">Buku Kas</span>
                </a>
                <a href="{{ route('finance.profit_loss') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('finance.profit_loss') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span class="font-medium">Laba Rugi (P&L)</span>
                </a>
            </div>
        </div>
        @endrole

        @role('superadmin|admin_cabang')
        <div x-data="{ open: {{ request()->routeIs('products.*', 'categories.*', 'brands.*', 'customers.*', 'suppliers.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mt-6 mb-2 hover:text-slate-300 focus:outline-none">
                <span>Master Data</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="open" class="space-y-2">
                <a href="{{ route('products.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span class="font-medium">Produk</span>
                </a>
                <a href="{{ route('categories.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span class="font-medium">Kategori</span>
                </a>
                <a href="{{ route('brands.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('brands.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span class="font-medium">Brand</span>
                </a>
                <a href="{{ route('customers.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="font-medium">Pelanggan</span>
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('suppliers.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="font-medium">Supplier</span>
                </a>
            </div>
        </div>
        @endrole
        @role('superadmin|admin_cabang')
        <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mt-6 mb-2 hover:text-slate-300 focus:outline-none">
                <span>Laporan & Analitik</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="open" class="space-y-2">
                <a href="{{ route('reports.sales') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('reports.sales') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="font-medium">Laporan Penjualan</span>
                </a>
                <a href="{{ route('reports.purchases') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('reports.purchases') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                    <span class="font-medium">Laporan Pembelian</span>
                </a>
                <a href="{{ route('reports.stocks') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('reports.stocks') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span class="font-medium">Laporan Aset Stok</span>
                </a>
            </div>
        </div>
        @endrole

        @role('superadmin')
        <div x-data="{ open: {{ request()->routeIs('users.*', 'settings.*', 'audit.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mt-6 mb-2 hover:text-slate-300 focus:outline-none">
                <span>Pengaturan Enterprise</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="open" class="space-y-2">
                <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="font-medium">Manajemen Karyawan</span>
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="font-medium">Pengaturan Toko</span>
                </a>
                <a href="{{ route('audit.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('audit.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="font-medium">Audit Log</span>
                </a>
            </div>
        </div>
        @endrole
    </nav>

    <!-- User Section in Sidebar -->
    <div class="p-4 border-t border-slate-800">
        <div class="flex items-center space-x-3 px-2">
            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold shrink-0">
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email ?? 'user@example.com' }}</p>
            </div>
        </div>
    </div>
</aside>
