<?php
require_once __DIR__ . '/../config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!file_exists(__DIR__ . '/../partials/header.php')) {
  die('Header file not found: ' . __DIR__ . '/../partials/header.php');
}

// --- Fetch job by id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("
  SELECT j.*, u.username AS company_username, u.img AS company_image_profile, u.email AS company_email
  FROM jobs j
  LEFT JOIN users u ON j.company_id = u.id
  WHERE j.id = :id
  LIMIT 1
");
$stmt->execute([':id' => $id]);
$job = $stmt->fetch(PDO::FETCH_OBJ);
if (!$job) {
  header("Location: " . APPURL . "/404.php");
  exit;
}

$canEdit = isset($_SESSION['type'], $_SESSION['id']) && $_SESSION['type'] === 'Company' && (int)$_SESSION['id'] === (int)$job->company_id;

// --- Messages to show to user
$successMessage = '';
$errorMessage = '';
$cvViewLink = ''; // show a button link when CV is uploaded or exists

// --- Handle form submit (apply + upload) on this same page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
  // user must be Worker
  if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'Worker') {
    $errorMessage = 'You need to be logged in as a Worker to apply.';
  } else {
    $job_id = (int)$_POST['job_id'];
    $worker_id = (int)$_SESSION['id'];
    $message = trim($_POST['message'] ?? '');

    // Prevent duplicate application
    $check = $conn->prepare("SELECT id FROM applications WHERE job_id = :job_id AND worker_id = :worker_id LIMIT 1");
    $check->execute([':job_id' => $job_id, ':worker_id' => $worker_id]);
    if ($check->fetch()) {
      $errorMessage = 'You have already applied to this job.';
    } else {
      // handle cv resume upload (optional but required by your form)
      $cvFileName = null;
      if (isset($_FILES['resume']) && $_FILES['resume']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
          $errorMessage = 'Error during file upload. Code: ' . (int)$_FILES['resume']['error'];
        } else {
          // validate extension and size
          $allowed = ['pdf', 'doc', 'docx'];
          $fileName = $_FILES['resume']['name'];
          $fileTmp  = $_FILES['resume']['tmp_name'];
          $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
          $maxBytes = 5 * 1024 * 1024; // 5 MB

          if (!in_array($fileExt, $allowed)) {
            $errorMessage = 'Only PDF, DOC and DOCX files are allowed.';
          } elseif ($_FILES['resume']['size'] > $maxBytes) {
            $errorMessage = 'File too large. Maximum 5MB allowed.';
          } else {
            // create folder if not exists
            $uploadDir = dirname(__DIR__) . '/users/cv/';
            if (!is_dir($uploadDir)) {
              if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                $errorMessage = 'Failed to create CV upload directory.';
              }
            }

            if (empty($errorMessage)) {
              // generate unique filename
              $cvFileName = uniqid('cv_') . '.' . $fileExt;
              $destination = $uploadDir . $cvFileName;

              if (!move_uploaded_file($fileTmp, $destination)) {
                $errorMessage = 'Failed to move uploaded file.';
              } else {
                // update user's cv column so profile has latest
                $update = $conn->prepare("UPDATE users SET cv = :cv WHERE id = :id");
                $update->execute([':cv' => $cvFileName, ':id' => $worker_id]);
              }
            }
          }
        }
      } else {
        // If no file uploaded, attempt to use existing user's cv (if any)
        $u = $conn->prepare("SELECT cv FROM users WHERE id = :id LIMIT 1");
        $u->execute([':id' => $worker_id]);
        $uobj = $u->fetch(PDO::FETCH_OBJ);
        $cvFileName = $uobj && !empty($uobj->cv) ? $uobj->cv : null;
        // note: if your form requires a file, this branch won't be used; keep for safety
      }

      // if there were no errors so far, insert application
      if ($errorMessage === '') {
        $insert = $conn->prepare("
          INSERT INTO applications (job_id, worker_id, message, cv, created_at)
          VALUES (:job_id, :worker_id, :message, :cv, NOW())
        ");
        $insert->execute([
          ':job_id' => $job_id,
          ':worker_id' => $worker_id,
          ':message' => $message,
          ':cv' => $cvFileName
        ]);

        $successMessage = '✅ Application submitted successfully!';
        if ($cvFileName) {
          $cvViewLink = APPURL . '/users/cv/' . rawurlencode($cvFileName);
        }
      }
    }
  }
}

