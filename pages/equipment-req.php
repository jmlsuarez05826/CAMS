<?php
session_start();
require_once '../pages/camsdatabase.php';
require_once '../pages/cams-sp.php';

$crud = new Crud();

// Return all equipment requests as JSON
if (isset($_GET['getRequests'])) {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    $requests = $crud->getEquipmentRequests($limit, $offset);

    $totalRequests = $crud->getEquipmentRequestsCount();
    $totalPages = ceil($totalRequests / $limit);

    echo json_encode([
        'data' => $requests,
        'totalPages' => $totalPages
    ]);
    exit;
}


// PROCESS APPROVE / REJECT USING SP
if (isset($_POST['action']) && isset($_POST['ids'])) {

    $ids = $_POST['ids'];

    foreach ($ids as $id) {
        if ($_POST['action'] === "approve") {
            $crud->approveEquipmentRequest($id);
        } else if ($_POST['action'] === "reject") {
            $crud->rejectEquipmentRequest($id);
        }
    }

    echo json_encode(["success" => true]);
    exit;
}

$firstname = $_SESSION['FirstName'] ?? null;
$lastname = $_SESSION['LastName'] ?? null;
$number = $_SESSION['PhoneNumber'] ?? null;
$user_id = $_SESSION['UserID'] ?? null;
$role = $_SESSION['Role'] ?? null;



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
          <h2 class="system-title">Welcome <?=  $firstname;?>!</h2>

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
    <?php
    $rowsPerPage = 10;
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $rowsPerPage;

    // Fetch only the equipment requests for this page
    $requests = $crud->getEquipmentRequests($rowsPerPage, $offset);

    // Total requests for pagination
    $totalRequests = $crud->getEquipmentRequestsCount();
    $totalPages = ceil($totalRequests / $rowsPerPage);
    ?>


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
                    <th id="sortStatus" style="cursor: pointer;">
                        Status
                        <span id="statusSortIcon">↕</span>
                    </th>

                </tr>
            </thead>
            <tbody id="requestTableBody">
               <?php foreach ($requests as $r): ?>
                    <?php $isDisabled = ($r['Status'] === 'Approved' || $r['Status'] === 'Rejected') ? 'disabled' : ''; ?>
                    <tr>
                        <td><input type="checkbox" class="rowCheck" <?= $isDisabled ?>></td>
                        <td><?= $r['ReservationID'] ?></td>
                        <td><?= $r['EquipmentName'] ?></td>
                        <td><?= $r['Requester'] ?></td>
                        <td><?= $r['ReservationDate'] ?></td>
                        <td><?= ($r['TimeFrom'] && $r['TimeTo']) ? $r['TimeFrom'] . ' - ' . $r['TimeTo'] : '' ?></td>
                        <td><?= $r['CreatedAt'] ?></td>
                        <td>
                            <span class="<?=
                                            $r['Status'] === 'Approved' ? 'badge bg-success' : ($r['Status'] === 'Rejected' ? 'badge bg-danger' : 'badge bg-warning text-dark')
                                            ?>">
                                <?= $r['Status'] ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <nav class="custom-pagination">
            <ul>
                <!-- Previous -->
                <li class="<?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                    <a href="?page=<?= $currentPage - 1 ?>">&laquo;</a>
                </li>

                <!-- Page numbers -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="<?= ($i == $currentPage) ? 'active' : '' ?>">
                        <a href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Next -->
                <li class="<?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                    <a href="?page=<?= $currentPage + 1 ?>">&raquo;</a>
                </li>
            </ul>
        </nav>


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
        const sortStatusHeader = document.getElementById("sortStatus");
        const statusSortIcon = document.getElementById("statusSortIcon");

        let statusSortDirection = "none"; // none → approved_first → pending_first
        let originalRows = null; // store original table

        sortStatusHeader.addEventListener("click", () => {
            const tbody = document.getElementById("requestTableBody");
            let rows = Array.from(tbody.querySelectorAll("tr"));

            // Save original ordering once
            if (!originalRows) {
                originalRows = rows.map(r => r.cloneNode(true));
            }

            // Toggle sort direction
            if (statusSortDirection === "none") {
                statusSortDirection = "approved_first";
                statusSortIcon.textContent = "↑"; // approved first
            } else if (statusSortDirection === "approved_first") {
                statusSortDirection = "pending_first";
                statusSortIcon.textContent = "↓"; // pending first
            } else {
                statusSortDirection = "none";
                statusSortIcon.textContent = "↕"; // reset
            }

            // Reset to original order
            if (statusSortDirection === "none") {
                rows = originalRows.map(r => r.cloneNode(true));
            } else {
                // Sorting logic
                rows.sort((a, b) => {
                    const statusA = a.querySelector("td:last-child span").textContent.trim();
                    const statusB = b.querySelector("td:last-child span").textContent.trim();

                    if (statusSortDirection === "approved_first") {
                        return statusPriority(statusA) - statusPriority(statusB);
                    }

                    if (statusSortDirection === "pending_first") {
                        return statusPriority(statusB) - statusPriority(statusA);
                    }

                    return 0;
                });
            }

            // Clear and re-append rows
            tbody.innerHTML = "";
            rows.forEach(row => tbody.appendChild(row));
        });

        // Helper priority function
        function statusPriority(status) {
            switch (status) {
                case "Approved":
                    return 1;
                case "Pending":
                    return 2;
                case "Rejected":
                    return 3;
                default:
                    return 4;
            }
        }


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


        // Select the "selectAll" checkbox


        const selectAllCheckbox = document.getElementById("selectAll");
        const tbody = document.getElementById("requestTableBody");



        selectAllCheckbox.addEventListener("change", () => {
            let selectedCount = 0;

            tbody.querySelectorAll("tr").forEach(row => {
                const statusSpan = row.querySelector("td:last-child span");
                const checkbox = row.querySelector(".rowCheck");

                // Only select rows with Pending status
                if (statusSpan.textContent.trim() === "Pending") {
                    checkbox.checked = selectAllCheckbox.checked;
                }

                if (checkbox.checked) selectedCount++;
            });

            // Update footer visibility and count
            if (selectedCount > 0) {
                footer.classList.add("show");
                countText.textContent = `${selectedCount} selected`;
            } else {
                footer.classList.remove("show");
            }
        });


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
                        
                        // New: Determine if checkbox should be disabled
                        const isDisabled = (req.Status === "Approved" || req.Status === "Rejected") ? 'disabled' : '';

                        const row = `
                <tr>
                    <td><input type="checkbox" class="rowCheck" ${isDisabled}></td> 
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

                    //refreshCheckboxLogic();
                });
        }



        // Get footer and count text
        const footer = document.getElementById("actionFooter");
        const countText = document.getElementById("selectedCount");

        // Add event listener to the table body
        document.querySelector("#requestTableBody").addEventListener("change", (e) => {
            if (e.target.classList.contains("rowCheck")) {
                // Check how many are selected
                const selected = document.querySelectorAll(".rowCheck:checked").length;

                if (selected > 0) {
                    countText.textContent = `${selected} selected`;
                    footer.classList.add("show");
                } else {
                    footer.classList.remove("show");
                }
            }
        });



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