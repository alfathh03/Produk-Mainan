<?php
session_start();

// Initialize an empty order array if it doesn't exist
if (!isset($_SESSION['orders'])) {
    $_SESSION['orders'] = [];
}

// Add order to the session array
if (isset($_POST['submit-order'])) {
    $newOrder = [
        'name' => $_POST['orderName'],
        'product' => $_POST['orderProduct'],
        'address' => $_POST['orderAddress'],
        'quantity' => $_POST['orderQuantity'],
        'price' => $_POST['orderPrice'],
        'status' => 'Belum Dikirim'
    ];
    $_SESSION['orders'][] = $newOrder;
}

// Delete order from session array
if (isset($_GET['delete'])) {
    $index = $_GET['delete'];
    unset($_SESSION['orders'][$index]);
    $_SESSION['orders'] = array_values($_SESSION['orders']); // Reindex array after delete
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komo Toys - Admin</title>
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
            <li><i class="fas fa-link"></i>Data Login</li>
            <li><i class="fas fa-cog"></i>Setting Website</li>
            <li><i class="fas fa-th-large"></i>Layout Setting</li>
        </ul>
    </div>
    <div class="content">
        <div class="header">
            <i class="fas fa-bars"></i>
            <h2>Pesanan</h2>
            <div class="user">
                <img src="https://via.placeholder.com/40" alt="User Avatar">
                <span>Admin</span>
            </div>
        </div>
        <div class="breadcrumb">
            <a href="#">Home</a> > <span>Pesanan</span>
        </div>
        <div class="card">
            <h2>Data Pesanan</h2>
            <button class="btn-add" id="edit-order-button"><i class="fas fa-plus"></i>Tambah Pesanan</button>
            <table id="order-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Produk</th>
                        <th>Alamat</th>
                        <th>Kuantitas</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Display orders
                    foreach ($_SESSION['orders'] as $index => $order) {
                        echo "<tr>
                                <td>" . ($index + 1) . "</td>
                                <td>" . htmlspecialchars($order['name']) . "</td>
                                <td>" . htmlspecialchars($order['product']) . "</td>
                                <td>" . htmlspecialchars($order['address']) . "</td>
                                <td>" . htmlspecialchars($order['quantity']) . "</td>
                                <td>" . htmlspecialchars($order['price']) . "</td>
                                <td><span class='text-red'>" . htmlspecialchars($order['status']) . "</span></td>
                                <td><a href='?delete=$index' class='btn-delete'>Hapus</a></td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Popup untuk menambahkan pesanan -->
    <div class="popup-container" id="popup-container">
        <div class="popup-box">
            <h3>Edit Pesanan</h3>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="orderName">Nama:</label>
                    <input type="text" id="orderName" name="orderName" placeholder="Masukkan Nama"/>
                </div>
                <div class="form-group">
                    <label for="orderProduct">Produk:</label>
                    <input type="text" id="orderProduct" name="orderProduct" placeholder="Masukkan Nama Produk"/>
                </div>
                <div class="form-group">
                    <label for="orderAddress">Alamat:</label>
                    <input type="text" id="orderAddress" name="orderAddress" placeholder="Masukkan Alamat"/>
                </div>
                <div class="form-group">
                    <label for="orderQuantity">Kuantitas:</label>
                    <input type="number" id="orderQuantity" name="orderQuantity" placeholder="Masukkan Kuantitas"/>
                </div>
                <div class="form-group">
                    <label for="orderPrice">Harga:</label>
                    <input type="number" id="orderPrice" name="orderPrice" placeholder="Masukkan Harga"/>
                </div>
                <button type="button" class="close-btn" id="close-popup">Tutup</button>
                <button type="submit" name="submit-order" id="submit-order">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Popup konfirmasi setelah menambah pesanan -->
    <div class="popup-container" id="confirmation-popup">
        <div class="popup-box">
            <h3>Pesanan Ditambahkan!</h3>
            <p>Pesanan baru telah berhasil ditambahkan.</p>
            <button class="close-btn" id="close-confirmation">Tutup</button>
        </div>
    </div>

    <script>
        document.getElementById('edit-order-button').onclick = function() {
            document.getElementById('popup-container').classList.add('active');
        };

        document.getElementById('close-popup').onclick = function() {
            document.getElementById('popup-container').classList.remove('active');
        };

        document.getElementById('close-confirmation').onclick = function() {
            document.getElementById('confirmation-popup').classList.remove('active');
        };
    </script>
</body>
</html>
