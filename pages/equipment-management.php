<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




    <link rel="stylesheet" href="../assets/css/equipment-management.css">

    <?php
    include '../includes/admin-sidebar.php';
    ?>

</head>

<body>

    <div class="main-content">

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
                    <tr class="equipment-row">
                        <td>HDMI</td>
                        <td>10</td>
                        <td>3</td>
                        <td>Available</td>
                        <td>
                            <button class="badge bg-edit action-btn">Edit</button>
                            <button class="badge bg-delete action-btn">Delete</button>
                        </td>
                    </tr>
                    <tr class="equipment-row">
                        <td>Viewboard</td>
                        <td>12</td>
                        <td>5</td>
                        <td>Under Maintenance</td>
                        <td>
                            <button class="badge bg-edit action-btn">Edit</button>
                            <button class="badge bg-delete action-btn">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


    <script>
        //SWAL for the equipment
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('.equipment-row');

            rows.forEach(row => {
                row.addEventListener('click', (e) => {
                    if (e.target.tagName === 'BUTTON') return;

                    // Get all cells (td elements) in the clicked row
                    const cells = row.querySelectorAll('td');

                    // Extract equipment data from the table cells(just a placeholder, apply backend logic here)
                    const item = cells[0].innerText; //equipment name
                    const quantity = parseInt(cells[1].innerText); // Total Quantity
                    const status = cells[2].innerText; // Status of Equipment  

                    // -----------------------------
                    // Build the HTML for the unit list dynamically
                    // Each unit will display its label, status, and a dot indicator
                    // -----------------------------

                    let unitListHTML = '';
                    for (let i = 0; i < quantity; i++) {
                        const num = i + 1;
                        const isReserved = num <= 4; // placeholder logic
                        unitListHTML +=
                            '<div class="unit-card ' + (isReserved ? 'reserved' : 'available') + '">' +
                            '<span class="dot"></span>' +
                            '<span class="unit-label">' + item + ' #' + num + '</span>' +
                            '<span class="unit-status">' + (isReserved ? 'Reserved until 3PM' : 'Available') + '</span>' +
                            '</div>';
                    }

                    Swal.fire({
                        width: "650px",
                        heightAuto: false,
                        showConfirmButton: true,
                        showCloseButton: true,
                        closeButtonHtml: '&times;',
                        customClass: {
                            popup: "equip-modal"
                        },

                        html: `

    <div class="equip-header"> 
        <h2 class="equip-title">Equipment Information</h2> 
        <hr class="equip-divider"> 
    </div> 

    <div class="equip-container"> 

    <!-- Left Image --> 

     <div class="equip-image-box" style="margin-top:10px; cursor:pointer;">
                <img id="equip-image-preview" src="https://cdn-icons-png.flaticon.com/512/1048/1048953.png" 
                     class="equip-image" style="width:140px; height:140px; object-fit:cover;">
                <input id="equip-image-upload" type="file" accept="image/*" style="display:none;">
            </div>

        <div class="equip-info">

            <!-- Editable name -->
            <div class="equip-row">
                <p><strong>Unit Name:</strong></p>
                <input id="edit-name" class="equip-input" type="text" value="${item}">
            </div>
            
            <div class="equip-row">
                <p><strong>Total Units:</strong></p>
                    <input id="edit-qty" class="equip-input" type="number" value="${quantity}">
            </div>            

                <div class="equip-summary">
    <div class="summary-row">
        <label>Available:</label>
        <span>3</span>
    </div>
    <div class="summary-row">
        <label>Reserved:</label>
        <span>4</span>
    </div>
</div>

        </div>
    </div>

    <hr class="equip-divider">
        <h3 class="unit-status-title">Unit Status</h3>                    

    <!-- Scrollable Unit List -->
    <div class="unit-list">
        ${unitListHTML}
    </div>


`,
                        didOpen: () => {
                            // This runs after the modal is in the DOM
                            const imagePreview = Swal.getHtmlContainer().querySelector('#equip-image-preview');
                            const imageUpload = Swal.getHtmlContainer().querySelector('#equip-image-upload');

                            imagePreview.addEventListener('click', () => {
                                imageUpload.click(); // open file picker
                            });

                            imageUpload.addEventListener('change', (e) => {
                                const file = e.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        imagePreview.src = e.target.result; // update preview
                                    };
                                    reader.readAsDataURL(file);
                                }
                            });
                        },

                        // should it go here??
                        showCancelButton: true,
                        confirmButtonText: "Save Changes",
                        cancelButtonText: "Cancel",
                        focusConfirm: false,

                        // Function to run when confirm is clicked
                        preConfirm: () => {
                            return {
                                name: document.getElementById("edit-name")?.value ?? "",
                                qty: document.getElementById("edit-qty")?.value ?? "",
                                status: document.getElementById("edit-status")?.value ?? ""
                            }
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            // Log the updated data (placeholder for backend save)
                            console.log("Updated data:", result.value);
                        }
                    });

                });
            });
        });



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