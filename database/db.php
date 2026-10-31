<?php
    
date_default_timezone_set('Asia/Jakarta');
    
$conn = mysqli_connect("127.0.0.1","root","","chikumi");
if (!$conn) {
    die("Koneksi gagal");
}

mysqli_query($conn, "SET time_zone = '+07:00'");

$pdo = new PDO(
    "mysql:host=127.0.0.1;dbname=chikumi;charset=utf8mb4",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);
$pdo->exec("SET time_zone = '+07:00'");
?>