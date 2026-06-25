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
<div class="form-container">

    <h1 style="text-align:center; margin-bottom:20px; font-size:22px; color:#333;">QA Inspection</h1>
    <div style="text-align:center; margin-bottom:20px; font-size:13px; color:#888;">
        OPERATOR: <strong><?php echo htmlspecialchars($_SESSION['user_namefl']); ?></strong>
    </div>

    <!-- ── SETUP ── -->
    <div class="two-column-layout" id="setupSection">
        <div class="column">
            <div class="form-section">
                <h3>Lot Information</h3>
                                <div class="lot-dupe-warning" id="lot_dupe_warning" style="display:none;"></div>
                <div class="form-group">
                    <label class="form-label">KEPI Lot No.:</label>
                    <input type="text" class="form-input" id="kepi_lot" autocomplete="off" placeholder="Enter lot number">
                </div>
                <div class="form-group">
                    <label class="form-label">Lot Quantity:</label>
                    <input type="number" class="form-input" id="lot_qty" min="1" placeholder="Enter quantity">
                </div>
                <div class="form-group">
                    <label class="form-label">Code Letter:</label>
                    <input type="text" class="form-input" id="code_letter" readonly placeholder="—">
                </div>
                <div class="form-group">
                    <label class="form-label">Model:</label>
                    <input type="text" class="form-input" id="model_name" placeholder="—" readonly>
                </div>
            </div>
        </div>

        <div class="column">
            <div class="form-section">
                <h3>Inspection Setup</h3>
                <div class="form-group">
                    <label class="form-label">Method:</label>
                    <select class="form-input" id="inspection_method">
                        <option value="">— Select Method —</option>
                        <option value="normal">Normal</option>
                        <option value="tightened">Tightened</option>
                       <option option value="reduced">Reduced</option>
                       <option option value="fullcheck">Full Check</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Sample Size:</label>
                    <input type="text" class="form-input" id="sample_size" readonly placeholder="—">
                </div>
                <div class="form-group">
                    <label class="form-label">Shift:</label>
                    <select class="form-input" id="shift">
                        <option value="">— Select Shift —</option>
                        <option value="Dayshift">Day</option>
                        <option value="Night Shift">Night</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Line:</label>
                    <select class="form-input" id="line">
                        <option value="">— Select Line —</option>
                        <option value="AV1">AV1</option>
                        <option value="AV2">AV2</option>
                        <option value="RG31">RG31</option>
                        <option value="RG2">RG2</option>
                    </select>
                </div>
                <div style="margin-top:20px;">
                    <button id="startBtn" class="btn-inline" disabled>START INSPECTION</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── AQL PANEL ── -->
    <div class="form-section" id="aqlPanel" style="display:none;">
        <h3>AQL Parameters</h3>
        <div class="aql-info-row">
            <div class="aql-box highlight">
                <div class="aql-box-label">Code Letter</div>
                <div class="aql-box-value" id="disp_letter">—</div>
            </div>
            <div class="aql-box highlight">
                <div class="aql-box-label">Sample Size</div>
                <div class="aql-box-value" id="disp_sample">—</div>
            </div>
            <div class="aql-box">
                <div class="aql-box-label">Method</div>
                <div class="aql-box-value" style="font-size:16px;" id="disp_method">—</div>
            </div>
            <div class="aql-box">
                <div class="aql-box-label">Lot Qty</div>
                <div class="aql-box-value" style="font-size:16px;" id="disp_lotqty">—</div>
            </div>
        </div>

        <div class="aql-threshold-row">
            <div class="threshold-card">
                <div class="tc-title"><span class="badge badge-015">AQL 0.15</span> Major</div>
                <div class="tc-row"><span>Reject ≥</span><span class="tc-val" id="re_015">—</span></div>
                <div class="tc-row"><span>Defects Found</span><span class="tc-count" id="count_015">0</span></div>
            </div>
            <div class="threshold-card">
                <div class="tc-title"><span class="badge badge-10">AQL 1.0</span> Minor</div>
                <div class="tc-row"><span>Reject ≥</span><span class="tc-val" id="re_10">—</span></div>
                <div class="tc-row"><span>Defects Found</span><span class="tc-count" id="count_10">0</span></div>
            </div>
        </div>

        <div class="judgement-banner ongoing" id="judgementBanner">
            ● INSPECTION IN PROGRESS
        </div>
    </div>

    <!-- ── SCAN PANEL ── -->
    <div class="form-section" id="scanPanel" style="display:none;">
        <h3>Serial Scanning</h3>
        <div class="two-column-layout" style="gap:16px;">
            <div class="column" style="min-width:200px;">
                <div class="form-group">
                    <label class="form-label">Serial Code:</label>
                    <input type="text" class="form-input" id="serial_input"
                           placeholder="Scan here..." autocomplete="off"
                           maxlength="13" minlength="13" disabled>
                </div>
                <div class="error-msg" id="serial_error"></div>
            </div>
            <div class="column" style="min-width:100px; display:flex; align-items:center; justify-content:center;">
                <div class="scan-counter">
                    <span id="scanned_count">0</span><span class="scan-denom"> / <span id="sample_total">0</span></span>
                </div>
            </div>
        </div>

        <div class="serial-list" id="serialList">
            <div class="empty-state">No serials scanned yet</div>
        </div>

        <div class="btn-row" style="margin-top:16px;">
            <button class="btn-nogood btn-inline" id="ngBtn" disabled>NO GOOD</button>
            <button class="btn-ghost" id="finalizeBtn" disabled>FINALIZE</button>
        </div>
    </div>

