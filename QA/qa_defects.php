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
    <title>NG Serials</title>
    <link rel="stylesheet" href="../css/qa.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .badge-major {
            background: #ede9fe;
            color: #7c3aed;
            border: 1px solid #c4b5fd;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-minor {
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #93c5fd;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-reject {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-accept {
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #86efac;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
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
        .dataTables_wrapper { font-size: 13px; }
        .dataTables_filter input {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 13px;
            outline: none;
        }
        .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #4facfe, #00c2fe) !important;
            color: white !important;
            border: none !important;
            border-radius: 4px !important;
        }
        .empty-state {
            text-align: center;
            color: #bbb;
            font-size: 13px;
            padding: 32px;
        }
        .form-container {
            max-width: 1400px;
            margin-left: 240px;
        }
    </style>
</head>
<body>
<div class="form-container">

    <h1 style="text-align:center; margin-bottom:20px; font-size:22px; color:#333;">NG Serials</h1>

    <div class="form-section">
    <div style="overflow-x: auto;">
        <div id="ngTableContainer">
            <div class="empty-state">Loading NG records…</div>
        </div>
    </div>
</div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

$(document).ready(function() {
    loadNGSerials();
});

function loadNGSerials() {
    $.ajax({
        url: '/traceabilitydev/QA/fetch_ng_serials.php',
        type: 'GET',
        success: function(response) {
            if (!response.success || !response.data.length) {
                $('#ngTableContainer').html('<div class="empty-state">No NG records found</div>');
                return;
            }
            renderNGTable(response.data);
        },
        error: function() {
            Swal.fire({ icon:'error', title:'Network Error', text:'Failed to load NG serials.' });
        }
    });
}

function renderNGTable(rows) {
    const tbody = rows.map(r => `
        <tr>
            <td><b>${r.kepi_lot}</b></td>
            <td style="font-family:monospace;">${r.serial_code}</td>
            <td>${r.model}</td>
            <td>${capitalize(r.inspection_method)}</td>
            <td>${r.line}</td>
            <td>${r.shift}</td>
            <td>${r.location || '—'}</td>
            <td>${r.defect_code || '—'}</td>
            <td>${r.severity ? `<span class="${r.severity === 'major' ? 'badge-major' : 'badge-minor'}">${r.severity.toUpperCase()}</span>` : '—'}</td>
            <td>${r.operator_id}</td>
            <td>${formatDate(r.created_at)}</td>
            <td><span class="${r.lot_result === 'ACCEPT' ? 'badge-accept' : 'badge-reject'}">${r.lot_result}</span></td>
        </tr>
    `).join('');

    $('#ngTableContainer').html(`
        <table class="history-table" id="ngDT" style="width:100%">
            <thead>
                <tr>
                    <th>KEPI Lot No.</th>
                    <th>Serial</th>
                    <th>Model</th>
                    <th>Method</th>
                    <th>Line</th>
                    <th>Shift</th>
                    <th>Location</th>
                    <th>Defect</th>
                    <th>Severity</th>
                    <th>Operator</th>
                    <th>Date</th>
                    <th>Lot Result</th>
                </tr>
            </thead>
            <tbody>${tbody}</tbody>
        </table>
    `);

    $('#ngDT').DataTable({
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        order: [[10, 'desc']],
        language: {
            search: 'Filter:',
            emptyTable: 'No NG records found.',
            info: 'Showing _START_ to _END_ of _TOTAL_ NG serials',
            infoEmpty: 'No records to show',
            infoFiltered: '(filtered from _MAX_ total)',
        }
    });
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

</script>
</body>
</html>