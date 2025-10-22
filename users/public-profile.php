<?php require "../partials/header.php"; ?>
<?php require "../config/config.php"; ?>

<?php 
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $select = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $select->execute([$id]);
    $profile = $select->fetch(PDO::FETCH_OBJ);

    $jobs = $conn->prepare("SELECT * FROM jobs WHERE company_id = ? AND status = 1 LIMIT 5");
    $jobs->execute([$id]);
    $moreJobs = $jobs->fetchAll(PDO::FETCH_OBJ);
} else {
    echo "404";
    exit;
}
?>

<!-- HERO -->
<section class="section-hero overlay inner-page bg-image" style="background-image: url('<?php echo APPURL; ?>/images/4k.jpg');" id="home-section">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <h1 class="text-white font-weight-bold"><?php echo htmlspecialchars($profile->username); ?></h1>
        <div class="custom-breadcrumbs">
          <a href="<?php echo APPURL; ?>">Home</a> <span class="mx-2 slash">/</span>
          <span class="text-white"><strong><?php echo htmlspecialchars($profile->username); ?></strong></span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PROFILE SECTION -->
<section class="site-section" id="home-section">
  <div class="container">

    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success']); ?></div>
      <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error']); ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card p-4 py-5 text-center shadow-sm border-0">

      <!-- Profile Picture -->
      <div class="d-flex justify-content-center mb-3">
        <img 
          src="user-images/<?php echo htmlspecialchars($profile->img); ?>" 
          alt="Profile picture" 
          width="120" 
          height="120" 
          class="rounded-circle border border-3 border-light shadow-sm"
          style="object-fit: cover;"
        >
      </div>

      <!-- Username -->
      <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($profile->username); ?></h4>

      <!-- Title (if any) -->
      <?php if ($profile->title): ?>
        <p class="text-muted mb-2" style="font-size: 0.95rem;">
          <?php echo htmlspecialchars($profile->title); ?>
        </p>
      <?php endif; ?>

      <!-- Bio -->
      <?php if (!empty($profile->bio)): ?>
        <p class="mt-3 mb-4 px-3" style="font-size: 0.95rem; color: #555;">
          <?php echo nl2br(htmlspecialchars($profile->bio)); ?>
        </p>
      <?php endif; ?>

      <!-- Social Links -->
<div class="d-flex justify-content-center mt-3 mb-2">
  <?php if ($profile->facebook): ?>
    <a href="<?php echo htmlspecialchars($profile->facebook); ?>" target="_blank" 
       class="text-primary fs-4 mx-3 social-link">
      <span class="icon-facebook"></span>
    </a>
  <?php endif; ?>

  <?php if ($profile->twitter): ?>
    <a href="<?php echo htmlspecialchars($profile->twitter); ?>" target="_blank" 
       class="text-info fs-4 mx-3 social-link">
      <span class="icon-twitter"></span>
    </a>
  <?php endif; ?>

  <?php if ($profile->linkedin): ?>
    <a href="<?php echo htmlspecialchars($profile->linkedin); ?>" target="_blank" 
       class="text-primary fs-4 mx-3 social-link">
      <span class="icon-linkedin"></span>
    </a>
  <?php endif; ?>
</div>


    </div>
  </div>
