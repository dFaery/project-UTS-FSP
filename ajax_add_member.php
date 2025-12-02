<?php
require_once("class/Grup.php");
$grupObj = new Grup();

if(isset($_POST['username']) && isset($_POST['idgrup'])){
    if($grupObj->addMember($_POST['idgrup'], $_POST['username'])){
        echo "ok"; 
    } else {
        echo "fail";
    }
}
?>