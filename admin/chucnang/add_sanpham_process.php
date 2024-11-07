<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
if (!isset($_SESSION['user']) || $_SESSION['role'] !== '1') {
    // Chuyển hướng về trang index.php
    header('Location: ../index.php'); // Sửa đường dẫn cho chính xác
    exit(); // Dừng thực thi các đoạn mã phía sau
}

// Kiểm tra xem đã nhấn submit chưa
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra xem có file được tải lên không
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

            // Tiếp tục xử lý lưu vào cơ sở dữ liệu
            // Kết nối đến MySQL

            $conn = new mysqli('localhost', 'root', '', 'product');

            // Kiểm tra kết nối
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Xử lý lưu vào cơ sở dữ liệu
            $tensanpham = $_POST['tensanpham'];
            $gia = $_POST['gia'];
            $soluong = $_POST['soluong'];
            $khuyenmai = $_POST['khuyenmai'];
            $ma_danhmuc = $_POST['ma_danhmuc']; // Sửa đổi tên trường là ma_danhmuc

            $sql = "INSERT INTO tbl_sanpham (tensanpham, img, gia, soluong, khuyenmai, ma_danhmuc) 
                    VALUES ('$tensanpham', '$imgContent', '$gia', '$soluong', '$khuyenmai', '$ma_danhmuc')";

            if ($conn->query($sql) === TRUE) {
                echo "success";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            $conn->close();
        } else {
            echo "File không hợp lệ. Chỉ chấp nhận các file JPG, JPEG, PNG, GIF.";
        }
    } else {
        echo "Vui lòng chọn một file ảnh.";
    }
}
?>
