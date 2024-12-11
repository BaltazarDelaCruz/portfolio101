<?php
session_start();
include('../config/portfolioDB.php');
$public_id = isset($_GET['portfolio']) ? $_GET['portfolio'] : '';


    $stmt = $pdo->prepare('
    SELECT 
        users.username, 
        landingpage.*,
        users.id AS portfolio_id,
        social.fb,
        social.github,
        social.linked,
        social.email,
        social.tg,
        users.public_id,
        contact_messages.*
    FROM users 
    LEFT JOIN contact_messages ON users.id = contact_messages.user_id
    LEFT JOIN social ON users.id = social.user_id
    LEFT JOIN landingpage ON users.id = landingpage.user_id
     WHERE users.public_id = :public_id
');

    $stmt->bindParam(':public_id', $public_id, PDO::PARAM_STR);
    $stmt->execute();
    $portfolio = $stmt->fetch(PDO::FETCH_ASSOC);
  
    if (!$portfolio) {
        echo "Portfolio not found or not public.";
        exit;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact | <?= isset($portfolio['navbrand']) ? htmlspecialchars($portfolio['navbrand']) :''?></title>
  <link rel="icon" href="<?= isset($portfolio['image_path']) ? htmlspecialchars($portfolio['image_path']) :'default.jpg'?>" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_drop_down" />

</head>
<style>
    body {
    background-color: #fff;
    font-family: Arial, Helvetica, sans-serif;
}

nav {
    background-color: #FFFFFF;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    border-bottom: 2px solid #000;
    font-family: Arial, Helvetica, sans-serif;
    color: #000;
}

.navbar-nav {
    display: flex;
    justify-content: center;
    flex: 1;
    margin: 0;
}

.navbar-nav li {
    list-style: none;
    margin: 0 10px;
}

nav a {
    color: #000000;
    text-decoration: none;
    padding: 10px;
    font-size: 1.2rem;
    display: inline-block;
    border-radius: 10px;
    margin: 5px;
    transition: background-color 0.3s, color 0.3s, border-bottom 0.3s;
    position: relative;
}

nav a::after {
    content: "";
    display: block;
    width: 0;
    height: 2px;
    background: #000000;
    position: absolute;
    bottom: 0;
    left: 0;
    transition: width 0.3s ease-in-out;
}

nav a:hover::after {
    width: 100%;
}

nav a.active::after {
    width: 100%;
}

.container-fluid {
    display: flex;
    align-items: center;
    width: 100%;
}

li {
    list-style-type: none;
    font-size: 1.2rem;
}

.custom-divider {
    border-color: #000;
    border-width: 2px;
    margin: 0.5rem 0;
}


.contact {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.8);
}

h2 {
    color: #000;
    font-family: 'Georgia', serif;
    font-weight: bold;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
}


.contact {
    margin-top: 90px;
    color: #333;
    border-top: 2px solid #000;
    border-radius: 10px;
    margin-bottom: 20px;
    background-color: #fff;
    border-top: 2px solid #000; 
    padding: 25px; 
    border-radius: 10px;
}
.btn-black-primary {
    border: 2px solid black;
    color: black;
    background-color: transparent;
    transition: all 0.3s ease;
}

.btn-black-primary:hover {
    background-color: black;
    color: white;
}

.contact-content {
    flex: 1;
    padding-right: 20px;
}

body.dark-mode {
    background-color: #121212;
    color: #fff;
}

nav.dark-mode {
    background-color: #1a1a1a;
    border-bottom: 2px solid white;
}

.nav-link.dark-mode.active {
    color: #ffffff;
}

.contact.dark-mode {
    background-color: #121212;
    box-shadow: 0 8px 16px rgba(255, 255, 255, 0.8);
    border-top: 2px solid #ffffff;
}


h2.dark-mode {
    color: #fff;
    border-bottom: 2px solid #fff;
}
p.dark-mode{
  color: #fff;
}

.container.dark-mode {
    color: #ffffff;
}

.nav-link.dark-mode:hover {
    color: #ffffff;
}

nav.dark-mode .dropdown-menu {
    background-color: #333;
    border-color: #444;
}

nav.dark-mode .dropdown-item {
    color: #fff;
}

nav.dark-mode .dropdown-item:hover {
    background-color: #555;
}

nav a.dark-mode {
    color: #ffffff;
    text-decoration: none;
    padding: 10px;
    font-size: 1.2rem;
    display: inline-block;
    border-radius: 10px;
    margin: 5px;
    transition: background-color 0.3s, color 0.3s, border-bottom 0.3s;
    position: relative;
}

nav a::after.dark-mode {
    content: "";
    display: block;
    width: 0;
    height: 2px;
    background: #ffffff;
    position: absolute;
    bottom: 0;
    left: 0;
    transition: width 0.3s ease-in-out;
}

nav.dark-mode a.active {
    color: #ffffff;
    border-bottom: #fff;
}

body.dark-mode nav a {
    color: #ffffff;
}

body.dark-mode nav a:hover {
    color: #ffffff;
}

body.dark-mode nav a::after {
    background: #ffffff;
}

body.dark-mode nav a.active {
    color: #ffffff;
    border-bottom: #fff;
}


nav .material-symbols-outlined {
    margin-left: 8px;
    vertical-align: middle;
    font-size: 1.2rem;
    line-height: 1; 
}


.nav-item.dropdown > a {
    display: flex;
    align-items: center;
}
.btn-black-primary.dark-mode {
    border: 2px solid white;
    color: white;
    background-color: transparent;
    transition: all 0.3s ease;
}

.btn-black-primary.dark-mode:hover {
    background-color: white;
    color: black;
}


.social.dark-mode {
    color: white;
    transition: color 0.3s ease, background-color 0.3s ease;
}

.social.dark-mode:hover {
    color: black;
    
}
.social{
    color:#000;
}






</style>
<body>
<?php if (isset($_SESSION['message'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 10px; left: 50%; transform: translateX(-50%); z-index: 1050; width: 80%; text-align: center;">
    <?= htmlspecialchars($_SESSION['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php unset($_SESSION['message']); ?>
<?php endif; ?>
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#"><?= isset($portfolio['navbrand']) ? htmlspecialchars($portfolio['navbrand']) :'navbrand'?></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" aria-current="page" href="home.php?portfolio=<?=htmlspecialchars($portfolio['public_id'])?>">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="aboutme.php?portfolio=<?=htmlspecialchars($portfolio['public_id'])?>">About me</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="skills.php?portfolio=<?=htmlspecialchars($portfolio['public_id'])?>">Skills</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="contact.php?portfolio=<?=htmlspecialchars($portfolio['public_id'])?>">Contact</a>
            </li>
            <li>
            
        </ul>
        <li>
        <a 
    class="nav-link night-mode-toggle d-flex align-items-center justify-content-center" 
    href="#" 
    id="nightModeToggle" 
    onclick="toggleNightMode()">
    <i class="fas fa-sun me-2"></i> 
    <span>Light Mode</span>
</a>
        </li>
        

      </div>
    </div>
  </nav>
  <section class="contact container py-5">
    <div class="container">
   
        <h2 class="text-center mb-4">Contact Me</h2>
        <p class="text-center mb-5">Feel free to reach out via the form below or connect with me on social media!</p>
        
      
        <div class="row justify-content-center">
    <div class="col-md-8">
        <form action="contact_process.php?portfolio=<?= htmlspecialchars($portfolio['public_id']) ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Your Name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter your name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Your Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea id="message" name="message" class="form-control" rows="5" placeholder="Enter your message" required></textarea>
            </div>
            <button type="submit" class="btn btn-black-primary w-100">Send Message</button>
        </form>
    </div>
</div>

        <div class="text-center mt-5">
    <a href="<?= isset($portfolio['fb']) ? htmlspecialchars($portfolio['fb']) : '' ?>" target="_blank" class="mx-2 social">
        <i class="fab fa-facebook fa-2x"></i>
    </a>
    <a href="<?= isset($portfolio['github']) ? htmlspecialchars($portfolio['github']) : '' ?>" target="_blank" class="mx-2 social">
        <i class="fab fa-github fa-2x"></i>
    </a>
    <a href="<?= isset($portfolio['telegram']) ? htmlspecialchars($portfolio['telegram']) : '' ?>" target="_blank" class="mx-2 social">
        <i class="fab fa-telegram fa-2x"></i>
    </a>
    <a href="<?= isset($portfolio['linked']) ? htmlspecialchars($portfolio['linked']) : '' ?>" target="_blank" class="mx-2 social">
        <i class="fab fa-linkedin fa-2x"></i>
    </a>
    <a href="mailto:<?= isset($portfolio['email']) ? htmlspecialchars($portfolio['email']) : '' ?>" class="mx-2 social">
        <i class="fas fa-envelope fa-2x"></i>
    </a>
</div>

    </div>
</section>







  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
   document.addEventListener('DOMContentLoaded', function () {
    const savedMode = localStorage.getItem('mode');
    if (savedMode === 'dark') {
        enableDarkMode();
    }
});

document.getElementById('nightModeToggle').addEventListener('click', function () {
    if (document.body.classList.contains('dark-mode')) {
        disableDarkMode();
        localStorage.setItem('mode', 'light');
    } else {
        enableDarkMode();
        localStorage.setItem('mode', 'dark');
    }
});

function enableDarkMode() {
    document.body.classList.add('dark-mode');
    document.querySelectorAll('nav').forEach(nav => nav.classList.add('dark-mode'));
    document.querySelectorAll('.nav-link').forEach(link => link.classList.add('dark-mode'));
    document.querySelectorAll('.contact').forEach(about => about.classList.add('dark-mode'));
    document.querySelectorAll('h2').forEach(h2 => h2.classList.add('dark-mode'));
    document.querySelectorAll('p').forEach(p => p.classList.add('dark-mode'));
    document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('dark-mode'));
    document.querySelectorAll('.dropdown-item').forEach(item => item.classList.add('dark-mode'));
    document.querySelectorAll('.social').forEach(item => item.classList.add('dark-mode'));
    document.querySelectorAll('.btn').forEach(item => item.classList.add('dark-mode'));
}

function disableDarkMode() {
    document.body.classList.remove('dark-mode');
    document.querySelectorAll('nav').forEach(nav => nav.classList.remove('dark-mode'));
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('dark-mode'));
    document.querySelectorAll('.contact').forEach(about => about.classList.remove('dark-mode'));
    document.querySelectorAll('h2').forEach(h2 => h2.classList.remove('dark-mode'));
    document.querySelectorAll('p').forEach(p => p.classList.remove('dark-mode'));
    document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.remove('dark-mode'));
    document.querySelectorAll('.dropdown-item').forEach(item => item.classList.remove('dark-mode'));
    document.querySelectorAll('.social').forEach(item => item.classList.add('dark-mode'));
    document.querySelectorAll('.btn').forEach(item => item.classList.add('dark-mode'));
}

  </script>
</body>
</html>
