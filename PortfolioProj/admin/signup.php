<?php
include('../config/portfolioDB.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');

  
    $public_id = bin2hex(random_bytes(16));

   
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  
    $sql = 'INSERT INTO users (username, email, password, public_id) VALUES (:username, :email, :password, :public_id)';
    $stmt = $pdo->prepare($sql);

    $params = [
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'public_id' => $public_id
    ];

    try {
        $stmt->execute($params);
        $_SESSION['success_message'] = 'Account created successfully!';

        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | My Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

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
        .signup-container {
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
            border-color: #ffffff;
            color: #fff;
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
       

        #passwordRequirements span {
    display: inline-block;
    font-size: 0.85rem;
    margin-right: 5px;
    padding: 0;
}
#passwordRequirements .valid {
    color: green;
}
#passwordRequirements .invalid {
    color: red;
}
.toggle-password {
    cursor: pointer;
    font-size: 1.2rem;
    color: #000;
}
.toggle-password:hover {
    color: #ffffff;
}
i{
    color: #fff;
}


    </style>
</head>
<body>

<div class="signup-container">
    <h2>Create Your Account</h2>
    <form method="POST" id="signupForm">

        <div class="mb-3 position-relative">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-fill" style="color: black;"></i></i></span>
                <input type="text" class="form-control" id="username" name="username" required>
               
            </div>
        </div>

       
        <div class="mb-3 position-relative">
            <label for="email" class="form-label">Email address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope-fill" style="color: black;"></i></span>
                <input type="email" class="form-control" id="email" name="email" required>
               
            </div>
        </div>

       
        <div class="mb-3 position-relative">
            <label for="password" class="form-label">Password</label>
            <div class="input-group" style="background-color:#444;">
                <span class="input-group-text"><i class="bi bi-lock-fill" style="color: black;"></i></span>
                <input type="password" class="form-control" id="password" name="password" required>
               
            </div>
            <span class="toggle-password" onclick="togglePasswordVisibility('password', this)" style="position: absolute; right: 10px; top: 38px; cursor: pointer;">
                <i class="bi bi-eye"></i>
            </span>
            <p class="mt-2">Password must include:</p>
            <div id="passwordRequirements" class="text-muted mt-2">
                <span id="length" class="invalid">At least 8 characters</span> |
                <span id="uppercase" class="invalid">At least one uppercase letter</span> |
                <span id="number" class="invalid">At least one number</span> |
                <span id="special" class="invalid">At least one special character</span>
            </div>
        </div>

        <!-- Confirm Password Field with Icon -->
        <div class="mb-3 position-relative">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill" style="color: black;"></i></span>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
              
            </div>
            <span class="toggle-password" onclick="togglePasswordVisibility('confirmPassword', this)" style="position: absolute; right: 10px; top: 38px; cursor: pointer;">
                <i class="bi bi-eye"></i>
            </span>
            <div id="passwordError" class="text-danger mt-2" style="display: none;">Passwords do not match.</div>
        </div>

       
        <button type="submit" name="submit" class="btn btn-custom" id="submitBtn" >Sign Up</button>
    </form>

  
    <div class="mt-3 text-center">
        <p>Already have an account? <a href="index.php">Login here</a></p>
    </div>
</div>

<script>
   document.addEventListener('DOMContentLoaded', function () {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordError = document.getElementById('passwordError');
    const submitBtn = document.getElementById('submitBtn');

    const lengthReq = document.getElementById('length');
    const uppercaseReq = document.getElementById('uppercase');
    const numberReq = document.getElementById('number');
    const specialReq = document.getElementById('special');

    const lengthPattern = /.{8,}/;
    const uppercasePattern = /[A-Z]/;
    const numberPattern = /[0-9]/;
    const specialPattern = /[!@#$%^&*(),.?":{}|<>]/;

    function validatePassword() {
        const value = password.value;

        lengthReq.classList.toggle('valid', lengthPattern.test(value));
        lengthReq.classList.toggle('invalid', !lengthPattern.test(value));

        uppercaseReq.classList.toggle('valid', uppercasePattern.test(value));
        uppercaseReq.classList.toggle('invalid', !uppercasePattern.test(value));

        numberReq.classList.toggle('valid', numberPattern.test(value));
        numberReq.classList.toggle('invalid', !numberPattern.test(value));

        specialReq.classList.toggle('valid', specialPattern.test(value));
        specialReq.classList.toggle('invalid', !specialPattern.test(value));

        const isPasswordValid =
            lengthPattern.test(value) &&
            uppercasePattern.test(value) &&
            numberPattern.test(value) &&
            specialPattern.test(value);

        const passwordsMatch = value === confirmPassword.value;

        if (isPasswordValid && passwordsMatch) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }

        if (confirmPassword.value && value !== confirmPassword.value) {
            passwordError.textContent = "Passwords do not match.";
            passwordError.style.display = 'block';
        } else if (confirmPassword.value && value === confirmPassword.value) {
            passwordError.textContent = "Passwords match!";
            passwordError.style.display = 'block';
            passwordError.classList.remove('text-danger');
            passwordError.classList.add('text-success');
        } else {
            passwordError.style.display = 'none';
        }
    }

    password.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);
});

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





    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
