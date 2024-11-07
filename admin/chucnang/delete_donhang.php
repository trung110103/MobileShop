<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
if (!isset($_SESSION['user']) || $_SESSION['role'] !== '1') {
    // Chuyển hướng về trang index.php
    header('Location: ../index.php'); // Sửa đường dẫn cho chính xác
    exit(); // Dừng thực thi các đoạn mã phía sau
}
$conn = new mysqli('localhost', 'root', '', 'product');

// Check connection
if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error)));
}

try {
    // Check if id_donhang is set
    if (isset($_POST['id_donhang'])) {
        $id_donhang = $_POST['id_donhang'];

        // Prepare and bind
        $stmt = $conn->prepare("DELETE FROM tbl_giohang WHERE id = ?");
        $stmt->bind_param("i", $id_donhang);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(array("status" => "success", "message" => "Order deleted successfully"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to delete order: " . $stmt->error));
        }

        // Close statement
        $stmt->close();
    } else {
        echo json_encode(array("status" => "error", "message" => "No order ID provided"));
    }
} catch (Exception $e) {
    echo json_encode(array("status" => "error", "message" => "Exception: " . $e->getMessage()));
}

// Close connection
$conn->close();
?>
