<?php
require_once("classParent.php");

class Akun extends classParent
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login($username, $plainpwd)
    {        
        $sql = "SELECT * from akun WHERE username=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $is_authenticated = password_verify($plainpwd, $row['password']);
            return ($is_authenticated) ? $row : false;
        } else {            
            return false;
        }
    }

    public function insertAkunDosen($username, $plainpwd, $npk_dosen)
    {        
        $stmt = $this->mysqli->prepare("INSERT INTO akun (username, password, npk_dosen) VALUES (?, ?, ?)");
        $encrypted_pwd = password_hash($plainpwd, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $username, $encrypted_pwd, $npk_dosen);
        $stmt->execute();
        $stmt->close();
    }

    public function insertAkunMahasiswa($username, $plainpwd, $nrp_mahasiswa)
    {        
        $stmt = $this->mysqli->prepare("INSERT INTO akun (username, password, nrp_mahasiswa) VALUES (?, ?, ?)");
        $encrypted_pwd = password_hash($plainpwd, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $username, $encrypted_pwd, $nrp_mahasiswa);
        $stmt->execute();                
        $stmt->close();
    }    
}
