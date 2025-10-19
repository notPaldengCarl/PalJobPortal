<?php
require "../config/config.php";
require "../partials/header.php";

// ✅ Ensure only logged-in companies can access
if (!isset($_SESSION['type']) || $_SESSION['type'] !== "Company") {
  header("Location: " . APPURL . "/auth/login.php");
  exit;
}

// ✅ Get all categories
$get_categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$get_categories->execute();
$get_category = $get_categories->fetchAll(PDO::FETCH_OBJ);

// ✅ Check if job ID exists
if (isset($_GET['id'])) {
  $id = $_GET['id'];

  $select = $conn->prepare("SELECT * FROM jobs WHERE id = :id");
  $select->execute([':id' => $id]);
  $singleJob = $select->fetch(PDO::FETCH_OBJ);

  if (!$singleJob) {
    header("Location: " . APPURL . "/404.php");
    exit;
  }

  // ✅ Prevent users from editing others’ jobs
  if ($singleJob->company_id != $_SESSION['id']) {
    header("Location: " . APPURL);
    exit;
  }
} else {
  header("Location: " . APPURL . "/404.php");
  exit;
}

// ✅ Handle Update Form Submission
if (isset($_POST['submit'])) {
  $required_fields = [
    'job_title', 'job_region', 'job_type', 'vacancy', 'job_category',
    'experience', 'salary', 'gender', 'application_deadline',
    'job_description', 'responsibilities', 'education_experience',
    'other_benifits', 'company_email', 'company_name', 'company_id', 'company_image'
  ];

  foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
      echo "<script>alert('One or more fields are empty');</script>";
      exit;
    }
  }

  // ✅ Prepare update query
  $update = $conn->prepare("
    UPDATE jobs SET
      job_title = :job_title,
      job_region = :job_region,
      job_type = :job_type,
      vacancy = :vacancy,
      job_category = :job_category,
      experience = :experience,
      salary = :salary,
      gender = :gender,
      application_deadline = :application_deadline,
      job_description = :job_description,
      responsibilities = :responsibilities,
      education_experience = :education_experience,
      other_benifits = :other_benifits,
      company_email = :company_email,
      company_name = :company_name,
      company_id = :company_id,
      company_image = :company_image
    WHERE id = :id
  ");

  $update->execute([
    ':job_title' => $_POST['job_title'],
    ':job_region' => $_POST['job_region'],
    ':job_type' => $_POST['job_type'],
    ':vacancy' => $_POST['vacancy'],
    ':job_category' => $_POST['job_category'],
    ':experience' => $_POST['experience'],
    ':salary' => $_POST['salary'],
    ':gender' => $_POST['gender'],
    ':application_deadline' => $_POST['application_deadline'],
    ':job_description' => $_POST['job_description'],
    ':responsibilities' => $_POST['responsibilities'],
    ':education_experience' => $_POST['education_experience'],
    ':other_benifits' => $_POST['other_benifits'],
    ':company_email' => $_POST['company_email'],
    ':company_name' => $_POST['company_name'],
    ':company_id' => $_POST['company_id'],
    ':company_image' => $_POST['company_image'],
    ':id' => $id
  ]);

  header("Location: " . APPURL . "/jobs/job-update.php?id=" . $id);
  exit;
}
?>

<section class="section-hero overlay inner-page bg-image" style="background-image: url('../images/4k.jpg');" id="home-section">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <h1 class="text-white font-weight-bold">Update A Job</h1>
        <div class="custom-breadcrumbs">
          <a href="<?php echo APPURL; ?>">Home</a> <span class="mx-2 slash">/</span>
          <a href="#">Job</a> <span class="mx-2 slash">/</span>
          <span class="text-white"><strong>Update Job</strong></span>
        </div>
      </div>
    </div>
  </div>
</section>


