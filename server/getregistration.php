<?php
include('connection.php');
if(isset($_POST['registrationBtn'])){
  $userfirstname = $_POST['flduserfirstname'];
  $userlastname = $_POST['flduserlastname'];
  $userstreetaddress = $_POST['flduserstreetaddress'];
  $userlocalarea = $_POST['flduserlocalarea'];
  $usercity = $_POST['fldusercity'];
  $userzone = $_POST['flduserzone'];
  $usercountry = $_POST['fldusercountry'];
  $userpostalcode = $_POST['flduserpostalcode'];
  
  $useremail = filter_var($_POST['flduseremail'], FILTER_SANITIZE_EMAIL);
  if (!filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
      header('location: ../registration.php?error=Invalid Email Format');
      exit;
  }
  
  $userphonenumber = $_POST['flduserphonenumber'];
  $userpassword = $_POST['flduserpassword'];
  $userconfirmpassword = $_POST['flduserconfirmpassword'];

  //if password dont match
  if($userpassword !== $userconfirmpassword){
    header('location: ../registration.php?error=Passwords Do Not Match');
    exit;
  }
  else if(strlen($userpassword) < 8)
  {//if password is less than 8 characters
    header('location: ../registration.php?error=Password Must Be Atleast 8 Characters');
    exit;
  }
  else{//if there is no error
    //check whether there is a user with this email or not
    $stmt = $conn->prepare("SELECT count(*) FROM users WHERE flduseremail=?");
    $stmt->bind_param('s',$useremail);
    if($stmt->execute()){
      $stmt->bind_result($num_rows);
      $stmt->store_result();
      $stmt->fetch();
    }
    else{
      header('location: ../registration.php?error=Something Went Wrong, Try Again!!');
      exit;
    }

    //if there is a user already registered with this email
    if($num_rows != 0){
      header('location: ../registration.php?error=User With This Email Already Exists!');
      exit;
    }
    else{//if no user registered with this email before
      // Use PHP's built-in password hashing function
      $userpassword = password_hash($userpassword, PASSWORD_DEFAULT);
      
      // Generate a random 6-digit OTP
      $otpcode = rand(100000, 999999);
      // Encrypt OTP using SHA256
      $_SESSION['fldverifyotpcode'] = hash('sha256', $otpcode);
      
      // Send Email To User
      $to = $useremail;
      $subject = "Registration OTP Code";
      $message = "Hello $userfirstname,\n\nHere is your Registration OTP Code: $otpcode. \n\nBest regards,\nHealthcare Friends Team";
      // Additional headers for better email security
      $headers = array(
        'From: info@fcsholdix.co.za',
        'X-Mailer: PHP/' . phpversion(),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8'
      );
      $headers = implode("\r\n", $headers);
      
      if(mail($to, $subject, $message, $headers)){
        // Email sent successfully. Go To Email Verification Page
        header('location: ../registrationverification.php?success=Email Has Been Sent With The OTP Code. Please Enter The OTP Code Before It Expires.&flduserfirstname='.$userfirstname.'&flduserlastname='.$userlastname.'&fldusercountry='.$usercountry.'&flduserzone='.$userzone.'&fldusercity='.$usercity.'&flduserlocalarea='.$userlocalarea.'&flduserstreetaddress='.$userstreetaddress.'&flduserpostalcode='.$userpostalcode.'&flduseremail='.$useremail.'&flduserphonenumber='.$userphonenumber.'&flduserpassword='.$userpassword);
        exit;
      } else {
        // Email sending failed
        header('location: ../registration.php?error=Failed To Send Email Verification To '.$useremail);
        exit;
      }
    }
  }
}