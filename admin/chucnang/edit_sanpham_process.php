<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}

// Kiểm tra nếu session 'user' không tồn tại hoặc giá trị không phải là 'hieuid001'
if (!isset($_SESSION['user']) || $_SESSION['role'] !== '1') {
    echo "Access denied.";
    exit();
}

// Kiểm tra xem có phương thức POST được gửi đến hay không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kết nối đến cơ sở dữ liệu
    $conn = new mysqli('localhost', 'root', '', 'product');

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Lấy dữ liệu từ form
    $productId = $_POST['productId'];
    $tensanpham = $_POST['tensanpham'];
    $gia = $_POST['gia'];
    $soluong = $_POST['soluong'];
    $khuyenmai = $_POST['khuyenmai'];
    $ma_danhmuc = $_POST['ma_danhmuc'];
    $thongtin = $_POST['thongtin'];

    // Kiểm tra xem có ảnh mới được tải lên hay không
    if(isset($_FILES["img"]) && $_FILES["img"]["error"] == 0) {
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        $temp = explode(".", $_FILES["img"]["name"]);
        $extension = end($temp);

        // Kiểm tra phần mở rộng của file
        if(in_array(strtolower($extension), $allowed_types) &&
            (
                ($_FILES["img"]["type"] == "image/gif") ||
                ($_FILES["img"]["type"] == "image/jpeg") ||
                ($_FILES["img"]["type"] == "image/jpg") ||
                ($_FILES["img"]["type"] == "image/pjpeg") ||
                ($_FILES["img"]["type"] == "image/x-png") ||
                ($_FILES["img"]["type"] == "image/png")
            )
        ) {
            $imgContent = addslashes(file_get_contents($_FILES["img"]["tmp_name"])); // Chuyển ảnh thành dạng nhị phân

            // Cập nhật sản phẩm có ảnh
            $sql = "UPDATE tbl_sanpham SET tensanpham='$tensanpham', img='$imgContent', gia='$gia', soluong='$soluong', khuyenmai='$khuyenmai', ma_danhmuc='$ma_danhmuc', thong_tin_chi_tiet='$thongtin' WHERE id='$productId'";
        } else {
            echo "File không hợp lệ. Chỉ chấp nhận các file JPG, JPEG, PNG, GIF.";
            exit();
        }
    } else {
        // Cập nhật sản phẩm không có ảnh
        $sql = "UPDATE tbl_sanpham SET tensanpham='$tensanpham', gia='$gia', soluong='$soluong', khuyenmai='$khuyenmai', ma_danhmuc='$ma_danhmuc', thong_tin_chi_tiet='$thongtin' WHERE id='$productId'";
    }

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>
