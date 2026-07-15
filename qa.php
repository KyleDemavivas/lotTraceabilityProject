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
                <div class="form-group" style="display:none;">
                    <label class="form-label">Code Letter:</label>
                    <input type="text" class="form-input" id="code_letter" readonly placeholder="—">
                </div>
                <div class="form-group">
                    <label class="form-label">Model:</label>
                    <input type="text" class="form-input" id="model_name" placeholder="—" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Assy No.:</label>
                    <input type="text" class="form-input" id="assy_no" autocomplete="off" placeholder="—" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Customer:</label>
                    <input type="text" class="form-input" id="customer" autocomplete="off" placeholder="Enter customer">
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
                    <label class="form-label">Inspection Level:</label>
                    <select class="form-input" id="inspection_level">
                        <option value="">— Select Level —</option>
                        <option value="I">Level I</option>
                        <option value="II">Level II</option>
                        <option value="III">Level III</option>
                        <option value="S-1">Special Level S-1</option>
                        <option value="S-2">Special Level S-2</option>
                        <option value="S-3">Special Level S-3</option>
                        <option value="S-4">Special Level S-4</option>
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
                        <option value="Line 1">Line 1</option>
                        <option value="Line 2">Line 2</option>
                        <option value="Line 3">Line 3</option>
                        <option value="Line 4">Line 4</option>
                        <option value="Line 5">Line 5</option>
                        <option value="Line 6">Line 6</option>
                        <option value="Line 7">Line 7</option>
                        <option value="Line 8">Line 8</option>
                        <option value="Line 9">Line 9</option>
                        <option value="Line 10">Line 10</option>
                        <option value="Line 11">Line 11</option>
                        <option value="Line 12">Line 12</option>
                        <option value="LINE A">LINE A</option>
                        <option value="LINE I">LINE I</option>
                        <option value="LINE O">LINE O</option>
                        <option value="LINE B">LINE B</option>
                        <option value="LINE H">LINE H</option>
                        <option value="LINE P">LINE P</option>
                        <option value="LINE J">LINE J</option>
                        <option value="LINE M">LINE M</option>
                        <option value="LINE N">LINE N</option>
                        <option value="LINE R">LINE R</option>
                        <option value="LINE D">LINE D</option>
                        <option value="LINE C">LINE C</option>
                        <option value="LINE L">LINE L</option>
                        <option value="LINE F">LINE F</option>
                        <option value="LINE K">LINE K</option>
                        <option value="LINE Q">LINE Q</option>
                        <option value="LINE X">LINE X</option>
                        <option value="FG 1">FG 1</option>
                        <option value="FG 2">FG 2</option>
                        <option value="FG 3">FG 3</option>
                    </select>
                </div>
                <div style="margin-top:20px;">
                    <button id="startBtn" class="btn-inline" disabled>START INSPECTION</button>
                    <button id="continueBtn" class="btn-inline" style="display:none;">CONTINUE INSPECTION</button>
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

