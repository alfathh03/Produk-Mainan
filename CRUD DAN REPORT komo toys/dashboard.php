<?php
session_start();

// Koneksi ke database
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'db_komotoys';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Menyimpan data pesanan baru
if (isset($_POST['submit-order'])) {
    $name = $_POST['orderName'];
    $product = $_POST['orderProduct'];
    $address = $_POST['orderAddress'];
    $quantity = (int)$_POST['orderQuantity'];
    $price = (float)$_POST['orderPrice'];
    $status = 'Belum Dikirim';

    $stmt = $conn->prepare("INSERT INTO orders (name, product, address, quantity, price, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssdis', $name, $product, $address, $quantity, $price, $status);
    $stmt->execute();
    $stmt->close();
}

// Mengambil data pesanan dari database
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Edit pesanan
if (isset($_POST['edit-order'])) {
    $index = (int)$_POST['editIndex'];
    $name = $_POST['orderName'];
    $product = $_POST['orderProduct'];
    $address = $_POST['orderAddress'];
    $quantity = (int)$_POST['orderQuantity'];
    $price = (float)$_POST['orderPrice'];
    $status = $_POST['orderStatus'];

    $stmt = $conn->prepare("UPDATE orders SET name=?, product=?, address=?, quantity=?, price=?, status=? WHERE id=?");
    $stmt->bind_param('sssdisi', $name, $product, $address, $quantity, $price, $status, $index);
    $stmt->execute();
    $stmt->close();
}

// Hapus pesanan
if (isset($_GET['delete'])) {
    $index = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
    $stmt->bind_param('i', $index);
    $stmt->execute();
    $stmt->close();
}

// Filter pesanan
$filteredOrders = $orders;
if (isset($_POST['filter'])) {
    $filterName = $_POST['filterName'];
    $filterStatus = $_POST['filterStatus'];

    $filteredOrders = array_filter($orders, function ($order) use ($filterName, $filterStatus) {
        $nameMatch = empty($filterName) || stripos($order['name'], $filterName) !== false;
        $statusMatch = empty($filterStatus) || $order['status'] === $filterStatus;
        return $nameMatch && $statusMatch;
    });
}

// Statistik
$totalOrders = count($orders);
$totalRevenue = array_reduce($orders, function ($carry, $order) {
    return $carry + ($order['quantity'] * $order['price']);
}, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pesanan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    
    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
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

        /* Popup Styles */
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
        <h1>Komo Toys</h1>
        <ul>
            <li><i class="fas fa-globe"></i><a href="index.php" class="anafbar">Website</a></li>
            <li><i class="fas fa-users"></i>Dashboard</li>
            <li><i class="fas fa-cubes"></i><a href="kategori.php" class="anafbar">Stok Barang</a></li>
            <li class="active"><i class="fas fa-briefcase"></i>Pesanan</li>
            <li><i class="fas fa-bars"></i>Menu User</li>
            <li><i class="fas fa-sign-out-alt"></i><a href="logout.php" class="anafbar">Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <div class="user">
                <img src="user.jpg" alt="User">
                <div class="user-info">
                    <h4>Admin</h4>
                    <p>admin@komotoys.com</p>
                </div>
            </div>
            <i class="fas fa-bell"></i>
        </div>

        <div class="breadcrumb">
            <a href="#">Dashboard</a> <span>/</span> <span>Pesanan</span>
        </div>

        <div class="card">
            <h2>Daftar Pesanan</h2>
            <button class="btn-add" onclick="showPopup()">Tambah Pesanan <i class="fas fa-plus"></i></button>
            
            <!-- Filter Form -->
            <form method="POST" style="margin-bottom: 20px;">
                <input type="text" name="filterName" placeholder="Cari Nama..." style="padding: 10px; margin-right: 10px;">
                <select name="filterStatus" style="padding: 10px; margin-right: 10px;">
                    <option value="">-- Semua Status --</option>
                    <option value="Belum Dikirim">Belum Dikirim</option>
                    <option value="Dikirim">Dikirim</option>
                </select>
                <button type="submit" name="filter" class="btn-add">Filter</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Produk</th>
                        <th>Alamat</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filteredOrders as $order): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['name']); ?></td>
                        <td><?php echo htmlspecialchars($order['product']); ?></td>
                        <td><?php echo htmlspecialchars($order['address']); ?></td>
                        <td><?php echo $order['quantity']; ?></td>
                        <td>Rp<?php echo number_format($order['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td>
                            <button onclick="editOrder(<?php echo $order['id']; ?>)">Edit</button>
                            <a href="?delete=<?php echo $order['id']; ?>" class="btn-delete">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Statistik -->
            <div class="card">
                <h3>Total Pesanan: <?php echo $totalOrders; ?></h3>
                <h3>Total Pendapatan: Rp<?php echo number_format($totalRevenue, 2); ?></h3>
            </div>
        </div>
    </div>

    <!-- Popup Form -->
    <div class="popup-container" id="popup-container">
        <div class="popup-box">
            <h3>Tambah Pesanan</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="orderName">Nama</label>
                    <input type="text" name="orderName" id="orderName" required>
                </div>
                <div class="form-group">
                    <label for="orderProduct">Produk</label>
                    <input type="text" name="orderProduct" id="orderProduct" required>
                </div>
                <div class="form-group">
                    <label for="orderAddress">Alamat</label>
                    <input type="text" name="orderAddress" id="orderAddress" required>
                </div>
                <div class="form-group">
                    <label for="orderQuantity">Jumlah</label>
                    <input type="number" name="orderQuantity" id="orderQuantity" required>
                </div>
                <div class="form-group">
                    <label for="orderPrice">Harga</label>
                    <input type="number" name="orderPrice" id="orderPrice" required>
                </div>
                <button type="submit" name="submit-order" id="submit-order">Simpan Pesanan</button>
                <button type="button" class="close-btn" onclick="closePopup()">Batal</button>
            </form>
        </div>
    </div>

    <script>
        function showPopup() {
            document.getElementById('popup-container').classList.add('active');
        }

        function closePopup() {
            document.getElementById('popup-container').classList.remove('active');
        }

        function editOrder(id) {
            const popup = document.getElementById('popup-container');
            popup.classList.add('active');

            // Fetch order from the database
            const order = <?php echo json_encode($orders); ?>.find(order => order.id === id);
            
            document.getElementById('orderName').value = order.name;
            document.getElementById('orderProduct').value = order.product;
            document.getElementById('orderAddress').value = order.address;
            document.getElementById('orderQuantity').value = order.quantity;
            document.getElementById('orderPrice').value = order.price;
        }
    </script>
</body>
</html>
