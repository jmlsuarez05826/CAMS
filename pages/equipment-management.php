<?php
session_start();
    require_once '../pages/camsdatabase.php';
    require_once '../pages/cams-sp.php';

    if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
    header("Location: ../pages/login.php");
    exit();
}

if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
    // Not an admin, redirect or show error
    header("Location: ../pages/login.php");
    exit();
}
    

    $crud = new Crud();

    if(isset($_POST['action']) && $_POST['action'] === 'addEquipment') {
        $equipmentname = $_POST['equipmentname'];
        $quantity = $_POST['quantity'];

        try {
            if ($crud->addEquipment($equipmentname, $quantity)) {
                echo "success";
            }
        } catch (PDOException $e) {
            echo "error: " . $e->getMessage();
        }
        exit;
    }

    if(isset($_POST['action']) && $_POST['action'] === 'editEquipment') {
        $equipmentID = $_POST['equipmentID'];
        $equipmentname = $_POST['equipmentname'];
        $quantity = $_POST['quantity'];
    
        try {
            if ($crud->editEquipment($equipmentID, $equipmentname, $quantity)) {
                echo "success";
            }
        } catch (PDOException $e) {
            echo "error: " . $e->getMessage();
        }
        exit;
    }
    
    if(isset($_POST['action']) && $_POST['action'] === 'deleteEquipment') {
        $equipmentID = $_POST['equipmentID'];
     

        try {
            if ($crud->deleteEquipment($equipmentID)) {
                echo "success";
            }
        } catch (PDOException $e) {
            echo "error: " . $e->getMessage();
        }
        exit;
    }

    require_once '../includes/admin-sidebar.php';
    
 

    
    
    $equipments = $crud->getEquipments();
    ?>







<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/room-req.css">
    <link rel="stylesheet" href="../assets/css/equipment-management.css">

   
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
                            <p class="profile-name">Mark Cristopher</p>
                            <p class="profile-number">093480324</p>
                            <div id="time"></div>
                        </div>
                    </div>

                </div>
      

            </div>
        </div>
    </header>

    <!--Table goes here -->
    <div class="content">
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
                    <th>Action</th>
                </tr>
            </thead>


            <tbody>
    <?php foreach ($equipments as $equipment): ?>
        <tr>
            <td><?= htmlspecialchars($equipment['EquipmentName']) ?></td>
            <td><?= htmlspecialchars($equipment['Quantity']) ?></td>


            <td>
    <button class="badge bg-edit edit-equipment-btn"
        data-id="<?= $equipment['EquipmentID'] ?>"
        data-name="<?= htmlspecialchars($equipment['EquipmentName']) ?>"
        data-qty="<?= $equipment['Quantity'] ?>">
        Edit
    </button>
    <button class="badge bg-delete">Delete</button>
</td>
        </tr>
    <?php endforeach; ?>
</tbody>

</div>



    <script>
  // Script for the time in 12-hour format with AM/PM
        function updateTime() {
            const now = new Date();
            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';

            // Convert 24-hour to 12-hour format
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            hours = String(hours).padStart(2, '0');

            document.getElementById('time').textContent = `${hours}:${minutes} ${ampm}`;
        }

        // Update every second
        setInterval(updateTime, 1000);

        // Initial call
        updateTime();



        //Script for the add equipment modal
        document.getElementById('addEquipmentBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Add New Equipment',
                html: `<input type="text" id="equipmentname" class="swal2-input" placeholder="Equipment Name">
             <input type="number" id="equipmentQty" class="swal2-input" placeholder="Quantity">`,
                confirmButtonText: 'Add',
                focusConfirm: false,
                preConfirm: () => {
                    const name = Swal.getPopup().querySelector('#equipmentname').value;
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
        const name = result.value.name;
        const qty = result.value.qty;

        fetch('', { // sends data to same PHP file
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'addEquipment',
                equipmentname: name,
                quantity: qty
            })
        })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === 'success') {
                Swal.fire('Added!', 'Equipment successfully added.', 'success')
                .then(() => location.reload()); // refresh table
            } else { 
            }
        })
        .catch(err => Swal.fire('Error', err.message, 'error'));
    }
});
        });


        document.querySelectorAll('.edit-equipment-btn').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const qty = button.getAttribute('data-qty');

        Swal.fire({
            title: 'Edit Equipment',
            html: `
                <input type="text" id="equipmentname" class="swal2-input" placeholder="Equipment Name" value="${name}">
                <input type="number" id="equipmentQty" class="swal2-input" placeholder="Quantity" value="${qty}">
            `,
            confirmButtonText: 'Save',
            focusConfirm: false,
            preConfirm: () => {
                const newName = Swal.getPopup().querySelector('#equipmentname').value.trim();
                const newQty = Swal.getPopup().querySelector('#equipmentQty').value.trim();
                if (!newName || !newQty) {
                    Swal.showValidationMessage('Please enter both fields');
                    return false;
                }
                return { id, newName, newQty };
            }
        }).then(result => {
            if (result.isConfirmed) {
                const data = result.value;

                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'editEquipment',
                        equipmentID: data.id,
                        equipmentname: data.newName,
                        quantity: data.newQty
                    })
                })
                .then(res => res.text())
                .then(response => {
                    if (response.trim() === 'success') {
                        Swal.fire('Updated!', 'Equipment successfully updated.', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', response, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', err.message, 'error'));
            }
        });
    });
});

document.querySelectorAll('.bg-delete').forEach(button => {
    button.addEventListener('click', () => {
        const row = button.closest('tr');
        const equipmentID = row.querySelector('.edit-equipment-btn').dataset.id;

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'deleteEquipment',
                        equipmentID: equipmentID
                    })
                })
                .then(res => res.text())
                .then(data => {
                    if (data.trim() === 'success') {
                        Swal.fire('Deleted!', 'Equipment has been deleted.', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', data, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', err.message, 'error'));
            }
        });
    });
});

    </script>

</body>