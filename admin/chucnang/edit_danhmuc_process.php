<?php
// Kiểm tra session
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

// Xử lý dữ liệu khi nhận được request POST từ form chỉnh sửa danh mục
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy thông tin từ form
    $categoryId = $_POST['categoryId'];
    $tendanhmuc = $_POST['tendanhmuc'];
    $ma_danhmuc = $_POST['ma_danhmuc'];
    $stt_hienthi = $_POST['stt_hienthi'];

    // Kiểm tra xem giá trị mới của stt_hienthi có trùng với bất kỳ giá trị nào khác không
    $checkQuery = "SELECT COUNT(*) as count FROM tbl_danhmuc WHERE stt_hienthi = ? AND id != ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param('si', $stt_hienthi, $categoryId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $row = $checkResult->fetch_assoc();
    $rowCount = $row['count'];

    if ($rowCount > 0) {
        // Nếu có giá trị trùng, thông báo lỗi
        echo "Trùng giá trị với một danh mục khác. Vui lòng chọn giá trị khác cho STT Hiển thị.";
    } else {
        // Nếu không có giá trị trùng, tiến hành cập nhật dữ liệu
        $query = "UPDATE tbl_danhmuc SET tendanhmuc = ?, ma_danhmuc = ?, stt_hienthi = ? WHERE id = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            echo "Error preparing statement: " . $conn->error;
            exit;
        }

        $stmt->bind_param('sssi', $tendanhmuc, $ma_danhmuc, $stt_hienthi, $categoryId);

        if ($stmt->execute()) {
            echo "success"; // Trả về "success" nếu cập nhật thành công
        } else {
            echo "Error: " . $stmt->error; // Trả về thông báo lỗi nếu có lỗi
        }

        $stmt->close();
    }

    $conn->close();
    exit;
}
?>
