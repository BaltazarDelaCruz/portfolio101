<?php
include('../config/portfolioDB.php');
session_start();


if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'You are not logged in.']);
    exit;
}

$user_id = $_SESSION['id'];


if (isset($_FILES['image']) && is_array($_FILES['image']) && isset($_POST['image_name']) && is_array($_POST['image_name'])) {
    $images = $_FILES['image'];
    $image_names = $_POST['image_name'];

    if (count($images['name']) != count($image_names)) {
        echo json_encode(['success' => false, 'message' => 'Mismatch between number of images and names.']);
        exit;
    }

    $uploaded = [];
    $upload_dir_admin = '../admin/uploads/';
    $upload_dir_client = '../client/uploads/';

    if (!is_writable($upload_dir_admin) || !is_writable($upload_dir_client)) {
        echo json_encode(['success' => false, 'message' => 'One or more upload directories are not writable.']);
        exit;
    }

    for ($i = 0; $i < count($images['name']); $i++) {
        $image = $images['name'][$i];
        $image_tmp = $images['tmp_name'][$i];
        $image_name = filter_var($image_names[$i], FILTER_SANITIZE_STRING);

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($images['type'][$i], $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type for image: ' . $image_name]);
            exit;
        }

        $unique_name = bin2hex(random_bytes(16)) . '.' . pathinfo($image, PATHINFO_EXTENSION);
        $upload_path_admin = $upload_dir_admin . $unique_name;
        $upload_path_client = $upload_dir_client . $unique_name;

        if (move_uploaded_file($image_tmp, $upload_path_admin)) {
            // Optionally, copy to client directory
            if (!copy($upload_path_admin, $upload_path_client)) {
                echo json_encode(['success' => false, 'message' => 'Error copying file to client directory for image: ' . $image_name]);
                exit;
            }

            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO skills (user_id, image_skills, skill_name) VALUES (:user_id, :image_skills, :skill_name)");
            $stmt->execute(['user_id' => $user_id, 'image_skills' => $upload_path_admin, 'skill_name' => $image_name]);

            $uploaded[] = $unique_name;
        } else {
            echo json_encode(['success' => false, 'message' => 'Error moving file for image: ' . $image_name]);
            exit;
        }
    }

    $_SESSION['success_message'] = 'Images uploaded successfully!';
    header('Location: main.php');
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'No images uploaded or upload error.']);
}

?>
