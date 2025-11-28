<?php
session_start();
if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
    header("Location: ../pages/login.php");
    exit();
}

if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
    // Not an admin, redirect or show error
    header("Location: ../pages/login.php");
    exit();
}
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

    try {
        if ($crud->addFloor($buildingID)) {
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
        if (!is_dir($uploadDir))
            mkdir($uploadDir, 0777, true);

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
        if (!is_dir($uploadDir))
            mkdir($uploadDir, 0777, true);

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


if (isset($_POST['action']) && $_POST['action'] === 'addSchedule') {
    $roomID = $_POST['roomID'];
    $subject = $_POST['subject'];
    $instructor = $_POST['instructor'];
    $timeFrom = $_POST['timeFrom'];
    $timeTo = $_POST['timeTo'];
    $section = $_POST['section'];
    $weekType = $_POST['weekType'];
    $dayOfWeek = $_POST['dayOfWeek'];

    try {
        if ($crud->addSchedule($roomID, $subject, $instructor, $timeTo, $timeFrom, $section, $weekType, $dayOfWeek)) {
            echo "success";
        } else {
            echo "error: failed to insert";
        }
    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
    exit;
}


if (isset($_POST['action']) && $_POST['action'] === 'getSchedules') {
    $roomID = $_POST['roomID'];
    $dayOfWeek = $_POST['dayOfWeek'] ?? null;
    $weekType = $_POST['weekType'] ?? null;

    try {
        $schedules = $crud->getSchedulesByRoom($roomID, $dayOfWeek, $weekType);
        echo json_encode($schedules);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}




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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</head>

<body>
    <?php require_once '../includes/admin-sidebar.php'; ?>
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

    </header>

    <!-- Button for the week identifier -->
    <div class="weekIdentifier">
        <button class="oddWeek-btn">Odd Week</button>
    </div>

    <?php foreach ($buildings as $index => $building): ?>
        <div class="building-title">
            <h3><?= htmlspecialchars($building['BuildingName']) ?></h3>

            <!-- Edit button for every building -->
            <button class="editBuilding-btn" data-id="<?= $building['BuildingID'] ?>"
                data-name="<?= htmlspecialchars($building['BuildingName']) ?>"
                data-image="<?= htmlspecialchars($building['BuildingIMG']) ?>">
                <i class="bi bi-pencil"></i> <!-- Edit Icon -->
            </button>

            <!-- Only show Add Building button on the first building -->
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
                    <div class="room-container" data-floor="<?= htmlspecialchars($floor['FloorID']) ?>"
                        style="display:none; background-image: url('<?= $bgImage ?>');">




                        <!-- Add Room button -->
                        <div class="room-card add-room-btn" data-floor="<?= $floor['FloorID'] ?>">
                            <i class="bi bi-door-open" style="color: #8b1717;"></i>
                            <span style="color: black;">Add Room (Floor <?= htmlspecialchars($floor['FloorNumber']) ?>)</span>
                        </div>

                        <?php foreach ($rooms as $room): ?>
                            <?php if ($room['FloorID'] == $floor['FloorID']): ?>
                                <div class="room-card clickable-room" data-room="<?= $room['RoomID'] ?>">
                                    <div class="room-label">Room no</div>
                                    <div class="room-number"><?= htmlspecialchars($room['RoomNumber']) ?></div>
                                    <hr>
                                    <div class="room-status">Loading...</div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>


    <script src="../js/class-management.js"></script>
</body>

</html>