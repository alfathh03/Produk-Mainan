<?php
session_start();

// Jika pengguna login, atur session atau cookie untuk menyimpan data pengguna
if (isset($_POST['login'])) {
    $_SESSION['user'] = $_POST['username'];
    setcookie("user", $_POST['username'], time() + (86400 * 30), "/"); // Cookie berlaku selama 30 hari
}

// Fungsi logout untuk menghapus session dan cookie
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie("user", "", time() - 3600, "/");
    header("Location: index.php"); // Redirect ke halaman utama setelah logout
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>komo toys</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .header .nav {
            display: flex;
            gap: 20px;
        }
        .header .nav a {
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }
        .header .nav .masuk {
            background-color: #00c853;
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
        }
        .header .nav .profile {
            font-size: 20px;
        }
        .container {
            display: flex;
            padding: 40px;
        }
        .sidebar {
            width: 200px;
            margin-right: 40px;
        }
        .sidebar .kategori {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .sidebar .menu {
            list-style: none;
            padding: 0;
        }
        .sidebar .menu li {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .sidebar .menu li a {
            text-decoration: none;
            color: #000;
            font-weight: 500;
            margin-left: 10px;
        }
        .sidebar .menu li a.active {
            background-color: #f0f0f0;
            padding: 10px 20px;
            border-radius: 20px;
        }
        .content {
            flex: 1;
        }
        .content .hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        .content .hero img {
            width: 50%;
            border-radius: 20px;
        }
        .content .hero .text {
            width: 40%;
        }
        .content .hero .text h1 {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .content .hero .text p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .content .hero .text .search {
            display: flex;
            align-items: center;
        }
        .content .hero .text .search input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px 0 0 20px;
            outline: none;
        }
        .content .hero .text .search button {
            padding: 10px 20px;
            border: none;
            background-color: #00c853;
            color: #fff;
            border-radius: 0 20px 20px 0;
            cursor: pointer;
        }
        .content .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .content .gallery .item {
            width: calc(25% - 20px);
            border-radius: 20px;
            overflow: hidden;
        }
        .content .gallery .item img {
            width: 100%;
            display: block;
        }
        .content .products {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 40px;
        }
        .content .products .product {
            width: calc(25% - 20px);
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .content .products .product img {
            width: 100%;
            display: block;
        }
        .content .products .product .info {
            padding: 20px;
        }
        .content .products .product .info h3 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .content .products .product .info p {
            font-size: 14px;
            color: #888;
            margin-bottom: 20px;
        }
        .content .products .product .info button {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #f0f0f0;
            border-radius: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Komo Toys</div>
        <div class="nav">
            <a href="#">Semua Produk</a>
            <a href="#">Unggulan</a>
            <a href="#">Baru</a>
            <a href="#">Diskon</a>
            <a href="#">Admin</a>
            <?php if (isset($_SESSION['user']) || isset($_COOKIE['user'])): ?>
                <a href="?logout=true" class="masuk">Keluar</a>
                <a class="profile" href="#">
                    <i class="fas fa-user-circle"></i>
                </a>
            <?php else: ?>
                <a href="login.php" class="masuk">Masuk</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">
        <div class="sidebar">
            <div class="kategori">Kategori</div>
            <ul class="menu">
                <li><a class="active" href="#"><i class="fas fa-th-list"></i> Semua Produk</a></li>
                <li><a href="#"><i class="fas fa-star"></i> Unggulan</a></li>
                <li><a href="#"><i class="fas fa-plus"></i> Baru</a></li>
                <li><a href="#"><i class="fas fa-tags"></i> Diskon</a></li>
                <li><a href="#"><i class="fas fa-gift"></i> Hadiah</a></li>
            </ul>
        </div>
        <div class="content">
            <div class="hero">
                <img alt="Mainan anak dengan latar belakang hijau" height="400" src="https://storage.googleapis.com/a1aa/image/TgEcnMtLiwJMM1tIbH5KdWFDUfoKWL8cir8gZ8E9ZqffhpKnA.jpg" width="600"/>
                <div class="text">
                    <h1>Kegembiraan Bermain</h1>
                    <p>Imajinasi menjadi nyata dengan koleksi mainan anak, permainan dan lainnya.</p>
                    <div class="search">
                        <input placeholder="Cari mainan, permainan dan lain" type="text"/>
                        <button>Cari</button>
                    </div>
                </div>
            </div>
            <div class="gallery">
                <div class="item">
                    <img alt="Rumah boneka" height="150" src="https://storage.googleapis.com/a1aa/image/F0tfGBurfDhvxkCbeaecevu1vk1235i8udylE4FgtbLmImqcC.jpg" width="200"/>
                </div>
                <div class="item">
                    <img alt="Helikopter mainan" height="150" src="https://storage.googleapis.com/a1aa/image/JVehFtJeU1nzR0MgTEpt6GwutJdmjVVGpwR6TIZGLwVCxUlTA.jpg" width="200"/>
                </div>
                <div class="item">
                    <img alt="Mobil mainan" height="150" src="https://storage.googleapis.com/a1aa/image/Vpy3eFaa70TcQ6rNn4QMZSouIfBNStHKjFssxS42ciK8wUlTA.jpg" width="200"/>
                </div>
                <div class="item">
                    <img alt="Mainan dinding" height="150" src="https://storage.googleapis.com/a1aa/image/f94vgkZpjCUaRyYG2xOA0l92f2goz2llEhMkf0BFulPyhpKnA.jpg" width="200"/>
                </div>
                <div class="item">
                    <img alt="Mainan boneka" height="150" src="https://storage.googleapis.com/a1aa/image/7F2Cb6ibBwIAMxhFNe8aMZXnEa8dp7docLxPFFlmaKeDxUlTA.jpg" width="200"/>
                </div>
                <div class="item">
                    <img alt="Mainan pemandangan" height="150" src="https://storage.googleapis.com/a1aa/image/lHwmPl8mTUrdJ1svee8mHTqv9p9zPHVxM2zG2Hii7RoAxUlTA.jpg" width="200"/>
                </div>
            </div>
            <div class="products">
                <div class="product">
                    <img alt="Rumah boneka" height="150" src="https://storage.googleapis.com/a1aa/image/F0tfGBurfDhvxkCbeaecevu1vk1235i8udylE4FgtbLmImqcC.jpg" width="200"/>
                    <div class="info">
                        <h3>Rumah Boneka &amp; Set Mainan</h3>
                        <p>Belanja Sekarang</p>
                        <button>Lihat Semua</button>
                    </div>
                </div>
                <div class="product">
                    <img alt="Kereta mainan" height="150" src="https://storage.googleapis.com/a1aa/image/OwSTxuzum45TCl8eX6deXVQ6UpkgKl7pTeAg5dC9vjq7hpKnA.jpg" width="200"/>
                    <div class="info">
                        <h3>Kereta &amp; Set Kereta Mainan</h3>
                        <p>Belanja Sekarang</p>
                        <button>Lihat Semua</button>
                    </div>
                </div>
                <div class="product">
                    <img alt="Boneka beruang" height="150" src="https://storage.googleapis.com/a1aa/image/7V4DZX3PdHbiJloLqCbNoMWL9hulxFuvor02DOj4tWnOMV5E.jpg" width="200"/>
                    <div class="info">
                        <h3>Boneka &amp; Boneka Beruang</h3>
                        <p>Belanja Sekarang</p>
                        <button>Lihat Semua</button>
                    </div>
                </div>
                <div class="product">
                    <img alt="Set lego" height="150" src="https://storage.googleapis.com/a1aa/image/ZBeGC7l8gaWRPCgvw4zAJqI8rqdX4sEe6TmFfb4sPpqNipKnA.jpg" width="200"/>
                    <div class="info">
                        <h3>Set Lego &amp; Bangunan</h3>
                        <p>Belanja Sekarang</p>
                        <button>Lihat Semua</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
