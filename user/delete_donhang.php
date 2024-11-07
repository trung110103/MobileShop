<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    // Kết nối tới cơ sở dữ liệu (ví dụ: MySQL)
    // Thực hiện các xử lý để xóa đơn hàng với id được gửi từ client-side
    $id_donhang = $_POST['id'];

    // Tạo kết nối
    $conn = new mysqli('localhost', 'root', '', 'product');

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
    }

    // Chuẩn bị câu truy vấn xóa dữ liệu
    $sql = "DELETE FROM tbl_giohang WHERE id= ?";

    // Sử dụng prepared statement để tránh SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_donhang);

    // Thực thi câu truy vấn
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    // Đóng kết nối
    $stmt->close();
    $conn->close();
} else {
    // Nếu không phải là phương thức POST hoặc không có id_donhang được gửi
    echo "Yêu cầu không hợp lệ";
}
?>
