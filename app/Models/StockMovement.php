<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'reference_no', 'type',
        'product_id', 'warehouse_id', 'user_id',
        'quantity', 'quantity_before', 'quantity_after',
        'unit_price', 'supplier_id', 'note', 'movement_date',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
        'unit_price'    => 'decimal:2',
    ];

    public function product()   { return $this->belongsTo(Product::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function user()      { return $this->belongsTo(User::class); }
    public function supplier()  { return $this->belongsTo(Supplier::class); }
}
