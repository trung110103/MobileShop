<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra session 'user'
if (!isset($_SESSION['user']) || $_SESSION['role'] !== '1') {
    // Nếu không có session hoặc không đăng nhập với user 'hieuid001', chuyển hướng về trang index.php
    header('Location: ../index.php');
    exit(); // Dừng thực thi các đoạn mã phía sau
}
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'product');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn để cập nhật lại giá trị stt_hienthi
$query = "SELECT id FROM tbl_danhmuc ORDER BY stt_hienthi";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $stt = 1;
    while ($row = $result->fetch_assoc()) {
        $categoryId = $row['id'];
        $updateQuery = "UPDATE tbl_danhmuc SET stt_hienthi = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param('ii', $stt, $categoryId);
        $updateStmt->execute();
        $stt++;
    }
    echo "success"; // Trả về success nếu cập nhật thành công
} else {
    echo "Không có danh mục để cập nhật.";
}

$conn->close();
?>
