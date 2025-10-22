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

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed)) {
            $_SESSION['flash_error'] = "Invalid image format. Allowed: JPG, PNG, GIF.";
            header("Location: public_profile.php?id=$user_id");
            exit;
        }

        $newName = uniqid('IMG_', true) . '.' . $ext;
        $uploadPath = __DIR__ . "/user-images/" . $newName;

        if (move_uploaded_file($fileTmp, $uploadPath)) {
            $query = $conn->prepare("UPDATE users SET img = ? WHERE id = ?");
            $query->execute([$newName, $user_id]);
            $_SESSION['flash_success'] = "🖼️ Profile image updated!";
        } else {
            $_SESSION['flash_error'] = "Failed to upload image.";
        }
    } else {
        $_SESSION['flash_error'] = "No image selected.";
    }
}

header("Location: public-profile.php?id=$user_id");
exit;
?>
