<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="mt-4">Đăng Nhập</h1>
    <?php
    if (isset($_SESSION['login_error'])) {
        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['login_error'] . '</div>';
        unset($_SESSION['login_error']);
    }
    ?>
    <form action="login_process.php" method="POST">
        <div class="form-group">
            <label for="username">Tên Người Dùng</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Mật Khẩu</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Đăng Nhập</button>
    </form>
</div>
</body>
</html>
