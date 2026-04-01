<?php
// =============================================
// FILE: koneksi.php
// Fungsi: Menghubungkan ke database MySQL
// =============================================

$host     = "localhost";
$user     = "root";
$password = "";        // kosong jika belum diset password di XAMPP
$database = "si_siblings"; // sesuaikan nama database kamu

// Membuat koneksi
$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi GAGAL: " . mysqli_connect_error());
}
// Jika berhasil, tidak ada output apapun (normal)
?>