<?php include 'sidebar.php'; ?>
<?php
if (!isset($_SESSION['user_namefl'])) {
    $_SESSION['user_namefl'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="css/mounter_process.css">
</head>

<body>
    <div class="form-container">
        <form method="POST" id="mounterForm">
            <div class="form-section">
                <div class="form-group">
                    <label class="form-label">QR Code:</label>
                    <input type="text" class="form-input" name="qr_code" id="qr_code" autofocus autocomplete="off" minlength="21" maxlength="21" required>
                </div>
                <input type="text" class="form-input" name="qty_input" id="qty_input" readonly hidden>
                <div class="form-group">
                    <label class="form-label">QTY INPUT:</label>
                    <input type="text" class="form-input" name="final_qtyinput" id="final_qtyinput" readonly>
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

            <input type="hidden" name="qr_count" value="0">
        </form>

        <div id="hourly-output-container" class="hourly">
            <!-- Hourly output table will be loaded here -->
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        let qrCounts = {};
        let isSubmitting = false;
        let lastSubmittedQR = "";
        let qrDebounceTimer = null;

        function refreshHourlyOutput(line) {
            $.ajax({
                url: 'fetch_output.php',
                method: 'POST',
                data: {
                    line: line
                },
                success: function(data) {
                    $('#hourly-output-container').html(data);
                },
                error: function(xhr, status, error) {
                    console.error("Failed to load hourly output:", error);
                }
            });
        }

        $('#qr_code').on('input', function() {
            let qr_code = $(this).val().trim();
            clearTimeout(qrDebounceTimer);

            if (qr_code.length > 20) {
                qrDebounceTimer = setTimeout(function() {
                $.ajax({
                    url: 'fetch_qrmounter.php',
                    type: 'POST',
                    data: {
                        qr_code: qr_code
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
                            $('input[name="qty_input"]').val(parseInt(response.qty_input) || 0);
                            $('input[name="final_qtyinput"]').val('LOADING...');
                            console.log(response.final_qtyinput);

                            let kepi_lot = response.kepi_lot;
                            qrCounts[kepi_lot] = (qrCounts[kepi_lot] || 0) + 1;
                            $('input[name="qr_count"]').val(qrCounts[kepi_lot]);

                            if (response.line) {
                                refreshHourlyOutput(response.line);
                            }

                            if (
                                response.assy_code &&
                                response.model_name &&
                                response.kepi_lot &&
                                response.line &&
                                response.shift &&
                                response.operator_name &&
                                response.qty_input &&
                                qr_code !== lastSubmittedQR
                            ) {
                                lastSubmittedQR = qr_code;
                                $('#mounterForm').submit();
                            } else {
                                $('#qr_code').focus().select();
                            }
                        } else {
                            $('input[name="assy_code"], input[name="model_name"], input[name="kepi_lot"], input[name="line"], input[name="shift"], input[name="operator_name"], input[name="qty_input"], input[name="final_qtyinput"]').val('');

                            Swal.fire({
                                icon: 'warning',
                                title: 'No Data Found',
                                text: response.message || 'QR Code not found.',
                                toast: true,
                                position: 'top-right',
                                showConfirmButton: false,
                                timer: 3000
                            });

                            $('#qr_code').val('').focus().select();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to fetch QR code data.',
                            toast: true,
                            position: 'top-right',
                            showConfirmButton: false,
                            timer: 3000
                        });

                        $('#qr_code').val('').focus().select();
                    }
                });
            }, 500);
            }
        });

        $('#mounterForm').submit(function(event) {
            event.preventDefault();

            if (isSubmitting) return;

            isSubmitting = true;
            $.ajax({
                url: 'mounterprocess_form.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
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
                        $('input[name="final_qtyinput"]').val(parseInt(response.final_qtyinput) || 0);
                        $('input[name="qty_input"]').select().focus();

                        // $('input[name="qty_input"]').val(parseInt(response.final_qtyinput) || 0);
                        // $('input[name="final_qtyinput"]').val(parseInt(response.final_qtyinput) || 0);

                        const currentLine = $('input[name="line"]').val();
                        if (currentLine) {
                            refreshHourlyOutput(currentLine);
                        }

                        $('#qr_code').val('').focus().select();
                        lastSubmittedQR = "";
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Submission failed.',
                            toast: true,
                            position: 'top-right',
                            showConfirmButton: false,
                            timer: 3000
                        });

                        $('#qr_code').val('').focus().select();
                    }
                },
                error: function(response) {
                    isSubmitting = false;

                    Swal.fire({
                        icon: 'error',
                        title: response.data,
                        text: response.message,
                        toast: true,
                        position: 'top-right',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    $('#qr_code').val('').focus().select();
                }
            });
        });

        $('#qr_code').val('').focus().select();
    });
</script>

</html>