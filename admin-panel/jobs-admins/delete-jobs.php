<?php
require "../../config/config.php";

// Define ADMINURL if not already defined
if (!defined('ADMINURL')) {
    define("ADMINURL", "http://localhost/paljob/admin-panel");
}
if (!defined('APPURL')) {
    define("APPURL", "http://localhost/paljob");
}

// Check if admin is logged in
if (!isset($_SESSION['adminname'])) {
    header("Location: " . ADMINURL . "/admins/login-admins.php");
    exit;
}

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete job safely using a prepared statement
    $delete = $conn->prepare("DELETE FROM jobs WHERE id = :id");
    $delete->execute([':id' => $id]);

    // Redirect back to job list
    header("Location: " . ADMINURL . "/jobs-admins/show-jobs.php");
    exit;
} else {
    // Invalid or missing ID — go to 404
    header("Location: " . APPURL . "/404.php");
    exit;
}
?>
