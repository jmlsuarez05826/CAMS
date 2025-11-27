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
    $weekType = 'Odd'; // force week type to Odd

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

    <style>
        :root {
            --primary: #8b1717;
            --primary-strong: #6f1313;
            --white: #ffffff;
            --muted: #6b6b6b;
            --card-radius: 20px;
        }

        * {
            box-sizing: content-box;
            margin: 0;
            scrollbar-color: #8B1717 #d9d9d9;
            /* thumb | track */
            scrollbar-width: thin;
            scroll-behavior: smooth;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            color: #111;

        }


        header {
            position: fixed;
            /* keep only this */
            left: 0;
            right: 0;
            top: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 14px 5%;
            background: rgba(255, 255, 255, 1);
            backdrop-filter: blur(8px) saturate(120%);
            border-bottom: 2px solid #8b171741;
            font-family: 'Poppins';
            margin-bottom: 5em;
            z-index: 1200;
        }

        header.scrolled {
            background: rgba(255, 255, 255, 0.95);
            border-bottom: none;
        }


        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 18px;
            color: var(--primary);
            font-family: 'Poppins';
        }

        .logo img {
            height: 42px;
            width: 42px;
            object-fit: contain;
            border-radius: 8px
        }

        nav ul {
            display: flex;
            gap: 28px;
            list-style: none;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        nav a {
            text-decoration: none;
            color: inherit;
            font-weight: 500;
        }

        .login-btn {
            background: var(--primary);
            color: white;
            padding: 9px 30px;
            border-radius: 10px;
            display: inline-block;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            font-family: 'Poppins';
            transition: transform .18s, background .18s, opacity .18s;
        }

        .login-btn:hover {
            transform: scale(1.03);
            background: var(--primary-strong)
        }

        /* hamburger on small screens */
        .menu-toggle {
            display: none;
            background: none;
            border: 0;
            font-size: 20px;
            cursor: pointer
        }

        /* ---------- HERO ---------- */


        /* space for fixed header */

        .hero {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 36px;
            align-items: center;
            padding: 72px 6% 48px;
            min-height: 64vh;
            margin-bottom: 8em;
            margin-top: 0em;
            position: relative;
            background-color: #ffffffff;
            overflow: hidden;
        }



        .hero-left h1 {
            font-size: clamp(1.6rem, 3.6vw, 3.2rem);
            line-height: 1.03;
            margin: 0 0 12px;
            color: #111;
            font-family: 'Poppins';
        }

        .hero-left p {
            margin: 0;
            color: var(--muted);
            max-width: 48ch;
            font-size: 1rem;
        }

        .cta-row {
            margin-top: 26px;
            display: flex;
            gap: 14px;
            align-items: center
        }

        .btn-ghost {
            border: 1px solid rgba(0, 0, 0, 0.08);
            padding: 10px 16px;
            border-radius: 12px;
            background: #ecebebff;
            color: #111;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-ghost:hover {
            transform: scale(1.03);
            border-color: var(--primary)
        }


        .btn-primary {
            background: var(--primary);
            color: #fff;
            padding: 10px 18px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
        }

        .hero-right {
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1200px;
        }

        .hero-card {
            width: 100%;
            max-width: 880px;
            height: 450px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(15, 15, 15, 0.08);
            transform-style: preserve-3d;
            transition: transform .6s cubic-bezier(.2, .9, .25, 1), box-shadow .3s;
            border: 1px solid rgba(0, 0, 0, 0.06);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(250, 250, 250, 0.95));
        }

        .hero-card img {
            width: 100%;
            height: 360px;
            object-fit: cover;
            display: block;
        }

        .hero-meta {
            padding: 20px 26px;
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .hero-meta .meta-title {
            font-weight: 700;
            font-size: 1.05rem;
            color: #111
        }

        .hero-meta .meta-sub {
            color: var(--muted);
            font-size: 0.92rem
        }



        /* ---------- FEATURES (old card style restored) ---------- */
        .features {
            padding: 48px 6%;
                background-color: #fafafa;
        }

        .features h2 {
            text-align: center;
            font-size: 2.2rem;
            margin: 6px 0 18px;
            color: var(--primary);
            font-weight: 700;
            font-family: 'Poppins';
        }

        .feature-grid {
            display: flex;
            gap: 32px;
            justify-content: center;
            align-items: stretch;
            margin-top: 28px;
            flex-wrap: wrap;
        }

        .feature-card {
            width: 320px;
            min-height: 220px;
            background: #fff;
            border-radius: var(--card-radius);
            border: 2px solid #000;
            padding: 22px;
            box-shadow: none;
            transition: transform .22s ease, border-color .22s;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary)
        }

        .feature-card i {
            font-size: 28px;
            color: var(--primary)
        }

        .feature-card h3 {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 700
        }

        .feature-card p {
            margin: 0;
            color: var(--muted)
        }

        /* ---------------- BUILDINGS ---------------- */

