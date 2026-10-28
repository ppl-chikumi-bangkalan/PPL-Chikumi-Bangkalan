<?php
    
date_default_timezone_set('Asia/Jakarta');
    
$conn = mysqli_connect("sql303.infinityfree.com","if0_43290517","zTVofP5gsvR","if0_43290517_chikumi");
if (!$conn) {
    die("Koneksi gagal");
}

mysqli_query($conn, "SET time_zone = '+07:00'");
?>