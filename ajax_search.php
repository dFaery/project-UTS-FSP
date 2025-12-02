<?php
require_once("class/Grup.php"); 
$grupObj = new Grup();

if(isset($_POST['keyword']) && isset($_POST['idgrup'])) {
    $res = $grupObj->searchCandidateMhs($_POST['keyword'], $_POST['idgrup']);

    if($res->num_rows > 0){
        echo "<table style='width:100%; margin:0; font-size:14px;'>";
        while($row = $res->fetch_assoc()){
            echo "<tr style='border-bottom:1px solid #eee;'>
                <td style='padding:8px;'>
                    <b>{$row['nama']}</b><br>
                    <span style='color:gray; font-size:12px;'>{$row['nrp']}</span>
                </td>
                <td style='text-align:right;'>
                    <button type='button' class='add-btn-ajax' data-user='{$row['username']}' 
                            style='background:#3498db; color:white; border:none; padding:5px 10px; cursor:pointer; border-radius:3px;'>
                        + Tambah
                    </button>
                </td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "<div style='padding:10px; color:#777; text-align:center;'>Tidak ditemukan / Sudah bergabung</div>";
    }
}
?>