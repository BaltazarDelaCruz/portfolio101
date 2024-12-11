<?php
include('../config/portfolioDB.php');

$public_id = isset($_GET['portfolio']) ? $_GET['portfolio'] : '';

    $stmt = $pdo->prepare('
    SELECT 
        users.username, 
        landingpage.*,
        users.id AS portfolio_id,
        users.public_id
    FROM users
    INNER JOIN landingpage ON users.id = landingpage.user_id 
    WHERE users.public_id = :public_id
');
$stmt->bindParam(':public_id', $public_id, PDO::PARAM_STR);
$stmt->execute();
$portfolio = $stmt->fetch(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare('
    SELECT 
        skills.skill_name,
        skills.image_skills,
        skills.id AS skill_id,
        users.public_id
    FROM skills
    INNER JOIN users ON users.id = skills.user_id
    WHERE users.public_id = :public_id
');
$stmt->bindParam(':public_id', $public_id, PDO::PARAM_STR);
$stmt->execute();
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);



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
  <title>Skills | <?= isset($portfolio['navbrand']) ? htmlspecialchars($portfolio['navbrand']) :''?></title>
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
}


.skills {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.8);
}

h2 {
    color: #000;
    font-family: 'Georgia', serif;
    font-weight: bold;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
}

p {
    color: #333;
}

.about-image img:hover {
    filter: grayscale(100%);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
    transform: scale(1.05);
}

.skills {
    margin-top: 100px;
    color: #333;
    border-top: 2px solid #000;
    border-radius: 10px;
    margin-bottom: 20px;
}

.about-content {
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

.skills.dark-mode {
    background-color: #121212;
    box-shadow: 0 8px 16px rgba(255, 255, 255, 0.8);
    border-top: 2px solid #ffffff;
}

p.dark-mode {
    color: #ffffff;
}

h2.dark-mode {
    color: #E5E5E5;
    border-bottom: 2px solid #E5E5E5;
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


        
.skill-card {
            border: 1px solid #000; /* Black border */
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff; /* White background for the cards */
            color: #000; /* Black text */
            text-align: center;
            transition: transform 0.3s ease, background-color 0.3s ease;
            overflow: hidden; /* Ensure the image doesn't overflow the card */
        }

        .skill-image:hover {
            transform: translateY(-10px);
            background-color: #f0f0f0; 
            color: #000; 
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .skill-image {
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            margin-bottom: 20px; 
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .skill-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #000;
            margin-bottom: 15px;
        }

      

.delete-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0; 
    font-size: 1.0rem; 
    margin-bottom: 8px;
}
.delete-checkbox {
    width: 20px;
    height: 20px;
    
}



.skill-card {
    position: relative;
    transition: box-shadow 0.3s;
}

.skill-card:hover {
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
}
.modal-header {
            background-color: #f5f5f5;
            color: white;
        }

        .modal-title {
            font-weight: bold;
        }

        .modal-body {
            background-color: #f8f9fa;
        }

        .form-control {
            border-radius: 0.25rem;
            border: 1px solid #ced4da;
        }

        .image-entry {
            padding: 1rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-close {
            background-color: transparent;
            border: none;
        }

        /* Hover effect for 'Add More' button */
        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Submit Button Styling */
        .btn-submit {
            background-color: #28a745;
            color: white;
        }
        .btn-submit:hover {
            background-color: #218838;
        }


</style>
<body>

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
              <a class="nav-link active" href="skills.php?portfolio=<?=htmlspecialchars($portfolio['public_id'])?>">Skills</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="contact.php?portfolio=<?=htmlspecialchars($portfolio['public_id'])?>"">Contact</a>
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

  <section class="skills container py-5">
    <div class="container">
        <h2 class="text-center mb-4">Skills</h2>
    
        <form action="delete_skills.php" method="POST" id="deleteSkillsForm">
           
            <div class="row g-4" id="skills-container">
                <?php foreach ($skills as $index => $skill): ?>
                    <?php if (!empty($skill['image_skills'])): ?>
                        <div class="col-md-3 d-flex justify-content-center mb-4 position-relative skill-card-wrapper" data-index="<?= $index ?>"> 
                            <div class="card skill-card text-center">
                                <img 
                                    src="<?= htmlspecialchars($skill['image_skills']) ?? 'default.jpg' ?>" 
                                    class="skill-image" 
                                    alt="<?= htmlspecialchars($skill['skill_name']) ?>" 
                                    style="height: 200px; width: 200px; border: solid #000;">
                                <h5 class="skill-title mt-3"><?= htmlspecialchars($skill['skill_name']) ?? 'Java, SQL etc....' ?></h5>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

        
        </form>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
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
    document.querySelectorAll('.skills').forEach(skill => skill.classList.add('dark-mode'));
    document.querySelectorAll('h2').forEach(h2 => h2.classList.add('dark-mode'));
    document.querySelectorAll('p').forEach(p => p.classList.add('dark-mode'));
    document.querySelectorAll('a').forEach(a => a.classList.add('dark-mode'));
}

function disableDarkMode() {
    document.body.classList.remove('dark-mode');
    document.querySelectorAll('nav').forEach(nav => nav.classList.remove('dark-mode'));
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('dark-mode'));
    document.querySelectorAll('.skills').forEach(skill => skill.classList.remove('dark-mode'));
    document.querySelectorAll('h2').forEach(h2 => h2.classList.remove('dark-mode'));
    document.querySelectorAll('p').forEach(p => p.classList.remove('dark-mode'));
    document.querySelectorAll('a').forEach(a => a.classList.remove('dark-mode'));
}

  
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    
   
  </script>
</body>
</html>