// --- After processing, read current worker CV (for display in the form), if logged in as worker
$currentWorkerCV = null;
if (isset($_SESSION['id']) && $_SESSION['type'] === 'Worker') {
  $s = $conn->prepare("SELECT cv FROM users WHERE id = :id LIMIT 1");
  $s->execute([':id' => (int)$_SESSION['id']]);
  $row = $s->fetch(PDO::FETCH_OBJ);
  $currentWorkerCV = $row ? $row->cv : null;
}

require_once __DIR__ . '/../partials/header.php';
?>

<style>
/* small helper for sticky sidebar (desktop only) */
@media (min-width: 992px) {
  .sidebar {
    position: sticky;
    top: 100px; /* adjust based on your navbar height */
  }
}
.sidebar .card {
  border: 1px solid #e9ecef;
  border-radius: 6px;
}
.badge-custom-padding { padding: .45rem .8rem; }
.alert-autohide { transition: opacity .4s ease; }
</style>

<section class="site-section">
  <div class="container">

    <!-- Success / Error Alerts -->
    <?php if (!empty($successMessage)): ?>
      <div id="appAlert" class="alert alert-success alert-dismissible fade show alert-autohide" role="alert">
        <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
        <?php if (!empty($cvViewLink)): ?>
          <a href="<?php echo htmlspecialchars($cvViewLink, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="btn btn-sm btn-outline-light ml-3">View CV</a>
        <?php endif; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="document.getElementById('appAlert').style.display='none'">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php elseif (!empty($errorMessage)): ?>
      <div id="appAlert" class="alert alert-danger alert-dismissible fade show alert-autohide" role="alert">
        <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="document.getElementById('appAlert').style.display='none'">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif; ?>

    <div class="row mb-4">
      <!-- MAIN COLUMN -->
      <div class="col-md-8">
        <h2><?php echo htmlspecialchars($job->job_title ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
       <p class="text-muted mb-2">
  <?php echo htmlspecialchars($job->job_region ?? '', ENT_QUOTES, 'UTF-8'); ?>
</p>

<div class="job-listing-meta mb-3">
  <?php
    $jobType = trim($job->job_type ?? '');
    $badgeColor = 'secondary';
    if (strcasecmp($jobType, 'Part Time') === 0) $badgeColor = 'danger';
    elseif (strcasecmp($jobType, 'Full Time') === 0) $badgeColor = 'danger';
    elseif (strcasecmp($jobType, 'Freelance') === 0) $badgeColor = 'danger';
  ?>
  <?php if (!empty($jobType)): ?>
    <span class="badge badge-<?php echo $badgeColor; ?>">
      <?php echo htmlspecialchars($jobType, ENT_QUOTES, 'UTF-8'); ?>
    </span>
  <?php endif; ?>
</div>


        <!-- badges / summary -->
        <div class="mb-4 d-flex flex-wrap align-items-center" style="gap:8px;overflow-wrap:break-word;word-break:break-word;white-space:normal;">
          <?php
            $type = trim((string)($job->job_type ?? ''));
            $badge = 'secondary';
            if (stripos($type, 'part') !== false) $badge = 'danger';
            elseif (stripos($type, 'full') !== false) $badge = 'success';
            elseif (stripos($type, 'free') !== false) $badge = 'info';

            $vacancy = (int)($job->vacancy ?? 0);
            $category = htmlspecialchars($job->job_category ?? '', ENT_QUOTES, 'UTF-8');
            $experience = htmlspecialchars($job->experience ?? '', ENT_QUOTES, 'UTF-8');
            $salary = htmlspecialchars($job->salary ?? '', ENT_QUOTES, 'UTF-8');
            $gender = htmlspecialchars($job->gender ?? '', ENT_QUOTES, 'UTF-8');
            $deadline = htmlspecialchars($job->application_deadline ?? '', ENT_QUOTES, 'UTF-8');
          ?>

          <?php if ($vacancy): ?><span class="badge badge-primary badge-custom-padding">Vacancy: <?php echo $vacancy; ?></span><?php endif; ?>
          <?php if ($category): ?><span class="badge badge-dark badge-custom-padding"><?php echo $category; ?></span><?php endif; ?>
          <?php if ($experience): ?><span class="badge badge-warning badge-custom-padding">Experience: <?php echo $experience; ?></span><?php endif; ?>
          <?php if ($salary): ?><span class="badge badge-info badge-custom-padding">Salary: <?php echo $salary; ?></span><?php endif; ?>
          <?php if ($gender): ?><span class="badge badge-light text-dark badge-custom-padding">Gender: <?php echo $gender; ?></span><?php endif; ?>
          <?php if ($deadline): ?><span class="small text-muted ml-2">Apply by: <?php echo $deadline; ?></span><?php endif; ?>
        </div>

        <!-- show job image if present, else company image, else default -->
        <?php
          $imgPath = APPURL . '/users/user-images/default.png';
          if (!empty($job->job_image)) {
            $imgPath = APPURL . '/jobs/job-images/' . $job->job_image;
          } elseif (!empty($job->company_image_profile)) {
            $imgPath = APPURL . '/users/user-images/' . $job->company_image_profile;
          }
        ?>
        <div class="mb-4">
          <img src="<?php echo htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8'); ?>" 
               alt="Job image" 
               style="width:100%;max-height:1000px;object-fit:cover;border-radius:6px;">
        </div>

        <!-- Job Description & Details (keeps spacing like your screenshot) -->
        <div class="job-details" style="line-height:1.7; word-break:break-word;">
          <h4 class="mt-4 mb-3" style="font-weight:600;">Job Description</h4>
          <div class="mb-5" style="white-space:pre-line;">
            <?php echo htmlspecialchars($job->job_description ?? '', ENT_QUOTES, 'UTF-8'); ?>
          </div>

          <?php if (!empty($job->responsibilities)): ?>
            <h5 class="mt-4 mb-3" style="font-weight:600;">Responsibilities</h5>
            <div class="mb-5" style="white-space:pre-line;">
              <?php echo htmlspecialchars($job->responsibilities, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($job->education_experience)): ?>
            <h5 class="mt-4 mb-3" style="font-weight:600;">Education & Experience</h5>
            <div class="mb-5" style="white-space:pre-line;">
              <?php echo htmlspecialchars($job->education_experience, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($job->other_benifits)): ?>
            <h5 class="mt-4 mb-3" style="font-weight:600;">Other Benefits</h5>
            <div class="mb-5" style="white-space:pre-line;">
              <?php echo htmlspecialchars($job->other_benifits, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>
        </div>
      </div> <!-- /.col-md-8 -->

      <!-- SIDEBAR COLUMN (kept on right) -->
      <div class="col-md-4">
        <aside class="sidebar">
          <!-- Company Card -->
          <div class="card p-3 mb-4 shadow-sm">
            <h5 class="mb-3 border-bottom pb-2">Company</h5>
            <div class="d-flex align-items-center">
              <img 
                src="<?php echo APPURL . '/users/user-images/' . ($job->company_image_profile ?: 'default.png'); ?>" 
                alt="Company Logo"
                style="width:56px;height:56px;object-fit:cover;border-radius:50%;margin-right:12px;">
              <div>
                <strong><?php echo htmlspecialchars($job->company_username ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></strong><br>
                <small class="text-muted"><?php echo htmlspecialchars($job->company_email ?? '', ENT_QUOTES, 'UTF-8'); ?></small>
                <a href="<?php echo APPURL; ?>/jobs/job-applicants.php?job_id=<?php echo $job->id; ?>" 
                   class="btn btn-outline-primary btn-sm mt-2">View Applicants</a>
              </div>
            </div>
          </div>

          <!-- Apply Section -->
          <?php if (isset($_SESSION['id']) && $_SESSION['type'] === 'Worker'): ?>
            <div class="card p-3 shadow-sm">
              <h5 class="mb-3 border-bottom pb-2">Apply for this Job</h5>

              <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="job_id" value="<?php echo (int)$job->id; ?>">
                <div class="form-group mb-3">
                  <label for="message">Message (optional)</label>
                  <textarea id="message" name="message" class="form-control" rows="4" placeholder="Introduce yourself..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                </div>

               <div class="form-group mb-3">
  <label class="d-block mb-2">CV / Resume Option</label>

  <?php if (!empty($currentWorkerCV)): ?>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="cv_option" id="use_existing_cv" value="existing" checked>
      <label class="form-check-label" for="use_existing_cv">
        Use existing CV 
        <a href="<?php echo APPURL . '/users/cv/' . rawurlencode($currentWorkerCV); ?>" target="_blank" class="ml-1">[View Current CV]</a>
      </label>
    </div>

    <div class="form-check mt-2">
      <input class="form-check-input" type="radio" name="cv_option" id="upload_new_cv" value="new">
      <label class="form-check-label" for="upload_new_cv">Upload a new CV</label>
    </div>

    <div id="new_cv_upload" class="mt-3" style="display:none;">
      <input type="file" name="resume" id="resume" class="form-control-file" accept=".pdf,.doc,.docx">
      <small class="form-text text-muted">Max 5MB. Allowed: pdf, doc, docx.</small>
    </div>
  <?php else: ?>
    <!-- If user has no CV yet -->
    <input type="file" name="resume" id="resume" class="form-control-file" accept=".pdf,.doc,.docx" required>
    <small class="form-text text-muted">Max 5MB. Allowed: pdf, doc, docx.</small>
  <?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const useExisting = document.getElementById('use_existing_cv');
  const uploadNew = document.getElementById('upload_new_cv');
  const uploadDiv = document.getElementById('new_cv_upload');

  if (uploadNew) {
    uploadNew.addEventListener('change', () => {
      uploadDiv.style.display = 'block';
    });
  }
  if (useExisting) {
    useExisting.addEventListener('change', () => {
      uploadDiv.style.display = 'none';
    });
  }
});
</script>


                <button type="submit" name="apply" class="btn btn-primary btn-block">Submit Application</button>
              </form>
            </div>
          <?php else: ?>
            <div class="card p-3 shadow-sm">
              <p class="mb-0 text-muted">
                Please <a href="<?php echo APPURL; ?>/auth/login.php" class="text-primary">login</a> as a Worker to apply.
              </p>
            </div>
          <?php endif; ?>
        </aside>
      </div> <!-- /.col-md-4 -->
    </div> <!-- /.row -->

    <?php if ($canEdit): ?>
      <div class="mt-3 mb-4">
        <a href="<?php echo APPURL; ?>/jobs/job-update.php?id=<?php echo (int)$job->id; ?>" class="btn btn-outline-secondary mr-2">Edit Job</a>
        <form id="deleteJobForm" action="<?php echo APPURL; ?>/jobs/job-delete.php" method="post" class="d-inline">
          <input type="hidden" name="job_id" value="<?php echo (int)$job->id; ?>">
          <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this job?');">Delete Job</button>
        </form>
      </div>
    <?php endif; ?>
  </div> <!-- /.container -->
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<script>
// auto-hide alert after 6 seconds
setTimeout(function() {
  var el = document.getElementById('appAlert');
  if (el) {
    el.style.opacity = '0';
    setTimeout(function(){ if (el.parentNode) el.parentNode.removeChild(el); }, 400);
  }
}, 6000);
</script>
