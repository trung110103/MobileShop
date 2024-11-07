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
            <h4 class="modal-title">Thêm danh mục</h4>
        </div>
        <div class="modal-body">
            <form class="modal-form" id="editForm" action="" method="post">
                <div class="form-group">
                    <label for="tendanhmuc">Tên danh mục:</label>
                    <input type="text" id="tendanhmuc" name="tendanhmuc" required>
                </div>
                <div class="form-group">
                    <label for="ma_danhmuc">Mã danh mục:</label>
                    <input type="text" id="ma_danhmuc" name="ma_danhmuc" readonly>
                </div>
                <div class="form-group">
                    <label for="stt_hienthi">STT Hiển thị:</label>
                    <input type="text" id="stt_hienthi" name="stt_hienthi" readonly>
                </div>
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
        $('.login-container').hide();
    }

    $(document).ready(function() {
        // Hiển thị form thêm danh mục
        $('.add_dm').click(function() {
            $('.login-container').show();
        });

        // Hàm xử lý khi nhấn nút "Lưu"
        $('#editForm').submit(function(e) {
            e.preventDefault(); // Ngăn chặn form submit mặc định

            // Gửi dữ liệu form qua Ajax
            $.ajax({
                type: "POST",
                url: 'chucnang/add_danhmuc_process.php', // URL đã được cập nhật
                data: $(this).serialize(), // Serialize form data
                success: function(response) {
                    if (response.trim() === "success") {
                        $('.login-container').hide();
                        // Hiển thị thông báo thành công
                        Swal.fire({
                            icon: 'success',
                            title: 'Đã lưu thành công!',
                            showConfirmButton: false,
                            timer: 1500 // Tự động đóng sau 1.5s
                        }).then(function() {
                            // Sau khi hiển thị thông báo, reload trang
                            location.reload();
                        });
                    } else {
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
