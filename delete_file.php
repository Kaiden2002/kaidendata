<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['filePath'])) {
        $filePath = $_POST['filePath'];
        $baseDir = realpath('uploads'); // Sesuaikan dengan folder uploads Anda
        $fullPath = realpath($baseDir . '/' . $filePath);

        // Validasi jalur file
        if (strpos($fullPath, $baseDir) !== 0) {
            echo "Path file tidak valid.";
            exit;
        }

        // Cek apakah file ada dan hapus
        if (file_exists($fullPath)) {
            if (unlink($fullPath)) {
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
