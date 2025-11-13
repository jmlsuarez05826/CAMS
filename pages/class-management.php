<?php
require_once '../pages/camsdatabase.php';
require_once '../pages/cams-sp.php';


$crud = new Crud();

if (isset($_POST['action']) && $_POST['action'] === 'addRoom') {
    $floorID = $_POST['floorID'];
    $roomnumber = $_POST['roomNumber'];

    try {
        if ($crud->addRoom($floorID, $roomnumber)) {
            echo "success";
        }
    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'addFloor') {
    $buildingID = $_POST['buildingID'];
    $floornumber = $_POST['floorNumber'];

    try {
        if ($crud->addFloor($buildingID, $floornumber)) {
            echo "success";
        }
    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'addBuilding') {
    $buildingName = $_POST['buildingName'];
    $buildingImage = null;

    if (isset($_FILES['buildingImage']) && $_FILES['buildingImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = uniqid() . '_' . basename($_FILES['buildingImage']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['buildingImage']['tmp_name'], $targetPath)) {
            $buildingImage = $fileName;
        }
    }

    try {
        if ($crud->addBuilding($buildingName, $buildingImage)) {
            // Return JSON instead of plain text
            echo json_encode([
                'status' => 'success',
                'buildingName' => $buildingName,
                'buildingIMG' => $buildingImage // filename only
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add building']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'editBuilding') {
    $buildingID = $_POST['buildingID'];
    $buildingName = $_POST['buildingName'];
    $buildingImage = null;

    if (isset($_FILES['buildingImage']) && $_FILES['buildingImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = uniqid() . '_' . basename($_FILES['buildingImage']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['buildingImage']['tmp_name'], $targetPath)) {
            $buildingImage = $fileName;
        }
    }

    try {
        if ($crud->editBuilding($buildingID, $buildingName, $buildingImage)) {
            echo json_encode([
                'status' => 'success',
                'buildingName' => $buildingName,
                'buildingIMG' => $buildingImage
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update building']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}



require_once '../includes/admin-sidebar.php';

$buildings = $crud->getBuildings(); 
$floors = $crud->getFloors();
$rooms = $crud->getRooms();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../assets/css/class-management.css">
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


    <?php foreach ($buildings as $index => $building): ?>
       <div class="building-title">
    <h3><?= htmlspecialchars($building['BuildingName']) ?></h3>

    <!-- Edit button for every building -->
    <button class="editBuilding-btn" 
        data-id="<?= $building['BuildingID'] ?>" 
        data-name="<?= htmlspecialchars($building['BuildingName']) ?>" 
        data-image="<?= htmlspecialchars($building['BuildingIMG']) ?>">
        Edit
    </button>

    <!-- Add Building button only for the first building -->
    <?php if ($index === 0): ?>
        <button class="addBuilding-btn">+ Add Building</button>
    <?php endif; ?>
</div>


        <div class="building-block">

            <!-- Floor Container -->
            <div class="floor-container">
                <?php foreach ($floors as $floor): ?>
                    <?php if ($floor['BuildingID'] == $building['BuildingID']): ?>
                        <div class="floor" data-floor="<?= htmlspecialchars($floor['FloorID']) ?>">
                            Floor <?= htmlspecialchars($floor['FloorNumber']) ?>
                        </div>

                    <?php endif; ?>
                <?php endforeach; ?>

                <button class="add-floor" data-building="<?= $building['BuildingID'] ?>">+ Add Floor</button>
                <div class="floor-indicator"></div>
            </div>



            <!-- Room Containers for each floor -->
            <?php foreach ($floors as $floor): ?>
                <?php if ($floor['BuildingID'] == $building['BuildingID']): ?>

                    <?php 
  $bgImage = !empty($building['BuildingIMG']) ? "../uploads/" . htmlspecialchars($building['BuildingIMG']) : "../../images/bsu_front.webp"; 
?>
<div class="room-container" data-floor="<?= htmlspecialchars($floor['FloorID']) ?>" style="display:none; background-image: url('<?= $bgImage ?>');">


                        <!-- Add Room button -->
                        <div class="room-card add-room-btn" data-floor="<?= $floor['FloorID'] ?>">
                            <i class="bi bi-door-open"></i>
                            <span>Add Room (Floor <?= htmlspecialchars($floor['FloorNumber']) ?>)</span>
                        </div>

                        <?php foreach ($rooms as $room): ?>
                            <?php if ($room['FloorID'] == $floor['FloorID']): ?>
                                <div class="room-card">
                                    <div class="room-label">Room no</div>
                                    <div class="room-number"><?= htmlspecialchars($room['RoomNumber']) ?></div>
                                    <hr>
                                    <div class="room-status">Available</div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>


    <script>
        //Script for the add room button
        document.querySelectorAll(".add-room-btn").forEach(button => {
            button.addEventListener("click", () => {
                const floorID = button.getAttribute("data-floor");

                Swal.fire({
                    title: "Add Room",
                    html: `
        <input type="number" id="roomNumber" class="swal2-input" placeholder="Enter Room Number" required>
      `,
                    confirmButtonText: "Add",
                    showCancelButton: true,
                    cancelButtonText: "Cancel",
                    preConfirm: () => {
                        const roomNumber = Swal.getPopup().querySelector("#roomNumber").value.trim();
                        if (!roomNumber) {
                            Swal.showValidationMessage("Please enter a room number");
                            return false;
                        }
                        return {
                            floorID,
                            roomNumber
                        };
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        const data = result.value;

                        // AJAX request to same file
                        fetch("", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: `action=addRoom&floorID=${encodeURIComponent(data.floorID)}&roomNumber=${encodeURIComponent(data.roomNumber)}`
                            })
                            .then(response => response.text())
                            .then(res => {
                                if (res.trim() === "success") {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Room added successfully!",
                                        confirmButtonText: "OK"
                                    }).then(() => window.location.reload());
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Failed to add room",
                                        text: res
                                    });
                                }
                            })
                            .catch(err => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: err
                                });
                            });
                    }
                });
            });
        });

        // Script for an interactive floor indicator
        document.querySelectorAll('.building-block').forEach(building => {
            const floors = building.querySelectorAll('.floor');
            const indicator = building.querySelector('.floor-indicator');

            //Default active floor 
            if (floors.length > 0) {
                const defaultFloor = floors[0];
                defaultFloor.classList.add('active');

                const position = defaultFloor.offsetLeft;
                const width = defaultFloor.offsetWidth;

                indicator.style.left = position + "px";
                indicator.style.width = width + "px";
            }

            // Handle user clicks
            floors.forEach((floor) => {
                floor.addEventListener('click', () => {
                    floors.forEach(f => f.classList.remove('active'));
                    floor.classList.add('active');

                    const position = floor.offsetLeft;
                    const width = floor.offsetWidth;

                    indicator.style.left = position + "px";
                    indicator.style.width = width + "px";
                });
            });
        });


        //SWAL for the add building button
document.querySelectorAll('.addBuilding-btn').forEach(button => {
    button.addEventListener('click', () => {
        Swal.fire({
            title: 'Add Building',
            html: `
                <input type="text" id="buildingName" class="swal2-input" placeholder="Building Name">
                <input type="file" id="buildingImage" class="swal2-input" accept="image/*" style="flex:1;">
            `,
            showCancelButton: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Close',
            focusConfirm: false,
            preConfirm: () => {
                const name = Swal.getPopup().querySelector('#buildingName').value;
                if (!name) {
                    Swal.showValidationMessage('Please enter a building name');
                }
                return name;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const buildingName = result.value;
                const fileInput = document.getElementById("buildingImage");
                const file = fileInput.files[0];

                const formData = new FormData();
                formData.append("action", "addBuilding");
                formData.append("buildingName", buildingName);
                if (file) formData.append("buildingImage", file);

                fetch("", {
    method: "POST",
    body: formData
})
.then(response => response.json()) // parse JSON
.then(res => {
    if (res.status === "success") {
        Swal.fire({
            icon: "success",
            title: "Building added successfully!",
            confirmButtonText: "OK"
        }).then(() => window.location.reload());
    } else {
        Swal.fire({
            icon: "error",
            title: "Failed to add building",
            text: res.message
        });
    }
})
.catch(err => {
    Swal.fire({
        icon: "error",
        title: "Error",
        text: err
    });
});

            }
        });
    });
});




        // Script for the time
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

        // SWAL for the Add Floor button
        document.querySelectorAll(".add-floor").forEach(button => {
            button.addEventListener("click", () => {
                const buildingID = button.getAttribute("data-building");

                Swal.fire({
                    title: "Add Floor",
                    html: `<input type="number" id="floorNumber" class="swal2-input" placeholder="Enter Floor Number" required>`,
                    confirmButtonText: "Add Floor",
                    showCancelButton: true,
                    cancelButtonText: "Cancel",
                    preConfirm: () => {
                        const floorNumber = Swal.getPopup().querySelector("#floorNumber").value.trim();
                        if (!floorNumber) {
                            Swal.showValidationMessage("Please enter a floor number");
                            return false;
                        }
                        return {
                            buildingID,
                            floorNumber
                        };
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        const data = result.value;

                        fetch("", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: `action=addFloor&buildingID=${encodeURIComponent(data.buildingID)}&floorNumber=${encodeURIComponent(data.floorNumber)}`
                            })
                            .then(response => response.text())
                            .then(res => {
                                if (res.trim() === "success") {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Floor added successfully!",
                                        confirmButtonText: "OK"
                                    }).then(() => window.location.reload());
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Failed to add floor",
                                        text: res
                                    });
                                }
                            })
                            .catch(err => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: err
                                });
                            });
                    }
                });
            });
        });

        document.querySelectorAll('.building-block').forEach(buildingBlock => {
            const floors = buildingBlock.querySelectorAll('.floor');
            const roomContainers = buildingBlock.querySelectorAll('.room-container');

            floors.forEach(floor => {
                floor.addEventListener('click', () => {
                    const floorID = floor.getAttribute('data-floor');

                    // Hide all room containers in this building
                    roomContainers.forEach(container => container.style.display = 'none');

                    // Show the selected floor's room container
                    const target = buildingBlock.querySelector(`.room-container[data-floor="${floorID}"]`);
                    if (target) target.style.display = 'flex'; // or 'block' depending on your layout
                });
            });

            // Optionally show the first floor by default
            if (floors.length > 0) {
                const firstFloorID = floors[0].getAttribute('data-floor');
                const firstContainer = buildingBlock.querySelector(`.room-container[data-floor="${firstFloorID}"]`);
                if (firstContainer) firstContainer.style.display = 'flex';
                floors[0].classList.add('active');
            }
        });

        document.querySelectorAll('.editBuilding-btn').forEach(button => {
    button.addEventListener('click', () => {
        const buildingID = button.getAttribute('data-id');
        const buildingName = button.getAttribute('data-name');
        const currentImage = button.getAttribute('data-image');

        Swal.fire({
            title: 'Edit Building',
            html: `
                <input type="text" id="buildingName" class="swal2-input" placeholder="Building Name" value="${buildingName}">
                <input type="file" id="buildingImage" class="swal2-input" accept="image/*" style="flex:1;">
            `,
            showCancelButton: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Close',
            focusConfirm: false,
            preConfirm: () => {
                const name = Swal.getPopup().querySelector('#buildingName').value;
                if (!name) {
                    Swal.showValidationMessage('Please enter a building name');
                }
                return name;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const name = result.value;
                const fileInput = document.getElementById("buildingImage");
                const file = fileInput.files[0];

                const formData = new FormData();
                formData.append("action", "editBuilding");
                formData.append("buildingID", buildingID);
                formData.append("buildingName", name);
                if (file) formData.append("buildingImage", file);

                fetch("", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    if (res.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Building updated successfully!",
                            confirmButtonText: "OK"
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Failed to update building",
                            text: res.message
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: err
                    });
                });
            }
        });
    });
});

    </script>

</body>

</html>