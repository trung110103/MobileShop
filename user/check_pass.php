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

    $session_username = $_SESSION['user'];
    $password = $_POST['password'];

    $sql = "SELECT password FROM tbl_user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Mật khẩu không đúng, kiểm tra lại']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy tài khoản']);
    }
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi']);
}
?>
