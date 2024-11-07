<?php
$servername = "localhost"; // Tên máy chủ MySQL
$username = "root";    // Tên đăng nhập MySQL
$password = "";    // Mật khẩu MySQL
$dbname = "product"; // Tên cơ sở dữ liệu

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
