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
            background-color: #383B39;
            margin: 0;

        }

        header {
            padding: 2px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            color: white;

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
            color: white;
        }

        nav button {
            color: black;
            background-color: darkred;
            padding: 5px 20px;
            border: solid black 2px;
            border-radius: 4px;
            transition: 0.3s;
        }

        .btn {
            color: white;
        }

        .bsu-front {
            position: relative;
            text-align: center;
            color: white;
        }

        .bsu-front img {
            width: 100%;
            height: 55em;
            opacity: 0.5;
            object-fit: cover
        }

        .quote {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            
        }

        .quote h1 {
            font-size: 3em;
            font-weight: bold;
            color: white;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }


        .title-f {
            background-color: #b93737ff;
            padding: 50px;
            margin-top: 3em;
            font-size: 14px;
            font-weight: lighter;
            font-family: monospace;
        }

        .feature {
            display: flex;
            justify-content: space-between;
            align-items: center;

        }

        .feature h1 {
            font-size: 25px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .view {
            margin-top: 10em;
            height: 60em;
        }

        .search-bar {
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
        }

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
            background-color: cornsilk;
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
    </style>

    <header>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


        <div class="logo">
            <img src="../images/BSU_logo (3).webp" alt="bsu logo"
                style="height: 40px; max-width: 100%; background-repeat: no-repeat;">
            <h2>CAMS</h2>
        </div>

        <nav>
            <ul>
                <li><a href="#">About</a></li>
                <li><a href="#search">Search</a></li>
                <li><button><a class="btn" href="#">Login</a></button></li>
            </ul>
        </nav>
    </header>
    <div class="land" id="home">
        <div class="bsu-front">
            <img src="../images/bsu_front.webp" alt="building"
                style="height: 55em; width: 100%; background-repeat: no-repeat; opacity: 0.7; ">
            <div class="quote">
                <h1>"Find Available Classrooms in Seconds"</h1>
            </div>

        </div>
    </div>

    <div class="title-f">
        <h1>Feature</h1>
        <div class="feature">
            <div>

                <h2> <i class="bi bi-geo-alt" alt="bootsrap"></i>VISUAL MAP & SEARCH</h2>
                <p>Easily Locates available classroom across campus. </p>
            </div>
            <div>

                <h2> <i class="bi bi-clock-history"></i>REAL-TIME AVAILABILITY & REQUEST</h2>
                <p>Instantly check which room are free or occupied and send request in one click.</p>
            </div>
            <div>

                <h2><i class="bi bi-phone"></i>MOBILE & NOTIFICATION</h2>
                <p>Get update and manage classroom anytime anywhere</p>
            </div>
        </div>

    </div>

    <div class="view" id="search">
        <div class="search-bar">
            <input type="text" placeholder="Search for available rooms...">
        </div>

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

    <script>
        const building = document.getElementById('building');
        const classroom = document.getElementById('classroom');

        building.addEventListener('click', () => {
            building.classList.add('hidden');
            classroom.classList.remove('hidden');
        });




    </script>






</body>

</html>