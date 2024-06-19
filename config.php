<?php
// $servername = "localhost";
// $username = "ltniovn66689_ltn";
// $password = "ltn12345678";
// $dbname = "ltniovn66689_ltn";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ltniovn66689_ltn";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Đặt charset của kết nối thành 'utf8'
if (!$conn->set_charset("utf8")) {
    printf("Lỗi khi load charset utf8: %s\n", $conn->error);
    exit();
}
?>