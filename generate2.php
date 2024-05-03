<?php
// Memeriksa apakah tombol Generate telah ditekan
if (isset($_POST['generate2'])) {
    // Memeriksa apakah konten Layer 2 telah diberikan
    if (!empty($_POST['layer2_content'])) {
        // Memuat daftar file HTML dari folder dbpostingan/
        $output_folder = 'dbpostingan/';
        $files = glob($output_folder . '*.html');

        // Memilih secara acak salah satu file HTML
        if (count($files) > 0) {
            $random_file = $files[array_rand($files)];
            $random_content = file_get_contents($random_file);

            // Periksa apakah isi file HTML yang dipilih memiliki tag html lengkap atau tidak
            if (strpos($random_content, '</html>') === false) {
                // Jika tidak lengkap, lengkapi dengan tag html, head, dan body
                $random_content = '<html><head></head><body>' . $random_content . '</body></html>';
            }

            // Memeriksa apakah konten Layer 2 valid (misalnya, mengandung </body>)
            if (strpos($random_content, '</body>') !== false && strpos($random_content, '</html>') !== false) {
                // Ambil konten Layer 2 yang dimasukkan oleh pengguna
                $layer2_content = htmlspecialchars($_POST['layer2_content']);

                // Menyiapkan tombol "Lihat file" dengan Vue.js
                $vue_script = '<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>';
                $vue_script .= '<div id="app"><button v-if="counter > 0" style="background-color: blue; color: white; display: block; margin: 0 auto;" v-text="`Lihat file (${counter} detik)`" @click="countdown"></button><a v-else href="#bottom">Lanjut</a></div>';
                $vue_script .= '<script>new Vue({ el: "#app", data: { counter: 10 }, methods: { countdown() { const interval = setInterval(() => { if (this.counter > 0) { this.counter--; } else { clearInterval(interval); window.location.href = "#bottom"; } }, 1000); } } });</script>';

                // Memasukkan script Vue.js dan tombol "Lihat file" setelah elemen header terdekat
                $new_content = str_replace('</head>', '</head>' . $vue_script, $random_content);

                // Menyiapkan link download dengan konten Layer 2
                $download_button = '<button id="bottom"><a href="' . $layer2_content . '">Download</a></button>';

                // Memasukkan link download setelah tag </body>
                $new_content = str_replace('</body>', $download_button . '</body>', $new_content);

                // Simpan perubahan ke file HTML baru
                $original_title = basename($random_file, '.html');
                $new_filename = 'generatelayer2/new_' . $original_title . '.html';
                file_put_contents($new_filename, $new_content);

                // Redirect kembali ke halaman generatelayer2.php
                header("Location: generatelayer2.php?success=1");
                exit;
                
            } else {
                echo "<script>alert('Konten Layer 2 tidak valid. Konten file HTML tidak diperbarui.');</script>";
                header("Location: generatelayer2.php");
                exit;
            }
        } else {
            echo "<script>alert('Tidak ada file HTML yang tersedia untuk diperbarui.');</script>";
            header("Location: generatelayer2.php");
                exit;
        }
    } else {
        echo "<script>alert('Konten Layer 2 kosong. Masukkan konten untuk diperbarui.');</script>";
        header("Location: generatelayer2.php");
                exit;
    }
}
?>