<?php
require "../../config/config.php";

// Make sure ADMINURL is defined
if (!defined('ADMINURL')) {
    define("ADMINURL", "http://localhost/paljob/admin-panel");
}

// Check if admin is logged in
if (!isset($_SESSION['adminname'])) {
    header("Location: " . ADMINURL . "/admins/login-admins.php");
    exit;
}

// Check if id and status are set
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    // Toggle job status safely
    $newStatus = ($status == 1) ? 0 : 1;

    $update = $conn->prepare("UPDATE jobs SET status = :status WHERE id = :id");
    $update->execute([
        ':status' => $newStatus,
        ':id' => $id
    ]);

    // Redirect back to job list
    header("Location: " . ADMINURL . "/jobs-admins/show-jobs.php");
    exit;

} else {
    // Invalid or missing parameters
    header("Location: " . APPURL . "/404.php");
    exit;
}
?>
