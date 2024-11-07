<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Bắt đầu session nếu chưa có session nào
    }
    if (!isset($_SESSION['user'])) {
        header('Location: index');
        exit();
    }
?>

<div class="menu-user-container">
    <form id="danhmucForm_user">
        <ul id="danhmucMenu_user">
            <div id="title_user">☰ Chức năng</div>
            <li>
                <input type="radio" id="infor" name="danhmuc_user" value="infor">
                <label for="infor">Thông tin cá nhân</label>
            </li>
            <li>
                <input type="radio" id="giohang" name="danhmuc_user" value="giohang">
                <label for="giohang">Giỏ hàng</label>
            </li>
            <li>
                <input type="radio" id="hotro" name="danhmuc_user" value="hotro">
                <label for="hotro">Hỗ trợ</label>
            </li>
            <li>
                <input type="radio" id="logout" name="danhmuc_user" value="logout">
                <label for="logout">Đăng xuất</label>
            </li>
        </ul>
    </form>

    <div id="user-container">
        <!-- Nội dung sản phẩm sẽ được load ở đây -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    $(document).ready(function() {
        function loadContent(ma_danhmuc) {
            if (ma_danhmuc === "logout") {
                Swal.fire({
                    title: 'Bạn có chắc muốn đăng xuất?',
                    text: "Bạn sẽ được đăng xuất khỏi hệ thống!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Đăng xuất'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "user/logout.php";
                    } else {
                        // Load lại trang nếu người dùng nhấn Hủy
                        location.reload();
                    }
                });
            } else {
                var fileToLoad = "user/" + ma_danhmuc + ".php";
                $("#user-container").load(fileToLoad);
                updateURL(ma_danhmuc);
            }
        }

        function updateURL(ma_danhmuc) {
            if (history.pushState) {
                var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?act=user&madanhmuc=' + ma_danhmuc;
                history.pushState(null, null, newUrl);
            }
        }

        const urlParams = new URLSearchParams(window.location.search);
        const madanhmuc = urlParams.get('madanhmuc') || 'infor';
        
        $('input[name="danhmuc_user"][value="' + madanhmuc + '"]').prop('checked', true);
        loadContent(madanhmuc);

        $('input[name="danhmuc_user"]').change(function() {
            var ma_danhmuc = $(this).val();
            loadContent(ma_danhmuc);
        });
    });
</script>
