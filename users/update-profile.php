<?php
require "../config/config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    if ($_SESSION['id'] != $user_id) {
        $_SESSION['flash_error'] = "Unauthorized action.";
        header("Location: public-profile.php?id=$user_id");
        exit;
    }

    $username = trim($_POST['username']);
    $title = isset($_POST['title']) ? trim($_POST['title']) : null;
    $bio = isset($_POST['bio']) ? trim($_POST['bio']) : null;
    $facebook = trim($_POST['facebook']);
    $twitter = trim($_POST['twitter']);
    $linkedin = trim($_POST['linkedin']);

    try {
        $query = $conn->prepare("
            UPDATE users 
            SET username = ?, title = ?, bio = ?, facebook = ?, twitter = ?, linkedin = ?
            WHERE id = ?
        ");
        $query->execute([$username, $title, $bio, $facebook, $twitter, $linkedin, $user_id]);

        $_SESSION['flash_success'] = "✅ Profile updated successfully.";
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "⚠️ Something went wrong: " . $e->getMessage();
    }
}

header("Location: public-profile.php?id=$user_id");
exit;
?>
