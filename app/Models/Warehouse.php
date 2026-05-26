<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'code', 'branch_id', 'address', 'phone', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function stocks() { return $this->hasMany(WarehouseStock::class); }
    public function stockMovements() { return $this->hasMany(StockMovement::class); }
    public function outgoingTransfers() { return $this->hasMany(WarehouseTransfer::class, 'from_warehouse_id'); }
    public function incomingTransfers() { return $this->hasMany(WarehouseTransfer::class, 'to_warehouse_id'); }
}
