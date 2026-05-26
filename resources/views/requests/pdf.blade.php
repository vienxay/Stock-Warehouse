<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8"/>
<style>
@font-face {
    font-family: 'Lao';
    font-weight: normal;
    src: url('data:font/truetype;base64,{{ $fontNormal }}') format('truetype');
}
@font-face {
    font-family: 'Lao';
    font-weight: bold;
    src: url('data:font/truetype;base64,{{ $fontBold }}') format('truetype');
}

* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Lao', sans-serif; font-size: 12px; color: #1f2937; background: #fff; }

.page { padding: 32px 38px; }

/* ===== HEADER ===== */
.header-table { width: 100%; border-collapse: collapse; padding-bottom: 14px; border-bottom: 2.5px solid #1d4ed8; margin-bottom: 16px; }
.hd-company { vertical-align: top; }
.hd-doc     { vertical-align: top; text-align: right; }
.company-name { font-size: 20px; font-weight: bold; color: #1d4ed8; }
.company-sub  { font-size: 10px; color: #6b7280; margin-top: 3px; }
.doc-title-text { font-size: 17px; font-weight: bold; color: #1f2937; }
.doc-no   { font-size: 13px; color: #1d4ed8; font-weight: bold; margin-top: 5px; }
.doc-date { font-size: 10px; color: #6b7280; margin-top: 3px; }

/* ===== STATUS BADGE ===== */
.status-wrap { text-align: center; margin-bottom: 16px; }
.badge-issued   { display: inline; padding: 4px 20px; font-size: 11px; font-weight: bold; border: 1px solid #86efac; background: #dcfce7; color: #166534; }
.badge-approved { display: inline; padding: 4px 20px; font-size: 11px; font-weight: bold; border: 1px solid #93c5fd; background: #dbeafe; color: #1e40af; }
.badge-pending  { display: inline; padding: 4px 20px; font-size: 11px; font-weight: bold; border: 1px solid #fcd34d; background: #fef9c3; color: #92400e; }

/* ===== INFO SECTION ===== */
.info-outer { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
.info-col   { width: 50%; vertical-align: top; }
.info-col-l { padding-right: 7px; }
.info-col-r { padding-left: 7px; }
.info-box   { border: 1px solid #dde3ee; background: #f8fafc; padding: 10px 13px; }
.info-box-title { font-size: 10px; font-weight: bold; color: #1d4ed8; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid #dde3ee; white-space: nowrap; }
.info-inner { width: 100%; border-collapse: collapse; }
.info-inner td { padding: 2px 0; vertical-align: top; font-size: 11px; }
.ik { color: #6b7280; width: 90px; }
.iv { font-weight: bold; color: #1f2937; }

/* ===== ITEMS TABLE ===== */
.section-label { font-size: 11px; font-weight: bold; color: #374151; margin-bottom: 8px; padding: 0 0 0 9px; border-left: 3px solid #1d4ed8; }
.items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
.items thead tr   { background: #1d4ed8; }
.items thead th   { color: #fff; font-size: 11px; font-weight: bold; padding: 8px 10px; text-align: left; white-space: nowrap; }
.items thead th.r { text-align: right; white-space: nowrap; }
.items thead th.c { text-align: center; white-space: nowrap; }
.items tbody tr.even { background: #f5f7ff; }
.items tbody td   { padding: 7px 10px; font-size: 11px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
.items tbody td.r { text-align: right; }
.items tbody td.c { text-align: center; }
.items tfoot td   { background: #eef2f9; font-weight: bold; padding: 7px 10px; font-size: 11px; border-top: 2px solid #c7d2e7; white-space: nowrap; }
.items tfoot td.r { text-align: right; white-space: nowrap; }
.pname { font-weight: bold; color: #1f2937; }
.punit { font-size: 10px; color: #9ca3af; margin-top: 1px; }
.qty-ok   { font-weight: bold; color: #166534; }
.qty-zero { color: #ef4444; }

/* ===== NOTE BOX ===== */
.note-box { background: #fffbeb; border: 1px solid #fcd34d; padding: 9px 13px; margin-bottom: 16px; font-size: 11px; color: #92400e; }
.note-lbl { font-weight: bold; margin-bottom: 3px; }

/* ===== SIGNATURES ===== */
.sig-outer { width: 100%; border-collapse: collapse; margin-top: 36px; }
.sig-cell  { width: 25%; text-align: center; vertical-align: bottom; padding: 0 6px; }
.sig-space { height: 50px; }
.sig-line  { border-top: 1px solid #374151; padding-top: 6px; margin-top: 0; }
.sig-role  { font-size: 10px; font-weight: bold; color: #374151; }
.sig-name  { font-size: 10px; color: #6b7280; margin-top: 3px; }

/* ===== FOOTER ===== */
.footer { margin-top: 22px; padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 10px; color: #9ca3af; }
</style>
</head>
<body>
<div class="page">

{{-- ===== HEADER ===== --}}
<table class="header-table">
    <tr>
        <td class="hd-company">
            <div class="company-name">{{ $company['name'] }}</div>
            @if($company['phone'])<div class="company-sub">ໂທ: {{ $company['phone'] }}</div>@endif
            @if($company['address'])<div class="company-sub">{{ $company['address'] }}</div>@endif
        </td>
        <td class="hd-doc">
            <div class="doc-title-text">ໃບເບີກສິນຄ້າ</div>
            <div class="doc-no">{{ $stockRequest->request_no }}</div>
            <div class="doc-date">ວັນທີ: {{ $stockRequest->created_at->format('d/m/Y') }}</div>
        </td>
    </tr>
</table>

{{-- ===== STATUS ===== --}}
<div class="status-wrap">
    @if($stockRequest->isReceived())
        <span class="badge-issued">ຮັບສິນຄ້າແລ້ວ</span>
    @elseif($stockRequest->isIssued())
        <span class="badge-issued">ຈ່າຍສຳເລັດ</span>
    @elseif($stockRequest->status === 'approved')
        <span class="badge-approved">ອະນຸມັດແລ້ວ</span>
    @else
        <span class="badge-pending">ລໍຖ້າອະນຸມັດ</span>
    @endif
</div>

{{-- ===== INFO GRID ===== --}}
<table class="info-outer">
    <tr>
        <td class="info-col info-col-l">
            <div class="info-box">
                <div class="info-box-title">ຂໍ້ມູນຜູ້ຮ້ອງຂໍ</div>
                <table class="info-inner">
                    <tr><td class="ik">ຊື່</td><td class="iv">{{ $stockRequest->requester_name ?? $stockRequest->requester?->name ?? '-' }}</td></tr>
                    <tr><td class="ik">ສາຂາ</td><td class="iv">{{ $stockRequest->branch?->name ?? '-' }}</td></tr>
                    <tr><td class="ik">ວັນທີຂໍ</td><td class="iv">{{ $stockRequest->created_at->format('d/m/Y H:i') }}</td></tr>
                </table>
            </div>
        </td>
        <td class="info-col info-col-r">
            <div class="info-box">
                <div class="info-box-title">ຂໍ້ມູນການເບີກ</div>
                <table class="info-inner">
                    <tr><td class="ik">ສາງ</td><td class="iv">{{ $stockRequest->warehouse?->name ?? '-' }}</td></tr>
                    <tr><td class="ik">ຈຸດປະສົງ</td><td class="iv">{{ $stockRequest->purpose ?? '-' }}</td></tr>
                    @if($stockRequest->issued_at)
                    <tr><td class="ik">ວັນທີຈ່າຍ</td><td class="iv">{{ $stockRequest->issued_at->format('d/m/Y H:i') }}</td></tr>
                    @endif
                    @if($stockRequest->approved_by)
                    <tr><td class="ik">ຜູ້ອະນຸມັດ</td><td class="iv">{{ $stockRequest->approver?->name ?? '-' }}</td></tr>
                    @endif
                </table>
            </div>
        </td>
    </tr>
</table>

{{-- ===== ITEMS TABLE ===== --}}
<div class="section-label">ລາຍການສິນຄ້າ</div>
<table class="items">
    <thead>
        <tr>
            <th class="c" style="width:28px">#</th>
            <th>ສິນຄ້າ</th>
            <th class="r" style="width:75px">ຈຳນວນຂໍ</th>
            <th class="r" style="width:85px">ຈຳນວນຈ່າຍ</th>
            <th style="width:110px">ໝາຍເຫດ</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stockRequest->items as $i => $item)
        <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
            <td class="c">{{ $i + 1 }}</td>
            <td>
                <div class="pname">{{ $item->product?->name }}</div>
                <div class="punit">{{ $item->product?->unit?->name }}</div>
            </td>
            <td class="r">{{ number_format($item->quantity_requested) }}</td>
            <td class="r">
                <span class="{{ $item->quantity_issued > 0 ? 'qty-ok' : 'qty-zero' }}">
                    {{ number_format($item->quantity_issued) }}
                </span>
            </td>
            <td>{{ $item->note ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">ລວມທັງໝົດ {{ $stockRequest->items->count() }} ລາຍການ</td>
            <td class="r">{{ number_format($stockRequest->items->sum('quantity_requested')) }}</td>
            <td class="r">{{ number_format($stockRequest->items->sum('quantity_issued')) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

{{-- ===== NOTE ===== --}}
@if($stockRequest->note)
<div class="note-box">
    <div class="note-lbl">ໝາຍເຫດ:</div>
    {{ $stockRequest->note }}
</div>
@endif

@if($stockRequest->rejection_reason)
<div class="note-box" style="background:#fef2f2;border-color:#fca5a5;color:#991b1b;">
    <div class="note-lbl">ເຫດຜົນປະຕິເສດ:</div>
    {{ $stockRequest->rejection_reason }}
</div>
@endif

{{-- ===== SIGNATURES ===== --}}
<table class="sig-outer">
    <tr>
        <td class="sig-cell">
            <div class="sig-space"></div>
            <div class="sig-line">
                <div class="sig-role">ຜູ້ຮ້ອງຂໍ</div>
                <div class="sig-name">{{ $stockRequest->requester_name ?? $stockRequest->requester?->name }}</div>
            </div>
        </td>
        <td class="sig-cell">
            <div class="sig-space"></div>
            <div class="sig-line">
                <div class="sig-role">ຜູ້ອະນຸມັດ</div>
                <div class="sig-name">{{ $stockRequest->approver?->name ?? '................................' }}</div>
            </div>
        </td>
        <td class="sig-cell">
            <div class="sig-space"></div>
            <div class="sig-line">
                <div class="sig-role">ຜູ້ຈ່າຍ</div>
                <div class="sig-name">{{ $stockRequest->issuer?->name ?? '................................' }}</div>
            </div>
        </td>
        <td class="sig-cell">
            <div class="sig-space"></div>
            <div class="sig-line">
                <div class="sig-role">ຜູ້ຮັບ</div>
                <div class="sig-name">{{ $stockRequest->receiver?->name ?? '................................' }}</div>
            </div>
        </td>
    </tr>
</table>

{{-- ===== FOOTER ===== --}}
<div class="footer">
    ພິມວັນທີ {{ now()->format('d/m/Y H:i') }} &nbsp;·&nbsp; {{ $company['name'] }}
</div>

</div>
</body>
</html>
