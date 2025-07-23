<?php
include('connection.php');
if(isset($_POST['loginBtn'])){
    $useremail = filter_var($_POST['flduseremail'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
        header('location: ../login.php?error=Invalid Email Format');
        exit;
    }

    $userpassword = $_POST['flduserpassword'];

    // Get the user data
    $stmt = $conn->prepare("SELECT flduserfirstname, flduseremail, flduserpassword FROM users WHERE flduseremail = ? LIMIT 1");
    $stmt->bind_param('s', $useremail);
    
    if($stmt->execute()){
        $stmt->bind_result($userfirstname, $useremail, $hashedPasswordFromDB);
        $stmt->store_result();
        
        if($stmt->num_rows() == 1){
            $stmt->fetch();
            
            // Verify password
            if(password_verify($userpassword, $hashedPasswordFromDB)){
                // Password matches, generate OTP Code
                $otpcode = rand(100000, 999999);
                $_SESSION['fldverifyotpcode'] = hash('sha256', $otpcode);

                // Send Email To User
                $to = $useremail;
                $subject = "Login OTP Code";
                $message = "Hello $userfirstname,\n\nHere is your Login OTP Code: $otpcode. \n\nBest regards,\nHealthcare Friends Team";
                // Additional headers for better email security
                $headers = array(
                    'From: info@fcsholdix.co.za',
                    'X-Mailer: PHP/' . phpversion(),
                    'MIME-Version: 1.0',
                    'Content-Type: text/plain; charset=UTF-8'
                );
                $headers = implode("\r\n", $headers);
                
                if(mail($to, $subject, $message, $headers)){
                    header('location: ../loginverification.php?success=Email Has Been Sent With The OTP Code. Please Enter The OTP Code Before It Expires.&flduseremail='.$useremail);
                    exit;
                } else {
                    header('location: ../login.php?error=Failed To Send Email Verification To '.$useremail);
                    exit;
                }
            } else {
                // Password doesn't match
                header('location: ../login.php?error=Invalid Password!');
                exit;
            }
        } else {
            // Email not found
            header('location: ../login.php?error=Invalid Email!');
            exit;
        }
    } else {
        header('location: ../login.php?error=Could Not Login At The Moment');
        exit;
    }
}