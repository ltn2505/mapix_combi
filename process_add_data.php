<?php
// Include file cấu hình
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ biểu mẫu
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $area = $_POST['area'];
    $city = $_POST['city'];


    // Kiểm tra xem số điện thoại có trùng lặp hay không
    $check_sql = "SELECT * FROM customer WHERE phone = '$phone'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "Số điện thoại đã tồn tại";
    } else {
        // Câu lệnh SQL thêm dữ liệu
        $sql = "INSERT INTO customer (name, phone, address, role, status, area, city) VALUES ('$name', '$phone', '$address', '$role', '$status', '$area', '$city')";
        if ($conn->query($sql) === TRUE) {
            echo "1";
        } else {
            echo "Lỗi: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Đóng kết nối
$conn->close();
?>
