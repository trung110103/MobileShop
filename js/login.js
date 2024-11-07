document.getElementById('user').addEventListener('focus', function() {
    if (this.value === 'Tài khoản') {
        this.value = '';
    }
});

document.getElementById('user').addEventListener('blur', function() {
    if (this.value === '') {
        this.value = 'Tài khoản';
    }
});

document.getElementById('pass').addEventListener('focus', function() {
    if (this.value === 'Mật khẩu') {
        this.value = '';
    }
});

document.getElementById('pass').addEventListener('blur', function() {
    if (this.value === '') {
        this.value = 'Mật khẩu';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var loginContainer = document.getElementById('loginContainer');

    // Event listener to close the form when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target == loginContainer) {
            loginContainer.style.display = 'none';
        }
    });

    // Prevent form closure when clicking inside the form
    document.querySelector('.login').addEventListener('click', function(event) {
        event.stopPropagation();
    });
});
$(document).ready(function() {
    // Đợi cho tài liệu tải hoàn tất
    $(".login-container").fadeIn(500);
});
