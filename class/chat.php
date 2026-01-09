<?php
require_once("classParent.php");

class Chat extends classParent
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllChatByThreadId($idthread){
        $stmt = $this->mysqli->prepare("SELECT * FROM chat WHERE idthread = ?");
        $stmt->bind_param("i", $idthread);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function insertChat($idthread, $username_pembuat, $isi){
        $tanggal_pembuatan = new DateTime('now');
        $tanggal_pembuatan_format = $tanggal_pembuatan->format('Y-m-d H:i:s'); 
        $stmt = $this->mysqli->prepare("INSERT INTO chat (idthread, username_pembuat, isi, tanggal_pembuatan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $idthread, $username_pembuat, $isi, $tanggal_pembuatan_format);
        $stmt->execute();
        $stmt->close();
    }
}
?>