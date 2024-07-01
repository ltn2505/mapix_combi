<?php
// Kiểm tra đăng nhập
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Include file cấu hình
include 'config.php';

// Lấy danh sách các role và status để tạo lựa chọn trong form lọc
$roles = ['Cá nhân', 'Đại lý'];
$statuses = ['Cần xử lý', 'Đang xử lý', 'Đã xử lý'];

// Truy vấn dữ liệu từ bảng
$sql = "SELECT * FROM customer ORDER BY date_collect DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="/assets/images/logo.png" />

    <style>
        /* Popup container */
        .popup-container {
            display: none;
            position: fixed;
            z-index: 999;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            width: 40%;
            ;
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        /* Popup overlay */
        .popup-overlay {
            display: none;
            position: fixed;
            z-index: 998;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Custom classes for status colors */
        .status-pending {
            color: #d9534f;
        }

        .status-completed {
            color: #1a9335;
        }

        .status-cancelled {
            color: #0080ff;
        }

        @media (min-width: 576px) {
            .form-inline .form-control {
                display: inline-block;
                width: 150px;
                vertical-align: middle;
            }
        }
    </style>
    <!-- Logout -->
    <script type="text/javascript">
        function confirmLogout(event) {
            event.preventDefault(); // Ngăn chặn hành động mặc định của liên kết
            var result = confirm("Bạn có muốn đăng xuất không?");
            if (result) {
                window.location.href = 'logout.php'; // Chuyển hướng đến trang logout.php
            }
        }
    </script>
</head>

<body>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1>Data khách hàng</h1>
        <div>
            <a href="#" id="exportButton" class="btn btn-success">Xuất Data</a>
            <a href="logout.php" class="btn btn-danger" onclick="confirmLogout(event)">Đăng Xuất</a>
        </div>
    </div>
    <form id="filterForm" class="form-inline mt-4">
        <label for="startDate" class="mr-2">Từ ngày:</label>
        <input type="date" id="startDate" name="start_date" class="form-control mr-2">
        <label for="endDate" class="mr-2">Đến ngày:</label>
        <input type="date" id="endDate" name="end_date" class="form-control mr-2">
        <label for="endDate" class="mr-2">Người dùng:</label>
        <select class="form-control mr-2" id="roleFilter" name="role">
            <option value="">Tất cả</option>
            <option value="Cá nhân">Cá nhân</option>
            <option value="Đại lý">Đại lý</option>
        </select>
        <label for="endDate" class="mr-2">Trạng thái:</label>
        <select class="form-control mr-2" id="statusFilter" name="status">
            <option value="">Tất cả</option>
            <option value="Cần xử lý">Cần xử lý</option>
            <option value="Đang xử lý">Đang xử lý</option>
            <option value="Đã xử lý">Đã xử lý</option>
        </select>
        <label for="Area" class="mr-2">Diện tích:</label>
        <select class="form-control mr-2" id="areaFilter" name="area">
            <option value="">Tất cả</option>
            <option value="1">Dưới 5 hecta</option>
            <option value="2">Từ 5 hecta</option>
        </select>
        <label for="City">Nguồn</label>
        <select class="form-control mr-2" id="cityFilter" name="city">
            <option value="">Tất cả</option>
            <option value="Biocrop">Biocrop</option>
            <option value="CT">CT</option>
            <option value="KG">KG</option>
            <option value="HG">HG</option>
            <option value="ST">ST</option>
            <option value="ĐT">ĐT</option>
        </select>
        <!-- <button type="submit" class="btn btn-primary ml-auto" style="margin-right:1%">ALL DATA</button> -->
    </form>
    <table class="table table-striped mt-4">
        <thead class="thead-dark">
            <tr>
                <th scope="col">STT</th>
                <th scope="col">Thời Gian</th>
                <th scope="col">Tên</th>
                <th scope="col">Số Điện Thoại</th>
                <th scope="col">Địa Chỉ</th>
                <th scope="col">Người dùng</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Diện tích</th>
                <th scope="col">Nguồn</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody id="customerData">

        </tbody>
    </table>

    <!-- Edit User Popup -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Chỉnh sửa khách hàng</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="form-group">
                            <label for="editName">Tên</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="editPhone">Số Điện Thoại</label>
                            <input type="text" class="form-control" id="editPhone" name="phone" readonly>
                        </div>
                        <div class="form-group">
                            <label for="editAddress">Địa Chỉ</label>
                            <input type="text" class="form-control" id="editAddress" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="editRole">Người dùng</label>
                            <select class="form-control" id="editRole" name="role">
                                <option value="Cá nhân">Cá nhân</option>
                                <option value="Đại lý">Đại lý</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editStatus">Trạng thái</label>
                            <select class="form-control" id="editStatus" name="status">
                                <option value="Cần xử lý">Cần xử lý</option>
                                <option value="Đang xử lý">Đang xử lý</option>
                                <option value="Đã xử lý">Đã xử lý</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editArea">Diện tích</label>
                            <select class="form-control mr-2" id="editArea" name="area">
                                <option value="">Diện tích canh tác</option>
                                <option value="1">Nhỏ hơn 5 hecta</option>
                                <option value="2">Từ 5 hecta trở lên</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editCity">Nguồn</label>
                            <select class="form-control mr-2" id="editCity" name="city">
                                <option value="">Nguồn dữ liệu</option>
                                <option value="CT">CT</option>
                                <option value="KG">KG</option>
                                <option value="HG">HG</option>
                                <option value="ST">ST</option>
                                <option value="ĐT">ĐT</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editNote">Ghi Chú</label>
                            <textarea class="form-control" id="editNote" name="note" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditPopup(name, phone, address, role, status, area, city, note) {
            $('#editName').val(name);
            $('#editPhone').val(phone);
            $('#editAddress').val(address);
            $('#editRole').val(role);
            $('#editStatus').val(status);
            $('#editArea').val(area);
            $('#editCity').val(city);
            $('#editNote').val(note);
            $('#editModal').modal('show');
        }

        $('#editForm').on('submit', function (event) {
            event.preventDefault();

            $.ajax({
                url: 'update_process.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    alert('Dữ liệu đã được cập nhật thành công');
                    $('#editModal').modal('hide');
                    loadDataWithFilters(); // Load lại dữ liệu sau khi cập nhật thành công với các bộ lọc hiện tại
                },
                error: function () {
                    alert('Lỗi khi cập nhật dữ liệu');
                }
            });
        });

        function loadData(startDate = '', endDate = '', roleFilter = '', statusFilter = '', areaFilter = '', cityFilter = '') {
            $.ajax({
                url: 'load_data.php',
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    role: roleFilter,
                    status: statusFilter,
                    area: areaFilter,
                    city: cityFilter
                },
                success: function (data) {
                    $('#customerData').html(data);
                },
                error: function () {
                    alert('Lỗi khi tải dữ liệu');
                }
            });
        }

        function loadDataWithFilters() {
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            const role = $('#roleFilter').val();
            const status = $('#statusFilter').val();
            const area = $('#areaFilter').val();
            const city = $('#cityFilter').val();
            loadData(startDate, endDate, role, status, area, city);
        }

        // Tự động gửi form lọc khi thay đổi giá trị
        $('#startDate, #endDate, #roleFilter, #statusFilter, #areaFilter, #cityFilter').on('change', function () {
            loadDataWithFilters(); // Gọi hàm loadDataWithFilters khi có thay đổi
        });

        $(document).ready(function () {
            loadDataWithFilters(); // Load dữ liệu ban đầu với các bộ lọc hiện tại
        });

        document.getElementById('exportButton').addEventListener('click', function () {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const role = document.getElementById('roleFilter').value;
            const status = document.getElementById('statusFilter').value;
            const area = document.getElementById('areaFilter').value;
            const city = document.getElementById('cityFilter').value;
            const exportUrl = `export_data.php?start_date=${startDate}&end_date=${endDate}&role=${role}&status=${status}&area=${area}&city=${city}`;
            window.location.href = exportUrl;
        });

    </script>
</body>

</html>

<?php
// Đóng kết nối
$conn->close();
?>