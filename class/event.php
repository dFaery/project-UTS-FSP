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
        $slug = strtolower(str_replace(' ', '-', $judul));
        $jenis = 'Publik';
        
        $stmt = $this->mysqli->prepare("INSERT INTO event (idgrup, judul, `judul-slug`, tanggal, keterangan, jenis) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $idgrup, $judul, $slug, $tanggal, $ket, $jenis);
        return $stmt->execute();
    }

    public function getEventById($idevent) {
        $stmt = $this->mysqli->prepare("SELECT * FROM event WHERE idevent = ?");
        $stmt->bind_param("i", $idevent);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateEvent($idevent, $judul, $tanggal, $ket) {
        $slug = strtolower(str_replace(' ', '-', $judul));
        $stmt = $this->mysqli->prepare("UPDATE event SET judul=?, `judul-slug`=?, tanggal=?, keterangan=? WHERE idevent=?");
        $stmt->bind_param("ssssi", $judul, $slug, $tanggal, $ket, $idevent);
        return $stmt->execute();
    }

    public function deleteEvent($idevent) {
        $stmt = $this->mysqli->prepare("DELETE FROM event WHERE idevent = ?");
        $stmt->bind_param("i", $idevent);
        return $stmt->execute();
    }
}
?>