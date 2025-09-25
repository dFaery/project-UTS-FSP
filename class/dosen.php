<?php
require_once("classParent.php");

class Dosen extends classParent
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDosen()
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM dosen");
        $stmt->execute();
        return $stmt->get_result();
    }

    public function insertDosen($npk, $nama, $foto_extension)
    {
        $stmt = $this->mysqli->prepare("INSERT INTO dosen (npk, nama, foto_extension) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $npk, $nama, $foto_extension);
        if ($stmt->execute()) {
            // Jika berhasil, redirect kembali ke halaman tabel dosen
            header("Location: tabeldosen.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $this->mysqli->close();
    }
}