</div>

<!-- ── NO GOOD MODAL ── -->
<div class="modal" id="ngModal">
    <div class="modal-content">
        <span class="close" id="closeNgModal">&times;</span>
        <h2>No Good Entry</h2>

        <div class="form-group">
            <label class="form-label">Serial:</label>
            <input type="text" class="form-input" id="ng_serial" readonly>
        </div>

        <div id="ng_defect_rows"></div>

        <div style="margin-top:12px;">
            <button class="add-defect" id="addNgDefectBtn">+ Add Defect</button>
        </div>

        <div class="modal-footer">
            <button class="button-close" id="ngCancelBtn">CANCEL</button>
            <button class="btn-nogood" id="ngSaveBtn">SAVE NO GOOD</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

// ── AQL DATA ──────────────────────────────────────────────────────────────────
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

const AQL_DATA = {
    'B': { sample:{normal:3,   tightened:3,   reduced:2  },
           normal:    {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }},
           tightened: {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }},
           reduced:   {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }} },

    'C': { sample:{normal:5,   tightened:5,   reduced:2  },
           normal:    {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }},
           tightened: {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }},
           reduced:   {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }} },

    'D': { sample:{normal:8,   tightened:8,   reduced:3  },
           normal:    {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }},
           tightened: {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }},
           reduced:   {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }} },

    'E': { sample:{normal:13,  tightened:13,  reduced:5  },
           normal:    {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }},
           tightened: {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }},
           reduced:   {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }}},

    'F': { sample:{normal:20,  tightened:20,  reduced:8  },
           normal:    {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }},
           tightened: {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }},
           reduced:   {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:2   }} },

    'G': { sample:{normal:32,  tightened:32,  reduced:13 },
           normal:    {aql015:{ac:0,   re:1   }, aql10:{ac:1,   re:2   }},
           tightened: {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:1   }},
           reduced:   {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:2   }} },

    'H': { sample:{normal:50,  tightened:50,  reduced:20 },
           normal:    {aql015:{ac:0,   re:1   }, aql10:{ac:1,   re:2   }},
           tightened: {aql015:{ac:0,   re:1   }, aql10:{ac:1,   re:2   }},
           reduced:   {aql015:{ac:0,   re:1   }, aql10:{ac:0,   re:2   }} },

    'J': { sample:{normal:80,  tightened:80,  reduced:32 },
           normal:    {aql015:{ac:0,   re:1   }, aql10:{ac:1,   re:2   }},
           tightened: {aql015:{ac:0,   re:1   }, aql10:{ac:1,   re:2   }},
           reduced:   {aql015:{ac:0,   re:1   }, aql10:{ac:1,   re:3   }} },

    'K': { sample:{normal:125, tightened:125, reduced:50 },
           normal:    {aql015:{ac:0,   re:1   }, aql10:{ac:3,   re:4   }},
           tightened: {aql015:{ac:0,   re:1   }, aql10:{ac:2,   re:3   }},
           reduced:   {aql015:{ac:0,   re:1   }, aql10:{ac:1,   re:4   }} },

    'L': { sample:{normal:200, tightened:200, reduced:80 },
           normal:    {aql015:{ac:1,   re:2   }, aql10:{ac:5,   re:6   }},
           tightened: {aql015:{ac:0,   re:1   }, aql10:{ac:3,   re:4   }},
           reduced:   {aql015:{ac:0,   re:1   }, aql10:{ac:1,   re:4   }} },

    'M': { sample:{normal:315, tightened:315, reduced:125},
           normal:    {aql015:{ac:1,   re:2   }, aql10:{ac:7,   re:8   }},
           tightened: {aql015:{ac:0,   re:1   }, aql10:{ac:5,   re:6   }},
           reduced:   {aql015:{ac:0,   re:1   }, aql10:{ac:2,   re:5   }} },

    'N': { sample:{normal:500, tightened:500, reduced:200},
           normal:    {aql015:{ac:2,   re:3   }, aql10:{ac:10,  re:11  }},
           tightened: {aql015:{ac:1,   re:2   }, aql10:{ac:8,   re:9   }},
           reduced:   {aql015:{ac:1,   re:3   }, aql10:{ac:5,   re:8   }} },

    'P': { sample:{normal:800, tightened:800, reduced:315},
           normal:    {aql015:{ac:3,   re:4   }, aql10:{ac:14,  re:15  }},
           tightened: {aql015:{ac:2,   re:3   }, aql10:{ac:12,  re:13  }},
           reduced:   {aql015:{ac:1,   re:4   }, aql10:{ac:5,   re:8   }} },

    'Q': { sample:{normal:1250,tightened:1250, reduced:500},
           normal:    {aql015:{ac:5,   re:6   }, aql10:{ac:21,  re:22  }},
           tightened: {aql015:{ac:3,   re:4   }, aql10:{ac:18,  re:19  }},
           reduced:   {aql015:{ac:3,   re:6   }, aql10:{ac:7,   re:10  }} },

    'R': { sample:{normal:2000,tightened:2000, reduced:800},
           normal:    {aql015:{ac:7,   re:8   }, aql10:{ac:21,  re:22  }},
           tightened: {aql015:{ac:5,   re:6   }, aql10:{ac:18,  re:19  }},
           reduced:   {aql015:{ac:3,   re:6   }, aql10:{ac:5,   re:8   }} },
};

