<?php
include('../config/portfolioDB.php');
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    if (!isset($_SESSION['id'])) {
        echo "You are not logged in.";
        exit;
    }

    $user_id = $_SESSION['id'];

   
    if (isset($_POST['skills']) && is_array($_POST['skills'])) {
        $skills_to_delete = $_POST['skills'];

      
        $placeholders = implode(',', array_map(function($i) { return ":skill$i"; }, range(0, count($skills_to_delete) - 1)));

        $stmt = $pdo->prepare("DELETE FROM skills WHERE user_id = :user_id AND id IN ($placeholders)");

     
        $params = ['user_id' => $user_id];

       
        foreach ($skills_to_delete as $index => $skill_id) {
            $params["skill$index"] = $skill_id;
        }

     
        $stmt->execute($params);

       
        if ($stmt->rowCount() > 0) {
            $_SESSION['success_message'] = "Image deleted successfully!";
            header('Location: main.php');
            exit;
        } else {
            echo "No image were deleted.";
        }
    } else {
        $_SESSION['warning_message'] = "No image selected for deletion.";
        header('Location: main.php');
        exit;
    }
} else {
    
    echo "Invalid request.";
    exit;
}
?>
