<?php
// This file displays all registered users for the system

// Features: Editing and Deleting of user info

// Upon click on edit button edit modal is opened

// Edit submit is handled via ajax that calls useredit.php

// Delete requests are handled via function userDelete(id) that calls via ajax, useredit.php
// userDelete(id) function will prompt a confirmation dialog before deleting the user

include 'sidebar.php';
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

$stmt = $conn->prepare('SELECT * FROM user_account');
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/repair_process.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.6/sorting/datetime-moment.js"></script>

</head>

<body>
    <div class="container" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <h2>USER ACCOUNTS</h2>
        <div class="table-container">
            <table id="userTable" class="display">
                <thead>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Process</th>
                    <th>Username</th>
                    <th>Type</th>
                    <th>Line</th>
                    <th>Section</th>
                    <th>Date Created</th>
                    <th>Updated Date</th>
                    <th>Action</th>
                </thead>

                <tbody>
                    <?php foreach ($users as $row) { ?>
                        <tr>
                            <td id="emp_id"><?php echo $row['emp_id']; ?></td>
                            <td id="user_namefl"><?php echo $row['user_namefl']; ?></td>
                            <td id="user_process"><?php echo $row['user_process']; ?></td>
                            <td id="user_username"><?php echo $row['user_username']; ?></td>
                            <td id="user_type"><?php echo $row['user_type']; ?></td>
                            <td id="user_line"><?php echo $row['user_line']; ?></td>
                            <td id="user_section"><?php echo $row['user_section']; ?></td>
                            <td id="date_created"><?php echo date('F d, Y h:i A', strtotime($row['created_at'])); ?></td>
                           <td id="updated_date">
                            <?php echo $row['updated_at'] ? date('F d, Y h:i A', strtotime($row['updated_at'])) : 'Not yet edited'; ?>
                        </td>
                            
                            <td>
                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                                    <button onclick='openEditModal(<?php echo json_encode($row); ?>)'>EDIT</button>
                                    <button class="button-close" onclick="deleteUser(<?php echo $row['user_id']; ?>)">DELETE</button>
                                </div>
                            </td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit User Account</h2>
            <span class="close" onclick="closeModal()" style="position:absolute; top:10px; right:15px; font-size:24px; cursor:pointer;">&times;</span>
            <form id="modalEditForm">
                <input type="hidden" name="user_id" id="modal_user_id">
                <div class="form-group" style="margin-top:15px;">
                    <label class="form-label">ID:</label>
                    <input type="text" name="emp_id" id="modal_emp_id" class="form-input" required>
                </div>
                <div class="form-group" style="margin-top:15px;">
                    <label class="form-label">Name:</label>
                    <input type="text" name="user_namefl" id="modal_user_namefl" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="modal_user_process">Process:</label>
                    <select name="user_process" id="modal_user_process" class="form-input" required>
                        <option value="LABELLER">LABELER</option>
                        <option value="SPA">SPA</option>
                        <option value="MOUNTER">MOUNTER</option>
                        <option value="VISUAL INSPECTION">VISUAL INSPECTION</option>
                        <option value="REPAIRER">REPAIRER</option>
                        <option value="LL VERIFICATION">LL VERIFICATION</option>
                        <option value="AUTOMATIC INSERTION">AUTOMATIC INSERTION</option>
                        <option value="MANUAL INSERTION">MANUAL INSERTION</option>
                        <option value="MODIFICATOR 1">MODIFICATOR 1</option>
                        <option value="MODIFICATOR 2">MODIFICATOR 2</option>
                        <option value="FVI SOLDERSIDE">FVI SOLDERSIDE</option>
                        <option value="PART SIDE 1">PART SIDE 1</option>
                        <option value="PART SIDE 2">PART SIDE 2</option>
                        <option value="MICROSCOPE INSPECTION">MICROSCOPE INSPECTION</option>
                        <option value="WITHSTAND INSULATION TEST">WITHSTAND INSULATION TEST</option>
                        <option value="ADMIN">ADMIN</option>
                        <option value="WITHSTAND INSULATION TEST">WITHSTAND INSULATION TEST</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Username:</label>
                    <input type="text" name="user_username" id="modal_user_username" class="form-input" required>
                </div>
                <div class="form-group" style="display: flex; justify-content: center; align-items: center;">
                    <span id="password_match_feedback" style="font-size:12px; color:red;"></span>
                </div>
                <div class="form-group">
                    <label class="form-label">New Password:</label>
                    <input type="password" name="new_password" id="modal_new_password" class="form-input" autocomplete="new-password">
                    <input type="checkbox" id="toggle_new_password" onclick="togglePassword('modal_new_password', 'toggle_new_password')">
                    <label for="toggle_new_password" style="font-size:12px;">Show</label>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password:</label>
                    <input type="password" name="confirm_password" id="modal_confirm_password" class="form-input" autocomplete="new-password">
                    <input type="checkbox" id="toggle_confirm_password" onclick="togglePassword('modal_confirm_password', 'toggle_confirm_password')">
                    <label for="toggle_confirm_password" style="font-size:12px;">Show</label>
                </div>
                <div class="form-group">
                    <label class="form-label">Type:</label>
                    <select name="user_type" id="modal_user_type" class="form-input" required>
                        <option value="User">User</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Line:</label>
                    <select name="user_line" id="modal_user_line" class="form-input" required>
                        <option value="N/A">N/A</option>
                        <option value="1">LINE 1</option>
                        <option value="2">LINE 2</option>
                        <option value="3">LINE 3</option>
                        <option value="4">LINE 4</option>
                        <option value="5">LINE 5</option>
                        <option value="6">LINE 6</option>
                        <option value="7">LINE 7</option>
                        <option value="8">LINE 8</option>
                        <option value="9">LINE 9</option>
                        <option value="10">LINE 10</option>
                        <option value="11">LINE 11</option>
                        <option value="12">LINE 12</option>
                        <option value="AV1">AV1</option>
                        <option value="AV2">AV2</option>
                        <option value="RG2">RG2</option>
                        <option value="RG131">RG131</option>
                        <option value="A">LINE A</option>
                        <option value="I">LINE I</option>
                        <option value="O">LINE O</option>
                        <option value="B">LINE B</option>
                        <option value="P">LINE P</option>
                        <option value="J">LINE J</option>
                        <option value="M">LINE M</option>
                        <option value="N">LINE N</option>
                        <option value="R">LINE R</option>
                        <option value="C">LINE C</option>
                        <option value="D">LINE D</option>
                        <option value="F">LINE F</option>
                        <option value="L">LINE L</option>
                        <option value="K">LINE K</option>
                        <option value="Q">LINE Q</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Section:</label>
                    <select name="user_section" id="modal_user_section" class="form-input" required>
                        <option value="IT">IT</option>
                        <option value="QA ENGR">QA ENGR</option>
                        <option value="SMT">SMT</option>
                        <option value="AI">AI</option>
                        <option value="HANDWORK">HANDWORK</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>

<script>

    $(document).ready(()=>{

        $.fn.dataTable.moment('MMMM DD, YYYY hh:mm A');

        $('#userTable').DataTable({
            pageLength: 10,
            ordering: true,
            searching: true,
            lengthChange: false,
            info: false,
            columnDefs: [
                {orderable: false, targets: 9},
                {className: 'dt-head-center', targets: '_all'}
            ],
            order: [7,'desc']
        });
    });

    $('.select2').select2({
        width: '100%',
        placeholder: 'Select an option',
        language: {
            noResults: () => $('<span>No record found</span>')
        },
        escapeMarkup: markup => markup
    });

    function togglePassword(inputId, checkboxId) {
        var input = document.getElementById(inputId);
        var checkbox = document.getElementById(checkboxId);
        input.type = checkbox.checked ? 'text' : 'password';
    }

    $(document).ready(function() {
        $('#modal_new_password, #modal_confirm_password').on('input', function() {
            var newPasswordVal = $('#modal_new_password').val();
            var confirmPasswordVal = $('#modal_confirm_password').val();
            var feedback = $('#password_match_feedback');
            var newPasswordInput = $('#modal_new_password');
            var confirmPasswordInput = $('#modal_confirm_password');
            if (newPasswordVal !== "" && confirmPasswordVal !== "") {
                if (newPasswordVal === confirmPasswordVal) {
                    feedback.css('color', 'green').text('Passwords match!');
                    newPasswordInput.css('border', '2px solid green');
                    confirmPasswordInput.css('border', '2px solid green');
                } else {
                    feedback.css('color', 'red').text('Passwords do not match!');
                    newPasswordInput.css('border', '2px solid red');
                    confirmPasswordInput.css('border', '2px solid red');
                }
            } else {
                feedback.text('');
            }
        });
    });

    function resetTable() {
        document.getElementById('editModal').reset();
    };

    function openEditModal(data) {
        document.getElementById('modal_emp_id').value = data.emp_id || '';
        document.getElementById('modal_user_id').value = data.user_id || '';
        document.getElementById('modal_user_namefl').value = data.user_namefl || '';
        document.getElementById('modal_user_process').value = data.user_process || '';
        document.getElementById('modal_user_username').value = data.user_username || '';
        document.getElementById('modal_user_type').value = data.user_type || '';
        document.getElementById('modal_user_line').value = data.user_line || '';
        document.getElementById('modal_user_section').value = data.user_section || '';
        document.getElementById('editModal').style.display = 'block';
    }



    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    };

    // Handle modal form submission
    document.getElementById('modalEditForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        console.log("Testing");

        $.ajax({
            url: 'useredit.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success === true) {
                    Swal.fire({
                        icon: 'success',
                        title: response.data,
                        text: response.message,
                        toast: true,
                        position: 'top-right',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
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
                    })
                }
                closeModal();
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    });

    function deleteUser(user_id) {
        Swal.fire({
            icon: 'question',
            title: 'Delete User',
            text: 'Are you sure you want to delete this user?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'This action is irreversible. Are you sure you want to delete this user?',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'useredit.php',
                            type: 'POST',
                            data: {
                                action: 'delete',
                                user_id: user_id
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success === true) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'User Data Deleted Successfully',
                                        toast: true,
                                        position: 'top-right',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        toast: true,
                                        position: 'top-right',
                                        timer: 1500,
                                        showConfirmButton: false
                                    })
                                }
                            }
                        });
                    }

                })
            }

        })
    };
</script>