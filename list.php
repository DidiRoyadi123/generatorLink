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
        deleteAllFiles('view/');
        echo "<script>alert('All files deleted successfully.')</script>";
    } else {
        echo "<script>alert('Delete all operation cancelled.')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Generated Files</title>
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Style */
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin-top: 60px; /* Untuk menyembunyikan menu sticky */
        }

        /* Sticky Menu Style */
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

        /* Content Container */
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        /* Table Style */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Delete All Button Style */
        .delete-all-button {
            background-color: red;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease; /* Animasi perubahan warna latar belakang */
        }

        /* Hover Effect for Delete All Button */
        .delete-all-button:hover {
            background-color: #b30000;
        }

        /* Copy Button Style */
        .copy-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
        }

        /* Hover Effect for Copy Button */
        .copy-button:hover {
            background-color: #45a049;
        }

        /* Pagination Style */
        ul.pagination {
            display: inline-block;
            padding: 0;
            margin: 20px 0;
        }

        ul.pagination li {
            display: inline;
        }

        ul.pagination li a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
        }

        ul.pagination li.active a {
            background-color: #4CAF50;
            color: white;
        }

        ul.pagination li a:hover:not(.active) {background-color: #ddd;}
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

    <!-- Content Container -->
    <div class="container">
        <h1>List of Generated Files</h1>

        <!-- Delete All Button -->
        <form action="" method="post" onsubmit="return confirm('Are you sure you want to delete all files?');">
            <input type="hidden" name="confirmation" value="yes">
            <input type="submit" name="delete_all" class="delete-all-button" value="Delete All">
        </form>

        <!-- File List Table -->
        <table>
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Copy Link</th>
                    <th>Link</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Menampilkan daftar file dengan nomor, nama file, link, tombol delete, dan tombol copy link
                $viewFolder = 'view/';
                $filesPerPage = 20;
                $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
                $start = ($currentPage - 1) * $filesPerPage;
                if (is_dir($viewFolder)) {
                    $files = array_diff(scandir($viewFolder), array('..', '.'));
                    // Mengurutkan daftar file berdasarkan waktu pembuatan (descending order)
                    usort($files, function($a, $b) use ($viewFolder) {
                        return filemtime($viewFolder . $b) - filemtime($viewFolder . $a);
                    });
                    $totalFiles = count($files);
                    $totalPages = ceil($totalFiles / $filesPerPage);

                    $files = array_slice($files, $start, $filesPerPage);
                    $count = $start + 1;
                    foreach ($files as $file) {
                        if ($file !== '.htaccess') {
                            $link = 'http' . (($_SERVER['SERVER_PORT'] == 443) ? 's://' : '://') . $_SERVER['HTTP_HOST'] . '/' . $viewFolder . $file;
                            $fileName = basename($file); // Dapatkan hanya nama file dari path
                            $linkPath = substr($link, strpos($link, 'view/') + strlen('view/')); // Ambil bagian link setelah 'view/'
                            echo "<tr>";
                            echo "<td>$count</td>";
                            // Tombol copy link
                            echo "<td><button class='copy-button' data-link='$link'>Copy Link</button></td>";
                            echo "<td><a href='$link'>$fileName</a></td>";
                            // Formulir delete
                            echo "<td>";
                            echo "<form action='delete.php' method='post'>";
                            echo "<input type='hidden' name='filename' value='$file'>";
                            echo "<input type='hidden' name='link' value='$linkPath'>"; // Mengirim bagian link setelah 'view/' ke delete.php
                            echo "<button class='delete-button' type='submit'>Delete</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                            $count++;
                        }
                    }
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <ul class="pagination">
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <li <?php if($i == $currentPage) echo 'class="active"'; ?>>
                    <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </div>

    <!-- JavaScript untuk menyalin link saat tombol copy diklik -->
    <script>
        const copyButtons = document.querySelectorAll('.copy-button');
        copyButtons.forEach(button => {
            button.addEventListener('click', () => {
                const link = button.getAttribute('data-link');
                navigator.clipboard.writeText(link).then(() => {
                    alert('Link copied to clipboard: ' + link);
                }).catch(err => {
                    console.error('Failed to copy link: ', err);
                });
            });
        });
    </script>
</body>
</html>