</div>

          <!-- Only show edit options for owner -->
          <?php if (isset($_SESSION['id']) && $_SESSION['id'] == $id): ?>
            <button class="btn btn-sm btn-outline-primary mt-4" id="toggleEdit">✏️ Edit Profile</button>

            <!-- Hidden Edit Section -->
            <div id="editSection" class="mt-4" style="display:none;">
              
              <!-- Update Profile Info -->
              <form action="<?php echo APPURL; ?>/users/update-profile.php" method="POST" enctype="multipart/form-data" class="border p-3 rounded">
                <h6 class="text-left mb-3">🧍 Update Info</h6>
                <div class="form-group text-left">
                  <label>Username</label>
                  <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($profile->username); ?>" required>
                </div>

                <?php if ($_SESSION['type'] == 'Worker'): ?>
                <div class="form-group text-left">
                  <label>Job Title</label>
                  <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($profile->title); ?>">
                </div>
                <div class="form-group text-left">
                  <label>Bio</label>
                  <textarea name="bio" rows="3" class="form-control"><?php echo htmlspecialchars($profile->bio); ?></textarea>
                </div>
                <?php endif; ?>

                <div class="form-group text-left">
                  <label>Facebook</label>
                  <input type="url" name="facebook" class="form-control" value="<?php echo htmlspecialchars($profile->facebook); ?>">
                </div>
                <div class="form-group text-left">
                  <label>Twitter</label>
                  <input type="url" name="twitter" class="form-control" value="<?php echo htmlspecialchars($profile->twitter); ?>">
                </div>
                <div class="form-group text-left">
                  <label>LinkedIn</label>
                  <input type="url" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($profile->linkedin); ?>">
                </div>
                <input type="hidden" name="user_id" value="<?php echo $id; ?>">
                <button type="submit" class="btn btn-success btn-sm mt-2">💾 Save Changes</button>
              </form>

              <!-- Update Profile Image -->
              <form action="<?php echo APPURL; ?>/users/update-image.php" method="POST" enctype="multipart/form-data" class="border p-3 mt-4 rounded">
                <h6 class="text-left mb-3">🖼️ Update Profile Image</h6>
                <input type="file" name="image" class="form-control-file mb-2" accept="image/*" required>
                <input type="hidden" name="user_id" value="<?php echo $id; ?>">
                <button type="submit" class="btn btn-primary btn-sm">Upload</button>
              </form>

              <!-- Upload CV -->
              <form action="<?php echo APPURL; ?>/users/upload-cv.php" method="POST" enctype="multipart/form-data" class="border p-3 mt-4 rounded">
                <h6 class="text-left mb-3">📄 Upload CV / Resume</h6>
                <?php if (!empty($profile->cv)): ?>
                  <a href="<?php echo APPURL; ?>/users/cv/<?php echo htmlspecialchars($profile->cv); ?>" target="_blank" class="btn btn-outline-secondary btn-sm mb-2">View Current CV</a>
                <?php else: ?>
                  <p class="text-muted small">No CV uploaded yet.</p>
                <?php endif; ?>
                <input type="file" name="cv" class="form-control-file mb-2" accept=".pdf,.doc,.docx" required>
                <input type="hidden" name="user_id" value="<?php echo $id; ?>">
                <button type="submit" class="btn btn-outline-primary btn-sm">Upload / Replace CV</button>
              </form>

            </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</section>

<!-- JOBS SECTION -->
<section class="site-section">
  <div class="container">
    <?php if (isset($_SESSION['type']) && $_SESSION['type'] == "Company" && $_SESSION['id'] == $id): ?>
      <div class="row mb-5 justify-content-center">
        <div class="col-md-7 text-center">
          <h2 class="section-title mb-2">Jobs Posted by this Company</h2>
        </div>
      </div>
    <?php endif; ?>

    <ul class="job-listings mb-5">
      <?php foreach ($moreJobs as $oneJob): ?>
        <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
          <a href="<?php echo APPURL; ?>/jobs/job-single.php?id=<?php echo $oneJob->id; ?>"></a>
          <div class="job-listing-logo">
          
  <img src="user-images/<?php echo htmlspecialchars($oneJob->company_image); ?>" alt="" class="img-fluid d-inline-block">


          </div>
          <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
            <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
              <h2><?php echo htmlspecialchars($oneJob->job_title); ?></h2>
              <strong><?php echo htmlspecialchars($oneJob->company_name); ?></strong>
            </div>
            <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
              <span class="icon-room"></span> <?php echo htmlspecialchars($oneJob->job_region); ?>
            </div>
            <div class="job-listing-meta">
              <?php
                $type = strtolower(trim($oneJob->job_type ?? ''));
                if (strpos($type, 'part') !== false) $badge = 'danger';
                elseif (strpos($type, 'full') !== false) $badge = 'success';
                elseif (strpos($type, 'free') !== false) $badge = 'info';
                else $badge = 'secondary';
              ?>
              <span class="badge badge-<?php echo $badge; ?>"><?php echo htmlspecialchars(ucfirst($type)); ?></span>
            </div>
          </div>
        </li>
        <br>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.getElementById('toggleEdit');
  const editSection = document.getElementById('editSection');
  if (toggleBtn && editSection) {
    toggleBtn.addEventListener('click', () => {
      editSection.style.display = editSection.style.display === 'none' ? 'block' : 'none';
      toggleBtn.textContent = editSection.style.display === 'none' ? '✏️ Edit Profile' : '❌ Cancel Edit';
    });
  }
});
</script>

<?php require "../partials/footer.php"; ?>
