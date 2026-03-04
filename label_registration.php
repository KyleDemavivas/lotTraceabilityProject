<?php include 'sidebar.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Registration</title>
    <link rel="stylesheet" href="css/label_registration.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let isSubmitting = false;

        function autoSubmitFormIfValid() {
            const form = document.querySelector("form");
            const inputs = form.querySelectorAll("input[required]");
            let isValid = true;

            inputs.forEach(input => {
                if (input.value.trim() === "" || input.style.border === "2px solid red") {
                    isValid = false;
                }
            });

            if (isValid && !isSubmitting) {
                isSubmitting = true;
                const formData = new FormData(form);
                fetch("submitlabel_form.php", {
                        method: "POST",
                        body: formData,
                    })
                    .then(res => res.json())
                    .then(data => {
                        isSubmitting = false;
                        if (data.status === "error") {
                            if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    let inputField = document.querySelector(`input[name="${field}"]`);
                                    if (inputField) {
                                        let errorSpan = inputField.nextElementSibling;
                                        errorSpan.textContent = data.errors[field];
                                        errorSpan.style.display = "block";
                                        inputField.style.border = "2px solid red";
                                    }
                                });
                            } else {
                                Swal.fire("Error", data.message, "error");
                            }
                        } else {
                            Swal.fire({
                                title: "Success!",
                                text: "Form submitted successfully!",
                                icon: "success",
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                resetQRAndSerial();
                                document.getElementById("qr_code").focus();
                            });
                        }
                    })
                    .catch(err => {
                        console.error("Fetch error:", err);
                        isSubmitting = false;
                        Swal.fire("Error", "Network or server error", "error");
                    });
            }
        }

        function checkDuplicateSerials(currentInput = null) {
            let serialInputs = document.querySelectorAll(".serial-input");
            let serialValues = new Map();
            let hasDuplicates = false;
            let letterAllocation = document.getElementById("letter_allocation").value.trim();

            serialInputs.forEach(input => {
                let errorSpan = input.nextElementSibling;
                errorSpan.style.display = "none";
                input.style.border = "";
            });

            serialInputs.forEach(input => {
                let value = input.value.trim();

                if (value) {
                    if (!value.startsWith(letterAllocation)) {
                        let errorSpan = input.nextElementSibling;
                        errorSpan.textContent = "Invalid Letter Allocation!";
                        errorSpan.style.display = "block";
                        input.style.border = "2px solid red";
                        hasDuplicates = true;
                        return;
                    }

                    if (serialValues.has(value)) {
                        hasDuplicates = true;
                        let duplicateField = serialValues.get(value);

                        let firstErrorSpan = duplicateField.nextElementSibling;
                        firstErrorSpan.textContent = "Duplicate serial code!";
                        firstErrorSpan.style.display = "block";
                        duplicateField.style.border = "2px solid red";

                        let currentErrorSpan = input.nextElementSibling;
                        currentErrorSpan.textContent = "Duplicate serial code!";
                        currentErrorSpan.style.display = "block";
                        input.style.border = "2px solid red";
                    } else {
                        serialValues.set(value, input);
                    }
                }
            });

            if (!hasDuplicates) {
                autoSubmitFormIfValid();
            }
        }

        function generateSerialFields() {
            let modelValue = parseInt(document.getElementById('serial_qty').value) || 1;
            modelValue = Math.min(Math.max(modelValue, 1), 24);

            let serialContainer = document.getElementById('serial-container');
            serialContainer.innerHTML = '';

            for (let i = 1; i <= modelValue; i++) {
                let label = document.createElement('label');
                label.textContent = `Serial Code ${i}:`;

                let input = document.createElement('input');
                input.type = 'text';
                input.name = `serial_code${i}`;
                input.required = true;
                input.autocomplete = "off";
                input.classList.add("serial-input");
                input.minLength = 13;
                input.maxLength = 13;

                input.onfocus = function() {
                    input.select();
                };

                input.oninput = function(e) {
                    const value = e.target.value.toUpperCase();
                    e.target.value = value;

                    let errorSpan = e.target.nextElementSibling;

                    if (value.length !== 13) {
                        errorSpan.textContent = `Serial code must be exactly 13 characters.`;
                        errorSpan.style.display = "block";
                        e.target.style.border = "2px solid red";
                    } else {
                        errorSpan.style.display = "none";
                        e.target.style.border = "";

                        const allSerials = Array.from(serialContainer.querySelectorAll(".serial-input"));
                        const currentIndex = allSerials.indexOf(e.target);
                        if (currentIndex >= 0 && currentIndex + 1 < allSerials.length) {
                            allSerials[currentIndex + 1].focus();
                        }

                        setTimeout(() => checkDuplicateSerials(e.target), 50);
                    }
                };

                let errorSpan = document.createElement('span');
                errorSpan.classList.add("error-message");
                errorSpan.style.color = "red";
                errorSpan.style.fontSize = "12px";
                errorSpan.style.display = "none";
                errorSpan.style.marginTop = "4px";

                serialContainer.appendChild(label);
                serialContainer.appendChild(input);
                serialContainer.appendChild(errorSpan);
            }
        }

        let assyTimeout = null;
        let lastAssyRequest = 0;

        function fetchAssyDataDebounced() {
            clearTimeout(assyTimeout);
            assyTimeout = setTimeout(fetchAssyData, 300);
        }

        function fetchAssyData() {
            let assyCode = document.getElementById("assy_code").value.toUpperCase();
            document.getElementById("assy_code").value = assyCode;

            let errorSpan = document.getElementById("assy_code_error");
            let assyInput = document.getElementById("assy_code");

            errorSpan.style.display = "none";
            assyInput.style.border = "";

            if (assyCode.trim() === '') {
                return;
            }

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "fetch_assydata.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        document.getElementById("model_name").value = response.model_name;
                        document.getElementById("letter_allocation").value = response.letter_allocation;
                        document.getElementById("serial_qty").value = response.serial_qty;

                        errorSpan.style.display = "none";
                        assyInput.style.border = "";
                        generateSerialFields();
                        document.getElementById("kepi_lot").focus();
                    } else {
                        errorSpan.textContent = response.message;
                        errorSpan.style.display = "block";
                        assyInput.style.border = "2px solid red";
                    }
                }
            };
            xhr.send("assy_code=" + encodeURIComponent(assyCode));
        }


        function resetQRAndSerial() {
            document.getElementById("qr_code").value = "";
            document.getElementById("serial-container").innerHTML = "";
            generateSerialFields();
        }

        function resetForm() {
            isSubmitting = false;
            document.querySelector("form").reset();
            document.getElementById("serial-container").innerHTML = "";
            document.getElementById("assy_code").focus();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('assy_code').focus();

            document.getElementById('kepi_lot').addEventListener('input', function(e) {
                let value = e.target.value.toUpperCase();
                let errorSpan = document.getElementById("kepi_lot_error");
                e.target.value = value;

                if (!value.startsWith("KPI")) {
                    errorSpan.textContent = "Invalid KEPI Lot data. Must start with 'KPI'.";
                    errorSpan.style.display = "block";
                    e.target.style.border = "2px solid red";
                } else if (value.length !== 15) {
                    errorSpan.textContent = "KEPI Lot must be 15 characters.";
                    errorSpan.style.display = "block";
                    e.target.style.border = "2px solid red";
                } else {
                    errorSpan.style.display = "none";
                    e.target.style.border = "";
                    document.getElementById("qr_code").focus();
                }
            });

            document.getElementById("qr_code").addEventListener("input", function(e) {
                const qrInput = e.target;
                const qrCode = qrInput.value.toUpperCase();
                const kepiLot = document.getElementById("kepi_lot").value.trim().toUpperCase();
                const qrErrorSpan = document.getElementById("qr_code_error");
                qrInput.value = qrCode;
                qrErrorSpan.style.display = "none";

                if (!kepiLot || kepiLot.length !== 15) {
                    qrInput.style.border = "2px solid red";
                    return;
                }

                if (qrCode.length < 21) {
                    qrInput.style.border = "";
                    return;
                }

                if (qrCode.length > 21) {
                    qrInput.style.border = "2px solid red";
                    Swal.fire("Too Long", "QR Code must be exactly 21 characters.", "error");
                    return;
                }

                if (qrCode.substring(0, 15) !== kepiLot) {
                    qrInput.style.border = "2px solid red";
                    const qrErrorSpan = document.getElementById("qr_code_error");
                    qrErrorSpan.textContent = "KEPI LOT and QR Code do not match.";
                    qrErrorSpan.style.display = "block";
                    qrInput.style.border = "2px solid red";
                    return;
                }

                const suffix = qrCode.substring(15);
                if (!/^-\d{5}$/.test(suffix)) {
                    qrInput.style.border = "2px solid red";
                    Swal.fire("Invalid Format", "QR Code must end with a dash and 5 digits (e.g., -12345).", "error");
                    return;
                }

                qrInput.style.border = "";

                autoSubmitFormIfValid();

                const firstSerial = document.querySelector(".serial-input");
                if (firstSerial) firstSerial.focus();
            });


            document.getElementById("resetButton").addEventListener("click", function() {
                resetForm();
            });

            document.querySelectorAll("input").forEach(input => {
                input.addEventListener("focus", function() {
                    this.select();
                });
            });
        });
    </script>
