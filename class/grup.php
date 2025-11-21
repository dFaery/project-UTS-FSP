<?php
require_once("classParent.php");

class Grup extends classParent
{
    public function __construct()
    {
        parent::__construct();
    }

    // Generate Random Code (6 Karakter)
    private function generateKode() {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < 6; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    // Menambah Grup Baru (Dosen)
    public function createGrup($username_pembuat, $nama, $deskripsi, $jenis)
    {
        $kode = $this->generateKode();
        // Pastikan kode unik (looping sederhana check existence)
        // Untuk simplifikasi tugas, kita asumsikan random cukup unik
        
        $stmt = $this->mysqli->prepare("INSERT INTO grup (username_pembuat, nama, deskripsi, tanggal_pembentukan, jenis, kode_pendaftaran) VALUES (?, ?, ?, NOW(), ?, ?)");
        $stmt->bind_param("sssss", $username_pembuat, $nama, $deskripsi, $jenis, $kode);
        
        if($stmt->execute()){
            return $kode; // Kembalikan kode untuk ditampilkan
        }
        return false;
    }

    // Mengambil Grup milik Dosen tertentu
    public function getGrupByDosen($username_dosen)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM grup WHERE username_pembuat = ? ORDER BY tanggal_pembentukan DESC");
        $stmt->bind_param("s", $username_dosen);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Mengambil Grup yang diikuti Mahasiswa
    public function getGrupByMahasiswa($username_mhs)
    {
        $sql = "SELECT g.* FROM grup g 
                JOIN member_grup mg ON g.idgrup = mg.idgrup 
                WHERE mg.username = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $username_mhs);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Detail Grup
    public function getGrupById($idgrup)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM grup WHERE idgrup = ?");
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // --- MANAJEMEN MEMBER ---

    // Ambil list member di grup tertentu (Join ke mahasiswa untuk dapat Nama/NRP)
    public function getMembers($idgrup)
    {
        $sql = "SELECT m.nama, m.nrp, a.username 
                FROM member_grup mg
                JOIN akun a ON mg.username = a.username
                JOIN mahasiswa m ON a.nrp_mahasiswa = m.nrp
                WHERE mg.idgrup = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Cari Mahasiswa untuk ditambahkan (Yang BELUM masuk grup ini)
    public function searchMahasiswaCandidate($keyword, $idgrup)
    {
        $cari = "%" . $keyword . "%";
        // Logic: Cari di tabel mahasiswa, TAPI exclude yang sudah ada di member_grup
        $sql = "SELECT m.nrp, m.nama, m.foto_extention 
                FROM mahasiswa m
                JOIN akun a ON m.nrp = a.nrp_mahasiswa
                WHERE (m.nama LIKE ? OR m.nrp LIKE ?)
                AND a.username NOT IN (SELECT username FROM member_grup WHERE idgrup = ?)
                LIMIT 10";
        
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ssi", $cari, $cari, $idgrup);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Tambah Member (Dosen menambahkan via Search)
    public function addMemberByNRP($idgrup, $nrp)
    {
        // 1. Cari username berdasarkan NRP
        $stmt = $this->mysqli->prepare("SELECT username FROM akun WHERE nrp_mahasiswa = ?");
        $stmt->bind_param("s", $nrp);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        
        if ($res) {
            $username = $res['username'];
            // 2. Insert ke member_grup
            $stmt2 = $this->mysqli->prepare("INSERT INTO member_grup (idgrup, username) VALUES (?, ?)");
            $stmt2->bind_param("is", $idgrup, $username);
            return $stmt2->execute();
        }
        return false;
    }

    // Mahasiswa Join via Kode
    public function joinByKode($username_mhs, $kode)
    {
        // 1. Cek apakah kode valid
        $stmt = $this->mysqli->prepare("SELECT idgrup FROM grup WHERE kode_pendaftaran = ?");
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        $grup = $stmt->get_result()->fetch_assoc();

        if ($grup) {
            $idgrup = $grup['idgrup'];
            // 2. Cek apakah sudah join
            $cek = $this->mysqli->prepare("SELECT * FROM member_grup WHERE idgrup = ? AND username = ?");
            $cek->bind_param("is", $idgrup, $username_mhs);
            $cek->execute();
            if ($cek->get_result()->num_rows == 0) {
                // 3. Insert
                $join = $this->mysqli->prepare("INSERT INTO member_grup (idgrup, username) VALUES (?, ?)");
                $join->bind_param("is", $idgrup, $username_mhs);
                return $join->execute();
            }
        }
        return false; // Kode salah atau sudah join
    }

    public function removeMember($idgrup, $username_target)
    {
        $stmt = $this->mysqli->prepare("DELETE FROM member_grup WHERE idgrup = ? AND username = ?");
        $stmt->bind_param("is", $idgrup, $username_target);
        return $stmt->execute();
    }
}
?>