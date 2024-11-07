<style>
.modal {
    display: none; 
    position: fixed; 
    z-index: 1; 
    padding-top: 100px; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgba(0,0,0,0.4); 
    transition: opacity 0.3s ease;
    opacity: 0;
}

.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #888;
    width: 50%;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    animation: modalopen 0.3s;
}

@keyframes modalopen {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

#orderDetails p {
    font-size: 16px;
    line-height: 1.6;
    margin: 10px 0;
}

#orderDetails p strong {
    display: inline-block;
    width: 150px;
}

</style>
<div class="container_1">
    <div class="header">
        <h1>Danh sách đơn hàng</h1>
    </div>
    <?php
    // Bắt đầu session
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Bắt đầu session nếu chưa có session nào
    }

    // Giả sử tên đăng nhập được lưu trong session
    $username = $_SESSION['user'];

    // Tạo kết nối
    $conn = new mysqli('localhost', 'root', '', 'product');

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Câu truy vấn SQL dựa trên tên đăng nhập từ session
    $sql = "SELECT g.id,g.id_sanpham,s.tensanpham,g.diachi, g.soluong, g.thoi_gian_khoi_tao, g.trang_thai_don_hang 
            FROM tbl_giohang AS g
            JOIN tbl_sanpham AS s ON g.id_sanpham = s.id
            WHERE g.user = '$username'"; // Thay đổi tên bảng và cột nếu cần thiết

    // Thực hiện câu truy vấn
    $result = $conn->query($sql);
    echo "<table border='1' class='giohang'>
    <tr>
        <th>Tên Sản phẩm</th>
        <th>SL</th>
        <th>Thời gian khởi tạo</th>
        <th>Trạng thái</th>
    </tr>";
    // Kiểm tra và hiển thị kết quả dưới dạng bảng
    if ($result->num_rows > 0) {
        // Đầu ra của từng hàng
        while($row = $result->fetch_assoc()) {
            $status = '';
            if ($row["trang_thai_don_hang"] == 0) {
                $status = 'Đang xử lý';
                $status_class = 'status-processing'; // Class CSS cho trạng thái Đang xử lý
            } elseif ($row["trang_thai_don_hang"] == 1) {
                $status = 'Xác nhận';
                $status_class = 'status-confirmed'; // Class CSS cho trạng thái Xác nhận
            } elseif ($row["trang_thai_don_hang"] == 2) {
                $status = 'Lỗi';
                $status_class = 'status-error'; // Class CSS cho trạng thái Lỗi
            } elseif ($row["trang_thai_don_hang"] == 3) {
                $status = 'Hoàn thành';
                $status_class = '.status-comform'; // Class CSS cho trạng thái Hoàn thành
            } else {
                $status = 'Không xác định'; // Trường hợp khác nếu cần
                $status_class = ''; // Nếu không cần áp dụng class nào khác
            }
            echo "<tr onclick='showOrderDetails(" . $row['id'] . ")'>
                    <td>" . $row["tensanpham"]. "</td>
                    <td>" . $row["soluong"]. "</td>
                    <td>" . $row["thoi_gian_khoi_tao"]. "</td>
                    <td><span class='".$status_class."'>".$status."</span></td>
                    </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>Không có kết quả nào.</td></tr>";
    }
    echo "</table>";

    // Đóng kết nối
    $conn->close();
    ?>
</div>
<div id="orderDetailModal" class="modal">
    <div class="modal-content">
        <h2>Chi tiết đơn hàng</h2>
        <div id="orderDetails"></div>
    </div>
</div>
<script>
    function deleteOrder(id, trangThaiDonHang) {
        if (trangThaiDonHang === 0) {
            // Nếu trạng thái đơn hàng là 0, gửi yêu cầu xóa đến delete_donhang.php
            $.ajax({
                url: 'user/delete_donhang.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    // Xử lý kết quả trả về từ delete_donhang.php
                    if (response.trim() === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Đã xóa đơn hàng',
                            showConfirmButton: false,
                            timer: 1500 // Tự động đóng sau 1.5 giây
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1500); // Reload sau khi hiển thị thông báo thành công
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Không thể xóa đơn hàng. Vui lòng thử lại sau.',
                            showConfirmButton: false,
                            timer: 3000 // Tự động đóng sau 3 giây
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã xảy ra lỗi trong quá trình xóa đơn hàng.',
                        showConfirmButton: false,
                        timer: 3000 // Tự động đóng sau 3 giây
                    });
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Không thể xóa',
                text: 'Đơn hàng không thể xóa do đơn hàng đã được xác nhận hoặc đã hoàn thành.',
                showConfirmButton: false,
                timer: 3000 // Tự động đóng sau 3 giây
            });
        }
    }
    function showOrderDetails(orderId) {
        $.ajax({
            url: 'user/get_order_details.php',
            type: 'GET',
            data: { id: orderId },
            success: function(response) {
                $('#orderDetails').html(response);
                $('#orderDetailModal').css('display', 'block').css('opacity', '1');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Đã xảy ra lỗi trong quá trình lấy chi tiết đơn hàng.',
                    showConfirmButton: false,
                    timer: 3000 // Tự động đóng sau 3 giây
                });
            }
        });
    }

    // Đóng modal khi nhấn bên ngoài modal
    window.onclick = function(event) {
        if (event.target == document.getElementById('orderDetailModal')) {
            closeModal();
        }
    }
    function closeModal() {
        $('#orderDetailModal').css('opacity', '0');
        setTimeout(function() {
            $('#orderDetailModal').css('display', 'none');
        }, 300); // Phải khớp với thời gian transition
    }
</script>

