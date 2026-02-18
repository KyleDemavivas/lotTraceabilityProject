<?php
require 'sidebar.php';

//session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div style="margin-left:auto; margin-right:10px">
        <h2>Welcome, <?= htmlspecialchars($_SESSION["user_username"]) ?>!</h2>
        <h3><a href="logout.php">Logout</a></h3>
    </div>
</body>

</html>