<?php
session_start();
require_once '../pages/camsdatabase.php';
require_once '../pages/cams-sp.php';

$crud = new Crud();

// Return all equipment requests as JSON
if (isset($_GET['getRequests'])) {
    $requests = $crud->getClassroomRequests(); // You create this function
    header('Content-Type: application/json');
    echo json_encode($requests);
    exit;
}

// PROCESS APPROVE / REJECT USING SP
if (isset($_POST['action']) && isset($_POST['ids'])) {

    $ids = $_POST['ids'];

    foreach ($ids as $id) {
        if ($_POST['action'] === "approve") {
            $crud->approveClassroomRequest($id);
        } 
        else if ($_POST['action'] === "reject") {
            $crud->rejectClassroomRequest($id);
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
 <header>

        <div class="topbar">
            <h2 class="system-title">Welcome Admin!</h2>

            <div class="search-field">
                <i class="bi bi-search search-icon"></i>
                <input type="text" placeholder="Search">
            </div>

            <div class="topbar-right">
                <div class="notification-icon">
                    <i class="bi bi-bell-fill notification-icon"></i>
                </div>

                <div class="profile-info">
                    <i class="bi bi-person-circle profile-icon"></i>
                    <div class="profile-text">
                        <p class="profile-name">
                            <?php echo $_SESSION['FirstName'] . " " . $_SESSION['LastName']; ?>
                        </p>
                        <p class="profile-number"> <?php echo $_SESSION['PhoneNumber'] ?></p>
                        <p class="profile-time" id="time"></p>
                    </div>
                </div>

            </div>


        </div>
        </div>
    </header>
    <!--Table goes here -->
    <div class="table-container">
        <table class="requests-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>ID</th>
                    <th>Room Number</th>
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
      
        // Script for real-time day & 12-hour format time
        function updateTimeDay() {
            const now = new Date();

            // Get day
            const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const day = days[now.getDay()];

            // Get hours and minutes
            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';

            // Convert 24-hour to 12-hour format
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            hours = String(hours).padStart(2, '0');

            // Set the text content
            document.getElementById('time').textContent = `${day}, ${hours}:${minutes}:${seconds} ${ampm}`;
        }

        // Update every second
        setInterval(updateTimeDay, 1000);

        // Initial call
        updateTimeDay();

   function loadRequests() {
    fetch("room-req.php?getRequests=1")
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
                    <td>${req.RoomNumber}</td>
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

            fetch("room-req.php", {
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