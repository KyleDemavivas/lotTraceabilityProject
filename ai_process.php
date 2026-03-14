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
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/ai_process.css">
</head>

<body>
    <div class="form-container">
        <form method="POST" id="mainForm" name="mainForm">
            <h1>
                <center>Automatic Insertion</center>
            </h1>
            <div class="form-section">
                <div class="form-group">
                    <label class="form-label">VERIFY ASSY NO:</label>
                    <input type="text" class="form-input" name="verify_assy_code">
                </div>
                <div class="form-group">
                    <label class="form-label">QR Code:</label>
                    <input type="text" class="form-input" name="qr_code" id="qr_code" autofocus autocomplete="off" minlength="21" maxlength="21" required>
                </div>
                <input type="hidden" class="form-input" name="qty_input" readonly>
                <div class="form-group">
                    <label class="form-label">QTY INPUT:</label>
                    <input type="text" class="form-input" name="final_qtyinput" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">OPERATOR:</label>
                    <input type="text" class="form-input" name="operator_name" value="<?php echo htmlspecialchars($_SESSION['user_namefl']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">SHIFT:</label>
                    <select class="form-input" name="shift" required>
                        <option value="">Select Shift</option>
                        <option value="Dayshift">Day</option>
                        <option value="Night Shift">Night</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">LINE:</label>
                    <select class="form-input" name="line" required>
                        <option value="">Select Line</option>
                        <option value="AV1">AV1</option>
                        <option value="AV2">AV2</option>
                        <option value="RG31">RG31</option>
                        <option value="RG2">RG2</option>
                    </select>
                </div>
                <div class="form-group" id="angleField">
                    <label class="form-label">ANGLE:</label>
                    <input type="text" class="form-input" name="angle" id="angleInput">
                </div>
                <div class="form-group" id="multiselect">
                    <label class="form-label">Location:</label>
                    <select class="form-input location-select" id="locationSelect" name="location[0][]" multiple="multiple" required>
                        <?php foreach ($locations as $location) { ?>
                            <option value="<?php echo htmlspecialchars($location); ?>"><?php echo htmlspecialchars($location); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div id="liveBoardCount" style="text-align:center; margin-top:15px; font-size:32px; font-weight:bold; color:#000000;">
                    SHEET COUNT: 0 / 5
                </div>
            </div>
            <div class="form-section">
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
            <!--<button id="modaltest">TEST</button>-->
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
                        <input type="hidden" class="form-input" name="qr_code" id="modal_qr_code" required autocomplete="off" readonly>
                    </div>
                    <div class="form-group">
                        <label for="operator_name" class="form-label">Operator Name:</label>
                        <input type="text" class="form-input" id="modal_operator_name" name="operator_name" readonly>
                    </div>
                    <div class="form-group">
                        <label for="serial" class="form-label">Serial:</label>
                        <input type="text" id="serial_code" name="serial_code" class="form-input" required autocomplete="off" minlength="13" maxlength="13">
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
                                <select class="form-input location-select" name="location2[0][]" multiple="multiple" required>
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
                </div>

                <div style="margin-top: 20px; flex-direction: row; justify-content: center; display: flex;">
                    <button type="submit" style=" position: absolute; left: 50%; transform: translateX(-50%);">Save</button>
                    <button type="button" class="button-close" id="scrapButton" name="scrapButton" style = "margin-right: auto;">Scrap</button>
                </div>
            </form>

        </div>
    </div>
    <script>
        const loggedInUser = "<?php echo $_SESSION['user_namefl'] ?? ''; ?>";
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/boardFetchNoGood.js"></script>
    <script>
        const defectOptions = `<?php foreach ($defects as $defect) { ?><option value="<?php echo htmlspecialchars($defect); ?>"><?php echo htmlspecialchars($defect); ?></option><?php } ?>`;
        const locationOptions = `<?php foreach ($locations as $location) { ?><option value="<?php echo htmlspecialchars($location); ?>"><?php echo htmlspecialchars($location); ?></option><?php } ?>`;

        let isSubmitting = false;
        let qrCounts = {};
        let isSerialValid = false;
        let serialValidationToken = 0;
        let serialDebounceTimer = null;
        let qrDebounceTimer = null;
        let currentBoardCount = 0;

        function updateUIByBoardCount(boardCount) {
            $('#liveBoardCount').text(`SHEET COUNT: ${boardCount} / 5`);
            if (boardCount >= 5) {
                $('#angleField').hide();
                $('#angleInput').prop('disabled', true);
                $('#locationSelect').prop('disabled', true).hide().trigger('change');
                $('#multiselect').hide();
                
                $('#liveBoardCount').hide();
            } else {
                $('#angleField').show();
                $('#angleInput').prop('disabled', false);
                $('#locationSelect').prop('disabled', false);
                $('#liveBoardCount').show();
            }
        }

        function checkAndAutoSubmit() {
            const qr = $('#qr_code').val().trim();
            const assy = $('input[name="assy_code"]').val().trim();
            const model = $('input[name="model_name"]').val().trim();
            const lot = $('input[name="kepi_lot"]').val().trim();
            const line = $('select[name="line"]').val();
            const shift = $('select[name="shift"]').val();
            const operator = $('input[name="operator_name"]').val();
            const qty = $('input[name="qty_input"]').val();
            const verifyAssy = $('input[name="verify_assy_code"]').val().trim();
            const angle = $('input[name="angle"]').val();
            const location = $('select[name="location[0][]"]').val();

            if (
                qr.length === 21 &&
                assy && model && lot &&
                line && shift && operator && qty && angle && location &&
                !isSubmitting
            ) {
                if (verifyAssy === "" || verifyAssy !== assy) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Mismatch',
                        text: 'Verify Assy No. must match Assy No.'
                    });
                    return;
                }
                $('#mainForm').submit();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Fields',
                    text: 'Please fill in all required fields.'
                });
            }
        }


        $(document).ready(function() {
            const loggedInUser = "<?php echo $_SESSION['user_namefl'] ?? ''; ?>";

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
                const qr_code = $(this).val().trim();
                clearTimeout(qrDebounceTimer);
                if (qr_code.length >= 20) {
                    qrDebounceTimer = setTimeout(() => {
                        $.ajax({
                            url: 'fetch_qrvi.php',
                            type: 'POST',
                            data: {
                                qr_code
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    $('input[name="assy_code"]').val(response.assy_code);
                                    $('input[name="model_name"]').val(response.model_name);
                                    $('input[name="kepi_lot"]').val(response.kepi_lot);
                                    $('input[name="qty_input"]').val(response.qty_input);
                                    $('input[name="line"]').val(response.line);
                                    $('input[name="final_qtyinput"]').val('LOADING...');
                                    checkAndAutoSubmit();
                                } else {
                                    $('input[name="assy_code"], input[name="model_name"], input[name="kepi_lot"], input[name="final_qtyinput"]').val('');
                                    $('input[name="qty_input"]').val(response.qty_input);
                                    Swal.fire({
                                        icon: 'warning',
                                        title: response.data,
                                        text: response.message,
                                        confirmButtonText: 'OK',
                                        didOpen: () => {
                                            $('#qr_code').focus().select();
                                        }
                                    });
                                }
                            }
                        });
                    }, 500);
                }
            });

            //$('select[name="shift"], select[name="line"]').on('change', checkAndAutoSubmit);

            $('#mainForm').submit(function(e) {
                e.preventDefault();
                if (isSubmitting) return;

                const verifyAssy = $('input[name="verify_assy_code"]').val().trim();
                const qrAssy = $('input[name="assy_code"]').val().trim();

                if (!verifyAssy || !qrAssy) return;

                if (verifyAssy !== qrAssy) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Mismatch',
                        text: 'Verify Assy No. does not match Assy No.'
                    });
                    $('#qr_code').focus().select();
                    return;
                }

                isSubmitting = true;
                const formData = new FormData(this);

                $.ajax({
                    url: 'aiprocess_form.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        isSubmitting = false;

                        let boardCount = response.lastBoardCount != null ? Number(response.lastBoardCount) : 0;
                        if (boardCount > 5) boardCount = 5;
                        currentBoardCount = boardCount;
                        updateUIByBoardCount(boardCount);

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
                                    resetMainForm();
                                    $('input[name="final_qtyinput"]').val(response.final_qtyinput);

                                    Swal.fire({
                                        icon: response.status === 'success' ? 'success' : 'warning',
                                        title: 'Saved!',
                                        text: response.message,
                                        toast: true,
                                        position: 'top-right',
                                        timer: 3000,
                                        showConfirmButton: false
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
                                toast: true,
                                position: 'top-right',
                                timer: 3000,
                                showConfirmButton: false,
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
                $('#qr_code').focus().select();
            }

            $('#nogoodForm').submit(function(e) {
                e.preventDefault();

                if (!$('#nogoodModal').is(':visible')) return;

                const validDefects = [];
                const validLocations = [];
                let isValid = true;

                $('#nogoodForm .defect-location-row').each(function(index) {
                    const defect = $(this).find('select[name="defect[]"]').val();
                    const location = $(this)
                        .find(`select[name="location[${index}][]"]`)
                        .val();

                    const defectVal = defect ? defect.trim() : '';
                    const locationVal = Array.isArray(location) ? location : [];

                    if (!defectVal && locationVal.length === 0) return;

                    if (defectVal && locationVal.length > 0) {
                        validDefects.push(defectVal.toUpperCase());
                        validLocations.push(locationVal);
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
                formData.append('serial_code', $('#serial_code').val());
                formData.append('board_number', $('#board_number').val());
                formData.append('source', $('#modal_source').val());

                validDefects.forEach((defect, i) => {
                    formData.append('defect[]', defect);
                    validLocations[i].forEach(loc => {
                        formData.append(`location[${i}][]`, loc);
                    });
                });

                $.ajax({
                    url: 'ai_serialnogood.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success(response) {
                        Swal.fire({
                            icon: response.status === 'success' ? 'success' : 'error',
                            title: response.status === 'success' ? 'Saved!' : 'Error',
                            text: response.message,
                            toast: true,
                            position: 'top-right',
                            timer: 3000,
                            showConfirmButton: false
                        });

                        if (response.status === 'success') {
                            closeNoGoodModal();
                        }
                    }
                });
            });


            $('#serial_code').on('input', function() {
                const serialcode = $(this).val().trim();
                const qrcode = $('#modal_qr_code').val().trim();
                const source = $('#modal_source').val();

                $('#serial_error').text('').hide();
                $('#serial_code').css('border', '');

                clearTimeout(serialDebounceTimer);

                if (serialcode.length > 3) {
                    const currentToken = ++serialValidationToken;

                    serialDebounceTimer = setTimeout(() => {
                        $.ajax({
                            url: 'ai_validate_serial_match.php',
                            type: 'POST',
                            data: {
                                serial_code: serialcode,
                                qr_code: qrcode,
                                source: source
                            },
                            dataType: 'json',
                            success: function(response) {
                                $('#modal_qr_code').val(response.qr_code);
                                if (currentToken !== serialValidationToken) return;
                                if (!response.valid) {
                                    $('#serial_code').css('border', '2px solid red');
                                    $('#serial_error').text(response.message).show();
                                    $('#serial_code').focus().select();
                                    isSerialValid = false;
                                } else {
                                    $('#serial_code').css('border', '2px solid green');
                                    $('#serial_error').text('').hide();
                                    isSerialValid = true;
                                }
                            },
                            error: function() {
                                isSerialValid = false;
                                $('#serial_error').text('Validation error').show();
                                $('#serial_code').css('border', '2px solid red');
                            }
                        });
                    }, 500);
                } else {
                    isSerialValid = false;
                }
            });

            const form = $('#nogoodForm')[0];

            $('#scrapButton').on('click', function(e) {
                e.preventDefault();
                  
                if (!form.checkValidity()) {
                        form.reportValidity();
                      return;

                  }

                const serial_code = $('#serial_code').val().trim();
                if (serial_code === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Serial Required',
                        text: 'Please enter the serial number before proceeding with scrap.',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    return;
                }
                const qr_code = $('#modal_qr_code').val().trim();
                const location = $('select[name="location2[0][]"]').val();
                const defect = $('select[name="defect[]"]').val();
                const board_number = $('#board_number').val();

                getBoardData(qr_code, 'fetch_qrvi.php', function(response) {
                    if (response.success === true) {

                    const scrapData = buildScrapData(qr_code, serial_code, response, location, defect, "AI", board_number);
                    
                       submitScrap(scrapData, function(scrapResponse) {
                                     if (scrapResponse.success === true) {
                                       showSuccessToast(scrapResponse.message);
                                        closeNoGoodModal();
                             } else {
                                        showErrorToast(scrapResponse.message);
                             }
                        });
                       
                    } else {
                       showErrorToast(response.message);
                    }
                 }, function(error) {
                        showErrorToast(error.message);
                 }, null);
                });
                
            $('#noGoodBtn').on('click', function() {
                $('#modal_operator_name').val(loggedInUser);
                $('#nogoodModal').show();
                $('#modal_source').val('');
                $('#serial_code').focus().select();
            });

            $('#modaltest').on('click', function() {
                $('#nogoodModal').show();
            })

            $('#closeModal').on('click', function() {
                closeNoGoodModal();
            });

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
                    <select class="form-input location-select" name="location2[${defectIndex}][]" multiple="multiple">
                        ${locationOptions}
                    </select>
                </div>
            </div>`;

                $('#defect-location-container').append(newRow);

                $(`select[name="location2[${defectIndex}][]"]`).select2({
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