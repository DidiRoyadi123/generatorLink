<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['filename'])) {
        $filename = $_POST['filename'];
        $viewFolder = 'view/';
        $filePath = $viewFolder . $filename;
        
        // Hapus file jika ada
        if (file_exists($filePath)) {
            unlink($filePath);
            echo "<script>alert('File berhasil dihapus.'); window.location.href = 'list.php';</script>";
        } else {
            echo "<script>alert('File tidak ditemukan.'); window.location.href = 'list.php';</script>";
        }
    } else {
        echo "<script>alert('Parameter filename tidak ditemukan.'); window.location.href = 'list.php';</script>";
    }
} else {
    echo "<script>alert('Metode HTTP tidak valid.'); window.location.href = 'list.php';</script>";
}
?>
