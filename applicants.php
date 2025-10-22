<?php
require_once __DIR__ . '/../config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'Company') {
  header("Location: " . APPURL . "/auth/login.php");
  exit;
}

$company_id = (int)$_SESSION['id'];

// fetch all applications for company's jobs
$query = "
  SELECT 
    a.id AS application_id,
    a.message,
    a.resume_file,
    a.created_at,
    j.job_title,
    u.username AS worker_name,
    u.email AS worker_email
  FROM applications a
  JOIN jobs j ON a.job_id = j.id
  JOIN users u ON a.worker_id = u.id
  WHERE j.company_id = :company_id
  ORDER BY a.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->execute([':company_id' => $company_id]);
$applications = $stmt->fetchAll(PDO::FETCH_OBJ);

require_once __DIR__ . '/../partials/header.php';
?>

<section class="site-section">
  <div class="container">
    <h2 class="mb-4">Applicants</h2>

    <?php if (empty($applications)): ?>
      <div class="alert alert-info">No one has applied yet.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="thead-light">
            <tr>
              <th>Job Title</th>
              <th>Applicant</th>
              <th>Message</th>
              <th>Resume</th>
              <th>Applied On</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($applications as $app): ?>
              <tr>
                <td><?php echo htmlspecialchars($app->job_title, ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                  <?php echo htmlspecialchars($app->worker_name, ENT_QUOTES, 'UTF-8'); ?><br>
                  <small class="text-muted"><?php echo htmlspecialchars($app->worker_email, ENT_QUOTES, 'UTF-8'); ?></small>
                </td>
                <td><?php echo nl2br(htmlspecialchars($app->message, ENT_QUOTES, 'UTF-8')); ?></td>
                <td>
                  <?php if ($app->resume_file): ?>
                    <a href="<?php echo APPURL . '/uploads/applications/' . $app->resume_file; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                      View Resume
                    </a>
                  <?php else: ?>
                    <span class="text-muted">No File</span>
                  <?php endif; ?>
                </td>
                <td><?php echo date('M d, Y', strtotime($app->created_at)); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
