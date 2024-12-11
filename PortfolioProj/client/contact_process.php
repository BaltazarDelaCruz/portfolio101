<?php
session_start();
include('../config/portfolioDB.php');

$public_id = isset($_GET['portfolio']) ? $_GET['portfolio'] : '';

// Fetch the portfolio information based on public_id
$stmt = $pdo->prepare('
    SELECT id AS portfolio_id, public_id FROM users WHERE public_id = :public_id
');
$stmt->bindParam(':public_id', $public_id, PDO::PARAM_STR);
$stmt->execute();
$portfolio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$portfolio) {
    echo "Portfolio not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
    // Sanitize input fields
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['message'] = 'All fields are required.';
        header("Location: contact.php?portfolio=" . urlencode($portfolio['public_id']));
        exit;
    }

    // Insert the message into the database
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message, user_id) VALUES (:name, :email, :message, :user_id)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':user_id', $portfolio['portfolio_id']); 
    $stmt->execute();

    // Set the success message and redirect
    $_SESSION['message'] = 'Your message has been sent successfully!';
    header("Location: contact.php?portfolio=" . urlencode($portfolio['public_id']));
    exit;
}
?>

