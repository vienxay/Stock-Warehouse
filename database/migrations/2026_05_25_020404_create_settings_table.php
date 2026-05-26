<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        $now = now();
        $defaults = [
            ['key' => 'company_name',          'value' => 'ລະບົບສາງ',         'group' => 'general'],
            ['key' => 'company_address',        'value' => '',                  'group' => 'general'],
            ['key' => 'company_phone',          'value' => '',                  'group' => 'general'],
            ['key' => 'company_email',          'value' => '',                  'group' => 'general'],
            ['key' => 'currency',               'value' => 'ກີບ',               'group' => 'system'],
            ['key' => 'currency_symbol',        'value' => '₭',                'group' => 'system'],
            ['key' => 'date_format',            'value' => 'd/m/Y',            'group' => 'system'],
            ['key' => 'items_per_page',         'value' => '15',               'group' => 'system'],
            ['key' => 'timezone',               'value' => 'Asia/Vientiane',   'group' => 'system'],
            ['key' => 'default_min_stock_alert','value' => '10',               'group' => 'stock'],
            ['key' => 'low_stock_notify',       'value' => '1',                'group' => 'stock'],
            ['key' => 'out_stock_notify',       'value' => '1',                'group' => 'stock'],
        ];

        foreach ($defaults as $row) {
            DB::table('settings')->insert(array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
