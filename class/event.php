<?php
require_once("classParent.php");

class Event extends classParent
{
    public function getEventsByGrup($idgrup) {
        $stmt = $this->mysqli->prepare("SELECT * FROM event WHERE idgrup = ? ORDER BY tanggal DESC");
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function addEvent($idgrup, $judul, $tanggal, $ket) {
        $slug = strtolower(str_replace(' ', '-', $judul)); // Simple slug
        $jenis = 'Publik';
        
        $stmt = $this->mysqli->prepare("INSERT INTO event (idgrup, judul, `judul-slug`, tanggal, keterangan, jenis) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $idgrup, $judul, $slug, $tanggal, $ket, $jenis);
        return $stmt->execute();
    }
}
?>