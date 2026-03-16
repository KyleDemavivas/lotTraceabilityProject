<?php
include 'sidebar.php';

// This file handles user registration

// Features include: real time password checking (only comparison between password and confirm password)
// username check handled by ajax that calls check_username.php

// submmission is handled via ajax that calls register.php

// all script files are found html tag on bottom of page

// this file uses sweetalert2
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.18/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(to right, #4facfe, #00f2fe);
        display: flex;
        justify-content: center;
        align-items: center;
        height: auto;
        margin: 1% auto;
    }

    .container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        width: 450px;
        text-align: center;
        height: auto;
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

    input,
    select {
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

    .button-1 {
        margin-top: 20px;
        padding: 10px;
        border: none;
        border-radius: 5px;
        background-color: rgb(254, 79, 117);
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }

    .button-1:hover {
        background-color: rgb(254, 0, 34);
    }
</style>

<body>
    <?php if (isset($success) && $success) { ?>
        <div id="success"></div>
    <?php } ?>
    <div class="container">
        <h2>Register</h2>
        <form action="" method="POST" name="registerForm" id="mainForm">
            <label>Name:</label>
            <input type="text" id="user_namefl" name="user_namefl" required value="<?php echo isset($_POST['user_namefl']) ? htmlspecialchars($_POST['user_namefl']) : ''; ?>" autocomplete="off">

            <label>Section:</label>
            <select name="user_section" id="user_section" required>
                <option value="" disabled selected>Select Section</option>
                <option value="IT" <?php echo (isset($_POST['user_section']) && $_POST['user_section'] == 'IT') ? 'selected' : ''; ?>>IT</option>
                <option value="QA Engr" <?php echo (isset($_POST['user_section']) && $_POST['user_section'] == 'QA Engr') ? 'selected' : ''; ?>>QA</option>
                <option value="SMT" <?php echo (isset($_POST['user_section']) && $_POST['user_section'] == 'SMT') ? 'selected' : ''; ?>>SMT</option>
                <option value="HANDWORK" <?php echo (isset($_POST['user_section']) && $_POST['user_section'] == 'HANDWORK') ? 'selected' : ''; ?>>HANDWORK</option>
            </select>

            <label>Process:</label>
            <select name="user_process" id="user_process" required>
                <option value="" disabled selected>Select Process</option>
                <option value="LABELLER" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'LABELLER') ? 'selected' : ''; ?>>LABELLER</option>
                <option value="SPA" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'SPA') ? 'selected' : ''; ?>>SPA</option>
                <option value="MOUNTER" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'MOUNTER') ? 'selected' : ''; ?>>MOUNTER</option>
                <option value="VISUAL INSPECTION" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'VISUAL INSPECTION') ? 'selected' : ''; ?>>VISUAL INSPECTION</option>
                <option value="REPAIRER" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'REPAIRER') ? 'selected' : ''; ?>>REPAIRER</option>
                <option value="LL VERIFICATION" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'LL VERIFICATION') ? 'selected' : ''; ?>>LL VERIFICATION</option>
                <option value="AUTOMATIC INSERTION" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'AUTOMATIC INSERTION') ? 'selected' : ''; ?>>AUTOMATIC INSERTION</option>
                <option value="MANUAL INSERTION" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'MANUAL INSERTION') ? 'selected' : ''; ?>>MANUAL INSERTION</option>
                <option value="MODIFICATOR 1" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'MODIFICATOR 1') ? 'selected' : ''; ?>>MODIFICATOR 1</option>
                <option value="MODIFICATOR 2" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'MODIFICATOR 2') ? 'selected' : ''; ?>>MODIFICATOR 2</option>
                <option value="FVI SOLDERSIDE" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'FVI SOLDERSIDE') ? 'selected' : ''; ?>>FVI SOLDERSIDE</option>
                <option value="PARTSIDE 1" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'PARTSIDE 1') ? 'selected' : ''; ?>>PART SIDE 1</option>
                <option value="PARTSIDE 2" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'PARTSIDE 2') ? 'selected' : ''; ?>>PART SIDE 2</option>
                <option value="MICROSCOPE INSPECTION" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'MICROSCOPE INSPECTION') ? 'selected' : ''; ?>>MICROSCOPE INSPECTION</option>
                <option value="WITHSTAND INSULATION TEST" <?php echo (isset($_POST['user_process']) && $_POST['user_process'] == 'WITHSTAND INSULATION TEST') ? 'selected' : ''; ?>>WITHSTAND INSULATION TEST</option>
            </select>

            <label>Username:</label>
            <input type="text" id="user_username" name="user_username" required value="<?php echo isset($_POST['user_username']) ? htmlspecialchars($_POST['user_username']) : ''; ?>" autocomplete="off">
            <small id="username_message" style="color: red;"></small>

            <span id="password_message" style="margin-top: 10px; margin-bottom:-5px"></span>

            <label>Password:</label>
            <div style="display: flex; align-items: center; gap: 8px;">
                <input type="password" name="user_password" required autocomplete="new-password" id="user_password" style="width: 100%;">
                <input type="checkbox" id="show_password"> <span>Show</span>
            </div>

            <label>Confirm Password:</label>
            <div style="display: flex; align-items: center; gap: 8px;">
                <input type="password" name="confirm_password" required autocomplete="new-password" id="confirm_password" style="width: 100%;">
            </div>

            <label>User Type:</label>
            <select name="user_type" required>
                <option value=""></option>
                <option value="Admin" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="User" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'User') ? 'selected' : ''; ?>>User</option>
            </select>

            <label>Line:</label>
            <select name="user_line" required>
                <option value="" disabled selected>Select Line</option>
                <option value="1" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == '1') ? 'selected' : ''; ?>>LINE 1</option>
                <option value="2" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == '2') ? 'selected' : ''; ?>>LINE 2</option>
                <option value="3" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == '3') ? 'selected' : ''; ?>>LINE 3</option>
                <option value="4" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == '4') ? 'selected' : ''; ?>>LINE 4</option>
                <option value="5" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == '5') ? 'selected' : ''; ?>>LINE 5</option>
                <option value="6" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == '6') ? 'selected' : ''; ?>>LINE 6</option>
                <option value="7" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == '7') ? 'selected' : ''; ?>>LINE 7</option>
                <option value="8" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == '8') ? 'selected' : ''; ?>>LINE 8</option>
                <option value="9" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == '9') ? 'selected' : ''; ?>>LINE 9</option>
                <option value="10" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == '10') ? 'selected' : ''; ?>>LINE 10</option>
                <option value="11" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == '11') ? 'selected' : ''; ?>>LINE 11</option>
                <option value="12" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == '12') ? 'selected' : ''; ?>>LINE 12</option>
                <option value="AV1" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'AV1') ? 'selected' : ''; ?>>AV1</option>
                <option value="AV2" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'AV2') ? 'selected' : ''; ?>>AV2</option>
                <option value="RG2" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'RG2') ? 'selected' : ''; ?>>RG2</option>
                <option value="RG131" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'RG131') ? 'selected' : ''; ?>>RG131</option>
                <option value="A" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'A') ? 'selected' : ''; ?>>LINE A</option>
                <option value="I" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'I') ? 'selected' : ''; ?>>LINE I</option>
                <option value="O" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'O') ? 'selected' : ''; ?>>LINE O</option>
                <option value="B" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'B') ? 'selected' : ''; ?>>LINE B</option>
                <option value="P" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'P') ? 'selected' : ''; ?>>LINE P</option>
                <option value="J" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'J') ? 'selected' : ''; ?>>LINE J</option>
                <option value="M" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'M') ? 'selected' : ''; ?>>LINE M</option>
                <option value="N" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'N') ? 'selected' : ''; ?>>LINE N</option>
                <option value="R" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'R') ? 'selected' : ''; ?>>LINE R</option>
                <option value="C" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'C') ? 'selected' : ''; ?>>LINE C</option>
                <option value="D" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'D') ? 'selected' : ''; ?>>LINE D</option>
                <option value="F" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'F') ? 'selected' : ''; ?>>LINE F</option>
                <option value="L" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'L') ? 'selected' : ''; ?>>LINE L</option>
                <option value="K" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'K') ? 'selected' : ''; ?>>LINE K</option>
                <option value="Q" <?php echo (isset($_POST['user_line']) && $_POST['user_line'] == 'Q') ? 'selected' : ''; ?>>LINE Q</option>

            </select>

            <button type="submit">Register</button>
            <button type="button" class="button-1" onclick="window.location.href='login.php'">Login</button>
        </form>
    </div>

    <script>
        <?php if (!empty($notification)) {
            echo $notification;
        } ?>
    </script>
