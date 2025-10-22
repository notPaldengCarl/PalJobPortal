<?php
require_once "../config/config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'Company') {
  header("Location: " . APPURL . "/auth/login.php");
  exit;
}

$company_id = $_SESSION['id'];
$job_id = isset($_GET['job_id']) ? (int) $_GET['job_id'] : 0;
$search = trim($_GET['search'] ?? '');

if ($job_id <= 0) {
  header("Location: " . APPURL . "/index.php");
  exit;
}

// ✅ Verify job belongs to company
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = :id AND company_id = :company_id LIMIT 1");
$stmt->execute([':id' => $job_id, ':company_id' => $company_id]);
$job = $stmt->fetch(PDO::FETCH_OBJ);
if (!$job) {
  header("Location: " . APPURL . "/404.php");
  exit;
}

// ✅ Delete applicant
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
  $delete_id = (int)$_GET['delete'];
  $stmt = $conn->prepare("DELETE FROM applications WHERE id = :id AND job_id = :job_id");
  $stmt->execute([':id' => $delete_id, ':job_id' => $job_id]);
  header("Location: job-applicants.php?job_id=$job_id&deleted=1");
  exit;
}

// ✅ Search applicants
$sql = "
  SELECT a.*, u.username, u.email, u.img, u.cv
  FROM applications a
  JOIN users u ON a.worker_id = u.id
  WHERE a.job_id = :job_id
";
$params = [':job_id' => $job_id];

if ($search !== '') {
  $sql .= " AND (u.username LIKE :search OR u.email LIKE :search)";
  $params[':search'] = "%$search%";
}
$sql .= " ORDER BY a.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$applicants = $stmt->fetchAll(PDO::FETCH_OBJ);

require_once "../partials/header.php";
?>

<section class="site-section">
  <div class="container">
    <h2>Applicants for: <span class="text-primary"><?php echo htmlspecialchars($job->job_title); ?></span></h2>

    <form method="get" class="form-inline mb-3">
      <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
      <input type="text" name="search" class="form-control mr-2" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>">
      <button type="submit" class="btn btn-secondary">Search</button>
    </form>

    <?php if (isset($_GET['deleted'])): ?>
      <div class="alert alert-success">Applicant deleted successfully.</div>
    <?php endif; ?>

    <?php if (count($applicants) === 0): ?>
      <div class="alert alert-info">No applicants found.</div>
    <?php else: ?>
      <?php foreach ($applicants as $a): ?>
        <div class="card mb-3 p-3 shadow-sm">
          <div class="d-flex align-items-center mb-2">
            <img src="<?php echo APPURL . '/users/user-images/' . ($a->img ?: 'default.png'); ?>"
                 style="width:60px;height:60px;border-radius:50%;object-fit:cover;margin-right:10px;">
            <div>
              <strong><?php echo htmlspecialchars($a->username); ?></strong><br>
              <small><?php echo htmlspecialchars($a->email); ?></small>
            </div>
          </div>

          <?php if (!empty($a->message)): ?>
            <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($a->message)); ?></p>
          <?php endif; ?>

          <?php if (!empty($a->cv)): ?>
            <a href="<?php echo APPURL . '/users/cv/' . htmlspecialchars($a->cv); ?>" 
               target="_blank" class="btn btn-sm btn-outline-primary">View CV</a>
          <?php else: ?>
            <span class="text-muted">No CV uploaded</span>
          <?php endif; ?>

          <a href="?job_id=<?php echo $job_id; ?>&delete=<?php echo $a->id; ?>" 
             class="btn btn-sm btn-outline-danger float-right"
             onclick="return confirm('Are you sure you want to delete this applicant?');">
             Delete
          </a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<?php require_once "../partials/footer.php"; ?>
