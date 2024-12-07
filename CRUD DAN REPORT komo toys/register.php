<?php
session_start();

// Koneksi ke database
$host = "localhost"; // Ganti dengan host Anda
$dbname = "db_komotoys"; // Nama database
$username_db = "root"; // Username database
$password_db = ""; // Password database

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username_db, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi input
    if (empty($email) || empty($username) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Simpan data ke database
        try {
            $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (:email, :username, :password)");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();

            // Redirect ke halaman sukses
            header("Location: register.php?success=true");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error
                $error = "Email or username already exists.";
            } else {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komo Toys Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <style>
        /* CSS dari versi awal */
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
        .success-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 255, 0, 0.8);
            text-align: center;
            padding: 20px;
            font-size: 1.2em;
            color: white;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Register</h1>
        <form method="POST" action="">
            <input type="text" name="email" placeholder="Email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
            <input type="text" name="username" placeholder="Username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
            <input type="password" name="password" placeholder="Password" required>
            <?php if (isset($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>
            <button type="submit">Register</button>
        </form>
        <a href="login.php">Already have an account? Login here</a>
    </div>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="success-container">
            Registration successful! You can now <a href="login.php" style="color: white; text-decoration: underline;">login</a>.
        </div>
    <?php endif; ?>
</body>
</html>
