<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['filePath'])) {
        $filePath = $_POST['filePath'];

        // Validasi jalur file
        if (strpos($filePath, '..') !== false || strpos($filePath, '/') === false) {
            echo "Path file tidak valid.";
            exit;
        }

        // Cek apakah file ada dan hapus
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                echo "File berhasil dihapus.";
            } else {
                echo "Gagal menghapus file.";
            }
        } else {
            echo "File tidak ditemukan.";
        }
    } else {
        echo "Path file tidak disertakan.";
    }
} else {
    echo "Metode permintaan tidak valid.";
}
?>