let allowReload = false;

// ── STATE ─────────────────────────────────────────────────────────────────────
let state = {
    active: false, letter: null, method: null, sampleSize: 0,
    scanned: [], currentSerial: null,
    defects015: 0, defects10: 0, aqlParams: null,
};

// ── HELPERS ───────────────────────────────────────────────────────────────────
function getCodeLetter(qty) {
    for (const [min, max, letter] of LEVEL3_TABLE) {
        if (qty >= min && qty <= max) return letter;
    }
    return null;
}

function fmtAcRe(val) { return val === null ? '↑↓' : val; }

function updateJudgement() {
    const { aqlParams, defects015, defects10, scanned, sampleSize } = state;
    const banner = document.getElementById('judgementBanner');
    const failed015 = aqlParams.aql015.re !== null && defects015 >= aqlParams.aql015.re;
    const failed10  = aqlParams.aql10.re  !== null && defects10  >= aqlParams.aql10.re;
    const rejected  = failed015 || failed10;

    if (rejected) {
        banner.className = 'judgement-banner fail';
        banner.textContent = '✕ REJECT — Defect threshold exceeded';
        $('#serial_input').prop('disabled', true).val('');
        $('#ngBtn').prop('disabled', true);
        document.getElementById('serial_error').style.display = 'none';
        // enable finalize so they can still submit
        document.getElementById('finalizeBtn').disabled = false;
    } else if (scanned.length >= sampleSize) {
        banner.className = 'judgement-banner pass';
        banner.textContent = '✓ ACCEPT — Inspection complete';
        $('#serial_input').prop('disabled', true).val('');
        $('#ngBtn').prop('disabled', true);
    } else {
        banner.className = 'judgement-banner ongoing';
        banner.textContent = '● INSPECTION IN PROGRESS';
        $('#serial_input').prop('disabled', false);
        $('#ngBtn').prop('disabled', false);
    }
}

