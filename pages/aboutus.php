<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us — CAMS</title>

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #8b1717;
            --text-dark: #111;
            --text-light: #444;
            --bg-light: #f4f4f4;
            --card-bg: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: var(--bg-light);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* GO BACK BUTTON */
        .back-btn {
            margin: 20px 6%;
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
        }

        /* HEADER */
        .header {
            padding: 40px 6% 10px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 700;
        }

        .header p {
            margin-top: 10px;
            font-size: 1rem;
            color: var(--text-light);
        }

        /* TEAM SECTION */
        .team-section {
            padding: 40px 6%;
        }

        .team-section h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 40px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .team-card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid #dcdcdc;
            transition: 0.3s;
            text-align: left;
        }

        .team-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }

       .team-img { width: 60%; height: 280px; background: #e5e5e5; border-radius: 12px; margin: 0 auto 15px auto; display: flex; justify-content: center; align-items: center; color: #888; font-size: 0.9rem; font-style: italic; } 
       .team-img img { width: 100%; height: 280px; border-radius: 12px; display: block; margin: 0 auto; }

        .team-card h3 {
            font-size: 1.2rem;
            margin-bottom: 6px;
        }

        .team-card p {
            font-size: 0.95rem;
            color: var(--text-light);
            line-height: 1.4;
            margin-bottom: 6px;
        }

        .email {
            font-size: 0.9rem;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 10px;
        }

        /* VISION & MISSION */
        .vm-section {
            padding: 60px 6%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .vm-box {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid #dcdcdc;
            transition: 0.3s;
        }

        .vm-box:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }

        .vm-box h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .vm-box p {
            color: var(--text-light);
            line-height: 1.6;
        }

        /* FOOTER */
        footer {
            margin-top: 80px;
            padding: 40px 6%;
            text-align: center;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .team-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .vm-section {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .team-grid {
                grid-template-columns: 1fr;
            }
            .header h1 {
                font-size: 1.8rem;
            }
            .header p {
                font-size: 0.95rem;
            }
            .team-card h3 {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .back-btn {
                margin: 20px 4%;
                padding: 8px 16px;
                font-size: 0.9rem;
            }
            .header h1 {
                font-size: 1.5rem;
            }
            .team-section h2 {
                font-size: 1.6rem;
            }
            .vm-box h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>

<body>
    <button class="back-btn" onclick="window.history.back()">← Go Back</button>

    <section class="header">
        <h1>About CAMS</h1>
        <p>Meet the developers and learn about the purpose behind our system.</p>
    </section>

    <section class="team-section" id="team">
        <h2>Meet the Developers</h2>

        <div class="team-grid">
            <div class="team-card">
                <div class="team-img"><img src="../images/MAK.jpg" alt="Suarez, John Mark L."></div>
                <h3>Suarez, John Mark L.</h3>
                <p class="email">23-01629@g.batstate-u.edu.ph</p>
                <p><strong>Leader & Support</strong></p>
                <p>• Team Leader – Guides the team and monitors progress</p>
                <p>• Project Manager – Handles scheduling & coordination</p>
                <p>• Quality Assurance – Tests and reviews system output</p>
                <p>• Requirements Specialist – Consolidates system needs</p>
                <p>• Documentation Lead – Prepares reports & presentation</p>
            </div>

            <div class="team-card">
                <div class="team-img"><img src="../images/KIT.jpg" alt="Fronda, Kit Justine M."></div>
                <h3>Fronda, Kit Justine M.</h3>
                <p class="email">23-32179@g.batstate-u.edu.ph</p>
                <p><strong>UI/UX Designer</strong></p>
                <p>• Frontend Developer – Designs and builds UI</p>
                <p>• Backend Support – Assists in integration</p>
                <p>• Requirements Specialist – Analyzes client needs</p>
                <p>• Testing Support – Helps debug and validate features</p>
            </div>

            <div class="team-card">
                <div class="team-img"><img src="../images/DHARZ.jpg" alt="Pamaylaon, Darence A."></div>
                <h3>Pamaylaon, Darence A.</h3>
                <p class="email">23-37725@g.batstate-u.edu.ph</p>
                <p><strong>System Integrator</strong></p>
                <p>• Backend Developer – Builds server logic & APIs</p>
                <p>• Frontend Support – UI integration assistance</p>
                <p>• Requirements Specialist – Ensures feasibility</p>
                <p>• Database Manager – Designs & maintains DB</p>
            </div>
        </div>
    </section>

    <section class="vm-section" id="vm">
        <div class="vm-box">
            <h2>Our Vision</h2>
            <p>
                To create a smarter, more efficient, and fully accessible classroom management ecosystem that enhances
                academic flow, reduces conflicts, and empowers both students and faculty through real‑time technological
                solutions.
            </p>
        </div>

        <div class="vm-box">
            <h2>Our Mission</h2>
            <p>
                Our mission is to streamline classroom usage by providing an intuitive system that ensures transparency,
                availability tracking, and accurate scheduling. We aim to support educational institutions in improving
                productivity and operational efficiency through innovation.
            </p>
        </div>
    </section>

    <footer>
        © 2025 Classroom Availability Management System — All Rights Reserved.
    </footer>
</body>

</html>
