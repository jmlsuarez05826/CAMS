document.addEventListener('DOMContentLoaded', function() {
    // Ask browser permission on dashboard load
    function requestNotificationPermission() {
        if ("Notification" in window) {
            if (Notification.permission === "default") {
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        // Start polling only after permission is granted
                        startPolling();
                    }
                });
            } else if (Notification.permission === "granted") {
                startPolling();
            }
        } else {
            console.log("This browser does not support notifications.");
        }
    }

    // Shows corner popup
    function showPopup(text) {
        const div = document.createElement("div");
        div.classList.add("notification-popup");
        div.innerHTML = text;
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 4000);
    }

    // Browser notification
    function browserNotify(title, body) {
        if (Notification.permission === "granted") {
            new Notification(title, { body: body });
        }
    }

    // CHECK CHAT MESSAGES
    function checkMessages() {
        fetch("../pages/check_messages.php")
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    data.forEach(msg => {
                        let text = `New chat message: ${msg.message}`;
                        showPopup(text);
                        browserNotify("New Chat Message", msg.message);

                        if (typeof updateGreenDot === "function") {
                            updateGreenDot();
                        }
                    });
                }
            });
    }

    // CHECK RESERVATIONS
    function checkReservations() {
        fetch("../pages/check_reservations.php")
            .then(res => res.json())
            .then(data => {
                data.forEach(res => {
                    let message = "";

                    if (res.type === "equipment") {
                        message = `New Equipment Reservation (User ${res.full_name}): ${res.ReservationDate}`;
                    } else {
                        message = `New Classroom Reservation (${res.Subject}): ${res.ReservationDate}`;
                    }

                    showPopup(message);
                    browserNotify("New Reservation", message);
                });
            });
    }

    // Start polling
    function startPolling() {
        setInterval(checkMessages, 3000);
        setInterval(checkReservations, 3000);
    }

    // Initial trigger
    requestNotificationPermission();
});
