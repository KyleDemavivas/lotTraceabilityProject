<?php include 'sidebar.php'; ?>
<?php
if (!isset($_SESSION['user_namefl'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

$defects = [];
try {
    $stmt = $conn->query("SELECT defect FROM defect_master ORDER BY defect ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $defects[] = $row['defect'];
    }
} catch (PDOException $e) {
    die("Error fetching defects: " . $e->getMessage());
}

$locations = [];
try {
    $stmt = $conn->query("SELECT location FROM location_master ORDER BY location ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $locations[] = $row['location'];
    }
} catch (PDOException $e) {
    die("Error fetching locations: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/fviss_process.css">
</head>

<body>
    <div class="form-container">
        <form method="POST" id="mainForm" name="mainForm">
            <h1>
                <center>FVI Solderside BATCHLOT</center>
            </h1>
            <div class="form-section">
                <input type="text" class="form-input" name="qr_code" id="qr_code" readonly>
                <div class="form-group">
                    <label class="form-label">Serial Code:</label>
                    <input type="text" class="form-input" name="serial_code_main" id="serial_code_main" autofocus autocomplete="off" minlength="3" required>
                </div>
                <div class="form-group">
                    <label class="form-label">QTY INPUT:</label>
                    <input type="text" class="form-input" name="qty_input" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">OPERATOR:</label>
                    <input type="text" class="form-input" name="operator_name" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">SHIFT:</label>
                    <input type="text" class="form-input" name="shift" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">ASM LINE:</label>
                    <input type="text" class="form-input" name="asmline" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">LINE:</label>
                    <input type="text" class="form-input" name="line" readonly>
                </div>
                <div id="liveBoardCount" style="text-align:center; margin-top:15px; font-size:32px; font-weight:bold; color:#000000;" hidden>
                    BOARD COUNT: 0 / 10
                </div>
            </div>
            <div class="form-section" hidden>
                <div class="form-group">
                    <label class="form-label">ASSY NO:</label>
                    <input type="text" class="form-input" name="assy_code" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">MODEL NAME:</label>
                    <input type="text" class="form-input" name="model_name" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">LOT NO:</label>
                    <input type="text" class="form-input" name="kepi_lot" readonly>
                </div>
                <div class="form-group">
                    <input type="hidden" class="form-input" name="serial_qty" readonly>
                </div>
            </div>

            <input type="hidden" name="qr_count" value="0">
            <!-- 
            <div style="text-align: center; margin-top: 20px;">
                <button type="submit">SUBMIT</button>
            </div> -->
        </form>
        <div style="text-align: center; margin-top: 20px;" id="noGoodBtn">
            <button>NO GOOD</button>
        </div>
    </div>
    <!-- <div style="text-align: center; margin-top: 25px;">
        <button id="noGoodBtn" class="nogood">NO GOOD</button>
    </div> -->
    <div id="nogoodModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <center>
                <h2>No Good</h2>
            </center>
            <form id="nogoodForm" method="POST">
                <div class="column">
                    <input type="hidden" id="modal_source" name="source" value="">
                    <div class="form-group" hidden>
                        <label for="qr_code" class="form-label" hidden>QR Code:</label>
                        <input type="hidden" class="form-input" name="qr_code" id="modal_qr_code" required autocomplete="off" readonly hidden>
                    </div>
                    <div class="form-group">
                        <label for="operator_name" class="form-label">Operator Name:</label>
                        <input type="text" class="form-input" id="operator_name" name="operator_name" readonly>
                    </div>
                    <div class="form-group">
                        <label for="serial" class="form-label">Serial:</label>
                        <input type="text" id="serial_code" name="serial_code" class="form-input" required autocomplete="off" minlength="3">
                    </div>
                    <div class="form-group">
                        <span id="serial_error" class="error-span" style="color: red; font-size: 22px; display: none;"></span>
                    </div>
                    <div id="defect-location-container">
                        <div class="form-group dual-inputs defect-location-row">
                            <div class="half-group">
                                <label class="form-label">Defect:</label>
                                <select class="form-input defect-select" name="defect[]" required>
                                    <option value="" disabled selected>Select defect</option>
                                    <?php foreach ($defects as $defect): ?>
                                        <option value="<?= htmlspecialchars($defect) ?>"><?= htmlspecialchars($defect) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="half-group">
                                <label class="form-label">Location:</label>
                                <select class="form-input location-select" name="location[0][]" multiple="multiple" required>
                                    <?php foreach ($locations as $location): ?>
                                        <option value="<?= htmlspecialchars($location) ?>"><?= htmlspecialchars($location) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <center><button type="button" class="add-defect" id="addDefectBtn">Add Defect</button></center>
                    <br>
                    <div class="form-group">
                        <label for="board_number" class="form-label">Board Number:</label>
                        <input type="text" class="form-input" name="board_number" id="board_number" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="scrap_fviss" class="form-label">Scrap:</label>
                        <select class="form-input" name="scrap_fviss" required>
                            <option value=""></option>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="repairable" class="form-label">Repairable:</label>
                        <select class="form-input" name="repairable" required>
                            <option value=""></option>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="action_fviss" class="form-label">Action:</label>
                        <input type="text" class="form-input" name="action_fviss" id="action_fviss" required autocomplete="off">
                    </div>
                </div>
                <div style="margin-top: 20px;">
                    <center><button type="submit">Save</button></center>
                </div>
            </form>

        </div>
    </div>
    <script>
        //for operator name fixed
        const loggedInUser = "<?= $_SESSION['user_namefl'] ?? '' ?>";
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const defectOptions = `<?php foreach ($defects as $defect): ?><option value="<?= htmlspecialchars($defect) ?>"><?= htmlspecialchars($defect) ?></option><?php endforeach; ?>`;
        const locationOptions = `<?php foreach ($locations as $location): ?><option value="<?= htmlspecialchars($location) ?>"><?= htmlspecialchars($location) ?></option><?php endforeach; ?>`;

        let isSubmitting = false;
        let qrCounts = {};
        let isSerialValid = false;
        let serialValidationToken = 0;
        let serialDebounceTimer = null;
        let lastKepiLot = "";

        function updateCountDisplay(boardCount) {
            $('#liveBoardCount').text(`BOARD COUNT: ${boardCount} / 10`);
        }

        function checkAndAutoSubmit() {
            const serial = $('#serial_code_main').val();
            const qrCode = $('#qr_code').val();
            const assy = $('input[name="assy_code"]').val();
            const model = $('input[name="model_name"]').val();
            const lot = $('input[name="kepi_lot"]').val();
            const asmline = $('input[name="asmline"]').val();
            const line = $('input[name="line"]').val();
            const shift = $('input[name="shift"]').val();
            const operator = $('input[name="operator_name"]').val();
            const qty = $('input[name="qty_input"]').val();

            console.log({
                serial,
                qrCode,
                assy,
                model,
                lot,
                asmline,
                line,
                shift,
                operator,
                qty
            });

            if (
                serial.length === 13 &&
                assy && model && lot &&
                line && shift && operator && qty &&
                !isSubmitting
            ) {
                $('#mainForm').submit();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please fill in all required fields.'
                });
            }
        }

        $(document).ready(function() {
            $('.location-select').select2({
                tags: true,
                placeholder: "Select or type locations",
                tokenSeparators: [','],
                width: '100%',
                language: {
                    noResults: () => $('<span>No record found</span>')
                },
                escapeMarkup: markup => markup
            });

            $('.defect-select').select2({
                placeholder: "Select defect",
                width: '100%',
                language: {
                    noResults: () => $('<span>No record found</span>')
                },
                escapeMarkup: markup => markup
            });

            $('input[type="text"]').on('input', function() {
                this.value = this.value.toUpperCase();
            });

            $('#serial_code').on('focus', function() {
                $(this).select();
            });

            $('#serial_code_main').on('input', function() {
                let serial_code = $(this).val().trim();
                clearTimeout(serialDebounceTimer);

                if (serial_code.length > 12) {
                    serialDebounceTimer = setTimeout(() => {
                        $.ajax({
                            url: 'fetch_fvissqr.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                code: serial_code,
                                source: 'batchlot'
                            },
                            success: function(response) {

                                if (response.success === false) {
                                    Swal.fire({
                                        title: 'Serial Code is on HOLD!',
                                        text: "This Serial Code is currently on HOLD and cannot be processed.",
                                        icon: 'warning',
                                        confirmButtonText: 'OK'
                                    });
                                } else if (response.errorCode === 'BATCHLOT') {
                                    Swal.fire({
                                        title: 'QR Code is in Batchlot!',
                                        text: "This QR Code is currently in the Batchlot and cannot be processed.",
                                        icon: 'warning',
                                        confirmButtonText: 'OK'
                                    });
                                }

                                if (response.success) {
                                    $('input[name="assy_code"]').val(response.assy_code);
                                    $('input[name="qr_code"]').val(response.qr_code);
                                    $('input[name="model_name"]').val(response.model_name);
                                    $('input[name="kepi_lot"]').val(response.kepi_lot);
                                    $('input[name="asmline"]').val(response.asmline);
                                    $('input[name="line"]').val(response.line);
                                    $('input[name="shift"]').val(response.shift);
                                    $('input[name="operator_name"]').val(response.operator_name);
                                    $('input[name="qty_input"]').val(response.qty_input);
                                    $('input[name="final_qtyinput"]').val(parseInt(response.final_qtyinput) || 0);

                                    let kepi_lot = response.kepi_lot;

                                    $.ajax({
                                        url: 'get_boardcountFVISS.php',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {
                                            kepi_lot: kepi_lot,
                                            line: response.line
                                        },
                                        success: function(countData) {

                                            let boardCount = Number(countData.count);

                                            if (!boardCount || boardCount < 1) {
                                                boardCount = 1;
                                            } else {
                                                boardCount = boardCount + 1;
                                            }

                                            if (boardCount > 10) {
                                                boardCount = 10;
                                            }

                                            $('input[name="qr_count"]').val(boardCount);

                                            $('#liveBoardCount').text(`BOARD COUNT: ${boardCount} / 10`);

                                            checkAndAutoSubmit();
                                        }
                                    });
                                    $('#serial_code').val('').focus().select();
                                } else {

                                    $('input[name="assy_code"]').val('');
                                    $('input[name="model_name"]').val('');
                                    $('input[name="kepi_lot"]').val('');
                                    $('input[name="asmline"]').val('');
                                    $('input[name="line"]').val('');
                                    $('input[name="shift"]').val('');
                                    $('input[name="operator_name"]').val('');
                                    $('input[name="qty_input"]').val('');
                                    $('input[name="final_qtyinput"]').val('');
                                    $('input[name="qr_count"]').val('0');

                                    $('#liveBoardCount').text("BOARD COUNT: 0 / 10");

                                    Swal.fire({
                                        icon: 'warning',
                                        title: response.title,
                                        text: response.message || 'This Serial Code does not exist in the system.',
                                        confirmButtonText: 'OK',
                                        allowOutsideClick: false
                                    }).then(() => {
                                        $('#serial_code').val('').focus().select();
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Server Error',
                                    text: 'An error occurred while fetching QR data. Please try again.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }, 300);
                }
            });


            $('#mainForm').submit(function(e) {
                e.preventDefault();
                if (isSubmitting) return;
                isSubmitting = true;

                const formData = new FormData(this);
                formData.append('source', 'batchlot');

                $.ajax({
                    url: 'fvissbatchlot_processform.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        isSubmitting = false;
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Confirm Result',
                                text: "Is everything good?",
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'GOOD',
                                cancelButtonText: 'NO GOOD',
                                reverseButtons: true,
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Saved successfully',
                                        text: 'FVISS saved successfully.',
                                        toast: true,
                                        position: 'top-right',
                                        timer: 1500,
                                        showConfirmButton: false,
                                        didOpen: () => {
                                            $('#serial_code').focus().select();
                                        }
                                    })
                                } else {
                                    $('#modal_qr_code').val($('input[name="qr_code"]').val());
                                    $('#modal_operator_name').val('<?php echo $_SESSION['user_namefl']; ?>');
                                    $('#modal_source').val('alert');
                                    $('#nogoodModal').show();
                                    $('#serial_code').focus().select();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        isSubmitting = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'AJAX Error',
                            html: `<strong>Status:</strong> ${status}<br><strong>Error:</strong> ${error}<br><strong>Response:</strong><br>${xhr.responseText}`
                        });
                    }
                });
            });

            function resetMainForm() {
                $('form')[0].reset();
                $('#serial_code').val('');
                $('input[name="assy_code"]').val('');
                $('input[name="model_name"]').val('');
                $('input[name="kepi_lot"]').val('');
                $('input[name="asmline"]').val('');
                $('input[name="line"]').val('');
                $('input[name="shift"]').val('');
                $('input[name="operator_name"]').val('');
                $('input[name="qty_input"]').val('');
                $('input[name="final_qtyinput"]').val('');
                $('input[name="qr_count"]').val('0');

                $('#liveBoardCount').text("BOARD COUNT: 0 / 10");

                $('#serial_code').focus();
            }

            $('#serial_code_main').focus().select();

            $('#nogoodForm').submit(function(e) {
                e.preventDefault();

                const defects = $('select[name="defect[]"]').map(function() {
                    return $(this).val();
                }).get();

                const validDefects = [];
                const validLocations = [];
                let isValid = true;

                defects.forEach((defect, index) => {
                    const location = $(`select[name="location[${index}][]"]`).val() || [];

                    if (defect === '' && location.length === 0) return;

                    if (defect !== '' && location.length > 0) {
                        validDefects.push(defect.toUpperCase());
                        validLocations.push(location);
                    } else {
                        isValid = false;
                    }
                });

                if (!isValid || validDefects.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Defect Entry',
                        text: 'Each defect must have both a name and location. Empty rows are okay, but partially filled ones are not.',
                        toast: true,
                        position: 'top-right',
                        timer: 4000,
                        showConfirmButton: false
                    });
                    return;
                }

                const formData = new FormData();
                formData.append('qr_code', $('#modal_qr_code').val());
                formData.append('operator_name', $('#operator_name').val());
                formData.append('serial_code', $('#serial_code').val());
                formData.append('board_number', $('#board_number').val());
                formData.append('scrap_fviss', $('#scrap_fviss').val());
                formData.append('repairable', $('#repairable').val());
                formData.append('action_fviss', $('#action_fviss').val());
                formData.append('source', $('#modal_source').val());
                formData.append('origin', 'batchlot');

                validDefects.forEach((defect, i) => {
                    formData.append('defect[]', defect);
                    validLocations[i].forEach(loc => {
                        formData.append(`location[${i}][]`, loc);
                    });
                });

                $.ajax({
                    url: 'fviss_serialnogood.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved!',
                                text: response.message,
                                toast: true,
                                position: 'top-right',
                                timer: 3000,
                                showConfirmButton: false
                            });
                            closeNoGoodModal();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                toast: true,
                                position: 'top-right',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'AJAX Error',
                            html: `<strong>Status:</strong> ${status}<br><strong>Error:</strong> ${error}<br><strong>Response:</strong><br>${xhr.responseText}`,
                            toast: true,
                            position: 'top-right',
                            timer: 6000,
                            showConfirmButton: true
                        });
                    }
                });
            });

            $('#serial_code').on('input', function() {
                const serialcode = $(this).val().trim();
                const qrcode = $('#modal_qr_code').val().trim();
                const source = $('#modal_source').val();
                console.log(serialcode);

                $('#serial_error').text('').hide();
                $('#serial_code').css('border', '');

                clearTimeout(serialDebounceTimer);

                if (serialcode.length > 3) {
                    const currenttoken = ++serialValidationToken;

                    serialDebounceTimer = setTimeout(() => {
                        $.ajax({
                            url: 'batchlot_serialvalidate.php',
                            type: 'post',
                            data: {
                                serial_code: serialcode,
                                qr_code: qrcode,
                                source: source,
                                origin: 'batchlot'
                            },
                            datatype: 'json',
                            success: function(response) {
                                if (currenttoken !== serialValidationToken) return;

                                if (!response.valid) {
                                    $('#serial_code').css('border', '2px solid red');
                                    $('#serial_error').text(response.message).show();
                                    isSerialValid = false;
                                } else {
                                    $('#serial_code').css('border', '2px solid green');
                                    $('#serial_error').text('').hide();
                                    isSerialValid = true;
                                }
                            },
                            error: function() {
                                isSerialValid = false;
                                $('#serial_error').text('validation error').show();
                                $('#serial_code').css('border', '2px solid red');
                            }
                        });
                    }, 300);
                } else {
                    isSerialValid = false;
                }
            });

            $('#noGoodBtn').on('click', function() {
                $('#operator_name').val(loggedInUser);
                $('#modal_source').val('manual');
                $('#nogoodModal').show();
                $('#serial_code').focus();
            });

            $('#closeModal').on('click', function() {
                closeNoGoodModal();
            });

            // $(window).on('click', function(event) {
            //     if (event.target.id === 'nogoodModal') {
            //         closeNoGoodModal();
            //     }
            // });

            function closeNoGoodModal() {
                $('#nogoodModal').hide();
                $('#nogoodForm')[0].reset();
                $('#modal_source').val('');
                $('.location-select').val(null).trigger('change');
                $('#defect-location-container .defect-location-row:not(:first)').remove();
                $('#defect-location-container .defect-location-row:first select').val('').trigger('change');
                defectIndex = 1;
                resetMainForm();
            }

            let defectIndex = 1;

            $('#addDefectBtn').on('click', function() {
                const newRow = `
                <div class="form-group dual-inputs defect-location-row">
                    <div class="half-group">
                        <label class="form-label">Defect:</label>
                        <select class="form-input defect-select" name="defect[]">
                            <option value="" disabled selected>Select defect</option>
                            ${defectOptions}
                        </select>
                    </div>
                    <div class="half-group">
                        <label class="form-label">Location:</label>
                        <select class="form-input location-select" name="location[${defectIndex}][]" multiple="multiple">
                            ${locationOptions}
                        </select>
                    </div>
                </div>`;

                $('#defect-location-container').append(newRow);

                $(`select[name="location[${defectIndex}][]"]`).select2({
                    tags: true,
                    placeholder: "Select or type locations",
                    tokenSeparators: [','],
                    width: '100%'
                });

                $('#defect-location-container').find('.defect-select').last().select2({
                    placeholder: "Select defect",
                    width: '100%',
                    language: {
                        noResults: () => $('<span>No record found</span>')
                    },
                    escapeMarkup: markup => markup
                });

                defectIndex++;
            });
        });
    </script>

</body>

</html>