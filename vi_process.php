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
    <link rel="stylesheet" href="css/vi_process.css">
</head>

<body>
    <div class="form-container">
        <form method="POST" id="mainForm" name="mainForm">
            <h1>
                <center>Visual Inspection</center>
            </h1>
            <div class="form-section">
                <div class="form-group">
                    <label class="form-label">QR Code:</label>
                    <input type="text" class="form-input" name="qr_code" id="qr_code" autofocus autocomplete="off" minlength="21" required>
                </div>
                <div class="form-group" hidden>
                    <input type="text" class="form-input" name="qty_input" readonly hidden>
                </div>
                <div class="form-group">
                    <label class="form-label">QTY INPUT:</label>
                    <input type="text" class="form-input" name="final_qtyinput" readonly>
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
                    <label class="form-label">LINE:</label>
                    <input type="text" class="form-input" name="line" readonly>
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
                        <input type="text" class="form-input" name="model_name" id="modal_model_name" required autocomplete="off" readonly hidden>
                        <input type="text" class="form-input" name="kepi_lot" id="modal_kepi_lot" required autocomplete="off" readonly hidden>
                        <input type="text" class="form-input" name="assy_code" id="modal_assy_code" required autocomplete="off" readonly hidden>
                        <input type="text" class="form-input" name="shift" id="modal_shift" required autocomplete="off" readonly hidden>
                        <input type="text" class="form-input" name="line" id="modal_line" required autocomplete="off" readonly hidden>
                    </div>
                    <div class="form-group">
                        <label for="operator_name" class="form-label">Operator Name:</label>
                        <input type="text" class="form-input" id="operator_name" name="operator_name" readonly>
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
        console.log(loggedInUser);
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
        let qrBuffer = ''

        //is called to check if fields are inputted, used by the form qr check and main submit form
        function checkAndAutoSubmit() {
            const qr = $('#qr_code').val();
            const assy = $('input[name="assy_code"]').val();
            const model = $('input[name="model_name"]').val();
            const lot = $('input[name="kepi_lot"]').val();
            const line = $('input[name="line"]').val();
            const shift = $('input[name="shift"]').val();
            const operator = $('input[name="operator_name"]').val();
            const qty = $('input[name="qty_input"]').val();

            if (
                qr.length === 21 &&
                assy && model && lot &&
                line && shift && operator && qty &&
                !isSubmitting
            ) {
                $('#mainForm').submit();
            }
        }

        //Execute functions when DOM is loaded
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

            //runs when qr code field is inputted
            $('#qr_code').on('input', function() {
                var qr_code = $(this).val();
                //if input reaches more than 3 characters, the timer will count to 300ms before making the ajax call
                clearTimeout(qrDebounceTimer);

                if (qr_code.length > 20) {

                    qrDebounceTimer = setTimeout(() => {
                        $.ajax({
                            url: 'fetch_qrvi2.php',
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
                                    $('input[name="line"]').val(response.line);
                                    $('input[name="shift"]').val(response.shift);
                                    $('input[name="operator_name"]').val(response.operator_name);
                                    $('input[name="qty_input"]').val(response.qty_input);
                                    $('input[name="final_qtyinput"]').val('LOADING...');

                                    let kepi_lot = response.kepi_lot;
                                    qrCounts[kepi_lot] = (qrCounts[kepi_lot] || 0) + 1;
                                    $('input[name="qr_count"]').val(qrCounts[kepi_lot]);

                                    checkAndAutoSubmit();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        didOpen: () => {
                                            $('#qr_code').focus().select();
                                        }
                                    });
                                    $('input[name="assy_code"], input[name="model_name"], input[name="kepi_lot"], input[name="line"], input[name="shift"], input[name="operator_name"], input[name="qty_input"], input[name="final_qtyinput"]').val('');
                                    $('#qr_code').focus().select();
                                }
                            }
                        });
                        //change this for the time the timer counts in ms
                    }, 500);
                }
            });

            //runs when a form is submitted
            $('#mainForm').submit(function(e) {
                e.preventDefault();
                if (isSubmitting) return;
                isSubmitting = true;

                const formData = new FormData(this);

                $.ajax({
                    url: 'viprocess_form.php',
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
                                        showConfirmButton: false
                                    });
                                    $('#qr_code').val('').focus().select();
                                    $('input[name="final_qtyinput"]').val(parseInt(response.final_qtyinput) || 'ERROR');

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
                            html: `<strong>Status:</strong> ${status}<br><strong>Error:</strong> ${error}<br><strong>Response:</strong><br>${xhr.responseText}`,
                            didOpen: () => {
                                $('#qr_code').focus().select();
                            }
                        });
                    }
                });
            });

            //call when you want page to reset via JQuery
            function resetMainForm() {
                $('form')[0].reset();
                $('input[name="assy_code"], input[name="model_name"], input[name="kepi_lot"], input[name="line"], input[name="shift"], input[name="operator_name"], input[name="qty_input"], input[name="final_qtyinput"], input[name="qr_count"]').val('');
                $('#qr_code').focus();
            }

            $('#qr_code').focus();

            //fires when the nogood modal submits
            $('#nogoodForm').submit(function(e) {
                e.preventDefault();

                // Skip validation if modal is not actually used
                if (!$('#nogoodModal').is(':visible')) {
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
                formData.append('operator_name', $('#modal_operator_name').val('<?php echo $_SESSION['user_namefl']; ?>'));
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
                    url: 'vi_serialnogood.php',
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

            //runs when serial code is inputted in field, this is in the NO GOOD MODAl
            $('#serial_code').on('input', function() {
                const serialcode = $(this).val().trim();
                const source = $('#modal_source').val() || 'modal';

                clearTimeout(serialDebounceTimer);

                if (serialcode.length > 3) {
                    serialDebounceTimer = setTimeout(() => {
                        $.ajax({
                            url: 'validate_serial_match.php',
                            type: 'POST',
                            data: {
                                serial_code: serialcode,
                                source: source
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.valid) {
                                    $('#modal_qr_code').val(response.qr_code); // auto-fill QR code
                                    $('#serial_code').css('border', '2px solid green');
                                    $('#serial_error').hide();
                                    isSerialValid = true;
                                } else {
                                    $('#modal_qr_code').val(response.qr_code || '');
                                    $('#serial_code').css('border', '2px solid red');
                                    $('#serial_error').text(response.message).show();
                                    $('#serial_code').focus().select();
                                    isSerialValid = false;
                                }
                            },
                            error: function() {
                                $('#serial_code').css('border', '2px solid red');
                                $('#serial_error').text('Validation error').show();
                                isSerialValid = false;
                            }
                        });
                    }, 500);
                } else {
                    $('#modal_qr_code').val('');
                    $('#serial_code').css('border', '');
                    $('#serial_error').hide();
                    isSerialValid = false;
                }
            });

            $('#scrapButton').on('click', function(e) {
                e.preventDefault();

                //  if (!form.checkValidity()) {
                //      form.reportValidity();
                //      return;
                //  }

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

                getBoardData(qr_code, 'fetch_qrvi2.php', function(response) {
                    if (response.success === true) {

                    const scrapData = buildScrapData(qr_code, serial_code, response, "VI");
                    
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
                 });
                });

            $('#noGoodBtn').on('click', function() {
                $('#operator_name').val(loggedInUser);
                $('#nogoodModal').show();
                $('#modal_source').val('');
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