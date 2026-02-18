<?php include 'sidebar.php'; ?>
<?php
include 'db_connect.php';

date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $assy_code = strtoupper($_POST['assy_code']);
    $model_name = strtoupper($_POST['model_name']);
    $letter_allocation = strtoupper($_POST['letter_allocation']);
    $serial_qty = $_POST['serial_qty'];
    $created_by = $_SESSION['user_namefl'] ?? 'Unknown';
    $created_date = date('Y-m-d H:i:s');

    try {
        $sql = "INSERT INTO model_data (assy_code, model_name, letter_allocation, serial_qty, created_by, created_date) 
                VALUES (:assy_code, :model_name, :letter_allocation, :serial_qty, :created_by, :created_date)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':assy_code', $assy_code, PDO::PARAM_STR);
        $stmt->bindParam(':model_name', $model_name, PDO::PARAM_STR);
        $stmt->bindParam(':letter_allocation', $letter_allocation, PDO::PARAM_STR);
        $stmt->bindParam(':serial_qty', $serial_qty, PDO::PARAM_INT);
        $stmt->bindParam(':created_by', $created_by, PDO::PARAM_STR);
        $stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
            alert('Model added successfully!');
            window.location.href='add_model.php';
            </script>";
        } else {
            echo "<script>alert('Error adding model.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/add_model.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <h2 class="he2">Add Model</h2>

        <form method="POST">
            <label>Assy Code:</label>
            <input type="text" id="assy_code" name="assy_code" required autocomplete="off"
                oninput="convertToUppercase(this)">

            <label>Model:</label>
            <input type="text" id="model_name" name="model_name" required autocomplete="off"
                oninput="convertToUppercase(this)">

            <label>Letter Allocation:</label>
            <input type="text" id="letter_allocation" name="letter_allocation" required autocomplete="off"
                oninput="convertToUppercase(this)">

            <label>Serial Quantity:</label>
            <input type="text" id="serial_qty" name="serial_qty" required autocomplete="off">

            <button type="submit">ADD</button>
        </form>
    </div>
</body>

</html>

<script>
    function convertToUppercase(input) {
        input.value = input.value.toUpperCase();
    }

    $(document).ready(function() {
        $('form').on('submit', function(e) {
            e.preventDefault();

            var data = new FormData(this);
            $.ajax({
                url: 'modelSubmit.php',
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                success: function(response){
                    Swal.fire({
                        icon: 'success',
                        title: response.data,
                        text: response.message,
                        toast: true,
                        position: 'top-right',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.reload();
                    });
                },
                error: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: response.data,
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });
</script>