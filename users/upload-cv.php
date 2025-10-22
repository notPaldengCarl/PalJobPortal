<?php
require "../config/config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    if ($_SESSION['id'] != $user_id) {
        $_SESSION['flash_error'] = "Unauthorized action.";
        header("Location: public_profile.php?id=$user_id");
        exit;
    }

    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['cv']['tmp_name'];
        $fileName = basename($_FILES['cv']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['pdf', 'doc', 'docx'];

        if (!in_array($ext, $allowed)) {
            $_SESSION['flash_error'] = "Invalid CV format. Allowed: PDF, DOC, DOCX.";
            header("Location: public-profile.php?id=$user_id");
            exit;
        }

        $newName = uniqid('CV_', true) . '.' . $ext;
        $uploadPath = __DIR__ . "/cv/" . $newName;

        if (move_uploaded_file($fileTmp, $uploadPath)) {
            $query = $conn->prepare("UPDATE users SET cv = ? WHERE id = ?");
            $query->execute([$newName, $user_id]);
            $_SESSION['flash_success'] = "📄 CV uploaded successfully.";
        } else {
            $_SESSION['flash_error'] = "Failed to upload CV.";
        }
    } else {
        $_SESSION['flash_error'] = "No file selected.";
    }
}

header("Location: public-profile.php?id=$user_id");
exit;
?>
