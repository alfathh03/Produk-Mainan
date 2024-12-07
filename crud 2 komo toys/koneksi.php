<?php
session_start();

// Database connection
$host = "localhost"; // Change if necessary
$dbname = "db_komotoys"; // Your database name
$username_db = "root"; // Your database username
$password_db = ""; // Your database password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username_db, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Authentication function
function authenticate($username, $password, $conn) {
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return true;
    }
    return false;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    if (authenticate($username, $password, $conn)) {
        $_SESSION["username"] = $username;

        // Set a cookie if "Remember Me" is checked
        if (isset($_POST["remember"])) {
            setcookie("username", $username, time() + (86400 * 30), "/"); // Expires in 30 days
        }

        $loginSuccess = true;
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
        /* Include the original CSS here */
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
            <button type="submit">Sign In</button>
        </form>
        
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
