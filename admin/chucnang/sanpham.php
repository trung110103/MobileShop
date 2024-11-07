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

// Truy vấn dữ liệu từ bảng sản phẩm
$sql = "SELECT id, tensanpham, img, gia, khuyenmai, soluong, ma_danhmuc,view FROM tbl_sanpham";
$result = $conn->query($sql);
?>

<div id='ftb_taikhoan'>
    <h1>Danh sách sản phẩm</h1>
    <div id='searchInput'>
        <input type='text' id='namesearch' placeholder='Tìm kiếm theo tên sản phẩm'>
        <input type='button' value='Tìm kiếm' onclick='searchByName()'>
        <input type='button' value='Thêm sản phẩm' id='btn-add-product'>
</div>

    <?php if ($result->num_rows > 0): ?>
        <table id='tb_taikhoan'>
            <tr>
                <th>ID</th>
                <th>Tên sản phẩm</th>
                <th>Ảnh</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Khuyến mãi</th>
                <th>Mã danh mục</th>
                <th>Lượt xem</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= $row["tensanpham"] ?></td>
                    <td><img src="data:image/jpeg;base64,<?= base64_encode($row["img"]) ?>" alt="<?= $row["tensanpham"] ?>" style="max-width: 100px;"></td>
                    <td><?= number_format($row["gia"], 0, ',', '.') ?></td>
                    <td><?= $row["soluong"] ?></td>
                    <td><?= $row["khuyenmai"] ?>%</td>
                    <td>
                            <?php
                            $conn = new mysqli('localhost', 'root', '', 'product');

                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            $ma_danhmuc = $row["ma_danhmuc"];

                            $sql_danhmuc = "SELECT tendanhmuc FROM tbl_danhmuc WHERE ma_danhmuc = '$ma_danhmuc'";
                            $result_danhmuc = $conn->query($sql_danhmuc);

                            if ($result_danhmuc->num_rows > 0) {
                                $row_danhmuc = $result_danhmuc->fetch_assoc();
                                echo $row_danhmuc["tendanhmuc"];
                            } else {
                                echo "Không có danh mục";
                            }

                            // Đóng kết nối
                            $conn->close();
                            ?>
                        </td>
                        <td><?= $row["view"] ?></td>
                        <td>
                        <button type='button' class='btn-edit-product' data-id='<?= $row['id'] ?>'>Sửa</button>
                        <button type='button' class='btn-delete-product' data-id='<?= $row['id'] ?>'>Xóa</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Không có kết quả</p>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="editModal" class="login-container" style="display: none;">
    <div id="editContent" class="modal-content">
        <!-- Nội dung form từ add_danhmuc.php sẽ được load vào đây -->
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function searchByName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("tb_taikhoan");
        tr = table.getElementsByTagName("tr");

        var found = false; // Biến để kiểm tra xem có kết quả tìm kiếm không

        // Hiển thị lại tất cả các hàng và xóa thông báo nếu có
        for (i = 0; i < tr.length; i++) {
            tr[i].style.display = "";
            if (tr[i].classList.contains("no-result")) {
                table.deleteRow(i); // Xóa dòng thông báo nếu có
            }
        }

        // Lặp qua từng hàng và ẩn các hàng không khớp với tên tìm kiếm
        for (i = 1; i < tr.length; i++) { // Bắt đầu từ 1 để bỏ qua dòng tiêu đề
            td = tr[i].getElementsByTagName("td")[1]; // Cột chứa tên sản phẩm (đếm từ 0)
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) === -1) {
                    tr[i].style.display = "none";
                } else {
                    found = true; // Đánh dấu có kết quả được tìm thấy
                }
            }
        }

        // Nếu không có kết quả, hiển thị thông báo
        if (!found) {
            var noResultRow = table.insertRow(-1); // Thêm hàng mới vào cuối bảng
            noResultRow.classList.add("no-result"); // Đánh dấu hàng là thông báo
            var cell = noResultRow.insertCell(0); // Thêm ô vào hàng mới
            cell.colSpan = 8; // Thiết lập colSpan để trải qua tất cả các cột
            cell.textContent = "Không có kết quả phù hợp.";
        }
    }
    $(document).ready(function() {  
        $('.btn-edit-product').click(function() {
            var categoryId = $(this).data('id'); // Lấy id từ data-id của nút "Sửa"
            
            // Hiển thị modal sửa
            showEditModal_1();

            // Gửi yêu cầu AJAX để lấy thông tin của danh mục cần sửa và hiển thị trong modal
            $.ajax({
                type: "GET",
                url: 'chucnang/edit_sanpham.php',
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
        function showEditModal_1() {
            var editModal = document.getElementById('editModal');
            editModal.style.display = 'flex';
        }

        function hideEditModal_1() {
            var editModal = document.getElementById('editModal');
            editModal.style.display = 'none';
        }
        $('.btn-delete-product').click(function() {
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
            url: 'chucnang/delete_sanpham.php',
            data: { employee_id: employeeId }, // 
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Xóa thành công!',
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reload trang sau khi người dùng nhấn OK
                        location.reload();
                    }
                });
            },
            error: function(xhr, status, error) {
                // Xử lý lỗi nếu có
                console.error(xhr.responseText);
                Swal.fire('Có lỗi xảy ra!', 'Vui lòng thử lại sau.', 'error');
            }
        });
    }
        // Khi nhấn vào nút Thêm sản phẩm
        $('#btn-add-product').click(function() {
            // Hiển thị modal
            showEditModal();

            // Gọi Ajax để load nội dung từ add_sanpham.php vào modal
            $.ajax({
                type: "GET",
                url: 'chucnang/add_sanpham.php',
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
            var editModal = $('#editModal');
            if (!editModal.is(e.target) && editModal.has(e.target).length === 0) {
                hideEditModal();
            }
        });

        function showEditModal() {
            var editModal = document.getElementById('editModal');
            editModal.style.display = 'flex';
        }

        function hideEditModal() {
            var editModal = document.getElementById('editModal');
            editModal.style.display = 'none';
        }
    });
</script>

