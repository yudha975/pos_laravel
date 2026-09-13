<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'sku', 'barcode', 'name', 'category_id', 'brand_id', 
        'cost_price', 'retail_price', 'reseller_price', 'technician_price', 
        'wholesale_price', 'min_stock', 'location', 
        'image_url', 'description', 'is_active'
    ];

    protected $appends = ['stock'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'product_branches')->withPivot('stock')->withTimestamps();
    }

    public function getStockAttribute()
    {
        // Get stock for the current user's branch
        $branchId = auth()->check() ? (auth()->user()->branch_id ?? 1) : 1;
        $pivot = $this->branches()->where('branch_id', $branchId)->first();
        return $pivot ? (int) $pivot->pivot->stock : 0;
    }
}
