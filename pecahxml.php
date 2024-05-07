<?php
// Mulai sesi
session_start();

// Tentukan waktu timeout untuk session (15 menit)
$session_timeout = 900; // 15 menit dalam detik

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Pengguna belum login, arahkan ke halaman login
    header('Location: login.php');
    exit;
}

// Menangani logout jika pengguna melakukan logout secara manual
if (isset($_GET["logout"]) && $_GET["logout"] == 1) {
    // Hapus semua data sesi
    session_unset();
    // Hancurkan sesi
    session_destroy();
    // Redirect ke halaman login
    header("Location: login.php");
    exit;
}

// Periksa apakah sudah melewati waktu timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    // Jika sudah melewati waktu timeout, hapus semua data sesi dan arahkan ke halaman logout
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1"); // Anda bisa menambahkan parameter timeout jika ingin menampilkan pesan khusus
    exit;
}

// Update waktu aktivitas terakhir pengguna
$_SESSION['last_activity'] = time();

// Fungsi untuk menghapus file
function deleteFile($filename) {
    $output_folder = 'dbpostingan/';
    $filepath = $output_folder . $filename;
    if (file_exists($filepath)) {
        unlink($filepath);
        return true;
    }
    return false;
}

// Mengatur jumlah file per halaman
$filesPerPage = 20;
// Mendapatkan jumlah total file
$output_folder = 'dbpostingan/';
$files = glob($output_folder . '*.html');
$totalFiles = count($files);
// Mendapatkan jumlah total halaman
$totalPages = ceil($totalFiles / $filesPerPage);
// Mendapatkan nomor halaman yang diminta
$currentPage = isset($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $totalPages ? $_GET['page'] : 1;
// Menentukan index awal dan akhir dari file yang akan ditampilkan pada halaman ini
$startIndex = ($currentPage - 1) * $filesPerPage;
$endIndex = min($startIndex + $filesPerPage - 1, $totalFiles - 1);
// Mendapatkan potongan file yang akan ditampilkan pada halaman ini
$filesToShow = array_slice($files, $startIndex, $filesPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar File HTML</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f1f1;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            display: inline-block;
            padding: 5px 10px;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            margin: 0 4px;
            text-decoration: none;
            color: black;
        }

        .pagination a.active {
            background-color: #4CAF50;
            color: white;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .delete-button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
        }

        .delete-button:hover {
            background-color: #b30000;
        }
        h2 {
            text-align: center;
        }

        form {
            margin-top: 20px;
            text-align: center;
        }

        input[type="file"] {
            display: none;
        }

        .upload-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .upload-btn:hover {
            background-color: #45a049;
        }

        .file-selected {
            background-color: #45a049;
        }

        .file-selected::before {
            content: "File Dipilih: ";
            color: #fff;
        }

        .message {
            text-align: center;
            margin-top: 20px;
        }

        .success-message {
            color: #4CAF50;
        }

        .error-message {
            color: #f44336;
        }
   .sticky-menu {
            background-color: #333;
            overflow: hidden;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .sticky-menu a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .sticky-menu a.active {
            background-color: #555;
        }
    </style>
</head>
<body>
<div class="sticky-menu">
        <a id="home" href="index.php">Home</a>
        <a id="list" href="list.php">List</a>
        <a id="generatelayer2" href="generatelayer2.php">GenerateLayer2</a>
        <a id="pecahxml" href="pecahxml.php">pecah xml</a>
        <a id="logout" href="?logout=1">Logout</a>
    </div>
<div class="container">
<h2>Upload dan Pecah File XML</h2>

<form action="" method="post" enctype="multipart/form-data">
    <label for="xml_file" class="upload-btn" id="upload-label">Pilih File</label>
    <input type="file" id="xml_file" name="xml_file" accept=".xml" required onchange="updateFileName(this)">
    <button type="submit" name="submit" class="upload-btn">Upload</button>
</form>

<?php
if (isset($_POST['submit'])) {
    // Folder untuk menyimpan hasil pecahan XML
    $output_folder = 'dbpostingan/';

    // Memeriksa apakah file XML telah diunggah
    if (isset($_FILES['xml_file']) && $_FILES['xml_file']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES["xml_file"]["tmp_name"];
        $file_name = basename($_FILES["xml_file"]["name"]);

        // Memeriksa apakah file memiliki ekstensi XML
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($file_extension != "xml") {
            echo "<p class='message error-message'>Hanya file dengan format XML yang diizinkan.</p>";
            exit;
        }

        // Memuat file XML
        $xml = simplexml_load_file($tmp_name);

        if ($xml === false) {
            echo "<p class='message error-message'>Gagal memuat file XML.</p>";
            exit;
        }

        // Membuat folder jika belum ada
        if (!file_exists($output_folder)) {
            mkdir($output_folder, 0777, true);
        }

        // Memecah setiap postingan dari file XML
        foreach ($xml->entry as $entry) {
            $title = (string) $entry->title;
            $published = (string) $entry->published;
            $content = (string) $entry->content;

            // Menyimpan postingan dalam format HTML
            $html_content = "<h2>$title</h2>";
            $html_content .= "<p>Tanggal Terbit: $published</p>";
            $html_content .= "<div>$content</div>";

            // Membuat nama file dari judul postingan
            $file_name = $output_folder . preg_replace('/[^a-zA-Z0-9\-]/', '_', $title) . '.html';

            // Menyimpan konten sebagai file HTML
            file_put_contents($file_name, $html_content);
        }

        echo "<p class='message success-message'>File XML berhasil dipecah dan hasil HTML disimpan di folder 'dbpostingan'.</p>";
    } else {
        echo "<p class='message error-message'>Terjadi kesalahan saat mengunggah file.</p>";
    }
}
function deleteAllFiles($folderPath) {
    $files = glob($folderPath . '*'); // get all file names
    foreach($files as $file) { // iterate files
        if(is_file($file)) {
            unlink($file); // delete file
        }
    }
}

if(isset($_POST['delete_all'])) {
    $confirmation = $_POST['confirmation'];
    if($confirmation == 'yes') {
        deleteAllFiles('dbpostingan/');
        echo "<script>alert('All files deleted successfully.')</script>";
    } else {
        echo "<script>alert('Delete all operation cancelled.')</script>";
    }
}
?>
    <h2>Daftar File HTML</h2>
<!-- Delete All Button -->
<form action="" method="post" onsubmit="return confirm('Are you sure you want to delete all files?');">
            <input type="hidden" name="confirmation" value="yes">
            <input type="submit" name="delete_all" class="delete-all-button" value="Delete All">
        </form>
    <?php if ($totalFiles > 0): ?>
        <table>
            <tr>
                <th>No</th>
                <th>Nama File</th>
                <th>Delete</th>
            </tr>
            <?php foreach ($filesToShow as $index => $file): ?>
                <tr>
                    <td><?php echo $startIndex + $index + 1; ?></td>
                    <td><?php echo basename($file); ?></td>
                    <td><button class="delete-button" onclick="deleteFile('<?php echo basename($file); ?>')">Delete</button></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php echo $currentPage == $i ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    <?php else: ?>
        <p>Tidak ada file yang tersedia.</p>
    <?php endif; ?>
</div>

<script>
    function deleteFile(filename) {
        if (confirm("Apakah Anda yakin ingin menghapus file '" + filename + "'?")) {
            // Kirim request AJAX untuk menghapus file
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Refresh halaman setelah penghapusan selesai
                    window.location.reload();
                }
            };
            xhr.open("GET", "delete_file.php?filename=" + filename, true);
            xhr.send();
        }
        
    }
    function updateFileName(input) {
            var label = document.getElementById('upload-label');
            var fileName = input.files[0].name;
            label.classList.add('file-selected');
            label.innerHTML = 'File Dipilih: ' + fileName;
        }
    
</script>
</body>
</html>
