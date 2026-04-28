<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Serial Code is Null!',
    'data' => [],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['SerialCode'])) {
    $BoardSerial = $_POST['SerialCode'];

    try {
        $query = 'SELECT TOP 3 Result FROM v_ict WHERE BoardSerial = :BoardSerial ORDER BY DateTime ASC';
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':BoardSerial', $BoardSerial);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($data)) {
            $response = [
                'success' => false,
                'message' => 'No data was found for the provided Serial Code.',
                'data' => [],
            ];
        } else {
            $response = [
                'success' => true,
                'message' => 'Data retrieved successfully.',
                'data' => $data,
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'success' => false,
            'message' => 'Database connection failed: '.$e->getMessage(),
            'data' => [],
        ];
    }
}

echo json_encode($response);
