<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}

// Kiểm tra nếu session 'user' không tồn tại hoặc giá trị không phải là 'hieuid001'
if (!isset($_SESSION['user']) || $_SESSION['role'] !== '1') {
    // Chuyển hướng về trang index.php
    header('Location: ../index.php'); // Sửa đường dẫn cho chính xác
    exit(); // Dừng thực thi các đoạn mã phía sau
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employee_id']) && $_SESSION['role'] === '1') { // Sửa từ $_GET thành $_POST
    $id = $_POST['employee_id']; // Sửa từ $_GET thành $_POST

    // Kết nối cơ sở dữ liệu
    $conn = new mysqli('localhost', 'root', '', 'product');
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Thực hiện xóa hàng với ID tương ứng
    $sql = "DELETE FROM tbl_user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Hàng đã được xóa thành công.";
    } else {
        echo "Lỗi khi xóa hàng: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Yêu cầu không hợp lệ.";
}
?>
