<?php
session_start();

$dsn = "sqlsrv:Server=localhost;Database=prod_traceability";
$username = "sa";
$password = "Kepi-123";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_username = trim($_POST["user_username"]);
    $user_password = $_POST["user_password"];

    // Fetch user data including user_namefl
    $stmt = $conn->prepare("SELECT user_id, user_namefl, user_password, user_process FROM user_account WHERE user_username = :user_username");
    $stmt->bindParam(':user_username', $user_username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($user_password, $user["user_password"])) {
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["user_username"] = $user_username;
        $_SESSION["user_namefl"] = $user["user_namefl"];
        $_SESSION["user_process"] = $user["user_process"];

        header("Location: index.php");
        exit();
    } else {
        $error_message = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4facfe, #00f2fe);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
            text-align: left;
        }

        input {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            margin-top: 20px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4facfe;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #00c2fe;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }

        .register-link {
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (!empty($error_message)) : ?>
            <script>
                Swal.fire("Error!", "<?= $error_message ?>", "error");
            </script>
        <?php endif; ?>
        <form action="" method="POST">
            <label>Username:</label>
            <input type="text" name="user_username" required autocomplete="off">

            <label>Password:</label>
            <input type="password" name="user_password" required autocomplete="current-password">

            <button type="submit">Login</button>
        </form>
    </div>
</body>

</html>