.building-title {
    text-align: center;
    margin-top: 120px;
    font-size: 2.6rem;
    font-weight: 700;
    letter-spacing: -0.5px;
    font-family: 'Poppins';
    color: #1a1a1a;
}


#time {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8em;
    font-weight: bold;
    width: 100%;
    color: #8b1717;
    gap: 25px;
    margin-bottom: 0.5em;
    font-family: 'Poppins';
    padding-right: 50px;
}


.buildings {
    margin-top: 130px;
    padding: 0;

}

.buildings * {
    max-width: 100%;
    box-sizing: border-box;
    text-decoration: none;
}


.all-buildings {
    display: flex;
    justify-content: center;
    align-items: center;
}

.building-grid {
    margin-top: 40px;
    background: #f2f2f7; /* iOS background gray */
    padding: 60px;
    border-radius: 22px;
    margin: 0 7%;
    display: flex;
    flex-wrap: wrap;
    gap: 1.5em;
    justify-content: center;
    max-height: 80vh;
    overflow-y: auto;

    /* soft iOS shadow */
    box-shadow:
        0 10px 25px rgba(0,0,0,0.08),
        0 1px 3px rgba(0,0,0,0.12);
}

/* ---------- BUILDING CARD ---------- */

.building-card {
    cursor: pointer;
    width: 42%;
}

.building {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.05);

    transition: 0.25s ease;
    box-shadow: 
        0 4px 12px rgba(0,0,0,0.06),
        0 1px 3px rgba(0,0,0,0.04);
}

.building:hover {
    transform: translateY(-4px);
    box-shadow:
        0 15px 30px rgba(0,0,0,0.12),
        0 4px 10px rgba(0,0,0,0.06);
}

.building img {
    width: 100%;
    height: 230px;
    object-fit: cover;
    border-bottom: 2px solid #e5e5ea;
}

.building p {
    padding: 18px;
    font-size: 1.45rem;
    font-weight: 600;
    text-align: center;
    color: #1c1c1e;
    font-family: 'Poppins';
}


.building-block {
    display: flex;
    justify-content: center;
}

.building-block h3 {
    font-size: 40px;
    font-family: 'Poppins';
    font-weight: 700;
    margin-bottom: 10px;
    color: #1a1a1a;
}


.floor-container {
    display: flex;
    gap: 14px;
    padding: 18px 24px;
    background-color: #fff;
    border-radius: 18px 18px 0 0;
    margin-top: 10px;

    box-shadow: 0 4px 8px rgba(0,0,0,0.07);
    position: relative;
}

.floor {
    padding: 12px 22px;
    font-weight: 600;
    font-family: 'Poppins';
    cursor: pointer;
    color: #333;
    border-radius: 12px;
    transition: 0.25s ease;
}

.floor:hover {
    color: #8b1717;
}

.floor.active {
 
   color: #8b1717;
}

/* ---------- FLOOR INDICATOR (Fluid iOS animation) ---------- */

.floor-indicator {
    position: absolute;
        background-color: #8b1717;
    bottom: -2px;
    height: 7px;
    width: 0;
    transition: 0.3s ease;
}



.room-container {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    padding: 2em;
    border-radius: 0 0 18px 18px;
    background-color: rgba(255,255,255,0.9);
    min-height: 300px;
    gap: 15px;
    backdrop-filter: blur(10px);
}

.room-card {
    background-color: #fff;
    border-radius: 14px;
    min-width: 150px;
    height: 150px;
    padding: 1em;
    margin: 8px;
    text-align: center;
    cursor: pointer;

    font-family: 'Poppins';
    font-weight: 600;

    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    transition: 0.2s ease;
}

.room-card:hover {
    transform: scale(1.04);
}

.room-label {
    font-size: 12px;
    color: #444;
}

.room-number {
    font-size: 22px;
    margin-top: 4px;
    color: #8b1717;
    font-weight: 700;
}

