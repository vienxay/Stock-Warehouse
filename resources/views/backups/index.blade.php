@extends('layouts.app')
@section('title','BackUp ຂໍ້ມູນ')
@section('page_title','BackUp ຂໍ້ມູນ')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400">BackUp ທັງໝົດ</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalCount) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400">ຂະໜາດທັງໝົດ</p>
            <p class="text-2xl font-bold text-gray-800">
                @php
                    $ts = (int)$totalSize;
                    echo $ts >= 1048576 ? number_format($ts/1048576,2).' MB'
                       : ($ts >= 1024 ? number_format($ts/1024,2).' KB' : $ts.' B');
                @endphp
            </p>
        </div>
    </div>
</div>

{{-- Create Backup Card --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
    <h3 class="text-sm font-semibold text-gray-700 mb-4">ສ້າງ BackUp ໃໝ່</h3>
    <div class="flex flex-wrap gap-3">
        {{-- SQL --}}
        <form method="POST" action="{{ route('backups.store') }}"
              data-confirm="ສ້າງ SQL Backup ໃໝ່?">
            @csrf
            <input type="hidden" name="format" value="sql"/>
            <button type="submit"
                class="flex items-center gap-2.5 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                SQL Dump (.sql)
            </button>
        </form>

        {{-- Excel --}}
        <form method="POST" action="{{ route('backups.store') }}"
              data-confirm="ສ້າງ Excel Backup ໃໝ່?">
            @csrf
            <input type="hidden" name="format" value="excel"/>
            <button type="submit"
                class="flex items-center gap-2.5 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Excel (.xlsx)
            </button>
        </form>
    </div>

    <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl p-3.5 flex gap-2.5">
        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-xs text-amber-700 space-y-0.5">
            <p><strong>SQL Dump</strong> — ໄຟລ໌ .sql ທີ່ສາມາດ Restore ກັບຄືນໄດ້ຢ່າງສົມບູນ</p>
            <p><strong>Excel</strong> — ໄຟລ໌ .xlsx ທີ່ສາມາດເປີດໃນ Excel / Google Sheets ເພື່ອກວດສອບຂໍ້ມູນ</p>
        </div>
    </div>
</div>

{{-- Backup List --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-700 text-sm">ລາຍການ BackUp</h3>
        <span class="text-xs text-gray-400">{{ $backups->total() }} ລາຍການ</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">ຊື່ໄຟລ໌</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">ຮູບແບບ</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">ຂະໜາດ</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">ສ້າງໂດຍ</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">ວັນທີ</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">ຈັດການ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($backups as $backup)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            @if($backup->format === 'sql')
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                    </svg>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <span class="font-mono text-xs text-gray-700 break-all">{{ $backup->filename }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @if($backup->format === 'sql')
                            <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                SQL
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                Excel
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-right font-medium text-gray-700">
                        {{ $backup->formatted_size }}
                    </td>
                    <td class="px-4 py-3.5 text-gray-600">
                        {{ $backup->creator?->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-gray-500 text-xs">
                        {{ $backup->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('backups.download', $backup) }}"
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="ດາວໂຫຼດ">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('backups.destroy', $backup) }}"
                                data-confirm="ລຶບ {{ $backup->filename }}?">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="ລຶບ">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <svg class="w-14 h-14 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                        <p class="text-gray-400 font-medium">ຍັງບໍ່ມີ BackUp</p>
                        <p class="text-gray-400 text-xs mt-1">ກົດປຸ່ມດ້ານເທິງເພື່ອສ້າງ BackUp ໃໝ່</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($backups->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $backups->links() }}
    </div>
    @endif
</div>

@endsection
