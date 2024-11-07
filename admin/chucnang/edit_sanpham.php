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

// Lấy thông tin sản phẩm cần sửa từ ID được truyền qua GET
if(isset($_GET['id'])) {
    $productId = $_GET['id'];
    // Kết nối đến cơ sở dữ liệu
    $conn = new mysqli('localhost', 'root', '', 'product');

    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Truy vấn dữ liệu từ bảng tbl_sanpham
    $sql = "SELECT * FROM tbl_sanpham WHERE id = $productId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tensanpham = $row['tensanpham'];
        $gia = $row['gia'];
        $soluong = $row['soluong'];
        $khuyenmai = $row['khuyenmai'];
        $ma_danhmuc = $row['ma_danhmuc'];
        $thongtin = $row['thong_tin_chi_tiet'];

        // Truy vấn để lấy danh sách danh mục
        $categorySql = "SELECT ma_danhmuc, tendanhmuc FROM tbl_danhmuc";
        $categoryResult = $conn->query($categorySql);

        // Hiển thị form sửa sản phẩm
        echo "<div class='modal-header'>
                        <h4 class='modal-title'>Sửa sản phẩm</h4>
                    </div>";
        echo "<form class='modal-form' id='editForm' action='chucnang/edit_sanpham_process.php' method='post' enctype='multipart/form-data'>
                <input type='hidden' name='productId' value='$productId'>
                <div class='form-group'>
                    <label for='tensanpham'>Tên sản phẩm:</label>
                    <input type='text' id='tensanpham' name='tensanpham' value='$tensanpham' required>
                </div>
                <div class='form-group'>
                    <label for='img'>Ảnh:</label>
                    <input type='file' id='img' name='img' accept='image/*'>
                </div>
                <div class='form-group'>
                    <label for='gia'>Giá:</label>
                    <input type='text' id='gia' name='gia' value='$gia' required>
                </div>
                <div class='form-group'>
                    <label for='soluong'>Số lượng:</label>
                    <input type='text' id='soluong' name='soluong' value='$soluong' required>
                </div>
                <div class='form-group'>
                    <label for='khuyenmai'>Khuyến mãi (%):</label>
                    <input type='text' id='khuyenmai' name='khuyenmai' value='$khuyenmai' min='0' max='100' required>
                </div>
                <div class='form-group'>
                    <label for='ma_danhmuc'>Chọn danh mục:</label>
                    <select id='ma_danhmuc' name='ma_danhmuc' required>";
        
        if ($categoryResult->num_rows > 0) {
            while ($categoryRow = $categoryResult->fetch_assoc()) {
                $selected = ($categoryRow["ma_danhmuc"] == $ma_danhmuc) ? "selected" : "";
                echo '<option value="' . $categoryRow["ma_danhmuc"] . '" ' . $selected . '>' . $categoryRow["tendanhmuc"] . '</option>';
            }
        }

        echo "      </select>
                </div>
                <div class='form-group'>
                    <label for='gia'>Thông tin:</label>
                </div>
                <div class='form-group'>
                    <textarea id='thongtin' name='thongtin' required rows='4'>".$thongtin."</textarea>
                </div>
                <div class='bt'>
                    <input type='submit' value='Lưu'>
                    <input type='button' value='Hủy' onclick='cancelEdit()'>
                </div>
            </form>";
    } else {
        echo "Không tìm thấy sản phẩm.";
    }

    $conn->close();
}
?>
<script>
    function cancelEdit() {
        $('#editModal').hide();
    }
    $(document).ready(function() {
        // Hàm xử lý khi nhấn nút "Lưu"
        $('#editForm').submit(function(e) {
            e.preventDefault(); // Ngăn chặn form submit mặc định

            // Gửi dữ liệu form qua Ajax
            $.ajax({
                type: "POST",
                url: $(this).attr('action'), // Sử dụng action của form
                data: new FormData(this), // Sử dụng FormData để chứa dữ liệu form và file
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    // Xử lý kết quả trả về từ edit_sanpham_process.php
                    if (response.trim() === "success") {
                        $('#editModal').hide();
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
