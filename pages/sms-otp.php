<?php
session_start();

// OTP Generator
function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

if(isset($_POST['phone']) && isset($_POST['fname']) && isset($_POST['lname'])) {

    $phone = $_POST['phone'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];

    $otp = generateVerificationCode();
    $_SESSION['otp_code'] = $otp; // Store OTP to validate later

    $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
    $message = "Hi $fname $lname, your OTP verification code is: $otp";

    $data = [
        'api_token' => 'ee14530521332383899b85b0098b3e92da7b116f',
        'message' => $message,
        'phone_number' => $phone
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
} else {
    echo json_encode(['error' => 'Missing parameters']);
}
?>
