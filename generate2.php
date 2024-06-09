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

            // Hapus isi dari semua href
            $random_content = preg_replace('/<a\s+[^>]*?href="[^"]*"/i', '<a href=""', $random_content);

            // Periksa apakah isi file HTML yang dipilih memiliki tag html lengkap atau tidak
            if (strpos($random_content, '</html>') === false) {
                // Jika tidak lengkap, lengkapi dengan tag html, head, dan body
                $random_content = '<html><head></head><body>' . $random_content . '</body></html>';
            }

            // Mengganti href pada a dengan rel="dofollow"
            if (strpos($random_content, 'rel="dofollow"') !== false) {
                $random_content = preg_replace('/<a\s+([^>]*?rel="dofollow"[^>]*?)href="[^"]*"/i', '<a $1href="https://tol.com"', $random_content);
            }

            // Memeriksa apakah string $random_content mengandung salah satu domain yang ingin diganti
            if (strpos($random_content, 'http://www.info-beasiswa.id/') !== false || strpos($random_content, 'http://www.mediabisnis.co.id/') !== false) {
                // Mengganti domain tertentu dengan tol.com
                $random_content = str_replace(
                    array(
                        'http://www.info-beasiswa.id/',
                        'http://www.mediabisnis.co.id/'
                    ),
                    'https://tol.com',
                    $random_content
                );
            }

            // Parafrase konten $random_content
            $random_content = paraphraseContent($random_content);

            // Memeriksa apakah konten Layer 2 valid (misalnya, mengandung </body>)
            if (strpos($random_content, '</body>') !== false && strpos($random_content, '</html>') !== false) {
                // Ambil konten Layer 2 yang dimasukkan oleh pengguna
                $layer2_content = htmlspecialchars($_POST['layer2_content']);

                // Menyiapkan tombol "Lihat file" dengan JavaScript
                $vue_script = '<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>';
                $vue_script .= '<div id="app"><button v-if="counter > 0" style="background-color: blue; color: white; display: block; margin: 0 auto; padding: 15px 32px; border: none; border-radius: 12px; cursor: pointer; font-size: 16px; transition: background-color 0.3s;" v-text="`Lihat file (${counter} detik)`" @click="startCountdown"></button><a v-else href="#bottom" style="background-color: blue; color: white; display: block; margin: 0 auto; padding: 15px 32px; border: none; border-radius: 12px; cursor: pointer; font-size: 16px; text-align: center; text-decoration: none;">Ke bawah</a></div>';
                $vue_script .= '<script>new Vue({ el: "#app", data: { counter: 15, interval: null }, methods: { startCountdown() { if (!this.interval) { this.interval = setInterval(() => { if (this.counter > 0) { this.counter--; } else { clearInterval(this.interval); this.interval = null; document.getElementById("download1").style.display = "inline-block"; document.getElementById("download2").style.display = "inline-block"; document.getElementById("download3").style.display = "inline-block"; } }, 1000); } } } });</script>';

                // Menyiapkan tiga tombol download dengan href yang berbeda
                $download_buttons = '<div id="bottom" style="text-align: center; margin-top: 20px;">
                                        <button id="download1" style="background-color: #4CAF50; color: white; display: none; padding: 15px 32px; border: none; border-radius: 12px; cursor: pointer; font-size: 16px; margin: 4px 2px; transition: background-color 0.3s;"><a href="' . $layer2_content . '" target="_blank" style="color: white; text-decoration: none;">Download</a></button>
                                        <button id="download2" style="background-color: #4CAF50; color: white; display: none; padding: 15px 32px; border: none; border-radius: 12px; cursor: pointer; font-size: 16px; margin: 4px 2px; transition: background-color 0.3s;"><a href="https://doomdefender.com/srnkvd12d?key=6a6a351610e04ee3c24395542460d518" target="_blank" style="color: white; text-decoration: none;">Download </a></button>
                                        <button id="download3" style="background-color: #4CAF50; color: white; display: none; padding: 15px 32px; border: none; border-radius: 12px; cursor: pointer; font-size: 16px; margin: 4px 2px; transition: background-color 0.3s;"><a href="https://doomdefender.com/srnkvd12d?key=6a6a351610e04ee3c24395542460d518" target="_blank" style="color: white; text-decoration: none;">Download </a></button>
                                    </div>';

                // Memasukkan script Vue.js dan tombol "Lihat file" setelah elemen header terdekat
                $new_content = str_replace('</head>', '</head>' . $vue_script, $random_content);

                // Menyisipkan tombol download setelah tag </body>
                $new_content = str_replace('</body>', $download_buttons . '</body>', $new_content);

                // Script JavaScript untuk pengacakan href setiap 20 detik
                $randomize_script = '<script>
                    function shuffleDownloadLinks() {
                        const links = [
                            "' . $layer2_content . '",
                            "https://doomdefender.com/srnkvd12d?key=6a6a351610e04ee3c24395542460d518",
                            "https://doomdefender.com/srnkvd12d?key=6a6a351610e04ee3c24395542460d518"
                        ];
                        for (let i = links.length - 1; i > 0; i--) {
                            const j = Math.floor(Math.random() * (i + 1));
                            [links[i], links[j]] = [links[j], links[i]];
                        }
                        document.querySelector("#download1 a").href = links[0];
                        document.querySelector("#download2 a").href = links[1];
                        document.querySelector("#download3 a").href = links[2];
                    }
                    setInterval(shuffleDownloadLinks, 20000); // Setiap 20 detik
                    window.onload = shuffleDownloadLinks; // Saat halaman dimuat
                </script>';

                // Menyisipkan script pengacakan setelah tag </body
                $new_content = str_replace('</body>', $randomize_script . '</body>', $new_content);

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

// Fungsi untuk melakukan parafrase konten
function paraphraseContent($content) {
    // Contoh sederhana parafrase dengan mengganti beberapa kata dan struktur kalimat dalam bahasa Indonesia
    $paraphrases = array(
        'konten' => 'isi',
        'file' => 'dokumen',
        'html' => 'HTML',
        'href' => 'referensi tautan',
        'yang' => 'yang mana',
        'dan' => 'serta',
        'untuk' => 'guna',
        'dengan' => 'dengan cara',
        'atau' => 'ataupun',
        'jika' => 'apabila',
        'tidak' => 'tidaklah',
        'ini' => 'hal ini',
        'adalah' => 'merupakan',
        'akan' => 'bakal',
        'memeriksa' => 'mengecek',
        'memilih' => 'memutuskan',
        'salah satu' => 'satu di antara',
        'mengganti' => 'mengubah',
        'mengandung' => 'berisi',
        'domain' => 'wilayah',
        'tertentu' => 'khusus',
        'dengan' => 'serta',
        'konten' => 'materi',
        'memasukkan' => 'menyisipkan',
        'script' => 'skrip',
        'file' => 'berkas',
        'download' => 'unduh',
        'perubahan' => 'modifikasi',
        'save' => 'menyimpan',
        'success' => 'berhasil',
        'valid' => 'sah',
        'alert' => 'peringatan',
        'location' => 'lokasi',
        'empty' => 'kosong',
        'masukkan' => 'inputkan',
        'generate' => 'hasilkan',
        'button' => 'tombol',
        'head' => 'kepala',
        'body' => 'tubuh',
        'header' => 'tajuk',
        'parafrase' => 'penguraian kata',
        'sederhana' => 'simple',
        'mengubah' => 'mengganti',
        'struktur' => 'susunan',
        'kalimat' => 'ayat',
        'bahasa' => 'language',
        'indonesia' => 'Indonesian',
        'dalam' => 'di dalam',
        'serta' => 'dan juga',
        'dari' => 'dari pada',
        'ke' => 'ke arah',
        'kepada' => 'kepada',
        'pada' => 'di',
        'baik' => 'bagus',
        'buruk' => 'jelek',
        'cepat' => 'laju',
        'lambat' => 'pelan',
        'besar' => 'luas',
        'kecil' => 'mini',
        'panjang' => 'lama',
        'pendek' => 'singkat',
        'tinggi' => 'jangkung',
        'rendah' => 'ceper',
        'kuat' => 'perkasa',
        'lemah' => 'rapuh',
        'baru' => 'segar',
        'lama' => 'usang',
        'mudah' => 'gampang',
        'sulit' => 'sukar',
        'panas' => 'gerah',
        'dingin' => 'sejuk',
        'terang' => 'cerah',
        'gelap' => 'suram',
        'sehat' => 'bugar',
        'sakit' => 'penyakit',
        'indah' => 'elok',
        'buruk' => 'jelek',
        'cantik' => 'menawan',
        'tampan' => 'ganteng',
        'bijak' => 'arif',
        'bodoh' => 'dungu',
        'senang' => 'gembira',
        'sedih' => 'duka',
        'kaya' => 'makmur',
        'miskin' => 'melarat',
        'pendidikan' => 'pengajaran',
        'pekerjaan' => 'kerjaan',
        'rumah' => 'kediaman',
        'mobil' => 'kendaraan',
        'motor' => 'sepeda motor',
        'sepeda' => 'ontel',
        'teman' => 'sahabat',
        'keluarga' => 'sanak saudara',
        'makanan' => 'pangan',
        'minuman' => 'beverage',
        'listrik' => 'energi',
        'air' => 'cairan',
        'tanah' => 'bumi',
        'laut' => 'samudra',
        'gunung' => 'pegunungan',
        'hutan' => 'rimba',
        'udara' => 'angin',
        'langit' => 'angkasa',
        'bintang' => 'cahaya',
        'bulan' => 'rembulan',
        'matahari' => 'surya',
        'pagi' => 'fajar',
        'siang' => 'tengah hari',
        'sore' => 'senja',
        'malam' => 'larut',
        'hari' => 'waktu',
        'minggu' => 'pekan',
        'bulan' => 'masa',
        'tahun' => 'periode',
        'umur' => 'usia',
        'hidup' => 'nyawa',
        'mati' => 'meninggal',
        'lahir' => 'terbit',
        'tua' => 'lanjut usia',
        'muda' => 'belia',
        'anak' => 'bocah',
        'orang' => 'manusia',
        'pria' => 'laki-laki',
        'wanita' => 'perempuan',
        'suami' => 'laki',
        'istri' => 'bini',
        'cinta' => 'kasih',
        'benci' => 'dendam',
        'teman' => 'kawan',
        'musuh' => 'lawan',
        'bahagia' => 'senang',
        'sedih' => 'muram',
        'ketawa' => 'tertawa',
        'menangis' => 'berlinang',
        'sakit' => 'nyeri',
        'sehat' => 'fit',
        'makan' => 'santap',
        'minum' => 'teguk',
        'tidur' => 'lelap',
        'bangun' => 'terjaga',
        'pergi' => 'berangkat',
        'datang' => 'tiba',
        'pulang' => 'balik',
        'tinggal' => 'huni',
        'pergi' => 'berangkat',
        'berbicara' => 'bicara',
        'diam' => 'bungkam',
        'melihat' => 'menonton',
        'mendengar' => 'menyimak',
        'bermain' => 'bermain-main',
        'belajar' => 'menuntut ilmu',
        'bekerja' => 'kerja',
        'berlari' => 'berkejaran',
        'berjalan' => 'melangkah',
        'membaca' => 'melahap',
        'menulis' => 'mengarang',
        'mendengar' => 'mendengarkan',
         'melihat' => 'memandang'
    );

    // Mengganti kata-kata dalam konten
    foreach ($paraphrases as $original => $replacement) {
        $content = preg_replace('/\b' . preg_quote($original, '/') . '\b/', $replacement, $content);
    }

    // Contoh sederhana mengubah struktur kalimat
    $content = preg_replace('/(Ini adalah contoh kalimat yang)/', 'Berikut merupakan contoh kalimat yang mana', $content);

    return $content;
}
?>
