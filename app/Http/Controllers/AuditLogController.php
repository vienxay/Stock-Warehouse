<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        $logs    = $query->paginate(25)->withQueryString();
        $users   = User::orderBy('name')->get();
        $actions = array_keys(AuditLog::ACTION_CONFIG);

        $todayCount  = AuditLog::whereDate('created_at', today())->count();
        $weekCount   = AuditLog::where('created_at', '>=', now()->startOfWeek())->count();
        $activeUsers = AuditLog::whereDate('created_at', today())
                               ->whereNotNull('user_id')
                               ->distinct('user_id')
                               ->count('user_id');

        return view('audit.index', compact(
            'logs', 'users', 'actions',
            'todayCount', 'weekCount', 'activeUsers'
        ));
    }

    public function destroy(AuditLog $auditLog)
    {
        $auditLog->delete();
        return back()->with('success', 'ລຶບ Log ສຳເລັດ');
    }

    public function clear(Request $request)
    {
        $request->validate(['days' => 'required|integer|min:1|max:365']);

        $count = AuditLog::where('created_at', '<', now()->subDays($request->days))->count();
        AuditLog::where('created_at', '<', now()->subDays($request->days))->delete();

        AuditLog::log('audit_clear', "ລຶບ Audit Log ເກົ່າກວ່າ {$request->days} ວັນ ({$count} ລາຍການ)");

        return back()->with('success', "ລຶບ Log ເກົ່າກວ່າ {$request->days} ວັນ ທັງໝົດ {$count} ລາຍການ");
    }
}
