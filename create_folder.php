<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baseDir = 'uploads/';
    $currentFolder = isset($_POST['currentFolder']) ? $_POST['currentFolder'] : '';
    $folderName = isset($_POST['folderName']) ? $_POST['folderName'] : '';

    if (!empty($folderName)) {
        $newFolderPath = $baseDir . $currentFolder . '/' . $folderName;
        if (!file_exists($newFolderPath)) {
            if (mkdir($newFolderPath, 0777, true)) {
                echo "Folder berhasil dibuat.";
            } else {
                echo "Gagal membuat folder.";
            }
        } else {
            echo "Folder sudah ada.";
        }
    } else {
        echo "Nama folder tidak boleh kosong.";
    }
}
?>
