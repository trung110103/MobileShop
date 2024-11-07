<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
$conn = new mysqli('localhost', 'root', '', 'product');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Truy vấn để lấy danh sách danh mục và sắp xếp theo stt_hienthi
$sql = "SELECT ma_danhmuc, tendanhmuc, stt_hienthi FROM tbl_danhmuc ORDER BY stt_hienthi ASC";
$result = $conn->query($sql);
echo '<div class="menu-container-full">';
echo '<div class="menu-container">';

// Kiểm tra và hiển thị danh sách danh mục
if ($result->num_rows > 0) {
    echo '<form id="danhmucForm"><ul id="danhmucMenu">';
    echo '<div id="title">☰ Danh Mục Sản Phẩm</div>';
    while ($row = $result->fetch_assoc()) {
        echo '<li>';
        echo '<input type="radio" id="danhmuc_' . $row["ma_danhmuc"] . '" name="danhmuc" value="' . $row["ma_danhmuc"] . '">';
        echo '<label for="danhmuc_' . $row["ma_danhmuc"] . '">' . $row["tendanhmuc"] . '</label>';
        echo '</li>';
    }
    echo '</ul></form>';
} else {
    echo "Không có danh mục nào.";
}

// Đóng kết nối
$conn->close();
?>

<div id="sanpham-container"></div>
</div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        loadProductList();

        // Lấy ma_danhmuc từ URL nếu có
        const urlParams = new URLSearchParams(window.location.search);
        const ma_danhmuc = urlParams.get('danhmuc');
        
        if (ma_danhmuc) {
            // Đặt trạng thái chọn cho danh mục tương ứng
            $('#danhmuc_' + ma_danhmuc).prop('checked', true);
            loadSanPham(ma_danhmuc);
        } else {
            loadSanPham(null);
        }

        $('input[name="danhmuc"]').change(function() {
            var ma_danhmuc = $(this).val();
            // Thêm tham số danh muc vào URL và load sản phẩm
            window.history.pushState(null, null, '?danhmuc=' + ma_danhmuc);
            loadSanPham(ma_danhmuc);
        });
        
        function loadSanPham(ma_danhmuc) {
            $.ajax({
                url: 'view/sanpham.php',
                type: 'GET',
                data: { ma_danhmuc: ma_danhmuc },
                success: function(response) {
                    $('#sanpham-container').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    });
    function loadProductList() {
            // AJAX request để lấy danh sách 10 sản phẩm có lượt xem nhiều nhất
            $.ajax({
                url: 'view/top10_sanpham.php',
                type: 'GET',
                success: function(response) {
                    $('#product-list').html(response); // Hiển thị top 10 sản phẩm ở đây
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    function updateTotal(price) {
        console.log("Updating total..."); // Kiểm tra xem hàm được gọi khi thay đổi giá trị
        var quantity = parseInt(document.getElementById("quantity").value);
        var totalPrice = price * quantity;
        document.getElementById("total-price").innerHTML = totalPrice.toLocaleString('vi-VN') + 'đ';
    }
</script>

<style>
   /* Your CSS styling here */
</style>
