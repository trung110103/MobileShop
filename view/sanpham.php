<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
?>
<div class="product-grid-container">
    <div class="product-grid" id="productGrid">
        <?php
        $conn = new mysqli('localhost', 'root', '', 'product');

        // Kiểm tra kết nối
        if ($conn->connect_error) {
            die("Kết nối thất bại: " . $conn->connect_error);
        }

        // Lấy giá trị ma_danhmuc và khuyenmai nếu có
        $ma_danhmuc = isset($_GET['ma_danhmuc']) ? $_GET['ma_danhmuc'] : null;
        $search_query = isset($_GET['query']) ? $_GET['query'] : null;
        $khuyenmai = isset($_GET['khuyenmai']) ? $_GET['khuyenmai'] : null;

        // Thiết lập số sản phẩm trên mỗi trang
        $products_per_page = 9; // số sp trên 1 page
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $products_per_page;

        // Câu lệnh SQL để lấy sản phẩm
        if ($khuyenmai) {
            $sql = "SELECT * FROM tbl_sanpham WHERE khuyenmai > 0 LIMIT $offset, $products_per_page";
            $count_sql = "SELECT COUNT(*) AS total FROM tbl_sanpham WHERE khuyenmai > 0";
        } elseif ($search_query) {
            $sql = "SELECT * FROM tbl_sanpham WHERE tensanpham LIKE '%$search_query%' LIMIT $offset, $products_per_page";
            $count_sql = "SELECT COUNT(*) AS total FROM tbl_sanpham WHERE tensanpham LIKE '%$search_query%'";
        } elseif ($ma_danhmuc) {
            $sql = "SELECT * FROM tbl_sanpham WHERE ma_danhmuc = '$ma_danhmuc' LIMIT $offset, $products_per_page";
            $count_sql = "SELECT COUNT(*) AS total FROM tbl_sanpham WHERE ma_danhmuc = '$ma_danhmuc'";
        } else {
            $sql = "SELECT * FROM tbl_sanpham LIMIT $offset, $products_per_page";
            $count_sql = "SELECT COUNT(*) AS total FROM tbl_sanpham";
        }
        $result = $conn->query($sql);
        $count_result = $conn->query($count_sql);
        $total_products = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_products / $products_per_page);

        // Kiểm tra và hiển thị sản phẩm
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="product-item fade-in" onclick="getProductInfo(' . $row['id'] . ')">';
                if (!empty($row["khuyenmai"])) {
                    echo '<div class="promotion">- ' . $row["khuyenmai"] . '%</div>';
                }
                echo '<img src="data:image/jpeg;base64,' . base64_encode($row["img"]) . '" alt="' . $row["tensanpham"] . '" style="max-height: 100px; margin-top:10px">';
                echo '<div class="name_sp">' . $row["tensanpham"] . '</div>';
                echo '<div class="price-stock">';
                if (!empty($row['khuyenmai']) && $row['khuyenmai'] > 0) {
                    $originalPrice = $row['gia'];
                    $discountedPrice = $originalPrice * (1 - $row['khuyenmai'] / 100);
                    echo '<span class="price">' . number_format($discountedPrice, 0, ',', '.') . 'đ</span>';
                } else {
                    echo '<span class="price">' . number_format($row['gia'], 0, ',', '.') . 'đ</span>';
                }
                if ($row["soluong"] > 0) {
                    echo '<div class="stock">Còn hàng</div>';
                } else {
                    echo '<div class="stock_1">Hết hàng</div>';
                }
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "Không có sản phẩm nào.";
        }

        $conn->close();
        ?>
    </div>

    <button class="navigation-btn navigation-btn-left" <?php if ($current_page == 1) echo 'style="display: none;"' ?> onclick="loadPreviousPage(<?php echo $current_page - 1 ?>)"></button>
    
    <!-- Phân trang -->
    <button class="navigation-btn navigation-btn-right" <?php if ($current_page == $total_pages || $total_pages == 0) echo 'style="display: none;"' ?> onclick="loadNextPage(<?php echo $current_page + 1 ?>)"></button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function getProductInfo(productId) {
        // Sử dụng Ajax để gửi id sản phẩm
        $.ajax({
            url: 'view/infor_sanpham.php',
            type: 'GET',
            data: { id: productId },
            success: function(response) {
                // Hiển thị thông tin sản phẩm
                showProductInfo(response);
            },
            error: function(xhr, status, error) {
                console.error('Lỗi:', error);
            }
        });
    }

    function showProductInfo(productInfo) {
        // Tìm phần tử container để hiển thị thông tin sản phẩm
        var productContainer = $('.product-grid-container');

        // Xóa nội dung cũ của container (nếu có)
        productContainer.empty();

        // Thêm thông tin sản phẩm vào container
        productContainer.append(productInfo);
    }
    
    function loadPreviousPage(page) {
        var ma_danhmuc = "<?php echo $ma_danhmuc ?>";
        var search_query = "<?php echo $search_query ?>";
        var khuyenmai = "<?php echo $khuyenmai ?>";
        $('#sanpham-container').fadeOut(300, function() {
            $.ajax({
                url: 'view/sanpham.php',
                type: 'GET',
                data: { 
                    ma_danhmuc: ma_danhmuc,
                    query: search_query,
                    khuyenmai: khuyenmai,
                    page: page 
                },
                success: function(response) {
                    $('#sanpham-container').html(response).fadeIn(300);
                    // Thay đổi URL trên thanh địa chỉ
                    let newUrl = '?page=' + page;
                    if (search_query) {
                        newUrl += '&query=' + search_query;
                    }
                    if (ma_danhmuc) {
                        newUrl += '&ma_danhmuc=' + ma_danhmuc;
                    }
                    if (khuyenmai) {
                        newUrl += '&khuyenmai=true';
                    }
                    window.history.pushState(null, null, newUrl);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });
    }

    function loadNextPage(page) {
        var ma_danhmuc = "<?php echo $ma_danhmuc ?>";
        var search_query = "<?php echo $search_query ?>";
        var khuyenmai = "<?php echo $khuyenmai ?>";
        $('#sanpham-container').fadeOut(300, function() {
            $.ajax({
                url: 'view/sanpham.php',
                type: 'GET',
                data: { 
                    ma_danhmuc: ma_danhmuc,
                    query: search_query,
                    khuyenmai: khuyenmai,
                    page: page 
                },
                success: function(response) {
                    $('#sanpham-container').html(response).fadeIn(300);
                    let newUrl = '?page=' + page;
                    if (search_query) {
                        newUrl += '&query=' + search_query;
                    }
                    if (ma_danhmuc) {
                        newUrl += '&ma_danhmuc=' + ma_danhmuc;
                    }
                    if (khuyenmai) {
                        newUrl += '&khuyenmai=true';
                    }
                    window.history.pushState(null, null, newUrl);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });
    }

    $(document).ready(function() {
        // Hiệu ứng fade-in cho từng sản phẩm
        $('.product-item').each(function(index) {
            $(this).delay(200 * index).fadeIn(500);
        });
    });
</script>

