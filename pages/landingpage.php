<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
</head>

<body>
    <style>
        * {
            scrollbar-width: none;
            scroll-behavior: smooth;
        }

        body {
            background-color: whitesmoke;
            margin: 0;
        }


        /*header designs  */
        header {
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: whitesmoke;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        header.scrolled {
            background-color: rgba(245, 245, 245, 0.6);
            backdrop-filter: blur(6px);
        }

        main {
            padding-top: 70px;
        }



        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 30px;
        }

        .logo h2 {
            font-size: 20px;
            font-weight: lighter;
            color: black;

        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            align-items: center;
            padding: 0 30px;
            color: white;

        }

        nav li a {
            text-decoration: none;
            color: black;
        }

        nav button {
            color: black;
            background-color: rgba(184, 13, 13, 1);
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            transition: 0.3s;
        }

        .btn {
            color: white;
            font-family: Arial, Helvetica, sans-serif;
        }

        nav button:hover{
            transform: scale(1.03);
            background-color: rgba(184, 13, 13, 0.69)
        }


        /*front page design*/
        .bsu-front {
            position: relative;
            text-align: center;
            height: 50em;
            color: white;
        }

        .bsu-front img {
            width: 100%;
            opacity: 0.5;
            object-fit: cover
        }

        .red-filter {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 60em;
            background-color: rgba(184, 13, 13, 0.67);
            pointer-events: none;
        }

        .quote {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);

        }

        .quote h1 {
            font-size: 6em;
            font-weight: bold;
            color: white;
            flex-wrap: wrap;
            font-family: monospace;
            margin: 0;
        }

        .quote P {
            font-size: 25px;
            font-weight: bold;
            color: white;
            font-family: unset;
            margin: 0;
        }

        .lrn {
            padding: 15px;
            background-color: whitesmoke;
            margin-top: 4em;
            cursor: pointer;
            transition: 0.3s;
        }

        .lrnmr {
            color: red;
            font-size: 20px;
            text-decoration: none;
            font-family: monospace;
            transition: 0.3s;
        }

        .lrnmr i {
            font-size: 15px;
        }

        .lrn:hover {
            transform: scale(1.05);

        }

        .lrnmr:hover {
            transform: scale(1.05);

        }


        /*Features*/
        .title-f {
            background-color: #e7e7e7ff;
            padding: 50px;
            margin-top: 8em;
            height: 55em;
            font-size: 14px;
            font-weight: lighter;
            font-family: monospace;
        }

        .feature {
            display: flex;
            justify-content: space-between;
            align-items: center;

        }

        .feature-1,
        .feature-2,
        .feature-3 {
            height: 25em;
            color: black;
            background-color: #ffffffff;
            border: 2px solid black;
            border-radius: 20px;
            flex-direction: column;
            justify-content: left;
            display: flex;
            flex-wrap: wrap;
            padding: 20px;
            width: 25%;
            cursor: pointer;
            color: black;
            transition: 0.3s;
        }

        .feature-1:hover,
        .feature-2:hover,
        .feature-3:hover {
            transform: scale(1.005);
            border: 2px solid #e60a0ace;
            background-color: ;

        }

        /*Classroom Availbility Design*/
        .view {
            margin-top: 10em;
            height: 60em;
        }

        /* .search-bar {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .view input[type="text"] {
            float: none;
            padding: 5px;
            border: none;
            margin-bottom: 20px;
            height: 30px;
            font-size: 20px;
            width: 50em;
        } */

        .container {
            position: absolute;
            height: 50em;
        }

        .box {
            background-color: white;
            height: 50em;
            margin: 0 250px;
            margin-bottom: 100px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 0 15em;

        }

        a {
            text-decoration: none;
        }

        .building-1,
        .building-2,
        .building-3,
        .building-4 {
            border: solid black 2px;
            height: 18em;
            width: 25em;
            background-color: aliceblue;
            border-radius: 20px;

        }

        .building-1 p,
        .building-2 p,
        .building-3 p,
        .building-4 p {
            font-size: 24px;
            text-transform: capitalize;
            padding-left: 20px;
            padding-bottom: 10px;

        }

        .hidden {
            display: none;
        }

        footer {
            background-color: #13132c;
            width: 100%;
            padding: 25px 0;
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
    </style>

    <header>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

        <!--- NAVIGATION BAR -->
        <div class="logo">
            <img src="../images/BSU_logo (3).webp" alt="bsu logo"
                style="height: 40px; max-width: 100%; background-repeat: no-repeat;">
            <h2>CAMS</h2>
        </div>

        <nav>
            <ul>
                <li><a href="#">About</a></li>
                <li><a href="#search">Search</a></li>
                <li><button><a class="btn" href="#">Log In</a></button></li>
            </ul>
        </nav>
    </header>


    <main>
        <!-- Home Page -->
        <div class="land" id="home">
            <div class="bsu-front">
                <img src="../images/bsu_front.webp" alt="building"
                    style="height: 60em; width: 100%; background-repeat: no-repeat; opacity: 0.7; ">
                <div class="red-filter"></div>
                <div class="quote">
                    <h1>CLASSROOM AVAILABILITY <span style="color: bisque;">MANAGEMENT SYSTEM</span></h1>
                    <P>"Find Available Classrooms in Seconds"</P>
                    <button class="lrn"><a class="lrnmr" href="#feature">Learn More <i
                                class="bi bi-arrow-down-right"></i></a></button>
                </div>

            </div>
        </div>


        <!-- features of the system -->
        <div class="title-f" id="feature">
            <div
                style="justify-content: center; align-items: center; display: flex; text-align: center; flex-direction: column; margin-top: 2em;">
                <h1
                    style="background-color: #cecdcd94; width: 80 autopx; padding: 15px; border-radius: 20px; font-size: 20px; opacity: 0.5;">
                    Feature</h1>
                <p
                    style="font-weight: bold; font-size: 3.5em; flex-wrap: wrap; width: 30%; margin:0; margin-bottom: 2em; font-family: 'Trebuchet MS', Grande', 'Lucida Sans', Arial, sans-serif;">
                    Everyrhing that our system can do.</p>
            </div>
            <div class="feature">
                <div class="feature-1">
                    <i class="bi bi-geo-alt" alt="bootsrap" style="font-size: 30px;"></i>
                    <h2 style="font-size: 25px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">VISUAL
                        MAP & SEARCH</h2>
                    <p style="font-size: 20px; opacity: 0.7;font-family: Arial, Helvetica, sans-serif;">Easily Locates
                        available classroom across campus. </p>
                </div>
                <div class="feature-2">
                    <i class="bi bi-clock-history" alt="bootsrap" style="font-size: 30px;"></i>
                    <h2 style="font-size: 25px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">REAL-TIME
                        AVAILABILITY & REQUEST</h2>
                    <p style="font-size: 20px; opacity: 0.7;font-family: Arial, Helvetica, sans-serif;">Instantly check
                        which room are free or occupied and send request in one click.</p>
                </div>
                <div class="feature-3">
                    <i class="bi bi-phone" alt="bootsrap" style="font-size: 30px;"></i>
                    <h2 style="font-size: 25px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">MOBILE &
                        NOTIFICATION</h2>
                    <p style="font-size: 20px; opacity: 0.7;font-family: Arial, Helvetica, sans-serif;">Get update and
                        manage classroom anytime anywhere</p>
                </div>
            </div>

        </div>

        <!-- Searching for available classroom -->
        <div class="view" id="search">

            <div class="container">
                <div id="building" class="box">
                    <a href="#">
                        <div class="building-1">
                            <img src="building1.jpg" alt="BUILDING 1"
                                style="height: 80%; width: 100%; border-radius: 20px 20px 0 0; ">
                            <p>Building 1</p>
                        </div>
                    </a>
                    <a href="#">
                        <div class="building-2">
                            <img src="building2.jpg" alt="BUILDING 2"
                                style="height: 80%; width: 100%; border-radius: 20px 20px 0 0; ">
                            <p>Building 2</p>
                        </div>
                    </a>
                    <a href="#">
                        <div class="building-3">
                            <img src="building3.jpg" alt="BUILDING 3"
                                style="height: 80%; width: 100%; border-radius: 20px 20px 0 0; ">
                            <p>Building 3</p>
                        </div>
                    </a>
                    <a href="#">
                        <div class="building-4">
                            <img src="building4.jpg" alt="BUILDING 4"
                                style="height: 80%; width: 100%; border-radius: 20px 20px 0 0; ">
                            <p>Building 4</p>
                        </div>
                    </a>
                </div>
            </div>

            <div id="classroom" class="box hidden">
                <p>Hello World</p>
            </div>

        </div>

    </main>


 <footer>


    <div style="display: flex; justify-content: center; align-items: center; width: 100%; gap: 10px;">
        <img src="../images/BSU_logo (3).webp" alt="BSU Logo" style="height: 50px;">

        <div style="display: flex; flex-direction: column; align-items: flex-start;">
            <h1 style="margin: 0; font-size: 1.4rem;">CAMS</h1>
            <p style="margin: 0; font-size: 0.9rem; opacity: 0.85;">Classroom Availability Management System</p>
        </div>
    </div>

    <!-- Decorative Line -->
    <div class="line" style=" height: 1px; width: 60%; background-color: rgba(255, 255, 255, 0.3); margin: 5px auto;"></div>

    <!-- Bottom Text -->
    <div style="display: flex; justify-content: center; align-items: center; width: 100%; margin-top: 10px;">
        <p style="font-size: 0.8rem; opacity: 0.7;">© 2025 Classroom Management System. All rights reserved.</p>
    </div>

</footer>



    <script>
        const building = document.getElementById('building');
        const classroom = document.getElementById('classroom');

        building.addEventListener('click', () => {
            building.classList.add('hidden');
            classroom.classList.remove('hidden');
        });

        window.addEventListener('scroll', function () {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

    </script>






</body>

</html>