<body>
<link rel="stylesheet" href="./css/style.css">
<?php
include './model/user.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
if (isset($_SESSION['user'])) {
    header('Location: index');
    exit();
}
// Kiểm tra xem người dùng đã gửi form hay chưa
if (isset($_POST["reg"])) {
   // Lấy dữ liệu từ form
   $phone = $_POST['phone'];
   $username = $_POST['user'];
   $password = $_POST['pass'];
   if (strlen($username) >= 8) {
       if (strlen($password) < 8 || !preg_match('/[!@#$%^&*()\-_=+{};:,<.>1-9a-zA-Z]/', $password)) {
           echo "Mật khẩu phải có ít nhất 8 ký tự và chứa ít nhất một ký tự đặc biệt!";
       } else {
           $checkPassword = $_POST['repass'];
           $check_isnone = Checkuser_isnone($username);
           // Kiểm tra dữ liệu
           if (empty($phone) || empty($username) || empty($password) || empty($checkPassword)) {
            $error= "Vui lòng điền đầy đủ thông tin!";
           } elseif ($password !== $checkPassword) {
            $error= "Mật khẩu không khớp!";
           } elseif (!empty($check_isnone)) {
            $error= "Tài khoản đã tồn tại";
           } else {
               // Thêm mới người dùng
               $error= themuser($username, $password,$phone);;
           }
       }
    }
    else
    {
        $error= "Tài khoản phải có ít nhất 8 ký tự!";
    }
}
if(isset($_POST["login"])){
    header('Location: login');
    exit();
}
?>
 <div class="login-container" id="registerContainer">
        <div class="login">
            <form action="" method="post">
                <div>
                    <h1><center>Đăng Ký</center></h1>
                </div>
                <div>
                    <input type="text" id="user" name="user" value="Tài khoản" required>
                </div>
                <div>
                    <input type="text" id="pass" name="pass" value="Mật khẩu" required>
                </div>
                <div>
                    <input type="text" id="repass" name="repass" value="Nhập lại mật khẩu" required>
                </div>
                <div>
                    <input type="text" id="phone" name="phone" value="Số điện thoại" required>
                </div>
                <div style="color: #550000;text-align:center; font-style: italic">
                    <?php if (isset($error)) { echo '<p>' . $error . '</p>'; } ?>
                </div>
                <div>
                    <input type="submit" value="Đăng ký" name="reg" id="bt_log">
                </div>
                <div>
                    <a href="index?act=login" id="bt_reg">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
<body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="js/register.js"></script>