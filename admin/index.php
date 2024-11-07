<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
// Kiểm tra nếu session 'user' không tồn tại hoặc giá trị không phải là 'hieuid001'
if (!isset($_SESSION['user']) || $_SESSION['role'] !== '1') {
    // Chuyển hướng về trang index.php
    header('Location: ../index');
    exit(); // Dừng thực thi các đoạn mã phía sau
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</head>
<body>
    <?php
    include 'header.php'; // Bao gồm header
    include '../model/connectdb.php';
    ?>

    <main>
        <?php
        echo "<div id='admin-container'>";
        if (isset($_GET['act'])) {
            $act = $_GET['act'];
            switch ($act) {
                case 'taikhoan':
                    include 'chucnang/taikhoan.php';
                    break;
                case 'danhmuc':
                    include 'chucnang/danhmuc.php';
                    break;
                case 'sanpham':
                    include 'chucnang/sanpham.php';
                    break;
                case 'logout':
                    include '../user/logout.php';
                    break;
                case 'donhang':
                    include 'chucnang/donhang.php';
                    break;
                case 'doanhthu':
                    include 'chucnang/doanhthu.php';
                    break;
                default:
                    echo "<h3>Trang chủ</h3>";
                    break;
            }
        }
        echo "</div>";
        include '../footer.php';
        ?>
    </main>
</body>
</html>
