<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
if (!isset($_SESSION['user']) || $_SESSION['role'] !== '1') {
    // Chuyển hướng về trang index.php
    header('Location: ../index.php'); // Sửa đường dẫn cho chính xác
    exit(); // Dừng thực thi các đoạn mã phía sau
}
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'product');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $userId = $_POST['user_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cập nhật dữ liệu vào cơ sở dữ liệu
    $sql = "UPDATE tbl_user 
            SET name = '$name', address = '$address', phone = '$phone', username = '$username', password = '$password' 
            WHERE id = $userId";

    if ($conn->query($sql) === TRUE) {
        // Đã cập nhật thành công
        echo "Cập nhật thành công!";
    } else {
        // Nếu có lỗi, hiển thị thông báo lỗi
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Đóng kết nối
$conn->close();
?>
