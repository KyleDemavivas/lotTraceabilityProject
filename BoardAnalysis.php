<?php
require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/sidebar.php';
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="css/BoardAnalysis.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <div class="form-container">
            
                <span>Date From:</span>
                <input type="date" id="date_from" name="date_from" onchange="filterDates()">
                <span>Date To:</span>
                <input type="date" id="date_to" name="date_to" onchange="filterDates()">

                <table id="table_main" name="table_main" class="display">
                    <thead>
                        <tr>
                            <th>Serial Code</th>
                            <th>Model</th>
                            <th>Process</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tResults">
                        
</tbody>

<!---- Sample data for testing -->
                        <!--<tr>
                            <td>123</td>
                            <td>Test Model</td>
                            <td>ICT</td>
                            <td>March 12, 2026</td>
                            <td>
                                    <button class="btn-analysis"
                                            data-serialcode="123"
                                            data-process="ICT">
                                        Analyze 
                                    </button>
                                </td>
                        </tr>
                    </tbody>
                </table>
        </div> -->
<!---- Sample data for testing -->

        <div class="modal">
           <div class="modal-content">
            <div class="modal-body">
                <button class="close" onClick="hideModal()">&times;</button>
                <div class="modal-header">
                    <h2>Board Analysis</h2>
                </div>
                <div class="modal-body">
                    <form id="analysisForm" type="submit" method="POST">
                    <div class="form-section">
                        <div class="form-group">
                            <label for="serialcode" class="form-label">Serial Code:</label>
                            <input type="text" id="serialcode" name="serialcode" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="defect" class="form-label">Defect:</label>
                            <input type="text" id="defect" name="defect" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="process" class="form-label">Process:</label>
                            <input type="text" id="process" name="process" class="form-input" readonly>
                        </div>

                        <div class="form-group">
                            <label for="ict_jig" class="form-label">ICT Jig No.:</label>
                            <input type="text" id="ict_jig" name="ict_jig" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="wi_jig" class="form-label">WI Jig No.:</label>
                            <input type="text" id="wi_jig" name="wi_jig" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="ft_jig" class="form-label">FT Jig No.:</label>
                            <input type="text" id="ft_jig" name="ft_jig" class="form-input">
                        </div>
                    

                        <div class="form-section ict-section">
                            <div class="section-title">
                                <center><h3>ICT</h3></center>
                            </div>
                            <div class="form-group">
                                <label for="component_ict" class="form-label">Component:</label>
                                <input type="text" id="component_ict" name="component_ict" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="reference_ict" class="form-label">Ref. Value:</label>
                                <input type="text" id="reference_ict" name="reference_ict" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="reading_ict" class="form-label">Reading:</label>
                                <input type="text" id="reading_ict" name="reading_ict" class="form-input">
                            </div>
                        </div>

                        <div class="form-section ft-section">
                            <div class="section-title">
                                <center><h3>FT</h3></center>
                            </div>
                            <div class="form-group">
                                <label for="step_ft" class="form-label">Step:</label>
                                <input type="text" id="step_ft" name="step_ft" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="reference_ft" class="form-label">Ref. Value:</label>
                                <input type="text" id="reference_ft" name="reference_ft" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="result_ft" class="form-label">Result:</label>
                                <input type="text" id="result_ft" name="result_ft" class="form-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="analysis" class="form-label">Analysis:</label>
                            <input type="text" id="analysis" name="analysis" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="action" class="form-label">Action:</label>
                            <input type="text" id="action" name="action" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="result_analysis" class="form-label">Result:</label>
                            <select id="result_analysis" name="result_analysis" class="form-input">
                                <option value="" disabled selected hidden>Select</option>
                                <option value="scrap">Scrap</option>
                                <option value="repair">Repair</option>
                            </select>
                        </div>

                         <div class="form-group">
                            <label for="operator_name" class="form-label">Analyzed By:</label>
                            <input type="text" id="operator_name" name="operator_name" class="form-input" readonly>
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-primary btn-save" id="submitBtn">Save Analysis</button>
                        </div>
                    </div>
