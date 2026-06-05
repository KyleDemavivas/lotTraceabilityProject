<?php include 'sidebar.php'; ?>
<?php
if (!isset($_SESSION['user_namefl'])) {
    header('Location: login.php');
    exit;
}
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

$locations = [];
try {
    $stmt = $conn->query('SELECT location FROM location_master ORDER BY location ASC');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $locations[] = $row['location'];
    }
} catch (PDOException $e) {
    exit('Error fetching locations: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QA Inspection</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/qa.css">
</head>
<body>
<div class="qa-wrapper">

    <div class="qa-header">
        <h1>QA Inspection</h1>
        <div class="operator-tag">OPERATOR: <?php echo htmlspecialchars($_SESSION['user_namefl']); ?></div>
    </div>

    <!-- Setup Panel -->
    <div class="qa-grid" id="setupSection">
        <div class="panel">
            <div class="panel-title">Lot Information</div>
            <div class="field">
                <label>KEPI Lot No.</label>
                <input type="text" id="kepi_lot" placeholder="ENTER LOT NUMBER" autocomplete="off">
            </div>
            <div class="field">
                <label>Lot Quantity</label>
                <input type="number" id="lot_qty" placeholder="ENTER QUANTITY" min="1">
            </div>
            <div class="field">
                <label>Code Letter (Auto)</label>
                <input type="text" id="code_letter" readonly placeholder="—">
            </div>
            <div class="field">
                <label>Model / Assy</label>
                <input type="text" id="model_name" placeholder="ENTER MODEL">
            </div>
        </div>

        <div class="panel">
            <div class="panel-title">Inspection Setup</div>
            <div class="field">
                <label>Inspection Method</label>
                <select id="inspection_method">
                    <option value="">— Select Method —</option>
                    <option value="normal">Normal</option>
                    <option value="tightened">Tightened</option>
                    <option value="reduced">Reduced</option>
                </select>
            </div>
            <div class="field">
                <label>Shift</label>
                <select id="shift">
                    <option value="">— Select Shift —</option>
                    <option value="Dayshift">Day</option>
                    <option value="Night Shift">Night</option>
                </select>
            </div>
            <div class="field">
                <label>Line</label>
                <select id="line">
                    <option value="">— Select Line —</option>
                    <option value="AV1">AV1</option>
                    <option value="AV2">AV2</option>
                    <option value="RG31">RG31</option>
                    <option value="RG2">RG2</option>
                </select>
            </div>
            <div class="btn-row" style="margin-top:20px; justify-content:flex-start;">
                <button class="btn btn-primary" id="startBtn" disabled>START INSPECTION</button>
            </div>
        </div>
    </div>

    <!-- AQL Info Panel (shown after start) -->
    <div class="aql-panel" id="aqlPanel" style="display:none;">
        <div class="panel-title">AQL Parameters</div>
        <div class="aql-grid">
            <div class="aql-cell">
                <div class="aql-label">Code Letter</div>
                <div class="aql-value accent" id="disp_letter">—</div>
            </div>
            <div class="aql-cell">
                <div class="aql-label">Sample Size</div>
                <div class="aql-value" id="disp_sample">—</div>
            </div>
            <div class="aql-cell">
                <div class="aql-label">Method</div>
                <div class="aql-value accent2" id="disp_method">—</div>
            </div>
        </div>

        <div class="aql-thresholds">
            <div class="threshold-block">
                <div class="th-title"><span class="badge badge-015">AQL 0.15</span> — Critical</div>
                <div class="threshold-row"><span>Accept ≤</span><span id="ac_015">—</span></div>
                <div class="threshold-row"><span>Reject ≥</span><span id="re_015">—</span></div>
                <div class="threshold-row"><span>Defects Found</span><span id="count_015" style="color:var(--warn)">0</span></div>
            </div>
            <div class="threshold-block">
                <div class="th-title"><span class="badge badge-10">AQL 1.0</span> — Major</div>
                <div class="threshold-row"><span>Accept ≤</span><span id="ac_10">—</span></div>
                <div class="threshold-row"><span>Reject ≥</span><span id="re_10">—</span></div>
                <div class="threshold-row"><span>Defects Found</span><span id="count_10" style="color:var(--warn)">0</span></div>
            </div>
        </div>

        <div class="judgement-banner ongoing" id="judgementBanner">
            ● INSPECTION IN PROGRESS
        </div>
    </div>

    <!-- Serial Scan Panel (shown after start) -->
    <div class="scan-panel" id="scanPanel" style="display:none;">
        <div class="panel-title">Serial Scanning</div>
        <div class="qa-grid">
            <div class="field">
                <label>Scan Serial Code</label>
                <input type="text" id="serial_input" placeholder="SCAN HERE..." autocomplete="off" maxlength="13" minlength="13" disabled>
                <div class="error-msg" id="serial_error"></div>
            </div>
            <div style="display:flex; align-items:center; justify-content:center;">
                <div class="scan-counter">
                    <span id="scanned_count">0</span> / <span id="sample_total" style="color:var(--accent)">0</span>
                </div>
            </div>
        </div>

        <div class="serial-list" id="serialList">
            <div class="empty-state">NO SERIALS SCANNED YET</div>
        </div>

        <div class="btn-row">
            <button class="btn btn-danger" id="ngBtn" disabled>MARK NO GOOD</button>
            <button class="btn btn-ghost" id="finalizeBtn" disabled>FINALIZE</button>
        </div>
    </div>

</div>

<!-- No Good Modal -->
<div class="modal-overlay" id="ngModal">
    <div class="modal-box">
        <div class="modal-title">● No Good Entry</div>

        <div class="field">
            <label>Serial Code</label>
            <input type="text" id="ng_serial" readonly>
        </div>

        <div id="ng_defect_rows">
            <div class="ng-defect-row">
                <div class="field">
                    <label>Defect</label>
                    <input type="text" class="defect-input" placeholder="ENTER DEFECT" style="width:100%;background:var(--surface2);border:1px solid var(--border);border-radius:4px;color:var(--text);font-family:var(--font-main);font-size:14px;padding:9px 12px;outline:none;">
                </div>
                <div class="field">
                    <label>Location</label>
                    <select class="location-select" name="location_ng[]" multiple="multiple" style="width:100%;">
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?php echo htmlspecialchars($loc); ?>"><?php echo htmlspecialchars($loc); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div style="text-align:center; margin-top:8px;">
            <button class="btn btn-ghost" id="addNgDefectBtn" style="font-size:11px;">+ ADD DEFECT</button>
        </div>

        <div class="modal-footer">
            <button class="btn btn-ghost" id="ngCancelBtn">CANCEL</button>
            <button class="btn btn-danger" id="ngSaveBtn">SAVE NO GOOD</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ─── AQL DATA TABLES ────────────────────────────────────────────────────────
// Level III code letter lookup: [minQty, maxQty, letter]
const LEVEL3_TABLE = [
    [1,       8,       'B'],
    [9,       15,      'C'],
    [16,      25,      'D'],
    [26,      50,      'E'],
    [51,      90,      'F'],
    [91,      150,     'G'],
    [151,     280,     'H'],
    [281,     500,     'J'],
    [501,     1200,    'K'],
    [1201,    3200,    'L'],
    [3201,    10000,   'M'],
    [10001,   35000,   'N'],
    [35001,   150000,  'P'],
    [150001,  500000,  'Q'],
    [500001,  Infinity,'R'],
];

// AQL tables: letter -> { sampleSize, normal: {aql015:{ac,re}, aql10:{ac,re}}, tightened:{...}, reduced:{...} }
// Ac/Re values from ANSI/ASQC Z1.4 Tables L, M, N at AQL 0.15 and 1.0
// null = use arrow (refer to next/prev letter's sampling plan)
const AQL_DATA = {
    // letter: [sampleSize, normal_015_ac, normal_015_re, normal_10_ac, normal_10_re,
    //          tight_015_ac, tight_015_re, tight_10_ac, tight_10_re,
    //          reduced_015_ac, reduced_015_re, reduced_10_ac, reduced_10_re]
    'B': { sample: { normal:3,   tightened:3,   reduced:2   },
           normal:    { aql015:{ac:null,re:null}, aql10:{ac:null,re:null} },
           tightened: { aql015:{ac:null,re:null}, aql10:{ac:null,re:null} },
           reduced:   { aql015:{ac:null,re:null}, aql10:{ac:null,re:null} } },
    'C': { sample: { normal:5,   tightened:5,   reduced:2   },
           normal:    { aql015:{ac:null,re:null}, aql10:{ac:null,re:null} },
           tightened: { aql015:{ac:null,re:null}, aql10:{ac:null,re:null} },
           reduced:   { aql015:{ac:null,re:null}, aql10:{ac:null,re:null} } },
    'D': { sample: { normal:8,   tightened:8,   reduced:3   },
           normal:    { aql015:{ac:null,re:null}, aql10:{ac:0,re:1}  },
           tightened: { aql015:{ac:null,re:null}, aql10:{ac:null,re:null} },
           reduced:   { aql015:{ac:null,re:null}, aql10:{ac:null,re:null} } },
    'E': { sample: { normal:13,  tightened:13,  reduced:5   },
           normal:    { aql015:{ac:null,re:null}, aql10:{ac:0,re:1}  },
           tightened: { aql015:{ac:null,re:null}, aql10:{ac:0,re:1}  },
           reduced:   { aql015:{ac:null,re:null}, aql10:{ac:0,re:1}  } },
    'F': { sample: { normal:20,  tightened:20,  reduced:8   },
           normal:    { aql015:{ac:null,re:null}, aql10:{ac:1,re:2}  },
           tightened: { aql015:{ac:null,re:null}, aql10:{ac:0,re:1}  },
           reduced:   { aql015:{ac:0,re:1},       aql10:{ac:0,re:1}  } },
    'G': { sample: { normal:32,  tightened:32,  reduced:13  },
           normal:    { aql015:{ac:null,re:null}, aql10:{ac:1,re:2}  },
           tightened: { aql015:{ac:null,re:null}, aql10:{ac:1,re:2}  },
           reduced:   { aql015:{ac:null,re:null}, aql10:{ac:0,re:1}  } },
    'H': { sample: { normal:50,  tightened:50,  reduced:20  },
           normal:    { aql015:{ac:null,re:null}, aql10:{ac:1,re:2}  },
           tightened: { aql015:{ac:0,re:1},       aql10:{ac:1,re:2}  },
           reduced:   { aql015:{ac:0,re:1},       aql10:{ac:1,re:2}  } },
    'J': { sample: { normal:80,  tightened:80,  reduced:32  },
           normal:    { aql015:{ac:0,re:1},       aql10:{ac:2,re:3}  },
           tightened: { aql015:{ac:0,re:1},       aql10:{ac:1,re:2}  },
           reduced:   { aql015:{ac:0,re:1},       aql10:{ac:1,re:2}  } },
    'K': { sample: { normal:125, tightened:125, reduced:50  },
           normal:    { aql015:{ac:0,re:1},       aql10:{ac:3,re:4}  },
           tightened: { aql015:{ac:0,re:1},       aql10:{ac:2,re:3}  },
           reduced:   { aql015:{ac:0,re:1},       aql10:{ac:2,re:3}  } },
    'L': { sample: { normal:200, tightened:200, reduced:80  },
           normal:    { aql015:{ac:1,re:2},       aql10:{ac:5,re:6}  },
           tightened: { aql015:{ac:0,re:1},       aql10:{ac:3,re:4}  },
           reduced:   { aql015:{ac:0,re:1},       aql10:{ac:2,re:3}  } },
    'M': { sample: { normal:315, tightened:315, reduced:125 },
           normal:    { aql015:{ac:1,re:2},       aql10:{ac:7,re:8}  },
           tightened: { aql015:{ac:1,re:2},       aql10:{ac:5,re:6}  },
           reduced:   { aql015:{ac:0,re:1},       aql10:{ac:3,re:4}  } },
    'N': { sample: { normal:500, tightened:500, reduced:200 },
           normal:    { aql015:{ac:2,re:3},       aql10:{ac:10,re:11}},
           tightened: { aql015:{ac:1,re:2},       aql10:{ac:8,re:9}  },
           reduced:   { aql015:{ac:1,re:2},       aql10:{ac:5,re:6}  } },
    'P': { sample: { normal:800, tightened:800, reduced:315 },
           normal:    { aql015:{ac:3,re:4},       aql10:{ac:14,re:15}},
           tightened: { aql015:{ac:2,re:3},       aql10:{ac:12,re:13}},
           reduced:   { aql015:{ac:1,re:2},       aql10:{ac:7,re:8}  } },
    'Q': { sample: { normal:1250,tightened:1250,reduced:500 },
           normal:    { aql015:{ac:5,re:6},       aql10:{ac:21,re:22}},
           tightened: { aql015:{ac:3,re:4},       aql10:{ac:18,re:19}},
           reduced:   { aql015:{ac:3,re:4},       aql10:{ac:10,re:11}} },
    'R': { sample: { normal:2000,tightened:2000,reduced:800 },
           normal:    { aql015:{ac:7,re:8},       aql10:{ac:21,re:22}},
           tightened: { aql015:{ac:5,re:6},       aql10:{ac:18,re:19}},
           reduced:   { aql015:{ac:3,re:6},       aql10:{ac:7,re:10} } },
};

// ─── STATE ──────────────────────────────────────────────────────────────────
let state = {
    active: false,
    letter: null,
    method: null,
    sampleSize: 0,
    scanned: [],       // { serial, good: bool, defects: [{defect, locations}] }
    currentSerial: null,
    defects015: 0,
    defects10: 0,
    aqlParams: null,
};

// ─── HELPERS ─────────────────────────────────────────────────────────────────
function getCodeLetter(qty) {
    for (const [min, max, letter] of LEVEL3_TABLE) {
        if (qty >= min && qty <= max) return letter;
    }
    return null;
}

function fmtAcRe(val) {
    return val === null ? '↑↓' : val;
}

function updateJudgement() {
    const { aqlParams, defects015, defects10, scanned, sampleSize } = state;
    const banner = document.getElementById('judgementBanner');
    const re015 = aqlParams.aql015.re;
    const re10  = aqlParams.aql10.re;

    const failed015 = re015 !== null && defects015 >= re015;
    const failed10  = re10  !== null && defects10  >= re10;

    if (failed015 || failed10) {
        banner.className = 'judgement-banner fail';
        banner.textContent = '✕ REJECT — DEFECT THRESHOLD EXCEEDED';
        return;
    }

    if (scanned.length >= sampleSize) {
        banner.className = 'judgement-banner pass';
        banner.textContent = '✓ ACCEPT — INSPECTION COMPLETE';
        return;
    }

    banner.className = 'judgement-banner ongoing';
    banner.textContent = '● INSPECTION IN PROGRESS';
}

function renderSerialList() {
    const list = document.getElementById('serialList');
    if (state.scanned.length === 0) {
        list.innerHTML = '<div class="empty-state">NO SERIALS SCANNED YET</div>';
        return;
    }
    list.innerHTML = state.scanned.map((s, i) => {
        const ngDetails = s.defects.length
            ? s.defects.map(d => `${d.defect} @ ${d.locations.join(', ')}`).join(' | ')
            : '';
        return `<div class="serial-row ${s.good ? '' : 'ng'}">
            <span class="serial-num">#${String(i+1).padStart(2,'0')}</span>
            <span class="serial-code">${s.serial}</span>
            ${ngDetails ? `<span class="ng-detail">${ngDetails}</span>` : ''}
            <span class="serial-status ${s.good ? 'status-good' : 'status-ng'}">${s.good ? 'GOOD' : 'NG'}</span>
        </div>`;
    }).join('');
    list.scrollTop = list.scrollHeight;
}

function updateCounts() {
    document.getElementById('scanned_count').textContent = state.scanned.length;
    document.getElementById('count_015').textContent = state.defects015;
    document.getElementById('count_10').textContent  = state.defects10;

    const finBtn = document.getElementById('finalizeBtn');
    finBtn.disabled = state.scanned.length < state.sampleSize;
}

// ─── SETUP LOGIC ─────────────────────────────────────────────────────────────
function checkStartReady() {
    const qty    = parseInt($('#lot_qty').val());
    const method = $('#inspection_method').val();
    const shift  = $('#shift').val();
    const line   = $('#line').val();
    const kepi   = $('#kepi_lot').val().trim();
    const ready  = qty > 0 && method && shift && line && kepi;
    $('#startBtn').prop('disabled', !ready);

    if (qty > 0) {
        const letter = getCodeLetter(qty);
        $('#code_letter').val(letter || '—');
    } else {
        $('#code_letter').val('—');
    }
}

$('#lot_qty, #inspection_method, #shift, #line, #kepi_lot').on('input change', checkStartReady);

$('#startBtn').on('click', function () {
    const qty    = parseInt($('#lot_qty').val());
    const method = $('#inspection_method').val();
    const letter = getCodeLetter(qty);

    if (!letter || !AQL_DATA[letter]) {
        Swal.fire({ icon:'error', title:'Error', text:'Could not determine code letter for this quantity.' });
        return;
    }

    const data   = AQL_DATA[letter];
    const params = data[method];
    const sample = data.sample[method];

    state.active     = true;
    state.letter     = letter;
    state.method     = method;
    state.sampleSize = sample;
    state.aqlParams  = params;
    state.scanned    = [];
    state.defects015 = 0;
    state.defects10  = 0;

    // Populate AQL display
    $('#disp_letter').text(letter);
    $('#disp_sample').text(sample);
    $('#disp_method').text(method.toUpperCase());
    $('#ac_015').text(fmtAcRe(params.aql015.ac));
    $('#re_015').text(fmtAcRe(params.aql015.re));
    $('#ac_10').text(fmtAcRe(params.aql10.ac));
    $('#re_10').text(fmtAcRe(params.aql10.re));
    $('#sample_total').text(sample);
    $('#scanned_count').text(0);

    $('#aqlPanel').show();
    $('#scanPanel').show();
    $('#serial_input').prop('disabled', false).focus();
    $('#ngBtn').prop('disabled', false);
    $('#startBtn').prop('disabled', true).text('ACTIVE');

    updateJudgement();
    renderSerialList();
});

// ─── SERIAL SCAN ─────────────────────────────────────────────────────────────
$('#serial_input').on('keydown', function(e) {
    if (e.key !== 'Enter') return;
    const serial = $(this).val().trim().toUpperCase();
    const errEl  = document.getElementById('serial_error');

    errEl.style.display = 'none';

    if (!state.active) return;
    if (state.scanned.length >= state.sampleSize) {
        errEl.textContent = 'SAMPLE SIZE REACHED.';
        errEl.style.display = 'block';
        return;
    }
    if (serial.length !== 13) {
        errEl.textContent = 'SERIAL MUST BE 13 CHARACTERS.';
        errEl.style.display = 'block';
        return;
    }
    if (state.scanned.find(s => s.serial === serial)) {
        errEl.textContent = 'DUPLICATE SERIAL.';
        errEl.style.display = 'block';
        $(this).select();
        return;
    }

    // Ask good/no good
    Swal.fire({
        title: serial,
        text: 'Inspection result for this unit?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'GOOD',
        cancelButtonText: 'NO GOOD',
        reverseButtons: true,
        allowOutsideClick: false,
        background: '#1a1d27',
        color: '#e8eaf6',
    }).then(result => {
        if (result.isConfirmed) {
            state.scanned.push({ serial, good: true, defects: [] });
            renderSerialList();
            updateCounts();
            updateJudgement();
            $('#serial_input').val('').focus();
        } else {
            // Open NG modal
            state.currentSerial = serial;
            openNgModal(serial);
        }
    });
});

$('#serial_input').on('input', function() {
    this.value = this.value.toUpperCase();
});

// ─── NO GOOD MODAL ───────────────────────────────────────────────────────────
const locationOptions = `<?php foreach ($locations as $loc): ?><option value="<?php echo htmlspecialchars($loc); ?>"><?php echo htmlspecialchars($loc); ?></option><?php endforeach; ?>`;

function initSelect2InRow(row) {
    $(row).find('.location-select').select2({
        tags: true,
        placeholder: 'Select or type locations',
        tokenSeparators: [','],
        width: '100%',
        dropdownParent: $('#ngModal'),
    });
}

function openNgModal(serial) {
    $('#ng_serial').val(serial);
    // Reset rows
    $('#ng_defect_rows').html(`
        <div class="ng-defect-row">
            <div class="field">
                <label>Defect</label>
                <input type="text" class="defect-input" placeholder="ENTER DEFECT" style="width:100%;background:var(--surface2);border:1px solid var(--border);border-radius:4px;color:var(--text);font-family:var(--font-main);font-size:14px;padding:9px 12px;outline:none;">
            </div>
            <div class="field">
                <label>Location</label>
                <select class="location-select" multiple="multiple" style="width:100%;">${locationOptions}</select>
            </div>
        </div>`);
    initSelect2InRow($('#ng_defect_rows .ng-defect-row')[0]);
    $('#ngModal').addClass('active');
    setTimeout(() => $('#ng_defect_rows .defect-input').first().focus(), 100);
}

$('#ngCancelBtn').on('click', function() {
    $('#ngModal').removeClass('active');
    // Add as good if cancelled
    state.scanned.push({ serial: state.currentSerial, good: true, defects: [] });
    renderSerialList();
    updateCounts();
    updateJudgement();
    $('#serial_input').val('').focus();
});

$('#addNgDefectBtn').on('click', function() {
    const newRow = $(`<div class="ng-defect-row" style="margin-top:12px;border-top:1px solid var(--border);padding-top:12px;">
        <div class="field">
            <label>Defect</label>
            <input type="text" class="defect-input" placeholder="ENTER DEFECT" style="width:100%;background:var(--surface2);border:1px solid var(--border);border-radius:4px;color:var(--text);font-family:var(--font-main);font-size:14px;padding:9px 12px;outline:none;">
        </div>
        <div class="field">
            <label>Location</label>
            <select class="location-select" multiple="multiple" style="width:100%;">${locationOptions}</select>
        </div>
    </div>`);
    $('#ng_defect_rows').append(newRow);
    initSelect2InRow(newRow[0]);
});

$('#ngSaveBtn').on('click', function() {
    const defects = [];
    let valid = true;

    $('#ng_defect_rows .ng-defect-row').each(function() {
        const defect    = $(this).find('.defect-input').val().trim().toUpperCase();
        const locations = $(this).find('.location-select').val() || [];
        if (!defect && locations.length === 0) return; // skip empty rows
        if (!defect || locations.length === 0) { valid = false; return; }
        defects.push({ defect, locations });
    });

    if (!valid || defects.length === 0) {
        Swal.fire({ icon:'warning', title:'Incomplete', text:'Each defect row must have both a defect name and location.',
            background:'#1a1d27', color:'#e8eaf6', toast:true, position:'top-right', timer:3000, showConfirmButton:false });
        return;
    }

    // Count against AQL — for now all defects count against both 0.15 and 1.0
    // TODO: categorize defects to specific AQL level once business rules are confirmed
    state.defects015 += defects.length;
    state.defects10  += defects.length;

    state.scanned.push({ serial: state.currentSerial, good: false, defects });
    $('#ngModal').removeClass('active');
    renderSerialList();
    updateCounts();
    updateJudgement();
    $('#serial_input').val('').focus();
});

// ─── NO GOOD BUTTON (manual trigger) ─────────────────────────────────────────
$('#ngBtn').on('click', function() {
    const serial = $('#serial_input').val().trim().toUpperCase();
    if (!serial) {
        Swal.fire({ icon:'warning', title:'Scan First', text:'Scan a serial number before marking No Good.',
            background:'#1a1d27', color:'#e8eaf6', toast:true, position:'top-right', timer:2500, showConfirmButton:false });
        return;
    }
    state.currentSerial = serial;
    openNgModal(serial);
});

// ─── FINALIZE ─────────────────────────────────────────────────────────────────
$('#finalizeBtn').on('click', function() {
    const { aqlParams, defects015, defects10, scanned, sampleSize, letter, method } = state;
    const failed015 = aqlParams.aql015.re !== null && defects015 >= aqlParams.aql015.re;
    const failed10  = aqlParams.aql10.re  !== null && defects10  >= aqlParams.aql10.re;
    const judgement = (failed015 || failed10) ? 'REJECT' : 'ACCEPT';

    Swal.fire({
        icon: judgement === 'ACCEPT' ? 'success' : 'error',
        title: `Lot ${judgement}`,
        html: `
            <div style="font-family:monospace;text-align:left;font-size:13px;line-height:1.8;">
                <b>Code Letter:</b> ${letter}<br>
                <b>Method:</b> ${method.toUpperCase()}<br>
                <b>Sample Size:</b> ${sampleSize}<br>
                <b>Inspected:</b> ${scanned.length}<br>
                <b>NG Units:</b> ${scanned.filter(s=>!s.good).length}<br>
                <hr style="border-color:#2e3248;margin:8px 0;">
                <b>AQL 0.15 Defects:</b> ${defects015} / Ac:${fmtAcRe(aqlParams.aql015.ac)} Re:${fmtAcRe(aqlParams.aql015.re)}<br>
                <b>AQL 1.0 Defects:</b>  ${defects10} / Ac:${fmtAcRe(aqlParams.aql10.ac)} Re:${fmtAcRe(aqlParams.aql10.re)}<br>
            </div>`,
        background: '#1a1d27',
        color: '#e8eaf6',
        confirmButtonText: 'DONE',
    });
});
</script>
</body>
</html>