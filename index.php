<?php require "partials/header.php"; ?>
<?php require "config/config.php"; ?>

<?php 
  // ✅ Fetch latest 5 active jobs, joined with their company info
  $select = $conn->prepare("
    SELECT 
      j.id, j.job_title, j.job_region, j.job_type, j.status, j.created_at,
      u.username AS company_name,
      u.img AS company_image
    FROM jobs j
    LEFT JOIN users u ON j.company_id = u.id
    WHERE j.status = 1
    ORDER BY j.created_at DESC
    LIMIT 5
  ");
  $select->execute();
  $jobs = $select->fetchAll(PDO::FETCH_OBJ);

  // ✅ Fetch top 4 trending search keywords
  $searches = $conn->query("
    SELECT COUNT(keyword) AS count, keyword 
    FROM searches
    GROUP BY keyword 
    ORDER BY count DESC 
    LIMIT 4
  ");
  $searches->execute();
  $allSearches = $searches->fetchAll(PDO::FETCH_OBJ);
?>

<!-- HOME -->
<section class="home-section section-hero overlay bg-image" style="background-image: url('images/4k.jpg');" id="home-section">
  <div class="container">
    <div class="row align-items-center justify-content-center">
      <div class="col-md-12">
        <div class="mb-5 text-center">
          <h1 class="text-white font-weight-bold">Find Your Next Job in the Philippines</h1>
          <p>Connecting Filipino talent with great companies nationwide.</p>
        </div>

        <!-- Search Form -->
        <form method="post" action="search.php" class="search-jobs-form needs-validation" novalidate>
          <div class="row mb-5">
            <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
              <input name="job-title" type="text" class="form-control form-control-lg" placeholder="Job Title or Keyword" required>
              <div class="invalid-feedback">Please enter a job title or keyword.</div>
            </div>

            <div class="col-22 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
              <select name="job-region" class="selectpicker" data-style="btn-white btn-lg" data-width="100%" data-live-search="true" title="Select Region" required>
                <option value="" disabled selected>Select Region</option>
                <option>Anywhere in the Philippines</option>
                <option>Metro Manila</option>
                <option>Cebu</option>
                <option>Davao</option>
                <option>Iloilo</option>
                <option>Cagayan de Oro</option>
                <option>Baguio</option>
                <option>Laguna</option>
                <option>Pampanga</option>
              </select>
              <div class="invalid-feedback d-block">Please select a region.</div>
            </div>

            <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
              <select name="job-type" class="selectpicker" data-style="btn-white btn-lg" data-width="100%" data-live-search="true" title="Select Job Type" required>
                <option value="" disabled selected>Select Job Type</option>
                <option>Full Time</option>
                <option>Part Time</option>
                <option>Freelance</option>
              </select>
              <div class="invalid-feedback d-block">Please select a job type.</div>
            </div>

            <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
              <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block text-white btn-search">
                <span class="icon-search icon mr-2"></span>Search Job
              </button>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 popular-keywords">
              <h3>Trending Keywords:</h3>
              <ul class="keywords list-unstyled m-0 p-0">
                <?php foreach($allSearches as $search) : ?>
                  <li><a href="#" class=""><?php echo htmlspecialchars($search->keyword, ENT_QUOTES, 'UTF-8'); ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>

  <a href="#next" class="scroll-button smoothscroll">
    <span class="icon-keyboard_arrow_down"></span>
  </a>
</section>

<!-- Validation Script -->
<script>
(function() {
  'use strict';
  const form = document.querySelector('.search-jobs-form');
  if (!form) return;

  function normalizeSelectpickerRequired() {
    const selects = form.querySelectorAll('select[required]');
    selects.forEach(sel => {
      if (!sel.value || sel.value === '') {
        sel.setCustomValidity('Please select an option.');
      } else {
        sel.setCustomValidity('');
      }
    });
  }

  form.addEventListener('submit', function(e) {
    normalizeSelectpickerRequired();
    if (!form.checkValidity()) {
      e.preventDefault();
      e.stopPropagation();
    }
    form.classList.add('was-validated');
  });

  form.addEventListener('change', function(e) {
    if (e.target.tagName === 'SELECT') normalizeSelectpickerRequired();
  });
})();
</script>

<!-- Stats Section -->
<section class="py-5 bg-image overlay-primary fixed overlay" id="next" style="background-image: url('images/hero_ph.jpg');">
  <div class="container">
    <div class="row mb-5 justify-content-center">
      <div class="col-md-7 text-center">
        <h2 class="section-title mb-2 text-white">JobBoard Philippines Stats</h2>
        <p class="lead text-white">Thousands of opportunities for professionals across the country.</p>
      </div>
    </div>
    <div class="row pb-0 block__19738 section-counter">
      <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
        <div class="d-flex align-items-center justify-content-center mb-2">
          <strong class="number" data-number="2140">0</strong>
        </div>
        <span class="caption">Candidates</span>
      </div>
      <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
        <div class="d-flex align-items-center justify-content-center mb-2">
          <strong class="number" data-number="85">0</strong>
        </div>
        <span class="caption">Jobs Posted</span>
      </div>
      <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
        <div class="d-flex align-items-center justify-content-center mb-2">
          <strong class="number" data-number="150">0</strong>
        </div>
        <span class="caption">Jobs Filled</span>
      </div>
      <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
        <div class="d-flex align-items-center justify-content-center mb-2">
          <strong class="number" data-number="430">0</strong>
        </div>
        <span class="caption">Companies</span>
      </div>
    </div>
  </div>
</section>

<!-- Job Listings -->
<section class="site-section">
  <div class="container">
    <ul class="job-listings mb-5">
      <?php foreach($jobs as $job) : ?>
        <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
          <a href="jobs/job-single.php?id=<?php echo $job->id; ?>"></a>
          <div class="job-listing-logo">
            <img src="users/user-images/<?php echo !empty($job->company_image) ? htmlspecialchars($job->company_image) : 'default.png'; ?>" 
                 alt="<?php echo htmlspecialchars($job->company_name); ?>" 
                 class="img-fluid">
          </div>
          <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
            <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
              <h2><?php echo htmlspecialchars($job->job_title); ?></h2>
              <strong><?php echo htmlspecialchars($job->company_name); ?></strong>
            </div>
            <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
              <span class="icon-room"></span> <?php echo htmlspecialchars($job->job_region); ?>
            </div>
           <div class="job-listing-meta">
  <?php
    // Normalize job_type (handle null, lowercase, or underscores)
    $type = strtolower(trim(str_replace('_', ' ', $job->job_type ?? '')));
    $badgeClass = 'secondary';

    if ($type === 'part time') $badgeClass = 'danger';
    elseif ($type === 'full time') $badgeClass = 'success';
    elseif ($type === 'freelance') $badgeClass = 'info';
  ?>

  <span class="badge badge-<?php echo $badgeClass; ?>">
   <small class="text-muted"><?php echo htmlspecialchars($job->job_type); ?></small>
  </span>
</div>

          </div>
        </li>
        <br>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-image overlay-primary fixed overlay" style="background-image: url('images/hero_ph.jpg');">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h2 class="text-white">Looking for a job in the Philippines?</h2>
        <p class="mb-0 text-white lead">Discover the best openings from Manila to Mindanao — apply now!</p>
      </div>
      <div class="col-md-3 ml-auto">
        <a href="auth/register.php" class="btn btn-warning btn-block btn-lg">Sign Up</a>
      </div>
    </div>
  </div>
</section>

<!-- Partners -->
<section class="site-section py-4">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-12 text-center mt-4 mb-5">
        <div class="row justify-content-center">
          <div class="col-md-7">
            <h2 class="section-title mb-2">Companies We've Partnered With</h2>
            <p class="lead">Trusted by top employers in the Philippines.</p>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3 col-md-6 text-center">
        <img src="images/ayala-logo.svg" alt="Ayala" class="img-fluid logo-1">
      </div>
      <div class="col-6 col-lg-3 col-md-6 text-center">
        <img src="images/globe-logo.png" alt="Globe" class="img-fluid logo-2">
      </div>
      <div class="col-6 col-lg-3 col-md-6 text-center">
        <img src="images/sm-logo.png" alt="SM" class="img-fluid logo-3">
      </div>
      <div class="col-6 col-lg-3 col-md-6 text-center">
        <img src="images/jollibee-logo.png" alt="Jollibee" class="img-fluid logo-4">
      </div>
    </div>
  </div>
</section>

<?php require "partials/footer.php"; ?>
