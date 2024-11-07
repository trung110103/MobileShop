<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
</head>
<body>
<div id="bieudo">
    <div class="title">
        <h1>Biểu đồ doanh thu</h1>
    </div>
    <canvas id="myChart" width="400" height="200"></canvas>
    <div class="button-container">
        <button id="prevBtn" class="btn">Sang trái</button>
        <button id="nextBtn" class="btn">Sang phải</button>
    </div>
</div>

<?php
// Tạo kết nối
$conn = new mysqli('localhost', 'root', '', 'product');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn dữ liệu
$sql = "SELECT DATE(thoi_gian_khoi_tao) as ngay, SUM(gia_tien) as tong_gia 
        FROM tbl_giohang 
        WHERE trang_thai_don_hang = 3
        GROUP BY DATE(thoi_gian_khoi_tao)";
$result = $conn->query($sql);

$data = array();
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let data = <?php echo json_encode($data); ?>;
        let last10DaysData = data.slice(-10); // Lấy 10 ngày gần nhất ban đầu

        const labels = last10DaysData.map(item => item.ngay);
        const totals = last10DaysData.map(item => item.tong_gia);

        const ctx = document.getElementById('myChart').getContext('2d');
        let myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tổng giá trị (VND)',
                    data: totals,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
            const prevBtn = document.getElementById('prevBtn');
            prevBtn.addEventListener('click', function() {
                // Lấy ngày đầu tiên và ngày cuối cùng trong last10DaysData
                let firstDate = new Date(last10DaysData[0].ngay);
                let lastDate = new Date(last10DaysData[last10DaysData.length - 1].ngay);

                // Tạo ngày mới trước đó
                let prevDate = new Date(firstDate.getTime() - 24 * 60 * 60 * 1000);

                // Kiểm tra ngày mới có trong dữ liệu không
                let prevDateFormatted = prevDate.toISOString().split('T')[0];
                let newDataItem = data.find(item => item.ngay === prevDateFormatted);

                if (newDataItem) {
                    // Thêm ngày mới vào đầu mảng
                    last10DaysData.unshift(newDataItem);

                    // Xóa ngày cuối cùng khỏi mảng
                    last10DaysData.pop();
                } else {
                    // Nếu không tìm thấy ngày mới trong dữ liệu, lấy 10 ngày đầu tiên
                    last10DaysData = data.slice(0, 10);
                }

                // Cập nhật lại biểu đồ và ẩn/hiện nút
                updateChart();
                toggleButtonsVisibility();
            });


        // Xử lý nút Sang phải
        const nextBtn = document.getElementById('nextBtn');
        nextBtn.addEventListener('click', function() {
            // Lấy ngày cuối cùng trong last10DaysData
            let lastDate = new Date(last10DaysData[last10DaysData.length - 1].ngay);

            // Tạo ngày mới tiếp theo
            let nextDate = new Date(lastDate.getTime() + 24 * 60 * 60 * 1000);

            // Kiểm tra ngày mới có trong dữ liệu không
            let nextDateFormatted = nextDate.toISOString().split('T')[0];
            let newDataItem = data.find(item => item.ngay === nextDateFormatted);

            if (newDataItem) {
                // Thêm ngày mới vào cuối mảng
                last10DaysData.push(newDataItem);

                // Xóa ngày đầu tiên khỏi mảng
                last10DaysData.shift();
            } else {
                // Nếu không tìm thấy ngày mới trong dữ liệu, lấy 10 ngày cuối cùng
                last10DaysData = data.slice(-10);
            }

            // Cập nhật lại biểu đồ và ẩn/hiện nút
            updateChart();
            toggleButtonsVisibility();
        });


        // Hàm cập nhật biểu đồ
        function updateChart() {
            myChart.data.labels = last10DaysData.map(item => item.ngay);
            myChart.data.datasets[0].data = last10DaysData.map(item => item.tong_gia);
            myChart.update();
        }

        // Hàm ẩn/hiện nút khi không có dữ liệu trước đó hoặc tiếp theo
        function toggleButtonsVisibility() {
            const currentIndex = data.findIndex(item => item.ngay === last10DaysData[0].ngay);
            prevBtn.style.visibility = currentIndex > 0 ? 'visible' : 'hidden';

            const lastIndex = data.findIndex(item => item.ngay === last10DaysData[last10DaysData.length - 1].ngay);
            nextBtn.style.visibility = lastIndex < data.length - 1 ? 'visible' : 'hidden';
        }
        // Ban đầu ẩn nút "Sang trái" nếu không đủ dữ liệu để lùi về thêm ngày trước
        toggleButtonsVisibility();
    });

</script>

</body>
</html>
