<?php
require_once "../config/config.php";

// ✅ Always start the session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Only companies can access
if (!isset($_SESSION['type']) || $_SESSION['type'] !== "Company") {
    header("Location: " . APPURL . "/index.php");
    exit;
}

// ✅ Get categories before rendering form
$get_categories = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$get_category = $get_categories->fetchAll(PDO::FETCH_OBJ);

// ✅ Handle submit BEFORE including header.php
if (isset($_POST['submit'])) {
    $required = [
        'job_title','job_region','job_type','vacancy','experience','salary','gender',
        'application_deadline','job_description','responsibilities','education_experience',
        'other_benifits','company_email','company_name','company_id','job_category'
    ];

    $missing = [];
    foreach ($required as $key) {
        if (!isset($_POST[$key]) || trim((string)$_POST[$key]) === '') {
            $missing[] = $key;
        }
    }

    if (!empty($missing)) {
        $_SESSION['flash_error'] = 'One or more inputs are empty.';
        header("Location: " . APPURL . "/jobs/post-job.php");
        exit;
    }

    // ✅ Sanitize input
    $job_title            = trim($_POST['job_title']);
    $job_region           = trim($_POST['job_region']);
    $job_type             = trim($_POST['job_type']);
    $vacancy              = (int)$_POST['vacancy'];
    $job_category         = trim($_POST['job_category']);
    $experience           = trim($_POST['experience']);
    $salary               = trim($_POST['salary']);
    $gender               = trim($_POST['gender']);
    $application_deadline = $_POST['application_deadline'];
    $job_description      = trim($_POST['job_description']);
    $responsibilities     = trim($_POST['responsibilities']);
    $education_experience = trim($_POST['education_experience']);
    $other_benifits       = trim($_POST['other_benifits']);
    $company_email        = trim($_POST['company_email']);
    $company_name         = trim($_POST['company_name']);
    $company_id           = (int)$_POST['company_id'];
    $company_image        = trim($_POST['company_image'] ?? '');

    // ✅ Handle optional job image upload
    $uploadedImageName = null;
    if (isset($_FILES['job_image']) && is_uploaded_file($_FILES['job_image']['tmp_name'])) {
        $file = $_FILES['job_image'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $maxSize = 2 * 1024 * 1024; // 2MB
            if ($file['size'] > $maxSize) {
                $_SESSION['flash_error'] = 'Job image too large (max 2MB).';
                header("Location: " . APPURL . "/jobs/post-job.php");
                exit;
            }

            $imgInfo = @getimagesize($file['tmp_name']);
            if ($imgInfo === false) {
                $_SESSION['flash_error'] = 'Invalid job image file.';
                header("Location: " . APPURL . "/jobs/post-job.php");
                exit;
            }

            $mime = $imgInfo['mime'];
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
            if (!isset($allowed[$mime])) {
                $_SESSION['flash_error'] = 'Only JPG, PNG or GIF allowed.';
                header("Location: " . APPURL . "/jobs/post-job.php");
                exit;
            }

            $ext = $allowed[$mime];
            $uploadDir = __DIR__ . '/job-images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $uploadedImageName = uniqid('job_', true) . '.' . $ext;
            $destination = $uploadDir . $uploadedImageName;
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                $_SESSION['flash_error'] = 'Failed to save uploaded image.';
                header("Location: " . APPURL . "/jobs/post-job.php");
                exit;
            }
            @chmod($destination, 0644);
        }
    }

    // ✅ Insert job record
    $stmtCol = $conn->prepare("SHOW COLUMNS FROM jobs LIKE 'job_image'");
    $stmtCol->execute();
    $hasJobImage = (bool) $stmtCol->fetch(PDO::FETCH_ASSOC);

    if ($hasJobImage) {
        $sql = "
            INSERT INTO jobs (
                job_title, job_region, job_type, vacancy, job_category, experience, salary, gender,
                application_deadline, job_description, responsibilities, education_experience, other_benifits,
                company_email, company_name, company_id, company_image, job_image, status, created_at
            ) VALUES (
                :job_title, :job_region, :job_type, :vacancy, :job_category, :experience, :salary, :gender,
                :application_deadline, :job_description, :responsibilities, :education_experience, :other_benifits,
                :company_email, :company_name, :company_id, :company_image, :job_image, 1, NOW()
            )
        ";
    } else {
        $sql = "
            INSERT INTO jobs (
                job_title, job_region, job_type, vacancy, job_category, experience, salary, gender,
                application_deadline, job_description, responsibilities, education_experience, other_benifits,
                company_email, company_name, company_id, company_image, status, created_at
            ) VALUES (
                :job_title, :job_region, :job_type, :vacancy, :job_category, :experience, :salary, :gender,
                :application_deadline, :job_description, :responsibilities, :education_experience, :other_benifits,
                :company_email, :company_name, :company_id, :company_image, 1, NOW()
            )
        ";
    }

    $insert = $conn->prepare($sql);
    $insert->execute([
        ':job_title' => $job_title,
        ':job_region' => $job_region,
        ':job_type' => $job_type,
        ':vacancy' => $vacancy,
        ':job_category' => $job_category,
        ':experience' => $experience,
        ':salary' => $salary,
        ':gender' => $gender,
        ':application_deadline' => $application_deadline,
        ':job_description' => $job_description,
        ':responsibilities' => $responsibilities,
        ':education_experience' => $education_experience,
        ':other_benifits' => $other_benifits,
        ':company_email' => $company_email,
        ':company_name' => $company_name,
        ':company_id' => $company_id,
        ':company_image' => $company_image,
        ':job_image' => $uploadedImageName
    ]);

    $_SESSION['flash_success'] = 'Job posted successfully!';
    header("Location: " . APPURL . "/jobs/post-job.php");
    exit;
}