</form>

                </div>
            </div>
           </div>
        </div>

        <script>
            var table
            const loggedInUser = "<?php echo $_SESSION['user_namefl']; ?>";
            
            function filterDates() {
            table.draw();
        }

        function hideModal() {
            $('.modal').hide();
        }

        function checkFields(process) {
            const serialcode = $('#serialcode').val().trim();
             const defect = $('#defect').val().trim();
             const ict_jig = $('#ict_jig').val().trim();
             const wi_jig = $('#wi_jig').val().trim();
             const ft_jig = $('#ft_jig').val().trim();
             const analysis = $('#analysis').val().trim();
             const action = $('#action').val().trim();
             const result_analysis = $('#result_analysis').val();
             const operator_name = $('#operator_name').val().trim();
             
             let component_ict = '';
             let reference_ict = '';
             let reading_ict = '';

             let step_ft = '';
             let reference_ft = '';
             let result_ft = '';

             const baseValid = serialcode && defect && ict_jig && wi_jig && ft_jig && analysis && action && result_analysis && operator_name && process;
             let ICTValid = false;
             let FTValid = false;
             let processValid = false;

             switch(process){
                case 'ICT':
                    component_ict = $('#component_ict').val().trim();
                    reference_ict = $('#reference_ict').val().trim();
                    reading_ict = $('#reading_ict').val().trim();
                    ICTValid = component_ict && reference_ict && reading_ict;
                    processValid = ICTValid;
                    break;
                
                case 'FT':
                    step_ft = $('#step_ft').val().trim();
                    reference_ft = $('#reference_ft').val().trim();
                    result_ft = $('#result_ft').val().trim();
                    FTValid = step_ft && reference_ft && result_ft;
                    processValid = FTValid;
                    break;
                default:
                    console.log("default");
                    component_ict = $('#component_ict').val().trim();
                    reference_ict = $('#reference_ict').val().trim();
                    reading_ict = $('#reading_ict').val().trim();
                    step_ft = $('#step_ft').val().trim();
                    reference_ft = $('#reference_ft').val().trim();
                    result_ft = $('#result_ft').val().trim();
                    ICTValid = component_ict && reference_ict && reading_ict;
                    FTValid = step_ft && reference_ft && result_ft;
                    processValid = ICTValid && FTValid;
                    break;     
             }

             if(!baseValid || !processValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please fill in all required fields for the selected process.',
                    confirmButtonText: 'OK'
                });
                return false;
             } 
             $('#analysisForm').submit();
                return true;
        }

        $(document).ready(function () {
            table = $('#table_main').DataTable({
                "ajax": {
                    "url": "boardanalysis_fetch.php",
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "BoardSerial" },
                    { "data": "ModelName" },
                    { "data": "Process" },
                    { "data": "DateTime" },
                    { 
                        "data": null,
                        "render": function(data, type, row) {
                            return `<button class="btn-analysis" 
                                    data-serialcode="${row.BoardSerial}" 
                                    data-process="${row.Process}">
                                    Analysis</button>`;
                        }
                    }
                ],
                "paging": true,
                "searching": true,
                deferRender: true,
                "ordering": true,
                "order": [[3, "desc"]],
                "info": false,
                "lengthChange": false,
                "pageLength": 10,
                "columnDefs": [
                    {"searchable": false, "targets": 3},
                    {"orderable": false, "targets": [0,1,2,4]}
                ]
            });

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            let dateFrom = $('#date_from').val();
            let dateTo   = $('#date_to').val();
            let rowData  = settings.aoData[dataIndex]._aData;
            let rowDate  = rowData[3]; // reads raw data, not filtered data

            console.log("rowDate:", rowDate, "from:", dateFrom, "to:", dateTo);

            if (!dateFrom && !dateTo) return true;
            if (!rowDate) return true;

            if (dateFrom && rowDate < dateFrom) return false;
            if (dateTo   && rowDate > dateTo)   return false;
            return true;
        });

        $(document).on('click', '.btn-analysis', function() {
             $('.modal').css('display', 'block');
            const serialcode = $(this).data('serialcode');
            const process = $(this).data('process');

            switch(process) {
                case 'ICT':
                    $('.ict-section').show();
                    $('.ft-section').hide();
                    break;
                case 'FT':
                    $('.ft-section').show();
                    $('.ict-section').hide();
                    break;
                case 'WI':
                    $('.ict-section').show();
                    $('.ft-section').show();
                    break;
            }
            
            $('#serialcode').val(serialcode);
            $('#process').val(process);
            $('#operator_name').val(loggedInUser);

        })

        $('#submitBtn').on('click', function() {
             const process = $('#process').val();
             checkFields(process);
        })

        $('#analysisForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);

            $.ajax({
                url: 'board_analysis_submit.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sent for Repair.',
                            text: response.data,
                            confirmButtonText: 'OK',
                            didOpen: ()=> {
                                hideModal();
                            }
                        }).then(()=>{
                            table.ajax.reload();
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'OK',
                            didOpen: ()=>{
                                hideModal();
                            }
                        })
                    }
                }
            })
        })
     })

        </script>
    </body>
    </html>