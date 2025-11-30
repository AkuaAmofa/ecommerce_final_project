<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (isset($_SESSION['user_id'])) {
  header("Location: ../index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    a {
      color: #0d6efd;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body class="bg-light">
  <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-4 shadow-lg" style="width: 100%; max-width: 450px;">
      <h3 class="text-center mb-4">Create Account</h3>

      <form id="registerForm" method="POST" action="../actions/register_user_action.php">
        <div class="mb-3">
          <label for="name" class="form-label">Full Name</label>
          <input 
            type="text" 
            id="name" 
            name="name" 
            class="form-control" 
            placeholder="Enter your full name" 
            required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-control" 
            placeholder="Enter your email" 
            required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            class="form-control" 
            placeholder="Enter your password" 
            required>
          <small class="text-muted">
            Password must be at least 8 characters long, include an uppercase letter, a number, and a special character.
          </small>
        </div>

        <div class="mb-3">
          <label for="country" class="form-label">Country</label>
          <input 
            type="text" 
            id="country" 
            name="country" 
            class="form-control" 
            placeholder="Enter your country" 
            required>
        </div>

        <div class="mb-3">
          <label for="city" class="form-label">City</label>
          <input 
            type="text" 
            id="city" 
            name="city" 
            class="form-control" 
            placeholder="Enter your city" 
            required>
        </div>

        <div class="mb-3">
          <label for="contact" class="form-label">Contact Number</label>
          <input 
            type="text" 
            id="contact" 
            name="contact" 
            class="form-control" 
            placeholder="Enter your contact number" 
            required>
        </div>

        <div class="mb-4">
          <label class="form-label d-block">Register As</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="role" id="customer" value="2" checked>
            <label class="form-check-label" for="customer">Customer</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="role" id="admin" value="1">
            <label class="form-check-label" for="admin">Organizer</label>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>

        <p class="text-center mt-3 mb-0">
          Already have an account? <a href="login.php">Login here</a>
        </p>
      </form>
    </div>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

  <!-- SweetAlert Feedback -->
  <script>
  $(document).ready(function() {
    $("#registerForm").on("submit", function(e) {
      e.preventDefault();

      $.ajax({
        url: "../actions/register_user_action.php",
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        beforeSend: function() {
          Swal.fire({
            title: "Registering...",
            text: "Please wait a moment.",
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
          });
        },
        success: function(res) {
          Swal.close();
          if (res.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Registration Successful",
              text: "You can now log in to your account.",
              confirmButtonText: "Login",
              confirmButtonColor: "#0d6efd"
            }).then(() => {
              window.location.href = "login.php";
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Registration Failed",
              text: res.message || "Please try again."
            });
          }
        },
        error: function(xhr) {
          Swal.close();
          Swal.fire({
            icon: "error",
            title: "Server Error",
            text: "Something went wrong. Please try again later."
          });
        }
      });
    });
  });
  </script>
</body>
</html>
