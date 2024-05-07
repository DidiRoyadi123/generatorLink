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
    $file_path = 'generatelayer2/' . $filename;
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            return "File $filename berhasil dihapus.";
        } else {
            return "Gagal menghapus file $filename.";
        }
    } else {
        return "File $filename tidak ditemukan.";
    }
}

// Jika ada permintaan untuk menghapus file
if (isset($_POST['filename'])) {
    $filename = $_POST['filename'];
    $delete_result = deleteFile($filename);
    echo $delete_result;
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Layer 2</title>
    <style>
        /* Reset CSS */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
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

        form {
            background-color: #fff;
            padding: 20px;
            margin-top: 60px; /* Adjust this value as needed */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            .sticky-menu a {
                padding: 10px 12px;
            }

            form {
                margin-top: 80px; /* Adjust this value as needed */
            }
        }

        /* Tabel Responsif */
        .table-wrapper {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table tr:hover {
            background-color: #ddd;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
        <form action="generate2.php" method="post">
            <textarea name="layer2_content" id="layer2_content" rows="10" placeholder="Masukkan konten Layer 2" required></textarea><br>
            <button type="submit" name="generate2">Generate</button>
        </form>

        <?php
        if (isset($_GET['success']) && $_GET['success'] == '1') {
            echo "<script>alert('File HTML baru telah berhasil dibuat dengan konten Layer 2.');</script>";
        } elseif (isset($_GET['success']) && $_GET['success'] == '0') {
            echo "<script>alert('Gagal membuat file HTML baru.');</script>";
        }
        ?>

        <!-- Tabel Responsif -->
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama File</th>
                        <th>Lihat</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil semua file dari folder generatelayer2/
                    $file_list = glob('generatelayer2/*');

                    // Jika ada file, tampilkan dalam tabel
                    if ($file_list && count($file_list) > 0) {
                        $no = 1;
                        foreach ($file_list as $file) {
                            // Ambil nama file dan tanggal modifikasi
                            $filename = basename($file);
                            $mod_date = date('d-m-Y', filemtime($file));
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td>$mod_date</td>";
                            echo "<td>$filename</td>";
                            echo "<td><button onclick='openModal(\"$filename\")'>Lihat</button></td>";
                            echo "<td><button style='background-color: #ff6347;' onclick='deleteFileConfirm(\"$filename\")'>Delete</button></td>";
                            echo "</tr>";
                            $no++;
                        }
                    } else {
                        // Jika tidak ada file, tampilkan pesan
                        echo "<tr><td colspan='5'>Tidak ada file yang tersedia.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal" id="myModal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <textarea id="fileContent" rows="10"></textarea><br>
                <button onclick="copyCode()">Salin</button>
            </div>
        </div>

        <script>
            // Fungsi untuk membuka modal
            function openModal(filename) {
                var modal = document.getElementById("myModal");
                var fileContent = document.getElementById("fileContent");

                // Set atribut value dari textarea
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        fileContent.value = this.responseText;
                        modal.style.display = "block";
                    }
                };
                xhr.open("GET", "generatelayer2/" + filename, true);
                xhr.send();
            }

            // Fungsi untuk menutup modal
            function closeModal() {
                var modal = document.getElementById("myModal");
                modal.style.display = "none";
            }

            // Fungsi untuk menyalin kode
            function copyCode() {
                var textarea = document.getElementById("fileContent");
                textarea.select();
                document.execCommand("copy");
                alert("Kode telah disalin!");
            }

            // Fungsi untuk menampilkan konfirmasi penghapusan file
            function deleteFileConfirm(filename) {
                var confirmation = confirm("Anda yakin ingin menghapus file " + filename + "?");
                if (confirmation) {
                    // Jika pengguna mengonfirmasi, kirim nama file ke script PHP untuk dihapus
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            alert(this.responseText);
                            // Refresh halaman setelah penghapusan file
                            location.reload();
                        }
                    };
                    xhr.open("POST", "generatelayer2.php", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.send("filename=" + filename);
                }
            }
        </script>
    </div>
</body>
</html>