function renderSerialList() {
    const list = document.getElementById('serialList');
    if (!state.scanned.length) {
        list.innerHTML = '<div class="empty-state">No serials scanned yet</div>';
        return;
    }
    list.innerHTML = state.scanned.map((s, i) => {
        const detail = s.defects.length
            ? s.defects.map(d => `[${d.severity.toUpperCase()}] ${d.defect} @ ${d.locations.join(', ')}`).join(' | ')
            : '';
        return `<div class="serial-row ${s.good ? '' : 'ng'}">
            <span class="serial-num">#${String(i+1).padStart(2,'0')}</span>
            <span class="serial-code">${s.serial}</span>
            ${detail ? `<span class="ng-detail">${detail}</span>` : ''}
            <span class="serial-status ${s.good ? 'status-good' : 'status-ng'}">${s.good ? 'GOOD' : 'NG'}</span>
        </div>`;
    }).join('');
    list.scrollTop = list.scrollHeight;
}

function updateCounts() {
    document.getElementById('scanned_count').textContent = state.scanned.length;
    document.getElementById('count_015').textContent = state.defects015;
    document.getElementById('count_10').textContent  = state.defects10;
    document.getElementById('finalizeBtn').disabled = state.scanned.length < state.sampleSize;
}

function doInserts(lot_result) {
    const kepi_lot          = $('#kepi_lot').val().trim();
    const operator_id       = '<?php echo htmlspecialchars($_SESSION['user_namefl'] ?? 'Unknown'); ?>';
    const shift             = $('#shift').val();
    const line              = $('#line').val();
    const inspection_method = $('#inspection_method').val();
    const sample_size       = $('#sample_size').val();
    const lot_qty           = $('#lot_qty').val();
    const model             = $('#model_name').val();

   const inserts = state.scanned.map(serial => {
    const formData = new FormData();

    const locations   = serial.defects.flatMap(d => d.locations).join(', ');
    const defectCodes = serial.defects.map(d => d.defect).join(', ');
    const severities  = serial.defects.map(d => d.severity).join(', ');

    formData.append('kepi_lot',          kepi_lot);
    formData.append('serial_code',       serial.serial);
    formData.append('inspection_method', inspection_method);
    formData.append('line',              line);
    formData.append('shift',             shift);
    formData.append('location',          locations);
    formData.append('defect_code',       defectCodes);
    formData.append('severity',          severities);
    formData.append('operator_id',       operator_id);
    formData.append('sample_size',       sample_size);
    formData.append('lot_qty',           lot_qty);
    formData.append('model',             model);
    formData.append('status',            serial.good ? 'GOOD' : 'NO GOOD');
    formData.append('lot_result',        lot_result);

    return fetch('/traceabilitydev/QA/qa_process.php', { method: 'POST', body: formData })
        .then(r => {
            if (!r.ok) throw new Error(`HTTP error! status: ${r.status}`);
            return r.json();
        });
});

    Promise.all(inserts)
        .then(results => {
            const failed = results.filter(r => r.status !== 'success');
            if (failed.length) {
                Swal.fire({ icon:'warning', title:'Partial Submit',
                    text: `${failed.length} record(s) failed to insert.`,
                    toast:true, position:'top-end', timer:2500, showConfirmButton:false });
            } else {
                Swal.fire({ icon:'success', title:'Submitted',
                    text:'QA inspection records submitted successfully.',
                    toast:true, position:'top-end', timer:1500, showConfirmButton:false })
                .then(() => {
                    allowReload = true;
                    location.reload();
                });
            }
        })
        .catch((error) => {
            console.error("Insertion failed:", error);
            Swal.fire({ icon:'error', title:'Insertion Error', text:'Failed to save row records.' });
        });
}

// ── LOCATION OPTIONS (PHP-rendered) ──────────────────────────────────────────
const locationOptions = `<?php foreach ($locations as $loc): ?><option value="<?php echo htmlspecialchars($loc); ?>"><?php echo htmlspecialchars($loc); ?></option><?php endforeach; ?>`;