// ── DEFECT SEVERITY + CATEGORY MAP (from Defect Classification Standard) ──────
// severity: null = depends on a sub-condition; operator selects manually.
const DEFECT_SEVERITY = {
    'Conductor spacing': { severity: 'minor', category: 'Parts Appearance' },
    'Contacting Lead': { severity: 'minor', category: 'Parts Appearance' },
    'Crack Solder': { severity: 'minor', category: 'Solderability Condition' },
    'Cut Pattern': { severity: 'major', category: 'PCB Appearance' },
    'Damaged Component': { severity: 'major', category: 'Parts Appearance' },
    'Deformed Pin': { severity: 'minor', category: 'Parts Appearance' },
    'Detached Component': { severity: 'major', category: 'Parts Appearance' },
    'Electrode Corrosion': { severity: 'minor', category: 'Parts Appearance' },
    'Excess Solder': { severity: 'minor', category: 'Solderability Condition' },
    'Floating Component': { severity: 'minor', category: 'Mounting Condition' },
    'Flux': { severity: 'major', category: 'Parts Appearance' },
    'Foreign Material': { severity: null, category: 'Parts Appearance' }, // Non-conductive = Minor, Conductive = Major
    'Insufficient Solder': { severity: null, category: 'Solderability Condition' }, // IC/PCB parts = Major, HW parts = Minor
    'Inverted Component': { severity: 'minor', category: 'Mounting Condition' },
    'Lead Not Inserted': { severity: 'minor', category: 'Mounting Condition' },
    'Lead to Lead': { severity: 'minor', category: 'Parts Appearance' },
    'Lead to Pattern': { severity: 'minor', category: 'Parts Appearance' },
    'Lead to Solder': { severity: 'minor', category: 'Parts Appearance' },
    'Lead Too Long/Short': { severity: 'minor', category: 'Parts Appearance' },
    'Lifted Component': { severity: 'minor', category: 'Mounting Condition' },
    'Lifted Lead': { severity: 'minor', category: 'Parts Appearance' },
    'Lifted Solder': { severity: 'minor', category: 'Solderability Condition' },
    'Horizontal/Rotational Misalignment': { severity: 'minor', category: 'Mounting Condition' },
    'Missing Component': { severity: 'major', category: 'Mounting Condition' },
    'Missing/Damaged Silk Print': { severity: 'minor', category: 'Parts Appearance' },
    'No Solder': { severity: 'major', category: 'Solderability Condition' },
    'Non-Legible of Specification': { severity: 'minor', category: 'Parts Appearance' },
    'Remove Pattern': { severity: 'minor', category: 'PCB Appearance' },
    'Resist Peeling': { severity: 'minor', category: 'PCB Appearance' },
    'Solder Ball': { severity: 'minor', category: 'Solderability Condition' },
    'Solder Bridge': { severity: 'major', category: 'Solderability Condition' },
    'Solder Horn/Icicle': { severity: 'minor', category: 'Solderability Condition' },
    'Solder Splash/Residue': { severity: 'minor', category: 'Solderability Condition' },
    'Solder Spouting': { severity: 'minor', category: 'Solderability Condition' },
    'Tombstone': { severity: 'minor', category: 'Mounting Condition' },
    'Uneven Pin Height': { severity: 'minor', category: 'Parts Appearance' },
    'Wrong Component': { severity: 'major', category: 'Parts Appearance' },
    'Wrong Polarity': { severity: 'major', category: 'Mounting Condition' },
    'Vertical Misalignment': { severity: 'minor', category: 'Mounting Condition' },
    'Component Chip Off': { severity: 'minor', category: 'Parts Appearance' },
    'Board Chip Off': { severity: 'minor', category: 'PCB Appearance' },
};

// Maps the spreadsheet's category labels to the finalize-payload field keys.
const CATEGORY_FIELD_MAP = {
    'Parts Appearance': 'parts_appearance',
    'PCB Appearance': 'pcb_appearance',
    'Solderability Condition': 'solder_condition',
    'Mounting Condition': 'subassembly_condition',
};

// Defect names are stored UPPERCASED once an NG entry is saved (see ngSaveBtn),
// but DEFECT_SEVERITY is keyed in Title Case. Build a case-insensitive index
// so category/severity re-derivation (e.g. recomputeCategoryCounts) still matches.
const DEFECT_SEVERITY_LOOKUP = {};
Object.keys(DEFECT_SEVERITY).forEach(k => {
    DEFECT_SEVERITY_LOOKUP[k.toUpperCase()] = DEFECT_SEVERITY[k];
});

let allowReload = false;
let pollTimer = null;
const POLL_INTERVAL_MS = 3000;

// ── STATE ─────────────────────────────────────────────────────────────────────
let state = {
    active: false, letter: null, method: null, sampleSize: 0, lotQty: 0,
    scanned: [], currentSerial: null,
    defects015: 0, defects10: 0, aqlParams: null,
    scanCountForSpec: 0,   // tracks GOOD boards seen so far, for the first-5 Parts Spec popup
    inspectionId: null, attemptNumber: null,
};

// ── SERVER SYNC HELPERS ──────────────────────────────────────────────────────
function applyServerSerials(serverSerials) {
    state.scanned = serverSerials.map(s => ({
        serial: s.serial_code,
        good: s.status === 'GOOD',
        defects: s.defect_code ? [{
            defect: s.defect_code, severity: (s.severity || 'major').split(', ')[0],
            locations: s.location ? s.location.split(', ') : []
        }] : [],
        parts_specification: s.parts_specification || null,
    }));
    state.scanCountForSpec = state.scanned.length;
}

