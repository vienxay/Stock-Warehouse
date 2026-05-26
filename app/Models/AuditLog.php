<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'old_values', 'new_values', 'ip_address', 'user_agent', 'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        string $action,
        string $description,
        ?Model $model     = null,
        array  $oldValues = [],
        array  $newValues = []
    ): void {
        try {
            static::create([
                'user_id'     => auth()->id(),
                'action'      => $action,
                'model_type'  => $model ? class_basename($model) : null,
                'model_id'    => $model?->id,
                'old_values'  => $oldValues ?: null,
                'new_values'  => $newValues ?: null,
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
                'description' => $description,
            ]);
        } catch (\Throwable) {
            // Never let audit logging break the main flow
        }
    }

    const ACTION_CONFIG = [
        'login'           => ['label' => 'ເຂົ້າລະບົບ',       'color' => 'bg-blue-100 text-blue-700'],
        'logout'          => ['label' => 'ອອກລະບົບ',        'color' => 'bg-gray-100 text-gray-600'],
        'product_create'  => ['label' => 'ເພີ່ມສິນຄ້າ',      'color' => 'bg-purple-100 text-purple-700'],
        'product_update'  => ['label' => 'ແກ້ໄຂສິນຄ້າ',     'color' => 'bg-amber-100 text-amber-700'],
        'product_delete'  => ['label' => 'ລຶບສິນຄ້າ',       'color' => 'bg-red-100 text-red-700'],
        'stock_in'        => ['label' => 'ນຳເຂົ້າສາງ',       'color' => 'bg-green-100 text-green-700'],
        'stock_out'       => ['label' => 'ເບີກຈ່າຍ',         'color' => 'bg-orange-100 text-orange-700'],
        'stock_import'    => ['label' => 'Import Excel',     'color' => 'bg-teal-100 text-teal-700'],
        'request_create'  => ['label' => 'ສ້າງຄຳຮ້ອງ',      'color' => 'bg-cyan-100 text-cyan-700'],
        'request_approve' => ['label' => 'ອະນຸມັດຄຳຮ້ອງ',   'color' => 'bg-green-100 text-green-700'],
        'request_reject'  => ['label' => 'ປະຕິເສດຄຳຮ້ອງ',  'color' => 'bg-red-100 text-red-700'],
        'request_issue'   => ['label' => 'ຈ່າຍສິນຄ້າ',      'color' => 'bg-indigo-100 text-indigo-700'],
        'request_cancel'  => ['label' => 'ຍົກເລີກຄຳຮ້ອງ',  'color' => 'bg-gray-100 text-gray-600'],
        'user_create'     => ['label' => 'ສ້າງຜູ້ໃຊ້',       'color' => 'bg-blue-100 text-blue-700'],
        'user_update'     => ['label' => 'ແກ້ໄຂຜູ້ໃຊ້',      'color' => 'bg-amber-100 text-amber-700'],
        'user_delete'     => ['label' => 'ລຶບຜູ້ໃຊ້',        'color' => 'bg-red-100 text-red-700'],
        'user_activate'   => ['label' => 'ເປີດໃຊ້ຜູ້ໃຊ້',   'color' => 'bg-green-100 text-green-700'],
        'user_deactivate' => ['label' => 'ປິດໃຊ້ຜູ້ໃຊ້',   'color' => 'bg-red-100 text-red-600'],
        'settings_update' => ['label' => 'ແກ້ໄຂຕັ້ງຄ່າ',    'color' => 'bg-slate-100 text-slate-700'],
        'backup_create'   => ['label' => 'ສ້າງ BackUp',      'color' => 'bg-indigo-100 text-indigo-700'],
        'backup_delete'   => ['label' => 'ລຶບ BackUp',       'color' => 'bg-red-100 text-red-700'],
    ];

    public function getActionLabelAttribute(): string
    {
        return self::ACTION_CONFIG[$this->action]['label'] ?? $this->action;
    }

    public function getActionColorAttribute(): string
    {
        return self::ACTION_CONFIG[$this->action]['color'] ?? 'bg-gray-100 text-gray-600';
    }
}
