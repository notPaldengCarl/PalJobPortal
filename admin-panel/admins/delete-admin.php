<?php
require "../../config/config.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!defined('ADMINURL')) {
  define('ADMINURL', 'http://localhost/jobboard/admin-panel');
}

if (!isset($_SESSION['adminname'])) {
  header("Location: " . ADMINURL . "/admins/login-admins.php");
  exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
  $_SESSION['flash_error'] = 'Invalid request.';
  header("Location: " . ADMINURL . "/admins/admins.php");
  exit;
}


try {
  $me = $conn->prepare("SELECT id FROM admins WHERE email = :email LIMIT 1");
  $me->execute([':email'=>$_SESSION['email']]);
  $self = $me->fetch(PDO::FETCH_ASSOC);
  if ($self && (int)$self['id'] === $id) {
    $_SESSION['flash_error'] = 'You cannot delete your own account.';
    header("Location: " . ADMINURL . "/admins/admins.php"); exit;
  }
} catch (Throwable $e) {}

try {
  $stmt = $conn->prepare("DELETE FROM admins WHERE id = :id");
  $stmt->execute([':id'=>$id]);

  if ($stmt->rowCount() > 0) {
    $_SESSION['flash_success'] = 'Admin deleted successfully.';
  } else {
    $_SESSION['flash_error'] = 'Admin not found or already deleted.';
  }
} catch (Throwable $e) {
  $_SESSION['flash_error'] = 'Could not delete admin.';
}

header("Location: " . ADMINURL . "/admins/admins.php");
exit;
