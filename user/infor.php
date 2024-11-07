<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Bắt đầu session nếu chưa có session nào
}
// Kiểm tra nếu session username tồn tại
if (!isset($_SESSION['user'])) {
    header('Location: index'); 
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
    echo "<div id='form_infor'><h1>Thông tin cá nhân</h1>";
    echo "<table border='1' class='infor_1'>";
    
    while($row = $result->fetch_assoc()) {
        // Lấy số điện thoại từ cơ sở dữ liệu và giấu 3 số ở giữa
        $phone = htmlspecialchars($row['phone']);   
        $hidden_digits = substr($phone, 3, -3); // Lấy phần 3 số ở đầu và cuối
        $hidden_phone = substr_replace($phone, str_repeat('*', strlen($hidden_digits)), 3, -3);
        
        // Hiển thị thông tin
        echo "<tr><th>Username</th><td>" . htmlspecialchars($row['username']) . "</td></tr>";
        echo "<tr><th>Name</th><td>" . htmlspecialchars($row['name']) . "</td></tr>";
        echo "<tr><th>Phone</th><td>" . $hidden_phone . "</td></tr>";
        echo "<tr><th>Address</th><td>" . htmlspecialchars($row['address']) . "</td></tr>";
    }
    echo "</table>";
    
} else {
    echo "Không có dữ liệu.";
}
$conn->close();

echo '<div class="button-container">';
echo '<a href="#" id="bt_edit">Sửa thông tin</a>';
echo '</div>';
echo "</div>";
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $('#bt_edit').click(function(e){
        e.preventDefault();
        $.ajax({
            url: 'user/edit_infor.php',
            method: 'GET',
            success: function(response) {
                $('#form_infor').html(response);
            },
            error: function() {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            }
        });
    });
});
</script>