.room-status {
    font-size: 12px;
    color: green;
    margin-top: 4px;
      background-color: #28a745;
       color: white;
        font-weight: bold;
    padding: 4px 8px;
     border-radius: 4px;
     margin-top: 3px;
}

/* ---------------- MOBILE RESPONSIVE ---------------- */
@media (max-width: 768px) {
  .building-title {
    margin-top: 60px;
    font-size: 1.8rem; /* smaller heading */
  }

  #time {
    font-size: 3em; /* shrink clock */
    padding-right: 0;
    gap: 10px;
  }

  .building-grid {
    padding: 20px;
    margin: 0 4%;
    border-radius: 12px;
    gap: 1em;
  }

  .building-card {
    width: 100%; /* full width on mobile */
  }

  .building img {
    height: 160px; /* smaller image height */
  }

  .building p {
    font-size: 1.1rem;
    padding: 12px;
  }

  .building-block h3 {
    font-size: 1.6rem;
    text-align: center;
  }

  .floor-container {
    flex-wrap: wrap; /* allow floors to wrap */
    gap: 10px;
    padding: 12px;
  }

  .floor {
    padding: 10px 16px;
    font-size: 0.9rem;
  }

  .room-container {
    padding: 1em;
    min-height: 200px;
    gap: 10px;
  }

  .room-card {
    min-width: 120px;
    height: auto;
    padding: 0.8em;
    margin: 6px;
  }

  .room-number {
    font-size: 18px;
  }

  .room-label,
  .room-status {
    font-size: 11px;
  }
}

/* ---------------- SMALL MOBILE (phones <480px) ---------------- */
@media (max-width: 480px) {
  .building-title {
    font-size: 1.4rem;
    margin-top: 40px;
  }

  #time {
    font-size: 2.2em;
    flex-direction: column;
    gap: 5px;
  }

  .building-block{
        height: 160px;
  }

  .building-grid {
    padding: 12px;
    margin: 0 2%;
    gap: 0.8em;
  }

  .building-card {
    width: 100%;
  }

  .building img {
    height: 120px;
  }

  .building p {
    font-size: 1rem;
  }

  .floor-container {
    flex-direction: column; /* stack floors vertically */
    align-items: stretch;
  }

  .floor {
    width: 100%;
    text-align: center;
    padding: 8px;
  }

  .room-container {
    flex-direction: column;
    align-items: stretch;
  }

  .room-card {
    width: 100%;
    margin: 6px 0;
  }
}




        .back-btn {
            background: #8B1717;
            color: #EAEAEA;
            border: none;
            border-radius: 5px;
            padding: 6px 12px;
            cursor: pointer;
            font-family: "Poppins";
            font-size: 20px;
            margin-bottom: 50px;
        }

        .back-btn:hover {
            background: #EAEAEA;
            color: #A81E1E;
        }


        /* ---------- FAQ ---------- */
        .faq {
            padding: 64px 6%;
            background: linear-gradient(180deg, rgba(255, 255, 255, 1), rgba(250, 250, 250, 1));
        }

        .faq h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 18px;
            color: #111;
            font-family: 'Poppins';
        }

        .faq-grid {
            max-width: 920px;
            margin: 22px auto 0;
            display: grid;
            gap: 12px
        }

        .faq-item {
            background: #fff;
            border-radius: 12px;
            padding: 16px 18px;
            border: 1px solid #e7e7e7;
            cursor: pointer;
            transition: border-color .18s, box-shadow .18s;
        }

        .faq-item.open {
            border-color: var(--primary);
            box-shadow: 0 8px 24px rgba(139, 23, 23, 0.06)
        }

        .faq-q {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-family: 'Poppins';
        }

        .faq-a {
            margin-top: 10px;
            color: var(--muted);
            display: none;
            line-height: 1.45;
            font-family: 'Poppins';
        }

        /* ---------- FOOTER ---------- */
        footer {
            padding: 36px 6%;
            text-align: center;
            color: white;
            border-top: 1px solid #eee;
            margin-top: 12px;
            background-color: #8b1717;
            border-radius: 300px 300px 0 0;
        }


        /* ---------- Responsiveness ---------- */
        @media (max-width:960px) {
            .hero {
                grid-template-columns: 1fr;
                padding: 56px 6% 36px;
                margin-top: 0em;
            }

            .hero-right {
                order: -1;
                margin-bottom: 12px;
            }

            nav ul {
                display: none
            }

            .menu-toggle {
                display: inline-flex;
            }

            footer {
                font-size: 10px;
            }

            .login-btn {
                padding: 9px 18px;
                font-size: 10px;

            }
        }

        @media (max-width:720px) {
            .feature-card {
                width: 100%
            }

            .hero-card {
                margin-top: 2em;
                height: 350px;
            }

            .hero-card img {
                height: 280px
            }

            #time {
                font-size: 3em;
            }
        }

        

