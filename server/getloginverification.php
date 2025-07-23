<?php
include('connection.php');
if(isset($_POST['loginVerificationBtn'])){
    // Rate Limiting
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_login_attempt'] = time();
    }

    if ($_SESSION['login_attempts'] > 5 && (time() - $_SESSION['last_login_attempt']) < 300) {
        session_destroy();
        unset($_POST);
        unset($_GET);
        header('location: ../login.php?error=Too Many Attempts');
        exit;
    }

    $_SESSION['login_attempts']++;
    $_SESSION['last_login_attempt'] = time();

    $useremail = filter_var($_POST['flduseremail'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
        header('location: ../login.php?error=Invalid Email Format');
        exit;
    }
    
    // Encrypt OTP using SHA256
    $userotpcode = $_POST['flduserotpcode'];
    $userotpcode = hash('sha256', $userotpcode);
    
    $verifyotpcode = $_SESSION['fldverifyotpcode'];
  
    if($verifyotpcode ==  $userotpcode){
        $stmt = $conn->prepare("SELECT flduserid,flduserimage,flduserfirstname,flduserlastname,flduserstreetaddress,flduserlocalarea,fldusercity,flduserzone,fldusercountry,flduserpostalcode,flduseremail,flduserphonenumber,flduseridnumber,flduserpassword FROM users WHERE flduseremail = ? LIMIT 1");
        $stmt->bind_param('s',$useremail);
        if($stmt->execute()){
            $stmt->bind_result($userid,$userimage,$userfirstname,$userlastname,$userstreetaddress,$userlocalarea,$usercity,$userzone,$usercountry,$userpostalcode,$useremail,$userphonenumber,$useridnumber,$userpassword);
            $stmt->store_result();
            //If user is found in database
            if($stmt->num_rows() == 1){
                $stmt->fetch();
                // Initialize Rate Limit
                $_SESSION['login_attempts'] = 0;
                $_SESSION['last_login_attempt'] = time();

                //Set Users Session
                $_SESSION['flduserid'] = $userid;
                $_SESSION['flduserimage'] = $userimage;
                $_SESSION['flduserfirstname'] = $userfirstname;
                $_SESSION['flduserlastname'] = $userlastname;
                $_SESSION['flduserstreetaddress'] = $userstreetaddress;
                $_SESSION['flduserlocalarea'] = $userlocalarea;
                $_SESSION['fldusercity'] = $usercity;
                $_SESSION['flduserzone'] = $userzone;
                $_SESSION['fldusercountry'] = $usercountry;
                $_SESSION['flduserpostalcode'] = $userpostalcode;
                $_SESSION['flduseremail'] = $useremail;
                $_SESSION['flduserphonenumber'] = $userphonenumber;
                $_SESSION['logged_in'] = true;

                
                // Send Email To User
                $to = $useremail;
                $subject = "Successful Login";
                $message = "Hello $userfirstname,\n\nYou have successfully logged in to your account.\n\nBest regards,\nHealthcare Friends Team";
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
                    unset($_POST);
                    unset($_GET);
                    header('location: ../dashboard.php?success=Logged In Successfully');
                    exit;
                } else {
                    // Email sending failed
                    unset($_SESSION['fldverifyotpcode']);
                    unset($_POST);
                    unset($_GET);
                    header('location: ../dashboard.php?error=Failed To Send Login Email To '.$useremail);
                    exit;
                }
            } else {
                // Email Not Found
                header('location: ../loginverification.php?error=Email Not Found!&flduseremail='.$useremail);
                exit;
            }
        } else{//OTP Code is Wrong
            //Go To Login Verification Page
            header('location: ../loginverification.php?error=Could Not Login At The Moment!&flduseremail='.$useremail);
            exit;
        }
    } else{
        header('location: ../loginverification.php?error=OTP Code Is Incorrect!&flduseremail='.$useremail);
        exit;
    }
}