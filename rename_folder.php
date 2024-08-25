<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentFolder = $_POST['currentFolder'];
    $oldFolderName = $_POST['oldFolderName'];
    $newFolderName = $_POST['newFolderName'];
    $oldFolderPath = 'uploads/' . $currentFolder . '/' . $oldFolderName;
    $newFolderPath = 'uploads/' . $currentFolder . '/' . $newFolderName;

    if (is_dir($oldFolderPath)) {
        if (!is_dir($newFolderPath)) {
            rename($oldFolderPath, $newFolderPath);
            echo "Folder renamed to '$newFolderName'.";
        } else {
            echo "Folder with the new name already exists.";
        }
    } else {
        echo "Folder not found.";
    }
}
?>