</body>

</html>
<script>
    $(document).ready(function() {
        $("#user_username").on("input", function() {
            let username = $("input[name='user_username']").val();

            if (username.length < 3) {
                $("#username_message").text("Username must be at least 3 characters.").css("color", "red");
                return;
            }

            $.ajax({
                type: "POST",
                url: "check_username.php",
                data: {
                    user_username: username
                },
                dataType: "json",
                success: function(response) {
                    if (response.status === "exists") {
                        $("#username_message").text(response.message).css("color", "red");
                    } else {
                        $("#username_message").text(response.message).css("color", "green");
                    }
                },
                error: function() {
                    $("#username_message").text("Error checking username.").css("color", "red");
                }
            });
        });

        $("#show_password").on("click", function() {
            let type = $(this).is(':checked') ? 'text' : 'password';
            $("#user_password, #confirm_password").attr('type', type);
        });

        $("#user_password, #confirm_password").on('input', function() {
            console.log('works');
            var user_password = $("#user_password").val();
            var confirm_password = $("#confirm_password").val();

            if (user_password !== "" && confirm_password !== "") {
                if (user_password !== confirm_password) {
                    $("#password_message").text("Passwords do not match.").css("color", "red");
                    $("#user_password").css("border", "1px solid red");
                    $("#confirm_password").css("border", "1px solid red");
                } else if (user_password.length < 8) {
                    $("#password_message").text("Password must be at least 8 characters.").css("color", "red");
                } else {
                    $("#password_message").text("Passwords match.").css("color", "green");
                    $("#user_password").css("border", "1px solid green");
                    $("#confirm_password").css("border", "1px solid green");
                }
            }
        });

        $("#mainForm").on('submit', function(e) {
            e.preventDefault();

            let data = new FormData(this);
            $.ajax({
                url: 'registerSubmit.php',
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registered Successfully',
                            text: response.message,
                            toast: true,
                            position: 'top-right',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $("#mainForm")[0].reset();
                            $("#user_username").text("");
                            $("#password_message").text("");
                            $('#username_message').text("");
                            $("#user_password, #confirm_password").css("border", "");
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.data,
                            text: response.message,
                            toast: true,
                            position: 'top-right',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    };
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>