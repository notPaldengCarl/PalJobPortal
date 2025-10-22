<?php
require_once __DIR__ . '/../config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id'], $_SESSION['type']) || $_SESSION['type'] !== 'Company') {
    header('Location: ' . APPURL . '/auth/login.php');
    exit;
}

$job_id = 0;
if (!empty($_POST['job_id'])) $job_id = (int) $_POST['job_id'];
elseif (!empty($_REQUEST['id'])) $job_id = (int) $_REQUEST['id'];

if ($job_id <= 0) {
    $_SESSION['flash_error'] = 'Missing job id.';
    header('Location: ' . APPURL . '/jobs');
    exit;
}

$stmt = $conn->prepare("SELECT id, company_id, job_image FROM jobs WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    $_SESSION['flash_error'] = 'Job not found.';
    header('Location: ' . APPURL . '/jobs');
    exit;
}

$company_id = (int)$job['company_id'];
if ($company_id !== (int)$_SESSION['id']) {
    $_SESSION['flash_error'] = 'You are not allowed to delete this job.';
    header('Location: ' . APPURL . '/users/public-profile.php?id=' . $company_id);
    exit;
}

$del = $conn->prepare("DELETE FROM jobs WHERE id = :id");
$del->execute([':id' => $job_id]);

if (!empty($job['job_image'])) {
    $file = __DIR__ . '/job-images/' . basename($job['job_image']);
    if (is_file($file)) @unlink($file);
}

$_SESSION['flash_success'] = 'Job deleted.';
header('Location: ' . APPURL . '/users/public-profile.php?id=' . $company_id);
exit;
?>
