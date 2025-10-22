<?php
require_once "../config/config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ Only workers can apply
if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'Worker') {
  header("Location: " . APPURL . "/auth/login.php");
  exit;
}

if (isset($_POST['apply'])) {
  $job_id = (int) $_POST['job_id'];
  $worker_id = $_SESSION['id'];
  $message = trim($_POST['message'] ?? '');

  // ✅ Prevent duplicate applications
  $stmt = $conn->prepare("SELECT id FROM applications WHERE job_id = :job_id AND worker_id = :worker_id");
  $stmt->execute([':job_id' => $job_id, ':worker_id' => $worker_id]);
  if ($stmt->fetch()) {
    header("Location: " . APPURL . "/jobs/job-single.php?id=$job_id&applied=1");
    exit;
  }

  // ✅ Handle file upload (CV/Resume)
  $cvFileName = null;
  if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
    $fileTmp  = $_FILES['resume']['tmp_name'];
    $fileName = $_FILES['resume']['name'];
    $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowed = ['pdf', 'doc', 'docx'];
    if (in_array($fileExt, $allowed)) {
      $cvFileName = uniqid('cv_') . '.' . $fileExt;
      $uploadDir = dirname(__DIR__) . "/users/cv/";

      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
      }

      if (move_uploaded_file($fileTmp, $uploadDir . $cvFileName)) {
        // ✅ Update worker's profile CV
        $stmt = $conn->prepare("UPDATE users SET cv = :cv WHERE id = :id");
        $stmt->execute([':cv' => $cvFileName, ':id' => $worker_id]);
      } else {
        $cvFileName = null;
      }
    }
  }

  // ✅ Insert application (with CV)
  $stmt = $conn->prepare("
    INSERT INTO applications (job_id, worker_id, message, cv, created_at)
    VALUES (:job_id, :worker_id, :message, :cv, NOW())
  ");
  $stmt->execute([
    ':job_id' => $job_id,
    ':worker_id' => $worker_id,
    ':message' => $message,
    ':cv' => $cvFileName
  ]);

  header("Location: " . APPURL . "/jobs/job-single.php?id=$job_id&applied=1");
  exit;
}

// Redirect if accessed directly
header("Location: " . APPURL . "/index.php");
exit;
