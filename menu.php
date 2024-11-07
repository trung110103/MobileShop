<div class="menu">
    <ul>
        <li><a href="index?act=trangchu">Trang chủ</a></li>
        <li><a href="#" id="promotionButton">Khuyến Mại</a></li>
    </ul>
</div>
<script>
    function searchPromotionProducts() {
        $.ajax({
            url: 'view/sanpham.php',
            type: 'GET',
            data: { khuyenmai: '>0' },
            success: function(response) {
                const inputs = document.querySelectorAll('input[type="checkbox"], input[type="radio"]');
                inputs.forEach(input => input.checked = false);
                $('#sanpham-container').empty(); // Xóa toàn bộ dữ liệu cũ
                $('#sanpham-container').append(response);     
            },
            error: function(xhr, status, error) {
                console.error('Lỗi:', error);
            }
        });
    }

    $(document).ready(function() {
        const promotionButton = $('#promotionButton'); 

        // Thêm sự kiện click cho nút Khuyến Mại
        promotionButton.on('click', function(event) {
            event.preventDefault(); // Ngăn chặn hành vi mặc định của liên kết
            searchPromotionProducts();
            // Thay đổi URL trên thanh địa chỉ
            window.history.pushState(null, null, '?khuyenmai');
        });

        // Kiểm tra URL khi tải trang
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('khuyenmai')) {
            searchPromotionProducts();
        }
    });
</script>
