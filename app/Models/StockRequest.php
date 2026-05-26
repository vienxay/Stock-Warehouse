<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    protected $fillable = [
        'request_no', 'requester_id', 'requester_name', 'branch_id', 'warehouse_id',
        'status', 'purpose', 'note',
        'approved_by', 'issued_by', 'approved_at', 'issued_at', 'rejection_reason',
        'received_by', 'received_at', 'received_note',
    ];

    protected $casts = [
        'approved_at'  => 'datetime',
        'issued_at'    => 'datetime',
        'received_at'  => 'datetime',
    ];

    public function requester() { return $this->belongsTo(User::class, 'requester_id'); }
    public function approver()  { return $this->belongsTo(User::class, 'approved_by'); }
    public function issuer()    { return $this->belongsTo(User::class, 'issued_by'); }
    public function receiver()  { return $this->belongsTo(User::class, 'received_by'); }
    public function branch()    { return $this->belongsTo(Branch::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items()     { return $this->hasMany(StockRequestItem::class); }

    public function isPending(): bool    { return $this->status === 'pending'; }
    public function isApproved(): bool   { return $this->status === 'approved'; }
    public function isIssued(): bool     { return $this->status === 'issued'; }
    public function isReceived(): bool   { return $this->isIssued() && !is_null($this->received_at); }
}
