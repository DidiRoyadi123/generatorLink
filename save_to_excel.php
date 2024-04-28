<?php
// Membuat file Excel dari daftar link
if (isset($_POST['save_to_excel'])) {
    $viewFolder = 'view/';
    $files = array_diff(scandir($viewFolder), array('..', '.'));

    // Buat nama file Excel
    $excelFileName = 'links_' . date('YmdHis') . '.xls';

    // Header untuk membuat file Excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$excelFileName\"");

    // Buat file Excel
    $file = fopen("php://output", "w");

    // Header kolom untuk file Excel
    $header = array('Link');
    fputcsv($file, $header);

    // Tulis link dari setiap file ke dalam file Excel
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $link = 'http' . (($_SERVER['SERVER_PORT'] == 443) ? 's://' : '://') . $_SERVER['HTTP_HOST'] . '/' . $viewFolder . $file;
            fputcsv($file, array($link));
        }
    }

    // Tutup file Excel
    fclose($file);
    exit();
}
?>