<section class="site-section">
  <div class="container">

    <div class="row align-items-center mb-5">
      <div class="col-lg-8 mb-4 mb-lg-0">
        <div class="d-flex align-items-center">
          <h2>Update Job Details</h2>
        </div>
      </div>
    </div>

    <div class="row mb-5">
      <div class="col-lg-12">
        <form class="p-4 p-md-5 border rounded" action="job-update.php?id=<?php echo $id; ?>" method="post">

          <div class="form-group">
            <label for="job-title">Job Title</label>
            <input type="text" name="job_title" value="<?php echo htmlspecialchars($singleJob->job_title); ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="job-region">Job Region</label>
            <select name="job_region" class="form-control" required>
              <?php
              $regions = ["Anywhere", "San Francisco", "Palo Alto", "New York", "Manhattan", "Ontario", "Toronto", "Kansas", "Mountain View"];
              foreach ($regions as $region) {
                $selected = ($region == $singleJob->job_region) ? "selected" : "";
                echo "<option $selected>$region</option>";
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label for="job-type">Job Type</label>
            <select name="job_type" class="form-control" required>
              <option <?php if ($singleJob->job_type == 'Part Time') echo 'selected'; ?>>Part Time</option>
              <option <?php if ($singleJob->job_type == 'Full Time') echo 'selected'; ?>>Full Time</option>
            </select>
          </div>

          <div class="form-group">
            <label for="vacancy">Vacancy</label>
            <input type="text" name="vacancy" value="<?php echo htmlspecialchars($singleJob->vacancy); ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Job Category</label>
            <select name="job_category" class="form-control" required>
              <?php foreach ($get_category as $category): ?>
                <option <?php if ($category->name == $singleJob->job_category) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($category->name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Experience</label>
            <select name="experience" class="form-control">
              <option <?php if ($singleJob->experience == '1-3 years') echo 'selected'; ?>>1-3 years</option>
              <option <?php if ($singleJob->experience == '3-6 years') echo 'selected'; ?>>3-6 years</option>
              <option <?php if ($singleJob->experience == '6-9 years') echo 'selected'; ?>>6-9 years</option>
            </select>
          </div>

          <div class="form-group">
            <label>Salary</label>
            <select name="salary" class="form-control">
              <option <?php if ($singleJob->salary == '$50k - $70k') echo 'selected'; ?>>$50k - $70k</option>
              <option <?php if ($singleJob->salary == '$70k - $100k') echo 'selected'; ?>>$70k - $100k</option>
              <option <?php if ($singleJob->salary == '$100k - $150k') echo 'selected'; ?>>$100k - $150k</option>
            </select>
          </div>

          <div class="form-group">
            <label>Gender</label>
            <select name="gender" class="form-control">
              <option <?php if ($singleJob->gender == 'Male') echo 'selected'; ?>>Male</option>
              <option <?php if ($singleJob->gender == 'Female') echo 'selected'; ?>>Female</option>
              <option <?php if ($singleJob->gender == 'Any') echo 'selected'; ?>>Any</option>
            </select>
          </div>

          <div class="form-group">
            <label>Application Deadline</label>
            <input type="text" name="application_deadline" value="<?php echo htmlspecialchars($singleJob->application_deadline); ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Job Description</label>
            <textarea name="job_description" class="form-control" rows="6" required><?php echo htmlspecialchars($singleJob->job_description); ?></textarea>
          </div>

          <div class="form-group">
            <label>Responsibilities</label>
            <textarea name="responsibilities" class="form-control" rows="6" required><?php echo htmlspecialchars($singleJob->responsibilities); ?></textarea>
          </div>

          <div class="form-group">
            <label>Education & Experience</label>
            <textarea name="education_experience" class="form-control" rows="6" required><?php echo htmlspecialchars($singleJob->education_experience); ?></textarea>
          </div>

          <div class="form-group">
            <label>Other Benefits</label>
            <textarea name="other_benifits" class="form-control" rows="6" required><?php echo htmlspecialchars($singleJob->other_benifits); ?></textarea>
          </div>

          <!-- ✅ Hidden Company Data -->
          <input type="hidden" name="company_email" value="<?php echo $_SESSION['email']; ?>">
          <input type="hidden" name="company_name" value="<?php echo $_SESSION['username']; ?>">
          <input type="hidden" name="company_id" value="<?php echo $_SESSION['id']; ?>">
          <input type="hidden" name="company_image" value="<?php echo $_SESSION['image']; ?>">

          <div class="text-right mt-4">
            <input type="submit" name="submit" class="btn btn-primary btn-md px-5" value="Update Job">
          </div>

        </form>
      </div>
    </div>

  </div>
</section>

<?php require "../partials/footer.php"; ?>
