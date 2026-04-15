<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json;');

$response = [
    'success' => false,
    'status' => 500,
    'message' => 'Database Error.',
    'data' => null,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serialcode = $_POST['serialcode'];

    if (empty($serialcode)) {
        throw new Exception('Serial Code is invalid!');
        exit;
    }

    try {
        $query = "UPDATE repair_boardanalysis SET status = 'done' WHERE serialcode = :serialcode";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':serialcode', $serialcode);
        $stmt->execute();

        $query = 'UPDATE trace_process SET wi_process=NULL, micro_process=NULL, partside1_process=NULL, partside2_process=NULL, fviss_process=NULL WHERE serialcode = :serialcode';
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':serialcode', $serialcode);
        $stmt->execute();

        $query = 'BEGIN TRANSACTION;
                    DELETE FROM wi_process WHERE serialcode = :serialcode;
                    DELETE FROM micro_process WHERE serialcode = :serialcode;
                    DELETE FROM partside1_process WHERE serialcode = :serialcode;
                    DELETE FROM partside2_process WHERE serialcode = :serialcode;
                    DELETE FROM fviss_process WHERE serialcode = :serialcode;
                    COMMIT;';
        $stmt->bindParam(':serialcode', $serialcode);
        $stmt->execute();

        $response = [
            'success' => true,
            'status' => 200,
            'message' => 'Board Successfully Verified.',
            'data' => null,
        ];
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
