<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/sidebar.php';
require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

$query = "SELECT * FROM repair_boardanalysis WHERE status = 'll' AND status IS NOT NULL";
$stmt = $conn->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

     <link rel="stylesheet" href="css/repair_boardanalysis.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
     <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

</head>
<body>

<div class="form-container">
    <div clas="form-label">
        <center>
            <h3>Line Leader Verification</h3>
        </center>
    </div>
    <table id="table_main">
        <thead>
            <tr>
                <th>Board Serial</th>
                <th>Defect</th>
                <th>Process</th>
                <th>Analysis</th>
                <th>Action</th>
                <th>Result</th>
                <th>Operator Name</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['serialcode']); ?></td>
                    <td><?php echo htmlspecialchars($row['defect']); ?></td>
                    <td><?php echo htmlspecialchars($row['process']); ?></td>
                    <td><?php echo htmlspecialchars($row['analysis']); ?></td>
                    <td><?php echo htmlspecialchars($row['action']); ?></td>
                    <td><?php echo htmlspecialchars($row['result']); ?></td>
                    <td><?php echo htmlspecialchars($row['operator']); ?></td>
                    <td><?php echo htmlspecialchars($row['DateTime']); ?></td>
                    <td>
                        <button class="btn-repair" id="repairbtn" name="repairbtn"
                        data-serialcode = "<?php echo $row['serialcode']; ?>"
                        data-defect="<?php echo $row['defect']; ?>"
                        data-process="<?php echo $row['process']; ?>"
                        data-analysis="<?php echo $row['analysis']; ?>"
                        data-action="<?php echo $row['action']; ?>"
                        data-result="<?php echo $row['result']; ?>"
                        data-operator="<?php echo $row['operator']; ?>"
                        data-ict_jig="<?php echo $row['ict_jig']; ?>"
                        data-wi_jig="<?php echo $row['wi_jig']; ?>"
                        data-ft_jig="<?php echo $row['ft_jig']; ?>"
                        data-ict_component="<?php echo $row['ict_component']; ?>"
                        data-ict_ref="<?php echo $row['ict_ref']; ?>"
                        data-ict_reading="<?php echo $row['ict_reading']; ?>"
                        data-ft_step="<?php echo $row['ft_step']; ?>"
                        data-ft_ref="<?php echo $row['ft_ref']; ?>"
                        data-ft_result="<?php echo $row['ft_result']; ?>"
                        >Repair</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="modal">
    <div class="modal-content">
        <div class="modal-body">
            <div class="modal-header">
                <button class="close" onClick="hideModal()">&times;</button>
                <center>
                    <h4>Board Details</h4>
                </center>
            </div>
            <div class="form-section">
                <form id="modalSubmit" name="modalSubmit">
                <div class="form-group">
                    <label class="form-label" for="serialcode">Serial Code:</label>
                    <input id="serialcode" name="serialcode" class="form-input" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label" for="defect">Defect:</label>
                    <input id="defect" name="defect" class="form-input" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label" for="process">Process:</label>
                    <input id="process" name="process" class="form-input" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label" for="analysis">Analysis:</label>
                    <input id="analysis" name="analysis" class="form-input" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label" for="action">Action:</label>
                    <input id="action" name="action" class="form-input" readonly>
                </div>
                 <div class="form-group">
                    <label class="form-label" for="ict_jig">ICT Jig No.:</label>
                    <input id="ict_jig" name="ict_jig" class="form-input" readonly>
                </div>
                 <div class="form-group">
                    <label class="form-label" for="wi_jig">WI Jig No.:</label>
                    <input id="wi_jig" name="wi_jig" class="form-input" readonly>
                </div>
                 <div class="form-group">
                    <label class="form-label" for="ft_jig">FT Jig No.:</label>
                    <input id="ft_jig" name="ft_jig" class="form-input" readonly>
                </div>

                <div class="ict-section">
                        <div class="form-group">
                            <label class="form-label" for="ict_component">ICT Component:</label>
                            <input id="ict_component" name="ict_component" class="form-input" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="ict_ref">ICT Reference:</label>
                            <input id="ict_ref" name="ict_ref" class="form-input" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="ict_reading">ICT Reading:</label>
                            <input id="ict_reading" name="ict_reading" class="form-input" readonly>
                        </div>
                    </div>

                <div class="ft-section">
                    <div class="form-group">
                            <label class="form-label" for="ft_step">FT Step:</label>
                            <input id="ft_step" name="ft_step" class="form-input" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="ft_ref">FT Reference:</label>
                            <input id="ft_ref" name="ft_ref" class="form-input" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="ft_result">FT Result:</label>
                            <input id="ft_result" name="ft_result" class="form-input" readonly>
                        </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="result">Result:</label>
                    <input id="result" name="result" class="form-input" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label" for="operator">Operator:</label>
                    <input id="operator" name="operator" class="form-input" readonly>
                </div>
            </div>
            <div class="form-section">
                <div class="btn-selection">
                    <button type="submit" class="btn-close" id="scrap-btn" data-btn="scrap">Scrap</button>
                    <button type="submit" class="btn-save" id="repair-btn" data-btn="repair">Repair</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let data=null;
    let btn=null;

    function openModal(data){
        $('.modal').css('display', 'block');
            process = data.process;

            $('#serialcode').val(data.serialcode);
            $('#defect').val(data.defect);
            $('#process').val(data.process);
            $('#analysis').val(data.analysis);
            $('#action').val(data.action);
            $('#ict_jig').val(data.ict_jig);
            $('#wi_jig').val(data.wi_jig);
            $('#ft_jig').val(data.ft_jig);
            $('#result').val(data.result);
            $('#operator').val(data.operator);

            if(process === 'ICT') {
                $('.ict-section').show();
                $('#ict_component').val(data.ict_component);
                $('#ict_ref').val(data.ict_ref);
                $('#ict_reading').val(data.ict_reading);
            }

            if(process === 'FT') {
                $('.ft-section').show();
                $('#ft_step').val(data.ft_step);
                $('#ft_ref').val(data.ft_ref);
                $('#ft_result').val(data.ft_result);
            }

            if(process==='WI') {
                $('.ict-section').show();
                $('#ict_component').val(data.ict_component);
                $('#ict_ref').val(data.ict_ref);
                $('#ict_reading').val(data.ict_reading);
                $('.ft-section').show();
                $('#ft_step').val(data.ft_step);
                $('#ft_ref').val(data.ft_ref);
                $('#ft_result').val(data.ft_result);
            }
    }

    function hideModal(){
        $('.modal').hide();
    }

    $(document).ready(function(){
        table = $('#table_main').DataTable({
                "paging": true,
                "searching": true,
                deferRender: true,
                "ordering": true,
                "order": [[7, "desc"]],
                "info": false,
                "lengthChange": false,
                "pageLength": 10,
                "columnDefs": [
                    {"searchable": false, "targets": 7},
                    {"orderable": false, "targets": [0,1,2,3,4,5,6,8]}
                ]
            });

        $(document).on('click','.btn-repair',function(){
            data = $(this).data();
            openModal(data);
        })
        
        $('[data-btn]').on('click',function(){
            btn = $(this).data('btn');
        })
        
        $('#modalSubmit').on('submit',function(e){
            e.preventDefault();

            formdata = new FormData(this);
            formdata.append('process','ll');

            if(btn==='repair'){
                $.ajax({
                    url:'repair_boardanalysis_submit.php',
                    type:'POST',
                    data:formdata,
                    processData:false,
                    contentType:false,
                    success:function(response){
                        if(response.success){
                            Swal.fire({
                                icon:'success',
                                title:'Successfully Verified',
                                text:response.message,
                                toast:true,
                                position:'top-right',
                                showConfirmButton:false,
                                timer:1500
                            }).then(()=>{
                                hideModal();
                                location.reload();
                            })
                        } else{
                            Swal.fire({
                                icon:'error',
                                title:response.status,
                                text:response.message
                            })
                        }
                    }
                })
            }

            if(btn==='scrap'){
                Swal.fire('scrap')
            }
        })
    })
</script>
</body>
</html>