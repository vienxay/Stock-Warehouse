<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code', 'contact_person',
        'phone', 'email', 'address', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function products()        { return $this->hasMany(Product::class); }
    public function stockMovements()  { return $this->hasMany(StockMovement::class); }
}
