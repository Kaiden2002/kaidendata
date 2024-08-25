<?php
// Pastikan file diambil dari parameter URL
if (isset($_GET['file']) && !empty($_GET['file'])) {
    $file = $_GET['file'];
    $filePath = 'uploads/' . $file;

    // Cek apakah file ada dan dapat dibaca
    if (file_exists($filePath) && is_readable($filePath)) {
        // Mengatur header untuk download file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        // Mengeluarkan isi file
        readfile($filePath);
        exit;
    } else {
        echo 'Sorry, the file does not exist or is not readable.';
    }
} else {
    echo 'Invalid request.';
}
?>