// ── DEFECT ROW TEMPLATE ───────────────────────────────────────────────────────
function defectRowTemplate() {
    const ts = Date.now();
    return `<div class="ng-defect-row">
        <div class="dual-inputs">
            <div class="half-group">
                <label>Defect</label>
                <input type="text" class="form-input defect-input" placeholder="Enter defect">
            </div>
            <div class="half-group">
                <label>Location</label>
                <select class="location-select" multiple="multiple" style="width:100%;">${locationOptions}</select>
            </div>
        </div>
        <div style="margin-top:8px;">
            <label style="font-size:12px; font-weight:600; color:#555;">Severity</label>
            <div class="severity-toggle">
                <label><input type="radio" name="severity_${ts}" class="severity-radio" value="major" checked> Major (AQL 0.15)</label>
                <label><input type="radio" name="severity_${ts}" class="severity-radio" value="minor"> Minor (AQL 1.0)</label>
            </div>
        </div>
    </div>`;
}

// ── SETUP ─────────────────────────────────────────────────────────────────────
function checkStartReady() {
    const qty    = parseInt($('#lot_qty').val());
    const method = $('#inspection_method').val();
    const modelname = $('#model_name').val();
    const ok = qty > 0 && method && $('#shift').val() && $('#line').val() && $('#kepi_lot').val().trim() && modelname;
    const letterStr = qty > 0 ? getCodeLetter(qty) : null;

    $('#code_letter').val(letterStr || '—');

    if (letterStr && method && AQL_DATA[letterStr] && modelname) {
        if (method === 'fullcheck') {
            $('#sample_size').val(5);
        } else {
            $('#sample_size').val(AQL_DATA[letterStr].sample[method]);
        }
    } else {
        $('#sample_size').val('—');
    }
    $('#startBtn').prop('disabled', !ok);
}

$('#lot_qty, #inspection_method, #shift, #line, #kepi_lot').on('input change', checkStartReady);

$('#startBtn').on('click', function() {
    const qty    = parseInt($('#lot_qty').val());
    const method = $('#inspection_method').val();
    const letter = getCodeLetter(qty);
    if (!letter || !AQL_DATA[letter]) {
        Swal.fire({ icon:'error', title:'Error', text:'Cannot determine code letter for this quantity.' });
        return;
    }
    const data   = AQL_DATA[letter];
    const params = method === 'fullcheck' ? data.normal : data[method];
    const sample = method === 'fullcheck' ? 5 : data.sample[method];

    state = {
        active: true, letter, method, sampleSize: sample,
        aqlParams: params, scanned: [], currentSerial: null,
        defects015: 0, defects10: 0,
    };

    $('#disp_letter').text(letter);
    $('#disp_sample').text(sample);
    $('#disp_method').text(method.charAt(0).toUpperCase() + method.slice(1));
    $('#disp_lotqty').text(qty);
    $('#re_015').text(fmtAcRe(params.aql015.re));
    $('#re_10').text(fmtAcRe(params.aql10.re));
    $('#sample_total').text(sample);
    $('#scanned_count').text(0);
    $('#count_015').text(0);
    $('#count_10').text(0);

    $('#aqlPanel, #scanPanel').show();
    $('#serial_input').prop('disabled', false).focus();
    $('#ngBtn').prop('disabled', false);
    $(this).prop('disabled', true).text('ACTIVE');
    updateJudgement();
    renderSerialList();
});

// ── SERIAL SCAN ───────────────────────────────────────────────────────────────
$('#serial_input').on('input', function() { this.value = this.value.toUpperCase(); });

$('#serial_input').on('keydown', function(e) {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    const serial = $(this).val().trim().toUpperCase();
    const errEl  = document.getElementById('serial_error');
    errEl.style.display = 'none';
    if (!state.active) return;
    if (state.scanned.length >= state.sampleSize) {
        errEl.textContent = 'Sample size reached.'; errEl.style.display = 'block'; return;
    }
    if (serial.length !== 13) {
        errEl.textContent = 'Serial must be 13 characters.'; errEl.style.display = 'block'; return;
    }
    if (state.scanned.find(s => s.serial === serial)) {
        errEl.textContent = 'Duplicate serial.'; errEl.style.display = 'block'; $(this).select(); return;
    }
    Swal.fire({
        title: serial,
        text: 'Inspection result for this unit?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'GOOD',
        cancelButtonText: 'NO GOOD',
        reverseButtons: true,
        allowOutsideClick: false,
    }).then(result => {
        if (result.isConfirmed) {
            state.scanned.push({ serial, good: true, defects: [] });
            renderSerialList(); updateCounts(); updateJudgement();
            $('#serial_input').val('').focus();
        } else {
            state.currentSerial = serial;
            openNgModal(serial);
        }
    });
});

