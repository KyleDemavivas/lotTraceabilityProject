<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'data' => null,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['serialcode']) {
    try {
        $serialcode = $_POST['serialcode'];
        $process = $_POST['process'];
        $defect = $_POST['defect'];
        $ict_jig = $_POST['ict_jig'];
        $wi_jig = $_POST['wi_jig'];
        $ft_jig = $_POST['ft_jig'];
        $analysis = $_POST['analysis'];
        $action = $_POST['action'];
        $result_analysis = $_POST['result_analysis'];
        $operator_name = $_POST['operator_name'];

        if ($process === 'ICT') {
            $component_ict = $_POST['component_ict'];
            $reference_ict = $_POST['reference_ict'];
            $reading_ict = $_POST['reading_ict'];

            $query = 'INSERT INTO repair_boardanalysis (serialcode, defect, process, ict_jig, wi_jig, ft_jig, analysis, action, result, operator, ict_component, ict_ref, ict_reading, DateTime) 
                  VALUES (:serialcode, :defect, :process, :ict_jig, :wi_jig, :ft_jig, :analysis, :action, :result, :operator, :ict_component, :ict_ref, :ict_reading, GETDATE())';

            $stmt = $conn->prepare($query);

            $stmt->bindParam(':serialcode', $serialcode);
            $stmt->bindParam(':defect', $defect);
            $stmt->bindParam(':process', $process);
            $stmt->bindParam(':ict_jig', $ict_jig);
            $stmt->bindParam(':wi_jig', $wi_jig);
            $stmt->bindParam(':ft_jig', $ft_jig);
            $stmt->bindParam(':analysis', $analysis);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':result', $result_analysis);
            $stmt->bindParam(':operator', $operator_name);
            $stmt->bindParam(':ict_component', $component_ict);
            $stmt->bindParam(':ict_ref', $reference_ict);
            $stmt->bindParam(':ict_reading', $reading_ict);

            $stmt->execute();
        }

        if ($process === 'FT') {
            $step_ft = $_POST['step_ft'];
            $reference_ft = $_POST['reference_ft'];
            $result_ft = $_POST['result_ft'];

            $query = 'INSERT INTO repair_boardanalysis (serialcode, defect, process, ict_jig, wi_jig, ft_jig, analysis, action, result, operator, ft_step, ft_ref, ft_result, DateTime) 
                  VALUES (:serialcode, :defect, :process, :ict_jig, :wi_jig, :ft_jig, :analysis, :action, :result, :operator, :ft_step, :ft_ref, :ft_result, GETDATE())';

            $stmt = $conn->prepare($query);

            $stmt->bindParam(':serialcode', $serialcode);
            $stmt->bindParam(':defect', $defect);
            $stmt->bindParam(':process', $process);
            $stmt->bindParam(':ict_jig', $ict_jig);
            $stmt->bindParam(':wi_jig', $wi_jig);
            $stmt->bindParam(':ft_jig', $ft_jig);
            $stmt->bindParam(':analysis', $analysis);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':result', $result_analysis);
            $stmt->bindParam(':operator', $operator_name);
            $stmt->bindParam(':ft_step', $step_ft);
            $stmt->bindParam(':ft_ref', $reference_ft);
            $stmt->bindParam(':ft_result', $result_ft);

            $stmt->execute();
        }

        if ($process === 'WI') {
            $component_ict = $_POST['component_ict'];
            $reference_ict = $_POST['reference_ict'];
            $reading_ict = $_POST['reading_ict'];

            $query = 'INSERT INTO repair_boardanalysis (serialcode, defect, process, ict_jig, wi_jig, ft_jig, analysis, action, result, operator, component_ict, reference_ict, reading_ict,ft_step, ft_ref, ft_result, DateTime) 
                  VALUES (:serialcode, :defect, :process, :ict_jig, :wi_jig, :ft_jig, :analysis, :action, :result, :operator, :component_ict, :reference_ict, :reading_ict, :ft_step, :ft_ref, :ft_result, GETDATE())';

            $stmt = $conn->prepare($query);

            $stmt->bindParam(':serialcode', $serialcode);
            $stmt->bindParam(':defect', $defect);
            $stmt->bindParam(':process', $process);
            $stmt->bindParam(':ict_jig', $ict_jig);
            $stmt->bindParam(':wi_jig', $wi_jig);
            $stmt->bindParam(':ft_jig', $ft_jig);
            $stmt->bindParam(':analysis', $analysis);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':result', $result_analysis);
            $stmt->bindParam(':operator', $operator_name);
            $stmt->bindParam(':component_ict', $component_ict);
            $stmt->bindParam(':reference_ict', $reference_ict);
            $stmt->bindParam(':reading_ict', $reading_ict);
            $stmt->bindParam(':ft_step', $step_ft);
            $stmt->bindParam(':ft_ref', $reference_ft);
            $stmt->bindParam(':ft_result', $result_ft);

            $stmt->execute();
        }

        $response = [
            'success' => true,
            'message' => 'Successfully sent board for repair.',
            'data' => null,
        ];
    } catch (PDOException $e) {
        $response['message'] = 'Database error: '.$e->getMessage();
        echo json_encode($response);
        exit;
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid request. Serial code is required.',
        'data' => null,
    ];
}

echo json_encode($response);
