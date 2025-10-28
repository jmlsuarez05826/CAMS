<?php
require_once 'camsdatabase.php';
require_once 'cams-sp.php';

class PhoneVerification {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function generateVerificationOTP() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function storeVerificationOTP($phone, $otp) {
        try {
            // Delete old OTP if any (avoid duplicates)
            $this->conn->prepare("DELETE FROM phone_verifications WHERE PhoneNumber = :phone")
                       ->execute([':phone' => $phone]);
    
            // Insert new OTP
            $stmt = $this->conn->prepare("
                INSERT INTO phone_verifications (PhoneNumber, OTP) 
                VALUES (:phone, :otp)
            ");
            return $stmt->execute([':phone' => $phone, ':otp' => $otp]);
        } catch (PDOException $e) {
            error_log("DB Error (storeVerificationOTP): " . $e->getMessage());
            return false;
        }
    }
    
    public function verifyPhone($phone, $otp) {
        try {
            $stmt = $this->conn->prepare("
                SELECT OTP FROM phone_verifications 
                WHERE PhoneNumber = :phone 
                  AND created_at >= (NOW() - INTERVAL 5 MINUTE)
            ");
            $stmt->execute([':phone' => $phone]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($row && trim($row['otp']) == trim($otp)) {
                // Delete OTP after successful verification
                $this->conn->prepare("DELETE FROM phone_verifications WHERE PhoneNumber = :phone")
                           ->execute([':phone' => $phone]);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("DB Error (verifyPhone): " . $e->getMessage());
            return false;
        }
    }
    

    public function sendSMS($fname, $lname, $number, $otp) {
        // ✅ Toggle this to false when you want to send real SMS
        $testMode = true;

        if ($testMode) {
            // 🔹 Fake response for testing (no real SMS sent)
            return json_encode([
                'status' => 'success',
                'test_mode' => true,
                'message' => "Simulated sending OTP {$otp} to {$number}",
                'otp' => $otp // ✅ Include OTP in response
            ]);
        }

        // 🔹 Real API call (runs only when testMode = false)
        $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
        $apiToken = 'YOUR_API_TOKEN_HERE';
        $message = sprintf("Hi %s %s, your verification code is %s. - Secret App", $fname, $lname, $otp);

        $data = [
            'api_token' => $apiToken,
            'message' => $message,
            'PhoneNumber' => $number
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $verification = new PhoneVerification();

    if ($action === 'send') {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $phone = $_POST['phone'];
        $otp = $verification->generateVerificationOTP();

        if ($verification->storeVerificationOTP($phone, $otp)) {
            $response = json_decode($verification->sendSMS($fname, $lname, $phone, $otp), true);

            // ✅ Show OTP directly in alert for testing mode
            if (isset($response['test_mode']) && $response['test_mode'] === true) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'OTP sent successfully!',
                    'otp' => $response['otp'], // send OTP to front-end
                    'sms_response' => $response
                ]);
            } else {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'OTP sent successfully!',
                    'sms_response' => $response
                ]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to store OTP']);
        }
        exit;
    }

    if ($action === 'verify') {
        $phone = $_POST['phone'];
        $otp = $_POST['otp'];

        if ($verification->verifyPhone($phone, $otp)) {
            echo json_encode(['status' => 'success', 'message' => 'Phone verified successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid OTP']);
        }
        exit;
    }
}
?>
