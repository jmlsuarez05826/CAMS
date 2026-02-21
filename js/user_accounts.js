
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search-field');
            const table = document.querySelector('.users-table tbody');
            const noResultsDiv = document.getElementById('no-results');

            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase();
                let anyVisible = false;

                Array.from(table.rows).forEach(row => {
                    const rowText = Array.from(row.cells)
                        .map(cell => cell.textContent.toLowerCase())
                        .join(' ');

                    if (rowText.includes(query)) {
                        row.style.display = '';
                        anyVisible = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                noResultsDiv.style.display = anyVisible ? 'none' : 'block';
            });
        });




function updateTimeDay() {
            const now = new Date();

            // Get day
            const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const day = days[now.getDay()];

            // Get 24-hour time
            const hours = String(now.getHours()).padStart(2, '0'); // military time
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            // Set the text content
            document.getElementById('time').textContent =
                `${day}, ${hours}:${minutes}:${seconds}`;
        }

        // Update every second
        setInterval(updateTimeDay, 1000);

        // Initial call
        updateTimeDay();


        // Delete Logic with SweetAlert2
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                let userId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will mark the user as inactive.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'deleteUser';
                        input.value = userId;
                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

      //script logic for the edit user
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        let userId = this.getAttribute('data-id');
        let row = this.closest('tr');

        // Current values
        let currentFname = row.cells[0].textContent.trim();
        let currentLname = row.cells[1].textContent.trim();
        let currentPhone = row.cells[2].textContent.trim();
        let currentEmail = row.cells[3].textContent.trim();

        Swal.fire({
            title: 'Update User',
            html:
                '<input id="fname" class="swal2-input" placeholder="First Name" value="' + currentFname + '">' +
                '<input id="lname" class="swal2-input" placeholder="Last Name" value="' + currentLname + '">' +
                '<input id="phone" class="swal2-input" placeholder="Phone" value="' + currentPhone + '">' +
                '<input id="email" class="swal2-input" placeholder="Email" value="' + currentEmail + '">',
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Save',
            preConfirm: () => {
                return {
                    fname: document.getElementById('fname').value,
                    lname: document.getElementById('lname').value,
                    phone: document.getElementById('phone').value,
                    email: document.getElementById('email').value
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show confirmation before submitting
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: 'The user details have been updated.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // Submit the form after the alert
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';

                    const fields = {
                        updateUser: '1',
                        UserID: userId,
                        fname: result.value.fname,
                        lname: result.value.lname,
                        phone: result.value.phone,
                        email: result.value.email
                    };

                    for (let key in fields) {
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = fields[key];
                        form.appendChild(input);
                    }

                    document.body.appendChild(form);
                    form.submit();
                });
            }
        });
    });
});



        // DEBUG: Track modal triggers
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {

            console.log("⏺ BUTTON FOUND:", btn);  // DEBUG

            btn.addEventListener('click', (e) => {
                e.preventDefault();   // DEBUG: stop default behavior first
                e.stopPropagation();  // DEBUG: stop Bootstrap from interfering

                const target = btn.getAttribute('data-bs-target');
                console.log("➡ CLICKED. TARGET:", target);  // DEBUG

                const modal = document.querySelector(target);

                if (!modal) {
                    console.error("❌ Modal NOT FOUND:", target);
                    return;
                }

                console.log("✅ Modal FOUND:", modal);

                // Force open your custom modal
                modal.style.display = 'flex';

                console.log("🎉 Modal DISPLAY applied:", modal.style.display);
            });
        });

        // Close modal
        document.querySelectorAll('.custom-close, .btn-close-modal').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.custom-modal').style.display = 'none';
            });
        });

        // Optional: close modal if clicking outside content
        document.querySelectorAll('.custom-modal').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) modal.style.display = 'none';
            });
        });



        const phoneInput = document.getElementById('phone');
        const getCodeBtn = document.getElementById('getCodeBtn');
        const phoneError = document.getElementById('phoneError');
        const addFacultyForm = document.getElementById('addFacultyForm');
        const addUserModalEl = document.getElementById('addUserModal');


        let pendingUserData = {
            verified: false
        };

        // Enable/disable Get Code button
        phoneInput.addEventListener('input', () => {
            const phoneVal = phoneInput.value.trim();
            const regex = /^(09\d{9}|639\d{9})$/;
            if (regex.test(phoneVal)) {
                getCodeBtn.disabled = false;
                phoneError.classList.add('d-none');
            } else {
                getCodeBtn.disabled = true;
                phoneError.classList.remove('d-none');
            }
        });

        // Send OTP
        getCodeBtn.addEventListener('click', () => {
            pendingUserData = {
                fname: document.querySelector('input[name="fname"]').value.trim(),
                lname: document.querySelector('input[name="lname"]').value.trim(),
                phone: phoneInput.value.trim(),
                email: document.querySelector('input[name="email"]').value.trim(),
                password: document.querySelector('input[name="password"]').value.trim(),
                verified: false
            };


            // Hide custom modal
              addUserModalEl.style.display = 'none';


            fetch('../pages/sms-otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=send&phone=${pendingUserData.phone}&fname=${pendingUserData.fname}&lname=${pendingUserData.lname}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        showVerificationModal(pendingUserData.phone, data.otp);
                    } else {
                        Swal.fire(" Error", data.message, "error");
                        // Show custom modal again
                        addUserModalEl.style.display = 'flex';

                    }
                })
                .catch(err => {
                    console.error(err);
                    // Show custom modal again
                     addUserModalEl.style.display = 'flex';

                });
        });

        // Show OTP modal
        function showVerificationModal(phone, otpValue) {
            addUserModalEl.style.display = 'none';
            Swal.fire({
                title: 'Verification Code',
                html: `
            <p id="showOtp" style="font-size:14px; color:#555;">OTP: ${otpValue}</p>
            <input type="text" id="verificationCode" class="swal2-input" placeholder="Enter 6-digit code" maxlength="6" style="margin-bottom:20px; text-align:center; letter-spacing:5px">
            <div style="display:flex; gap:5px; justify-content:flex-end; margin-top:5px;">
                <button id="resendBtn" class="swal2-styled" style="flex:1;">Resend</button>
                <button id="verifyBtn" class="swal2-confirm swal2-styled" style="flex:1;">Verify</button>
            </div>
        `,
                showCloseButton: true,
                showConfirmButton: false,
                didOpen: () => {
                    const popup = Swal.getPopup();
                    const inputField = popup.querySelector('#verificationCode');
                    inputField.focus();
                    inputField.addEventListener('input', () => {
                        inputField.value = inputField.value.replace(/\D/g, '').slice(0, 6);
                    });

                    // Resend OTP
                    popup.querySelector('#resendBtn').addEventListener('click', () => {
                        fetch('../pages/sms-otp.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `action=send&phone=${phone}&fname=${pendingUserData.fname}&lname=${pendingUserData.lname}`
                        })
                            .then(res => res.json())
                            .then(resp => {
                                if (resp.status === "success") {
                                    otpValue = resp.otp || '';
                                    popup.querySelector('#showOtp').textContent = `OTP: ${otpValue}`;
                                    let msg = popup.querySelector('#resendMessage');
                                    if (!msg) {
                                        msg = document.createElement('p');
                                        msg.id = 'resendMessage';
                                        msg.style.color = 'green';
                                        msg.style.fontSize = '13px';
                                        msg.style.marginTop = '5px';
                                        popup.querySelector('#resendBtn').parentElement.appendChild(msg);
                                    }
                                    msg.textContent = 'OTP Resent!';
                                } else {
                                    Swal.fire(' Error resending OTP', '', 'error');
                                }
                            });
                    });




                    // Verify OTP
                    popup.querySelector('#verifyBtn').addEventListener('click', () => {
                        const code = inputField.value.trim();
                        if (code.length !== 6) {
                            Swal.fire("Invalid Code", "OTP must be 6 digits", "error");
                            return;
                        }

                        fetch('../pages/sms-otp.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `action=verify&phone=${phone}&otp=${code}`
                        })
                            .then(res => res.json())
                            .then(result => {

                                if (result.status === "verified") {

                                    Swal.fire("Verified!", "Phone number confirmed!", "success").then(() => {
                                        // ✅ This runs only after clicking OK on Swal

                                        pendingUserData.verified = true;

                                        // ENABLE ADD BUTTON
                                        const addBtn = document.getElementById("addBtn");
                                        addBtn.disabled = false;
                                        addBtn.style.backgroundColor = "#0d6efd";
                                        addBtn.style.cursor = "pointer";

                                        // SHOW THE ADD USER MODAL AFTER CONFIRMATION
                                        addUserModalEl.style.display = 'flex';
                                    });

                                } else {

                                    Swal.fire("Incorrect Code", result.message, "error").then(() => {
                                        // SHOW MODAL AFTER OK EVEN ON ERROR
                                        addUserModalEl.style.display = 'flex';
                                    });
                                }
                            });
                    });

                }
            });
        }

        // Prevent submission if phone not verified
        addFacultyForm.addEventListener('submit', (e) => {
            if (!pendingUserData.verified) {
                e.preventDefault();
                Swal.fire(" Verification Required", "Please verify your phone number first.", "warning");
            }
        });

        const phoneInputAdmin = document.getElementById('phoneAdmin');
        const getCodeBtnAdmin = document.getElementById('getCodeBtnAdmin');
        const phoneErrorAdmin = document.getElementById('phoneErrorAdmin');
        const addAdminForm = document.getElementById('addAdminForm');
        const addAdminModalEl = document.getElementById('addAdminModal');


        const closeAddAdminBtns = document.querySelectorAll('#closeAddAdminModal, #closeAddAdminFooter');

        closeAddAdminBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                addAdminModalEl.style.display = 'none';
            });
        });



        let pendingAdminData = {
            verified: false
        };

        // Enable/disable Get Code button
        phoneInputAdmin.addEventListener('input', () => {
            const phoneVal = phoneInputAdmin.value.trim();
            const regex = /^(09\d{9}|639\d{9})$/;
            if (regex.test(phoneVal)) {
                getCodeBtnAdmin.disabled = false;
                phoneErrorAdmin.classList.add('d-none');
            } else {
                getCodeBtnAdmin.disabled = true;
                phoneErrorAdmin.classList.remove('d-none');
            }
        });

        // Send OTP
        getCodeBtnAdmin.addEventListener('click', () => {
            pendingAdminData = {
                fname: document.querySelector('#addAdminForm input[name="fname"]').value.trim(),
                lname: document.querySelector('#addAdminForm input[name="lname"]').value.trim(),
                phone: phoneInputAdmin.value.trim(),
                email: document.querySelector('#addAdminForm input[name="email"]').value.trim(),
                password: document.querySelector('#addAdminForm input[name="password"]').value.trim(),
                verified: false
            };

                addAdminModalEl.style.display = 'none';



            fetch('../pages/sms-otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=send&phone=${pendingAdminData.phone}&fname=${pendingAdminData.fname}&lname=${pendingAdminData.lname}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        showAdminVerificationModal(pendingAdminData.phone, data.otp);
                    } else {
                        Swal.fire("Error", data.message, "error");
                        addAdminModalEl.style.display = 'flex';

                    }
                })
                .catch(err => {
                    console.error(err);
                     addAdminModalEl.style.display = 'flex';

                });
        });

        // OTP verification function (similar to Add User)
        function showAdminVerificationModal(phone, otpValue) {
            Swal.fire({
                title: 'Verification Code',
                html: `
            <p id="showOtp" style="font-size:14px; color:#555;">OTP: ${otpValue}</p>
            <input type="text" id="verificationCodeAdmin" class="swal2-input" placeholder="Enter 6-digit code" maxlength="6" style="margin-bottom:20px; text-align:center; letter-spacing:5px">
            <div style="display:flex; gap:5px; justify-content:flex-end; margin-top:5px;">
                <button id="resendBtnAdmin" class="swal2-styled" style="flex:1;">Resend</button>
                <button id="verifyBtnAdmin" class="swal2-confirm swal2-styled" style="flex:1;">Verify</button>
            </div>
        `,
                showCloseButton: true,
                showConfirmButton: false,
                didOpen: () => {
                    const popup = Swal.getPopup();
                    const inputField = popup.querySelector('#verificationCodeAdmin');
                    inputField.focus();
                    inputField.addEventListener('input', () => {
                        inputField.value = inputField.value.replace(/\D/g, '').slice(0, 6);
                    });

                    // Resend OTP
                    popup.querySelector('#resendBtnAdmin').addEventListener('click', () => {
                        fetch('../pages/sms-otp.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `action=send&phone=${phone}&fname=${pendingAdminData.fname}&lname=${pendingAdminData.lname}`
                        })
                            .then(res => res.json())
                            .then(resp => {
                                if (resp.status === "success") {
                                    otpValue = resp.otp || '';
                                    popup.querySelector('#showOtp').textContent = `OTP: ${otpValue}`;
                                } else Swal.fire('Error resending OTP', '', 'error');
                            });
                    });

                    // Verify OTP
                    popup.querySelector('#verifyBtnAdmin').addEventListener('click', () => {
                        const code = inputField.value.trim();
                        if (code.length !== 6) {
                            Swal.fire("Invalid Code", "OTP must be 6 digits", "error");
                            return;
                        }
                        fetch('../pages/sms-otp.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `action=verify&phone=${phone}&otp=${code}`
                        })
                            .then(res => res.json())
                            .then(result => {
                                if (result.status === "verified") {
                                    Swal.fire("Verified!", "Phone number confirmed!", "success").then(() => {
                                        pendingAdminData.verified = true;

                                        const addBtn = document.getElementById("addAdminBtn");
                                        addBtn.disabled = false;
                                        addBtn.style.backgroundColor = "#198754";
                                        addBtn.style.cursor = "pointer";

                                        // Show the modal AFTER Swal closes
                                        addAdminModalEl.style.display = 'flex';
                                    });

                                } else Swal.fire("Incorrect Code", result.message, "error");
                            });
                    });
                }
            });
        }

        // Prevent submission if phone not verified
        addAdminForm.addEventListener('submit', (e) => {
            if (!pendingAdminData.verified) {
                e.preventDefault();
                Swal.fire("Verification Required", "Please verify your phone number first.", "warning");
            }
        });
