<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'units');

        $units     = Unit::withCount('products')->orderBy('name')->get();
        $brands    = Brand::withCount('products')->orderBy('name')->get();
        $suppliers = Supplier::withCount('products')->orderBy('name')->get();

        return view('catalog.index', compact('units', 'brands', 'suppliers', 'tab'));
    }

    // ===== UNITS =====
    public function storeUnit(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:50|unique:units,name',
            'abbreviation' => 'nullable|string|max:10',
        ]);
        Unit::create($data + ['is_active' => true]);
        return redirect()->route('catalog.index', ['tab' => 'units'])->with('success', 'ເພີ່ມໜ່ວຍ "' . $data['name'] . '" ສຳເລັດ');
    }

    public function updateUnit(Request $request, Unit $unit)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:50|unique:units,name,' . $unit->id,
            'abbreviation' => 'nullable|string|max:10',
        ]);
        $unit->update($data);
        return redirect()->route('catalog.index', ['tab' => 'units'])->with('success', 'ແກ້ໄຂໜ່ວຍ ສຳເລັດ');
    }

    public function destroyUnit(Unit $unit)
    {
        $unit->loadCount('products');
        if ($unit->products_count > 0) {
            return redirect()->route('catalog.index', ['tab' => 'units'])
                ->with('error', 'ບໍ່ສາມາດລຶບໜ່ວຍ "' . $unit->name . '" ຍັງມີສິນຄ້າ ' . $unit->products_count . ' ລາຍການໃຊ້ຢູ່');
        }
        $unit->delete();
        return redirect()->route('catalog.index', ['tab' => 'units'])->with('success', 'ລຶບໜ່ວຍ ສຳເລັດ');
    }

    // ===== BRANDS =====
    public function storeBrand(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:brands,name',
            'code'        => 'nullable|string|max:20|unique:brands,code',
            'description' => 'nullable|string|max:255',
        ]);
        Brand::create($data + ['is_active' => true]);
        return redirect()->route('catalog.index', ['tab' => 'brands'])->with('success', 'ເພີ່ມຍີ່ຫໍ້ "' . $data['name'] . '" ສຳເລັດ');
    }

    public function updateBrand(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:brands,name,' . $brand->id,
            'code'        => 'nullable|string|max:20|unique:brands,code,' . $brand->id,
            'description' => 'nullable|string|max:255',
        ]);
        $brand->update($data);
        return redirect()->route('catalog.index', ['tab' => 'brands'])->with('success', 'ແກ້ໄຂຍີ່ຫໍ້ ສຳເລັດ');
    }

    public function destroyBrand(Brand $brand)
    {
        $brand->loadCount('products');
        if ($brand->products_count > 0) {
            return redirect()->route('catalog.index', ['tab' => 'brands'])
                ->with('error', 'ບໍ່ສາມາດລຶບຍີ່ຫໍ້ "' . $brand->name . '" ຍັງມີສິນຄ້າ ' . $brand->products_count . ' ລາຍການໃຊ້ຢູ່');
        }
        $brand->delete();
        return redirect()->route('catalog.index', ['tab' => 'brands'])->with('success', 'ລຶບຍີ່ຫໍ້ ສຳເລັດ');
    }

    // ===== SUPPLIERS =====
    public function storeSupplier(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100|unique:suppliers,name',
            'code'           => 'nullable|string|max:20|unique:suppliers,code',
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string|max:255',
        ]);
        Supplier::create($data + ['is_active' => true]);
        return redirect()->route('catalog.index', ['tab' => 'suppliers'])->with('success', 'ເພີ່ມຜູ້ສະໜອງ "' . $data['name'] . '" ສຳເລັດ');
    }

    public function updateSupplier(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100|unique:suppliers,name,' . $supplier->id,
            'code'           => 'nullable|string|max:20|unique:suppliers,code,' . $supplier->id,
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string|max:255',
        ]);
        $supplier->update($data);
        return redirect()->route('catalog.index', ['tab' => 'suppliers'])->with('success', 'ແກ້ໄຂຜູ້ສະໜອງ ສຳເລັດ');
    }

    public function destroySupplier(Supplier $supplier)
    {
        $supplier->loadCount('products');
        if ($supplier->products_count > 0) {
            return redirect()->route('catalog.index', ['tab' => 'suppliers'])
                ->with('error', 'ບໍ່ສາມາດລຶບ "' . $supplier->name . '" ຍັງມີສິນຄ້າ ' . $supplier->products_count . ' ລາຍການໃຊ້ຢູ່');
        }
        $supplier->delete();
        return redirect()->route('catalog.index', ['tab' => 'suppliers'])->with('success', 'ລຶບຜູ້ສະໜອງ ສຳເລັດ');
    }
}
