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
// donhang.php
$conn = new mysqli('localhost', 'root', '', 'product');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Query to retrieve all orders from tbl_giohang
$sql = "SELECT gh.id, gh.user,gh.diachi, sp.tensanpham, gh.soluong, gh.thoi_gian_khoi_tao, gh.trang_thai_don_hang
        FROM tbl_giohang gh
        LEFT JOIN tbl_sanpham sp ON gh.id_sanpham = sp.id";

// Execute your SQL query here and process the results as needed
$result = $conn->query($sql);

?>
<div id='ftb_taikhoan'>
    <h1>Danh sách đơn hàng</h1>
    <table id='tb_taikhoan'>
            <tr>
                <th>STT</th>
                <th>User</th>
                <th>Địa chỉ</th>
                <th>ID Sản Phẩm</th>
                <th>Số Lượng</th>
                <th>Thời Gian Khởi Tạo</th>
                <th>Trạng Thái Đơn Hàng</th>
            </tr>
    <?php if ($result->num_rows > 0): ?>
            <?php $stt = 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $stt ?></td>
                    <td><?= $row["user"] ?></td>
                    <td><?= $row["diachi"] ?></td>
                    <td><?= $row["tensanpham"] ?></td>
                    <td><?= $row["soluong"] ?></td>
                    <td><?= $row["thoi_gian_khoi_tao"] ?></td>
                    <td>
                        <?php
                        $trang_thai = $row["trang_thai_don_hang"];
                        $button_text = '';
                        $button_class = '';
                        
                        switch ($trang_thai) {
                            case 0:
                                $button_text = 'Chờ xử lí';
                                $button_class = 'wait-processing';
                                break;
                            case 1:
                                $button_text = 'Đã xác nhận';
                                $button_class = 'confirmed';
                                break;
                            case 2:
                                $button_text = 'Lỗi';
                                $button_class = 'error';
                                break;
                            case 3:
                                $button_text = 'Đã hoàn thành';
                                $button_class = 'completed';
                                break;
                            default:
                                $button_text = 'Unknown';
                                $button_class = 'unknown';
                                break;
                        }
                        ?>
                        <div class="dropdown">
                            <button class="<?= $button_class ?>"><?= $button_text ?></button>
                            <div class="dropdown-content">
                                <button onclick="makeChoice(1, <?= $row['id'] ?>)">Đã xác nhận</button>
                                <button onclick="makeChoice(2, <?= $row['id'] ?>)">Lỗi</button>
                                <button onclick="makeChoice(3, <?= $row['id'] ?>)">Đã hoàn thành</button>
                                <button onclick="makeChoice(4, <?= $row['id'] ?>)">Xóa</button>
                            </div>
                        </div>
                    </td>

                </tr>
                <?php $stt++; ?>
            <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan='6'>Không có đơn hàng</td></tr>
    <?php endif; ?>
    </table>
</div>

<script>
    function makeChoice(choice, id_donhang) {
        let status;
        switch (choice) {
            case 1:
                status = 1;
                break;
            case 2:
                status = 2;
                break;
            case 3:
                status = 3;
                break;
            case 4:
                Swal.fire({
                    title: 'Bạn có chắc chắn muốn xóa đơn hàng này không?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Xác nhận'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'chucnang/delete_donhang.php',
                            type: 'POST',
                            data: { id_donhang: id_donhang },
                            success: function(response) {
                                Swal.fire(
                                    'Đã xóa!',
                                    'Đơn hàng của bạn đã được xóa.',
                                    'success'
                                ).then(() => {
                                    location.reload(); // Reload the page to reflect the deletion
                                });
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Xóa không thành công',
                                    'Đã xảy ra lỗi khi xóa đơn hàng.',
                                    'error'
                                );
                            }
                        });
                    }
                });
                return;
            default:
                alert("Không hợp lệ");
                return;
        }

        // For cases 1, 2, 3, send AJAX request to update status
        $.ajax({
            url: 'chucnang/update_donhang.php',
            type: 'POST',
            data: { id_donhang: id_donhang, trang_thai_don_hang: status },
            success: function(response) {
                Swal.fire(
                    'Thành công!',
                    'Trạng thái đơn hàng đã được cập nhật.',
                    'success'
                ).then(() => {
                    location.reload(); // Reload the page to reflect the update
                });
            },
            error: function(xhr, status, error) {
                Swal.fire(
                    'Cập nhật không thành công',
                    'Đã xảy ra lỗi khi cập nhật trạng thái đơn hàng.',
                    'error'
                );
            }
        });

        closeModal(); // Close the dropdown
    }


    function closeModal() {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
</script>
<?php
// Close the database connection
$conn->close();
?>
