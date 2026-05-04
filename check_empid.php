<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'status' => 500,
    'message' => 'Database Error.',
    'data' => null,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empid = $_POST['empid'] ?? '';

    if (empty($empid)) {
        exit('Employee ID is null.');
    }

    try {
        $query = 'SELECT COUNT(emp_id) FROM user_account WHERE emp_id = :empid';
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':empid', $empid, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $response = [
                'success' => false,
                'status' => 300,
                'message' => 'Employee ID already exists!',
                'data' => null,
            ];
        } else {
            $response = [
                'success' => true,
                'status' => 200,
                'message' => 'Employee ID available.',
                'data' => null,
            ];
        }
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'status' => 400,
            'message' => $e->getMessage(),
            'data' => null,
        ];
    }
}

echo json_encode($response);
