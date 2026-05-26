<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockRequestItem extends Model
{
    protected $fillable = [
        'stock_request_id', 'product_id',
        'quantity_requested', 'quantity_issued', 'note',
    ];

    public function stockRequest() { return $this->belongsTo(StockRequest::class); }
    public function product()      { return $this->belongsTo(Product::class); }
}
