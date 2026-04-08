<?php
require $_SERVER['DOCUMENT_ROOT'].'/traceability/sidebar.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/ICT.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <div class="form-container">
        <Form method="POST" id="ICTForm" name="ICTForm">
        <center><h2>ICT Board</h2></center>
        <div class="form-section">
            <div class="form-group">
                <label for="SerialCode" class="form-label">Serial Code:</label>
                <input type="text" id="SerialCode" name="SerialCode" class="form-input" required>
            </div>
            <table id="tResults">
                <thead>
                    <tr>
                        <th>Test 1</th>
                        <th>Test 2</th>
                        <th>Test 3</th>
                        <th>Judgement</th>
                    </tr>
                </thead>
                <tbody>
                  
                </tbody>
            </table>
        </div>
        </Form>
    </div>
</body>
</html>

<script>
    $(document).ready(function(){
        $('#tResults').hide();
        let SerialDebounceTimer = null;

       $('#SerialCode').on('input', function() {
    var SerialCode = $(this).val();

    clearTimeout(SerialDebounceTimer);

            if (SerialCode.length > 10) {
                SerialDebounceTimer = setTimeout(function() {
                    $.ajax({
                        url: 't_judgement.php',
                        method: 'POST',
                        data: {
                            BoardSerial: SerialCode  // Fix: matches PHP's $_POST['BoardSerial']
                        },
                        success: function(response) {
                            if (response.success === true) {

                                $('#tResults').show();

                                let results = response.data.map(x => x.Result);

                                let test1 = results[0] || '';
                                let test2 = results[1] || '';
                                let test3 = results[2] || '';

                                let tests = [test1, test2, test3];

                                let failCount = tests.filter(r => r === 'FAIL').length;
                                let filledCount = tests.filter(r => r !== '').length;

                                let allFilled = (filledCount === 3);

                                // Fix: removed duplicate judgement block, unified logic
                                let judgement = 'N/A';
                                if (allFilled) {
                                    judgement = (failCount > 1) ? 'FAIL' : 'GOOD';
                                }

                                let row = `
                                    <tr>
                                        <td style="color:${test1 === 'PASS' ? 'green' : test1 === '' ? 'black' : 'red'}">${test1 || 'N/A'}</td>
                                        <td style="color:${test2 === 'PASS' ? 'green' : test2 === '' ? 'black' : 'red'}">${test2 || 'N/A'}</td>
                                        <td style="color:${test3 === 'PASS' ? 'green' : test3 === '' ? 'black' : 'red'}">${test3 || 'N/A'}</td>
                                        <td style="color:${judgement === 'GOOD' ? 'green' : judgement === 'N/A' ? 'black' : 'red'}">${judgement}</td>
                                    </tr>
                                `;

                                $('#tResults tbody').html(row);

                            } else {
                                $('#tResults').hide();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Invalid Serial Code',
                                    text: response.message || 'Please enter a valid Serial Code.',
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end',
                                    timer: 3000
                                });
                            }
                        },
                        error: function(xhr, status, error) {  
                            Swal.fire({
                                icon: 'error',
                                title: 'Request Failed',
                                text: 'Could not reach the server. Please try again.',
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end',
                                timer: 3000
                            });
                        }
                    });
                }, 500);
            }
        });
    })
</script>