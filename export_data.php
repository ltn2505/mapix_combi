<?php
// Include PhpSpreadsheet library
require 'vendor/autoload.php';

// Include configuration file for database connection
include 'config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Function to validate date
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

// Retrieve and validate time filters from GET request
$start_time = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_time = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$role = isset($_GET['role']) ? $_GET['role'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$area = isset($_GET['area']) ? $_GET['area'] : '';

$valid_start = validateDate($start_time) ? $start_time . ' 00:00:00' : null;
$valid_end = validateDate($end_time) ? $end_time . ' 23:59:59' : null;


// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set column headers
$sheet->setCellValue('A1', 'Thời Gian');
$sheet->setCellValue('B1', 'Tên');
$sheet->setCellValue('C1', 'Số Điện Thoại');
$sheet->setCellValue('D1', 'Địa Chỉ');
$sheet->setCellValue('E1', 'Người dùng');
$sheet->setCellValue('F1', 'Trạng thái');
$sheet->setCellValue('G1', 'Diện tích');
$sheet->setCellValue('H1', 'Ghi chú');

// Set font for the entire spreadsheet to ensure proper Unicode support
$defaultFont = [
    'font' => [
        'name' => 'Arial',
        'size' => 12
    ]
];
$spreadsheet->getDefaultStyle()->applyFromArray($defaultFont);

// Initialize query with a base statement
$sql = "SELECT * FROM customer WHERE 1=1";
$params = [];

// Build SQL query with time filters
if ($valid_start) {
    $sql .= " AND date_collect >= ?";
    $params[] = $valid_start;
}

if ($valid_end) {
    $sql .= " AND date_collect <= ?";
    $params[] = $valid_end;
}

if (!empty($role)) {
    $sql .= " AND role = ?";
    $params[] = $role;
}

if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
}

if (!empty($area)) {
    $sql .= " AND area = ?";
    $params[] = $area;
}
$sql .= " ORDER BY date_collect DESC";

// Execute query using prepared statements
try {
    // Replace with your actual database credentials
    // $pdo = new PDO('mysql:host=localhost;dbname=ltniovn66689_ltn;charset=utf8', 'ltniovn66689_ltn', 'ltn12345678');
    $pdo = new PDO('mysql:host=localhost;dbname=ltniovn66689_ltn;charset=utf8', 'root', '');

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("set names utf8");

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Start row for data in Excel
    $row = 2;

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Convert data to UTF-8
        $date_collect = mb_convert_encoding($data['date_collect'], 'UTF-8', 'auto');
        $name = mb_convert_encoding($data['name'], 'UTF-8', 'auto');
        $phone = mb_convert_encoding($data['phone'], 'UTF-8', 'auto');
        $address = mb_convert_encoding($data['address'], 'UTF-8', 'auto');
        $role = mb_convert_encoding($data['role'], 'UTF-8', 'auto');
        $status = mb_convert_encoding($data['status'], 'UTF-8', 'auto');
        $area = mb_convert_encoding($data['area'], 'UTF-8', 'auto');
        if ($data['area'] == 1) {
            // Set the new key in the $data array
            $area = '< 5 hecta';
        }elseif ($data['area'] == 2) {
            // Set the new key in the $data array
            $area = '>= 5 hecta';
        }
        else  $area = '';
        $note = mb_convert_encoding($data['note'], 'UTF-8', 'auto');

        // Set cell values
        $sheet->setCellValue('A' . $row, $date_collect);
        $sheet->setCellValue('B' . $row, $name);
        $sheet->setCellValue('C' . $row, $phone);
        $sheet->setCellValue('D' . $row, $address);
        $sheet->setCellValue('E' . $row, $role);
        $sheet->setCellValue('F' . $row, $status);
        $sheet->setCellValue('G' . $row, $area);
        $sheet->setCellValue('H' . $row, $note);
        $row++;
    }

    // Create a Writer object for Excel file
    $writer = new Xlsx($spreadsheet);

    // Set headers for Excel download
    $filename = 'customer_data.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Write data to Excel file and send it to the browser for download
    $writer->save('php://output');

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

// Close database connection
$pdo = null;
?>
