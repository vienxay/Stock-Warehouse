<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'barcode', 'name', 'description',
        'category_id', 'brand_id', 'unit_id', 'supplier_id',
        'cost_price', 'selling_price', 'min_stock_alert', 'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function brand() { return $this->belongsTo(Brand::class); }
    public function unit() { return $this->belongsTo(Unit::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function images() { return $this->hasMany(ProductImage::class); }
    public function primaryImage() { return $this->hasOne(ProductImage::class)->where('is_primary', true); }
    public function warehouseStocks() { return $this->hasMany(WarehouseStock::class); }
    public function stockMovements() { return $this->hasMany(StockMovement::class); }

    public function getTotalStockAttribute(): int
    {
        return $this->warehouseStocks()->sum('quantity');
    }

    public function isLowStock(): bool
    {
        return $this->total_stock <= $this->min_stock_alert && $this->total_stock > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->total_stock <= 0;
    }
}