// ✅ Include header only after logic is done
require "../partials/header.php";
?>

<!-- HERO -->
<section class="section-hero overlay inner-page bg-image" 
         style="background-image: url('../images/4K.jpg'); position: relative; z-index:1;" 
         id="home-section">
  <div class="container" style="position: relative; z-index:1;">
    <div class="row">
      <div class="col-md-7">
        <h1 class="text-white font-weight-bold">Post A Job</h1>
        <div class="custom-breadcrumbs">
          <a href="<?php echo APPURL; ?>">Home</a> <span class="mx-2 slash">/</span>
          <a href="#">Job</a> <span class="mx-2 slash">/</span>
          <span class="text-white"><strong>Post a Job</strong></span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="site-section" style="position: relative; z-index: 2; background: #fff;">
  <div class="container">

    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8'); ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8'); ?></div>
      <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <div class="row align-items-center mb-5">
      <div class="col-lg-8 mb-4 mb-lg-0">
        <div class="d-flex align-items-center">
          <div><h2>Post A Job</h2></div>
        </div>
      </div>
    </div>

    <div class="row mb-5">
      <div class="col-lg-12">
        <form class="p-4 p-md-5 border rounded needs-validation" 
              action="post-job.php" method="post" enctype="multipart/form-data" novalidate>

          <div class="form-group">
            <label for="job-title">Job Title</label>
            <input type="text" name="job_title" class="form-control" id="job-title" placeholder="Product Designer"
                   required minlength="3" maxlength="100">
            <div class="invalid-feedback">Please enter a job title (3–100 characters).</div>
          </div>

          <div class="form-group">
            <label for="job-region">Job Region</label>
            <select name="job_region" class="form-control" id="job-region" required>
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
            <div class="invalid-feedback">Please choose a job region.</div>
          </div>

          <div class="form-group">
            <label for="job-type">Job Type</label>
            <select name="job_type" class="form-control" id="job-type" required>
              <option value="" disabled selected>Select Job Type</option>
              <option>Part Time</option>
              <option>Full Time</option>
            </select>
            <div class="invalid-feedback">Please choose a job type.</div>
          </div>

          <div class="form-group">
            <label for="vacancy">Vacancy</label>
            <input name="vacancy" type="number" class="form-control" id="vacancy" placeholder="e.g. 3"
                   required min="1" max="10000" step="1">
            <div class="invalid-feedback">Please enter vacancies (number ≥ 1).</div>
          </div>

          <div class="form-group">
            <label for="job-category">Job Category</label>
            <select name="job_category" class="form-control" id="job-category" required>
              <option value="" disabled selected>Select Job Category</option>
              <?php foreach ($get_category as $category): ?>
                <option><?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please choose a job category.</div>
          </div>

          <div class="form-group">
            <label for="experience">Experience</label>
            <select name="experience" class="form-control" id="experience" required>
              <option value="" disabled selected>Select Years of Experience</option>
              <option>1-3 years</option>
              <option>3-6 years</option>
              <option>6-9 years</option>
            </select>
            <div class="invalid-feedback">Please choose required experience.</div>
          </div>

          <div class="form-group">
            <label for="salary">Salary</label>
            <select name="salary" class="form-control" id="salary" required>
              <option value="" disabled selected>Select Salary</option>
              <option>$50k - $70k</option>
              <option>$70k - $100k</option>
              <option>$100k - $150k</option>
            </select>
            <div class="invalid-feedback">Please choose a salary range.</div>
          </div>

          <div class="form-group">
            <label for="gender">Gender</label>
            <select name="gender" class="form-control" id="gender" required>
              <option value="" disabled selected>Select Gender</option>
              <option>Male</option>
              <option>Female</option>
              <option>Any</option>
            </select>
            <div class="invalid-feedback">Please select a gender option.</div>
          </div>

          <div class="form-group">
            <label for="application-deadline">Application Deadline</label>
            <input name="application_deadline" type="date" class="form-control" id="application-deadline" required>
            <div class="invalid-feedback">Please select an application deadline.</div>
          </div>

          <div class="form-group">
            <label for="job-description">Job Description</label>
            <textarea name="job_description" id="job-description" cols="30" rows="7" class="form-control"
                      placeholder="Write Job Description..." required minlength="30" maxlength="5000"></textarea>
            <div class="invalid-feedback">Please enter a description (min 30 characters).</div>
          </div>

          <div class="form-group">
            <label for="responsibilities">Responsibilities</label>
            <textarea name="responsibilities" id="responsibilities" cols="30" rows="7" class="form-control"
                      placeholder="Write Responsibilities..." required minlength="20" maxlength="5000"></textarea>
            <div class="invalid-feedback">Please list responsibilities (min 20 characters).</div>
          </div>

          <div class="form-group">
            <label for="education-experience">Education & Experience</label>
            <textarea name="education_experience" id="education-experience" cols="30" rows="7" class="form-control"
                      placeholder="Write Education & Experience..." required minlength="10" maxlength="5000"></textarea>
            <div class="invalid-feedback">Please add education/experience (min 10 characters).</div>
          </div>

          <div class="form-group">
            <label for="other-benifits">Other Benefits</label>
            <textarea name="other_benifits" id="other-benifits" cols="30" rows="7" class="form-control"
                      placeholder="Write Other Benefits..." required minlength="5" maxlength="5000"></textarea>
            <div class="invalid-feedback">Please add other benefits (min 5 characters).</div>
          </div>

          <div class="form-group">
            <label for="job-image">Job Image (optional)</label>
            <input type="file" name="job_image" id="job-image" accept="image/jpeg,image/png,image/gif" class="form-control-file">
            <small class="form-text text-muted">Optional. JPG, PNG or GIF. Max 2MB.</small>
          </div>

          <input type="hidden" name="company_email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="company_name"  value="<?php echo htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="company_id"    value="<?php echo (int)($_SESSION['id'] ?? 0); ?>">
          <input type="hidden" name="company_image" value="<?php echo htmlspecialchars($_SESSION['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

          <div class="text-right mt-3">
            <input type="submit" name="submit" class="btn btn-primary" value="Save Job">
          </div>

        </form>
      </div>
    </div>

  </div>
</section>

<?php require_once "../partials/footer.php"; ?>

<script>
(function () {
  'use strict';
  var forms = document.querySelectorAll('.needs-validation');
  Array.prototype.forEach.call(forms, function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>
