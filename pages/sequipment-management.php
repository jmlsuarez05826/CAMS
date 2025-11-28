<?php

session_start();
require_once '../pages/camsdatabase.php';
require_once '../pages/cams-sp.php';


if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
    header("Location: ../pages/login.php");
    exit();
}

if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: ../pages/login.php");
    exit();
}


$crud = new Crud();

if (isset($_GET['equipmentID'])) {
    $equipmentID = (int) $_GET['equipmentID'];
    $units = $crud->getEquipmentUnits($equipmentID);
    header('Content-Type: application/json');
    echo json_encode($units);
    exit;
}


// CRUD operations
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    try {
        if ($action === 'addEquipment') {
            $name = $_POST['equipmentname'];
            $qty = $_POST['quantity'];
            if ($crud->addEquipment($name, $qty))
                echo 'success';
        } elseif ($action === 'editEquipment') {
            $id = $_POST['equipmentID'];
            $name = $_POST['equipmentname'];
            $qty = $_POST['quantity'];
            $imagePath = null;

            // Handle file upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                // Server path for upload
                $uploadDir = __DIR__ . '/../uploads/equipments/';
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);

                // File name
                $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES['image']['name']));

                $fullPath = $uploadDir . $fileName;

                // Move file
                if (move_uploaded_file($_FILES['image']['tmp_name'], $fullPath)) {
                    // Browser-accessible path
                    $imagePath = $fileName; // just filename
                }

            }

            // Only update image if a new file was uploaded
            if ($crud->editEquipment($id, $name, $qty, $imagePath)) {
                echo 'success';
            }
        } elseif ($action === 'deleteEquipment') {
            $id = $_POST['equipmentID'];
            if ($crud->deleteEquipment($id))
                echo 'success';
        }
    } catch (PDOException $e) {
        echo 'error: ' . $e->getMessage();
    }
    exit;
}

$firstname = $_SESSION['FirstName'] ?? null;
$lastname = $_SESSION['LastName'] ?? null;
$number = $_SESSION['PhoneNumber'] ?? null;
$user_id = $_SESSION['UserID'] ?? null;
$role = $_SESSION['Role'] ?? null;

