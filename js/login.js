$(document).ready(function () {
    $('#loginForm').submit(function (e) {
        e.preventDefault();

        let email = $('#email').val().trim();
        let password = $('#password').val();

        // Validate fields
        if (email === '' || password === '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter both email and password!',
            });
            return;
        }

        // Validate email format
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address!',
            });
            return;
        }

        // AJAX request
        $.ajax({
            url: '../actions/login_user_action.php',
            type: 'POST',
            dataType: 'json',
            data: {
                email: email,
                password: password
            },
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect based on user role
                            if (response.is_admin) {
                                window.location.href = '../admin/dashboard.php'; // Admin dashboard
                            } else {
                                window.location.href = '../index.php'; // Customer homepage
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message,
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Something went wrong, please try again later!',
                });
            }
        });
    });
});
