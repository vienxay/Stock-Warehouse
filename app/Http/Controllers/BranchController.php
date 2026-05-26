<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount(['users', 'warehouses'])
            ->latest()->paginate(20);
        return view('branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100|unique:branches,name',
            'code'    => 'required|string|max:20|unique:branches,code',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        $branch = Branch::create($data + ['is_active' => true]);
        AuditLog::log('branch_create', 'ສ້າງສາຂາ: ' . $branch->name, $branch);

        return back()->with('success', 'ເພີ່ມສາຂາ "' . $branch->name . '" ສຳເລັດ');
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100|unique:branches,name,' . $branch->id,
            'code'    => 'required|string|max:20|unique:branches,code,' . $branch->id,
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        $branch->update($data);
        AuditLog::log('branch_update', 'ແກ້ໄຂສາຂາ: ' . $branch->name, $branch);

        return back()->with('success', 'ແກ້ໄຂສາຂາ "' . $branch->name . '" ສຳເລັດ');
    }

    public function toggleActive(Branch $branch)
    {
        $branch->update(['is_active' => !$branch->is_active]);
        $status = $branch->is_active ? 'ເປີດໃຊ້' : 'ປິດໃຊ້';
        AuditLog::log('branch_toggle', $status . 'ສາຂາ: ' . $branch->name, $branch);

        return back()->with('success', $status . 'ສາຂາ "' . $branch->name . '" ສຳເລັດ');
    }

    public function destroy(Branch $branch)
    {
        $branch->loadCount(['users', 'warehouses']);

        if ($branch->users_count > 0 || $branch->warehouses_count > 0) {
            return back()->with('error', 'ບໍ່ສາມາດລຶບ "' . $branch->name . '" ຍັງມີຜູ້ໃຊ້ ' . $branch->users_count . ' ຄົນ ຫຼື ສາງ ' . $branch->warehouses_count . ' ແຫ່ງຜູກຢູ່');
        }

        AuditLog::log('branch_delete', 'ລຶບສາຂາ: ' . $branch->name, $branch);
        $branch->delete();

        return back()->with('success', 'ລຶບສາຂາ "' . $branch->name . '" ສຳເລັດ');
    }
}