// ── NO GOOD MODAL ─────────────────────────────────────────────────────────────
function initSelect2InRow(row) {
    $(row).find('.location-select').select2({
        tags: true,
        placeholder: 'Select or type locations',
        tokenSeparators: [','],
        width: '100%',
        dropdownParent: $('#ngModal'),
        language: { noResults: () => $('<span>No record found</span>') },
        escapeMarkup: m => m,
    });
}

function openNgModal(serial) {
    $('#ng_serial').val(serial);
    $('#ng_defect_rows').html(defectRowTemplate());
    initSelect2InRow($('#ng_defect_rows .ng-defect-row')[0]);
    $('#ngModal').addClass('active');
    setTimeout(() => $('#ng_defect_rows .defect-input').first().focus(), 100);
}

function closeNgModal() {
    $('#ngModal').removeClass('active');
    $('#serial_input').val('').focus();
}

$('#closeNgModal, #ngCancelBtn').on('click', function() {
    // cancelled without saving — push as GOOD
    state.scanned.push({ serial: state.currentSerial, good: true, defects: [] });
    renderSerialList(); updateCounts(); updateJudgement();
    closeNgModal();
});

$('#addNgDefectBtn').on('click', function() {
    const newRow = $(defectRowTemplate());
    newRow.css({ marginTop:'14px', borderTop:'1px solid #eee', paddingTop:'14px' });
    $('#ng_defect_rows').append(newRow);
    initSelect2InRow(newRow[0]);
});

$('#ngSaveBtn').on('click', function() {
    const defects = [];
    let valid = true;
    $('#ng_defect_rows .ng-defect-row').each(function() {
        const defect    = $(this).find('.defect-input').val().trim().toUpperCase();
        const locations = $(this).find('.location-select').val() || [];
        const severity  = $(this).find('.severity-radio:checked').val() || 'major';
        if (!defect && !locations.length) return;
        if (!defect || !locations.length) { valid = false; return; }
        defects.push({ defect, locations, severity });
    });
    if (!valid || !defects.length) {
        Swal.fire({ icon:'warning', title:'Incomplete', text:'Each defect row needs both a defect and location.',
            toast:true, position:'top-right', timer:3000, showConfirmButton:false });
        return;
    }

    defects.forEach(d => {
        if (d.severity === 'major') state.defects015 += 1;
        else                        state.defects10  += 1;
    });

    state.scanned.push({ serial: state.currentSerial, good: false, defects });
    closeNgModal();
    renderSerialList(); updateCounts(); updateJudgement();
});

$('#ngBtn').on('click', function() {
    const serial = $('#serial_input').val().trim().toUpperCase();
    if (!serial) {
        Swal.fire({ icon:'warning', title:'Scan First', text:'Scan a serial before marking No Good.',
            toast:true, position:'top-right', timer:2500, showConfirmButton:false });
        return;
    }
    state.currentSerial = serial;
    openNgModal(serial);
});

