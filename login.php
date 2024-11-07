<body>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
include './model/user.php';
if (isset($_SESSION['user'])) {
    header('Location: index');
    exit();
}
// Kiểm tra xem người dùng đã gửi form hay chưa
if (isset($_POST["login"])) {
    // Xử lý thông tin đăng nhập
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    if (strlen($user) >= 8) {
        if (strlen($pass) < 8 || !preg_match('/[!@#$%^&*()\-_=+{};:,<.>1-9a-zA-Z]/', $pass)) {
            $error= "Mật khẩu phải có ít nhất 8 ký tự và chứa ít nhất một ký tự đặc biệt!";
        } else {
            $role = Checkuser($user, $pass);

            if ($role === false) {
                $error= "Tên đăng nhập hoặc mật khẩu không đúng!";
            } else {
                $_SESSION['user'] = $user; // Lưu tên người dùng vào session
                $_SESSION['role'] = $role;
                if ($role == 0) {
                    $countdown = 5;
                } else if ($role == 1) {
                    $countdown = 0;
                }
            }
        }
    }
    else
    {
        $error= "Tài khoản phải có ít nhất 8 ký tự!";
    }
}
if(isset($_POST["reg"])){
    header('Location: register');
    exit();
}
?>
 <div class="login-container" id="loginContainer">
        <div class="login">
            <form action="" method="post">
                <div>
                    <h1>Đăng Nhập</h1>
                </div>
                <div>
                    <input type="text" id="user" name="user" value="Tài khoản" required>
                </div>
                <div>
                    <input type="text" id="pass" name="pass" value="Mật khẩu" required>
                </div>
                <div style="color: #550000;text-align:center; font-style: italic">
                    <?php if (isset($countdown)): ?>
                    <p id="countdownMessage">Bạn sẽ được đăng nhập sau <?php echo $countdown; ?> giây.</p>
                    <script>
                        var countdown = <?php echo $countdown; ?>; // Thời gian đếm ngược ban đầu

                        // Hàm đếm ngược và chuyển hướng
                        function startCountdown() {
                            if (countdown > 0) {
                                document.getElementById('countdownMessage').innerText = 'Bạn sẽ được chuyển tiếp sau ' + countdown + ' giây.';
                                countdown--;
                                setTimeout(startCountdown, 1000); // Đếm ngược mỗi 1 giây
                            } else {
                                // Sau khi đếm ngược xong, chuyển hướng đến trang index
                                window.location.href = 'index'; // Thay đổi 'index.php' bằng trang bạn muốn chuyển hướng đến
                            }
                        }

                        // Bắt đầu đếm ngược khi trình duyệt đã sẵn sàng
                        document.addEventListener('DOMContentLoaded', function() {
                            startCountdown();
                        });
                    </script>
                    <?php endif; ?>
                    <?php if (isset($error)) { echo '<p>' . $error . '</p>'; } ?>
                </div>
                <div>
                    <input type="submit" value="Đăng nhập" id="bt_log" name="login">
                </div>
                <div>
                    <a href="index?act=dangky" id="bt_reg">Đăng Ký</a>
                </div>
            </form>
        </div>
    </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="js/login.js"></script>
<body>
