<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'unit', 'warehouseStocks', 'primaryImage']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
                  ->orWhere('barcode', 'like', "%{$request->search}%");
            });
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->status === 'low') {
            $query->whereRaw(
                '(SELECT COALESCE(SUM(quantity),0) FROM warehouse_stocks WHERE warehouse_stocks.product_id = products.id) <= products.min_stock_alert'
            )->whereRaw(
                '(SELECT COALESCE(SUM(quantity),0) FROM warehouse_stocks WHERE warehouse_stocks.product_id = products.id) > 0'
            );
        }
        if ($request->status === 'out') {
            $query->whereRaw(
                '(SELECT COALESCE(SUM(quantity),0) FROM warehouse_stocks WHERE warehouse_stocks.product_id = products.id) = 0'
            );
        }

        $products    = $query->latest()->paginate(15)->withQueryString();
        $categories  = Category::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands     = Brand::where('is_active', true)->get();
        $units      = Unit::where('is_active', true)->get();
        $suppliers  = Supplier::where('is_active', true)->get();
        return view('products.create', compact('categories', 'brands', 'units', 'suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'            => 'required|string|max:50|unique:products',
            'barcode'         => 'nullable|string|max:100|unique:products',
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'category_id'     => 'nullable|exists:categories,id',
            'brand_id'        => 'nullable|exists:brands,id',
            'unit_id'         => 'nullable|exists:units,id',
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'cost_price'      => 'required|numeric|min:0',
            'selling_price'   => 'required|numeric|min:0',
            'min_stock_alert' => 'required|integer|min:0',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $product = Product::create($data);

        AuditLog::log('product_create', 'ເພີ່ມສິນຄ້າ "' . $product->name . '"', $product);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->images()->create(['image_path' => $path, 'is_primary' => true]);
        }

        return redirect()->route('products.index')
            ->with('success', 'ເພີ່ມສິນຄ້າ "' . $product->name . '" ສຳເລັດ');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'unit', 'supplier', 'warehouseStocks.warehouse', 'images']);
        $movements = $product->stockMovements()->with('warehouse', 'user')->latest()->limit(20)->get();
        return view('products.show', compact('product', 'movements'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $brands     = Brand::where('is_active', true)->get();
        $units      = Unit::where('is_active', true)->get();
        $suppliers  = Supplier::where('is_active', true)->get();
        return view('products.edit', compact('product', 'categories', 'brands', 'units', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'code'            => 'required|string|max:50|unique:products,code,' . $product->id,
            'barcode'         => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'category_id'     => 'nullable|exists:categories,id',
            'brand_id'        => 'nullable|exists:brands,id',
            'unit_id'         => 'nullable|exists:units,id',
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'cost_price'      => 'required|numeric|min:0',
            'selling_price'   => 'required|numeric|min:0',
            'min_stock_alert' => 'required|integer|min:0',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $product->update($data);

        AuditLog::log('product_update', 'ແກ້ໄຂສິນຄ້າ "' . $product->name . '"', $product);

        if ($request->hasFile('image')) {
            // ລຶບຮູບເກົ່າ
            $old = $product->primaryImage;
            if ($old) {
                Storage::disk('public')->delete($old->image_path);
                $old->delete();
            }
            $path = $request->file('image')->store('products', 'public');
            $product->images()->create(['image_path' => $path, 'is_primary' => true]);
        }

        return redirect()->route('products.index')
            ->with('success', 'ແກ້ໄຂສິນຄ້າ "' . $product->name . '" ສຳເລັດ');
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success', 'ລຶບຮູບສຳເລັດ');
    }

    public function destroy(Product $product)
    {
        AuditLog::log('product_delete', 'ລຶບສິນຄ້າ "' . $product->name . '"', $product);

        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'ລຶບສິນຄ້າ "' . $product->name . '" ສຳເລັດ');
    }
}
