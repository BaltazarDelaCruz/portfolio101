<?php
include('../config/portfolioDB.php');
session_start();
if (isset($_SESSION['id']) && isset($_SESSION['username'])) {
    $user_id = $_SESSION['id'];
    $username = $_SESSION['username'];

 
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :user_id');
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = '
    SELECT 
        users.username, 
        landingpage.*,
        social.fb,
        social.github,
        social.linked,
        social.email,
        social.tg
    FROM users
    INNER JOIN landingpage ON users.id = landingpage.user_id 
    LEFT JOIN social ON users.id = social.user_id
    WHERE users.id = :user_id';
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $portfolio = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        // Sanitize and process form data
        $name = htmlspecialchars($_POST['name'] ?? $portfolio['name']);
        $title = htmlspecialchars($_POST['title'] ?? $portfolio['title']);
        $body = htmlspecialchars($_POST['body'] ?? $portfolio['body']);
        $button = htmlspecialchars($_POST['button'] ?? 'button');
        $aboutme = htmlspecialchars($_POST['aboutme'] ?? $portfolio['aboutme']);
        $birthdate = htmlspecialchars($_POST['birthdate'] ?? $portfolio['birthdate']);
        $navbrand = htmlspecialchars($_POST['navbrand'] ?? $portfolio['navbrand']);
        $hobbies = htmlspecialchars($_POST['hobbies'] ?? '');
        $college = htmlspecialchars($_POST['college'] ?? '');
        $degree = htmlspecialchars($_POST['degree'] ?? '');
        $uploadedFilePath = $portfolio['image_path'];
        $fb = htmlspecialchars($_POST['fb'] ?? $portfolio['fb']);
        $github = htmlspecialchars($_POST['github'] ?? $portfolio['github']);
        $linked = htmlspecialchars($_POST['linked'] ?? $portfolio['fb']);
        $email = htmlspecialchars($_POST['email'] ?? $portfolio['email']);
        $tg = htmlspecialchars($_POST['tg'] ?? $portfolio['tg']);

       
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['image']['tmp_name'];
            $imageName = basename($_FILES['image']['name']);
            $imageType = mime_content_type($imageTmpPath);
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
            if (in_array($imageType, $allowedTypes)) {
                // Define the two directories
                $uploadedFilePath = '../admin/uploads/';
                $uploadedFilePath = '../client/uploads/';
        
                // Ensure both directories exist, create them if not
                if (!is_dir( $uploadedFilePath)) {
                    mkdir( $uploadedFilePath, 0755, true);
                }
                if (!is_dir( $uploadedFilePath)) {
                    mkdir( $uploadedFilePath, 0755, true);
                }
        
                // Generate a unique file name to avoid conflicts
                $uploadedFilePath =  $uploadedFilePath . uniqid() . '_' . $imageName;
                $uploadedFilePath =  $uploadedFilePath . uniqid() . '_' . $imageName;
        
                // Move the file to both directories
                if (!move_uploaded_file($imageTmpPath,  $uploadedFilePath)) {
                    echo "Error moving uploaded file to admin directory.";
                } else {
                    // Optionally, copy the file to the client directory
                    if (!copy( $uploadedFilePath,  $uploadedFilePath)) {
                        echo "Error copying file to client directory.";
                    }
                }
            } else {
                echo "Invalid image type. Only JPG, PNG, and GIF files are allowed.";
            }
        }
        

     
        if ($portfolio) {
            $updateLandingPageSql = '
                UPDATE landingpage 
                SET name = :name, title = :title, body = :body, button = :button, image_path = :image_path,
                    aboutme = :aboutme, birthdate = :birthdate, hobbies = :hobbies, college = :college, degree = :degree, navbrand = :navbrand
                WHERE user_id = :user_id';
            $stmt = $pdo->prepare($updateLandingPageSql);
            $stmt->execute([
                ':name' => $name,
                ':title' => $title,
                ':body' => $body,
                ':button' => $button,
                ':image_path' => $uploadedFilePath,
                ':aboutme' => $aboutme,
                ':birthdate' => $birthdate,
                ':hobbies' => $hobbies,
                ':college' => $college,
                ':degree' => $degree,
                ':navbrand' => $navbrand,
                ':user_id' => $user_id
            ]);

            // Update social media data
            if (isset($portfolio['fb'])) {
                $updateSocialSql = '
                    UPDATE social 
                    SET fb = :fb, github = :github, linked = :linked, email = :email, tg = :tg 
                    WHERE user_id = :user_id';
                $stmt = $pdo->prepare($updateSocialSql);
                $stmt->execute([
                    ':fb' => $fb,
                    ':github' => $github,
                    ':linked' => $linked,
                    ':email' => $email,
                    ':tg' => $tg,
                    ':user_id' => $user_id,
                ]);
            } else {
    
                $insertSocialSql = '
                    INSERT INTO social (fb, github, linked, email, tg, user_id) 
                    VALUES (:fb, :github, :linked, :email, :tg, :user_id)';
                $stmt = $pdo->prepare($insertSocialSql);
                $stmt->execute([
                    ':fb' => $fb,
                    ':github' => $github,
                    ':linked' => $linked,
                    ':email' => $email,
                    ':tg' => $tg,
                    ':user_id' => $user_id,
                ]);
            }

            $_SESSION['success_message'] = 'Portfolio info updated successfully!';
        } else {
            $addLandingPageSql = '
                INSERT INTO landingpage (name, title, body, user_id, button, image_path, aboutme, birthdate, hobbies, college, degree, navbrand)
                VALUES (:name, :title, :body, :user_id, :button, :image_path, :aboutme, :birthdate, :hobbies, :college, :degree, :navbrand)';
            $stmt = $pdo->prepare($addLandingPageSql);
            $stmt->execute([
                ':name' => $name,
                ':title' => $title,
                ':body' => $body,
                ':user_id' => $user_id,
                ':button' => $button,
                ':image_path' => $uploadedFilePath,
                ':aboutme' => $aboutme,
                ':birthdate' => $birthdate,
                ':hobbies' => $hobbies,
                ':college' => $college,
                ':degree' => $degree,
            ]);

          
            $insertSocialSql = '
                INSERT INTO social (fb, github, linked, email, tg, user_id) 
                VALUES (:fb, :github, :linked, :email, :tg, :user_id)';
            $stmt = $pdo->prepare($insertSocialSql);
            $stmt->execute([
                ':fb' => $fb,
                ':github' => $github,
                ':linked' => $linked,
                ':email' => $email,
                ':tg' => $tg,
                ':user_id' => $user_id,
            ]);

            $_SESSION['success_message'] = 'Portfolio info added successfully!';
        }

        header('Location: main.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit | <?= isset($portfolio['navbrand']) ? htmlspecialchars($portfolio['navbrand']) : '' ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        .container {
            background-color: #222;
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            margin-top: 20px;
            margin-bottom: 20px;
        }

        h1 {
            text-align: center;
            color: #fff;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            padding: 10px;
            border: 1px solid #444;
            background-color: #333;
            color: #fff;
            border-radius: 5px;
            font-size: 16px;
        }

        .image-preview {
            margin-top: 10px;
            text-align: center;
        }

        .img-thumbnail {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .title-text {
            color: #aaa;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .section-title {
            margin-top: 20px;
            font-size: 18px;
            color: #fff;
        }

        .btn-black-primary{
            padding: 12px;
            background-color: #444;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            border: 2px solid white;
            color: white;
            background-color: transparent;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-black-primary:hover {
            background-color: white;
            color: black;
        }
        .back-button {
    padding: 12px ;
    font-size: 12px;
    font-weight: bold;
    color: #fff;
    background-color: #444;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
}

.back-button:hover {
    background-color: #555;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
}



        
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit your Info, <?= htmlspecialchars($username) ?>!</h1>
        
        
        <a href="main.php" class="btn btn-secondary mb-4 back-button">
    <i class="fas fa-arrow-left"></i> Back to Main
</a>


        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name" class="title-text">Your Name</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($portfolio['name'] ?? '') ?>" class="form-control" placeholder="Name" required />
            </div>

            <div class="form-group">
                <label for="title" class="title-text">Your Professional Title</label>
                <input type="text" name="title" id="title" value="<?= htmlspecialchars($portfolio['title'] ?? '') ?>" class="form-control" placeholder="Title" required />
            </div>

            <div class="form-group">
                <label for="body" class="title-text">Introduction to You</label>
                <textarea name="body" id="body" class="form-control" placeholder="Body" required><?= htmlspecialchars($portfolio['body'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="button" class="title-text">Link on the Button(CV)</label>
                <input type="text" name="button" id="button" value="<?= htmlspecialchars($portfolio['button'] ?? '') ?>" class="form-control" placeholder="Button Text" required />
            </div>
            <div class="form-group">
                <label for="navbrand" class="title-text">Navbrand</label>
                <input type="text" name="navbrand" id="navbrand" value="<?= htmlspecialchars($portfolio['navbrand'] ?? '') ?>" class="form-control" placeholder="Title" required />
            </div>

            <div class="form-group">
                <label for="image" class="title-text">Profile Image:</label>
                <input type="file" id="image" name="image" class="form-control-file" accept="image/*" onchange="previewImage(this)">
                <?php if (!empty($portfolio['image_path'])): ?>
                    <img src="<?= htmlspecialchars($portfolio['image_path']) ?>" alt="Profile Image" class="img-thumbnail">
                <?php endif; ?>
                <div class="image-preview"></div>
            </div>

            <div class="form-group">
                <label for="aboutme" class="title-text">About Me</label>
                <textarea name="aboutme" id="aboutme" class="form-control" placeholder="About Me"><?= htmlspecialchars($portfolio['aboutme'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="birthdate" class="title-text">Birthdate</label>
                <input type="date" name="birthdate" id="birthdate" value="<?= htmlspecialchars($portfolio['birthdate'] ?? '') ?>" class="form-control" />
            </div>

            <div class="form-group">
                <label for="hobbies" class="title-text">Your Hobbies</label>
                <input type="text" name="hobbies" id="hobbies" value="<?= htmlspecialchars($portfolio['hobbies'] ?? '') ?>" class="form-control" placeholder="Hobbies" />
            </div>

            <div class="form-group">
                <label for="college" class="title-text">Your College</label>
                <input type="text" name="college" id="college" value="<?= htmlspecialchars($portfolio['college'] ?? '') ?>" class="form-control" placeholder="College" />
            </div>

            <div class="form-group">
                <label for="degree" class="title-text">Your Degree</label>
                <input type="text" name="degree" id="degree" value="<?= htmlspecialchars($portfolio['degree'] ?? '') ?>" class="form-control" placeholder="Degree" />
            </div>

            <div class="section-title">Social Media Links</div>

            <div class="form-group">
                <label for="fb" class="title-text">Your Facebook Profile Link</label>
                <input type="text" name="fb" id="fb" value="<?= htmlspecialchars($portfolio['fb'] ?? '') ?>" class="form-control" placeholder="Facebook Profile Link">
            </div>

            <div class="form-group">
                <label for="github" class="title-text">Your GitHub Profile Link</label>
                <input type="text" name="github" id="github" value="<?= htmlspecialchars($portfolio['github'] ?? '') ?>" class="form-control" placeholder="GitHub Profile Link">
            </div>

            <div class="form-group">
                <label for="linked" class="title-text">Your LinkedIn Profile Link</label>
                <input type="text" name="linked" id="linked" value="<?= htmlspecialchars($portfolio['linked'] ?? '') ?>" class="form-control" placeholder="LinkedIn Profile Link">
            </div>

            <div class="form-group">
                <label for="email" class="title-text">Your Email Address</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($portfolio['email'] ?? '') ?>" class="form-control" placeholder="Email Address">
            </div>

            <div class="form-group">
                <label for="tg" class="title-text">Your Telegram Profile Link</label>
                <input type="text" name="tg" id="tg" value="<?= htmlspecialchars($portfolio['tg'] ?? '') ?>" class="form-control" placeholder="Telegram Profile Link">
            </div>

            <button type="submit" name="submit" class="btn btn-black-primary">Save</button>
        </form>
    </div>

    <!-- Include Bootstrap 4 JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function previewImage(input) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imagePreview = document.querySelector('.image-preview');
                imagePreview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    </script>
</body>
</html>

