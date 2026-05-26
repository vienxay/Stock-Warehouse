<?php

namespace App\Http\Controllers;

use App\Exports\BackupExport;
use App\Models\AuditLog;
use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class BackupController extends Controller
{
    const SKIP_TABLES = [
        'migrations', 'sessions', 'cache', 'cache_locks',
        'jobs', 'job_batches', 'failed_jobs', 'password_reset_tokens',
        'personal_access_tokens',
    ];

    public function index()
    {
        $backups    = Backup::with('creator')->latest()->paginate(15);
        $totalSize  = Backup::sum('size');
        $totalCount = Backup::count();
        return view('backups.index', compact('backups', 'totalSize', 'totalCount'));
    }

    public function store(Request $request)
    {
        $request->validate(['format' => 'required|in:sql,excel']);

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_His');

        try {
            if ($request->format === 'sql') {
                $filename = "backup_{$timestamp}.sql";
                $filePath = $backupDir . '/' . $filename;
                $this->generateSql($filePath);
            } else {
                $filename = "backup_{$timestamp}.xlsx";
                $filePath = $backupDir . '/' . $filename;
                $content  = Excel::raw(new BackupExport(), \Maatwebsite\Excel\Excel::XLSX);
                file_put_contents($filePath, $content);
            }

            Backup::create([
                'filename'   => $filename,
                'path'       => 'backups/' . $filename,
                'size'       => file_exists($filePath) ? filesize($filePath) : 0,
                'type'       => 'manual',
                'created_by' => auth()->id(),
            ]);

            AuditLog::log('backup_create', 'ສ້າງ BackUp "' . $filename . '"');

            return redirect()->route('backups.index')
                ->with('success', 'ສ້າງ BackUp "' . $filename . '" ສຳເລັດ');

        } catch (\Throwable $e) {
            return redirect()->route('backups.index')
                ->with('error', 'ສ້າງ BackUp ລົ້ມເຫລວ: ' . $e->getMessage());
        }
    }

    public function download(Backup $backup)
    {
        $filePath = storage_path('app/' . $backup->path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'ໄຟລ໌ BackUp ບໍ່ພົບໃນລະບົບ');
        }

        $mime = $backup->format === 'sql'
            ? 'application/sql'
            : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

        return response()->download($filePath, $backup->filename, ['Content-Type' => $mime]);
    }

    public function destroy(Backup $backup)
    {
        $filePath = storage_path('app/' . $backup->path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $deletedFilename = $backup->filename;
        $backup->delete();
        AuditLog::log('backup_delete', 'ລຶບ BackUp "' . $deletedFilename . '"');
        return back()->with('success', 'ລຶບ BackUp "' . $deletedFilename . '" ສຳເລັດ');
    }

    // ─── SQL Generation ──────────────────────────────────────────

    private function getTables(): array
    {
        $db   = config('database.connections.mysql.database');
        $rows = DB::select('SHOW TABLES');
        $key  = 'Tables_in_' . $db;

        return collect($rows)
            ->map(fn($r) => $r->$key)
            ->reject(fn($t) => in_array($t, self::SKIP_TABLES))
            ->values()
            ->toArray();
    }

    private function generateSql(string $filePath): void
    {
        $db     = config('database.connections.mysql.database');
        $handle = fopen($filePath, 'w');

        fwrite($handle, "-- ================================================\n");
        fwrite($handle, "-- Stock Management System — SQL Backup\n");
        fwrite($handle, "-- Generated : " . now()->format('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- Database  : {$db}\n");
        fwrite($handle, "-- ================================================\n\n");
        fwrite($handle, "SET NAMES utf8mb4;\n");
        fwrite($handle, "SET CHARACTER SET utf8mb4;\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

        foreach ($this->getTables() as $table) {
            if (!Schema::hasTable($table)) continue;
            fwrite($handle, $this->dumpTable($table));
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($handle);
    }

    private function dumpTable(string $table): string
    {
        $sql  = "-- ------------------------------------------------\n";
        $sql .= "-- Table: `{$table}`\n";
        $sql .= "-- ------------------------------------------------\n";
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

        $create = DB::select("SHOW CREATE TABLE `{$table}`");
        $sql   .= ($create[0]->{'Create Table'} ?? '') . ";\n\n";

        $rows = DB::table($table)->get();
        if ($rows->isEmpty()) {
            return $sql . "\n";
        }

        $columns = array_keys((array) $rows->first());
        $colList = '`' . implode('`, `', $columns) . '`';
        $sql    .= "INSERT INTO `{$table}` ({$colList}) VALUES\n";

        $inserts = [];
        foreach ($rows as $row) {
            $vals = array_map(function ($v) {
                if ($v === null) return 'NULL';
                if (is_int($v) || is_float($v)) return $v;
                return "'" . str_replace(
                    ["\\",  "'",    "\r",   "\n",   "\x1a"],
                    ["\\\\","\\\'","\\r",  "\\n",  "\\Z"],
                    (string) $v
                ) . "'";
            }, array_values((array) $row));

            $inserts[] = '(' . implode(', ', $vals) . ')';
        }

        $sql .= implode(",\n", $inserts) . ";\n\n";
        return $sql;
    }
}
