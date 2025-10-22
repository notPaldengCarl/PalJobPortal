<?php
require_once "../config/config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'Company') {
  header("Location: " . APPURL . "/auth/login.php");
  exit;
}

if (isset($_POST['action'], $_POST['application_id'])) {
  $app_id = (int) $_POST['application_id'];
  $action = $_POST['action'];

  if ($action === 'delete') {
    $stmt = $conn->prepare("DELETE FROM applications WHERE id = :id");
    $stmt->execute([':id' => $app_id]);
  } elseif (in_array($action, ['Shortlisted', 'Rejected', 'Pending'])) {
    $stmt = $conn->prepare("UPDATE applications SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $action, ':id' => $app_id]);
  }

  header("Location: " . $_SERVER['HTTP_REFERER']);
  exit;
}
?>
