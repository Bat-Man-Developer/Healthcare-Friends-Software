<?php
include('connection.php');
if(isset($_SESSION['logged_in'])){
  if(isset($_GET['logout']) && $_GET['logout'] == 1){
    //Unset Úser Session
    unset($_SESSION['flduserid']);
    unset($_SESSION['flduserimage']);
    unset($_SESSION['flduserfirstname']);
    unset($_SESSION['flduserlastname']);
    unset($_SESSION['flduserstreetaddress']);
    unset($_SESSION['flduserlocalarea']);
    unset($_SESSION['fldusercity']);
    unset($_SESSION['flduserzone']);
    unset($_SESSION['fldusercountry']);
    unset($_SESSION['flduserpostalcode']);
    unset($_SESSION['flduseremail']);
    unset($_SESSION['flduserphonenumber']);
    unset($_SESSION['flduseridnumber']);
    unset($_SESSION['logged_in']);
    //Go to login
    header('location: ../login.php?error=User Logged Out!');
    exit;
  }
}