</head>

<body>
    <div class="container">
        <h2 class="he2">Label Registration</h2>

        <form action="submitlabel_form.php" method="POST">
            <label>Assy Code:</label>
            <input type="text" id="assy_code" name="assy_code" required autocomplete="off" oninput="fetchAssyDataDebounced()" minlength="9" maxlength="9">

            <span id="assy_code_error" style="color: red; font-size: 22px; display: none;"></span>

            <label>Model:</label>
            <input type="text" id="model_name" name="model_name" required autocomplete="off" readonly>

            <label>Letter Allocation:</label>
            <input type="text" id="letter_allocation" name="letter_allocation" required autocomplete="off" readonly>

            <label>Board Per Sheet:</label>
            <input type="text" id="serial_qty" name="serial_qty" required autocomplete="off" readonly>

            <label>KEPI Lot:</label>
            <input type="text" id="kepi_lot" name="kepi_lot" required autocomplete="off" minlength="15" maxlength="15">
            <span id="kepi_lot_error" style="color: red; font-size: 22px; display: none;"></span>

            <label>QR Code:</label>
            <input type="text" id="qr_code" name="qr_code" required autocomplete="off" minlength="21" maxlength="21">
            <span id="qr_code_error" style="color: red; font-size: 22px; display: none;"></span>
            <br><br>

            <div id="serial-container"></div>

            <button type="button" id="resetButton">Reset</button>
        </form>
    </div>
</body>

</html>