<?php
//hubungkan dengan koneksi.php
include "koneksi.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Cek apakah dapat terkoneksi langsung menggunakan informasi yang diberikan
    $conn = new mysqli("localhost", "root", "", "generatorlink");
    if ($conn->connect_error) {
        // Jika tidak dapat terkoneksi secara langsung, gunakan API
        $api_url = "https://sql303.infinityfree.com:2083/cpsess1234567890/json-api/cpanel?cpanel_jsonapi_user=if0_36451537&cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=MysqlFE&cpanel_jsonapi_func=connectdb";
        $api_username = "if0_36451537";
        $api_password = "QAbuR1ao8aoxmr";

        $api_data = array(
            "dbname" => "if0_36451537_generatorlink",
            "dbuser" => $api_username,
            "password" => $api_password
        );

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_data));
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($result["cpanelresult"]["error"]) {
            die("Connection failed: " . $result["cpanelresult"]["error"]);
        }

        $conn = new mysqli($result["cpanelresult"]["data"]["server"], $api_username, $api_password, $result["cpanelresult"]["data"]["db"]);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    }

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        echo "Username atau password salah.";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        form {
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        input[type="text"],
        input[type="password"],
        input[type="submit"] {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h2>Login</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>
</body>
</html>
