<?php
require_once("classParent.php");

class Grup extends classParent
{
    public function generateKode() {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($chars), 0, 6);
    }

    public function isDosen($username) {
        $stmt = $this->mysqli->prepare("SELECT npk_dosen FROM akun WHERE username = ? AND npk_dosen IS NOT NULL");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function createGrup($username, $nama, $deskripsi, $jenis) {
        $kode = $this->generateKode();
        $stmt = $this->mysqli->prepare("INSERT INTO grup (username_pembuat, nama, deskripsi, tanggal_pembentukan, jenis, kode_pendaftaran) VALUES (?, ?, ?, NOW(), ?, ?)");
        $stmt->bind_param("sssss", $username, $nama, $deskripsi, $jenis, $kode);
        if($stmt->execute()){
            $idgrup = $this->mysqli->insert_id;

            $this->addMember($idgrup, $username);
            return $kode;
        }

        return false;
    }

    public function updateGrup($idgrup, $nama, $deskripsi, $jenis) {
        $stmt = $this->mysqli->prepare("UPDATE grup SET nama=?, deskripsi=?, jenis=? WHERE idgrup=?");
        $stmt->bind_param("sssi", $nama, $deskripsi, $jenis, $idgrup);
        return $stmt->execute();
    }
    
    public function getGrupByDosen($username) {
        $stmt = $this->mysqli->prepare("SELECT * FROM grup WHERE username_pembuat = ? ORDER BY tanggal_pembentukan DESC");
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
    
    public function getJoinedGroups($username) {
        $sql = "SELECT g.*, g.username_pembuat FROM grup g 
                JOIN member_grup mg ON g.idgrup = mg.idgrup 
                WHERE mg.username = ? ORDER BY g.tanggal_pembentukan DESC";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function joinGrup($username, $kode) {
        $stmt = $this->mysqli->prepare("SELECT idgrup, jenis, username_pembuat FROM grup WHERE kode_pendaftaran = ?");
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if($row = $res->fetch_assoc()){
            $idgrup = $row['idgrup'];
            $jenis = $row['jenis']; 
            $pembuat = $row['username_pembuat'];

            if($pembuat == $username) {
                return "OWNER";
            }

            if($jenis == 'Privat') {
                return "PRIVAT"; 
            }

            if(!$this->isMember($idgrup, $username)){
                if($this->addMember($idgrup, $username)){
                    return "SUCCESS";
                }
            } else {
                return "ALREADY_MEMBER";
            }
        }
        return "NOT_FOUND";
    }

    public function getAvailablePublicGroups($username) {
        $sql = "SELECT g.*, d.nama as nama_dosen 
                FROM grup g
                JOIN akun a ON g.username_pembuat = a.username
                JOIN dosen d ON a.npk_dosen = d.npk
                WHERE g.jenis = 'Publik'
                AND g.username_pembuat != ? 
                AND g.idgrup NOT IN (SELECT idgrup FROM member_grup WHERE username = ?)
                ORDER BY g.tanggal_pembentukan DESC";
                
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getMembers($idgrup) {
        $sql = "SELECT m.username, 
                CASE 
                    WHEN d.nama IS NOT NULL THEN d.nama 
                    WHEN mhs.nama IS NOT NULL THEN mhs.nama 
                    ELSE m.username 
                END as nama_lengkap,
                CASE 
                    WHEN d.npk IS NOT NULL THEN d.npk 
                    WHEN mhs.nrp IS NOT NULL THEN mhs.nrp 
                    ELSE '-' 
                END as id_nomor,
                CASE 
                    WHEN d.npk IS NOT NULL THEN 'Dosen' 
                    ELSE 'Mahasiswa' 
                END as role
                FROM member_grup m
                LEFT JOIN akun a ON m.username = a.username
                LEFT JOIN dosen d ON a.npk_dosen = d.npk
                LEFT JOIN mahasiswa mhs ON a.nrp_mahasiswa = mhs.nrp
                WHERE m.idgrup = ?"; 
        
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

    public function removeMember($idgrup, $username_target) {
        $grupData = $this->getGrupById($idgrup);
        
        if ($grupData['username_pembuat'] == $username_target) {
            return "OWNER"; 
        }

        $stmt = $this->mysqli->prepare("DELETE FROM member_grup WHERE idgrup = ? AND username = ?");
        $stmt->bind_param("is", $idgrup, $username_target);
        
        if($stmt->execute()){
            return "SUCCESS";
        }
        return "FAIL";
    }

    public function deleteGrup($idgrup, $username_pembuat) {
        $stmt = $this->mysqli->prepare("DELETE FROM grup WHERE idgrup = ? AND username_pembuat = ?");
        $stmt->bind_param("is", $idgrup, $username_pembuat);
        return $stmt->execute();
    }
    
    public function searchCandidateMhs($keyword, $idgrup) {
        $key = "%" . $keyword . "%";
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