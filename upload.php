<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = 'uploads/' . $_POST['currentFolder'] . '/';
    foreach ($_FILES['filesToUpload']['name'] as $key => $name) {
        $targetFile = $targetDir . basename($_FILES['filesToUpload']['name'][$key]);
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($targetFile)) {
            echo "Sorry, file already exists.";
            continue;
        }

        // Allow certain file formats
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'docx', 'txt', 'zip', 'rar', 'exe', 'apk'];
        if (!in_array($fileType, $allowedTypes)) {
            echo "Sorry, only JPG, JPEG, PNG, GIF, PDF, DOCX, TXT, ZIP, RAR, EXE & APK files are allowed.";
            continue;
        }

        // Try to upload file
        if (move_uploaded_file($_FILES['filesToUpload']['tmp_name'][$key], $targetFile)) {
            echo "The file $name has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