// Derives category counts from every recorded defect across all scanned boards.
// Counts per defect occurrence (a board with 2 Parts Appearance defects counts as 2).
function recomputeCategoryCounts() {
    const counts = { parts_appearance: 0, pcb_appearance: 0, solder_condition: 0, subassembly_condition: 0 };
    state.scanned.forEach(s => {
        s.defects.forEach(d => {
            d.defect.split(', ').forEach(name => {
                const entry = DEFECT_SEVERITY_LOOKUP[name.trim().toUpperCase()];
                const field = entry && CATEGORY_FIELD_MAP[entry.category];
                if (field) counts[field]++;
            });
        });
    });
    return counts;
}

function submitScan(serial, status, location, defect_code, severity, parts_spec, majorCount, minorCount) {
    $('#serial_input').prop('disabled', true);
    $.post('QA/qa_scan.php', {
        inspection_id: state.inspectionId,
        serial_code: serial,
        status: status,
        location: location,
        defect_code: defect_code,
        severity: severity,
        parts_specification: parts_spec,
        major_count: majorCount,
        minor_count: minorCount,
        scanned_by: '<?php echo htmlspecialchars($_SESSION['user_namefl'] ?? 'Unknown'); ?>',
    }, null, 'json')
    .done(function(res) {
        if (res.status === 'duplicate') {
            document.getElementById('serial_error').textContent = res.message;
            document.getElementById('serial_error').style.display = 'block';
            syncFromServer(); // pull the real state, someone else beat us to it
        } else if (res.status !== 'success') {
            Swal.fire({ icon:'error', title:'Scan Failed', text: res.message || 'Could not save scan.' });
        } else {
            state.scanned.push({ serial, good: status === 'GOOD',
                defects: defect_code ? [{ defect: defect_code, severity: severity || 'major', locations: location ? location.split(', ') : [] }] : [],
                parts_specification: parts_spec });
            state.scanCountForSpec++;
            state.defects015 += majorCount;
            state.defects10  += minorCount;
            renderSerialList(); updateCounts(); updateJudgement();
        }
    })
    .fail(function() {
        Swal.fire({ icon:'error', title:'Network Error', text:'Scan was not saved — check connection and rescan.' });
    })
    .always(function() {
        $('#serial_input').prop('disabled', false).val('').focus();
    });
}

function syncFromServer() {
    if (!state.inspectionId) return;
    $.getJSON('QA/qa_lot_state.php', { inspection_id: state.inspectionId })
        .done(function(res) {
            if (res.status !== 'success' || !res.lot) return;
            if (res.lot.status !== 'IN_PROGRESS') {
                // Someone else finalized this lot from another tab
                stopPolling();
                Swal.fire({ icon:'info', title:'Lot Finalized',
                    text:'This lot was finalized from another session.' })
                    .then(() => { allowReload = true; location.reload(); });
                return;
            }
            // Defect counts only ever go up during an inspection. If this poll landed
            // before the server's aggregate columns caught up with a scan we already
            // know succeeded locally, don't let the count regress — that's what was
            // causing the REJECT banner to flash and then disappear.
            state.defects015 = Math.max(state.defects015, res.lot.defects_015);
            state.defects10  = Math.max(state.defects10,  res.lot.defects_10);
            applyServerSerials(res.serials);
            renderSerialList();
            updateCounts();
            updateJudgement();
        });
}

function startPolling() {
    stopPolling();
    pollTimer = setInterval(syncFromServer, POLL_INTERVAL_MS);
}

function stopPolling() {
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
}

// ── HELPERS ───────────────────────────────────────────────────────────────────
function getCodeLetter(qty) {
    for (const [min, max, letter] of LEVEL3_TABLE) {
        if (qty >= min && qty <= max) return letter;
    }
    return null;
}

function setSetupFieldsDisabled(disabled) {
    $('#lot_qty, #inspection_method, #inspection_level, #shift, #line, #customer').prop('disabled', disabled);
    if (disabled) {
        $('#startBtn').hide();
    } else {
        $('#startBtn').show();
        checkStartReady();
    }
}

function fmtAcRe(val) { return val === null ? '↑↓' : val; }

