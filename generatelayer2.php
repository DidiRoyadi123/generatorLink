<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Layer 2</title>
</head>
<body>
    <form action="generate2.php" method="post">
        <textarea name="layer2_content" id="layer2_content" rows="10" cols="50" placeholder="Masukkan konten Layer 2"></textarea><br>
        <button type="submit" name="generate2">Generate</button>
    </form>

    <?php
    if (isset($_GET['success']) && $_GET['success'] == '1') {
        echo "<script>alert('File HTML baru telah berhasil dibuat dengan konten Layer 2.');</script>";
    } elseif (isset($_GET['success']) && $_GET['success'] == '0') {
        echo "<script>alert('Gagal membuat file HTML baru.');</script>";
    }
    ?>
</body>
</html>
