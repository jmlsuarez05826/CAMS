<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notification API Demo</title>
</head>
<body>
  <button id="notifyBtn">Show Notification</button>

  <script>
    // Step 1: Ask for permission
    document.getElementById("notifyBtn").addEventListener("click", () => {
      if (!("Notification" in window)) {
        alert("This browser does not support notifications.");
        return;
      }

      Notification.requestPermission().then(permission => {
        if (permission === "granted") {
          // Step 2: Create a notification
          new Notification("Room Update", {
            body: "Room 101 is now occupied!",
            icon: "room.png" // optional icon
          });
        } else {
          alert("Notifications are blocked.");
        }
      });
    });
  </script>
</body>
</html>
