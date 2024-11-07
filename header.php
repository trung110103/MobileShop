<body>
<header>
    <nav>
        <ul>
            <li><a href="index?act=trangchu" id="logo">DƯƠNG ĐÌNH TRUNG</a></li>
            <li class="search-box">
                <input id="searchInput" type="text" placeholder="Nhập tên sản phẩm" required>
                <input id="searchButton" type="button" value="Tìm kiếm">
            </li>
            <?php
                // Kiểm tra xem session 'user' đã tồn tại hay không
                if(isset($_SESSION['user'])) {
                    if ($_SERVER['PHP_SELF'] !== '/user/infor.php') {
                        if($_SESSION['role'] ===  '1'){
                            echo'<a href="index?act=quanly" id="bt_login">Quản Lý</a>';
                        } else {
                            echo'<a href="index?act=user" id="bt_login">Tài khoản</a>';
                        }
                    } 
                } else {
                    echo'<a href="index?act=login" id="bt_login">Đăng nhập</a>';
                }
            ?>
        </ul>
    </nav>  
</header>
<script>
    document.getElementById('searchInput').addEventListener('focus', function() {
        if (this.value === 'Nhập tên sản phẩm') {
            this.value = '';
        }
    });

    document.getElementById('searchInput').addEventListener('blur', function() {
        if (this.value === '') {
            this.value = 'Nhập tên sản phẩm';
        }
    });
    function searchProduct() {
            const searchInput = document.getElementById('searchInput');
            const query = searchInput.value.trim();
            
            if (query === '' || query === 'Nhập tên sản phẩm') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Lỗi',
                    text: 'Vui lòng nhập tên sản phẩm.',
                });
                return;
            }
        
            $.ajax({
                url: 'view/sanpham.php',
                type: 'GET',
                data: { query: query },
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
            const searchInput = $('#searchInput');
            const searchButton = $('#searchButton');

            searchButton.on('click', searchProduct);

            searchInput.on('keyup', function(event) {
                if (event.key === 'Enter') {
                    searchProduct();
                }
            });
        });
</script>
</body>
