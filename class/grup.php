<?php
require_once("classParent.php");

class Grup extends classParent
{
    // Cek Dosen
    public function isDosen($username) {
        $stmt = $this->mysqli->prepare("SELECT npk_dosen FROM akun WHERE username = ? AND npk_dosen IS NOT NULL");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function createGrup($username, $nama, $deskripsi, $jenis) {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $kode = substr(str_shuffle($chars), 0, 6); 

        $stmt = $this->mysqli->prepare("INSERT INTO grup (username_pembuat, nama, deskripsi, tanggal_pembentukan, jenis, kode_pendaftaran) VALUES (?, ?, ?, NOW(), ?, ?)");
        $stmt->bind_param("sssss", $username, $nama, $deskripsi, $jenis, $kode);
        if($stmt->execute()) return $kode;
        return false;
    }

    public function getGrupByDosen($username) {
        $stmt = $this->mysqli->prepare("SELECT * FROM grup WHERE username_pembuat = ? ORDER BY tanggal_pembentukan DESC");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getGrupByMahasiswa($username) {
        $sql = "SELECT g.* FROM grup g 
                JOIN member_grup mg ON g.idgrup = mg.idgrup 
                WHERE mg.username = ? ORDER BY g.tanggal_pembentukan DESC";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getGrupById($idgrup) {
        $stmt = $this->mysqli->prepare("SELECT * FROM grup WHERE idgrup = ?");
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function joinGrup($username, $kode) {
        // 1. Cek Kode
        $stmt = $this->mysqli->prepare("SELECT idgrup FROM grup WHERE kode_pendaftaran = ?");
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if($row = $res->fetch_assoc()){
            $idgrup = $row['idgrup'];
            // 2. Cek Duplikasi
            if(!$this->isMember($idgrup, $username)){
                return $this->addMember($idgrup, $username);
            }
        }
        return false;
    }

    // --- MEMBER MANAGEMENT (Encapsulation Fix) ---

    public function getMembers($idgrup) {
        $sql = "SELECT m.nrp, m.nama, a.username 
                FROM member_grup mg 
                JOIN akun a ON mg.username = a.username 
                JOIN mahasiswa m ON a.nrp_mahasiswa = m.nrp 
                WHERE mg.idgrup = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function isMember($idgrup, $username) {
        $stmt = $this->mysqli->prepare("SELECT * FROM member_grup WHERE idgrup = ? AND username = ?");
        $stmt->bind_param("is", $idgrup, $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function addMember($idgrup, $username) {
        $stmt = $this->mysqli->prepare("INSERT INTO member_grup (idgrup, username) VALUES (?, ?)");
        $stmt->bind_param("is", $idgrup, $username);
        return $stmt->execute();
    }

    public function removeMember($idgrup, $username) {
        $stmt = $this->mysqli->prepare("DELETE FROM member_grup WHERE idgrup = ? AND username = ?");
        $stmt->bind_param("is", $idgrup, $username);
        return $stmt->execute();
    }

    public function searchCandidateMhs($keyword, $idgrup) {
        $key = "%" . $keyword . "%";
        // Logic: Cari mahasiswa yg BELUM ada di grup ini
        $sql = "SELECT m.nama, m.nrp, a.username 
                FROM mahasiswa m 
                JOIN akun a ON m.nrp = a.nrp_mahasiswa
                WHERE (m.nama LIKE ? OR m.nrp LIKE ?)
                AND a.username NOT IN (SELECT username FROM member_grup WHERE idgrup = ?)
                LIMIT 5";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ssi", $key, $key, $idgrup);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>