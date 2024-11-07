<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra nếu session 'user' không tồn tại, chuyển hướng đến trang đăng nhập
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'redirect', 'url' => '?act=login']);
    exit; // Kết thúc script
}

// Tạo kết nối
$conn = new mysqli('localhost', 'root', '', 'product');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra xem ID sản phẩm được truyền vào từ AJAX request
if (isset($_POST['id'])) {
    $productId = $_POST['id'];

    // Truy vấn SQL để lấy thông tin sản phẩm theo ID
    $sql = "SELECT * FROM tbl_sanpham WHERE id = $productId";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Hiển thị thông tin sản phẩm ra form
        $productInfo = $result->fetch_assoc();
        if (!empty($productInfo['khuyenmai']) && $productInfo['khuyenmai'] > 0) {
            $originalPrice = $productInfo['gia'];
            $discountedPrice = $originalPrice * (1 - $productInfo['khuyenmai'] / 100);
            $priceHTML = '<span class="discounted-price">' . number_format($discountedPrice, 0, ',', '.') . 'đ</span>';
            $price = $discountedPrice; // Giá sau khi đã áp dụng khuyến mãi
        } else {
            $priceHTML = '<span class="discounted-price">' . number_format($productInfo['gia'], 0, ',', '.') . 'đ</span>';
            $price = $productInfo['gia']; // Giá gốc
        }
    
        // Kiểm tra nếu quantity tồn tại trong $_POST thì sử dụng, ngược lại gán mặc định là 1
        $quantity = isset($_POST["quantity"]) ? $_POST["quantity"] : 1;
    
        // Tính tổng giá
        $totalPrice = $price * $quantity;
        $productInfo['img'] = base64_encode($productInfo['img']);

        echo json_encode([
            'status' => 'success',
            'html' => '
                <div id="form-buy">
                    <label style="background-color: #36414b; color: #ffffff; padding: 10px; font-weight: bold; text-transform: uppercase; display: block; margin-bottom: 10px;">Mua ngay</label>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; min-height:300px">
                        <div style="flex: 1; margin-right: 10px; padding-left:20px">
                            <!-- Form mua hàng -->
                            <form id="buy-form" method="POST" action="buy.php">
                                <h3>' . $productInfo['tensanpham'] . '</h3>
                                <p>Giá: ' . $priceHTML . '</p>
                                <input type="hidden" name="product_id" value="' . $productId . '">
                                <input type="hidden" id="payment-method" name="payment_method">
                                <div style="margin-bottom: 10px;">
                                    <label for="quantity" style="margin-right: 5px;">Số lượng:</label>
                                    <input type="number" id="quantity" name="quantity" min="1" max="10" value="' . $quantity . '" class="quantity-input" oninput="updateTotal(' . $price . '); validateQuantity()">
                                </div>
                                <p>Đơn giá: <span id="total-price">' . number_format($totalPrice, 0, ',', '.') . 'đ</span></p>
                                <div class="input-container">
                                    <input type="text" id="address" name="address" placeholder="Nhập địa chỉ">
                                    <button type="button" onclick="confirmPurchase()">Xác nhận mua</button>
                                </div>
                            </form>
                        </div>
                        <div style="flex: 1; text-align: center;">
                            <img src="data:image/jpeg;base64,' . $productInfo['img'] . '" alt="Product Image" style="max-width: 100%; height: 100%">
                        </div>
                    </div>
                </div>
                <script>
                    function validateQuantity() {
                        var quantityInput = document.getElementById("quantity");
                        if (quantityInput.value > 10) {
                            quantityInput.value = 10;
                        }
                    }

                    function updateTotal(price) {
                        var quantity = document.getElementById("quantity").value;
                        var totalPrice = price * quantity;
                        document.getElementById("total-price").innerText = new Intl.NumberFormat("vi-VN").format(totalPrice) + "đ";
                    }

                    function confirmPurchase() {
    Swal.fire({
        title: "Xác nhận mua hàng",
        text: "Bạn có chắc chắn muốn mua sản phẩm này?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Xác nhận",
        cancelButtonText: "Hủy"
    }).then((result) => {
        if (result.isConfirmed) {
            var formData = new FormData(document.getElementById("buy-form"));
            var address = document.getElementById("address").value; // Lấy giá trị địa chỉ
            formData.append("address", address); // Thêm địa chỉ vào FormData
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "view/buy_process.php", true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log(xhr.responseText); // Thêm dòng này để kiểm tra phản hồi từ máy chủ
                    try {
                        var response = JSON.parse(xhr.responseText);
                        document.getElementById("myModal").style.display = "none";
                        if (response.status === "success") {
                            Swal.fire("Thành công!", response.message, "success");
                        } else {
                            Swal.fire("Thất bại!", response.message, "error");
                        }
                    } catch (e) {
                        Swal.fire("Lỗi!", "Đã xảy ra lỗi khi phân tích phản hồi từ máy chủ.", "error");
                    }
                } else {
                    Swal.fire("Lỗi!", "Đã xảy ra lỗi khi thực hiện yêu cầu.", "error");
                }
            };
            xhr.onerror = function() {
                Swal.fire("Lỗi!", "Đã xảy ra lỗi khi thực hiện yêu cầu.", "error");
            };
            xhr.send(formData);
        }
    });
}


                </script>
            ',
            'price' => $price // Thêm giá trị giá vào response JSON
        ]);
        
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy sản phẩm.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không có ID sản phẩm được cung cấp.']);
}

// Đóng kết nối
$conn->close();
?>