require_once '../includes/sadmin-sidebar.php';
$equipments = $crud->getEquipments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Management</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/equipment-management.css">
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
    <div class="main-content">

        <div class="table-container">
            <div class="table-header-actions">
                <button class="add-btn" id="addEquipmentBtn">Add Equipment</button>
            </div>
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>Equipment Name</th>
                        <th>Quantity</th>
                        <th>In Use</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipments as $eq): ?>
                        <tr class="equipment-row" data-id="<?= $eq['EquipmentID'] ?>"
                            data-image="<?= htmlspecialchars($eq['EquipmentIMG'] ? '../uploads/equipments/' . $eq['EquipmentIMG'] : '../uploads/equipments/default.png') ?>">


                            <td><?= htmlspecialchars($eq['EquipmentName']) ?></td>
                            <td><?= htmlspecialchars($eq['Quantity']) ?></td>
                            <td>0</td>
                            <td>
                                <button class="badge bg-delete action-btn">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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



        // Add Equipment
        document.getElementById('addEquipmentBtn').addEventListener('click', () => {
            Swal.fire({
                title: 'Add New Equipment',
                html: `<input type="text" id="equipmentname" class="swal2-input" placeholder="Equipment Name">
              <input type="number" id="equipmentQty" class="swal2-input" placeholder="Quantity">`,
                confirmButtonText: 'Add',
                preConfirm: () => {
                    const name = Swal.getPopup().querySelector('#equipmentname').value;
                    const qty = Swal.getPopup().querySelector('#equipmentQty').value;
                    if (!name || !qty) Swal.showValidationMessage('Please enter both fields');
                    return { name, qty };
                }
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            action: 'addEquipment',
                            equipmentname: result.value.name,
                            quantity: result.value.qty
                        })
                    })
                        .then(res => res.text())
                        .then(data => {
                            if (data.trim() === 'success') {
                                Swal.fire('Added!', 'Equipment successfully added.', 'success')
                                    .then(() => location.reload()); // reload after user clicks OK
                            } else {
                                Swal.fire('Error', data, 'error');
                            }
                        })
                        .catch(err => Swal.fire('Error', err.message, 'error'));
                }
            });
        });


        document.querySelectorAll('.equipment-row').forEach(row => {
            const id = row.dataset.id;

            // Row click to open full equipment modal
            row.addEventListener('click', (e) => {
                if (e.target.tagName === 'BUTTON') return;

                const id = row.dataset.id;
                const equipmentName = row.cells[0].innerText;
                const totalQty = row.cells[1].innerText;

                // Fetch units from backend
                fetch(`sequipment-management.php?equipmentID=${id}`)
                    .then(res => res.json())
                    .then(units => {

                        // Build unit cards
                        let unitListHTML = '';
                        units.forEach(u => {
                            unitListHTML += `
                    <div class="unit-card ${u.Status.toLowerCase().replace(' ', '-')}">
                        <span class="dot"></span>
                        <span class="unit-label">${equipmentName} #${u.UnitNumber}</span>
                        <span class="unit-status">${u.Status}</span>
                    </div>
                `;
                        });

                        // Open SWAL modal
                        Swal.fire({
                            width: "650px",
                            heightAuto: false,
                            showConfirmButton: true,
                            showCloseButton: true,
                            closeButtonHtml: '&times;',
                            customClass: { popup: "equip-modal" },

                            html: `
<div class="equip-header"> 
    <h2 class="equip-title">Equipment Information</h2> 
    <hr class="equip-divider"> 
</div> 

<div class="equip-container"> 

    <!-- Image upload -->
    <div class="equip-image-box" style="margin-top:10px; cursor:pointer;">
        <img id="equip-image-preview" src="https://cdn-icons-png.flaticon.com/512/1048/1048953.png"
             class="equip-image" style="width:140px; height:140px; object-fit:cover;">
        <input id="equip-image-upload" type="file" accept="image/*" style="display:none;">
    </div>

    <!-- Editable fields -->
    <div class="equip-info">
        <div class="equip-row">
            <p><strong>Equipment Name:</strong></p>
            <input id="edit-name" class="equip-input" type="text" value="${equipmentName}">
        </div>

        <div class="equip-row">
            <p><strong>Total Units:</strong></p>
            <input id="edit-qty" class="equip-input" type="number" value="${totalQty}">
        </div>

        <div class="equip-summary">
            <div class="summary-row">
                <label>Available:</label>
                <span>${units.filter(u => u.Status === "Available").length}</span>
            </div>
            <div class="summary-row">
                <label>Reserved:</label>
                <span>${units.filter(u => u.Status !== "Available").length}</span>
            </div>
        </div>
    </div>
</div>

<hr class="equip-divider">
<h3 class="unit-status-title">Unit Status</h3>

<div class="unit-list">
    ${unitListHTML}
</div>
`,

                            didOpen: () => {
                                const img = Swal.getHtmlContainer().querySelector('#equip-image-preview');
                                const upload = Swal.getHtmlContainer().querySelector('#equip-image-upload');

                                // Set current image from data attribute
                                img.src = row.dataset.image || 'https://cdn-icons-png.flaticon.com/512/1048/1048953.png';


                                img.addEventListener('click', () => upload.click());

                                upload.addEventListener('change', e => {
                                    const file = e.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = ev => img.src = ev.target.result;
                                        reader.readAsDataURL(file);
                                    }
                                });
                            },

                            showCancelButton: true,
                            confirmButtonText: "Save Changes",
                            cancelButtonText: "Cancel",

                            preConfirm: () => ({
                                name: document.getElementById("edit-name").value,
                                qty: document.getElementById("edit-qty").value
                            })
                        }).then(result => {
                            if (result.isConfirmed) {

                                // CALL editEquipment backend
                                const formData = new FormData();
                                formData.append('action', 'editEquipment');
                                formData.append('equipmentID', id);
                                formData.append('equipmentname', result.value.name);
                                formData.append('quantity', result.value.qty);

                                // ADD IMAGE
                                let imageFile = document.getElementById("equip-image-upload").files[0];
                                if (imageFile) {
                                    formData.append('image', imageFile);
                                }

                                fetch('', {
                                    method: 'POST',
                                    body: formData
                                })

                                    .then(res => res.text())
                                    .then(data => {
                                        if (data.trim() === 'success') {
                                            Swal.fire('Updated!', 'Equipment updated.', 'success')
                                                .then(() => location.reload());
                                        } else {
                                            Swal.fire('Error', data, 'error');
                                        }
                                    });
                            }
                        });
                    })
                    .catch(err => Swal.fire('Error', err.message, 'error'));
            });

        });

        // DELETE EQUIPMENT
        document.querySelectorAll('.bg-delete').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation(); // prevent row click from opening modal

                const row = this.closest('.equipment-row');
                const id = row.dataset.id;

                Swal.fire({
                    title: "Are you sure?",
                    text: "This equipment and all its units will be deleted.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Delete",
                    cancelButtonText: "Cancel"
                }).then(result => {
                    if (result.isConfirmed) {

                        fetch('', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams({
                                action: 'deleteEquipment',
                                equipmentID: id
                            })
                        })
                            .then(res => res.text())
                            .then(data => {
                                if (data.trim() === 'success') {
                                    Swal.fire("Deleted!", "Equipment removed.", "success")
                                        .then(() => row.remove());
                                } else {
                                    Swal.fire("Error", data, "error");
                                }
                            });
                    }
                });
            });
        });

    </script>
</body>

</html>