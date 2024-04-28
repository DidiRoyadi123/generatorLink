<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "generatorlink";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    // Jika koneksi gagal, tampilkan pesan error
    echo "Connection failed: " . $conn->connect_error;
    exit(); // Hentikan eksekusi skrip jika koneksi gagal
}

echo "Connection successful"; // Tampilkan pesan jika koneksi berhasil
?>
