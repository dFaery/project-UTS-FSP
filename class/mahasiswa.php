<?php
require_once("classParent.php");

class Mahasiswa extends classParent
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getMahasiswa($cari_mahasiswa, $nrp_to_edit = null, $offset = null, $limit = null)
    {

        $cari_persen = "%" . $cari_mahasiswa . "%";


        if ($nrp_to_edit == null) {
            if (!is_null($offset)) {
                $stmt = $this->mysqli->prepare("SELECT * FROM mahasiswa WHERE nrp LIKE ? OR nama LIKE ? LIMIT ? OFFSET ?");
                $stmt->bind_param("ssii", $cari_persen, $cari_persen, $limit, $offset);
                $stmt->execute();
                return $stmt->get_result();
            } else {
                $stmt = $this->mysqli->prepare("SELECT * FROM mahasiswa WHERE nrp LIKE ? OR nama LIKE ?");
                $stmt->bind_param("ss", $cari_persen, $cari_persen);
                $stmt->execute();
                return $stmt->get_result();
            }
        } else {
            $stmt = $this->mysqli->prepare("SELECT * FROM mahasiswa WHERE nrp = ?");
            $stmt->bind_param("s", $nrp_to_edit);
            $stmt->execute();
            return $stmt->get_result();
        }
        
    } 

    public function insertMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension)
    {
        $stmt = $this->mysqli->prepare("INSERT INTO mahasiswa (nrp, nama, gender, tanggal_lahir, angkatan, foto_extention) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension);
        $stmt->execute();
        $stmt->close();
    }

    public function deleteMahasiswa($nrp_to_delete)
    {
        $stmt_select = $this->mysqli->prepare("SELECT foto_extention FROM mahasiswa WHERE nrp = ?");
        $stmt_select->bind_param("s", $nrp_to_delete);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        if ($result->num_rows > 0) {
            $mahasiswa = $result->fetch_assoc();
            $foto_extension = $mahasiswa['foto_extention'];

            if (!empty($foto_extension)) {
                $file_path = "images/" . $nrp_to_delete . "." . $foto_extension;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
        $stmt_select->close();

        $stmt_delete = $this->mysqli->prepare("DELETE FROM mahasiswa WHERE nrp = ?");
        $stmt_delete->bind_param("s", $nrp_to_delete);

        if ($stmt_delete->execute()) {
            header("Location: tabelmahasiswa.php");
            exit();
        } else {
            echo "Error deleting record: " . $this->mysqli->error;
        }

        $stmt_delete->close();
    }

    public function updateMahasiswa($nrp_asli, $nama, $gender, $tanggal_lahir, $angkatan)
    {
        // Ambil data foto lama
        $stmt_select = $this->mysqli->prepare("SELECT foto_extention FROM mahasiswa WHERE nrp = ?");
        $stmt_select->bind_param("s", $nrp_asli);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $mahasiswa_lama = $result->fetch_assoc();
        $stmt_select->close();

        $foto_extension = $mahasiswa_lama['foto_extention'];

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            if (!empty($foto_extension)) {
                $path_foto_lama = "images/" . $nrp_asli . "." . $foto_extension;
                if (file_exists($path_foto_lama)) {
                    unlink($path_foto_lama);
                }
            }

            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            $file_info = pathinfo($_FILES['foto']['name']);
            $extension = strtolower($file_info['extension']);
            if (in_array($extension, $allowed_extensions)) {
                $foto_extension = $extension;
                $target_file = "images/" . $nrp_asli . '.' . $foto_extension;
                move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);
            }
        }

        $stmt_update = $this->mysqli->prepare("UPDATE mahasiswa SET nama = ?, gender = ?, tanggal_lahir = ?, angkatan = ?, foto_extention = ? WHERE nrp = ?");
        $stmt_update->bind_param("sssiss", $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension, $nrp_asli);

        if ($stmt_update->execute()) {
            header("Location: tabelmahasiswa.php");
            exit();
        } else {
            echo "Error updating record: " . $this->mysqli->error;
        }

        $stmt_update->close();
    }
}
