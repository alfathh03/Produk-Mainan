<?php
session_start();

// Koneksi ke database
$servername = "localhost"; // ganti dengan server Anda jika berbeda
$usernameDB = "root"; // ganti dengan username database Anda
$passwordDB = ""; // ganti dengan password database Anda
$dbname = "db_komotoys"; // nama database Anda

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fungsi autentikasi untuk memverifikasi pengguna
function authenticate($username, $password, $conn) {
    // Menghindari SQL Injection dengan menggunakan prepared statements
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika ada hasil, kita cek apakah password sesuai
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Memastikan kolom 'password' ada dan tidak null sebelum menggunakan password_verify
        if (!empty($user['password']) && password_verify($password, $user['password'])) {
            return $user;  // Mengembalikan data pengguna jika login berhasil
        }
    }
    return false;
}

// Cek jika form sudah disubmit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    $user = authenticate($username, $password, $conn);
    if ($user) {
        $_SESSION["username"] = $username;

        // Set cookie jika "Remember Me" dicentang
        if (isset($_POST["remember"])) {
            setcookie("username", $username, time() + (86400 * 30), "/"); // Expire dalam 30 hari
        }

        // Menyimpan data login ke tabel users (misalnya: waktu login)
        $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        // Redirect ke index.php setelah login berhasil
        header("Location: index.php");
        exit(); // Pastikan script berhenti setelah redirect
    } else {
        $loginSuccess = false;
        $errorMessage = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page with Popup</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('biru.jpeg');
            background-size: cover;
        }
        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        .login-container h1 {
            font-size: 36px;
            color: #3A7DFF;
            margin-bottom: 20px;
        }
        .login-container img {
            width: 40px;
            margin: 0 10px;
        }
        .login-container input[type="text"], .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background-color: #B3E5FC;
            font-size: 16px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            margin: 20px 0;
            border: none;
            border-radius: 5px;
            background-color: #5A6E5D;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }
        .login-container a {
            display: block;
            margin: 10px 0;
            color: black;
            text-decoration: none;
        }
        /* Popup Box Style */
        .popup-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .popup-box {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        .popup-box h2 {
            color: #3A7DFF;
            margin-bottom: 10px;
        }
        .popup-box p {
            color: #0ee92e;
            margin-bottom: 20px;
        }
        .popup-box button {
            background-color: #3A7DFF;
            color: rgb(236, 232, 232);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .popup-box button:hover {
            background-color: #732ab8;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>LOGIN</h1>
        <div>
            <img alt="Toy airplane" height="40" src="https://storage.googleapis.com/a1aa/image/ivTHZmvpCa5zMNbfyoNgIS584dFacM8gyiiRpYivrR1FAqyJA.jpg" width="40"/>
            <img alt="Toy truck" height="40" src="https://storage.googleapis.com/a1aa/image/syILMIY2o1pEMpzqY4fhPUeOpNPxbb2tzLumDWXGrVqMAUlTA.jpg" width="40"/>
            <img alt="Toy dinosaur" height="40" src="https://storage.googleapis.com/a1aa/image/macOUJ2R2VbpB9Ebzgw9LL2iwPA0hxRoYMdIxV3TSFQCAV5E.jpg" width="40"/>
            <img alt="Balloons" height="40" src="https://storage.googleapis.com/a1aa/image/741aa0aP2NbXIpimYIIKCi97UFdfrT0FyohkzAeM0ZPIAUlTA.jpg" width="40"/>
        </div>

        <?php if (isset($errorMessage)): ?>
            <div class="error-message"><?= $errorMessage ?></div>
        <?php endif; ?>

        <form method="POST" action="">
    <input placeholder="Username" type="text" id="username" name="username" value="<?= isset($_COOKIE["username"]) ? $_COOKIE["username"] : '' ?>" required />
    <input placeholder="Password" type="password" id="password" name="password" required />
    <label>
        <input type="checkbox" name="remember"> Remember Me
    </label>
    <!-- Sign In Link Styled Like a Button -->
    <a href="admin.php" class="signin-button">Sign In</a>
</form>

<style>
    .signin-button {
        display: inline-block;
        width: 100%;
        padding: 10px;
        margin: 20px 0;
        text-align: center;
        background-color: #5A6E5D;
        color: white;
        font-size: 18px;
        text-decoration: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .signin-button:hover {
        background-color: #3A7DFF; /* Hover color change */
    }
</style>


        <a href="#">Lupa Password</a>
        <a href="register.php" class="anafbar">Belum punya akun? </a>
    </div>

    <!-- Popup Box -->
    <div class="popup-container" id="popupContainer" style="display: <?= isset($loginSuccess) && $loginSuccess ? 'flex' : 'none' ?>">
        <div class="popup-box">
            <h2>Welcome Back!</h2>
            <p>Login berhasil! Selamat datang kembali di Komo Toys.</p>
            <button onclick="closePopup()">Close</button>
        </div>
    </div>

    <script>
        function closePopup() {
            document.getElementById('popupContainer').style.display = 'none';
        }
    </script>
</body>
</html>