function updateJudgement() {
    const { aqlParams, defects015, defects10, scanned, sampleSize } = state;
    const banner = document.getElementById('judgementBanner');
    const failed015 = aqlParams.aql015.re !== null && defects015 >= aqlParams.aql015.re;
    const failed10  = aqlParams.aql10.re  !== null && defects10  >= aqlParams.aql10.re;
    const rejected  = failed015 || failed10;
    const complete  = scanned.length >= sampleSize;

    if (rejected) {
        banner.className = 'judgement-banner fail';
        banner.textContent = '✕ REJECT — Defect threshold exceeded';
        document.getElementById('serial_error').style.display = 'none';
        // enable finalize so they can still submit
        document.getElementById('finalizeBtn').disabled = false;
        // Don't lock scanning just because REJECT triggered — for fullcheck (sample
        // size = lot qty) the operator still needs to document the rest of the lot.
        // Only stop once every unit has actually been scanned.
        $('#serial_input').prop('disabled', complete).val(complete ? '' : $('#serial_input').val());
        $('#ngBtn').prop('disabled', complete);
    } else if (complete) {
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
        const specTag = s.parts_specification
            ? `<span class="ng-detail" style="color:#4facfe;">Spec: ${s.parts_specification}</span>`
            : '';
        return `<div class="serial-row ${s.good ? '' : 'ng'}">
            <span class="serial-num">#${String(i+1).padStart(2,'0')}</span>
            <span class="serial-code">${s.serial}</span>
            ${detail ? `<span class="ng-detail">${detail}</span>` : ''}
            ${specTag}
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

function finalizeInspection(lot_result, finalizeData) {
    if (!state.inspectionId) {
        Swal.fire({ icon:'error', title:'Error', text:'No active inspection session found.' });
        return;
    }

    const payload = Object.assign({
        inspection_id: state.inspectionId,
        lot_result: lot_result,
    }, finalizeData);

    $.post('QA/qa_finalize.php', payload, null, 'json')
        .done(function(result) {
            if (result.status === 'success') {
                stopPolling();
                Swal.fire({ icon:'success', title:'Submitted',
                    text:'QA inspection records submitted successfully.',
                    toast:true, position:'top-end', timer:1500, showConfirmButton:false })
                .then(() => {
                    allowReload = true;
                    location.reload();
                });
            } else {
                Swal.fire({ icon:'error', title:'Submit Failed', text: result.message || 'Failed to finalize.' });
            }
        })
        .fail(function() {
            Swal.fire({ icon:'error', title:'Network Error', text:'Failed to reach the server to finalize.' });
        });
}

function deriveJudgement(method, defects015, defects10, aqlParams) {
    const failed015 = aqlParams.aql015.re !== null && defects015 >= aqlParams.aql015.re;
    const failed10  = aqlParams.aql10.re  !== null && defects10  >= aqlParams.aql10.re;
    const rejected  = failed015 || failed10;
    const hasDefects = (defects015 + defects10) > 0;
    const isFullcheck = method === 'fullcheck';

    if (rejected) {
        return isFullcheck ? 'NG Reject' : 'D Lot Out';
    }
    if (isFullcheck && !hasDefects) {
        return 'GO Accept';
    }
    if (!isFullcheck && !hasDefects) {
        return 'A Passed';
    }
    // Any method with some defects but under threshold (includes fullcheck-with-defects)
    return 'B Passed';
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
                <select class="defect-select" style="width:100%;">
                    <option value="">Select a defect</option>
                    <option value="Conductor spacing">Conductor spacing</option>
                    <option value="Contacting Lead">Contacting Lead</option>
                    <option value="Crack Solder">Crack Solder</option>
                    <option value="Cut Pattern">Cut Pattern</option>
                    <option value="Damaged Component">Damaged Component</option>
                    <option value="Deformed Pin">Deformed Pin</option>
                    <option value="Detached Component">Detached Component</option>
                    <option value="Electrode Corrosion">Electrode Corrosion</option>
                    <option value="Excess Solder">Excess Solder</option>
                    <option value="Floating Component">Floating Component</option>
                    <option value="Flux">Flux</option>
                    <option value="Foreign Material">Foreign Material</option>
                    <option value="Insufficient Solder">Insufficient Solder</option>
                    <option value="Inverted Component">Inverted Component</option>
                    <option value="Lead Not Inserted">Lead Not Inserted</option>
                    <option value="Lead to Lead">Lead to Lead</option>
                    <option value="Lead to Pattern">Lead to Pattern</option>
                    <option value="Lead to Solder">Lead to Solder</option>
                    <option value="Lead Too Long/Short">Lead Too Long/Short</option>
                    <option value="Lifted Component">Lifted Component</option>
                    <option value="Lifted Lead">Lifted Lead</option>
                    <option value="Lifted Solder">Lifted Solder</option>
                    <option value="Horizontal/Rotational Misalignment">Horizontal/Rotational Misalignment</option>
                    <option value="Missing Component">Missing Component</option>
                    <option value="Missing/Damaged Silk Print">Missing/Damaged Silk Print</option>
                    <option value="No Solder">No Solder</option>
                    <option value="Non-Legible of Specification">Non-Legible of Specification</option>
                    <option value="Remove Pattern">Remove Pattern</option>
                    <option value="Resist Peeling">Resist Peeling</option>
                    <option value="Solder Ball">Solder Ball</option>
                    <option value="Solder Bridge">Solder Bridge</option>
                    <option value="Solder Horn/Icicle">Solder Horn/Icicle</option>
                    <option value="Solder Splash/Residue">Solder Splash/Residue</option>
                    <option value="Solder Spouting">Solder Spouting</option>
                    <option value="Tombstone">Tombstone</option>
                    <option value="Uneven Pin Height">Uneven Pin Height</option>
                    <option value="Wrong Component">Wrong Component</option>
                    <option value="Wrong Polarity">Wrong Polarity</option>
                    <option value="Vertical Misalignment">Vertical Misalignment</option>
                    <option value="Component Chip Off">Component Chip Off</option>
                    <option value="Board Chip Off">Board Chip Off</option>
                </select>
            </div>
            <div class="half-group">
                <label>Location</label>
                <select class="location-select" multiple="multiple" style="width:100%;">${locationOptions}</select>
            </div>
        </div>
        <div style="margin-top:8px; display:none;" class="severity-wrap">
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
            $('#sample_size').val(qty);
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
    const sample = method === 'fullcheck' ? qty : data.sample[method];
    const $btn = $(this).prop('disabled', true).text('STARTING...');

    $.post('QA/qa_session.php', {
        kepi_lot: $('#kepi_lot').val().trim(),
        model: $('#model_name').val(),
        inspection_method: method,
        code_letter: letter,
        sample_size: sample,
        lot_qty: qty,
        line: $('#line').val(),
        shift: $('#shift').val(),
        operator_id: '<?php echo htmlspecialchars($_SESSION['user_namefl'] ?? 'Unknown'); ?>',
        customer: $('#customer').val().trim(),
        assy_no: $('#assy_no').val().trim(),
        inspection_level: $('#inspection_level').val(),
    }, null, 'json')
    .done(function(res) {
        if (res.status !== 'success') {
            Swal.fire({ icon:'error', title:'Error', text: res.message || 'Failed to start inspection.' });
            $btn.prop('disabled', false).text('START INSPECTION');
            return;
        }

        if (res.joined) {
            const lot = res.lot;
            const jLetter = lot.code_letter, jMethod = lot.inspection_method, jSample = lot.sample_size;
            const jData = AQL_DATA[jLetter];
            const jParams = jMethod === 'fullcheck' ? jData.normal : jData[jMethod];

            state = {
                active: true, letter: jLetter, method: jMethod, sampleSize: jSample, lotQty: lot.lot_quantity,
                aqlParams: jParams, scanned: [], currentSerial: null,
                defects015: lot.defects_015, defects10: lot.defects_10,
                scanCountForSpec: 0, inspectionId: lot.id, attemptNumber: lot.attempt_number,
            };
            applyServerSerials(res.serials);

            $('#disp_letter').text(jLetter);
            $('#disp_sample').text(jSample);
            $('#disp_method').text(jMethod.charAt(0).toUpperCase() + jMethod.slice(1));
            $('#disp_lotqty').text(lot.lot_quantity);
            $('#re_015').text(fmtAcRe(jParams.aql015.re));
            $('#re_10').text(fmtAcRe(jParams.aql10.re));
            $('#sample_total').text(jSample);

            Swal.fire({ icon:'info', title:'Joined In-Progress Inspection',
                text:`Attempt #${lot.attempt_number} is already being inspected. You're now scanning the same session — the method/sample size are locked to what was started first.`,
                toast:true, position:'top-end', timer:4000, showConfirmButton:false });
        } else {
            state = {
                active: true, letter, method, sampleSize: sample, lotQty: qty,
                aqlParams: params, scanned: [], currentSerial: null,
                defects015: 0, defects10: 0,
                scanCountForSpec: 0, inspectionId: res.lot_id, attemptNumber: res.attempt_number,
            };
            $('#disp_letter').text(letter);
            $('#disp_sample').text(sample);
            $('#disp_method').text(method.charAt(0).toUpperCase() + method.slice(1));
            $('#disp_lotqty').text(qty);
            $('#re_015').text(fmtAcRe(params.aql015.re));
            $('#re_10').text(fmtAcRe(params.aql10.re));
            $('#sample_total').text(sample);
        }

        $('#inspection_method, #inspection_level, #lot_qty').prop('disabled', true);
        $('#aqlPanel, #scanPanel').show();
        $('#serial_input').prop('disabled', false).focus();
        $('#ngBtn').prop('disabled', false);
        $btn.text('ACTIVE');
