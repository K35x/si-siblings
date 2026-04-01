<?php
// =============================================
// FILE: dashboard_kasir.php
// Fungsi: Halaman khusus untuk role KASIR
// =============================================

session_start();
include "koneksi.php";

// ---- PROTEKSI HALAMAN ----
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION["role"] ?? "";
if ($role !== "kasir") {
    if ($role === "owner") {
        header("Location: dashboard_owner.php");
    } else {
        header("Location: login.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-cream: #f4ece1;
            --dark-brown: #2a1a14;
            --mid-brown: #6d3e29;
            --sidebar-gradient: linear-gradient(180deg, #6d3e29 0%, #2a1a14 100%);
            --panel-bg: #fdfdfd;
            --radius-panel: 22px;
            --space-4: 16px;
            --white: #ffffff;
        }

        .kasir-dashboard * {
            box-sizing: border-box;
        }

        body.kasir-dashboard {
            margin: 0;
            background-color: var(--bg-cream);
            min-height: 100vh;
            color: var(--dark-brown);
            font-family: 'Poppins', sans-serif;
        }

        .kasir-dashboard .dashboard-shell,
        .kasir-dashboard .main-area {
            min-height: 100vh;
        }

        .kasir-dashboard .sidebar-column {
            background: var(--sidebar-gradient);
            color: var(--white);
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        }

        .kasir-dashboard .sidebar-inner {
            padding: 30px 0;
        }

        .kasir-dashboard .logo {
            padding-bottom: 40px;
            font-size: 28px;
            font-weight: 700;
            font-style: italic;
            text-decoration: underline;
        }

        .kasir-dashboard .sidebar-nav {
            overflow: hidden;
        }

        .kasir-dashboard .sidebar-logout {
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.24);
        }

        .kasir-dashboard .sidebar-logout .logout-form {
            width: 100%;
        }

        .kasir-dashboard .sidebar-logout .logout-button {
            width: 100%;
        }

        .kasir-dashboard .sidebar-nav .menu-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            color: var(--dark-brown);
            background-color: var(--white);
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            white-space: nowrap;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .kasir-dashboard .sidebar-nav .menu-link .menu-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .kasir-dashboard .sidebar-nav .menu-link:hover,
        .kasir-dashboard .sidebar-nav .menu-link:focus {
            color: var(--dark-brown);
            transform: translateY(-1px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.14);
        }

        .kasir-dashboard .main-area {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .kasir-dashboard .topbar {
            background: var(--sidebar-gradient);
            color: var(--white);
        }

        .kasir-dashboard .topbar .nav-top {
            display: flex;
            gap: 30px;
            font-size: 14px;
        }

        .kasir-dashboard .topbar-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .kasir-dashboard .topbar .search-box {
            display: flex;
            align-items: center;
            background: var(--white);
            border-radius: 25px;
            padding: 5px 20px;
            width: min(100%, 350px);
            min-width: 220px;
        }

        .kasir-dashboard .topbar .search-box .form-control {
            width: 100%;
            border: none;
            outline: none;
            box-shadow: none;
            padding: 5px 0;
            color: var(--dark-brown);
        }

        .kasir-dashboard .topbar .search-box .form-control::placeholder {
            color: rgba(42, 26, 20, 0.55);
        }

        .kasir-dashboard .profile-avatar {
            font-size: 30px;
            margin-left: 20px;
            line-height: 1;
        }

        .kasir-dashboard .logout-form {
            margin: 0;
        }

        .kasir-dashboard .logout-button {
            border: 1.5px solid var(--white);
            border-radius: 999px;
            background: transparent;
            color: var(--white);
            font-size: 14px;
            font-weight: 600;
            padding: 9px 18px;
            line-height: 1;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .kasir-dashboard .logout-button:hover,
        .kasir-dashboard .logout-button:focus {
            background: var(--white);
            color: var(--dark-brown);
        }

        .kasir-dashboard .content-body {
            display: flex;
            flex-direction: column;
            gap: 25px;
            flex: 1 1 auto;
            overflow-y: auto;
        }

        .kasir-dashboard .panel.card {
            background: var(--panel-bg);
            border: 1.5px solid #000;
            border-radius: var(--radius-panel);
            box-shadow: none;
        }

        .kasir-dashboard .stat-card .card-body {
            min-height: 96px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 8px;
            text-align: center;
        }

        .kasir-dashboard .stat-card h2 {
            margin: 0;
            font-size: clamp(40px, 2.4vw, 64px);
            line-height: 0.95;
            color: #2a1a14;
            font-weight: 800;
        }

        .kasir-dashboard .stat-card p {
            margin: 6px 0 0;
            font-size: clamp(14px, 0.95vw, 18px);
            font-weight: 500;
            color: #2a1a14;
        }

        .kasir-dashboard .plus-card .card-body {
            min-height: 320px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 22px;
            padding: 18px;
        }

        .kasir-dashboard .plus-symbol {
            width: 146px;
            height: 146px;
            position: relative;
            opacity: 0.76;
        }

        .kasir-dashboard .plus-symbol::before,
        .kasir-dashboard .plus-symbol::after {
            content: "";
            position: absolute;
            background: #7f7f7f;
            border-radius: 40px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .kasir-dashboard .plus-symbol::before {
            width: 182px;
            height: 44px;
        }

        .kasir-dashboard .plus-symbol::after {
            width: 36px;
            height: 146px;
        }

        .kasir-dashboard .plus-card h3,
        .kasir-dashboard .activity-card h3,
        .kasir-dashboard .queue-card h3 {
            margin: 0;
            font-size: clamp(18px, 1.24vw, 24px);
            font-weight: 500;
            color: #2a1a14;
            text-align: center;
        }

        .kasir-dashboard .activity-card .card-body {
            min-height: 320px;
            padding: 14px 14px 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 14px;
        }

        .kasir-dashboard .activity-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: var(--space-4);
        }

        .kasir-dashboard .activity-list li {
            display: flex;
            gap: 10px;
            align-items: start;
            font-size: 14px;
            line-height: 0.95;
            color: #2a1a14;
            font-weight: 700;
        }

        .kasir-dashboard .activity-list li > span:last-child {
            flex: 1 1 auto;
            min-width: 0;
        }

        .kasir-dashboard .activity-list .time {
            flex: 0 0 58px;
            font-size: 16px;
            font-weight: 800;
            color: #2a1a14;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }

        .kasir-dashboard .queue-card .card-body {
            min-height: 320px;
            padding: 14px 14px 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 14px;
        }

        .kasir-dashboard .queue-list .time {
            flex: 0 0 86px;
            font-size: 13px;
            letter-spacing: 0;
        }

        .kasir-dashboard .queue-list li > span:last-child {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .kasir-dashboard .queue-meta {
            font-size: 12px;
            line-height: 1.3;
            font-weight: 600;
            color: rgba(42, 26, 20, 0.76);
        }

        @media (max-width: 1199.98px) {
            .kasir-dashboard .plus-card .card-body,
            .kasir-dashboard .activity-card .card-body,
            .kasir-dashboard .queue-card .card-body {
                min-height: 280px;
            }
        }

        @media (max-width: 991.98px) {
            .kasir-dashboard .sidebar-column {
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .kasir-dashboard .sidebar-inner {
                padding: 20px 0;
            }

            .kasir-dashboard .logo {
                padding-bottom: 20px;
            }

            .kasir-dashboard .sidebar-nav {
                overflow-x: auto;
                overflow-y: hidden;
                scrollbar-width: none;
                -ms-overflow-style: none;
                padding-bottom: 6px;
            }

            .kasir-dashboard .sidebar-nav::-webkit-scrollbar {
                display: none;
            }

            .kasir-dashboard .sidebar-nav .menu-link {
                min-width: max-content;
            }

            .kasir-dashboard .sidebar-logout {
                margin-top: 12px;
            }

            .kasir-dashboard .topbar .topbar-wrap {
                justify-content: center;
            }

            .kasir-dashboard .topbar-actions {
                width: 100%;
                justify-content: center;
            }

            .kasir-dashboard .topbar .search-box {
                width: 100%;
                max-width: 360px;
                min-width: 0;
            }

            .kasir-dashboard .profile-avatar {
                margin-left: 0;
            }

            .kasir-dashboard .content-body {
                gap: var(--space-4);
            }
        }

        @media (max-width: 575.98px) {
            .kasir-dashboard .plus-symbol {
                width: 140px;
                height: 140px;
            }

            .kasir-dashboard .plus-symbol::before {
                width: 134px;
                height: 34px;
            }

            .kasir-dashboard .plus-symbol::after {
                width: 34px;
                height: 134px;
            }
        }
    </style>
</head>
<body class="kasir-dashboard">
    <div class="container-fluid px-0 dashboard-shell">
        <div class="row g-0 min-vh-100">
            <aside class="col-12 col-lg-3 col-xl-2 sidebar-column">
                <div class="sidebar-inner d-flex flex-column h-100">
                    <div class="logo px-4">Siblings.co</div>
                    <nav class="nav sidebar-nav flex-row flex-lg-column gap-2 px-3" aria-label="Menu kasir">
                        <a class="nav-link menu-link" href="#"><svg class="menu-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"></path><path d="M5.25 9.75V21h13.5V9.75"></path><path d="M9.75 21v-6h4.5v6"></path></svg>Beranda</a>
                        <a class="nav-link menu-link" href="#"><svg class="menu-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="3.75" width="12" height="16.5" rx="2"></rect><path d="M9 3.75v-1.5h6v1.5"></path><path d="M9 9.5h6M9 13h6M9 16.5h4.5"></path></svg>Pesanan</a>
                        <a class="nav-link menu-link" href="#"><svg class="menu-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3.75 7.5 12 3l8.25 4.5L12 12 3.75 7.5Z"></path><path d="M3.75 7.5V16.5L12 21l8.25-4.5V7.5"></path><path d="M12 12v9"></path></svg>Stok Barang</a>
                        <a class="nav-link menu-link" href="#"><svg class="menu-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3.75 7.5h14.5a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5.75a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2Z"></path><path d="M3.75 7.5V6.25a2 2 0 0 1 2-2h10.5"></path><path d="M15.75 12.5h4.5v3h-4.5a1.5 1.5 0 0 1 0-3Z"></path></svg>Keuangan</a>
                        <a class="nav-link menu-link" href="#"><svg class="menu-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="7" height="7" rx="1.5"></rect><rect x="13" y="4" width="7" height="7" rx="1.5"></rect><rect x="4" y="13" width="7" height="7" rx="1.5"></rect><rect x="13" y="13" width="7" height="7" rx="1.5"></rect></svg>Katalog produk</a>
                    </nav>
                    <div class="sidebar-logout px-3 pt-3 pb-1">
                        <form class="logout-form" action="logout.php" method="post">
                            <button class="logout-button" type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            </aside>

            <main class="col-12 col-lg-9 col-xl-10 d-flex flex-column main-area">
                <header class="navbar topbar px-3 px-md-4 py-3">
                    <div class="container-fluid px-0">
                        <div class="topbar-wrap d-flex flex-wrap align-items-center justify-content-between gap-3 w-100">
                            <div class="nav-top">
                                <span>Beranda</span>
                                <span>Finance</span>
                            </div>
                            <div class="topbar-actions">
                                <div class="search-box">
                                    <input class="form-control" id="search-dashboard" type="text" placeholder="Search">
                                </div>
                                <div class="profile-avatar" aria-label="Profil kasir <?= htmlspecialchars(
                                    $_SESSION["username"],
                                ) ?>">👤</div>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="content-body container-fluid px-3 px-md-4 py-4">
                    <section class="row g-3 stats-grid" aria-label="Ringkasan status pesanan">
                        <article class="col-6 col-lg-3">
                            <div class="card panel stat-card h-100">
                                <div class="card-body">
                                    <h2>12</h2>
                                    <p>Pesanan Baru</p>
                                </div>
                            </div>
                        </article>
                        <article class="col-6 col-lg-3">
                            <div class="card panel stat-card h-100">
                                <div class="card-body">
                                    <h2>4</h2>
                                    <p>Sedang Diproses</p>
                                </div>
                            </div>
                        </article>
                        <article class="col-6 col-lg-3">
                            <div class="card panel stat-card h-100">
                                <div class="card-body">
                                    <h2>8</h2>
                                    <p>Siap Diambil</p>
                                </div>
                            </div>
                        </article>
                        <article class="col-6 col-lg-3">
                            <div class="card panel stat-card h-100">
                                <div class="card-body">
                                    <h2>15</h2>
                                    <p>Belum Lunas</p>
                                </div>
                            </div>
                        </article>
                    </section>

                    <section class="row g-3 bottom-grid" aria-label="Aktivitas dan antrian pesanan">
                        <article class="col-12 col-md-6 col-xl-3">
                            <div class="card panel plus-card h-100">
                                <div class="card-body">
                                    <div class="plus-symbol" aria-hidden="true"></div>
                                    <h3>Tambah Pesanan</h3>
                                </div>
                            </div>
                        </article>

                        <article class="col-12 col-md-6 col-xl-3">
                            <div class="card panel activity-card h-100">
                                <div class="card-body">
                                    <ul class="activity-list">
                                        <li>
                                            <span class="time">10.17</span>
                                            <span>Kasir Menambahkan pesanan baru #105</span>
                                        </li>
                                        <li>
                                            <span class="time">10.50</span>
                                            <span>Pembayaran DP masuk dari pelanggan Ahmad</span>
                                        </li>
                                        <li>
                                            <span class="time">11.07</span>
                                            <span>Kasir menambahkan pesanan baru #106</span>
                                        </li>
                                        <li>
                                            <span class="time">11.26</span>
                                            <span>Pembayaran DP masuk dari pelanggan SMA 2 Jember</span>
                                        </li>
                                        <li>
                                            <span class="time">12.09</span>
                                            <span>Kasir Menambahkan pesanan baru #107</span>
                                        </li>
                                    </ul>
                                    <h3>Aktivitas Terakhir</h3>
                                </div>
                            </div>
                        </article>

                        <article class="col-12 col-xl-6">
                            <div class="card panel queue-card h-100">
                                <div class="card-body">
                                    <h3>Antrian Invoice dan pesanan</h3>
                                </div>
                            </div>
                        </article>
                    </section>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
