<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create a default branch if it doesn't exist
        $branchId = DB::table('branches')->insertGetId([
            'name' => 'Cabang Pusat',
            'address' => 'Jl. Pusat Utama No.1',
            'phone' => '081234567890',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 2. Update existing data to use this branch_id
        DB::table('users')->update(['branch_id' => $branchId]);
        DB::table('sales')->update(['branch_id' => $branchId]);
        DB::table('purchases')->update(['branch_id' => $branchId]);
        DB::table('cash_transactions')->update(['branch_id' => $branchId]);
        DB::table('stock_transactions')->update(['branch_id' => $branchId]);

        // 3. Move stock to product_branches
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            DB::table('product_branches')->insert([
                'product_id' => $product->id,
                'branch_id' => $branchId,
                'stock' => $product->stock,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 4. Drop stock column from products
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('reseller_price');
        });

        // Copy back
        $branches = DB::table('product_branches')->where('branch_id', 1)->get();
        foreach ($branches as $pb) {
            DB::table('products')->where('id', $pb->product_id)->update(['stock' => $pb->stock]);
        }
    }
};
