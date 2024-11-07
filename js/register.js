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
document.getElementById('repass').addEventListener('focus', function() {
    if (this.value === 'Nhập lại mật khẩu') {
        this.value = '';
    }
});

document.getElementById('repass').addEventListener('blur', function() {
    if (this.value === '') {
        this.value = 'Nhập lại mật khẩu';
    }
});
document.getElementById('phone').addEventListener('focus', function() {
    if (this.value === 'Số điện thoại') {
        this.value = '';
    }
});

document.getElementById('phone').addEventListener('blur', function() {
    if (this.value === '') {
        this.value = 'Số điện thoại';
    }
});
document.addEventListener('DOMContentLoaded', function() {
    var registerContainer = document.getElementById('registerContainer');
    var registerForm = document.getElementById('registerForm');
    var registerMessage = document.getElementById('registerMessage');

    // Event listener to close the form when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target == registerContainer) {
            registerContainer.style.display = 'none';
        }
    });

    // Prevent form closure when clicking inside the form
    registerForm.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    // Function to show the registration form
    function showRegisterForm() {
        registerContainer.style.display = 'flex';
    }

    // Show registration form initially
    showRegisterForm();    
});
