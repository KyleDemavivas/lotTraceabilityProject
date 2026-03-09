<?php include 'sidebar.php'; ?>
<?php
if (!isset($_SESSION['user_namefl'])) {
    header('Location: login.php');
    exit;
}
include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';

$defects = [];
try {
    $stmt = $conn->query('SELECT defect FROM defect_master ORDER BY defect ASC');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $defects[] = $row['defect'];
    }
} catch (PDOException $e) {
    exit('Error fetching defects: '.$e->getMessage());
}

$locations = [];
try {
    $stmt = $conn->query('SELECT location FROM location_master ORDER BY location ASC');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $locations[] = $row['location'];
    }
} catch (PDOException $e) {
    exit('Error fetching locations: '.$e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/mod2.css">
</head>

<body>
    <div class="form-container">
        <form method="POST" id="mainForm" name="mainForm">
            <h1>
                <center>Modificator 2</center>
            </h1>
            <div class="form-section">
                <div class="form-group">
                    <label class="form-label">QR Code:</label>
                    <input type="text" class="form-input" name="qr_code" id="qr_code" autofocus autocomplete="off" minlength="21" maxlength="21" required>
                </div>
                <input type="text" class="form-input" name="final_qtyinput" readonly hidden>
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
                <div id="liveBoardCount" style="text-align:center; margin-top:15px; font-size:32px; font-weight:bold; color:#000000;">
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
    <!--<div style="text-align: center; margin-top: 25px;">
        <button id="noGoodBtn" class="nogood">NO GOOD</button>
    </div>-->
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
                        <input type="text" class="form-input" name="qr_code" id="modal_qr_code" required autocomplete="off" readonly hidden>
                    </div>
                    <div class="form-group">
                        <label for="operator_name" class="form-label">Operator Name:</label>
                        <input type="text" class="form-input" id="modal_operator_name" name="operator_name" readonly>
                    </div>
                    <div class="form-group">
                        <label for="serial" class="form-label">Serial:</label>
                        <input type="text" id="modal_serial_code" name="serial_code" class="form-input" required autocomplete="off" minlength="13" maxlength="13">
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
                                    <?php foreach ($defects as $defect) { ?>
                                        <option value="<?php echo htmlspecialchars($defect); ?>"><?php echo htmlspecialchars($defect); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="half-group">
                                <label class="form-label">Location:</label>
                                <select class="form-input location-select" name="location[0][]" multiple="multiple" required>
                                    <?php foreach ($locations as $location) { ?>
                                        <option value="<?php echo htmlspecialchars($location); ?>"><?php echo htmlspecialchars($location); ?></option>
                                    <?php } ?>
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
                        <label for="scrap_mod2" class="form-label">Scrap:</label>
                        <select class="form-input" id="scrap_mod2" name="scrap_mod2" required>
                            <option value="" disabled selected hidden>Required</option>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="repairable" class="form-label">Repairable:</label>
                        <select class="form-input" id="repairable" name="repairable" required>
                            <option value="" disabled selected hidden>Required</option>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>
                            <option value="REPAIR">FOR REPAIRER</option>
                        </select>
                    </div>
                    <div class="form-group" id="tenboardGroup">
                        <label for="tenboard" class="form-label">1ST 10 BOARD:</label>
                        <select class="form-input" id="tenboard" name="tenboard" required>
                            <option value="" disabled selected hidden>Required</option>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>
                        </select>
                    </div>
                    <div class="form-group" id="actionGroup">
                        <label for="action_mod1" class="form-label">Action:</label>
                        <select class="form-input" name="action_mod2" id="action_mod2" required>
                            <option value="" disabled selected hidden>Select Action</option>
                            <option value="">N/A</option>
                            <option value="FOR TOUCHUP">FOR TOUCH-UP</option>
                            <option value="FOR REPLACEMENT">FOR REPLACEMENT</option>
                            <option value="FOR PUSH">FOR PUSH</option>
                            <option value="FOR ALIGN">FOR ALIGN</option>
                            <option value="FOR REMOVAL">FOR REMOVAL</option>
                        </select>
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
        const loggedInUser = "<?php echo $_SESSION['user_namefl'] ?? ''; ?>";
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const defectOptions = `<?php foreach ($defects as $defect) { ?><option value="<?php echo htmlspecialchars($defect); ?>"><?php echo htmlspecialchars($defect); ?></option><?php } ?>`;
        const locationOptions = `<?php foreach ($locations as $location) { ?><option value="<?php echo htmlspecialchars($location); ?>"><?php echo htmlspecialchars($location); ?></option><?php } ?>`;

        let isSubmitting = false;
        let qrCounts = {};
        let isSerialValid = false;
        let serialValidationToken = 0;
        let serialDebounceTimer = null;
        let qrDebounceTimer = null;
        let lastKepiLot = "";

        function updateCountDisplay(boardCount) {
            $('#liveBoardCount').text(`BOARD COUNT: ${boardCount} / 10`);
        }

        function hideCounter() {
            $('#liveBoardCount').hide();
            $('#actionGroup').hide();
            $("#tenboardGroup").hide();
        }

        // Shows them when new KEPI LOT is scanned
        function showCounter() {
            $('#liveBoardCount').show();
        }

        function checkAndAutoSubmit() {
            const qr = $('#qr_code').val();
            const assy = $('input[name="assy_code"]').val().trim();
            const model = $('input[name="model_name"]').val().trim();
            const lot = $('input[name="kepi_lot"]').val().trim();
            const asmline = $('input[name="asmline"]').val().trim();
            const line = $('input[name="line"]').val().trim();
            const shift = $('input[name="shift"]').val().trim();
            const operator = $('input[name="operator_name"]').val().trim();
            const qty = $('input[name="qty_input"]').val().trim();
            if (
                qr.length === 21 &&
                assy && model && lot &&
                line && shift && operator && qty &&
                !isSubmitting
            ) {
                $('#mainForm').submit();
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

            $('#qr_code').on('focus', function() {
                $(this).select();
            });

            $('#qr_code').on('input', function() {
                let qr_code = $(this).val().trim();
                clearTimeout(qrDebounceTimer);
                if (qr_code.length > 20) {
                    qrDebounceTimer = setTimeout(() => {
                        $.ajax({
                            url: 'fetch_qrmod2.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                qr_code: qr_code
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('input[name="assy_code"]').val(response.assy_code);
                                    $('input[name="model_name"]').val(response.model_name);
                                    $('input[name="kepi_lot"]').val(response.kepi_lot);

                                    // If new KEPI LOT, reset UI
                                    if (window.lastKepiLot !== response.kepi_lot) {
                                        window.lastKepiLot = response.kepi_lot;
                                        showCounter();
                                    }
                                    console.log(response.serial_qty);
                                    $('input[name="asmline"]').val(response.asmline);
                                    $('input[name="line"]').val(response.line);
                                    $('input[name="shift"]').val(response.shift);
                                    $('input[name="operator_name"]').val(response.operator_name);
                                    $('input[name="qty_input"]').val(response.qty_input ?? console.log('final_qtyinput is not a number'));
                                    $('input[name="final_qtyinput"]').val(parseInt(response.final_qtyinput) ?? console.log('final_qtyinput is not a number'));

                                    let kepi_lot = response.kepi_lot;

                                    $.ajax({
                                        url: 'get_boardcountmod2.php',
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

                                            if (boardCount >= 10) {
                                                hideCounter();
                                            } else {
                                                showCounter();
                                            }

                                            checkAndAutoSubmit();
                                        }
                                    });

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
                                        text: response.message || 'This QR Code is not Found in the System.',
                                        confirmButtonText: 'OK',
                                        didOpen: () => {
                                            $('#qr_code').focus().select();
                                        }
                                    });
                                }
                            },
                            error: function() {
                                console.log('Error status:', status); // Log the status
                                console.log('Error message:', error); // Log the error message
                                console.log('Response text:', xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Server Error',
                                    text: 'An error occurred while fetching QR data. Please try again.',
                                    confirmButtonText: 'OK',
                                    didOpen: () => {
                                        $('#qr_code').focus().select();
                                    }
                                });
                            }
                        });
                    }, 500);
                }
            });


            $('#mainForm').submit(function(e) {
                e.preventDefault();
                if (isSubmitting) return;
                isSubmitting = true;
                const formData = new FormData(this);

                $.ajax({
                    url: 'mod2_processform.php',
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
                                        icon: response.status === 'success' ? 'success' : 'warning',
                                        title: 'Saved!',
                                        text: response.message,
                                        toast: true,
                                        position: 'top-right',
                                        timer: 3000,
                                        showConfirmButton: false,
                                        didOpen: () => {
                                            $('#qr_code').focus().select();
                                        }
                                    });
                                } else {
                                    $('#modal_qr_code').val($('input[name="qr_code"]').val());
                                    $('#modal_operator_name').val('<?php echo $_SESSION['user_namefl']; ?>');
                                    $('#modal_source').val('alert');
                                    $('#nogoodModal').show();
                                    $('#serial_code').focus();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                didOpen: () => {
                                    $('#qr_code').focus().select();
                                }
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
                $('#qr_code').val('');
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

                $('#qr_code').focus();
            }

            $('#qr_code').focus();

            $('#nogoodForm').submit(function(e) {
                e.preventDefault();

                if($('select[name="repairable"]').val() === "REPAIR") { 
                    closeNoGoodModal()
                    Swal.fire({
                        icon: 'success',
                     title: 'Saved!',
                     text: 'Item passed to next process',
                     toast: true,
                     position: 'top-right',
                     timer: 3000,
                     showConfirmButton: false,
                     didOpen: () => {
                             $('#qr_code').focus().select();

                     }
                 });
                    return;
                }

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
                formData.append('operator_name', $('#modal_operator_name').val());
                formData.append('serial_code', $('#modal_serial_code').val());
                formData.append('board_number', $('#board_number').val());
                formData.append('scrap_mod2', $('#scrap_mod2').val());
                formData.append('repairable', $('#repairable').val());
                formData.append('tenboard', $('#tenboard').val());
                formData.append('action_mod2', $('#action_mod2').val());
                formData.append('source', $('#modal_source').val());

                validDefects.forEach((defect, i) => {
                    formData.append('defect[]', defect);
                    validLocations[i].forEach(loc => {
                        formData.append(`location[${i}][]`, loc);
                    });
                });

                $.ajax({
                    url: 'mod2_serialnogood.php',
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

            $('#modal_serial_code').on('input', function() {
                const serial = $(this).val().trim();
                const source = $('#modal_source').val();

                $('#serial_error').hide();
                $('#modal_qr_code').val('');


                clearTimeout(serialDebounceTimer);

                if (serial.length > 3) {
                    serialDebounceTimer = setTimeout(() => {
                        $.ajax({
                            url: 'mod2_validateserialmatch.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                serial_code: serial,
                                source: source
                            },
                            success: function(response) {
                                if (response.valid) {
                                    $('#modal_qr_code').val(response.qr_code);
                                    $('#modal_serial_code').css('border', '2px solid green');
                                } else {
                                    $('#modal_serial_code').css('border', '2px solid red');
                                    $('#serial_error').text(response.message).show();
                                    $("#modal_serial_code").focus().select();
                                }
                            },
                            error: function() {
                                $('#serial_error').text('Validation server error').show();
                            }
                        });
                    }, 500);
                }
            });

            $('#noGoodBtn').on('click', function() {
                $('#modal_operator_name').val(loggedInUser);
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