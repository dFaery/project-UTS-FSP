<?php
require_once("classParent.php");

class Thread extends classParent
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getThreadByGroupId($idgrup){
        $stmt = $this->mysqli->prepare("SELECT * FROM thread WHERE idgrup = ?");
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public function getThreadById($idThread){
        $stmt = $this->mysqli->prepare("SELECT * FROM thread WHERE idthread = ?");
        $stmt->bind_param("i", $idThread);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function insertThreadByGroupId($username_pembuat, $idgrup, $status){
        $tanggal_pembuatan = new DateTime('now');
        $tanggal_pembuatan_format = $tanggal_pembuatan->format('Y-m-d H:i:s');        
        $sql = "INSERT INTO thread (username_pembuat, idgrup, tanggal_pembuatan, status) VALUES (?,?,?,?)";        
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("siss", $username_pembuat, $idgrup, $tanggal_pembuatan_format, $status);
        return $stmt->execute();
    }

}
?>