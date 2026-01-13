<?php
require_once("classParent.php");

class Dosen extends classParent
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDosen($cari_dosen, $npk_to_edit = null, $offset = null, $limit = null)
    {
        $cari_persen = "%" . $cari_dosen . "%";

        if ($npk_to_edit == null) {
            if(!is_null($offset)){
                $stmt = $this->mysqli->prepare("SELECT * FROM dosen WHERE npk LIKE ? OR nama LIKE ? LIMIT ? OFFSET ?");
                $stmt->bind_param("ssii", $cari_persen, $cari_persen, $limit, $offset);
                $stmt->execute();
                return $stmt->get_result();
            }
            else{
                $stmt = $this->mysqli->prepare("SELECT * FROM dosen WHERE npk LIKE ? OR nama LIKE ?");
                $stmt->bind_param("ss", $cari_persen, $cari_persen);
                $stmt->execute();
                return $stmt->get_result();
            }
        } else {
            $stmt = $this->mysqli->prepare("SELECT npk, nama, foto_extension FROM dosen WHERE npk = ?");
            $stmt->bind_param("s", $npk_to_edit);
            $stmt->execute();
            return $stmt->get_result();
        }
    }

    public function insertDosen($npk, $nama, $foto_extension)
    {
        $stmt = $this->mysqli->prepare("INSERT INTO dosen (npk, nama, foto_extension) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $npk, $nama, $foto_extension);
        $stmt->execute();
        $stmt->close();
    }

    public function deleteDosen($npk_to_delete)
    {
        $stmt_select = $this->mysqli->prepare("SELECT foto_extension FROM dosen WHERE npk = ?");
        $stmt_select->bind_param("s", $npk_to_delete);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        if ($result->num_rows > 0) {
            $dosen = $result->fetch_assoc();
            $foto_extension = $dosen['foto_extension'];

            if (!empty($foto_extension)) {
                $file_path = "images/" . $npk_to_delete . "." . $foto_extension;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
        $stmt_select->close();

        $stmt_delete = $this->mysqli->prepare("DELETE FROM dosen WHERE npk = ?");
        $stmt_delete->bind_param("s", $npk_to_delete);

        if ($stmt_delete->execute()) {
            header("Location: tabeldosen.php");
            exit();
        } else {
            echo "Error deleting record: " . $this->mysqli->error;
        }

        $stmt_delete->close();
    }

    public function updateDosen($npk_asli, $nama)
    {
        $stmt_select = $this->mysqli->prepare("SELECT foto_extension FROM dosen WHERE npk = ?");
        $stmt_select->bind_param("s", $npk_asli);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $dosen_lama = $result->fetch_assoc();
        $stmt_select->close();

        $foto_extension = $dosen_lama['foto_extension'];

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            if (!empty($foto_extension)) {
                $path_foto_lama = "images/" . $npk_asli . "." . $foto_extension;
                if (file_exists($path_foto_lama)) {
                    unlink($path_foto_lama);
                }
            }

            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            $file_info = pathinfo($_FILES['foto']['name']);
            $extension = strtolower($file_info['extension']);
            if (in_array($extension, $allowed_extensions)) {
                $foto_extension = $extension;
                $target_file = "images/" . $npk_asli . '.' . $foto_extension;
                move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);
            }
        }

        $stmt_update = $this->mysqli->prepare("UPDATE dosen SET nama = ?, foto_extension = ? WHERE npk = ?");
        $stmt_update->bind_param("sss", $nama, $foto_extension, $npk_asli);

        if ($stmt_update->execute()) {
            header("Location: tabeldosen.php");
            exit();
        } else {
            echo "Error updating record: " . $this->mysqli->error;
        }

        $stmt_update->close();
    }
}
