<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('code', 'HQ')->first();

        Category::firstOrCreate(['code' => 'ELEC'], ['name' => 'ອຸປະກອນອີເລັກໂທຣນິກ', 'is_active' => true]);
        Category::firstOrCreate(['code' => 'STAT'], ['name' => 'ເຄື່ອງຂຽນ', 'is_active' => true]);
        Category::firstOrCreate(['code' => 'FURN'], ['name' => 'ເຟີນິເຈີ', 'is_active' => true]);
        Category::firstOrCreate(['code' => 'FOOD'], ['name' => 'ສິນຄ້າອາຫານ', 'is_active' => true]);

        Brand::firstOrCreate(['code' => 'SONY'], ['name' => 'Sony', 'is_active' => true]);
        Brand::firstOrCreate(['code' => 'SAML'], ['name' => 'Samsung', 'is_active' => true]);
        Brand::firstOrCreate(['code' => 'GNRC'], ['name' => 'Generic', 'is_active' => true]);

        Unit::firstOrCreate(['name' => 'ອັນ'], ['abbreviation' => 'ອັນ', 'is_active' => true]);
        Unit::firstOrCreate(['name' => 'ກ່ອງ'], ['abbreviation' => 'ກ່ອງ', 'is_active' => true]);
        Unit::firstOrCreate(['name' => 'ໂຫລ'], ['abbreviation' => 'ໂຫລ', 'is_active' => true]);
        Unit::firstOrCreate(['name' => 'ກິໂລ'], ['abbreviation' => 'ກກ', 'is_active' => true]);

        Supplier::firstOrCreate(['code' => 'SUP001'], [
            'name' => 'ບໍລິສັດ ABC ຈຳກັດ',
            'contact_person' => 'ທ. ສົມຈິດ',
            'phone' => '020-1234-5678',
            'is_active' => true,
        ]);

        if ($branch) {
            Warehouse::firstOrCreate(['code' => 'WH001'], [
                'name' => 'ສາງກາງ',
                'branch_id' => $branch->id,
                'is_active' => true,
            ]);
            Warehouse::firstOrCreate(['code' => 'WH002'], [
                'name' => 'ສາງສາຂາ 1',
                'branch_id' => $branch->id,
                'is_active' => true,
            ]);
        }
    }
}
