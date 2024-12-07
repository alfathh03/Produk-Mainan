<?php
// Start a session to store product data and user info
session_start();

// Database connection variables
$DB_HOST = 'localhost'; // or your database host
$DB_USER = 'root'; // your database username
$DB_PASS = ''; // your database password
$DB_NAME = 'db_komotoys'; // your database name

// Create a connection to the database
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle user authentication with cookies (a simple example)
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Simple authentication (you can replace this with a proper DB check)
    if ($username === 'admin' && $password === 'admin123') {
        // Set a cookie for the user for 30 days
        setcookie('user', $username, time() + (86400 * 30), "/"); // 30 days expiration
        $_SESSION['user'] = $username;
    } else {
        $error = "Invalid credentials";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    // Delete the cookie by setting its expiration time in the past
    setcookie('user', '', time() - 3600, "/"); // Delete the cookie
    session_destroy(); // Destroy session
    header("Location: index.php");
    exit();
}

// Handle product addition
if (isset($_POST['addProduct'])) {
    $productName = $_POST['productName'];
    $productStock = $_POST['productStock'];
    $productImage = $_FILES['productImage'];

    // Handle image upload
    $imagePath = "";
    if ($productImage['error'] == 0) {
        $targetDir = "uploads/";
        $imagePath = $targetDir . basename($productImage["name"]);
        move_uploaded_file($productImage["tmp_name"], $imagePath);
    }

    // Insert product into the database
    if ($imagePath) {
        $stmt = $mysqli->prepare("INSERT INTO products (name, image, stock) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $productName, $imagePath, $productStock);
        $stmt->execute();
        $stmt->close();
    }
}

// Function to display products from the database
function displayProducts() {
    global $mysqli;
    $result = $mysqli->query("SELECT * FROM products");

    while ($product = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$product['id']}</td>
                <td>{$product['name']}</td>
                <td><img src='{$product['image']}' alt='Image' style='width: 50px;'></td>
                <td>{$product['stock']}</td>
            </tr>";
    }
}

// Function to generate PDF report of products
if (isset($_GET['download-pdf'])) {
    require('fpdf.php');  // Ensure fpdf.php is included in the same directory

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Title of the PDF
    $pdf->Cell(0, 10, 'Komo Toys - Produk Report', 0, 1, 'C');
    $pdf->Ln(10);

    // Table headers
    $pdf->Cell(20, 10, 'No.', 1);
    $pdf->Cell(50, 10, 'Nama Barang', 1);
    $pdf->Cell(50, 10, 'Gambar', 1);
    $pdf->Cell(50, 10, 'Stok', 1);
    $pdf->Ln();

    // Fetch product data and populate the table
    $result = $mysqli->query("SELECT * FROM products");
    $index = 1;
    while ($product = $result->fetch_assoc()) {
        $pdf->Cell(20, 10, $index++, 1);
        $pdf->Cell(50, 10, $product['name'], 1);
        $pdf->Cell(50, 10, $product['image'], 1);  // For simplicity, adding the image URL or path
        $pdf->Cell(50, 10, $product['stock'], 1);
        $pdf->Ln();
    }

    // Output the PDF to the browser for download
    $pdf->Output('D', 'product_report.pdf');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komo Toys - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>

    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', Arial;
        }

        body {
            display: flex;
            background-color: #f4f4f4;
        }

        .sidebar {
            width: 220px;
            background-color: #1c1c1c;
            color: #fff;
            padding: 20px;
            position: fixed;
            height: 100%;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h1 {
            font-size: 24px;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin-bottom: 20px;
            font-size: 18px;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
        }

        .sidebar ul li.active {
            font-weight: bold;
        }

        .content {
            margin-left: 240px;
            padding: 30px;
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }

        .header i {
            font-size: 24px;
        }

        .user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .breadcrumb {
            margin: 20px 0;
            font-size: 16px;
        }

        .breadcrumb a {
            color: #000;
            text-decoration: none;
        }

        .breadcrumb span {
            color: #888;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .btn-add {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .btn-add i {
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #f8f8f8;
        }

        .btn-delete {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .popup-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }

        .popup-container.active {
            display: flex;
        }

        .popup-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            width: 400px;
            text-align: center;
        }

        .popup-box h3 {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .close-btn, #submit-order {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .close-btn {
            background-color: #6c757d;
        }

        .popup-container .active {
            display: block;
        }

        .text-red {
            color: red;
        }

    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Komo Toys</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-globe"></i>Website</a></li>
            <li><a href="#"><i class="fas fa-user"></i>Dashboard</a></li>
            <li class="active"><a href="#"><i class="fas fa-box"></i>Stok Barang</a></li>
            <li><a href="admin.php"><i class="fas fa-shopping-cart"></i>Pesanan</a></li>
            <li><a href="#"><i class="fas fa-users"></i>Menu User</a></li>
            <li><a href="#"><i class="fas fa-key"></i>Data Login</a></li>
            <li><a href="#"><i class="fas fa-cogs"></i>Setting Website</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <div class="role-manager">Role Manager</div>
            <div class="user">
                <i class="fas fa-cog"></i>
                <?php if (isset($_SESSION['user'])): ?>
                    <img src="https://via.placeholder.com/40" alt="User">
                    <span><?php echo $_SESSION['user']; ?></span>
                    <a href="?logout=true">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>

        <h3>Stok Barang</h3>

        <div class="product-form-wrapper">
            <button class="btn-add" onclick="document.getElementById('addProductForm').style.display='block'">
                <i class="fas fa-plus"></i>Tambah Barang
            </button>
        </div>

        <!-- Button to download PDF report -->
        <a href="?download-pdf=true" class="btn-add">
            <i class="fas fa-download"></i>Download Laporan PDF
        </a>

        <div class="product-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Gambar</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody id="product-table-body">
                    <?php displayProducts(); ?> <!-- This should work if the function is defined properly -->
                </tbody>
            </table>
        </div>

        <div id="addProductForm" style="display:none; margin-top: 20px;">
            <h3>Tambah Barang</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="productName">Nama Barang</label>
                    <input id="productName" name="productName" type="text" required />
                </div>
                <div class="form-group">
                    <label for="productImage">Gambar Barang</label>
                    <input id="productImage" name="productImage" type="file" required />
                </div>
                <div class="form-group">
                    <label for="productStock">Stok Barang</label>
                    <input id="productStock" name="productStock" type="number" required />
                </div>
                <div class="form-group">
                    <button type="submit" name="addProduct">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
