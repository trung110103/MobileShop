<?php
// Kết nối đến cơ sở dữ liệu
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
if (!isset($_SESSION['user']) || $_SESSION['role'] !== '1') {
    // Chuyển hướng về trang index.php
    header('Location: ../index.php'); // Sửa đường dẫn cho chính xác
    exit(); // Dừng thực thi các đoạn mã phía sau
}

// Hàm sinh mã danh mục ngẫu nhiên
function generateMaDanhMuc() {
    return sprintf("%04d", mt_rand(0, 9999));
}

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'product');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $tendanhmuc = $_POST['tendanhmuc'];
    $stt_hienthi = $_POST['stt_hienthi'];

    // Tạo mã danh mục mới và kiểm tra xem đã tồn tại chưa
    do {
        $ma_danhmuc = generateMaDanhMuc();
        $check_query = "SELECT COUNT(*) as count FROM tbl_danhmuc WHERE ma_danhmuc = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param('s', $ma_danhmuc);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
    } while ($row['count'] > 0);

    // Truy vấn để thêm dữ liệu vào bảng tbl_danhmuc
    $query = "INSERT INTO tbl_danhmuc (tendanhmuc, ma_danhmuc, stt_hienthi) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }

    $stmt->bind_param('sss', $tendanhmuc, $ma_danhmuc, $stt_hienthi);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>