// ── FINALIZE ──────────────────────────────────────────────────────────────────
$('#finalizeBtn').on('click', function() {
    const { aqlParams, defects015, defects10, scanned, sampleSize, letter, method } = state;
    const failed015 = aqlParams.aql015.re !== null && defects015 >= aqlParams.aql015.re;
    const failed10  = aqlParams.aql10.re  !== null && defects10  >= aqlParams.aql10.re;
    const judgement = (failed015 || failed10) ? 'REJECT' : 'ACCEPT';

    Swal.fire({
        icon: judgement === 'ACCEPT' ? 'success' : 'error',
        title: `Lot ${judgement}`,
        html: `<div style="text-align:left;font-size:14px;line-height:2;">
            <b>Code Letter:</b> ${letter}<br>
            <b>Method:</b> ${method.charAt(0).toUpperCase()+method.slice(1)}<br>
            <b>Sample Size:</b> ${sampleSize}<br>
            <b>Inspected:</b> ${scanned.length}<br>
            <b>NG Units:</b> ${scanned.filter(s=>!s.good).length}<br>
            <hr style="margin:8px 0;">
            <b>AQL 0.15 (Major):</b> ${defects015} defects &nbsp;|&nbsp; Re: ${fmtAcRe(aqlParams.aql015.re)}<br>
            <b>AQL 1.0 (Minor):</b> ${defects10} defects &nbsp;|&nbsp; Re: ${fmtAcRe(aqlParams.aql10.re)}<br>
        </div>`,
        confirmButtonText: 'CONFIRM & SUBMIT',
        showCancelButton: true,
        cancelButtonText: 'GO BACK',
    }).then(result => {
        if (result.isConfirmed) submitQAInspection();
    });
});

// ── SUBMIT ────────────────────────────────────────────────────────────────────
function submitQAInspection() {
    const kepi_lot  = $('#kepi_lot').val().trim();
    const failed015 = state.aqlParams.aql015.re !== null && state.defects015 >= state.aqlParams.aql015.re;
    const failed10  = state.aqlParams.aql10.re  !== null && state.defects10  >= state.aqlParams.aql10.re;
    const lot_result = (failed015 || failed10) ? 'REJECT' : 'ACCEPT';

    // 💡 Added strict application header & absolute URL paths context 
    fetch('/traceabilitydev/QA/check_lot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ kepi_lot })
    })
    .then(r => {
        if (!r.ok) throw new Error(`HTTP check_lot error! status: ${r.status}`);
        return r.json();
    })
    .then(check => {
        if (check.accepted) {
            Swal.fire({
                icon: 'warning',
                title: 'Lot Already Accepted',
                html: `Lot <b>${kepi_lot}</b> has already been accepted in a previous inspection.<br><br>Are you sure you want to submit again?`,
                confirmButtonText: 'SUBMIT ANYWAY',
                showCancelButton: true,
                cancelButtonText: 'CANCEL',
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('/traceabilitydev/QA/delete_lot.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ kepi_lot })
                    })
                    .then(r => {
                        if (!r.ok) throw new Error(`HTTP delete_lot error! status: ${r.status}`);
                        return r.json();
                    })
                    .then(del => {
                        if (del.status === 'success') {
                            doInserts(lot_result);
                        } else {
                            Swal.fire({ icon:'error', title:'Error', text:'Failed to clear previous records.' });
                        }
                    })
                    .catch(err => {
                        console.error("Error clearing lot data:", err);
                        Swal.fire({ icon:'error', title:'Network Error', text:'Failed to run deletion script.' });
                    });
                }
            });
        } else {
            doInserts(lot_result);
        }
    })
    .catch((error) => {
        console.error("Network validation error details:", error);
        Swal.fire({ icon:'error', title:'Network Error', text:'Failed to check lot verification status. See console for details.' });
    });
}

// ── KEPI LOT FETCH ────────────────────────────────────────────────────────────
var KepiLotTimer;

$('#kepi_lot').on('change input', function() {
    clearTimeout(KepiLotTimer);
    const lot = $(this).val().trim();
    if (!lot) return;
    KepiLotTimer = setTimeout(function() {
        $.ajax({
            url: 'QA/fetch_model.php',
            type: 'POST',
            data: { kepi_lot: lot },
            success: function(response) {
                if (response.success) {
                    $('#model_name').val(response.data);
                    checkStartReady();
                } else {
                    console.error('Error fetching model:', response.message);
                    $('#model_name').val('');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
            }
        });

         $.ajax({
                url: 'QA/check_lot.php',
                type: 'POST',
                data: { kepi_lot: lot },
                success: function(check) {
                    if (check.accepted) {
                        $('#lot_dupe_warning').show().text('⚠ This lot was already ACCEPTED in a previous inspection.');
                    } else {
                        $('#lot_dupe_warning').hide();
                    }
                }
            });
            }, 500);
});

// ── UNLOAD GUARD ──────────────────────────────────────────────────────────────
window.addEventListener('beforeunload', function(e) {
    if (state && state.active && !allowReload) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
</body>
</html>