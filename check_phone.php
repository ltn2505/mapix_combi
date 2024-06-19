<?php
// Include file cấu hình
include 'config.php';

if (isset($_POST['phone'])) {
    $phone = $_POST['phone'];
    //check độ dài sđt
    if (strlen($phone) < 10 || strlen($phone) > 11) {
        echo "invalid";
        exit;
    }
    // Kiểm tra xem số điện thoại có trùng lặp hay không
    $check_sql = "SELECT * FROM customer WHERE phone = '$phone'";
    $check_result = $conn->query($check_sql);


    //check số điện thoại đã đăng ký
    if ($check_result->num_rows > 0) {
        echo "exists";
    } else {
        echo "not_exists";
    }
}
if (isset($_POST['phone2'])) {
    $phone2 = $_POST['phone2'];
    //check độ dài sđt
    if (strlen($phone2) < 10 || strlen($phone2) > 11) {
        echo "invalid";
        exit;
    }
    // Kiểm tra xem số điện thoại có trùng lặp hay không
    $check_sql = "SELECT * FROM customer WHERE phone = '$phone2'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "exists";
    } else {
        echo "not_exists";
    }
}
// Đóng kết nối
$conn->close();
?>