<?php
include('../config/portfolioDB.php');
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    
    $email = htmlspecialchars($_POST['email'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');

    
    $sql = 'SELECT * FROM users WHERE email = :email';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    
    $stmt->execute();

   
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
       
        $_SESSION['email'] = $user['email'];
        $_SESSION['id'] = $user['id']; 
        $_SESSION['username'] = $user['username']; 
        $_SESSION['success_message'] = 'Login successful! Welcome ' . $user['username'];
        header('Location: main.php'); 
        exit;
    } else {
        
       
        $error_message = 'Invalid email or password. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | My Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <style>
        body {
            background: url('bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            object-fit: cover;
        }
        .login-container {
            background-color: rgba(31, 31, 31, 0.8); 
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 6px rgba(255, 253, 253, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }
        .btn-custom {
            background-color: #060707;
            color: white;
            border: none;
            width: 100%;
        }
        .btn-custom:hover {
            background-color: #ffffff;
            color: #000000;
        }
        .form-label {
            color: #ddd;
        }
        .form-control {
            background-color: #333;
            border-color: #444;
            color: #fff;
        }
        .form-control:focus {
            background-color: #444;
            border-color: #007bff;
            color: #fff;
        }
        .form-check-label {
            color: #ddd;
        }
        .text-center a {
            color: #ffffff;
            text-decoration: none;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
        .form-control {
            background-color: #333;
            border-color: #444;
            color: #fff;
        }
        .form-control:focus {
            background-color: #444;
            border-color: #ffffff;
            color: #fff;
        }
        .form-control:focus + .form-label {
            color: #ffffff;
        }
    </style>
</head>
<body>
<?php if (isset($_SESSION['success_message'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 10px; left: 50%; transform: translateX(-50%); z-index: 1050; width: 80%; text-align: center;">
    <?= htmlspecialchars($_SESSION['success_message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<div class="login-container">
    <h2>Login to Your Portfolio</h2>
    <form method="POST" id="loginForm">
        
    
        <div class="mb-3 position-relative">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                <input type="text" class="form-control" id="email" name="email" required>
                
            </div>
        </div>

      
        <div class="mb-3 position-relative">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <span class="toggle-password" onclick="togglePasswordVisibility('password', this)" style="position: absolute; right: 10px; top: 38px; cursor: pointer;">
                <i class="bi bi-eye"></i>
            </span>
        </div>

       
        <?php if (isset($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: smaller;">
        <?php echo $error_message; ?>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>


      
        <button type="submit" name="submit" class="btn btn-custom">Login</button>
    </form>

    <div class="mt-3 text-center">
        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script>
    function togglePasswordVisibility(fieldId, iconElement) {
        const passwordField = document.getElementById(fieldId);
        const icon = iconElement.querySelector("i");
        
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            passwordField.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }
</script>
</body>
</html>
