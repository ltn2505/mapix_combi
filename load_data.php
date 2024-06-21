<?php
// Include file cấu hình
include 'config.php';

// Lấy các tham số từ request
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$role = isset($_GET['role']) ? $_GET['role'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$area = isset($_GET['area']) ? $_GET['area'] : '';

// Tăng thêm 1 ngày vào end_date nếu có giá trị
if (!empty($end_date)) {
    $end_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
}

// Truy vấn dữ liệu từ bảng
$sql = "SELECT * FROM customer WHERE 1=1"; // 1=1 để dễ dàng nối các điều kiện WHERE sau này

$params = [];

// Thêm điều kiện lọc theo ngày
if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND date_collect >= ? AND date_collect < ?";
    $params[] = $start_date;
    $params[] = $end_date;
}
// Thêm điều kiện lọc theo user
if (!empty($role)) {
    $sql .= " AND role = ?";
    $params[] = $role;
}
// Thêm điều kiện lọc theo trạng thái
if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
}

if (!empty($area)) {
    $sql .= " AND area = ?";
    $params[] = $area;
}

$sql .= " ORDER BY date_collect DESC";

// Chuẩn bị và thực thi câu truy vấn
$stmt = $conn->prepare($sql);

// Kiểm tra nếu có tham số để bind
if (!empty($params)) {
    // Tạo mảng types dựa trên số lượng tham số
    $types = str_repeat('s', count($params));
    // Bind các tham số với types tương ứng
    $stmt->bind_param($types, ...$params); // Sử dụng bind_param để tránh SQL Injection
}

$stmt->execute();
$result = $stmt->get_result();
$counter = 1; // Initialize counter
// Xử lý kết quả trả về
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $counter . "</td>";
        echo "<td>" . htmlspecialchars($row['date_collect']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
        echo "<td>" . htmlspecialchars($row['address']) . "</td>";
        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
        echo "<td class='";
        if ($row['status'] == 'Cần xử lý') {
            echo "status-pending";
        } elseif ($row['status'] == 'Đang xử lý') {
            echo "status-completed";
        } elseif ($row['status'] == 'Đã xử lý') {
            echo "status-cancelled";
        }
        echo "'>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>";
        switch ($row['area']) {
            case 1:
                echo "<5 hecta";
                break;
            case 2:
                echo ">= 5 hecta";
                break;
            default:
                echo htmlspecialchars($row['area']); // In ra giá trị gốc nếu không phải 1 hoặc 2
                break;
        }
        echo "</td>";
        // echo "<td>" . htmlspecialchars($row['note']) . "</td>";
        echo "<td><button class='btn btn-primary' onclick='openEditPopup(\"" . (isset($row['name']) ? htmlspecialchars($row['name']) : "") . "\", \"" . (isset($row['phone']) ? htmlspecialchars($row['phone']) : "") . "\", \"" . (isset($row['address']) ? htmlspecialchars($row['address']) : "") . "\", \"" . (isset($row['role']) ? htmlspecialchars($row['role']) : "") . "\", \"" . (isset($row['status']) ? htmlspecialchars($row['status']) : "") . "\", \"" . (isset($row['area']) ? htmlspecialchars($row['area']) : "") . "\", \"" . (isset($row['note']) ? htmlspecialchars($row['note']) : "") . "\")'>Edit</button></td>";
        echo "</tr>";
        $counter++; // Increment counter
    }
} else {
    echo "<tr><td colspan='7'>Không có dữ liệu</td></tr>";
}

// Đóng kết nối
$stmt->close();
$conn->close();
?>