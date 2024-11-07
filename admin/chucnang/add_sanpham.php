<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
if (!isset($_SESSION['user']) || $_SESSION['role'] !== '1') {
    // Chuyển hướng về trang index.php
    header('Location: ../index.php'); // Sửa đường dẫn cho chính xác
    exit(); // Dừng thực thi các đoạn mã phía sau
}
?>
<div class="login-container">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Thêm sản phẩm</h4>
        </div>
        <div class="modal-body">
            <form class="modal-form" id="addProductForm" action="chucnang/add_sanpham_process.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="tensanpham">Tên sản phẩm:</label>
                    <input type="text" id="tensanpham" name="tensanpham" required>
                </div>
                <div class="form-group">
                    <label for="img">Ảnh:</label>
                    <input type="file" id="img" name="img" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="gia">Giá:</label>
                    <input type="text" id="gia" name="gia" required>
                </div>
                <div class="form-group">
                    <label for="soluong">Số lượng:</label>
                    <input type="text" id="soluong" name="soluong" required>
                </div>
                <div class="form-group">
                    <label for="khuyenmai">Khuyến mãi (%):</label>
                    <input type="text" id="khuyenmai" name="khuyenmai" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label for="ma_danhmuc">Chọn danh mục:</label>
                    <select id="ma_danhmuc" name="ma_danhmuc" required>
                        <?php
                        $conn = new mysqli('localhost', 'root', '', 'product');

                        // Kiểm tra kết nối
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Truy vấn để lấy danh sách danh mục
                        $sql = "SELECT ma_danhmuc, tendanhmuc FROM tbl_danhmuc";
                        $result = $conn->query($sql);

                        // Kiểm tra và hiển thị danh sách danh mục
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row["ma_danhmuc"] . '">' . $row["tendanhmuc"] . '</option>';
                            }
                        }

                        // Đóng kết nối
                        $conn->close();
                        ?>
                    </select>
                </div>
                <div class="bt">
                    <input type="submit" value="Lưu">
                    <input type="button" value="Hủy" onclick="cancelAdd()">
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('tensanpham').addEventListener('input', function() {
        var input = this.value.toUpperCase(); // Chuyển thành chữ hoa        
        this.value = input; // Cập nhật giá trị của input
    });
    function cancelAdd() {
        $('.login-container').hide();
    }

    $(document).ready(function() {
        // Hàm xử lý khi nhấn nút "Lưu" để thêm sản phẩm
        $('#addProductForm').submit(function(e) {
            e.preventDefault(); // Ngăn chặn form submit mặc định

            // Gửi dữ liệu form qua Ajax
            $.ajax({
                type: "POST",
                url: 'chucnang/add_sanpham_process.php', // URL xử lý thêm sản phẩm
                data: new FormData(this), // Sử dụng FormData để chứa dữ liệu form và file
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    if (response.trim() === "success") {
                        $('.login-container').hide();
                        // Hiển thị thông báo thành công
                        Swal.fire({
                            icon: 'success',
                            title: 'Đã thêm sản phẩm thành công!',
                            showConfirmButton: false,
                            timer: 1500 // Tự động đóng sau 1.5s
                        }).then(function() {
                            // Sau khi hiển thị thông báo, reload trang
                            location.reload();
                        });
                    } else {
                        // Hiển thị thông báo lỗi
                        Swal.fire({
                            icon: 'error',
                            title: 'Đã xảy ra lỗi!',
                            text: response,
                        });
                    }
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
