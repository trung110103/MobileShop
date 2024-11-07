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
$conn = new mysqli('localhost', 'root', '', 'product');

// Check connection
if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error)));
}

// Check if id_donhang and trang_thai_don_hang are set
if (isset($_POST['id_donhang']) && isset($_POST['trang_thai_don_hang'])) {
    $id_donhang = $_POST['id_donhang'];
    $trang_thai_don_hang = $_POST['trang_thai_don_hang'];

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE tbl_giohang SET trang_thai_don_hang = ? WHERE id = ?");
    $stmt->bind_param("ii", $trang_thai_don_hang, $id_donhang);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("status" => "success", "message" => "Order status updated successfully"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Failed to update order status: " . $stmt->error));
    }

    // Close statement
    $stmt->close();
} else {
    echo json_encode(array("status" => "error", "message" => "Missing parameters"));
}

// Close connection
$conn->close();
?>
