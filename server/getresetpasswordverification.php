<?php
include('connection.php');
if(isset($_POST['resetPasswordVerificationBtn'])){
    // Rate Limiting
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_login_attempt'] = time();
    }

    if ($_SESSION['login_attempts'] > 5 && (time() - $_SESSION['last_login_attempt']) < 300) {
        session_destroy();
        unset($_POST);
        unset($_GET);
        header('location: ../resetpassword.php?error=Too Many Attempts');
        exit;
    }

    $_SESSION['login_attempts']++;
    $_SESSION['last_login_attempt'] = time();

    $useremail = filter_var($_POST['flduseremail'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
        header('location: ../resetpasswordverification.php?error=Invalid Email Format&flduseremail='.$useremail);
        exit;
    }

    // When creating/updating password
    $userpassword = $_POST['flduserpassword'];
    $userconfirmpassword = $_POST['flduserconfirmpassword'];

    // Encrypt OTP using SHA256
    $userotpcode = $_POST['flduserotpcode'];
    $userotpcode = hash('sha256', $userotpcode);
    
    $verifyotpcode = $_SESSION['fldverifyotpcode'];

    if($verifyotpcode ==  $userotpcode){
        //1. if password dont match
        if($userpassword !== $userconfirmpassword){
            header('location: ../resetpasswordverification.php?error=Passwords Do Not Match&flduseremail='.$useremail);
            exit;
        }
        else if(strlen($userpassword) < 8)
        {//2. if password is less than 8 characters
            header('location: ../resetpasswordverification.php?error=Password Must Be Atleast 8 Characters&flduseremail='.$useremail);
            exit;
        }
        else{//3. no errors
            // Use PHP's built-in password hashing function
            $userpassword = password_hash($userpassword, PASSWORD_DEFAULT);
            
            //3.1 check whether there is a user with this email or not
            $stmt = $conn->prepare("SELECT flduserid, flduserfirstname FROM users WHERE flduseremail = ? LIMIT 1");
            $stmt->bind_param('s',$useremail);
            if($stmt->execute()){
                $stmt->bind_result($userid, $userfirstname);
                $stmt->store_result();
            }
            else{
                $stmt->close();
                header('../resetpasswordverification.php?error=Something Went Wrong. Contact Support Team.&flduseremail='.$useremail);
                exit;
            }

            //3.1.1 if there is a user already registered with this email
            if($stmt->num_rows() == 1){
                $stmt->fetch();
                $stmt->close();
                
                $stmt1 = $conn->prepare("UPDATE users SET flduserpassword=? WHERE flduseremail=?");
                $stmt1->bind_param('ss',$userpassword,$useremail);
                if($stmt1->execute()){
                    // Initialize Rate Limit
                    $_SESSION['login_attempts'] = 0;
                    $_SESSION['last_login_attempt'] = time();

                    // Send Email To User
                    $to = $useremail;
                    $subject = "Reset Password Successful";
                    $message = "Hello $userfirstname,\n\nYou have successfully reset your password.\n\nBest regards,\nHealthcare Friends Team";
                    // Additional headers for better email security
                    $headers = array(
                        'From: info@fcsholdix.co.za',
                        'X-Mailer: PHP/' . phpversion(),
                        'MIME-Version: 1.0',
                        'Content-Type: text/plain; charset=UTF-8'
                    );
                    $headers = implode("\r\n", $headers);
                    
                    if(mail($to, $subject, $message, $headers)){
                        // Email sent successfully. Go To Account Page.
                        unset($_SESSION['fldverifyotpcode']);
                        header('location: ../login.php?success=Password Reset Successful');
                        exit;
                    } else {
                        // Email sending failed
                        unset($_SESSION['fldverifyotpcode']);
                        header('location: ../login.php?error=Failed To Send Login Email To '.$useremail);
                        exit;
                    }

                } else{
                    header('location: ../resetpasswordverification.php?error=Something Went Wrong!! Contact Support Team.&flduseremail='.$useremail);
                    exit;
                }
            }
            else{//3.1.2 if no user registered with this email before
                header('location: ../resetpasswordverification.php?error=Email Not Found!&flduseremail='.$useremail);
                exit;
            }
        }
    } else{
        header('location: ../resetpasswordverification.php?error=OTP Code Is Incorrect!&flduseremail='.$useremail);
        exit;
    }
}