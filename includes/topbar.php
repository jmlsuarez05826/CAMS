*
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Bar</title>
</head>

<body>
    <style>
        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 30px;
            /* spacing inside header */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            /* subtle shadow */
            margin-left: 320px;
            width: calc(100% - 250px);
        }

        .topbar h2 {
            color: rgb(0, 0, 0);
            font-size: 32px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            /* vertically center search + button */
            gap: 25px;
            /* space between search and button */
        }
    </style>
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


</body>

</html>