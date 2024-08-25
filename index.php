<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penyimpanan Dokumen</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        td {
            word-wrap: break-word; /* Memungkinkan teks panjang untuk membungkus */
            max-width: 200px; /* Atur lebar maksimum kolom */
            overflow: hidden; /* Sembunyikan teks yang meluap */
            text-overflow: ellipsis; /* Tambahkan elipsis jika teks terlalu panjang */
        }
        .actions form { display: inline; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Sistem Penyimpanan Dokumen</h1>

    <!-- Form pencarian -->
    <form id="searchForm" method="GET">
        <input type="text" name="search" placeholder="Cari file atau folder" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <input type="hidden" name="folder" value="<?php echo isset($_GET['folder']) ? htmlspecialchars($_GET['folder']) : ''; ?>">
        <input type="submit" value="Cari">
    </form>

<!-- Form untuk membuat folder -->
<form id="createFolderForm">
    <input type="text" name="folderName" placeholder="Nama Folder Baru" required>
    <input type="hidden" name="currentFolder" value="<?php echo isset($_GET['folder']) ? htmlspecialchars($_GET['folder']) : ''; ?>">
    <input type="submit" value="Buat Folder">
</form>


    <!-- Form untuk upload file hanya jika berada di dalam folder -->
    <?php 
    $currentFolder = isset($_GET['folder']) ? $_GET['folder'] : '';
    if (!empty($currentFolder)): ?>
        <form id="uploadForm" enctype="multipart/form-data">
            <input type="hidden" name="currentFolder" value="<?php echo htmlspecialchars($currentFolder); ?>">
            <input type="file" name="filesToUpload[]" id="fileToUpload" multiple>
            <input type="submit" value="Unggah Dokumen" name="submit">
        </form>
    <?php endif; ?>

    <!-- Menampilkan informasi folder saat ini -->
    <?php if (!empty($currentFolder)): ?>
        <p>Saat ini Anda berada di dalam folder: <strong><?php echo htmlspecialchars($currentFolder); ?></strong></p>
    <?php endif; ?>

    <h2><?php echo !empty($searchQuery) ? 'Hasil Pencarian' : 'Daftar Folder dan File'; ?></h2>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Jalur</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $baseDir = 'uploads/';
            $currentFolderPath = !empty($currentFolder) ? $baseDir . $currentFolder : $baseDir;
            $searchQuery = isset($_GET['search']) ? strtolower($_GET['search']) : '';

            function searchFiles($dir, $searchQuery) {
                $results = [];
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $filePath = $dir . '/' . $file;
                        if (stripos($file, $searchQuery) !== false) {
                            $results[] = $filePath;
                        }
                        if (is_dir($filePath)) {
                            $results = array_merge($results, searchFiles($filePath, $searchQuery));
                        }
                    }
                }
                return $results;
            }

            // Jika ada pencarian
            if ($searchQuery !== '') {
                $files = searchFiles($baseDir, $searchQuery);

                if (empty($files)) {
                    echo "<tr><td colspan='4'>Tidak ada hasil ditemukan.</td></tr>";
                } else {
                    foreach ($files as $filePath) {
                        $file = basename($filePath);
                        $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $relativePath = str_replace($baseDir, '', $filePath); // Jalur relatif

                        if (is_dir($filePath)) {
                            echo "<tr>
                                    <td>$file</td>
                                    <td>Folder</td>
                                    <td>$relativePath</td>
                                    <td class='actions'>
                                        <form class='inline-form rename-folder-form' method='POST'>
                                            <input type='hidden' name='currentFolder' value='" . htmlspecialchars(dirname($filePath)) . "'>
                                            <input type='hidden' name='oldFolderName' value='" . htmlspecialchars($file) . "'>
                                            <input type='text' name='newFolderName' placeholder='Nama Baru'>
                                            <input type='submit' value='Folder'>
                                        </form>
                                        <form class='inline-form delete-folder-form' method='POST'>
                                            <input type='hidden' name='folderPath' value='" . htmlspecialchars($filePath) . "'>
                                            <input type='submit' value='Hapus'>
                                        </form>
                                    </td>
                                  </tr>";
                        } else {
                            $viewLink = '';
                            $downloadLink = "<a href='download.php?file=" . urlencode($relativePath) . "'>[Unduh]</a>";

                            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'pdf'])) {
                                $viewLink = "<a href='$filePath' target='_blank'>[Lihat]</a>";
                            }

                            echo "<tr>
                                    <td><a href='$filePath'>$file</a></td>
                                    <td>" . strtoupper($fileExt) . "</td>
                                    <td>$relativePath</td>
                                    <td class='actions'>
                                        $viewLink $downloadLink 
                                        <a href='#' class='delete-file' data-file='" . htmlspecialchars($relativePath) . "'>[Hapus]</a>
                                    </td>
                                  </tr>";
                        }
                    }
                }
            } else {
                // Tampilkan semua file dan folder jika tidak ada pencarian
                $files = scandir($currentFolderPath);

                if (!empty($currentFolder)) {
                    $parentFolder = dirname($currentFolder);
                    if ($parentFolder === '.' || $parentFolder === '/') {
                        $parentFolder = '';
                    }
                    echo "<tr><td><a href='index.php" . ($parentFolder ? "?folder=" . urlencode($parentFolder) : "") . "'>.. (Naik ke atas)</a></td><td></td><td></td><td></td></tr>";
                }

                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $filePath = $currentFolderPath . '/' . $file;
                        $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $relativePath = (!empty($currentFolder) ? $currentFolder . '/' : '') . $file; // Jalur relatif

                        if (is_dir($filePath)) {
                            echo "<tr>
                                    <td><a href='index.php?folder=" . urlencode($relativePath) . "'>$file</a></td>
                                    <td>Folder</td>
                                    <td>$relativePath</td>
                                    <td class='actions'>
                                        <form class='inline-form rename-folder-form' method='POST'>
                                            <input type='hidden' name='currentFolder' value='" . htmlspecialchars($currentFolder) . "'>
                                            <input type='hidden' name='oldFolderName' value='" . htmlspecialchars($file) . "'>
                                            <input type='text' name='newFolderName' placeholder='Nama Baru'>
                                            <input type='submit' value='Folder'>
                                        </form>
                                        <form class='inline-form delete-folder-form' method='POST'>
                                            <input type='hidden' name='folderPath' value='" . htmlspecialchars($filePath) . "'>
                                            <input type='submit' value='Hapus'>
                                        </form>
                                    </td>
                                  </tr>";
                        } else {
                            $viewLink = '';
                            $downloadLink = "<a href='download.php?file=" . urlencode($relativePath) . "'>[Unduh]</a>";

                            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'pdf'])) {
                                $viewLink = "<a href='$filePath' target='_blank'>[Lihat]</a>";
                            }

                            echo "<tr>
                                    <td><a href='$filePath'>$file</a></td>
                                    <td>" . strtoupper($fileExt) . "</td>
                                    <td>$relativePath</td>
                                    <td class='actions'>
                                        $viewLink $downloadLink 
                                        <a href='#' class='delete-file' data-file='" . htmlspecialchars($relativePath) . "'>[Hapus]</a>
                                    </td>
                                  </tr>";
                        }
                    }
                }
            }
            ?>
        </tbody>
    </table>

    <!-- Hanya tampilkan jika ada pencarian -->
    <?php if (!empty($searchQuery)): ?>
        <a href="index.php" class="back-to-home">Kembali ke Halaman Utama</a>
    <?php endif; ?>

    <script>
    $(document).ready(function() {
        $('.delete-file').on('click', function(e) {
            e.preventDefault();
            var filePath = $(this).data('file');
            if (confirm('Anda yakin ingin menghapus file ini?')) {
                $.ajax({
                    url: 'delete_file.php',
                    type: 'POST',
                    data: { filePath: filePath },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert("Terjadi kesalahan: " + error);
                    }
                });
            }
        });

        $('.delete-folder-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            if (confirm('Anda yakin ingin menghapus folder ini?')) {
                $.ajax({
                    url: 'delete_folder.php',
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert("Terjadi kesalahan: " + error);
                    }
                });
            }
        });

        $('.rename-folder-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: 'rename_folder.php',
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert("Terjadi kesalahan: " + error);
                }
            });
        });

        $('#uploadForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: 'upload.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert("Terjadi kesalahan: " + error);
                }
            });
        });
    });
    $(document).ready(function() {
    // Script lainnya...

    // Handler untuk pembuatan folder
    $('#createFolderForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'create_folder.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert(response);
                location.reload(); // Reload halaman jika berhasil
            },
            error: function(xhr, status, error) {
                alert("Terjadi kesalahan: " + error);
            }
        });
    });

    // Script lainnya...
});

    </script>
</body>
</html>
