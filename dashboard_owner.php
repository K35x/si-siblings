<?php
// =============================================
// FILE: dashboard_admin.php
// Fungsi: Halaman khusus untuk role ADMIN
// =============================================

session_start();
include "koneksi.php";

// ---- PROTEKSI HALAMAN ----
// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah role-nya owner
// Kalau bukan owner, tendang ke halaman kasir
if ($_SESSION['role'] != 'owner') {
    header("Location: dashboard_kasir.php");
    exit();
}

// Ambil semua data user untuk ditampilkan
$query_user = "SELECT * FROM tb_login ORDER BY id_user ASC";
$hasil_user = mysqli_query($koneksi, $query_user);

// ---- PROSES HAPUS USER ----
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];  // cast ke integer untuk keamanan

    // Jangan izinkan admin hapus akunnya sendiri
    if ($id_hapus == $_SESSION['id_user']) {
        $notif = "Tidak bisa menghapus akun sendiri!";
    } else {
        mysqli_query($koneksi, "DELETE FROM tb_login WHERE id_user = $id_hapus");
        header("Location: dashboard_owner.php?notif=hapus");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siblings.co - Owner Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-cream: #f4ece1;
            --dark-brown: #2a1a14;
            --sidebar-gradient: linear-gradient(180deg, #6d3e29 0%, #2a1a14 100%);
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            background-color: var(--bg-cream);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: 280px;
            background: var(--sidebar-gradient);
            color: white;
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar .logo {
            padding: 0 30px 40px;
            font-size: 28px;
            font-weight: bold;
            font-style: italic;
            text-decoration: underline;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding: 0 15px;
        }

        .sidebar nav a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: var(--dark-brown);
            text-decoration: none;
            background-color: var(--white);
            border-radius: 50px; /* Membuat bentuk pill/lonjong */
            font-weight: 600;
            font-size: 15px;
            transition: 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .sidebar nav a img {
            width: 20px;
            margin-right: 15px;
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* TOPBAR */
        header.topbar {
           background:linear-gradient(180deg, #6d3e29 0%, #2a1a14 100%);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .topbar .nav-top {
            display: flex;
            gap: 30px;
            font-size: 14px;
        }

        .topbar .search-box {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 25px;
            padding: 5px 20px;
            width: 350px;
        }

        .topbar .search-box input {
            border: none;
            outline: none;
            width: 100%;
            padding: 5px;
        }

        .profile-icon {
            font-size: 30px;
            margin-left: 20px;
        }

        /* CONTENT AREA */
        .content-body {
            padding: 30px 40px;
            display: flex;
            flex-direction: column;
            gap: 25px;
            overflow-y: auto;
        }

        /* GRID ATAS (Stats) */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1.5fr;
            gap: 20px;
        }

        .card {
            background: var(--white);
            border: 1.5px solid #000;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .card h2 { margin: 0; font-size: 48px; font-weight: 800; }
        .card p { margin: 5px 0 0; font-size: 16px; font-weight: 500; }

        /* GRID BAWAH (Info & Chart) */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 2fr;
            gap: 20px;
        }

        .info-box {
            background: var(--white);
            border: 1.5px solid #000;
            border-radius: 20px;
            padding: 20px;
            min-height: 400px;
            position: relative;
        }

        .info-box h3 {
            position: absolute;
            bottom: 20px;
            left: 20px;
            margin: 0;
            font-size: 18px;
        }

        .list-content {
            font-size: 12px;
            line-height: 1.6;
        }

        .chart-container {
            padding-bottom: 50px;
        }

        .chart-title {
            text-align: center;
            margin-top: 10px;
            font-weight: 600;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <aside class="sidebar">
        <div class="logo">Siblings.co</div>
        <nav>
            <a href="#"> Beranda</a>
            <a href="#"> Pesanan</a>
            <a href="#"> Stok Barang</a>
            <a href="#"> Keuangan</a>
            <a href="#"> Katalog produk</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="nav-top">
                <span>Beranda</span>
                <span>Finance</span>
            </div>
            <div style="display: flex; align-items: center;">
                <div class="search-box">
                    <span></span>
                    <input type="text" placeholder="Search">
                </div>
                <div class="profile-icon">👤</div>
            </div>
        </header>

        <div class="content-body">
            <section class="stats-grid">
                <div class="card"><h2>12</h2><p>Pesanan Baru</p></div>
                <div class="card"><h2>4</h2><p>Sedang Diproses</p></div>
                <div class="card"><h2>8</h2><p>Siap Diambil</p></div>
                <div class="card">
                    <h2>1.540.000</h2>
                    <p>Total Omzet<br>Hari ini</p>
                </div>
            </section>

            <section class="bottom-grid">
                <div class="info-box">
                    <div class="list-content">
                        • #1025 - Ahmad (Kirim hari ini)<br>
                        • #1026 - Mey (Deadline besok)<br>
                        • #1027 - Nisa (Deadline 03/03/26)
                    </div>
                    <h3>Deadline Produksi</h3>
                </div>

                <div class="info-box">
                    <div class="list-content">
                        <strong>10.17</strong> Kasir Menambahkan pesanan #105<br><br>
                        <strong>10.50</strong> Pembayaran DP masuk dari pelanggan Ahmad<br><br>
                        <strong>11.07</strong> Kasir menambahkan pesanan baru #106<br><br>
                        <strong>11.26</strong> Pembayaran DP masuk dari pelanggan SMA 2 Jember<br><br>
                        <strong>12.09</strong> Kasir Menambahkan pesanan baru #107
                    </div>
                    <h3>Aktivitas Terakhir</h3>
                </div>

                <div class="info-box">
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                    <div class="chart-title">Statistik Penjualan</div>
                </div>
            </section>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jersey', 'PDH', 'Kaos', 'Kemeja'],
                datasets: [{
                    data: [8, 12, 16, 20],
                    backgroundColor: '#2a1a14',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 20 }
                },
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>
</html>