<?php include 'sidebar.php'; ?>
<?php
$dsn = "sqlsrv:Server=localhost;Database=prod_traceability";
$username = "sa";
$password = "Kepi-123";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST["new_name"]);
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    $stmt = $conn->prepare("SELECT user_password FROM user_account WHERE user_id = :user_id");
    $stmt->bindParam(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (!empty($new_name) && $new_name !== $_SESSION["user_namefl"]) {
            $update_name_stmt = $conn->prepare("UPDATE user_account SET user_namefl = :new_name WHERE user_id = :user_id");
            $update_name_stmt->bindParam(":new_name", $new_name, PDO::PARAM_STR);
            $update_name_stmt->bindParam(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
            if ($update_name_stmt->execute()) {
                $_SESSION["user_namefl"] = $new_name;
                $message .= "Name updated successfully! ";
            } else {
                $message .= "Error updating name. ";
            }
        }

        if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
            if (password_verify($current_password, $user["user_password"])) {
                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_stmt = $conn->prepare("UPDATE user_account SET user_password = :new_password WHERE user_id = :user_id");
                    $update_stmt->bindParam(":new_password", $hashed_password, PDO::PARAM_STR);
                    $update_stmt->bindParam(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
                    if ($update_stmt->execute()) {
                        $message .= "Password updated successfully!";
                    } else {
                        $message .= "Error updating password.";
                    }
                } else {
                    $message .= "New password and confirm password do not match.";
                }
            } else {
                $message .= "Current password is incorrect.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/account_settings.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <h2>Account Settings</h2>

       
        <form method="POST">
            <div class="form-group full-width">
                <label>Change Name:</label>
                <input type="text" name="new_name" value="<?= htmlspecialchars($_SESSION["user_namefl"]); ?>" autocomplete="off">
            </div>

            <div class="form-group">
                <label>Current Password:</label>
                <input type="password" name="current_password">
            </div>

            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="new_password">
            </div>

            <div class="form-group full-width">
                <label>Confirm New Password:</label>
                <input type="password" name="confirm_password">
            </div>

            <div class="full-width">
                <button type="submit">Update Account</button>
            </div>
        </form>
    </div>

    <?php if (!empty($message)): ?>
        <script>
            Swal.fire("Account Settings", "<?= $message ?>", "info");
        </script>
    <?php endif; ?>
</body>

</html>