<?php include 'sidebar.php'; ?>
<?php
if (!isset($_SESSION['user_namefl'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="css/spa_process.css">
</head>

<body>
    <div class="form-container">
        <form method="POST" id="modelForm">
            <div class="two-column-layout">
                <div class="column">
                    <div class="form-section">
                        <div class="form-group">
                            <label class="form-label">VERIFY ASSY NO.:</label>
                            <input type="text" class="form-input" id="verify_model" autocomplete="off">
                        </div>
                        <div id="errorMsg" style="color: red; font-size: 20px;">
                        </div>
                        <div class=" form-group">
                            <label class="form-label">QR Code:</label>
                            <input type="text" class="form-input" name="qr_code" id="qr_code" autofocus autocomplete="off" minlength="21" maxlength="21" required>
                        </div>
                        <input type="text" class="form-input" name="qty_input" readonly hidden>
                        <div class="form-group">
                            <label class="form-label">QTY INPUT:</label>
                            <input type="text" class="form-input" name="final_qtyinput" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">OPERATOR:</label>
                            <input type="text" class="form-input" name="operator_name" value="<?php echo isset($_SESSION['user_namefl']) ? $_SESSION['user_namefl'] : ''; ?>" readonly>
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
                                <option value="SMT L1">Line 1</option>
                                <option value="SMT L2">Line 2</option>
                                <option value="SMT L3">Line 3</option>
                                <option value="SMT L4">Line 4</option>
                                <option value="SMT L5">Line 5</option>
                                <option value="SMT L6">Line 6</option>
                                <option value="SMT L7">Line 7</option>
                                <option value="SMT L8">Line 8</option>
                                <option value="SMT L9">Line 9</option>
                                <option value="SMT L0">Line 10</option>
                                <option value="SMT L11">Line 11</option>
                                <option value="SMT L12">Line 12</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-group">
                            <label class="form-label">ASSY NO:</label>
                            <input type="text" class="form-input" name="assy_code" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">MODEL NAME:</label>
                            <input type="text" class="form-input" name="model_name" id="model_name" readonly>
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

                    <!-- <div style="text-align: center; margin-top: 20px;">
                        <button type="submit">Submit</button>
                    </div> -->
                </div>

                <div class="column">
                    <div class="form-section">
                        <div class="form-group">
                            <label class="form-label">ADHESIVE:</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="adhesive" value="SOLDER PASTE" onclick="showSection('solderPaste')">
                                    SOLDER PASTE
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="adhesive" value="BONDING" onclick="showSection('bonding')">
                                    BONDING
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-section" id="solderPasteSection" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">SERIAL CODE:</label>
                            <input type="text" class="form-input" name="serial_paste" id="serial_paste" required autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label class="form-label">SOLDER PASTE:</label>
                            <input type="text" class="form-input" name="solder_paste" id="solder_paste" required readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">PART LOT:</label>
                            <input type="text" class="form-input" name="part_lot" id="part_lot" required readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">TIME PULLED OUT:</label>
                            <input type="text" class="form-input" name="time_pulledout" id="time_pulledout" required readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">TIME USE:</label>
                            <input type="text" class="form-input" name="time_use" id="time_use" required readonly>
                        </div>
                    </div>

                    <div class="form-section" id="bondingSection" style="display:none;">
                        <div class="form-group">
                            <label class="form-label">SERIAL CODE:</label>
                            <input type="text" name="serial_bonding" id="serial_bonding" class="form-input" required autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label class="form-label">BONDING:</label>
                            <input type="text" name="bonding" id="bonding" class="form-input" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">PART LOT:</label>
                            <input type="text" name="bonding_part_lot" id="bonding_part_lot" class="form-input" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">TIME PULLED OUT:</label>
                            <input type="text" name="bonding_time_pulledout" id="bonding_time_pulledout" class="form-input" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">TIME USE:</label>
                            <input type="text" name="bonding_time_use" id="bonding_time_use" class="form-input" readonly>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-group">
                            <label class="form-label">STENCIL NO:</label>
                            <input type="text" class="form-input" name="stencil_no" id="stencil_no" required autocomplete="off" minlength="9" maxlength="9">
                        </div>
                        <div class="form-group">
                            <label class="form-label">NO. OF STROKES:</label>
                            <input type="text" class="form-input" name="total_stroke" id="total_stroke" required readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">TOTAL NO. OF STROKES:</label>
                            <input type="text" class="form-input" name="current_stroke" id="current_stroke" required readonly>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-group">
                            <label class="form-label">SQUEEGEE BLADE NO:</label>
                            <input type="text" class="form-input" name="squeegee_no" id="squeegee_no" required autocomplete="off" minlength="12" maxlength="12">
                        </div>
                        <div class="form-group">
                            <label class="form-label">NO. OF STROKES:</label>
                            <input type="text" class="form-input" name="squeegeetotal_stroke" id="squeegeetotal_stroke" required readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">TOTAL NO. OF STROKES:</label>
                            <input type="text" class="form-input" name="squeegeecurrent_stroke" id="squeegeecurrent_stroke" required readonly>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script>
            function showSection(section) {
                let solderPasteSection = document.getElementById('solderPasteSection');
                let bondingSection = document.getElementById('bondingSection');

                [solderPasteSection, bondingSection].forEach(sec => {
                    sec.style.display = 'none';
                    sec.querySelectorAll('input').forEach(input => {
                        input.value = '';
                        input.checked = false;
                        input.removeAttribute('required');
                    });
                });
                if (section === 'solderPaste') {
                    solderPasteSection.style.display = 'block';
                    solderPasteSection.querySelectorAll('input').forEach(input => input.setAttribute('required', 'required'));
                } else if (section === 'bonding') {
                    bondingSection.style.display = 'block';
                    bondingSection.querySelectorAll('input').forEach(input => input.setAttribute('required', 'required'));
                }
            }
        </script>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        let qrCounts = {};
        let modelNameFromQR = '';
        let isSubmitting = false;
        let qrDebounceTimer = null;

        function resetFormWithAlert(title, text) {
            Swal.fire({
                icon: 'error',
                title: title,
                text: text,
                confirmButtonText: 'OK'
            }).then(() => {
                $('#modelForm')[0].reset();
                $('input[type="text"], input[type="hidden"]').val('');
                $('select').val('');
                $('input[name="operator_name"]').val('<?php echo $_SESSION['user_namefl']; ?>');
                $('#verify_model').prop('readonly', false).val('');
                $('#errorMsg').text('');
                $('#solderPasteSection, #bondingSection').hide();
                $('#qr_code').focus();
            });
        }

        function tryAutoSubmit() {
            if (isFormReadyToSubmit() && !isSubmitting) {
                isSubmitting = true;
                $('#modelForm').trigger('submit');
            }
        }

        $('#qr_code').on('input', function() {
            var qr_code = $(this).val();
            clearTimeout(qrDebounceTimer);

            if (qr_code.length > 20) {
                qrDebounceTimer = setTimeout(() => {
                    $.ajax({
                        url: 'fetch_qrdata.php',
                        type: 'POST',
                        data: {
                            qr_code: qr_code
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                let new_kepi_lot = response.kepi_lot;
                                let current_kepi_lot = $('input[name="kepi_lot"]').val();

                                if (current_kepi_lot && new_kepi_lot !== current_kepi_lot) {
                                    const prevModelName = $('input[name="model_name"]').val().trim();
                                    const newModelName = response.model_name.trim();

                                    if (prevModelName !== newModelName) {
                                        $('#modelForm')[0].reset();
                                        $('input[type="text"], input[type="hidden"]').val('');
                                        $('select').val('');
                                        $('input[name="operator_name"]').val('<?php echo $_SESSION['user_namefl']; ?>');
                                        $('#verify_model').prop('readonly', false).val('');
                                        $('#errorMsg').text('');
                                        $('#solderPasteSection, #bondingSection').hide();
                                    } else {
                                        const adhesiveVal = $('input[name="adhesive"]:checked').val();
                                        const sectionData = {};

                                        if (adhesiveVal === 'SOLDER PASTE') {
                                            sectionData.serial = $('#serial_paste').val();
                                            sectionData.solder_paste = $('#solder_paste').val();
                                            sectionData.part_lot = $('#part_lot').val();
                                            sectionData.time_pulledout = $('#time_pulledout').val();
                                            sectionData.time_use = $('#time_use').val();
                                        } else if (adhesiveVal === 'BONDING') {
                                            sectionData.serial = $('#serial_bonding').val();
                                            sectionData.bonding = $('#bonding').val();
                                            sectionData.part_lot = $('#part_lot').val();
                                            sectionData.time_pulledout = $('#time_pulledout').val();
                                            sectionData.time_use = $('#time_use').val();
                                        }
                                        const stencilNo = $('#stencil_no').val();
                                        const stencilTotal = $('input[name="total_stroke"]').val();
                                        const stencilCurrent = $('input[name="current_stroke"]').val();
                                        const squeegeeNo = $('#squeegee_no').val();
                                        const squeegeeTotal = $('input[name="squeegeetotal_stroke"]').val();
                                        const squeegeeCurrent = $('input[name="squeegeecurrent_stroke"]').val();
                                        const shiftVal = $('select[name="shift"]').val();
                                        const lineVal = $('select[name="line"]').val();

                                        $('#modelForm')[0].reset();
                                        $('input[type="text"], input[type="hidden"]').val('');
                                        $('select').val('');

                                        $('input[name="operator_name"]').val('<?php echo $_SESSION['user_namefl']; ?>');
                                        $('select[name="shift"]').val(shiftVal);
                                        $('select[name="line"]').val(lineVal);
                                        $('#verify_model').prop('readonly', false).val('');
                                        $('#errorMsg').text('');

                                        if (adhesiveVal) {
                                            $(`input[name="adhesive"][value="${adhesiveVal}"]`).prop('checked', true);
                                            showSection(adhesiveVal === 'SOLDER PASTE' ? 'solderPaste' : 'bonding');
                                        }
                                        if (adhesiveVal === 'SOLDER PASTE') {
                                            $('#serial_paste').val(sectionData.serial);
                                            $('#solder_paste').val(sectionData.solder_paste);
                                            $('#part_lot').val(sectionData.part_lot);
                                            $('#time_pulledout').val(sectionData.time_pulledout);
                                            $('#time_use').val(sectionData.time_use);
                                        } else if (adhesiveVal === 'BONDING') {
                                            $('#serial_bonding').val(sectionData.serial);
                                            $('#bonding').val(sectionData.bonding);
                                            $('#bonding_part_lot').val(sectionData.part_lot);
                                            $('#bonding_time_pulledout').val(sectionData.time_pulledout);
                                            $('#bonding_time_use').val(sectionData.time_use);
                                        }
                                        $('#stencil_no').val(stencilNo);
                                        $('input[name="total_stroke"]').val(stencilTotal);
                                        $('input[name="current_stroke"]').val(stencilCurrent);
                                        $('#squeegee_no').val(squeegeeNo);
                                        $('input[name="squeegeetotal_stroke"]').val(squeegeeTotal);
                                        $('input[name="squeegeecurrent_stroke"]').val(squeegeeCurrent);
                                    }

                                    $('#qr_code').focus();
                                }

                                $('input[name="assy_code"]').val(response.assy_code);
                                $('input[name="model_name"]').val(response.model_name);
                                $('input[name="kepi_lot"]').val(response.kepi_lot);
                                $('input[name="serial_qty"]').val(response.serial_qty);
                                $('input[name="qty_input"]').val(response.serial_qty);

                                let kepi_lot = response.kepi_lot;
                                qrCounts[kepi_lot] = (qrCounts[kepi_lot] || 0) + 1;
                                $('input[name="qr_count"]').val(qrCounts[kepi_lot]);

                                let serial_qty = parseInt(response.serial_qty) || 0;
                                $('input[name="qty_input"]').val(serial_qty);
                                $('input[name="final_qtyinput"]').val(parseInt(response.final_qtyinput) || 0);

                                tryAutoSubmit();
                            } else {
                                resetFormWithAlert('QR Code Error', 'No data found for the scanned QR code.');
                            }
                        }
                    });

                }, 500);
            }
        });

        $('input[type="text"]').on('input', function() {
            this.value = this.value.toUpperCase();
        });

        $('#modelForm').submit(function(event) {
            event.preventDefault();
            $('#verify_model').css('border-color', '');
            $('#errorMsg').text('').hide();

            const verifyModel = $('#verify_model').val().trim();
            const actualAssy = $('input[name="assy_code"]').val().trim();

            if (verifyModel !== actualAssy) {
                $('#verify_model').css('border-color', 'red');
                $('#errorMsg').text('ASSY NO does not match!');
                isSubmitting = false;
                return;
            }

            $('#verify_model').prop('readonly', true).css('border-color', '');
            $('#errorMsg').text('');

            let stencil_total = parseInt($('input[name="total_stroke"]').val()) || 0;
            let stencil_current = parseInt($('input[name="current_stroke"]').val()) || 0;
            let squeegee_total = parseInt($('input[name="squeegeetotal_stroke"]').val()) || 0;
            let squeegee_current = parseInt($('input[name="squeegeecurrent_stroke"]').val()) || 0;

            if (stencil_current >= stencil_total) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stencil Limit Reached',
                    text: 'The stencil has reached its maximum number of strokes.',
                    confirmButtonText: 'OK'
                });
                isSubmitting = false;
                return;
            }

            if (squeegee_current >= squeegee_total) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Squeegee Limit Reached',
                    text: 'The squeegee blade has reached its maximum number of strokes.',
                    confirmButtonText: 'OK'
                });
                isSubmitting = false;
                return;
            }

            $.ajax({
                url: 'spaprocess_form.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    isSubmitting = true;
                    $('#modelForm :input').prop('disabled', true);
                },
                success: function(response) {
                    $('#modelForm :input').prop('disabled', false);
                    isSubmitting = false;

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Form submitted successfully!',
                            toast: true,
                            position: 'top-right',
                            showConfirmButton: false,
                            timer: 3000
                        });

                        if (response.updated_stencil_stroke !== null) {
                            $('input[name="current_stroke"]').val(response.updated_stencil_stroke);
                        }
                        if (response.updated_squeegee_stroke !== null) {
                            $('input[name="squeegeecurrent_stroke"]').val(response.updated_squeegee_stroke);
                        }

                        if (response.final_qtyinput !== undefined) {
                            $('input[name="final_qtyinput"]').val(response.final_qtyinput);
                        }

                        $('#qr_code').val('').focus();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            toast: true,
                            position: 'top-right',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                },
                error: function() {
                    $('#modelForm :input').prop('disabled', false);
                    isSubmitting = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: 'Something went wrong. Please try again.',
                        toast: true,
                        position: 'top-right',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        });

        $('#stencil_no').on('input', function() {
            var stencil_no = $(this).val();
            clearTimeout(qrDebounceTimer);

            if (stencil_no.length > 8) {
                qrDebounceTimer = setTimeout(() => {
                $.ajax({
                    url: 'fetch_rightdata.php',
                    type: 'POST',
                    data: {
                        stencil_no: stencil_no
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('input[name="total_stroke"]').val(response.total_stroke);
                            $('input[name="current_stroke"]').val(response.last_stencil_stroke_spa ?? response.current_stroke);
                            tryAutoSubmit();
                        } else {
                            Swal.fire('No Stencil Data', 'No data found for the entered Stencil No.', 'warning');
                        }
                    }
                });
            }, 500);
            }
        });

        $('#squeegee_no').on('input', function() {
            var squeegee_no = $(this).val();
            clearTimeout(qrDebounceTimer);
            if (squeegee_no.length > 11) {
                qrDebounceTimer = setTimeout(() => {
                $.ajax({
                    url: 'fetch_rightdata.php',
                    type: 'POST',
                    data: {
                        squeegee_no: squeegee_no
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('input[name="squeegeetotal_stroke"]').val(response.squeegeetotal_stroke);
                            $('input[name="squeegeecurrent_stroke"]').val(response.last_squeegee_stroke_spa ?? response.squeegeecurrent_stroke);
                            tryAutoSubmit();
                        } else {
                            Swal.fire('No Squeegee Data', 'No data found for the entered Squeegee Blade No.', 'warning');
                        }
                    }
                });
            }, 500);
            }
        });

        $('#serial_paste').on('input', function() {
            var serial_paste = $(this).val();
            clearTimeout(qrDebounceTimer);
            if (serial_paste.length > 8) {
                qrDebounceTimer = setTimeout(() => {
                $.ajax({
                    url: 'fetch_rightdata.php',
                    type: 'POST',
                    data: {
                        serial_paste: serial_paste
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('input[name="solder_paste"]').val(response.solder_paste);
                            $('input[name="part_lot"]').val(response.part_lot);
                            $('input[name="time_pulledout"]').val(response.time_pulledout);
                            $('input[name="time_use"]').val(response.time_use);
                            tryAutoSubmit();
                        } else {
                            Swal.fire('No Solder Paste Data', 'No data found for the entered Serial Paste.', 'warning');
                        }
                    }
                });
            }, 500);
            }
        });

        $('#serial_bonding').on('input', function() {
            const serial_bonding = $(this).val().trim();
            clearTimeout(qrDebounceTimer);
            
            if (serial_bonding.length >= 12) {
                qrDebounceTimer = setTimeout(() => {
                $.ajax({
                    url: 'fetch_rightdata.php',
                    type: 'POST',
                    data: {
                        serial_bonding
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#bonding').val(response.bonding);
                            $('#bonding_part_lot').val(response.part_lot);
                            $('#bonding_time_pulledout').val(response.time_pulledout);
                            $('#bonding_time_use').val(response.time_use);
                            tryAutoSubmit();
                        } else {
                            Swal.fire('No Bonding Data', 'Invalid bonding serial.', 'warning');
                        }
                    }
                });
            }, 500);
            }
        });



        function isFormReadyToSubmit() {
            const assy = $('input[name="assy_code"]').val().trim();
            const verify = $('#verify_model').val().trim();
            const qty = $('input[name="final_qtyinput"]').val().trim();
            const operator = $('input[name="operator_name"]').val().trim();
            const shift = $('select[name="shift"]').val();
            const line = $('select[name="line"]').val();
            const stencil = $('#stencil_no').val().trim();
            const squeegee = $('#squeegee_no').val().trim();
            const adhesive = $('input[name="adhesive"]:checked').val();

            if (!assy || !verify || !qty || !operator || !shift || !line || !stencil || !squeegee || !adhesive) {
                return false;
            }

            if (adhesive === "SOLDER PASTE") {
                const serial = $('#serial_paste').val().trim();
                const solder = $('#solder_paste').val().trim();
                const partLot = $('#part_lot').val().trim();
                const pulledOut = $('#time_pulledout').val().trim();
                const useTime = $('#time_use').val().trim();
                return serial && solder && partLot && pulledOut && useTime;
            }

            if (adhesive === "BONDING") {
                return (
                    $('#serial_bonding').val().trim() &&
                    $('#bonding').val().trim() &&
                    $('#bonding_part_lot').val().trim() &&
                    $('#bonding_time_pulledout').val().trim() &&
                    $('#bonding_time_use').val().trim()
                );
            }

            return false;
        }

        $('#qr_code').focus();
    });
</script>

</html>