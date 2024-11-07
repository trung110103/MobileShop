<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
if (!isset($_SESSION['user']) || $_SESSION['role'] !== '1') {
    // Chuyển hướng về trang index.php
    header('Location: ../index.php'); // Sửa đường dẫn cho chính xác
    exit(); // Dừng thực thi các đoạn mã phía sau
}
// Thông tin kết nối cơ sở dữ liệu
include '../model/user.php';
$conn = new mysqli('localhost', 'root', '', 'product');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn dữ liệu từ bảng tbl_user
$sql = "SELECT id, tendanhmuc, ma_danhmuc, stt_hienthi FROM tbl_danhmuc";
$result = $conn->query($sql);
echo "<div id='ftb_taikhoan'>";
echo "<h1>Danh sách danh mục</h1>";
echo "<div id='searchInput'>
<input type='button' value='Cập nhật STT_Hiển Thị' class='reload_dm'>
<input type='button' value='Thêm danh mục' class='add_dm'>
</div>";

// Hiển thị dữ liệu trong bảng HTML
if ($result->num_rows > 0) {
    echo "<table id='tb_taikhoan'>
            <tr>
                <th>STT</th>
                <th>Tên Danh Mục</th>
                <th>Mã Danh Mục</th>
                <th>STT_Hiển Thị</th>
                <th>Hành động</th>
            </tr>";
    $stt = 1;

    // Lặp qua từng hàng dữ liệu
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . $stt . "</td>
            <td>" . $row["tendanhmuc"] . "</td>
            <td>" . $row["ma_danhmuc"] . "</td>
            <td>" . $row["stt_hienthi"] . "</td>
            <td>
                <button type='button' class='btn-edit' data-id='" . $row['id'] . "'>Sửa</button>
                <button type='button' class='btn-delete' data-id='" . $row['id'] . "'>Xóa</button>
            </td>
        </tr>";
        $stt++;
    }
    echo "</table>";
} else {
    echo "0 kết quả";
}
echo "</div>";

// Đóng kết nối
$conn->close();
?>

<div id="editModal_1" class="login-container" style="display: none;">
    <div id="editContent" class="modal-content">
        <!-- Nội dung form từ add_danhmuc.php sẽ được load vào đây -->
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.reload_dm').click(function() {
            $.ajax({
                type: "POST",
                url: 'chucnang/reload_stt_hienthi.php',
                success: function(response) {
                    if (response.trim() === "success") {
                        // Hiển thị thông báo thành công
                        Swal.fire({
                            icon: 'success',
                            title: 'Đã cập nhật STT_Hiển Thị!',
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
        $('.btn-edit').click(function() {
            var categoryId = $(this).data('id'); // Lấy id từ data-id của nút "Sửa"
            
            // Hiển thị modal sửa
            showEditModal_1();

            // Gửi yêu cầu AJAX để lấy thông tin của danh mục cần sửa và hiển thị trong modal
            $.ajax({
                type: "GET",
                url: 'chucnang/edit_danhmuc.php',
                data: { id: categoryId },
                success: function(response) {
                    $('#editContent').html(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#editContent').html("Đã xảy ra lỗi. Vui lòng thử lại sau.");
                }
            });
        });
        $('.btn-delete').click(function() {
        var employeeId = $(this).data('id'); // Lấy id từ data-id của nút Xóa được nhấn
        
        Swal.fire({
            icon: 'warning',
            title: 'Bạn có chắc chắn muốn xóa danh mục này?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                // Gọi hàm del() với id của nhân viên để xóa
                del(employeeId);
            }
        });
    });

    // Hàm del() để xử lý việc xóa nhân viên
    function del(employeeId) {
        $.ajax({
            type: "POST", // Sửa từ GET thành POST
            url: 'chucnang/delete_dm.php',
            data: { employee_id: employeeId }, // 
            success: function(response) {
                if (response.trim() === "success") {
                    // Hiển thị thông báo thành công
                    Swal.fire({
                        icon: 'success',
                        title: 'Xóa danh mục thành công',
                        showConfirmButton: false,
                        timer: 1500 // Tự động đóng sau 1.5s
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Reload trang sau khi người dùng nhấn OK
                            location.reload();
                        }
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
                // Xử lý lỗi nếu có
                console.error(xhr.responseText);
                Swal.fire('Có lỗi xảy ra!', 'Vui lòng thử lại sau.', 'error');
            }
        });
    }
        // Khi nhấn vào nút Thêm danh mục
        $('.add_dm').click(function() {
            // Hiển thị modal
            showEditModal_1();

            // Lấy giá trị max của stt_hienthi và hiển thị trong modal
            $.ajax({
                url: 'chucnang/get_max_stt_hienthi.php',
                method: 'GET',
                success: function(data) {
                    $('#stt_hienthi').val(parseInt(data.trim()));
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

            // Gửi AJAX request để lấy nội dung của add_danhmuc.php và hiển thị
            $.ajax({
                type: "GET",
                url: 'chucnang/add_danhmuc.php',
                success: function(response) {
                    $('#editContent').html(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#editContent').html("Đã xảy ra lỗi. Vui lòng thử lại sau.");
                }
            });
        });

        // Ẩn modal khi nhấn ra ngoài modal
        $(document).mouseup(function(e) {
            var editModal = $('#editModal_1');
            if (!editModal.is(e.target) && editModal.has(e.target).length === 0) {
                hideEditModal_1();
            }
        });

        function showEditModal_1() {
            var editModal = document.getElementById('editModal_1');
            editModal.style.display = 'flex';
        }

        function hideEditModal_1() {
            var editModal = document.getElementById('editModal_1');
            editModal.style.display = 'none';
        }

    });
</script>
``
