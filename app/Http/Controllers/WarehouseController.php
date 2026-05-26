<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with(['branch:id,name'])
            ->withCount('stocks')
            ->latest()->paginate(15);
        $branches = Branch::where('is_active', true)->get();
        return view('warehouses.index', compact('warehouses', 'branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:20|unique:warehouses',
            'branch_name' => 'required|string|max:100',
            'address'     => 'nullable|string',
            'phone'       => 'nullable|string|max:20',
        ]);

        $branch = $this->findOrCreateBranch($data['branch_name']);

        Warehouse::create([
            'name'      => $data['name'],
            'code'      => $data['code'],
            'branch_id' => $branch->id,
            'address'   => $data['address'] ?? null,
            'phone'     => $data['phone'] ?? null,
            'is_active' => true,
        ]);
        return back()->with('success', 'ເພີ່ມສາງ "' . $data['name'] . '" ສຳເລັດ');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:20|unique:warehouses,code,' . $warehouse->id,
            'branch_name' => 'required|string|max:100',
            'address'     => 'nullable|string',
            'phone'       => 'nullable|string|max:20',
            'is_active'   => 'boolean',
        ]);

        $branch = $this->findOrCreateBranch($data['branch_name']);

        $warehouse->update([
            'name'      => $data['name'],
            'code'      => $data['code'],
            'branch_id' => $branch->id,
            'address'   => $data['address'] ?? null,
            'phone'     => $data['phone'] ?? null,
            'is_active' => $data['is_active'] ?? $warehouse->is_active,
        ]);
        return back()->with('success', 'ແກ້ໄຂສາງ ສຳເລັດ');
    }

    private function findOrCreateBranch(string $name): Branch
    {
        return Branch::firstOrCreate(
            ['name' => trim($name)],
            [
                'code'      => 'BR-' . strtoupper(substr(uniqid(), -5)),
                'is_active' => true,
            ]
        );
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return back()->with('success', 'ລຶບສາງ ສຳເລັດ');
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['branch', 'stocks.product.unit', 'stocks.product.category']);
        return view('warehouses.show', compact('warehouse'));
    }
}
