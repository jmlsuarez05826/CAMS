<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/room-req.css">
    <link rel="stylesheet" href="../assets/css/equipment-management.css">

    <?php
    require_once '../includes/admin-sidebar.php';
    ?>

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
        <!-- Add Equipment Button -->
        <div class="table-header-actions">
            <button class="add-btn" id="addEquipmentBtn">Add Equipment</button>
        </div>

        <table class="requests-table">
            <thead>
                <tr>
                    <th>Equipment Name</th>
                    <th>Quantity</th>
                    <th>In Use</th>
                    <th class="dropdown-header">
                        Status
                        <span class="dropdown-icon">▼</span>
                        <ul class="dropdown-menu">
                            <li onclick="filterStatus('Available')">Available</li>
                            <li onclick="filterStatus('Unavailable')">Unavailable</li>
                            <li onclick="filterStatus('Under Maintenance')">Under Maintenance</li>
                        </ul>
                    </th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>HDMI</td>
                    <td>10</td>
                    <td>3</td>
                    <td>Available</td>
                    <td>
                        <button class="badge bg-edit">Edit</button>
                        <button class="badge bg-delete">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>Viewboard</td>
                    <td>12</td>
                    <td>5</td>
                    <td>Under Maintenance</td>
                    <td>
                        <button class="badge bg-edit">Edit</button>
                        <button class="badge bg-delete">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
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


        const dropdownHeader = document.querySelector('.dropdown-header');

        dropdownHeader.addEventListener('click', () => {
            dropdownHeader.classList.toggle('active');
        });

        // Optional: function to filter or set status
        function filterStatus(status) {
            const rows = document.querySelectorAll('.requests-table tbody tr');
            rows.forEach(row => {
                row.cells[3].textContent = status; // update the 4th column (Status)
            });
            dropdownHeader.classList.remove('active');
        }

        //Script for the add equipment modal
        document.getElementById('addEquipmentBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Add New Equipment',
                html: `<input type="text" id="equipmentName" class="swal2-input" placeholder="Equipment Name">
             <input type="number" id="equipmentQty" class="swal2-input" placeholder="Quantity">`,
                confirmButtonText: 'Add',
                focusConfirm: false,
                preConfirm: () => {
                    const name = Swal.getPopup().querySelector('#equipmentName').value;
                    const qty = Swal.getPopup().querySelector('#equipmentQty').value;
                    if (!name || !qty) {
                        Swal.showValidationMessage(`Please enter both fields`);
                    }
                    return {
                        name: name,
                        qty: qty
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Equipment Name:', result.value.name);
                    console.log('Quantity:', result.value.qty);

                    // Here you can add the logic to actually insert it into the table
                    const table = document.querySelector('.requests-table tbody');
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>${result.value.name}</td>
                <td>${result.value.qty}</td>
                <td>0</td>
                <td>Available</td>
                <td>
                    <button class="badge bg-edit">Edit</button>
                    <button class="badge bg-delete">Delete</button>
                </td>
            `;
                    table.appendChild(row);
                }
            });
        });
    </script>

</body>