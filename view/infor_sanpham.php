<?php
// Kết nối đến MySQL server
$conn = new mysqli('localhost', 'root', '', 'product');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy id sản phẩm từ request
$productId = isset($_GET['id']) ? $_GET['id'] : null;

// Khởi tạo mảng để lưu thông tin sản phẩm
$productInfo = array();

// Thực hiện truy vấn để lấy thông tin sản phẩm từ CSDL
if ($productId) {
    // Cập nhật số lượt xem (view) của sản phẩm
    $sqlUpdateView = "UPDATE tbl_sanpham SET view = view + 1 WHERE id = $productId";
    $conn->query($sqlUpdateView);

    $sql = "SELECT * FROM tbl_sanpham WHERE id = $productId";
    $result = $conn->query($sql);

    if ($result) {
        // Kiểm tra số lượng hàng trả về
        if ($result->num_rows > 0) {
            // Lấy thông tin của sản phẩm
            $row = $result->fetch_assoc();
            // Lưu thông tin vào mảng
            $productInfo['id'] = $row['id'];
            $productInfo['tensanpham'] = $row['tensanpham'];
            $productInfo['img'] = base64_encode($row['img']); // Chuyển ảnh sang dạng base64
            $productInfo['gia'] = $row['gia'];
            $productInfo['khuyenmai'] = $row['khuyenmai'];
            $productInfo['soluong'] = $row['soluong'];
            $productInfo['view'] = $row['view'];
            $productInfo['thong_tin'] = $row['thong_tin_chi_tiet'];
        }
    } else {
        // Trả về lỗi nếu có vấn đề trong truy vấn
        echo "Lỗi truy vấn: " . $conn->error;
        exit;
    }
} else {
    echo 'Không có id sản phẩm được cung cấp.';
    exit;
}

// Đóng kết nối
$conn->close();

// Hiển thị thông tin sản phẩm dưới dạng HTML
?>
<form id="infor_sp" class="fade-in">
    <div class="product-info">
            <div class="product-image">
            <?php
            if($productInfo['khuyenmai']>0){
                echo'<div class="product-discount">
                <label for="productDiscount">Khuyến mại: '. $productInfo['khuyenmai'] .'%</label>
            </div>';
            }
            ?>
            <img src="data:image/jpeg;base64,<?php echo $productInfo['img']; ?>" alt="Product Image">
        </div>

        <div class="product-details">
            <div class="product-name">
                <label for="productName"><?php echo $productInfo['tensanpham']; ?></label>
            </div>
            <div class="product-view">
                <label for="productView">Lượt xem: <?php echo $productInfo['view']; ?></label>
            </div>
            <div class="product-price">
                <?php
                if (!empty($productInfo['khuyenmai']) && $productInfo['khuyenmai'] > 0) {
                    $originalPrice = $productInfo['gia'];
                    $discountedPrice = $originalPrice * (1 - $productInfo['khuyenmai'] / 100);
                    echo '<span class="original-price">' . number_format($originalPrice, 0, ',', '.') . 'đ</span> ';
                    echo '<span class="discounted-price">' . number_format($discountedPrice, 0, ',', '.') . 'đ</span>';
                } else {
                    echo '<span class="discounted-price">' . number_format($productInfo['gia'], 0, ',', '.') . 'đ</span>';
                }
                ?>
            </div>
            <div class="title_1">
                <label for="title1">Thông tin thêm: </label>
            </div>
            <?php
            if ($productInfo['soluong'] > 0) {
                echo '<div class="product-quantity">
                        <label for="productQuantity">- Trạng thái: Còn hàng</label>
                    </div>';
            } else {
                echo '<div class="product-quantity">
                        <label for="productQuantity">- Trạng thái: Hết hàng</label>
                    </div>';
            }
            ?>
            <div class="product-quantity">
                <label for="productQuantity">- Lưu ý: Khách hàng có thể kiểm tra hàng trước khi nhận hàng, bảo hành trong vòng 1 năm, 1 đổi 1 trong vòng 7 ngày nếu lỗi do nhà sản xuất.</label>
            </div>
            <div class="buy-button">
                <button class="button">Mua hàng</button>
            </div>
        </div>
    </div>
    <div id="more_infor">
    <div id="header"><span>Thông tin chi tiết</span></div>
    <div class="infor">
        <?php echo nl2br(htmlspecialchars($productInfo['thong_tin'], ENT_QUOTES | ENT_HTML5)); ?>
    </div>
</div>

</form>
<div id="myModal" class="modal">
    <div class="modal-content">
        <div id="modal-body">
            <!-- Nội dung từ buy_sanpham.php sẽ được chèn vào đây -->
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    $(document).ready(function() {
    // Hiệu ứng fade-in cho thông tin sản phẩm
    $('#infor_sp').fadeIn(500);
    
    $(document).ready(function(){
    $('.button').click(function(e){
        e.preventDefault(); // Ngăn chặn hành động mặc định của nút

        // Lấy giá trị ID sản phẩm
        var productId = <?php echo json_encode($productInfo['id']); ?>;

        // Gửi AJAX request
        $.ajax({
            url: 'view/buy_sanpham.php',
            type: 'POST',
            data: { id: productId },
            dataType: 'json',
            success: function(response) {
                console.log(response); // In ra phản hồi để kiểm tra
                if (response.status === 'redirect') {
                    // Chuyển hướng đến trang đăng nhập
                    window.location.href = response.url;
                } else if (response.status === 'success') {
                    // Chèn nội dung từ buy_sanpham.php vào modal
                    $('#modal-body').html(response.html);
                    // Hiển thị modal
                    $('#myModal').css('display', 'flex');
                } else {
                    console.error(response.message); // Hiển thị lỗi nếu có
                }
            },
            error: function(xhr, status, error) {
                console.error('Error: ' + status + ' - ' + error); // Hiển thị lỗi chi tiết hơn
                console.error(xhr.responseText); // In ra phản hồi lỗi từ server
            }
        });
    });
});

    // Đóng modal khi nhấn ra ngoài modal
    $(window).click(function(event) {
        if (event.target == $('#myModal')[0]) {
            $('#myModal').css('display', 'none');
        }
    });
});

</script>