$('#continueBtn').hide();
        updateJudgement();
        renderSerialList();
        updateCounts();
        startPolling();
    })
    .fail(function() {
        Swal.fire({ icon:'error', title:'Network Error', text:'Could not reach the server to start inspection.' });
        $btn.prop('disabled', false).text('START INSPECTION');
    });
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
        const spec = state.scanCountForSpec < 5 ? "Part Spec" : null;
        submitScan(serial, 'GOOD', null, null, null, spec, 0, 0);
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
    setTimeout(() => $('#ng_defect_rows .defect-select').first().focus(), 100);
}

function closeNgModal() {
    $('#ngModal').removeClass('active');
    $('#serial_input').val('').focus();
}

$('#closeNgModal, #ngCancelBtn').on('click', function() {
    const serial = state.currentSerial;
    const spec = state.scanCountForSpec < 5 ? "Part Spec" : null;
    closeNgModal();
    submitScan(serial, 'GOOD', null, null, null, spec, 0, 0);
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
        const defect    = $(this).find('.defect-select').val().trim().toUpperCase();
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

    let majorCount = 0, minorCount = 0;
    defects.forEach(d => { if (d.severity === 'major') majorCount++; else minorCount++; });

    const locations   = defects.flatMap(d => d.locations).join(', ');
    const defectCodes = defects.map(d => d.defect).join(', ');
    const severities   = defects.map(d => d.severity).join(', ');
    const serial = state.currentSerial;
    const spec = state.scanCountForSpec < 5 ? "Part Spec" : null;

    closeNgModal();
    submitScan(serial, 'NO GOOD', locations, defectCodes, severities, spec, majorCount, minorCount);
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

// ── DEFECT AUTO-SEVERITY ───────────────────────────────────────────────────
$('#ng_defect_rows').on('change', '.defect-select', function() {
    const $row = $(this).closest('.ng-defect-row');
    const defect = $(this).val();
    const entry = DEFECT_SEVERITY[defect];
    const severity = entry ? entry.severity : undefined;

    if (defect && severity === null) {
        // Foreign Material / Insufficient Solder — needs manual judgement call
        $row.find('.severity-wrap').show();
    } else {
        // everything else — auto-classified, no need to show the operator a choice
        $row.find('.severity-wrap').hide();
        if (severity) {
            $row.find(`.severity-radio[value="${severity}"]`).prop('checked', true);
        }
    }
});

// ── FINALIZE ──────────────────────────────────────────────────────────────────
$('#finalizeBtn').on('click', function() {
    const { aqlParams, defects015, defects10, scanned, sampleSize, letter, method, lotQty } = state;
    const failed015 = aqlParams.aql015.re !== null && defects015 >= aqlParams.aql015.re;
    const failed10  = aqlParams.aql10.re  !== null && defects10  >= aqlParams.aql10.re;
    const judgement = (failed015 || failed10) ? 'REJECT' : 'ACCEPT';
    const derivedJudgement = deriveJudgement(method, defects015, defects10, aqlParams);

    const categoryCounts = recomputeCategoryCounts();
    const denom = method === 'fullcheck' ? lotQty : sampleSize;

    const remarkFieldsHtml = `
        <hr style="margin:12px 0;">
        <div style="text-align:left; font-size:13px;">
            <div class="form-group"><label class="form-label" style="min-width:160px;">Parts Appearance:</label><span style="font-weight:600;">${categoryCounts.parts_appearance}/${denom}</span></div>
            <div class="form-group"><label class="form-label" style="min-width:160px;">PCB Appearance:</label><span style="font-weight:600;">${categoryCounts.pcb_appearance}/${denom}</span></div>
            <div class="form-group"><label class="form-label" style="min-width:160px;">Solder Condition:</label><span style="font-weight:600;">${categoryCounts.solder_condition}/${denom}</span></div>
            <div class="form-group"><label class="form-label" style="min-width:160px;">Sub Assembly Condition:</label><span style="font-weight:600;">${categoryCounts.subassembly_condition}/${denom}</span></div>
            <div class="form-group"><label class="form-label" style="min-width:160px;">Labels/Markings:</label><span style="font-weight:600;">0/${denom}</span></div>
            <div class="form-group"><label class="form-label" style="min-width:160px;">Package Condition:</label><span style="font-weight:600;">0/${denom}</span></div>
        </div>
    `;

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
            <hr style="margin:8px 0;">
            <div class="form-group">
                <label class="form-label" style="min-width:160px;">Judgement:</label>
                <select class="form-input" id="fz_judgement">
                    <option value="A Passed" ${derivedJudgement === 'A Passed' ? 'selected' : ''}>A Passed</option>
                    <option value="B Passed" ${derivedJudgement === 'B Passed' ? 'selected' : ''}>B Passed</option>
                    <option value="D Lot Out" ${derivedJudgement === 'D Lot Out' ? 'selected' : ''}>D Lot Out</option>
                    <option value="GO Accept" ${derivedJudgement === 'GO Accept' ? 'selected' : ''}>GO Accept</option>
                    <option value="NG Reject" ${derivedJudgement === 'NG Reject' ? 'selected' : ''}>NG Reject</option>
                </select>
            </div>
            ${remarkFieldsHtml}
        </div>`,
        confirmButtonText: 'CONFIRM & SUBMIT',
        showCancelButton: true,
        cancelButtonText: 'GO BACK',
        preConfirm: () => {
            const selectedJudgement = document.getElementById('fz_judgement').value;
            if (selectedJudgement !== derivedJudgement) {
                Swal.showValidationMessage(`Judgement must match the calculated result: ${derivedJudgement}`);
                return false;
            }
            return {
                judgement: selectedJudgement,
                parts_appearance:      `${categoryCounts.parts_appearance}/${denom}`,
                pcb_appearance:        `${categoryCounts.pcb_appearance}/${denom}`,
                solder_condition:      `${categoryCounts.solder_condition}/${denom}`,
                subassembly_condition: `${categoryCounts.subassembly_condition}/${denom}`,
                labels_markings:       `0/${denom}`,
                package_condition:     `0/${denom}`,
            };
        },
    }).then(result => {
        if (result.isConfirmed) submitQAInspection(result.value);
    });
});

// ── SUBMIT ────────────────────────────────────────────────────────────────────
function submitQAInspection(finalizeData) {
    const failed015 = state.aqlParams.aql015.re !== null && state.defects015 >= state.aqlParams.aql015.re;
    const failed10  = state.aqlParams.aql10.re  !== null && state.defects10  >= state.aqlParams.aql10.re;
    const lot_result = (failed015 || failed10) ? 'REJECT' : 'ACCEPT';

    finalizeInspection(lot_result, finalizeData);
}

// ── KEPI LOT FETCH ────────────────────────────────────────────────────────────
var KepiLotTimer;
var lastCheckedLot = null;

$('#kepi_lot').on('input change', function() {
    clearTimeout(KepiLotTimer);
    const lot = $(this).val().trim();
    if (!lot) return;

    KepiLotTimer = setTimeout(function() {
        if (lot === lastCheckedLot) return;   // ← skip redundant re-check
        lastCheckedLot = lot;

        $.ajax({
            url: 'QA/fetch_model.php',
            type: 'POST',
            data: { kepi_lot: lot },
            success: function(response) {
                if (response.success) {
                    $('#model_name').val(response.data.model_name);
                    $('#assy_no').val(response.data.assy_code || '');
                    checkStartReady();
                } else {
                    console.error('Error fetching model:', response.message);
                    $('#model_name').val('');
                    $('#assy_no').val('');
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
                if (check.in_progress) {
                    $('#lot_dupe_warning').show().text(
                        `⚠ This lot is currently IN PROGRESS (Attempt #${check.attempt_number || '?'}). Click Continue to join the scanning session.`
                    );
                    setSetupFieldsDisabled(true);
                    $('#continueBtn').show().prop('disabled', false).text('CONTINUE INSPECTION');
                    return;
                }

                $('#continueBtn').hide();
                setSetupFieldsDisabled(false);

                // attempt_count can arrive as a string from the backend (e.g. an
                // uncast SQL COUNT()) — force it to a number so "+1" adds instead
                // of concatenating (was showing "attempt #11" instead of "#2").
                const attemptCount = Number(check.attempt_count) || 0;

                if (check.accepted) {
                    $('#lot_dupe_warning').show().text(
                        `⚠ This lot was already ACCEPTED on attempt #${attemptCount}. This will be recorded as attempt #${attemptCount + 1}.`
                    );
                } else if (attemptCount > 0) {
                    $('#lot_dupe_warning').show().text(
                        `This lot has ${attemptCount} prior attempt(s), most recent result: ${check.last_result}.`
                    );
                } else {
                    $('#lot_dupe_warning').hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
            }
        });
    }, 500);
});

