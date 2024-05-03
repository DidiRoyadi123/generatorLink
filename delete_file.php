<?php
// Memeriksa apakah parameter filename ada dalam request GET
if (isset($_GET['filename'])) {
    // Mendapatkan nama file dari parameter GET
    $filename = $_GET['filename'];

    // Folder tempat file disimpan
    $output_folder = 'dbpostingan/';

    // Path lengkap ke file yang akan dihapus
    $file_path = $output_folder . $filename;

    // Memeriksa apakah file tersebut ada
    if (file_exists($file_path)) {
        // Menghapus file
        unlink($file_path);
        // Mengembalikan pesan sukses
        echo "File '$filename' berhasil dihapus.";
    } else {
        // Jika file tidak ditemukan, kirim pesan error
        echo "File '$filename' tidak ditemukan.";
    }
} else {
    // Jika parameter filename tidak ada, kirim pesan error
    echo "Parameter filename tidak ditemukan dalam request.";
}
?>
