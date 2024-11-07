<?php
function checkinfor($user){
    $conn = new mysqli('localhost', 'root', '', 'product');

    // Truy vấn để lấy tên người dùng từ username
    $stmt = $conn->prepare("SELECT name FROM tbl_user WHERE username = ?");
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $stmt->close(); // Đóng câu lệnh

    // Kiểm tra xem có dữ liệu trả về không
    if ($userData) {
        // Lấy tên người dùng
        $username = $userData['name'];

        // Kiểm tra định dạng của tên người dùng
        if (preg_match('/^user\d+$/', $username)) {
            // Lấy userId từ tên người dùng
            $userId = substr($username, 4);
    ?>    
            <div class="message-form">
                <h2>Xin chào, <?php echo $username; ?>!</h2>
                <p>Bạn đã đăng nhập thành công với tài khoản có id là <?php echo $userId; ?>.</p>
                <!-- Thêm các nút hoặc chức năng khác tại đây nếu cần -->
            </div>
    <?php
        } else {
            echo '<p>Lỗi: Tên người dùng không đúng định dạng.</p>';
        }
    } else {
        // Nếu không tìm thấy thông tin người dùng
        echo '<p>Lỗi: Không tìm thấy thông tin người dùng.</p>';
    }
}

// Hàm đăng nhập sử dụng MySQLi
function Checkuser($user, $pass) {
    // Kết nối đến cơ sở dữ liệu
    $conn = new mysqli('localhost', 'root', '', 'product');

    // Xử lý chuỗi để tránh SQL injection
    $user = $conn->real_escape_string($user);
    $pass = $conn->real_escape_string($pass);

    // Chuẩn bị truy vấn
    $sql = "SELECT * FROM tbl_user WHERE username = '$user' AND password = '$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Lấy dòng đầu tiên từ kết quả
        $row = $result->fetch_assoc();
        
        // Trả về lever
        return $row['role'];
    } else {
        return false; // Đăng nhập không thành công
    }

    // Đóng kết nối
    $conn->close();
}
    function Checkuser_isnone($user) {
        // Kết nối tới cơ sở dữ liệu
        $conn = new mysqli('localhost', 'root', '', 'product');
        
        // Chuẩn bị truy vấn SQL với tham số
        $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE username = ?");

    // Gán giá trị cho tham số trong truy vấn
        $stmt->bind_param('s', $user);

        // Thực thi truy vấn
        $stmt->execute();

        // Lấy kết quả
        $result = $stmt->get_result();

        // Lấy tất cả các dòng kết quả dưới dạng mảng kết hợp
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        // Đóng kết nối
        $stmt->close();
        $conn->close();

        // Trả về kết quả
        return $rows;
    }
    


    function themuser($username, $password, $phone, $address = null)
{
    // Kết nối tới cơ sở dữ liệu
    $conn = new mysqli('localhost', 'root', '', 'product');
    try {
        // Chuẩn bị truy vấn SQL
        $sql = "INSERT INTO tbl_user (username, password, phone, name, address) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Kiểm tra lỗi khi chuẩn bị truy vấn
        if (!$stmt) {
            return "Đăng kí thất bại, hệ thống gặp lỗi!";
        }

        // Tạo giá trị mặc định cho name nếu không có thông tin
        $name = "user";

        // Thực thi truy vấn
        $stmt->bind_param('sssss', $username, $password, $phone, $name, $address);
        $result = $stmt->execute();

        if ($result) {
            // Lấy id của dòng vừa thêm vào
            $id = $stmt->insert_id;

            // Tạo username từ 'user' và id
            $name = 'user' . $id;

            // Cập nhật username vào cơ sở dữ liệu
            $sql_update = "UPDATE tbl_user SET name=? WHERE id=?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param('si', $name, $id);
            $stmt_update->execute();
            $stmt_update->close();

            return "Đăng ký tài khoản thành công!";
        } else {
            return "Đăng ký tài khoản thất bại!";
        }
        // Đóng câu lệnh
        $stmt->close();
    } catch (Exception $e) {
        return "Đăng kí thất bại, hệ thống gặp lỗi!";
    }

    // Đóng kết nối
    $conn->close();
}
?>