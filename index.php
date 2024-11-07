<?php
ob_start(); // Bật bộ đệm đầu ra
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Di Động</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="js/script.js"></script> 
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/infor.css">
    <link rel="stylesheet" href="css/view.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php
    include 'header.php'; // Bao gồm header
    include 'menu.php'; // Bao gồm menu
    include 'model/connectdb.php';

    // Mặc định ẩn menu_danhmuc.php
    $showMenuDanhMuc = true;

    // Kiểm tra act
    if (isset($_GET['act'])) {
        $act = $_GET['act'];
        switch ($act) {
            case 'trangchu':
                header('Location: index');
                exit(); // Kết thúc và chuyển hướng ngay lập tức
            case 'login':
                include 'login.php';
                break;
            case 'dangky':
                include 'register.php';
                break;
            case 'user':
                include 'user/menu_user.php';
                $showMenuDanhMuc = false;
                break;
            case 'edit_infor':
                include 'user/edit_infor.php';
                $showMenuDanhMuc = false;
                break;
            case 'quanly':
                header('Location: admin/index?act=taikhoan');
                exit();
            default:
                echo "<h3>Trang chủ</h3>";
                break;
        }
    } 
    if ($showMenuDanhMuc) {
        include 'view/menu_danhmuc.php';
    }
    ?>

    <script>
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const ma_danhmuc = urlParams.get("ma_danhmuc");
        const page = urlParams.get("page");

        if (ma_danhmuc && page) {
            loadProductsByCategoryAndPage(ma_danhmuc, page);
        }
    });

    // function loadProductsByCategoryAndPage(ma_danhmuc, page) {
    //     $.ajax({
    //         url: 'view/sanpham.php',
    //         type: 'GET',
    //         data: { ma_danhmuc: ma_danhmuc, page: page },
    //         success: function(response) {
    //             $('#sanpham-container').empty(); // Xóa toàn bộ dữ liệu cũ
    //             $('#sanpham-container').append(response);    
    //         },
    //         error: function(xhr, status, error) {
    //             console.error('Lỗi:', error);
    //         }
    //     });
    // }
    </script>

    <?php
    include 'footer.php'; // Bao gồm footer
    ?>
</body>
</html>
<?php
ob_end_flush(); // Đẩy bộ đệm đầu ra và tắt nó
?>
