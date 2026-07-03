    <?php

    require_once $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

    header('Content-Type: application/json');

    if(!isset($_POST['kepi_lot'])) {
        $response['success'] = false;
        $response['message'] = 'Source not provided';
        $response['data'] = 'JSON ERROR';
    }

    $Kepi_lot = trim($_POST['kepi_lot']);

    try{
        $stmt = $conn->prepare('SELECT top 1 model_name, assy_code FROM trace_process WHERE kepi_lot = :kepi_lot ORDER BY created_at DESC');
        $stmt->bindParam(':kepi_lot', $Kepi_lot, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $response['success'] = true;
            $response['message'] = 'Model found.';
            $response['data']    = [
                'model_name' => $row['model_name'],
                'assy_code'  => $row['assy_code'],
            ];
        } else {
            $response['success'] = false;
            $response['message'] = 'No record found for this lot.';
            $response['data']    = null;
        }

    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'Error fetching data: ' . $e->getMessage();
        $response['data'] = null;
    }

    echo json_encode($response);