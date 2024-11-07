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

if ( isset($_POST['employee_id'])) {
    $id = $_POST['employee_id'];

    // Kết nối cơ sở dữ liệu
    $conn = new mysqli('localhost', 'root', '', 'product');
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }
    $checkQuery1 = "SELECT ma_danhmuc FROM tbl_danhmuc WHERE id = ?";
    $checkStmt1 = $conn->prepare($checkQuery1);
    if ($checkStmt1 === false) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $checkStmt1->bind_param("i", $id);
    $checkStmt1->execute();
    $checkStmt1->bind_result($ma_danhmuc);
    $checkStmt1->fetch();
    $checkStmt1->close();

    if ($ma_danhmuc === null) {
        die("Không tìm thấy mã danh mục.");
    }

    // Kiểm tra xem có sản phẩm nào sử dụng ma_danhmuc của dòng muốn xóa không
    $checkQuery = "SELECT * FROM tbl_sanpham WHERE ma_danhmuc = ?";
    $checkStmt = $conn->prepare($checkQuery);
    if ($checkStmt === false) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $checkStmt->bind_param("s", $ma_danhmuc);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        echo "Không thể xóa vì có sản phẩm sử dụng danh mục này.";
    } else {
        // Thực hiện xóa hàng với ID tương ứng
        $sql = "DELETE FROM tbl_danhmuc WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Lỗi chuẩn bị truy vấn: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "success"; // Trả về "success" nếu cập nhật thành công
        } else {
            echo "Error: " . $stmt->error; // Trả về thông báo lỗi nếu có lỗi
        }

        $stmt->close();
    }

    $conn->close();
} else {
    echo "Yêu cầu không hợp lệ.";
}
?>
