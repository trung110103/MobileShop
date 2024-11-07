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
$sql = "SELECT id, name, address, phone, username, password FROM tbl_user";
$result = $conn->query($sql);
echo "<div id='ftb_taikhoan'>";
echo "<h1>Danh sách tài khoản</h1>
<div id='searchInput'>
 <input type='text' id='namesearch' placeholder='Tìm kiếm theo tên tài khoản'>
 <input type='button' value='Tìm kiếm' onclick='searchByName()'>
 </div>";


// Hiển thị dữ liệu trong bảng HTML
if ($result->num_rows > 0) {
    echo "<table id='tb_taikhoan'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Username</th>
                <th>Password</th>
                <th>Actions</th>
            </tr>";
    // Lặp qua từng hàng dữ liệu
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
        <td>" . $row["id"] . "</td>
        <td>" . $row["name"] . "</td>
        <td>" . $row["address"] . "</td>
        <td>" . $row["phone"] . "</td>
        <td>" . $row["username"] . "</td>
        <td>" . $row["password"] . "</td>
        <td>
            <button type='button' class='btn-edit-employee' data-id='" . $row['id'] . "'>Sửa</button>";
        if ($row['id'] !== '1') { // Điều kiện kiểm tra id có phải là 1 không
            echo "<button type='button' class='btn-delete-employee' data-id='" . $row['id'] . "'>Xóa</button>";
        }
        echo "</td>
    </tr>";

    }
    echo "</table>";
} else {
    echo "0 kết quả";
}
echo "</div>";

// Đóng kết nối
$conn->close();
?>

<!-- Modal -->
<div id="editModal" class="login-container" style="display: none;">
    <div id="editContent" class="modal-content">
        <!-- Nội dung form từ edit.php sẽ được load vào đây -->
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
        td = tr[i].getElementsByTagName("td")[4]; // Cột chứa username (đếm từ 0)
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
        cell.colSpan = 7; // Thiết lập colSpan để trải qua tất cả các cột
        cell.textContent = "Không có kết quả phù hợp.";
    }
}



$(document).ready(function() {  
    // Hàm xử lý sự kiện khi nút "Xóa" được nhấn
    $('.btn-delete-employee').click(function() {
        var employeeId = $(this).data('id'); // Lấy id từ data-id của nút Xóa được nhấn
        
        Swal.fire({
            icon: 'warning',
            title: 'Bạn có chắc chắn muốn xóa tài khoản này?',
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
            url: 'chucnang/delete.php',
            data: { employee_id: employeeId }, // Sửa từ employee_id thành employee_id
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

    // Xử lý sự kiện khi nhấn vào nút "Sửa"
    $('.btn-edit-employee').click(function() {
        var userId = $(this).data('id');

        // Hiển thị modal
        showEditModal();

        // Gửi AJAX request để lấy nội dung của edit.php và hiển thị
        $.ajax({
            type: "GET",
            url: 'chucnang/edit.php?id=' + userId,
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
            editModal.hide();
        }
    });
    function showEditModal() {
    var editModal = document.getElementById('editModal');
    editModal.style.display = 'flex';
    }

    // Hàm để ẩn modal
    function hideEditModal() {
        var editModal = document.getElementById('editModal');
        editModal.style.display = 'none';
    }
});
</script>

