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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Generator</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
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

        .container {
            margin-top: 60px;
            padding: 10px;
            max-width: 600px; /* Menetapkan lebar maksimum kontainer untuk responsivitas */
            margin: 0 auto; /* Pusatkan kontainer di tengah */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow-x: auto;
            background-color: white;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .copy-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
        }

        .copy-button:hover {
            background-color: #0056b3;
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

        .error {
            color: red;
        }

        /* Gaya untuk input textarea */
        textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            margin-bottom: 10px; /* Tambahkan jarak bawah */
            border: 1px solid #ccc; /* Atur tepi */
            border-radius: 4px; /* Tambahkan sudut */
            resize: vertical; /* Izinkan pengguliran vertikal */
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            display: block; /* Membuat tombol submit menjadi blok untuk memenuhi lebar kontainer */
            margin: 0 auto; /* Pusatkan tombol submit di tengah */
        }

        input[type="submit"]:hover {
            background-color: #45a049;
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
    <br> <br> <br>
    <div class="container">
        <h1>Link Generator</h1>
        <form id="linkForm" action="generate.php" method="post">
            <label for="links">Masukkan link (pisahkan dengan baris baru):</label><br>
            <textarea id="links" name="links" rows="5" placeholder="Paste links here..." required></textarea><br>
            <span id="linkError" class="error"></span><br>
            <input type="submit" value="Generate">
        </form>

        <h2>List of Generated Links</h2>
        <div style="overflow-x: auto;">
            <table id="linkTable">
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
                    // PHP code to display generated links
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
        </div>
    </div>
    <script>
        // JavaScript untuk menyalin link saat tombol copy diklik
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

        // JavaScript untuk menyembunyikan tabel setelah 5 menit
        setTimeout(function() {
            document.getElementById('linkTable').style.display = 'none';
        }, 300000); // 5 menit dalam milidetik
    </script>
    <script>
        const linkForm = document.getElementById('linkForm');
        const linksInput = document.getElementById('links');
        const linkError = document.getElementById('linkError');

        linkForm.addEventListener('submit', function(event) {
            if (linksInput.value.trim() === '') {
                linkError.textContent = 'Please enter at least one link.';
                event.preventDefault(); // Prevent form submission
            } else {
                linkError.textContent = '';
            }
        });
    </script>
</body>
</html>
