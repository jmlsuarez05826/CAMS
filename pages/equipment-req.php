<?php
require_once '../pages/camsdatabase.php';
require_once '../pages/cams-sp.php';

$crud = new Crud();

// Return all equipment requests as JSON
if (isset($_GET['getRequests'])) {
    $requests = $crud->getEquipmentRequests(); // You create this function
    header('Content-Type: application/json');
    echo json_encode($requests);
    exit;
}

// PROCESS APPROVE / REJECT USING SP
if (isset($_POST['action']) && isset($_POST['ids'])) {

    $ids = $_POST['ids'];

    foreach ($ids as $id) {
        if ($_POST['action'] === "approve") {
            $crud->approveEquipmentRequest($id);
        } 
        else if ($_POST['action'] === "reject") {
            $crud->rejectEquipmentRequest($id);
        }
    }

    echo json_encode(["success" => true]);
    exit;
}

require_once '../includes/admin-sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>


    <link rel="stylesheet" href="../assets/css/room-req.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



</head>

<body>



    <div class="topbar">
        <h2>Welcome Admin!</h2>

        <div class="topbar-right">
            <div class="search-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" placeholder="Search" class="search-field">
                <div class="notification-wrapper">
                    <i class="bi bi-bell-fill notification-icon"></i>
                </div>
            </div>
            <div id="time"></div>
        </div>

    </div>

    <!--Table goes here -->
    <div class="table-container">
        <table class="requests-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>ID</th>
                    <th>Equipment</th>
                    <th>Requester</th>
                    <th>Req Date</th>
                    <th>Time</th>
                    <th>Submitted</th>
                    <th>Status</th>
                </tr>
            </thead>
           <tbody id="requestTableBody"></tbody>

        </table>
    </div>


    <!-- Footer Action Bar -->
    <div id="actionFooter" class="action-footer hidden">
        <div class="footer-content">
            <span id="selectedCount">0 selected out of 0</span>
            <div class="action-buttons">
                <button class="btn btn-success btn-sm">Approve</button>
                <button class="btn btn-danger btn-sm">Reject</button>
            </div>
        </div>
    </div>




    <script>
        //script for the time
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('time').textContent = `${hours}:${minutes}:${seconds}`;
        }

        // Update every second
        setInterval(updateTime, 1000);

        // Initial call
        updateTime();

   function loadRequests() {
    fetch("equipment-req.php?getRequests=1")
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById("requestTableBody");
            tbody.innerHTML = "";

            data.forEach(req => {
                const statusClass = 
                    req.Status === "Approved" ? "badge bg-success" :
                    req.Status === "Rejected" ? "badge bg-danger" :
                    "badge bg-warning text-dark";

                const row = `
                <tr>
                    <td><input type="checkbox" class="rowCheck"></td>
                    <td>${req.ReservationID}</td>
                    <td>${req.EquipmentName}</td>
                    <td>${req.Requester}</td>
                    <td>${req.ReservationDate}</td>
                    <td>${req.Time}</td>
                    <td>${req.CreatedAt}</td>
                    <td><span class="${statusClass}">${req.Status}</span></td>
                </tr>`;

                tbody.innerHTML += row;
            });

            refreshCheckboxLogic();
        });
}

document.addEventListener("DOMContentLoaded", loadRequests);

       function refreshCheckboxLogic() {
    const checkboxes = document.querySelectorAll('.rowCheck');
    const footer = document.getElementById("actionFooter");
    const countText = document.getElementById("selectedCount");

    const totalRows = checkboxes.length;

    function updateFooter() {
        const selected = document.querySelectorAll('.rowCheck:checked').length;

        if (selected > 0) {
            countText.textContent = `${selected} selected out of ${totalRows}`;
            footer.classList.add("show");
        } else {
            footer.classList.remove("show");
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener("change", updateFooter);
    });

    updateFooter();
}


     
function getSelectedRows() {
    const ids = [];
    document.querySelectorAll("#requestTableBody tr").forEach(row => {
        if (row.querySelector(".rowCheck").checked) {
            ids.push(row.children[1].textContent);
        }
    });
    return ids;
}

document.addEventListener("DOMContentLoaded", () => {

    document.querySelector(".btn-success").addEventListener("click", () => {
        processAction("approve");
    });

    document.querySelector(".btn-danger").addEventListener("click", () => {
        processAction("reject");
    });

});

function processAction(action) {
    const ids = getSelectedRows();

    if (ids.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No selection',
            text: 'Please select at least one request.'
        });
        return;
    }

    // Confirmation popup
    Swal.fire({
        title: `Are you sure you want to ${action} ${ids.length} request(s)?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: action === 'approve' ? 'Yes, Approve' : 'Yes, Reject',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with action
            const formData = new FormData();
            formData.append('action', action);
            ids.forEach(id => formData.append('ids[]', id));

            fetch("equipment-req.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadRequests(); // refresh table
                    Swal.fire({
                        icon: 'success',
                        title: action === 'approve' ? 'Approved!' : 'Rejected!',
                        text: `${ids.length} request(s) ${action === 'approve' ? 'approved' : 'rejected'} successfully!`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Cannot connect to server.'
                });
            });
        }
    });
}



    </script>

</body>