$('#continueBtn').on('click', function() {
    const $btn = $(this).prop('disabled', true).text('JOINING...');

    $.post('QA/qa_session.php', {
        kepi_lot: $('#kepi_lot').val().trim(),
        model: $('#model_name').val(),
        operator_id: '<?php echo htmlspecialchars($_SESSION['user_namefl'] ?? 'Unknown'); ?>',
    }, null, 'json')
    .done(function(res) {
        if (res.status !== 'success' || !res.joined) {
            Swal.fire({ icon:'error', title:'Error', text: res.message || 'Could not join this inspection.' });
            $btn.prop('disabled', false).text('CONTINUE INSPECTION');
            return;
        }

        const lot = res.lot;
        const jLetter = lot.code_letter, jMethod = lot.inspection_method, jSample = lot.sample_size;
        const jData = AQL_DATA[jLetter];
        const jParams = jMethod === 'fullcheck' ? jData.normal : jData[jMethod];

        state = {
            active: true, letter: jLetter, method: jMethod, sampleSize: jSample, lotQty: lot.lot_quantity,
            aqlParams: jParams, scanned: [], currentSerial: null,
            defects015: lot.defects_015, defects10: lot.defects_10,
            scanCountForSpec: 0, inspectionId: lot.id, attemptNumber: lot.attempt_number,
        };
        applyServerSerials(res.serials);

        $('#disp_letter').text(jLetter);
        $('#disp_sample').text(jSample);
        $('#disp_method').text(jMethod.charAt(0).toUpperCase() + jMethod.slice(1));
        $('#disp_lotqty').text(lot.lot_quantity);
        $('#re_015').text(fmtAcRe(jParams.aql015.re));
        $('#re_10').text(fmtAcRe(jParams.aql10.re));
        $('#sample_total').text(jSample);

        $('#kepi_lot').prop('disabled', true);
        $('#aqlPanel, #scanPanel').show();
        $('#serial_input').prop('disabled', false).focus();
        $('#ngBtn').prop('disabled', false);
        $('#continueBtn').hide();

        Swal.fire({ icon:'info', title:'Joined In-Progress Inspection',
            text:`You're now scanning attempt #${lot.attempt_number} alongside the original session.`,
            toast:true, position:'top-end', timer:4000, showConfirmButton:false });

        updateJudgement();
        renderSerialList();
        updateCounts();
        startPolling();
    })
    .fail(function() {
        Swal.fire({ icon:'error', title:'Network Error', text:'Could not reach the server.' });
        $btn.prop('disabled', false).text('CONTINUE INSPECTION');
    });
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