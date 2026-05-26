<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\WarehouseTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = WarehouseTransfer::with([
            'fromWarehouse', 'toWarehouse', 'product.unit', 'creator',
        ]);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->from_warehouse_id) {
            $query->where('from_warehouse_id', $request->from_warehouse_id);
        }

        $transfers  = $query->latest()->paginate(20)->withQueryString();
        $warehouses = Warehouse::where('is_active', true)->get();
        $products   = Product::where('is_active', true)->with('unit')->orderBy('name')->get();

        return view('transfers.index', compact('transfers', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id'   => 'required|exists:warehouses,id|different:from_warehouse_id',
            'product_id'        => 'required|exists:products,id',
            'quantity'          => 'required|integer|min:1',
            'note'              => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($data) {
            $stock = WarehouseStock::where([
                'warehouse_id' => $data['from_warehouse_id'],
                'product_id'   => $data['product_id'],
            ])->lockForUpdate()->first();

            $available = $stock?->quantity ?? 0;
            if ($available < $data['quantity']) {
                throw new \Exception('ສາງຕົ້ນທາງມີພຽງ ' . number_format($available) . ' ໜ່ວຍ ບໍ່ພໍໂອນ');
            }

            $transferNo = 'TRF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            // ຫັກຈາກສາງຕົ້ນທາງ
            $before = $stock->quantity;
            $stock->decrement('quantity', $data['quantity']);

            StockMovement::create([
                'type'            => 'transfer',
                'reference_no'    => $transferNo,
                'product_id'      => $data['product_id'],
                'warehouse_id'    => $data['from_warehouse_id'],
                'user_id'         => Auth::id(),
                'quantity'        => $data['quantity'],
                'quantity_before' => $before,
                'quantity_after'  => $before - $data['quantity'],
                'note'            => 'ໂອນໄປ warehouse #' . $data['to_warehouse_id'],
                'movement_date'   => now(),
            ]);

            $transfer = WarehouseTransfer::create([
                'transfer_no'       => $transferNo,
                'from_warehouse_id' => $data['from_warehouse_id'],
                'to_warehouse_id'   => $data['to_warehouse_id'],
                'product_id'        => $data['product_id'],
                'quantity'          => $data['quantity'],
                'status'            => 'pending',
                'created_by'        => Auth::id(),
                'note'              => $data['note'] ?? null,
            ]);

            AuditLog::log('transfer_create', 'ສ້າງການໂອນ ' . $transferNo, $transfer);
        });

        return redirect()->route('transfers.index')
            ->with('success', 'ສ້າງການໂອນສາງສຳເລັດ ລໍຖ້າຮັບ');
    }

    public function receive(Request $request, WarehouseTransfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return back()->with('error', 'ການໂອນນີ້ບໍ່ແມ່ນສະຖານະລໍຖ້າ');
        }

        $request->validate(['note' => 'nullable|string|max:500']);

        DB::transaction(function () use ($transfer, $request) {
            // ເພີ່ມເຂົ້າສາງປາຍທາງ
            $dest = WarehouseStock::firstOrCreate(
                ['warehouse_id' => $transfer->to_warehouse_id, 'product_id' => $transfer->product_id],
                ['quantity' => 0]
            );
            $before = $dest->quantity;
            $dest->increment('quantity', $transfer->quantity);

            StockMovement::create([
                'type'            => 'transfer',
                'reference_no'    => $transfer->transfer_no,
                'product_id'      => $transfer->product_id,
                'warehouse_id'    => $transfer->to_warehouse_id,
                'user_id'         => Auth::id(),
                'quantity'        => $transfer->quantity,
                'quantity_before' => $before,
                'quantity_after'  => $before + $transfer->quantity,
                'note'            => 'ຮັບໂອນຈາກ warehouse #' . $transfer->from_warehouse_id,
                'movement_date'   => now(),
            ]);

            $transfer->update([
                'status'      => 'completed',
                'received_by' => Auth::id(),
                'received_at' => now(),
                'note'        => $request->note ?? $transfer->note,
            ]);

            AuditLog::log('transfer_receive', 'ຮັບການໂອນ ' . $transfer->transfer_no, $transfer);
        });

        return back()->with('success', 'ຢືນຢັນຮັບການໂອນ ' . $transfer->transfer_no . ' ສຳເລັດ');
    }

    public function cancel(WarehouseTransfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return back()->with('error', 'ຍົກເລີກໄດ້ສະເພາະການໂອນທີ່ລໍຖ້າ');
        }

        DB::transaction(function () use ($transfer) {
            // ຄືນຂອງກັບສາງຕົ້ນທາງ
            $source = WarehouseStock::where([
                'warehouse_id' => $transfer->from_warehouse_id,
                'product_id'   => $transfer->product_id,
            ])->lockForUpdate()->firstOrFail();

            $before = $source->quantity;
            $source->increment('quantity', $transfer->quantity);

            StockMovement::create([
                'type'            => 'transfer',
                'reference_no'    => $transfer->transfer_no . '-CANCEL',
                'product_id'      => $transfer->product_id,
                'warehouse_id'    => $transfer->from_warehouse_id,
                'user_id'         => Auth::id(),
                'quantity'        => $transfer->quantity,
                'quantity_before' => $before,
                'quantity_after'  => $before + $transfer->quantity,
                'note'            => 'ຍົກເລີກການໂອນ',
                'movement_date'   => now(),
            ]);

            $transfer->update(['status' => 'cancelled']);

            AuditLog::log('transfer_cancel', 'ຍົກເລີກການໂອນ ' . $transfer->transfer_no, $transfer);
        });

        return back()->with('success', 'ຍົກເລີກການໂອນ ' . $transfer->transfer_no . ' ແລ້ວ');
    }
}
