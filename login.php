<?php
// =============================================
// FILE: login.php
// Fungsi: Halaman login untuk semua user
// =============================================

session_start();          // Wajib ada di baris paling atas untuk menggunakan session
include "koneksi.php";    // Sambungkan ke database

$pesan = "";  // Variabel untuk menyimpan pesan error

// ---- PROSES LOGIN (ketika tombol Submit diklik) ----
if (isset($_POST['btn_login'])) {

    // Ambil data dari form (trim = hapus spasi di awal/akhir)
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi: pastikan form tidak kosong
    if (empty($username) || empty($password)) {
        $pesan = "Username dan password tidak boleh kosong!";
    } else {
        // Query: cari user di database
        // CATATAN: password di sini dianggap sudah di-hash dengan MD5
        //          Sesuaikan jika kamu tidak pakai hash
        $query  = "SELECT * FROM tb_login WHERE username = '$username' AND password = MD5('$password')";
        $hasil  = mysqli_query($koneksi, $query);
        $jumlah = mysqli_num_rows($hasil);  // Hitung jumlah baris hasil

        if ($jumlah == 1) {
            // Login berhasil — simpan data ke session
            $data = mysqli_fetch_assoc($hasil);  // Ambil 1 baris data sebagai array

            $_SESSION['id_user']  = $data['id_user'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role']     = $data['role'];

            // Arahkan ke halaman sesuai role
            if ($data['role'] == 'owner') {
                header("Location: dashboard_owner.php");
            } else {
                header("Location: dashboard_kasir.php");
            }
            exit(); // Hentikan eksekusi setelah redirect

        } else {
            $pesan = "Username atau password salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        /* ---- RESET & BASE ---- */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1a0f0a 0%, #3d2516 50%, #5d3a24 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* ---- CARD ---- */
        .card {
            background: white;
            padding: 35px 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 380px;
        }

        .card h2 {
            text-align: center;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        .card p.subtitle {
            text-align: center;
            color: #7f8c8d;
            font-size: 13px;
            margin-bottom: 25px;
        }

        /* ---- FORM ---- */
        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #34495e;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #3498db;
            outline: none;
        }

        button {
            width: 100%;
            padding: 11px;
            background: linear-gradient(135deg, #1a0f0a 0%, #3d2516 50%, #5d3a24 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover { background-color: #2980b9; }

        /* ---- PESAN ERROR ---- */
        .pesan-error {
            background-color: #fdecea;
            color: #c0392b;
            border: 1px solid #e74c3c;
            padding: 10px 12px;
            border-radius: 5px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        /* ---- LINK REGISTER ---- */
        .link-register {
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
        }

        .link-register a { color: #3498db; text-decoration: none; }
        .link-register a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="card">
    <h2>Login</h2>
    <p class="subtitle">Siblings.CO</p>

    <!-- Tampilkan pesan error jika ada -->
    <?php if ($pesan != "") : ?>
        <div class="pesan-error">⚠️ <?= $pesan ?></div>
    <?php endif; ?>

    <!-- Form Login -->
    <form method="POST" action="">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Masukkan username">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password">

        <button type="submit" name="btn_login">Login</button>
    </form>

    <div class="link-register">
        Belum punya akun? <a href="register.php">Daftar di sini</a>
    </div>
</div>

</body>
</html>