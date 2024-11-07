<style>
    /* CSS cho bảng chi tiết đơn hàng */
    #details {
        border: 1px solid #00FFFF;
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px; /* Khoảng cách từ đầu bảng đến phần tử trên nó */
    }

    #details th, #details td {
        border: 1px solid #ccc;
        padding: 12px;
        text-align: left;
    }

    #details th {
        background-color: #f2f2f2; /* Màu nền cho các tiêu đề cột */
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
    }

    #details td:first-child {
        width: 25%; /* Chiếm 30% chiều rộng */
    }

    #details table td {
        font-size: 14px;
    }

    /* Định dạng cho các dòng chẵn và lẻ trong bảng */
    #details table tr:nth-child(even) {
        background-color: #f9f9f9; /* Màu nền cho các dòng chẵn */
    }

    /* CSS cho nút xóa */
    .delete-btn {
        margin-top: 10px; /* Khoảng cách từ nút xóa đến bảng */
        padding: 10px 20px;
        background-color: #dc3545; /* Màu đỏ */
        color: white;
        border: none;
        cursor: pointer;
        font-size: 14px;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .delete-btn:hover {
        background-color: #c82333; /* Màu đỏ nhạt khi hover */
    }
</style>

<?php
// Bắt đầu session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tạo kết nối
$conn = new mysqli('localhost', 'root', '', 'product');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy ID đơn hàng từ yêu cầu
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Câu truy vấn SQL để lấy chi tiết đơn hàng
$sql = "SELECT g.id, g.id_sanpham, s.tensanpham, g.diachi, g.soluong, g.thoi_gian_khoi_tao, g.trang_thai_don_hang 
        FROM tbl_giohang AS g
        JOIN tbl_sanpham AS s ON g.id_sanpham = s.id
        WHERE g.id = $order_id";

// Thực hiện câu truy vấn
$result = $conn->query($sql);

// Kiểm tra và hiển thị kết quả
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<table border='1' id='details'>";
    echo "<tr><td><strong>Tên Sản phẩm:</strong></td><td>" . $row["tensanpham"] . "</td></tr>";
    echo "<tr><td><strong>Địa chỉ:</strong></td><td>" . $row["diachi"] . "</td></tr>";
    echo "<tr><td><strong>Số lượng:</strong></td><td>" . $row["soluong"] . "</td></tr>";
    echo "<tr><td><strong>Thời gian khởi tạo:</strong></td><td>" . $row["thoi_gian_khoi_tao"] . "</td></tr>";
    $status = '';
    if ($row["trang_thai_don_hang"] == 0) {
        $status = 'Đang xử lý';
    } elseif ($row["trang_thai_don_hang"] == 1) {
        $status = 'Xác nhận';
    } elseif ($row["trang_thai_don_hang"] == 2) {
        $status = 'Lỗi';
    } elseif ($row["trang_thai_don_hang"] == 3) {
        $status = 'Hoàn thành';
    } else {
        $status = 'Không xác định';
    }
    echo "<tr><td><strong>Trạng thái:</strong></td><td>" . $status . "</td></tr>";
    echo "</table>";
    echo "<button type='button' class='delete-btn' onclick='deleteOrder(".$row['id'].",".$row['trang_thai_don_hang'].")'>Xóa</button>";

} else {
    echo "Không có chi tiết đơn hàng.";
}

// Đóng kết nối
$conn->close();
?>
