<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockRequest;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Notifications\NewStockRequest;
use App\Notifications\StockRequestUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class StockRequestController extends Controller
{
    /** @return \App\Models\User */
    private function authUser(): \App\Models\User
    {
        return Auth::user(); /** @phpstan-ignore-line */
    }

    public function index(Request $request)
    {
        $query = StockRequest::with(['requester', 'branch', 'items']);

        if (!$this->authUser()->isManager()) {
            $query->where('requester_id', Auth::id());
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();
        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        $products   = Product::where('is_active', true)->with('unit')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('requests.create', compact('products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'requester_name' => 'required|string|max:100',
            'purpose'        => 'nullable|string|max:500',
            'note'           => 'nullable|string',
            'warehouse_id'   => 'nullable|exists:warehouses,id',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.note'       => 'nullable|string',
        ]);

        $createdRequest = null;

        DB::transaction(function () use ($data, &$createdRequest) {
            $user  = Auth::user();
            $reqNo = 'REQ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $createdRequest = StockRequest::create([
                'request_no'     => $reqNo,
                'requester_id'   => $user->id,
                'requester_name' => $data['requester_name'],
                'branch_id'      => $user->branch_id,
                'warehouse_id'   => $data['warehouse_id'] ?? null,
                'status'         => 'pending',
                'purpose'        => $data['purpose'] ?? null,
                'note'           => $data['note'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $createdRequest->items()->create([
                    'product_id'         => $item['product_id'],
                    'quantity_requested' => $item['quantity'],
                    'note'               => $item['note'] ?? null,
                ]);
            }

            AuditLog::log('request_create', 'ສ້າງຄຳຮ້ອງ #' . $createdRequest->request_no, $createdRequest);
        });

        // ແຈ້ງເຕືອນ admin / manager ທຸກຄົນ (ຍົກເວັ້ນຜູ້ສ້າງຄຳຮ້ອງ)
        if ($createdRequest) {
            $createdRequest->load('requester', 'items');

            $recipients = User::whereIn('role', ['super_admin', 'admin', 'manager'])
                ->where('is_active', true)
                ->where('id', '!=', Auth::id())
                ->get();

            Notification::send($recipients, new NewStockRequest($createdRequest));
        }

        return redirect()->route('requests.index')
            ->with('success', 'ສ້າງຄຳຮ້ອງຂໍສຳເລັດ ກຳລັງລໍຖ້າອະນຸມັດ');
    }

    public function show(StockRequest $request)
    {
        $request->load(['requester', 'approver', 'issuer', 'branch', 'warehouse', 'items.product.unit']);
        return view('requests.show', compact('request'));
    }

    public function approve(StockRequest $stockRequest)
    {
        if (!$this->authUser()->isManager()) {
            return back()->with('error', 'ທ່ານບໍ່ມີສິດດຳເນີນການນີ້');
        }
        if ($stockRequest->requester_id === Auth::id()) {
            return back()->with('error', 'ບໍ່ສາມາດອະນຸມັດຄຳຮ້ອງຂອງຕົນເອງໄດ້');
        }
        if (!$stockRequest->isPending()) {
            return back()->with('error', 'ສະຖານະຄຳຮ້ອງບໍ່ຖືກຕ້ອງ');
        }

        $stockRequest->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        AuditLog::log('request_approve', 'ອະນຸມັດຄຳຮ້ອງ ' . $stockRequest->request_no, $stockRequest);

        $stockRequest->requester?->notify(new StockRequestUpdated($stockRequest, 'approved'));

        return back()->with('success', 'ອະນຸມັດຄຳຮ້ອງ ' . $stockRequest->request_no . ' ສຳເລັດ');
    }

    public function reject(Request $request, StockRequest $stockRequest)
    {
        if (!$this->authUser()->isManager()) {
            return back()->with('error', 'ທ່ານບໍ່ມີສິດດຳເນີນການນີ້');
        }
        if ($stockRequest->requester_id === Auth::id()) {
            return back()->with('error', 'ບໍ່ສາມາດປະຕິເສດຄຳຮ້ອງຂອງຕົນເອງໄດ້');
        }
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $stockRequest->update([
            'status'           => 'rejected',
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        AuditLog::log('request_reject', 'ປະຕິເສດຄຳຮ້ອງ ' . $stockRequest->request_no, $stockRequest);

        $stockRequest->requester?->notify(new StockRequestUpdated($stockRequest, 'rejected'));

        return back()->with('success', 'ປະຕິເສດຄຳຮ້ອງ ' . $stockRequest->request_no . ' ແລ້ວ');
    }

    public function issue(StockRequest $stockRequest)
    {
        if (!$this->authUser()->isManager()) {
            return back()->with('error', 'ທ່ານບໍ່ມີສິດດຳເນີນການນີ້');
        }
        if (!$stockRequest->isApproved()) {
            return back()->with('error', 'ຕ້ອງອະນຸມັດກ່ອນ');
        }
        if (!$stockRequest->warehouse_id) {
            return back()->with('error', 'ຄຳຮ້ອງນີ້ບໍ່ໄດ້ລະບຸສາງ ບໍ່ສາມາດຈ່າຍໄດ້');
        }

        DB::transaction(function () use ($stockRequest) {
            foreach ($stockRequest->items as $item) {
                $warehouseId = $stockRequest->warehouse_id;

                $stock = WarehouseStock::where([
                    'warehouse_id' => $warehouseId,
                    'product_id'   => $item->product_id,
                ])->lockForUpdate()->first();

                $qtyToIssue = min($item->quantity_requested, $stock?->quantity ?? 0);

                if ($stock && $qtyToIssue > 0) {
                    $before = $stock->quantity;
                    $stock->decrement('quantity', $qtyToIssue);

                    StockMovement::create([
                        'type'            => 'out',
                        'reference_no'    => $stockRequest->request_no,
                        'product_id'      => $item->product_id,
                        'warehouse_id'    => $warehouseId,
                        'user_id'         => Auth::id(),
                        'quantity'        => $qtyToIssue,
                        'quantity_before' => $before,
                        'quantity_after'  => $before - $qtyToIssue,
                        'movement_date'   => now(),
                    ]);
                }

                $item->update(['quantity_issued' => $qtyToIssue]);
            }

            $stockRequest->update([
                'status'    => 'issued',
                'issued_by' => Auth::id(),
                'issued_at' => now(),
            ]);

            AuditLog::log('request_issue', 'ຈ່າຍສິນຄ້າຕາມຄຳຮ້ອງ ' . $stockRequest->request_no, $stockRequest);
        });

        $stockRequest->requester?->notify(new StockRequestUpdated($stockRequest, 'issued'));

        return back()->with('success', 'ຈ່າຍສິນຄ້າຕາມຄຳຮ້ອງ ' . $stockRequest->request_no . ' ສຳເລັດ');
    }

    public function receive(Request $request, StockRequest $stockRequest)
    {
        if (!$stockRequest->isIssued()) {
            return back()->with('error', 'ຕ້ອງຈ່າຍສິນຄ້າກ່ອນຈຶ່ງຢືນຢັນຮັບໄດ້');
        }
        if ($stockRequest->isReceived()) {
            return back()->with('error', 'ຢືນຢັນຮັບແລ້ວ');
        }

        $request->validate(['received_note' => 'nullable|string|max:500']);

        $stockRequest->update([
            'received_by'   => Auth::id(),
            'received_at'   => now(),
            'received_note' => $request->received_note,
        ]);

        AuditLog::log('request_receive', 'ຢືນຢັນຮັບສິນຄ້າຄຳຮ້ອງ ' . $stockRequest->request_no, $stockRequest);

        return back()->with('success', 'ຢືນຢັນຮັບສິນຄ້າ ' . $stockRequest->request_no . ' ສຳເລັດ');
    }

    public function pdf(StockRequest $stockRequest)
    {
        $stockRequest->load(['requester', 'approver', 'issuer', 'receiver', 'branch', 'warehouse', 'items.product.unit']);
        $company = [
            'name'    => Setting::get('company_name', 'ລະບົບສາງ'),
            'phone'   => Setting::get('company_phone', ''),
            'address' => Setting::get('company_address', ''),
        ];

        $fontNormal = base64_encode(file_get_contents(public_path('fonts/NotoSansLao-Regular.ttf')));
        $fontBold   = $fontNormal; // Bold glyph ຂາດ — ໃຊ້ regular ສຳລັບທຸກ weight

        $pdf = Pdf::loadView('requests.pdf', compact('stockRequest', 'company', 'fontNormal', 'fontBold'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled'      => false,
                'isHtml5ParserEnabled' => true,
                'defaultFont'          => 'serif',
            ]);

        return $pdf->download('ໃບເບີກ-' . $stockRequest->request_no . '.pdf');
    }

    public function destroy(StockRequest $request)
    {
        if (!$request->isPending()) {
            return back()->with('error', 'ສາມາດຍົກເລີກໄດ້ສະເພາະຄຳຮ້ອງທີ່ລໍຖ້າ');
        }
        $request->update(['status' => 'cancelled']);
        AuditLog::log('request_cancel', 'ຍົກເລີກຄຳຮ້ອງ ' . $request->request_no, $request);
        return back()->with('success', 'ຍົກເລີກຄຳຮ້ອງ ສຳເລັດ');
    }
}
