<?php
require_once("class/Grup.php");
$grupObj = new Grup();

if(isset($_POST['keyword']) && isset($_POST['idgrup'])) {
    $res = $grupObj->searchCandidateMhs($_POST['keyword'], $_POST['idgrup']);

    if($res->num_rows > 0){
        echo "<table style='width:100%; margin:0;'>";
        while($row = $res->fetch_assoc()){
            echo "<tr style='border-bottom:1px solid #eee;'>
                <td style='padding:5px;'>{$row['nama']} ({$row['nrp']})</td>
                <td style='text-align:right;'><button type='button' class='add-btn-ajax' data-user='{$row['username']}' style='background:#3498db; color:white; border:none; padding:3px 8px; cursor:pointer;'>+</button></td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "<div style='padding:10px; color:#777;'>Tidak ditemukan / Sudah terdaftar</div>";
    }
}
?>