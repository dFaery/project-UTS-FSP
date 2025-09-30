<?php
require_once("classParent.php");

class Akun extends classParent
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login($username, $password)
    {
        $sql_login = "SELECT username, password, isadmin FROM akun WHERE username=? AND password=?;";
        $stmt_login = $this->mysqli->prepare($sql_login);
        $stmt_login->bind_param("ss", $username, $password);
        $stmt_login->execute();
        return $stmt_login->get_result();

        $stmt_login->close();
    }

    public function insertAkunDosen($username, $password, $npk_dosen)
    {
        // $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->mysqli->prepare("INSERT INTO akun (username, password, npk_dosen) VALUES (?, ?, ?)");
        // $stmt->bind_param("sss", $username, $hash_password, $npk_dosen);
        $stmt->bind_param("sss", $username, $password, $npk_dosen);
        $stmt->execute();
        $stmt->close();
    }

    public function insertAkunMahasiswa($username, $password, $nrp_mahasiswa)
    {
        // $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->mysqli->prepare("INSERT INTO akun (username, password, nrp_mahasiswa) VALUES (?, ?, ?)");
        // $stmt->bind_param("sss", $username, $hash_password, $nrp_mahasiswa);
        $stmt->bind_param("sss", $username, $password, $nrp_mahasiswa);
        $stmt->execute();
        $stmt->close();
    }
}