/* ======== Modal overlay ======== */
.custom-modal {
    display: none;            /* hidden by default */
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    opacity: 0;
     pointer-events: none;           /* prevents clicks when hidden */
    z-index: 3000;
    transition: opacity 0.3s ease; /* smooth fade */
}

/* When modal is shown */
.custom-modal.show {
    display: flex;
    opacity: 1;
    pointer-events: all;
}

/* ======== Modal box ======== */
.custom-modal-dialog {
    background: white;
     z-index: 1001;
    padding: 20px;
    border-radius: 8px;
    width: 600px;
    max-width: 90%;
    animation: pop 0.3s ease;
}

@keyframes pop {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

/* Close button */
.custom-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
}

.btn-close-modal {
    padding: 6px 12px;
    cursor: pointer;
}

.custom-modal-header {
    position: relative;           /* make button absolute relative to header */
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
}

.custom-modal-title {
    margin: 0;
    font-size: 18px;
}

.custom-close {
    position: absolute;           /* float above title */
    top: 10px;                    /* adjust vertical position */
    right: 15px;                  /* adjust horizontal position */
    font-size: 24px;
    background: none;
    border: none;
    cursor: pointer;
    line-height: 1;
    color: #8B1717;
}


.custom-modal-body p {
    margin: 0 0 15px 0; /* remove top margin, small bottom margin */
    font-weight: 600;
    font-size: 16px;   /* optional: adjust size */
}

/* ============================
   TABLE STYLING
============================ */
.classSchedTable {
    width: 100%;
    border-collapse: separate;
    font-size: 14px;
}

.classSchedTable thead {
    background: #f1f1f1;
    
}

.classSchedTable thead th {
  position: sticky;
    background: #f1f1f1;
    top: 0;
    z-index: 2;
}
.classSchedTable th,
.classSchedTable td {
    padding: 10px 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

/* Bold header text */
.classSchedTable th {
    font-weight: 600;
    
}

/* Row hover effect */
.classSchedTable tbody tr:hover {
    background: #f9f9f9;
}

.Sched-table-wrapper {
    max-height: 300px;        /* adjust as needed */
    max-width: 100%;
    overflow-y: auto;         /* vertical scrollbar if needed */
    border-radius: 10px;      /* rounded corners for the container */
    border: 1px solid #ddd;   /* optional border around table */
}
/* ============================
   SEPARATOR LINE
============================ */
.table-separator {
    border: none;
    border-top: 1px solid #ddd;
    margin: 15px 0;
}

/* ============================
   FOOTER BUTTONS
============================ */
.custom-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 10px;
}

.custom-modal-footer button {
    padding: 8px 14px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

.btn-close-modal {
    background: #ccc;
}

#addBtn {
    background: #2b67f0;
    color: white;
}

/* ============================
   MODAL WIDTH / BODY SPACING
============================ */
.custom-modal-dialog {
    width: 650px;
    max-width: 95%;
}

.custom-modal-body {
    padding: 15px 20px;
}

/* Reservation fields modal */

/* Form fields styling */
.form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 10px;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 5px;
}

.form-group input {
    padding: 8px 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}

