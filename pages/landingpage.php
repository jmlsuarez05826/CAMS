<?php
session_start();
require_once '../pages/camsdatabase.php';
require_once '../pages/cams-sp.php';

$database = new Database();
$pdo = $database->getConnection();

$session_id = session_id();
$ip = $_SERVER['REMOTE_ADDR'];

// CALL the stored procedure
$stmt = $pdo->prepare("CALL GetDailyVisit(?, ?)");
$stmt->execute([$session_id, $ip]);

$stmt->closeCursor();


$crud = new Crud();




if (isset($_POST['action']) && $_POST['action'] === 'getSchedules') {
    $roomID = $_POST['roomID'];
    $dayOfWeek = $_POST['dayOfWeek'] ?? null;
    $weekType = $_POST['weekType'];

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
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>CAMS — Classroom Availability Management System</title>

    <!-- Bootstrap icons (used for small icons) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/landing.css">

</head>

<body>

    <!-- HEADER / NAV -->
    <header>
        <div class="logo">
            <img src="../images/BSU_logo (3).webp" alt="logo">
            CAMS
        </div>


        <nav aria-label="Main navigation">
            <ul id="main-menu">
                <li><a href="#home">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#buildings">Buildings</a></li>
                <li><a href="#faqs">FAQs</a></li>
                <li><a href="../pages/aboutus.php">About</a></li>
            </ul>
        </nav>

        <div>
            <a class="login-btn" href="../pages/login.php">LOGIN</a>
            <button class="menu-toggle" aria-label="Open menu" onclick="toggleMobileMenu()">
                <i class="bi bi-list"></i>
            </button>
        </div>

    </header>



    <!-- MAIN -->
    <main>

        <!-- HERO -->
        <section class="hero" id="home" aria-label="Hero">
            <div class="hero-left">
                <h1>Classroom Availability <br /><span style="color:var(--primary)">Management System</span></h1>
                <p>
                    Find available classrooms instantly. CAMS gives students and faculty a clear, minimal, and fast
                    interface for searching rooms, checking live availability, and submitting requests — all in one
                    place.
                </p>

                <div class="cta-row">
                    <a class="btn-ghost" href="#features">Learn More</a>
                </div>
            </div>

            <div class="hero-right">
                <div class="hero-card" id="heroCard">
                    <img src="../images/bsu_front.webp" alt="Campus building">
                    <div class="hero-meta">
                        <div>
                            <div class="meta-title">Classroom Availability Checker</div>
                            <div class="meta-sub"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES (old card style) -->
        <section class="features" id="features" aria-labelledby="featuresTitle">
            <h2 id="featuresTitle">Our Features</h2>

            <div class="feature-grid" role="list">
                <div class="feature-card" role="listitem" tabindex="0" aria-label="Visual Map & Search">
                    <i class="bi bi-geo-alt"></i>
                    <h3>Search & Filter</h3>
                    <p>Easily locate available classrooms across campus.</p>
                </div>

                <div class="feature-card" role="listitem" tabindex="0" aria-label="Real-Time Availability">
                    <i class="bi bi-clock-history"></i>
                    <h3>Real-Time Availability & Requests</h3>
                    <p>Check which rooms are free or occupied instantly, and send requests quickly.</p>
                </div>

                <div class="feature-card" role="listitem" tabindex="0" aria-label="Mobile Notifications">
                    <i class="bi bi-phone"></i>
                    <h3>Live chat</h3>
                    <p>Receive updates anytime</p>
                </div>
            </div>
        </section>

        <!-- BUILDINGS -->
        <section class="buildings" id="buildings">
            <div id="timeDay"></div>
            <div class="table-contents">
                <div id="classrooms" class="tab-content active">
                    <div class="tab-scroll">
                        <div id="all-buildings">
                            <div class="building-grid">
                                <?php foreach ($buildings as $building): ?>
                                    <div class="building-card" data-building="<?= $building['BuildingID'] ?>">
                                        <div class="building">
                                            <?php
                                            $serverPath = __DIR__ . "/../uploads/" . $building['BuildingIMG'];
                                            $bgImage = (!empty($building['BuildingIMG']) && file_exists($serverPath))
                                                ? "../uploads/" . $building['BuildingIMG']
                                                : "../images/bsu_front.webp";
                                            ?>
                                            <img src="<?= $bgImage ?>"
                                                alt="<?= htmlspecialchars($building['BuildingName']) ?>">
                                            <p><?= htmlspecialchars($building['BuildingName']) ?></p>
                                        </div>

                                        <div class="building-block" style="display:none;">
                                            <button class="back-btn" style="display:none;">
                                                <i class="bi bi-arrow-left-short"></i>Back
                                            </button>
                                            <h3><?= htmlspecialchars($building['BuildingName']) ?></h3>

                                            <!-- Floor Container -->
                                            <div class="floor-container">
                                                <?php foreach ($floors as $floor): ?>
                                                    <?php if ($floor['BuildingID'] == $building['BuildingID']): ?>
                                                        <div class="floor" data-floor="<?= $floor['FloorID'] ?>">
                                                            Floor <?= htmlspecialchars($floor['FloorNumber']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <div class="floor-indicator"></div>
                                            </div>

                                            <!-- Room Containers -->
                                            <?php foreach ($floors as $floor): ?>
                                                <?php if ($floor['BuildingID'] == $building['BuildingID']): ?>
                                                    <div class="room-container" data-floor="<?= $floor['FloorID'] ?>"
                                                        style="background-image: url('<?= $bgImage ?>');">
                                                         <?php foreach ($rooms as $room): ?>
                                                        <?php if ($room['FloorID'] == $floor['FloorID']): ?>
                                                           <div class="room-card clickable-room" data-room="<?= $room['RoomID'] ?>">
                                                                <div class="room-label">Room no</div>
                                                                <div class="room-number"><?= htmlspecialchars($room['RoomNumber']) ?></div>
                                                                <hr>
                                                                <div class="room-status">Loading... </div>
                                                            </div>

                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </section>

        <!-- FAQ -->
        <section class="faq" id="faqs" aria-labelledby="faqTitle">
            <h2 id="faqTitle">Frequently Asked Questions</h2>

            <div class="faq-grid" role="list">
                <div class="faq-item" role="listitem">
                    <div class="faq-q">How does CAMS detect room availability?<span class="faq-toggle-icon"><i
                                class="bi bi-chevron-down"></i></span></div>
                    <div class="faq-a">CAMS integrates with the university scheduling system and sensors (if available)
                        to present near real-time availability status.</div>
                </div>

                <div class="faq-item" role="listitem">
                    <div class="faq-q">Do I need to log in to view rooms?<span class="faq-toggle-icon"><i
                                class="bi bi-chevron-down"></i></span></div>
                    <div class="faq-a">Basic search is available publicly, but requesting and booking may require
                        authentication for security and tracking.</div>
                </div>

                <div class="faq-item" role="listitem">
                    <div class="faq-q">Can I request a classroom through CAMS?<span class="faq-toggle-icon"><i
                                class="bi bi-chevron-down"></i></span></div>
                    <div class="faq-a">Yes — after login, users can send booking requests which admins can approve or
                        deny through the system.</div>
                </div>

                <div class="faq-item" role="listitem">
                    <div class="faq-q">Is the system mobile friendly?<span class="faq-toggle-icon"><i
                                class="bi bi-chevron-down"></i></span></div>
                    <div class="faq-a">Yes. CAMS is designed to be responsive and works across phones, tablets, and
                        desktops.</div>
                </div>
            </div>
        </section>

                  <!-- class sched modal -->
                <div class="custom-modal" id="classroomModal">
                    <div class="custom-modal-dialog">
                        <div class="custom-modal-content">
                            <div class="custom-modal-header">
                                <h5 class="custom-modal-title">Classroom Schedule</h5>
                                <button type="button" class="custom-close" id="closeclassroomModal">&times;</button>
                            </div>

                            <div class="custom-modal-body">
                                <form method="post" id="classSchedForm">

                                    <p>Building Name Room No</p>
                                    <div class="Sched-table-wrapper">
                                        <table class="classSchedTable">
                                            <thead>
                                                <tr>
                                                    <th>Instructor</th>
                                                    <th>Subject</th>
                                                    <th>Time</th>
                                                    <th>Section</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>

                                    <hr class="table-separator">

                                    <div class="custom-modal-footer">
                                        <button type="button" class="btn-close-modal" id="closeAddUserFooter">Close</button>
                                        
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>




    </main>

    <footer>
        <div>© 2025 Classroom Availability Management System — All rights reserved.</div>
    </footer>
      <script src="../js/landing.js"></script>

</body>

</html>