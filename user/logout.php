<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
// Xóa session 'user'
unset($_SESSION['user']);

// Redirect người dùng về trang đăng nhập hoặc trang chính
header("Location: ../index"); // Thay 'login.php' bằng trang bạn muốn chuyển hướng sau khi đăng xuất
exit();
?>
