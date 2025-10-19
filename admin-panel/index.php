<?php require "layouts/header.php"; ?>
<?php require "../config/config.php"; ?>

<?php 
if(!isset($_SESSION['adminname'])) {
  header("location: ".ADMINURL."/admins/login-admins.php");
  exit;
}

// Counts (keep your existing queries if you prefer)
$jobs = $conn->query("SELECT COUNT(*) AS count_jobs FROM jobs");
$jobs->execute();
$counJobs = $jobs->fetch(PDO::FETCH_OBJ);

$categories = $conn->query("SELECT COUNT(*) AS count_cats FROM categories");
$categories->execute();
$counCategories = $categories->fetch(PDO::FETCH_OBJ);

$admins = $conn->query("SELECT COUNT(*) AS count_admins FROM admins");
$admins->execute();
$counAdmins = $admins->fetch(PDO::FETCH_OBJ);

// Safe username output
$username = htmlspecialchars($_SESSION['adminname'], ENT_QUOTES, 'UTF-8');
?>

<div class="container-fluid">

  <!-- Friendly header -->
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h4 class="mb-0">Admin Dashboard</h4>
      <small class="text-muted">Hi, <?php echo $username; ?> 👋</small>
    </div>
  </div>

  <!-- Your existing cards -->
  <div class="row">
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Jobs</h5>
          <p class="card-text">Number of jobs: <?php echo (int)$counJobs->count_jobs; ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Categories</h5>
          <p class="card-text">Number of categories: <?php echo (int)$counCategories->count_cats; ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Admins</h5>
          <p class="card-text">Number of admins: <?php echo (int)$counAdmins->count_admins; ?></p>
        </div>
      </div>
    </div>
  </div>

</div>

<?php require "layouts/footer.php"; ?>
