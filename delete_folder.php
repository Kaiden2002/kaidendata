<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderPath = isset($_POST['folderPath']) ? $_POST['folderPath'] : '';

    if (!empty($folderPath) && is_dir($folderPath)) {
        // Menghapus folder secara rekursif
        function deleteDirectory($dir) {
            if (!is_dir($dir)) {
                return false;
            }
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? deleteDirectory("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }

        if (deleteDirectory($folderPath)) {
            echo 'Folder berhasil dihapus.';
        } else {
            echo 'Gagal menghapus folder.';
        }
    } else {
        echo 'Folder tidak ditemukan.';
    }
} else {
    echo 'Permintaan tidak valid.';
}
?>
