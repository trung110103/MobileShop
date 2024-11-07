<?php
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'product');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Truy vấn để lấy giá trị lớn nhất của stt_hienthi
$query = "SELECT MAX(stt_hienthi) as max_stt FROM tbl_danhmuc";
$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    echo $row['max_stt'] + 1; // Tăng giá trị lớn nhất lên 1
} else {
    echo "1"; // Nếu không có bản ghi nào, bắt đầu từ 1
}

$conn->close();
?>
