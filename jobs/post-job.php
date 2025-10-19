<?php
require_once "../config/config.php";
require_once "../partials/header.php";

// Only companies can access
if (!isset($_SESSION['type']) || $_SESSION['type'] !== "Company") {
  header("Location: " . APPURL . "/index.php");
  exit;
}

// Get categories
$get_categories = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$get_categories->execute();
$get_category = $get_categories->fetchAll(PDO::FETCH_OBJ);

// Handle submit
if (isset($_POST['submit'])) {
  $required = [
    'job_title','job_region','job_type','vacancy','experience','salary','gender',
    'application_deadline','job_description','responsibilities','education_experience',
    'other_benifits','company_email','company_name','company_id','company_image','job_category'
  ];

  $missing = [];
  foreach ($required as $key) {
    if (!isset($_POST[$key]) || trim((string)$_POST[$key]) === '') {
      $missing[] = $key;
    }
  }

  if (!empty($missing)) {
    echo "<script>alert('One or more inputs are empty.');</script>";
  } else {
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
    $company_image        = trim($_POST['company_image']);

    $insert = $conn->prepare("
      INSERT INTO jobs (
        job_title, job_region, job_type, vacancy, job_category, experience, salary, gender,
        application_deadline, job_description, responsibilities, education_experience, other_benifits,
        company_email, company_name, company_id, company_image, status, created_at
      ) VALUES (
        :job_title, :job_region, :job_type, :vacancy, :job_category, :experience, :salary, :gender,
        :application_deadline, :job_description, :responsibilities, :education_experience, :other_benifits,
        :company_email, :company_name, :company_id, :company_image, 1, NOW()
      )
    ");

    $insert->execute([
      ':job_title'            => $job_title,
      ':job_region'           => $job_region,
      ':job_type'             => $job_type,
      ':vacancy'              => $vacancy,
      ':job_category'         => $job_category,
      ':experience'           => $experience,
      ':salary'               => $salary,
      ':gender'               => $gender,
      ':application_deadline' => $application_deadline,
      ':job_description'      => $job_description,
      ':responsibilities'     => $responsibilities,
      ':education_experience' => $education_experience,
      ':other_benifits'       => $other_benifits,
      ':company_email'        => $company_email,
      ':company_name'         => $company_name,
      ':company_id'           => $company_id,
      ':company_image'        => $company_image
    ]);

    header("Location: " . APPURL . "/jobs/post-job.php");
    exit;
  }
}
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
              action="post-job.php" method="post" novalidate>

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
              <option>Anywhere</option>
              <option>San Francisco</option>
              <option>Palo Alto</option>
              <option>New York</option>
              <option>Manhattan</option>
              <option>Ontario</option>
              <option>Toronto</option>
              <option>Kansas</option>
              <option>Mountain View</option>
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
