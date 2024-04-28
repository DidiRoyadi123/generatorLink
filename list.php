<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Generated Files</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
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

        ul.pagination {
            display: inline-block;
            padding: 0;
            margin: 20px 0;
        }

        ul.pagination li {
            display: inline;
        }

        ul.pagination li a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
        }

        ul.pagination li.active a {
            background-color: #4CAF50;
            color: white;
        }

        ul.pagination li a:hover:not(.active) {background-color: #ddd;}

        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
        }
        .copy-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
        }
        .excel-button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="sticky-menu">
        <a id="home" href="index.php">Home</a>
        <a id="list" class="active" href="list.php">List</a>
    </div>
    <br> <br> <br>
    <h1>List of Generated Files</h1>
 <!-- Delete All Button -->
 <?php
    function deleteAllFiles($folderPath) {
        $files = glob($folderPath . '*'); // get all file names
        foreach($files as $file) { // iterate files
            if(is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    if(isset($_POST['delete_all'])) {
        $confirmation = $_POST['confirmation'];
        if($confirmation == 'yes') {
            deleteAllFiles('view/');
            echo "<script>alert('All files deleted successfully.')</script>";
        } else {
            echo "<script>alert('Delete all operation cancelled.')</script>";
        }
    }
    ?>

    <form action="" method="post" onsubmit="return confirm('Are you sure you want to delete all files?');">
        <input type="hidden" name="confirmation" value="yes">
        <input type="submit" name="delete_all" value="Delete All">
    </form>
    <table>
        <tr>
            <th>Nomor</th>
            <th>Copy Link</th>
            <th>Link</th>
            <th>Delete</th>
        </tr>
        <?php
        // Menampilkan daftar file dengan nomor, nama file, link, tombol delete, dan tombol copy link
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
    </table>
    <!-- Pagination -->
    <ul class="pagination">
        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <li <?php if($i == $currentPage) echo 'class="active"'; ?>>
                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>

    <!-- Form untuk menyimpan daftar file ke Excel -->
    <!-- <form action="save_to_excel.php" method="post">
        <input class="excel-button" type="submit" name="save_to_excel" value="Save to Excel">
    </form> -->

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
    </script>
</body>
</html>
