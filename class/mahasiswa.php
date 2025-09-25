<?php
require_once("classParent.php");

class Mahasiswa extends classParent
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getMahasiswa()
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM mahasiswa");
        $stmt->execute();
        return $stmt->get_result();
    }

    public function insertMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension)
    {
        $stmt = $this->mysqli->prepare("INSERT INTO mahasiswa (nrp, nama, gender, tanggal_lahir, angkatan, foto_extention) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension);

        if ($stmt->execute()) {
            header("Location: tabelmahasiswa.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $this->mysqli->close();
    }
}