/* Reuse existing modal footer buttons */
#confirmReserve {
    background: #2b67f0;
    color: white;
    border-radius: 5px;
    padding: 8px 14px;
    border: none;
    cursor: pointer;
}
    </style>
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
            <div id="time"></div>
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
                                                                <div class="room-card">
                                                                    <div class="room-label">Room no</div>
                                                                    <div class="room-number"><?= htmlspecialchars($room['RoomNumber']) ?>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="room-status">
                                                                        <?= htmlspecialchars($room['Status'] ?? 'Available') ?></div>
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
                                    <select id="dayFilter" class="day-dropdown">
                                        <option>Monday</option>
                                        <option>Tuesday</option>
                                        <option>Wednesday</option>
                                        <option>Thursday</option>
                                        <option>Friday</option>
                                        <option>Saturday</option>
                                        <option>Sunday</option>
                                    </select>

                                    <div class="Sched-table-wrapper">
                                        <table class="classSchedTable">
                                            <thead>
                                                <tr>
                                                    <th>Instructor</th>
                                                    <th>Subject</th>
                                                    <th>Time</th>
                                                    <th>Section</th>
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

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".building-card").forEach(card => {
        const floorContainer = card.querySelector(".floor-container");
        const detailsContainer = card.querySelector(".building-block");
        const backBtn = card.querySelector(".back-btn");

        // Building click
        card.querySelector(".building").addEventListener("click", () => {
            // Hide all buildings
            document.querySelectorAll(".building-card").forEach(c => {
                c.querySelector(".building").style.display = "none";
                c.querySelector(".building-block").style.display = "none";
                c.style.width = "";
            });

            // Expand this building
            card.style.width = "100%";
            card.style.display = "flex";
            card.style.flexDirection = "column";

            detailsContainer.style.display = "block";
            backBtn.style.display = "block";

            // ✅ Default to first floor by simulating a click
            const firstFloor = floorContainer.querySelector(".floor");
            if (firstFloor) {
                firstFloor.click(); // triggers the floor click handler
            }
        });

        // Floor click
        floorContainer.addEventListener("click", e => {
            if (!e.target.classList.contains("floor")) return;

            // Remove active from all floors
            floorContainer.querySelectorAll(".floor").forEach(f => f.classList.remove("active"));
            e.target.classList.add("active");

            const selectedFloor = e.target.dataset.floor;

            // Hide all room containers
            card.querySelectorAll(".room-container").forEach(rc => rc.style.display = "none");

            // Show only the matching one
            const targetRoomContainer = card.querySelector(`.room-container[data-floor="${selectedFloor}"]`);
            if (targetRoomContainer) targetRoomContainer.style.display = "flex";

            // Move floor indicator
            const indicator = floorContainer.querySelector(".floor-indicator");
            if (indicator) {
                indicator.style.width = e.target.offsetWidth + "px";
                indicator.style.left = e.target.offsetLeft + "px";
            }
        });

        // Back button click
        backBtn.addEventListener("click", e => {
            e.stopPropagation();
            document.querySelectorAll(".building-card").forEach(c => {
                c.querySelector(".building").style.display = "block";
                c.querySelector(".building-block").style.display = "none";
                c.style.width = "";
            });
            backBtn.style.display = "none";
            detailsContainer.style.display = "none";
        });
    });
});

  // =========================
                // 1. Get modal element
                // =========================
                const classroomModal = document.getElementById("classroomModal");

                // Close buttons inside the modal
                const closeModalBtn = document.getElementById("closeclassroomModal");
                const closeFooterBtn = document.getElementById("closeAddUserFooter");

                // =========================
                // 2. Function to open modal
                // =========================
                function openClassroomModal() {
                    classroomModal.classList.add("show"); // makes modal visible
                }

                // =========================
                // 3. Function to close modal
                // =========================
                function closeClassroomModal() {
                    classroomModal.classList.remove("show"); // hides modal
                }

                // =========================
                // 4. Attach click event to all .room-card items
                //    THIS IS THE TRIGGER
                // =========================
                    // document.querySelectorAll(".room-card").forEach(card => {
                    //     card.addEventListener("click", () => {
                    //         openClassroomModal(); // show modal when clicking any room
                    //     });
                    // });

                // =========================
                // 5. Close modal using the "X" button
                // =========================
                closeModalBtn.addEventListener("click", closeClassroomModal);

                // =========================
                // 6. Close modal using footer Close button
                // =========================
                closeFooterBtn.addEventListener("click", closeClassroomModal);

                // =========================
                // 7. Close modal when clicking outside content
                // =========================
                window.addEventListener("click", (e) => {
                    if (e.target === classroomModal) {
                        closeClassroomModal();
                    }
                });

                  document.querySelectorAll(".room-card").forEach(card => {
        card.addEventListener("click", () => {
            const roomID = card.getAttribute("data-room-id"); // make sure to set this in PHP
            const roomNumber = card.querySelector(".room-number").innerText;
            window.currentRoomID = roomID;

              const roomInput = document.getElementById("roomID");
        if (roomInput) roomInput.value = roomID;
        
            // DEBUG: check if roomID is correctly set
            console.log("Clicked roomID:", roomID);
            console.log("window.currentRoomID:", window.currentRoomID);

            // Set currentDay to today's day automatically
            const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const today = days[new Date().getDay()];
            window.currentDay = today;

            // Also update the dayFilter dropdown to match today
            const daySelect = document.getElementById("dayFilter");
            if (daySelect) daySelect.value = today;

            // Open modal
            const classroomModal = document.getElementById("classroomModal");
            classroomModal.classList.add("show");

            // Update modal title with room number
            document.querySelector("#classroomModal .custom-modal-title").innerText = `Classroom Schedule - Room ${roomNumber}`;

            // Load today's schedules
            loadSchedules(today);
        });
    });

    function loadRoomStatuses() {
    const weekType = "Odd"; // force week type to Odd

    document.querySelectorAll(".clickable-room").forEach(roomCard => {
        const roomID = roomCard.dataset.room;

        fetch("faculty-reservation.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                action: "getSchedules",
                roomID: roomID,
                dayOfWeek: new Date().toLocaleString("en-US", { weekday: "long" }),
                weekType: weekType
            })
        })
        .then(res => res.json())
        .then(schedules => {
            let status = "Available";
            const now = new Date();
            const currentTime = now.getHours().toString().padStart(2, "0") + ":" +
                                now.getMinutes().toString().padStart(2, "0");

            // Build schedule list
            let scheduleHTML = "<ul>";
            schedules.forEach(sch => {
                scheduleHTML += `<li>${sch.Subject || "Class"}: ${sch.TimeFrom} - ${sch.TimeTo}</li>`;
                if (currentTime >= sch.TimeFrom && currentTime <= sch.TimeTo) {
                    status = "Occupied";
                }
            });
            scheduleHTML += "</ul>";

            // Update status
            const statusDiv = roomCard.querySelector(".room-status");
            statusDiv.textContent = status;
            statusDiv.className = "room-status " + status.toLowerCase();

            // Update schedules
            const schedDiv = roomCard.querySelector(".room-schedules");
            if (schedDiv) {
                schedDiv.innerHTML = schedules.length > 0 ? scheduleHTML : "No schedules today";
            }
        })
        .catch(err => console.error(err));
    });
}



