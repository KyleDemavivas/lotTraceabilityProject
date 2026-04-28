<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Something went wrong.'];
date_default_timezone_set('Asia/Manila');
$created_at = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $user_id = $_POST['user_id'];
            $query = 'DELETE FROM user_account WHERE user_id = :user_id';
            $stmt = $conn->prepare($query);
            $stmt->execute([':user_id' => $user_id]);
            $response = ['success' => true, 'message' => 'User account successfully deleted.'];
            echo json_encode($response);
            exit;
        }

        $user_id = $_POST['user_id'];
        $user_namefl = $_POST['user_namefl'];
        $user_process = $_POST['user_process'];
        $user_username = $_POST['user_username'];
        $user_type = $_POST['user_type'];
        $user_line = strval($_POST['user_line']);
        $user_section = $_POST['user_section'];
        $emp_id = $_POST['emp_id'];

        if (isset($_POST['new_password']) && !empty($_POST['new_password'])) {
            $user_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

            $query = 'UPDATE user_account 
                        SET user_namefl = :user_namefl, user_process = :user_process, 
                        user_username = :user_username, user_type = :user_type, user_line = :user_line, user_section = :user_section, user_password = :user_password, updated_at = GETDATE(), emp_id = :emp_id
                        WHERE user_id = :user_id';

            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':user_namefl' => $user_namefl,
                ':user_process' => $user_process,
                ':user_username' => $user_username,
                ':user_type' => $user_type,
                ':user_line' => $user_line,
                ':user_section' => $user_section,
                ':user_password' => $user_password,
                ':user_id' => $user_id,
                ':emp_id' => $emp_id,
            ]);
        } else {
            $query = 'UPDATE user_account 
SET user_namefl = :user_namefl, user_process = :user_process, 
user_username = :user_username, user_type = :user_type, user_line = :user_line, user_section = :user_section, updated_at = GETDATE(), emp_id = :emp_id
WHERE user_id = :user_id';

            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':user_namefl' => $user_namefl,
                ':user_process' => $user_process,
                ':user_username' => $user_username,
                ':user_type' => $user_type,
                ':user_line' => $user_line,
                ':user_section' => $user_section,
                ':user_id' => $user_id,
                ':emp_id' => $emp_id,
            ]);
        }

        $response['success'] = true;
        $response['message'] = 'User account successfully updated.';
        $response['data'] = 'Successfully Updated';
    } catch (Exception $e) {
        $response['success'] = false;
        $response['data'] = 'Database Error';
        $response['message'] = 'An error has occured.';
    }
    echo json_encode($response);
}
