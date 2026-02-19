<?php
include 'sidebar.php';
if (!isset($_SESSION['user_namefl'])) {
    header("Location: login.php");
    exit();
}
$operator_name = $_SESSION['user_namefl'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/mi_process.css">
</head>

<body>
    <div class="form-container">
        <h1>
            <center>MANUAL INSERTION</center>
        </h1>
        <form id="miForm" method="POST" action="manual_insertion_form.php">
            <div class="form-section">
                <div class="form-group">
                    <label class="form-label">ASSY NO:</label>
                    <input type="text" class="form-input" name="assy_code_mi" autocomplete="off" minlength="9" maxlength="9">
                </div>
                <div class="form-group">
                    <label class="form-label">MODEL NAME:</label>
                    <input type="text" class="form-input" name="model_name_mi" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">LOT NO:</label>
                    <input type="text" class="form-input" name="kepi_lot_mi" autocomplete="off" minlength="15" maxlength="15">
                </div>
                <input type="hidden" class="form-input" name="assy_code" readonly>
                <input type="hidden" class="form-input" name="model_name" readonly>
                <input type="hidden" class="form-input" name="kepi_lot" readonly>

                <div class="form-group">
                    <label class="form-label">REMARKS:</label>
                    <select class="form-input" name="mi_remarks" required>
                        <option value="">Select Remarks</option>
                        <option value="N/A">N/A</option>
                        <option value="INTERNAL 4M">Internal 4M</option>
                        <option value="EXTERNAL 4M">External 4M</option>
                        <option value="PT">PT</option>
                        <option value="PRE-ES">Pre-ES</option>
                        <option value="ES1">ES1</option>
                        <option value="ES2">ES2</option>
                        <option value="1ST MP">1ST MP</option>
                        <option value="EVALUATION">Evaluation</option>
                        <option value="PLS">PLS</option>
                        <option value="SA">SA</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">REASON:</label>
                    <input type="text" class="form-input" name="mi_reason" autocomplete="off">
                </div>
                <input type="hidden" name="operator_name" value="<?= htmlspecialchars($operator_name) ?>">
            </div>

            <div class="form-section">
                <div class="form-group">
                    <label class="form-label">QR Code:</label>
                    <input type="text" class="form-input" name="qr_code" id="qr_code" autofocus autocomplete="off" minlength="21" maxlength="21" required>
                </div>
                <input type="hidden" class=" form-input" name="qty_input" readonly>
                <div class="form-group">
                    <label class="form-label">QTY INPUT:</label>
                    <input type="text" class="form-input" name="final_qtyinput" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">OPERATOR:</label>
                    <input type="text" class="form-input" value="<?= htmlspecialchars($operator_name) ?>" readonly>
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
                        <option value="HW LA">Line A</option>
                        <option value="HW LI">Line I</option>
                        <option value="HW LO">Line O</option>
                        <option value="HW LB">Line B</option>
                        <option value="HW LP">Line P</option>
                        <option value="HW LJ">Line J</option>
                        <option value="HW LM">Line M</option>
                        <option value="HW LN">Line N</option>
                        <option value="HW LR">Line R</option>
                        <option value="HW LC">Line C</option>
                        <option value="HW LD">Line D</option>
                        <option value="HW LF">Line F</option>
                        <option value="HW LL">Line L</option>
                        <option value="HW LK">Line K</option>
                        <option value="HW LQ">Line Q</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">ASSEMBLY LINE:</label>
                    <select class="form-input" name="asmline" required>
                        <option value="">Select Line</option>
                        <option value="ASM 1">ASM 1</option>
                        <option value="ASM 2">ASM 2</option>
                        <option value="ASM 3">ASM 3</option>
                        <option value="ASM 4">ASM 4</option>
                        <option value="ASM 5">ASM 5</option>
                        <option value="ASM 6">ASM 6</option>
                        <option value="ASM 7">ASM 7</option>
                        <option value="ASM 8">ASM 8</option>
                        <option value="ASM 9">ASM 9</option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="qr_count" value="0">
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let qrCounts = {};
        let isSubmitting = false;
        let qrDebounceTimer = null;

        $('input[type="text"]').on('input', function() {
            this.value = this.value.toUpperCase();
        });

        $('#qr_code').on('focus', function() {
            $(this).select();
        });

        $('input[name="assy_code_mi"]').focus();

        $('input[name="assy_code_mi"]').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('input[name="kepi_lot_mi"]').focus();
            }
        });

        $('input[name="kepi_lot_mi"]').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('select[name="mi_remarks"]').focus();
            }
        });

        $('select[name="mi_remarks"]').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('input[name="mi_reason"]').focus();
            }
        });

        $('input[name="mi_reason"]').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#qr_code').focus().select();
            }
        });

        $('input[name="assy_code_mi"]').on('input', function() {
            let assy_code_mi = $(this).val().trim();
            if (assy_code_mi.length === 9) {
                $.post('fetch_assydata.php', {
                    assy_code: assy_code_mi
                }, function(response) {
                    if (response.success) {
                        $('input[name="model_name_mi"]').val(response.model_name);
                    } else {
                        $('input[name="model_name_mi"]').val('');
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Assy Code',
                            text: response.message
                        });
                    }
                }, 'json');
            } else {
                $('input[name="model_name_mi"]').val('');
            }
        });

        $('#qr_code').on('input', function() {
            let qr_code = $(this).val().trim();
            clearTimeout(qrDebounceTimer);

            if (qr_code.length > 20) {
                qrDebounceTimer = setTimeout(() => {
                    console.log("debouncing");
                    $.ajax({
                        url: 'fetch_qrvi.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            qr_code: qr_code
                        },
                        success: function(response) {
                            if (response.success) {
                                // Populate fields
                                $('input[name="assy_code"]').val(response.assy_code);
                                $('input[name="model_name"]').val(response.model_name);
                                $('input[name="kepi_lot"]').val(response.kepi_lot);
                                $('input[name="qty_input"]').val(response.qty_input);
                                $('input[name="final_qtyinput"]').val(parseInt(response.final_qtyinput) || 0);
                                validateAndSubmit();
                            } else {
                                $('input[name="assy_code"], input[name="model_name"], input[name="kepi_lot"], input[name="qty_input"], input[name="final_qtyinput"]').val('');

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'QR Code Not Found',
                                    text: response.message || 'This QR Code does not exist in the system.',
                                    confirmButtonText: 'OK',
                                    allowOutsideClick: false
                                }).then(() => {
                                    $('#qr_code').val('').focus();
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
                }, 500);
            }
        });

        function validateAndSubmit() {
            let requiredFields = [
                'input[name="assy_code_mi"]',
                'input[name="model_name_mi"]',
                'input[name="kepi_lot_mi"]',
                'input[name="assy_code"]',
                'input[name="model_name"]',
                'input[name="kepi_lot"]',
                'select[name="mi_remarks"]',
                'input[name="qr_code"]',
                'select[name="shift"]',
                'select[name="line"]',
                'select[name="asmline"]'
            ];

            let allFilled = requiredFields.every(function(selector) {
                let val = $(selector).val();
                return val && val.trim().length > 0;
            });

            if (!allFilled) {
                Swal.fire({
                    icon: 'error',
                    title: 'Incomplete Fields',
                    text: 'Please fill in all required fields before submitting.'
                });
                return;
            }

            if (isSubmitting) return;

            let assy_mi = $('input[name="assy_code_mi"]').val().trim();
            let assy_qr = $('input[name="assy_code"]').val().trim();
            let lot_mi = $('input[name="kepi_lot_mi"]').val().trim();
            let lot_qr = $('input[name="kepi_lot"]').val().trim();

            if (assy_mi !== assy_qr || lot_mi !== lot_qr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Mismatch',
                    text: 'ASSY Code or LOT No does not match the QR Code data.'
                });
                return;
            }
            console.log("submits");
            $('#miForm').submit();
        }

        $('#miForm').on('submit', function(e) {
            e.preventDefault();
            if (isSubmitting) return;
            isSubmitting = true;

            $.post('manual_insertion_form.php', $(this).serialize(), function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Saved Successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    $('#qr_code').val('').focus().select();
                    if (response.final_qtyinput !== undefined) {
                        $('input[name="final_qtyinput"]').val(response.final_qtyinput);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
                isSubmitting = false;
            }, 'json');
        });
    </script>

</body>

</html>