<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
if (!isset($_SESSION['user']) || $_SESSION['role'] !== '1') {
    // Chuyển hướng về trang index.php
    header('Location: ../index.php'); // Sửa đường dẫn cho chính xác
    exit(); // Dừng thực thi các đoạn mã phía sau
}

// Kiểm tra kết nối
$conn = new mysqli('localhost', 'root', '', 'product');
// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kiểm tra xem ID có được truyền qua URL không
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Truy vấn thông tin của người dùng từ cơ sở dữ liệu
    $query = "SELECT username,name,address,phone,password FROM tbl_user WHERE id = $userId";
    $result = $conn->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc(); // Lấy thông tin người dùng
?>

            <div class="login-container">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Sửa thông tin người dùng</h4>
                    </div>
                    <div class="modal-body">
                        <form class="modal-form" id="editForm" action="chucnang/edit_process.php" method="post">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" id="address" name="address" value="<?php echo $user['address']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone:</label>
                                <input type="text" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="text" id="password" name="password" value="<?php echo $user['password']; ?>">
                            </div>
                            <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                            <div class="bt">
                            <input type="submit" value="Lưu">
                            <input type="button" value="Hủy" onclick="cancelEdit()">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                function cancelEdit() {
                    $('#editModal').hide();
                }
            </script>

<?php
        } else {
            echo "User not found.";
        }
    } else {
        echo "Error: " . $conn->error; // Hiển thị lỗi nếu có
    }
} else {
    echo "ID not provided.";
}
?>
<script>
    $(document).ready(function() {
        // Hàm xử lý khi nhấn nút "Lưu"
        $('#editForm').submit(function(e) {
            e.preventDefault(); // Ngăn chặn form submit mặc định

            // Gửi dữ liệu form qua Ajax
            $.ajax({
                type: "POST",
                url: $(this).attr('action'),
                data: $(this).serialize(), // Serialize form data
                success: function(response) {
                    $('#editModal').hide();
                    // Hiển thị thông báo thành công
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã lưu thành công!',
                        showConfirmButton: false,
                        timer: 1500 // Tự động đóng sau 1.5s
                    }).then(function() {
                        // Sau khi hiển thị thông báo, reload form
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    // Hiển thị thông báo lỗi
                    Swal.fire({
                        icon: 'error',
                        title: 'Đã xảy ra lỗi!',
                        text: 'Vui lòng thử lại sau.',
                    });
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
