<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}

// Kiểm tra nếu session 'user' không tồn tại hoặc giá trị không phải là 'hieuid001'
if (!isset($_SESSION['user']) || $_SESSION['role'] !== '1') {
    // Chuyển hướng về trang index.php
    header('Location: ../index.php'); // Sửa đường dẫn cho chính xác
    exit(); // Dừng thực thi các đoạn mã phía sau
}
// Lấy thông tin danh mục cần sửa từ ID được truyền qua GET
if(isset($_GET['id'])) {
    $categoryId = $_GET['id'];
    // Kết nối đến cơ sở dữ liệu
    $conn = new mysqli('localhost', 'root', '', 'product');

    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Truy vấn dữ liệu từ bảng tbl_danhmuc
    $sql = "SELECT * FROM tbl_danhmuc WHERE id = $categoryId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tendanhmuc = $row['tendanhmuc'];
        $ma_danhmuc = $row['ma_danhmuc'];
        $stt_hienthi = $row['stt_hienthi'];

        // Hiển thị form sửa danh mục
        echo "<div class='modal-header'>
                        <h4 class='modal-title'>Sửa danh mục</h4>
                    </div>";
        echo "<form class='modal-form' id='editForm' action='chucnang/edit_danhmuc_process.php' method='post'>
                <input type='hidden' name='categoryId' value='$categoryId'>
                <div class='form-group'>
                    <label for='tendanhmuc'>Tên danh mục:</label>
                    <input type='text' id='tendanhmuc' name='tendanhmuc' value='$tendanhmuc' required>
                </div>
                <div class='form-group'>
                    <label for='ma_danhmuc'>Mã danh mục:</label>
                    <input type='text' id='ma_danhmuc' name='ma_danhmuc' value='$ma_danhmuc' readonly>
                </div>
                <div class='form-group'>
                    <label for='stt_hienthi'>STT Hiển thị:</label>
                    <input type='text' id='stt_hienthi' name='stt_hienthi' value='$stt_hienthi' required>
                </div>
                <div class='bt'>
                    <input type='submit' value='Lưu'>
                    <input type='button' value='Hủy' onclick='cancelEdit()'>
                </div>
            </form>";
    } else {
        echo "Không tìm thấy danh mục.";
    }

    $conn->close();
}
?>
<script>
    function cancelEdit() {
        $('#editModal_1').hide();
    }
    $(document).ready(function() {
    // Hàm xử lý khi nhấn nút "Lưu"
    $('#editForm').submit(function(e) {
        e.preventDefault(); // Ngăn chặn form submit mặc định

        // Gửi dữ liệu form qua Ajax
        $.ajax({
            type: "POST",
            url: $(this).attr('action'), // Sử dụng action của form
            data: $(this).serialize(), // Serialize form data
            success: function(response) {
                console.log(response); // Log response để kiểm tra
                // Xử lý kết quả trả về từ edit_danhmuc_process.php
                if (response.trim() === "success") {
                    $('#editModal_1').hide();
                    // Hiển thị thông báo thành công
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã lưu thành công!',
                        showConfirmButton: false,
                        timer: 1500 // Tự động đóng sau 1.5s
                    }).then(function() {
                        location.reload(); // Reload trang sau khi lưu thành công
                    });
                } else {
                    // Hiển thị thông báo lỗi nếu có
                    Swal.fire({
                        icon: 'error',
                        title: 'Đã xảy ra lỗi!',
                        text: response,
                    });
                }
            },
            error: function(xhr, status, error) {
                // Hiển thị thông báo lỗi nếu có
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
