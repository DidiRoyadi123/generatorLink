<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $links = explode("\n", $_POST['links']);

    foreach ($links as $link) {
       
        if (strpos($link, 'neocloud.co.in/shared') === false && strpos($link, 'https://wave-cloud.s3.ap-south-1.amazonaws.com/neocloud/') === false) {
            // Jika tidak ada yang memenuhi kondisi, tampilkan pesan kesalahan
            echo "<script>alert('Link tidak valid. Harap masukkan link yang valid.'); window.location.href = 'index.php';</script>";
            exit;
        }

        if (!empty($link)) {
            $randomString = generateRandomString(10);
            $timestamp = time();
            $formattedDate = date('dmY', $timestamp);
            $formattedDate = implode("-", str_split($formattedDate, 4)); // Memisahkan tanggal-bulan-tahun-jam-menit dengan tanda "-"
            $fileName = $formattedDate . '_' . $randomString . '.html';
            $filePath = 'view/' . $fileName;
            $iframeCode = '<!DOCTYPE html>' .
                '<html lang="en">' .
                '<head>' .
                '    <meta charset="UTF-8">' .
                '    <meta name="viewport" content="width=device-width, initial-scale=1.0">' .
                '    <title>Link Viewer</title>' .
                '    <style>' .
                '        body { margin: 0; }' .
                '        .ad-banner { background-color: #f1f1f1; text-align: center; padding: 10px; }' .
                '        iframe { width: 100%; min-height: 100vh; border: none; display: none; }' . // Menyembunyikan iframe secara default
                '        .button-container { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 999; }' . // Menempatkan tombol di tengah iframe
                // hoover effect
                '        .tampilkan-button:hover { background-color: #003366; }' .
                '        .tampilkan-button { background-color: blue; color: white; padding: 10px 20px; border: none; cursor: pointer; }' . // Mengubah gaya tombol tampilkan
                '    </style>' .
                '</head>' .
                '<body>' .
                '<div class="ad-banner"><script type="text/javascript" src="//doomdefender.com/63/5d/cf/635dcf50d8f5bda4fa2715b198a63f8b.js"></script></div>' .
                '<div class="ad-banner"><script type="text/javascript" src="//doomdefender.com/86/d0/0f/86d00ff8933e120ee7fc4641cde0de3c.js"></script></div>' .
                '    <div class="ad-banner"><script async="async" data-cfasync="false" src="//doomdefender.com/19e05f0b0a07cdd87c48da1e805920d3/invoke.js"></script>
                <div id="container-19e05f0b0a07cdd87c48da1e805920d3"></div></div>' .
                '    <iframe id="myFrame" src="about:blank"></iframe>' . // Mengatur src awal ke about:blank
                '<link href="https://codeflareblogspot.github.io/code/KillAdBlock.css" rel="stylesheet"/>' .
                '<script>' .
                '    var counter = 15;' .
                '    var timer;' . // Deklarasi variabel timer di luar scope fungsi untuk dapat diakses di fungsi lain
                '    function countdown() {' .
                '        var button = document.getElementById("tampilkanButton");' .
                '        button.disabled = true;' . // Menonaktifkan tombol saat hitungan mundur berlangsung
                '        timer = setInterval(function() {' .
                '            counter--;' .
                '            button.innerHTML = "Tunggu/Wait (" + counter + "s)";' .
                '            if (counter < 0) {' .
                '                clearInterval(timer);' .
                '                button.innerHTML = "Video ada dibawah";' .
                '                document.getElementById("myFrame").src = "' . $link . '";' . // Mengatur src iframe ke link setelah counter selesai
                '                document.getElementById("myFrame").style.display = "block";' . // Menampilkan iframe setelah counter selesai
                '            }' .
                '        }, 1000);' .
                '    }' .
                '</script>' .
                '<div class="button-container"><button id="tampilkanButton" class="tampilkan-button" onclick="countdown()">Tonton/Watch (15s)</button></div>' . // Menambahkan tombol tampilkan dengan event onclick yang memanggil fungsi countdown
                '<script src="https://codeflareblogspot.github.io/code/KillAdBlock.js"></script>' .
                '<script>' .
                '    var titleAd = "AD Blocker Detected";' .
                '    var notifAd = "Please Support t.me/bokep_indonesia18 with disable your browser AD-Block to continue reading or register this website into whitelist.<br/><b>Thank You</b>";' .
                '</script>' .
                '</body>' .
                '</html>';

            file_put_contents($filePath, $iframeCode);
        }
    }

    echo "Halaman-halaman telah dibuat dan disimpan dalam folder 'view/'.";
    header("Location: index.php"); // Mengarahkan kembali ke index.php setelah selesai
} else {
    header("Location: index.php");
}

function generateRandomString($length = 10)
{
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
?>
