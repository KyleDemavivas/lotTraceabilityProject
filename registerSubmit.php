<?php

header('content-type: application/json');

$success = false;
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_namefl = trim($_POST['user_namefl'] ?? '');
    $user_process = trim($_POST['user_process'] ?? '');
    $user_username = trim($_POST['user_username'] ?? '');
    $user_password = trim($_POST['user_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $user_type = trim($_POST['user_type'] ?? '');
    $user_line = trim($_POST['user_line'] ?? '');
    $user_section = trim($_POST['user_section'] ?? '');
    $emp_id = trim($_POST['emp_id'] ?? '');

    try {
        if (
            empty($user_namefl) || empty($user_process) || empty($user_username)
            || empty($user_password) || empty($user_type) || empty($user_line) || empty($user_section) || empty($emp_id)
        ) {
            $response['success'] = false;
            $response['message'] = 'All fields are required.';
            $response['data'] = 'Missing Fields';
            echo json_encode($response);
            exit;
        }

        if ($user_password !== $confirm_password) {
            $response['success'] = false;
            $response['message'] = 'Passwords do not match.';
            $response['data'] = 'Password Mismatch';
            echo json_encode($response);
            exit;
        }

        $stmt = $conn->prepare('SELECT COUNT(*) FROM user_account WHERE user_username = :user_username');
        $stmt->execute([':user_username' => $user_username]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $response['success'] = false;
            $response['message'] = 'Username already exists.';
            $response['data'] = 'Username Exists';
            echo json_encode($response);
            exit;
        }

        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO user_account (user_namefl, user_process, user_username, user_password, user_type, user_line, user_section, emp_id)
VALUES (:user_namefl, :user_process, :user_username, :user_password, :user_type, :user_line, :user_section, :emp_id)';
        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':user_namefl' => $user_namefl,
            ':user_process' => $user_process,
            ':user_username' => $user_username,
            ':user_password' => $hashed_password,
            ':user_type' => $user_type,
            ':user_line' => $user_line,
            ':user_section' => $user_section,
            ':emp_id' => $emp_id,
        ]);

        $response['success'] = true;
        $response['message'] = 'Registration successful!';
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'An error has occured';
        $response['data'] = $e->getMessage();
        echo json_encode($response);
        exit;
    }
    exit;
}
