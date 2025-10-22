<?php
require_once "../config/config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ Get job ID
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
  header("Location: " . APPURL . "/index.php");
  exit;
}

// ✅ Fetch job
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$job = $stmt->fetch(PDO::FETCH_OBJ);

if (!$job) {
  header("Location: " . APPURL . "/404.php");
  exit;
}

// ✅ Determine image path
$imgPath = APPURL . '/users/user-images/default.png';
if (!empty($job->job_image)) {
  $imgPath = APPURL . '/jobs/job-images/' . htmlspecialchars($job->job_image, ENT_QUOTES, 'UTF-8');
} elseif (!empty($job->company_image)) {
  $imgPath = APPURL . '/users/user-images/' . htmlspecialchars($job->company_image, ENT_QUOTES, 'UTF-8');
}

require_once "../partials/header.php";
?>

<section class="site-section">
  <div class="container">

    <!-- ✅ Applied success message -->
    <?php if (isset($_GET['applied']) && $_GET['applied'] == 1): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        ✅ You have successfully applied for this job!
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif; ?>

    <div class="row">
      <!-- LEFT: Job Info -->
      <div class="col-md-8 mb-5">

        <h2 class="mb-2"><?php echo htmlspecialchars($job->job_title, ENT_QUOTES, 'UTF-8'); ?></h2>
        <p class="text-muted mb-3">
          <?php echo htmlspecialchars($job->job_region, ENT_QUOTES, 'UTF-8'); ?> •
          <?php echo htmlspecialchars($job->job_type, ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <!-- Job image -->
        <div class="mb-4">
          <img src="<?php echo $imgPath; ?>" alt="Job image"
               style="width:100%;max-height:360px;object-fit:cover;border-radius:6px;">
        </div>

        <!-- Job Description -->
        <h4 class="mb-3">Job Description</h4>
        <p style="white-space: pre-line;"><?php echo htmlspecialchars($job->job_description ?? '', ENT_QUOTES, 'UTF-8'); ?></p>

        <!-- Responsibilities -->
        <?php if (!empty($job->responsibilities)): ?>
          <h5 class="mt-4">Responsibilities</h5>
          <p style="white-space: pre-line;"><?php echo htmlspecialchars($job->responsibilities, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <!-- Education & Experience -->
        <?php if (!empty($job->education_experience)): ?>
          <h5 class="mt-4">Education & Experience</h5>
          <p style="white-space: pre-line;"><?php echo htmlspecialchars($job->education_experience, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <!-- Other Benefits -->
        <?php if (!empty($job->other_benifits)): ?>
          <h5 class="mt-4">Other Benefits</h5>
          <p style="white-space: pre-line;"><?php echo htmlspecialchars($job->other_benifits, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

      </div>

      <!-- RIGHT: Company & Apply -->
      <div class="col-md-4">

        <!-- Company Card -->
        <div class="card p-3 mb-4 shadow-sm">
          <h5 class="border-bottom pb-2 mb-3">Company</h5>
          <div class="d-flex align-items-center">
            <img 
              src="<?php echo APPURL . '/users/user-images/' . ($job->company_image ?: 'default.png'); ?>"
              alt="Company Logo"
              style="width:56px;height:56px;object-fit:cover;border-radius:50%;margin-right:12px;">
            <div>
              <strong><?php echo htmlspecialchars($job->company_name ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></strong><br>
              <small class="text-muted"><?php echo htmlspecialchars($job->company_email ?? '', ENT_QUOTES, 'UTF-8'); ?></small>
            </div>
          </div>
        </div>

        <!-- Apply Box -->
        <?php if (isset($_SESSION['id']) && isset($_SESSION['type']) && $_SESSION['type'] === 'Worker'): ?>
          <div class="card p-3 shadow-sm">
            <h5 class="border-bottom pb-2 mb-3">Apply for this Job</h5>
            <form action="<?php echo APPURL; ?>/jobs/apply.php" method="post">
              <input type="hidden" name="job_id" value="<?php echo (int)$job->id; ?>">
              <div class="form-group">
                <label for="message">Message (optional)</label>
                <textarea id="message" name="message" class="form-control" rows="4" placeholder="Introduce yourself..."></textarea>
              </div>
              <button type="submit" name="apply" class="btn btn-primary btn-block">Submit Application</button>
            </form>
          </div>
        <?php else: ?>
          <div class="card p-3 shadow-sm">
            <p class="mb-0 text-muted">Please <a href="<?php echo APPURL; ?>/auth/login.php">login</a> as a Worker to apply.</p>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</section>

<?php require_once "../partials/footer.php"; ?>
