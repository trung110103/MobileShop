<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Bắt đầu session nếu chưa có session nào
    }
    // Kiểm tra nếu session username tồn tại
    if (!isset($_SESSION['user'])) {
        header('Location: index'); // Sửa đường dẫn cho chính xác
        exit();
    }
    // Tạo kết nối
    $conn = new mysqli('localhost', 'root', '', 'product');

    // Lấy username từ session
    $session_username = $_SESSION['user'];

    // Câu lệnh SQL để lấy dữ liệu từ bảng tbl_user theo username
    $sql = "SELECT username, name, phone, address FROM tbl_user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Hiển thị dữ liệu trong bảng HTML
    if ($result->num_rows > 0) {
        echo "<div><h1>Sửa thông tin cá nhân</h1>";
        echo '<form id="updateForm" action="user/update_infor.php" method="post">';
        echo "<table border='1' class='infor_1'>";
        
        while($row = $result->fetch_assoc()) {
            echo "<tr><th>Username</th><td><input type='text' id='username' name='username' value='" . htmlspecialchars($row['username']) ."' readonly></td></tr>";
            echo "<tr><th>Name</th><td><input type='text' id='name' name='name' value='" . htmlspecialchars($row['name']) . "'></td></tr>";
            echo "<tr><th>Phone</th><td><input type='text' id='phone' name='phone' value='" . htmlspecialchars($row['phone']) . "'></td></tr>";
            echo "<tr><th>Address</th><td><input type='text' id='address' name='address' value='" . htmlspecialchars($row['address']) . "'></td></tr>";
        }
        echo "</table>";
        echo '<div class="button-container">';
        echo '<button type="button" id="btnSubmit" class="bt_login">Lưu thông tin</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    } else {
        echo "Không có dữ liệu.";
    }
    $conn->close();
    ?>

    <div id="passwordModal" class="modal">
        <div class="modal-content_1">
            <h2>Nhập mật khẩu của bạn</h2>
            <form id="passwordForm">
                <input type="password" id="password" name="password" required>
                <div class="error-message" id="errorMessage"></div>
                <button type="submit" id="btnPasswordSubmit">Xác nhận</button>
            </form>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function(){
            var modal = $('#passwordModal');
            var span = $('.close');

            // When the user clicks the button, open the modal 
            $('#btnSubmit').click(function() {
                modal.show();
            });

            // When the user clicks on <span> (x), close the modal
            span.click(function() {
                modal.hide();
            });

            // When the user clicks anywhere outside of the modal, close it
            $(window).click(function(event) {
                if (event.target == modal[0]) {
                    modal.hide();
                }
            });

            // Handle the password form submission
            $('#passwordForm').submit(function(event) {
                event.preventDefault();
                var password = $('#password').val();
                
                $.ajax({
                    url: 'user/check_pass.php',
                    type: 'POST',
                    data: { password: password },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            $.ajax({
                                url: $('#updateForm').attr('action'),
                                type: 'POST',
                                data: $('#updateForm').serialize(),
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Thành công',
                                        text: 'Thông tin của bạn đã được cập nhật!',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            location.reload();
                                        }
                                    });
                                },
                                error: function() {
                                    Swal.fire({
                                        title: 'Lỗi',
                                        text: 'Có lỗi xảy ra, vui lòng thử lại.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        } else {
                            $('#errorMessage').text(result.message);
                        }
                    }
                });
            });
        });
    </script>