<?php
include('connection.php');
if(isset($_POST['resetPasswordBtn'])){
    $useremail = filter_var($_POST['flduseremail'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
        header('location: ../resetpassword.php?error=Invalid Email Format');
        exit;
    }
    
    //3.1 check whether there is a user with this email or not
    $stmt = $conn->prepare("SELECT flduserid, flduserfirstname FROM users WHERE flduseremail = ? LIMIT 1");
    $stmt->bind_param('s',$useremail);
    if($stmt->execute()){
        $stmt->bind_result($userid,$userfirstname);
        $stmt->store_result();
    } else{
        $stmt->close();
        header('../resetpassword.php?error=Something Went Wrong!! Contact Support Team.');
        exit;
    }

    //3.1.1 if there is a user already registered with this email
    if($stmt->num_rows() == 1){
        $stmt->fetch();
        $stmt->close();

        // Generate a random 6-digit OTP
        $otpcode = rand(100000, 999999);
        // Encrypt OTP using SHA256
        $_SESSION['fldverifyotpcode'] = hash('sha256', $otpcode);

        // Send Email To User
        $to = $useremail;
        $subject = "Reset Password OTP Code";
        $message = "Hello $userfirstname,\n\nHere is your Reset Password OTP Code: $otpcode. \n\nBest regards,\nHealthcare Friends Team";
        // Additional headers for better email security
        $headers = array(
            'From: info@fcsholdix.co.za',
            'X-Mailer: PHP/' . phpversion(),
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8'
        );
        $headers = implode("\r\n", $headers);
        
        if(mail($to, $subject, $message, $headers)){
            header('location: ../resetpasswordverification.php?success=Email Has Been Sent With The OTP Code. Please Enter The OTP Code Before It Expires.&flduseremail='.$useremail);
            exit;
        } else {
            header('location: ../resetpassword.php?error=Failed To Send Email Verification To '.$useremail);
            exit;
        }
    } else{//3.1.2 if no user registered with this email before
        header('location: ../resetpassword.php?error=Email Not Found!');
        exit;
    }
}