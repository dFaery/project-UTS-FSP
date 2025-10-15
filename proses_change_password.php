<?php
require_once("class/akun.php");

$akun = new Akun();
if(isset($_POST['change_password'])){
    $username = $_POST['username'];
    $new_password = $_POST['password'];
    $re_enter_password = $_POST['re_enter_password'];
    if($new_password!=$re_enter_password){
        header("location: change_password.php?err=PWD");
    }

    if ($akun->changePassword($username, $new_password)) {
        header("Location: index.php?status=success");
        exit;
    } else {
        header("Location: index.php?status=fail");
        exit;
    }
}

?>