</script>



    <script>

        window.addEventListener('scroll', function () {
            const header = document.querySelector('header');

            if (window.scrollY > 10) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });


        // Mobile menu toggle (simple)
        function toggleMobileMenu() {
            const ul = document.getElementById('main-menu');
            if (!ul) return;
            if (window.getComputedStyle(ul).display === 'none') {
                ul.style.display = 'flex';
                ul.style.flexDirection = 'column';
                ul.style.position = 'absolute';
                ul.style.right = '5%';
                ul.style.top = '66px';
                ul.style.background = 'white';
                ul.style.padding = '10px 14px';
                ul.style.borderRadius = '10px';
                ul.style.boxShadow = '0 12px 36px rgba(15,15,15,0.08)';
            } else {
                // reset to default for wide screens
                if (window.innerWidth < 960) {
                    ul.style.display = 'none';
                } else {
                    ul.style.display = 'flex';
                    ul.style.flexDirection = 'row';
                    ul.style.position = '';
                    ul.style.right = '';
                    ul.style.top = '';
                    ul.style.background = '';
                    ul.style.padding = '';
                    ul.style.borderRadius = '';
                    ul.style.boxShadow = '';
                }
            }
        }

        // Close mobile menu when resizing to desktop
        window.addEventListener('resize', () => {
            const ul = document.getElementById('main-menu');
            if (!ul) return;
            if (window.innerWidth >= 960) {
                ul.style.display = 'flex';
                ul.style.flexDirection = 'row';
                ul.style.position = '';
                ul.style.right = '';
                ul.style.top = '';
                ul.style.background = '';
                ul.style.padding = '';
                ul.style.borderRadius = '';
                ul.style.boxShadow = '';
            } else {
                ul.style.display = 'none';
            }
        });

        // FAQ accordion
        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', () => {
                const isOpen = item.classList.contains('open');
                // close others (optional: single-open behavior)
                document.querySelectorAll('.faq-item').forEach(i => {
                    i.classList.remove('open');
                    const ans = i.querySelector('.faq-a');
                    if (ans) ans.style.display = 'none';
                });

                if (!isOpen) {
                    item.classList.add('open');
                    const ans = item.querySelector('.faq-a');
                    if (ans) ans.style.display = 'block';
                }
            });
        });

        // initial responsive menu state
        (function initMenu() {
            if (window.innerWidth < 960) {
                const ul = document.getElementById('main-menu');
                if (ul) ul.style.display = 'none';
            }
        })();

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




    </script>

</body>

</html>