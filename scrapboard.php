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
    <link rel="stylesheet" href="css/vi_process.css">
</head>

<body>
    <!-- SCRAP BOARD BUTTON -->
    <button id="scrapBoardBtn" class="scrapboard">SCRAP BOARD</button>

    <!-- SCRAP BOARD MODAL -->
    <div id="scrapBoardModal" class="modal" style="height: 100vh; display: none;">
        <div class="modal-content">
            <span class="close" id="closeScrapBoardModal">&times;</span>
            <div id="scrapBoardContent">
                <center>
                    <h2>Scrap Board</h2>
                </center>

                <form id="scrapBoardForm" method="POST">
                    <div class="column">
                        <input type="hidden" id="scrap_source" name="source" value="">

                        <div class="form-group">
                            <label for="scrap_qr_code" class="form-label">QR Code:</label>
                            <input type="text" class="form-input" name="qr_code" id="scrap_qr_code" required autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="scrap_assy_code" class="form-label">Assy Code:</label>
                            <input type="text" class="form-input" id="scrap_assy_code" name="assy_code" readonly>
                        </div>
                        <div class="form-group">
                            <label for="scrap_model_name" class="form-label">Model Name:</label>
                            <input type="text" class="form-input" id="scrap_model_name" name="model_name" readonly>
                        </div>
                        <div class="form-group">
                            <label for="scrap_kepi_lot" class="form-label">KEPI Lot:</label>
                            <input type="text" class="form-input" id="scrap_kepi_lot" name="kepi_lot" readonly>
                        </div>

                        <div class="form-group">
                            <label for="scrap_serial_code" class="form-label">Serial:</label>
                            <input type="text" id="scrap_serial_code" name="serial_code" class="form-input" required autocomplete="off" minlength="13" maxlength="13">
                        </div>

                        <div class="form-group">
                            <label for="scrap_qty" class="form-label">QTY:</label>
                            <input type="text" class="form-input" id="scrap_qty" name="qty" readonly>
                        </div>

                        <div class="form-group">
                            <label for="scrap_process" class="form-label">Process:</label>
                            <input type="text" class="form-input" id="scrap_process" name="process" value="Visual Inspection" readonly>
                        </div>

                        <div id="scrap-defect-location-container">
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

                        <center><button type="button" class="add-defect" id="addScrapDefectBtn">Add Defect</button></center>

                        <br>

                        <div class="form-group">
                            <label for="scrap_operator_name" class="form-label">Operator Name:</label>
                            <input type="text" class="form-input" id="scrap_operator_name" name="operator_name" value="<?= htmlspecialchars($_SESSION['user_namefl'] ?? '') ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="scrap_line" class="form-label">Line:</label>
                            <input type="text" class="form-input" id="scrap_line" name="line" readonly>
                        </div>

                        <div class="form-group">
                            <label for="scrap_status" class="form-label">Status:</label>
                            <input type="text" class="form-input" id="scrap_status" name="status" readonly>
                        </div>

                        <div class="form-group">
                            <label for="scrap_shift" class="form-label">Shift:</label>
                            <input type="text" class="form-input" id="scrap_shift" name="shift" readonly>
                        </div>

                        <div class="form-group">
                            <label for="scrap_analysis" class="form-label">Analysis:</label>
                            <input type="text" class="form-input" id="scrap_analysis" name="analysis" readonly>
                        </div>
                    </div>

                    <div style="margin-top: 20px;">
                        <center><button type="submit" id="scrapSaveBtn">Save</button></center>
                    </div>
                </form>
            </div>
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
        $(document).ready(function() {
            const defectOptions = `<?php foreach ($defects as $defect): ?><option value="<?= htmlspecialchars($defect) ?>"><?= htmlspecialchars($defect) ?></option><?php endforeach; ?>`;
            const locationOptions = `<?php foreach ($locations as $location): ?><option value="<?= htmlspecialchars($location) ?>"><?= htmlspecialchars($location) ?></option><?php endforeach; ?>`;

            function initScrapSelects() {
                $('#scrapBoardModal .defect-select').select2({
                    placeholder: "Select defect",
                    width: '100%',
                    dropdownParent: $('#scrapBoardModal')
                });

                $('#scrapBoardModal .location-select').select2({
                    tags: true,
                    placeholder: "Select or type locations",
                    tokenSeparators: [','],
                    width: '100%',
                    dropdownParent: $('#scrapBoardModal')
                });
            }
            initScrapSelects();
            $('#scrapBoardBtn').on('click', function() {
                $('#scrapBoardModal').fadeIn(200);
                initScrapSelects();
            });
            $('#closeScrapBoardModal').on('click', function() {
                $('#scrapBoardModal').fadeOut(200);
            });

            $(window).on('click', function(e) {
                if ($(e.target).is('#scrapBoardModal')) {
                    $('#scrapBoardModal').fadeOut(200);
                }
            });
            let scrapDefectIndex = 1;
            $('#addScrapDefectBtn').on('click', function() {
                const newRow = `
            <div class="form-group dual-inputs defect-location-row">
                <div class="half-group">
                    <label class="form-label">Defect:</label>
                    <select class="form-input defect-select" name="defect[]" required>
                        <option value="" disabled selected>Select defect</option>
                        ${defectOptions}
                    </select>
                </div>
                <div class="half-group">
                    <label class="form-label">Location:</label>
                    <select class="form-input location-select" name="location[${scrapDefectIndex}][]" multiple="multiple" required>
                        ${locationOptions}
                    </select>
                </div>
            </div>`;

                $('#scrap-defect-location-container').append(newRow);
                initScrapSelects();
                scrapDefectIndex++;
            });
        });
        $('#scrapBoardForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            $.ajax({
                url: 'scrap_board_process.php',
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
                            timer: 3000,
                            showConfirmButton: false
                        });

                        $('#scrapBoardModal').fadeOut(200);
                        resetScrapBoardForm();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'AJAX Error',
                        html: `<strong>Status:</strong> ${status}<br><strong>Error:</strong> ${error}<br><strong>Response:</strong><br>${xhr.responseText}`
                    });
                }
            });
        });

        function resetScrapBoardForm() {
            const modal = $('#scrapBoardModal');

            $('#scrapBoardForm')[0].reset();

            $('#scrap-defect-location-container .defect-location-row').not(':first').remove();

            modal.find('.defect-select, .location-select').val(null).trigger('change');

            $('#scrap_operator_name').val(loggedInUser);

            $('#scrapBoardModal .defect-select').select2({
                placeholder: "Select defect",
                width: '100%',
                dropdownParent: $('#scrapBoardModal')
            });

            $('#scrapBoardModal .location-select').select2({
                tags: true,
                placeholder: "Select or type locations",
                tokenSeparators: [','],
                width: '100%',
                dropdownParent: $('#scrapBoardModal')
            });
        }

        function resetMainForm() {
            $('scrapBoardForm')[0].reset();
            $('input[name="assy_code"],[name="scrap_qr_code"], input[name="scrap_model_name"], input[name="kepi_lot"], input[name="process"], input[name="analysis"], input[name="line"], input[name="shift"]').val('');
            $('input[name="operator_name"]').val(loggedInUser); // restore static operator
            $('#qr_code').focus();
        }
    </script>


</body>

</html>