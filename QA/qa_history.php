<?php include '../sidebar.php'; ?>
<?php
if (!isset($_SESSION['user_namefl'])) {
    header('Location: ../login.php');
    exit;
}
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QA Inspection History</title>
    <link rel="stylesheet" href="../css/qa.css">
    <style>
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
            align-items: center;
        }
        .search-bar input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            height: 36px;
        }
        .search-bar button {
            width: auto;
            padding: 8px 20px;
            font-size: 14px;
        }
        .history-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            font-size: 13px;
        }

        #resultsContainer {
            overflow-x: auto;
            width: 100%;
        }

        .history-table td:nth-child(2),
        .history-table td:nth-child(10) {
            white-space: nowrap;
        }
        .history-table th {
            background: #f0f0f0;
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
            border-bottom: 2px solid #ddd;
            white-space: nowrap;
        }
        .history-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
            vertical-align: middle;
        }
        .history-table tr:hover td {
            background: #f9f9f9;
            cursor: pointer;
        }
        .history-table tr.reject-row td {
            background: #fef2f2;
        }
        .history-table tr.reject-row:hover td {
            background: #fde8e8;
        }
        .badge-accept {
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #86efac;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .badge-reject {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .badge-progress {
            background: #fef9c3;
            color: #a16207;
            border: 1px solid #fde047;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .no-results {
            text-align: center;
            color: #bbb;
            font-size: 13px;
            padding: 32px;
            letter-spacing: 1px;
        }
        /* Detail modal table */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 12px;
        }
        .detail-table th {
            background: #f5f5f5;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #777;
            border-bottom: 1px solid #ddd;
        }
        .detail-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
            vertical-align: top;
        }
        .detail-table tr.ng-row td {
            background: #fff5f5;
        }
        .lot-summary {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }
        .summary-box {
            flex: 1;
            min-width: 100px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }
        .summary-box .sb-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #888;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .summary-box .sb-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .summary-box .sb-value.accept { color: #16a34a; }
        .summary-box .sb-value.reject { color: #dc2626; }
        .summary-box .sb-value.blue   { color: #4facfe; }
        .result-banner {
            flex: none !important;
            width: 100%;
            padding: 14px !important;
            border-width: 2px !important;
        }
        .result-banner.rb-accept {
            background: #dcfce7 !important;
            border-color: #86efac !important;
        }
        .result-banner.rb-reject {
            background: #fee2e2 !important;
            border-color: #fca5a5 !important;
        }
        .result-banner.rb-progress {
            background: #fef9c3 !important;
            border-color: #fde047 !important;
        }
        .result-banner .sb-label {
            font-size: 11px;
        }
        .result-banner .sb-value {
            font-size: 26px !important;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
<div class="form-container">

    <h1 style="text-align:center; margin-bottom:20px; font-size:22px; color:#333;">QA Inspection History</h1>

    <div class="form-section">
        <h3>Search Lot</h3>
        <div class="search-bar">
            <input type="text" id="lot_search" placeholder="Enter KEPI Lot No." autocomplete="off">
            <button id="searchBtn" style="width:auto; padding:8px 24px;">SEARCH</button>
            <button id="clearBtn" class="btn-ghost" style="padding:8px 16px;">CLEAR</button>
        </div>
    </div>

    <div class="form-section" id="resultsSection" style="display:none;">
        <h3>Results</h3>
        <div id="resultsContainer"></div>
    </div>

</div>

<!-- ── DETAIL MODAL ── -->
<div class="modal" id="detailModal">
    <div class="modal-content" style="max-width:720px;">
        <span class="close" id="closeDetailModal">&times;</span>
        <h2 id="modalTitle">Lot Detail</h2>
        <div id="modalBody"></div>
        <div class="modal-footer">
            <button class="btn-ghost" id="printDetailBtn" style="padding:8px 20px; font-size:14px;">PRINT</button>
            <button class="btn-ghost" id="closeDetailBtn" style="padding:8px 20px; font-size:14px;">CLOSE</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentDetailData = null;
let currentDetailInspection = null;
let showAllSerials           = false;

$('#searchBtn').on('click', doSearch);
$('#lot_search').on('keydown', function(e) {
    if (e.key === 'Enter') doSearch();
});

$('#clearBtn').on('click', function() {
    $('#lot_search').val('');
    $('#resultsSection').hide();
    $('#resultsContainer').html('');
});

function doSearch() {
    const lot = $('#lot_search').val().trim().toUpperCase();
    if (!lot) {
        Swal.fire({ icon:'warning', title:'Enter a lot number', toast:true,
            position:'top-right', timer:2000, showConfirmButton:false });
        return;
    }

    $.ajax({
        url: '/traceabilitydev/QA/fetch_lot_history.php',
        type: 'POST',
        data: { kepi_lot: lot },
        success: function(response) {
            $('#resultsSection').show();
            if (!response.success || !response.data.length) {
                $('#resultsContainer').html('<div class="no-results">No records found for lot <b>' + lot + '</b></div>');
                return;
            }
            renderTable(response.data);
        },
        error: function() {
            Swal.fire({ icon:'error', title:'Network Error', text:'Failed to fetch lot history.' });
        }
    });
}

function renderTable(rows) {
    const html = `
        <table class="history-table">
            <thead>
                <tr>
                    <th>KEPI Lot No.</th>
                    <th>Attempt</th>
                    <th>Model</th>
                    <th>Lot Qty</th>
                    <th>Sample Size</th>
                    <th>Method</th>
                    <th>Line</th>
                    <th>Shift</th>
                    <th>Operator</th>
                    <th>Date</th>
                    <th>Result</th>
                    <th>NG Units</th>
                </tr>
            </thead>
            <tbody>
                ${rows.map(r => `
               <tr class="lot-row ${r.status !== 'IN_PROGRESS' && r.lot_result === 'REJECT' ? 'reject-row' : ''}" data-inspection="${r.inspection_id}">
                    <td><b>${r.kepi_lot}</b></td>
                    <td style="text-align:center;">${r.attempt_number > 1 ? 'Re-Inspection #' + r.attempt_number : '#' + r.attempt_number}</td>
                    <td>${r.model}</td>
                    <td>${r.lot_quantity}</td>
                    <td>${r.sample_size}</td>
                    <td>${capitalize(r.inspection_method)}</td>
                    <td>${r.line}</td>
                    <td>${r.shift}</td>
                    <td>${r.operator_id}</td>
                    <td>${formatDate(r.created_at)}</td>
                    <td>${resultBadge(r.status, r.lot_result)}</td>
                    <td style="text-align:center;">${r.status === 'IN_PROGRESS' ? '—' : r.ng_count}</td>
                </tr>`).join('')}
            </tbody>
        </table>
        <div style="font-size:12px; color:#aaa; margin-top:8px; text-align:right;">Click a row to view serial details</div>
    `;
    $('#resultsContainer').html(html);

    $('.lot-row').on('click', function() {
        const inspectionId = $(this).data('inspection');
        const lot          = $(this).find('td').first().text();
        openDetailModal(inspectionId, lot);
    });
}

$(document).on('change', '#toggleShowAll', function() {
    showAllSerials = $(this).is(':checked');
    $('#detailTableWrapper').html(renderDetailTable(currentDetailData.serials, showAllSerials));
});

function openDetailModal(inspectionId, lotLabel) {
    $.ajax({
        url: '/traceabilitydev/QA/fetch_lot_detail.php',
        type: 'POST',
        data: { inspection_id: inspectionId },
        success: function(response) {
            if (!response.success) {
                Swal.fire({ icon:'error', title:'Error', text:'Failed to load lot detail.' });
                return;
            }
            const d = response.data;
            currentDetailData       = d;
            currentDetailInspection = inspectionId;
            showAllSerials           = false;   // reset toggle on every fresh open

            $('#modalTitle').text(`Lot: ${d.kepi_lot} — Attempt #${d.attempt_number}`);

            const ngCount   = d.serials.filter(s => s.status === 'NO GOOD').length;
            const goodCount = d.serials.filter(s => s.status === 'GOOD').length;
            const resultClass = d.status === 'IN_PROGRESS' ? 'rb-progress' : (d.lot_result === 'ACCEPT' ? 'rb-accept' : 'rb-reject');

            $('#modalBody').html(`
            <div class="lot-summary">
                <div class="summary-box result-banner ${resultClass}">
                    <div class="sb-label">Result</div>
                    <div class="sb-value ${d.status === 'IN_PROGRESS' ? '' : (d.lot_result === 'ACCEPT' ? 'accept' : 'reject')}" style="${d.status === 'IN_PROGRESS' ? 'color:#a16207;' : ''}">${d.status === 'IN_PROGRESS' ? 'IN PROGRESS' : d.lot_result}</div>
                </div>
            </div>

            <div class="lot-summary">
                <div class="summary-box">
                    <div class="sb-label">Judgement</div>
                    <div class="sb-value" style="font-size:13px;">${d.judgement || '—'}</div>
                </div>
                <div class="summary-box">
                    <div class="sb-label">Method</div>
                    <div class="sb-value" style="font-size:14px;">${capitalize(d.inspection_method)}</div>
                </div>
                <div class="summary-box">
                    <div class="sb-label">Level</div>
                    <div class="sb-value" style="font-size:14px;">${d.inspection_level || '—'}</div>
                </div>
                <div class="summary-box">
                    <div class="sb-label">Sample Size</div>
                    <div class="sb-value blue">${d.sample_size}</div>
                </div>
                <div class="summary-box">
                    <div class="sb-label">Good</div>
                    <div class="sb-value accept">${goodCount}</div>
                </div>
                <div class="summary-box">
                    <div class="sb-label">NG</div>
                    <div class="sb-value reject">${ngCount}</div>
                </div>
            </div>

            <div class="lot-summary">
                <div class="summary-box">
                    <div class="sb-label">Model</div>
                    <div class="sb-value" style="font-size:14px;">${d.model || '—'}</div>
                </div>
                <div class="summary-box">
                    <div class="sb-label">Lot Qty</div>
                    <div class="sb-value blue">${d.lot_quantity || '—'}</div>
                </div>
                <div class="summary-box">
                    <div class="sb-label">Line</div>
                    <div class="sb-value" style="font-size:14px;">${d.line || '—'}</div>
                </div>
                <div class="summary-box">
                    <div class="sb-label">Shift</div>
                    <div class="sb-value" style="font-size:14px;">${d.shift || '—'}</div>
                </div>
                <div class="summary-box">
                    <div class="sb-label">Customer</div>
                    <div class="sb-value" style="font-size:13px;">${d.customer || '—'}</div>
                </div>
                <div class="summary-box">
                    <div class="sb-label">Assy No.</div>
                    <div class="sb-value" style="font-size:13px;">${d.assy_no || '—'}</div>
                </div>
            </div>

            ${d.reference_no ? `<div style="font-size:13px; color:#555; margin-bottom:12px;"><b>Reference No.:</b> ${d.reference_no}</div>` : ''}

            ${(d.parts_appearance || d.pcb_appearance || d.solder_condition || d.labels_markings || d.subassembly_condition || d.package_condition) ? `
            <div style="font-size:12px; background:#f9f9f9; border:1px solid #eee; border-radius:6px; padding:10px 14px; margin-bottom:12px;">
                <div style="font-weight:bold; text-transform:uppercase; font-size:10px; letter-spacing:1px; color:#888; margin-bottom:8px;">Defect Category Summary</div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:4px 16px; color:#444;">
                    ${d.parts_appearance       ? `<div><b>Parts Appearance:</b> ${d.parts_appearance}</div>` : ''}
                    ${d.pcb_appearance         ? `<div><b>PCB Appearance:</b> ${d.pcb_appearance}</div>` : ''}
                    ${d.solder_condition       ? `<div><b>Solder Condition:</b> ${d.solder_condition}</div>` : ''}
                    ${d.subassembly_condition  ? `<div><b>Sub Assembly Condition:</b> ${d.subassembly_condition}</div>` : ''}
                    ${d.labels_markings        ? `<div><b>Labels/Markings:</b> ${d.labels_markings}</div>` : ''}
                    ${d.package_condition      ? `<div><b>Package Condition:</b> ${d.package_condition}</div>` : ''}
                </div>
            </div>` : ''}

            <div style="display:flex; justify-content:flex-end; align-items:center; gap:8px; margin-bottom:8px;">
                <label style="font-size:12px; color:#666; display:flex; align-items:center; gap:6px; cursor:pointer;">
                    <input type="checkbox" id="toggleShowAll">
                    Show all serials (including GOOD)
                </label>
            </div>

            <div id="detailTableWrapper">
                ${renderDetailTable(d.serials, showAllSerials)}
            </div>
        `);

            $('#detailModal').addClass('active');
        },
        error: function() {
            Swal.fire({ icon:'error', title:'Network Error', text:'Failed to load serial details.' });
        }
    });
}

function renderDetailTable(serials, showAll) {
    const filtered = showAll ? serials : serials.filter(s => s.status === 'NO GOOD');

    if (!filtered.length) {
        return `<div class="empty-state">${showAll ? 'No serials recorded.' : 'No NG units — all serials passed.'}</div>`;
    }

    return `
        <table class="detail-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Serial</th>
                    <th>Status</th>
                    <th>Parts Spec</th>
                    <th>Defects</th>
                    <th>Scanned By</th>
                </tr>
            </thead>
            <tbody>
                ${filtered.map((s, i) => `
                <tr class="${s.status === 'NO GOOD' ? 'ng-row' : ''}">
                    <td style="color:#aaa; font-size:11px;">${String(i+1).padStart(2,'0')}</td>
                    <td style="font-family:monospace;">${s.serial_code}</td>
                    <td><span class="${s.status === 'GOOD' ? 'status-good' : 'status-ng'}" style="font-weight:bold; font-size:11px; letter-spacing:1px;">${s.status}</span></td>
                    <td style="font-size:12px; color:#4facfe;">${s.parts_specification || '—'}</td>
                    <td style="font-size:12px; color:#666;">${formatDefects(s.defects)}</td>
                    <td style="font-size:12px; color:#666;">${s.scanned_by || '—'}</td>
                </tr>`).join('')}
            </tbody>
        </table>
    `;
}

$('#printDetailBtn').on('click', function() {
    if (!currentDetailData) return;
    printLotDetail(currentDetailData.kepi_lot, currentDetailData);
});

function printLotDetail(lot, d) {
    const ngCount   = d.serials.filter(s => s.status === 'NO GOOD').length;
    const goodCount = d.serials.filter(s => s.status === 'GOOD').length;
    const printSerials = showAllSerials ? d.serials : d.serials.filter(s => s.status === 'NO GOOD');
    const resultClass = d.lot_result === 'ACCEPT' ? 'rb-accept' : 'rb-reject';


    const printHtml = `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>QA Lot Detail - ${lot}</title>
        <style>
            body { font-family: Arial, sans-serif; color:#222; padding: 24px; }
            h1 { font-size: 18px; margin-bottom: 4px; }
            .sub { font-size: 12px; color:#666; margin-bottom: 18px; }
            .lot-summary { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:12px; }
            .summary-box { flex:1; min-width:80px; border:1px solid #ccc; border-radius:6px; padding:8px; text-align:center; }
            .sb-label { font-size:10px; font-weight:bold; text-transform:uppercase; color:#888; letter-spacing:1px; margin-bottom:4px; }
            .sb-value { font-size:15px; font-weight:bold; }
            .accept { color:#16a34a; }
            .reject { color:#dc2626; }
            .blue   { color:#4facfe; }
            .meta-row { font-size:12px; color:#555; margin-bottom:10px; display:flex; flex-wrap:wrap; gap:6px 20px; }
            .remarks-box { font-size:11px; background:#f9f9f9; border:1px solid #eee; border-radius:4px; padding:8px 12px; margin-bottom:12px; }
            .remarks-box b { text-transform:uppercase; font-size:10px; letter-spacing:1px; color:#888; display:block; margin-bottom:6px; }
            .remarks-grid { display:grid; grid-template-columns:1fr 1fr; gap:3px 14px; }
            table { width:100%; border-collapse:collapse; font-size:12px; margin-top:10px; }
            th { background:#f0f0f0; padding:6px 8px; text-align:left; font-size:10px; text-transform:uppercase; letter-spacing:1px; color:#555; border-bottom:2px solid #ddd; }
            td { padding:6px 8px; border-bottom:1px solid #eee; }
            tr.ng-row td { background:#fff5f5; }
            .status-good { color:#16a34a; }
            .status-ng   { color:#dc2626; }
            .result-banner { flex:none; width:100%; padding:14px; border-width:2px; }
            .result-banner.rb-accept { background:#dcfce7; border-color:#86efac; }
            .result-banner.rb-reject { background:#fee2e2; border-color:#fca5a5; }
            .result-banner .sb-label { font-size:11px; }
            .result-banner .sb-value { font-size:22px; letter-spacing:1px; }
            @media print { body { padding: 0; } }
        </style>
    </head>
    <body>
        <h1>QA Inspection Detail — Lot ${lot} (Attempt #${d.attempt_number})</h1>
        <div class="sub">Printed ${new Date().toLocaleString('en-PH')}</div>

        <div class="lot-summary">
            <div class="summary-box result-banner ${resultClass}">
                <div class="sb-label">Result</div>
                <div class="sb-value ${d.lot_result === 'ACCEPT' ? 'accept' : 'reject'}">${d.lot_result}</div>
            </div>
        </div>

        <div class="lot-summary">
            <div class="summary-box">
                <div class="sb-label">Judgement</div>
                <div class="sb-value" style="font-size:12px;">${d.judgement || '—'}</div>
            </div>
            <div class="summary-box">
                <div class="sb-label">Method</div>
                <div class="sb-value" style="font-size:13px;">${capitalize(d.inspection_method)}</div>
            </div>
            <div class="summary-box">
                <div class="sb-label">Level</div>
                <div class="sb-value" style="font-size:13px;">${d.inspection_level || '—'}</div>
            </div>
            <div class="summary-box">
                <div class="sb-label">Sample Size</div>
                <div class="sb-value blue">${d.sample_size}</div>
            </div>
            <div class="summary-box">
                <div class="sb-label">Good</div>
                <div class="sb-value accept">${goodCount}</div>
            </div>
            <div class="summary-box">
                <div class="sb-label">NG</div>
                <div class="sb-value reject">${ngCount}</div>
            </div>
        </div>

        <div class="lot-summary">
            <div class="summary-box">
                <div class="sb-label">Model</div>
                <div class="sb-value" style="font-size:13px;">${d.model || '—'}</div>
            </div>
            <div class="summary-box">
                <div class="sb-label">Lot Qty</div>
                <div class="sb-value blue">${d.lot_quantity || '—'}</div>
            </div>
            <div class="summary-box">
                <div class="sb-label">Line</div>
                <div class="sb-value" style="font-size:13px;">${d.line || '—'}</div>
            </div>
            <div class="summary-box">
                <div class="sb-label">Shift</div>
                <div class="sb-value" style="font-size:13px;">${d.shift || '—'}</div>
            </div>
            <div class="summary-box">
                <div class="sb-label">Customer</div>
                <div class="sb-value" style="font-size:12px;">${d.customer || '—'}</div>
            </div>
            <div class="summary-box">
                <div class="sb-label">Assy No.</div>
                <div class="sb-value" style="font-size:12px;">${d.assy_no || '—'}</div>
            </div>
        </div>

        ${d.reference_no ? `<div class="meta-row"><span><b>Reference No.:</b> ${d.reference_no}</span></div>` : ''}

        ${(d.parts_appearance || d.pcb_appearance || d.solder_condition || d.labels_markings || d.subassembly_condition || d.package_condition) ? `
        <div class="remarks-box">
            <b>Defect Category Summary</b>
            <div class="remarks-grid">
                ${d.parts_appearance      ? `<div><b>Parts Appearance:</b> ${d.parts_appearance}</div>` : ''}
                ${d.pcb_appearance        ? `<div><b>PCB Appearance:</b> ${d.pcb_appearance}</div>` : ''}
                ${d.solder_condition      ? `<div><b>Solder Condition:</b> ${d.solder_condition}</div>` : ''}
                ${d.subassembly_condition ? `<div><b>Sub Assembly Condition:</b> ${d.subassembly_condition}</div>` : ''}
                ${d.labels_markings       ? `<div><b>Labels/Markings:</b> ${d.labels_markings}</div>` : ''}
                ${d.package_condition     ? `<div><b>Package Condition:</b> ${d.package_condition}</div>` : ''}
            </div>
        </div>` : ''}

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Serial</th>
                    <th>Status</th>
                    <th>Parts Spec</th>
                    <th>Defects</th>
                    <th>Scanned By</th>
                </tr>
            </thead>
            <tbody>
                ${printSerials.map((s, i) => `
                <tr class="${s.status === 'NO GOOD' ? 'ng-row' : ''}">
                    <td>${String(i+1).padStart(2,'0')}</td>
                    <td style="font-family:monospace;">${s.serial_code}</td>
                    <td class="${s.status === 'GOOD' ? 'status-good' : 'status-ng'}"><b>${s.status}</b></td>
                    <td style="color:#4facfe;">${s.parts_specification || '—'}</td>
                    <td>${formatDefects(s.defects)}</td>
                    <td>${s.scanned_by || '—'}</td>
                </tr>`).join('')}
            </tbody>
        </table>
    </body>
    </html>
`;

    const printFrame = document.createElement('iframe');
    printFrame.style.position = 'fixed';
    printFrame.style.right = '0';
    printFrame.style.bottom = '0';
    printFrame.style.width = '0';
    printFrame.style.height = '0';
    printFrame.style.border = '0';
    document.body.appendChild(printFrame);

    const frameDoc = printFrame.contentWindow.document;
    frameDoc.open();
    frameDoc.write(printHtml);
    frameDoc.close();

    printFrame.contentWindow.focus();
    printFrame.contentWindow.print();

    setTimeout(() => document.body.removeChild(printFrame), 1000);
}

$('#closeDetailModal, #closeDetailBtn').on('click', function() {
    $('#detailModal').removeClass('active');
});

function formatDefects(defects) {
    if (!defects || !defects.length) return '—';
    return defects.map(d =>
        `[${d.severity.toUpperCase()}] ${d.defect_code} @ ${d.location}`
    ).join('<br>');
}

function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatDate(str) {
    if (!str) return '—';
    const d = new Date(str);
    return d.toLocaleDateString('en-PH', { year:'numeric', month:'short', day:'numeric' })
        + ' ' + d.toLocaleTimeString('en-PH', { hour:'2-digit', minute:'2-digit' });
}

function resultBadge(status, lotResult) {
    if (status === 'IN_PROGRESS') return `<span class="badge-progress">ONGOING</span>`;
    return `<span class="${lotResult === 'ACCEPT' ? 'badge-accept' : 'badge-reject'}">${lotResult}</span>`;
}

</script>
</body>
</html>