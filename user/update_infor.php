<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Không tồn tại session']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli('localhost', 'root', '', 'product');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST['username'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "UPDATE tbl_user SET name = ?, phone = ?, address = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $phone, $address, $username);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Thông tin của bạn đã được cập nhật!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra, vui lòng thử lại.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi']);
}
?>
