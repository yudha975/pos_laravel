<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])->latest();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->paginate(10);
        return view('pages.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        
        $generatedSku = 'SKU-' . rand(100000, 999999);
        
        return view('pages.products.create', compact('categories', 'brands', 'generatedSku'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku|max:255',
            'barcode' => 'nullable|string|unique:products,barcode|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'cost_price' => 'required|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            'reseller_price' => 'required|numeric|min:0',
            'technician_price' => 'required|numeric|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $stock = $validated['stock'];
        unset($validated['stock']);
        
        $validated['is_active'] = $request->has('is_active');

        $product = Product::create($validated);

        // Assign stock to current user's branch
        $branchId = auth()->check() ? (auth()->user()->branch_id ?? 1) : 1;
        $product->branches()->attach($branchId, ['stock' => $stock]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        return view('pages.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'cost_price' => 'required|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            'reseller_price' => 'required|numeric|min:0',
            'technician_price' => 'required|numeric|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $stock = $validated['stock'];
        unset($validated['stock']);

        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        // Update stock for current user's branch
        $branchId = auth()->check() ? (auth()->user()->branch_id ?? 1) : 1;
        
        $pivot = $product->branches()->where('branch_id', $branchId)->first();
        if ($pivot) {
            $product->branches()->updateExistingPivot($branchId, ['stock' => $stock]);
        } else {
            $product->branches()->attach($branchId, ['stock' => $stock]);
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
