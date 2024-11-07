$(document).ready(function(){
    $('#updateForm').on('submit', function(event){
        event.preventDefault(); // Ngăn chặn hành động mặc định của form

        // Thu thập dữ liệu từ form
        var formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            method: 'post',
            data: formData,
            dataType: 'json', // Đặt kiểu dữ liệu trả về là JSON
            success: function(response){
                // Hiển thị thông báo từ response
                Swal.fire({
                    title: 'Thông báo',
                    text: response.message,
                    icon: response.status === 'success' ? 'success' : 'error',
                    showClass: {
                        popup: `
                            animate__animated
                            animate__fadeInUp
                            animate__faster
                        `
                    },
                    hideClass: {
                        popup: `
                            animate__animated
                            animate__fadeOutDown
                            animate__faster
                        `
                    }
                }).then((result) => {
                    // Chuyển hướng người dùng sau khi nhấn nút OK
                    if (result.isConfirmed) {
                        window.location.href = 'index?act=user';
                    }
                });
            },
            error: function(){
                // Hiển thị thông báo lỗi
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra khi cập nhật thông tin.',
                    icon: 'error'
                });
            }
        });
    });
});












