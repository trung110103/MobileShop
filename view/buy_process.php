<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hiển thị tất cả các lỗi và cảnh báo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra nếu session 'user' không tồn tại, chuyển hướng đến trang đăng nhập
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'redirect', 'url' => '?act=login']);
    exit; // Kết thúc script
}

// Tạo kết nối
$conn = new mysqli('localhost', 'root', '', 'product');

// Kiểm tra kết nối
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Kết nối thất bại: ' . $conn->connect_error]);
    exit;
}

// Xử lý yêu cầu POST từ form mua hàng
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy thông tin từ POST request
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $address = $_POST['address'];
    $time_created = time(); // Lấy timestamp hiện tại
    $user = $_SESSION['user'];
    $status = '0'; // Trạng thái đơn hàng, ví dụ như 'pending', 'completed', 'cancelled', etc.

    // Truy vấn giá sản phẩm từ cơ sở dữ liệu
    $sql = "SELECT gia, khuyenmai FROM tbl_sanpham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        // Tính toán giá sản phẩm sau khuyến mãi (nếu có)
        $price = $product['gia'];
        if (!empty($product['khuyenmai']) && $product['khuyenmai'] > 0) {
            $price = $price * (1 - $product['khuyenmai'] / 100);
        }

        // Tính tổng giá
        $total_price = $price * $quantity;

        // Lưu thông tin vào bảng đơn hàng trong cơ sở dữ liệu
        $stmt = $conn->prepare("INSERT INTO tbl_giohang (user, id_sanpham, diachi, soluong, thoi_gian_khoi_tao, trang_thai_don_hang, gia_tien) VALUES (?, ?, ?, ?, FROM_UNIXTIME(?), ?, ?)");
        $stmt->bind_param("siisiss", $user, $product_id, $address, $quantity, $time_created, $status, $total_price);

        if ($stmt->execute()) {
            // Thực hiện thành công
            echo json_encode(['status' => 'success', 'message' => 'Đơn hàng đã được thêm thành công.']);
        } else {
            // Lỗi khi thêm đơn hàng vào cơ sở dữ liệu
            echo json_encode(['status' => 'error', 'message' => 'Đã xảy ra lỗi khi thêm đơn hàng: ' . $conn->error]);
        }

        $stmt->close();
    } else {
        // Không tìm thấy sản phẩm
        echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại.']);
    }
} else {
    // Không phải yêu cầu POST, xử lý lỗi hoặc trả về thông báo
    echo json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.']);
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>
