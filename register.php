<?php
// =============================================
// FILE: register.php
// Fungsi: Halaman pendaftaran akun baru
// =============================================

session_start();
include "koneksi.php";

$pesan       = "";
$jenis_pesan = ""; // "sukses" atau "error"

// ---- PROSES REGISTER ----
if (isset($_POST['btn_register'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $konfirm  = trim($_POST['konfirmasi']);
    $role     = $_POST['role'];  // 'owner' atau 'kasir'

    // --- Validasi ---
    if (empty($username) || empty($password) || empty($konfirm)) {
        $pesan       = "Semua field wajib diisi!";
        $jenis_pesan = "error";

    } elseif (strlen($username) < 4) {
        $pesan       = "Username minimal 4 karakter!";
        $jenis_pesan = "error";

    } elseif (strlen($password) < 6) {
        $pesan       = "Password minimal 6 karakter!";
        $jenis_pesan = "error";

    } elseif ($password !== $konfirm) {
        $pesan       = "Password dan konfirmasi password tidak cocok!";
        $jenis_pesan = "error";

    } else {
        // Cek apakah username sudah dipakai
        $cek   = mysqli_query($koneksi, "SELECT * FROM tb_login WHERE username = '$username'");
        $exist = mysqli_num_rows($cek);

        if ($exist > 0) {
            $pesan       = "Username '$username' sudah digunakan. Pilih username lain!";
            $jenis_pesan = "error";
        } else {
            // Simpan ke database
            // Password di-hash dengan MD5 (sama dengan saat login)
            $password_hash = MD5($password);

            $query  = "INSERT INTO tb_login (username, password, role) 
                       VALUES ('$username', '$password_hash', '$role')";
            $simpan = mysqli_query($koneksi, $query);

            if ($simpan) {
                $pesan       = "Akun berhasil dibuat! Silakan login.";
                $jenis_pesan = "sukses";
            } else {
                $pesan       = "Gagal menyimpan data: " . mysqli_error($koneksi);
                $jenis_pesan = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .card {
            background: white;
            padding: 35px 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
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

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #34495e;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        input:focus, select:focus {
            border-color: #27ae60;
            outline: none;
        }

        button {
            width: 100%;
            padding: 11px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover { background-color: #229954; }

        .pesan-error {
            background-color: #fdecea;
            color: #c0392b;
            border: 1px solid #e74c3c;
            padding: 10px 12px;
            border-radius: 5px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .pesan-sukses {
            background-color: #eafaf1;
            color: #1e8449;
            border: 1px solid #27ae60;
            padding: 10px 12px;
            border-radius: 5px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .link-login {
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
        }

        .link-login a { color: #27ae60; text-decoration: none; }
        .link-login a:hover { text-decoration: underline; }

        /* Info hint di bawah input */
        .hint {
            font-size: 11px;
            color: #95a5a6;
            margin-top: -12px;
            margin-bottom: 14px;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>📝 Register</h2>
    <p class="subtitle">Buat akun baru</p>

    <!-- Tampilkan pesan -->
    <?php if ($pesan != "") : ?>
        <div class="pesan-<?= $jenis_pesan ?>">
            <?= ($jenis_pesan == 'sukses') ? '✅' : '⚠️' ?> <?= $pesan ?>
        </div>
    <?php endif; ?>

    <!-- Form Register -->
    <form method="POST" action="">

        <label for="username">Username</label>
        <input type="text" id="username" name="username" 
               placeholder="Masukkan username"
               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
        <p class="hint">Minimal 4 karakter</p>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password">
        <p class="hint">Minimal 6 karakter</p>

        <label for="konfirmasi">Konfirmasi Password</label>
        <input type="password" id="konfirmasi" name="konfirmasi" placeholder="Ulangi password">

        <label for="role">Role / Hak Akses</label>
        <select id="role" name="role">
            <option value="mahasiswa">Mahasiswa</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit" name="btn_register">Daftar Sekarang</button>
    </form>

    <div class="link-login">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
</div>

</body>
</html>