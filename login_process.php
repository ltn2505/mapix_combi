<?php
session_start();

// Thông tin đăng nhập giả định
$valid_username = 'admin';
$valid_password = 'password';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == $valid_username && $password == $valid_password) {
        $_SESSION['loggedin'] = true;
        header('Location: data.php');
    } else {
        $_SESSION['login_error'] = 'Tên người dùng hoặc mật khẩu không đúng';
        header('Location: login.php');
    }
}
?>
