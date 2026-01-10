<?php
session_start();
require_once("../class/Chat.php");

if (isset($_POST['idThread']) && isset($_POST['isi'])) {

    $idthread = $_POST['idThread'];
    $isi = $_POST['isi'];
    $username = $_SESSION['user'];

    $chatObj = new Chat();
    $chatObj->insertChat($idthread, $username, $isi);

    echo "OK";
}

var_dump($_POST);
exit;
?>