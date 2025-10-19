<?php require "partials/header.php"; ?>
<?php require "config/config.php"; ?>

<?php 
$select = $conn->query("SELECT id, username, img, bio FROM users WHERE type = 'Company'");
$select->execute();
$allCompanies = $select->fetchAll(PDO::FETCH_OBJ);
?>

<section class="section-hero overlay inner-page bg-image" style="background-image: url('<?php echo APPURL; ?>/images/4k.jpg');" id="home-section">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <h1 class="text-white font-weight-bold">Companies</h1>
        <div class="custom-breadcrumbs">
          <a href="<?php echo APPURL; ?>/index.php">Home</a> <span class="mx-2 slash">/</span>
          <span class="text-white"><strong>Companies</strong></span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="site-section" id="home-section">
  <div class="container">
    <div class="row">
      <?php foreach ($allCompanies as $company): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?php echo htmlspecialchars($company->username, ENT_QUOTES, 'UTF-8'); ?></h5>
              <p class="card-text">
                <?php echo htmlspecialchars(mb_strimwidth((string)($company->bio ?? ''), 0, 80, '…', 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>
              </p>
              <a target="_blank"
                 href="<?php echo APPURL; ?>/users/public-profile.php?id=<?php echo (int)$company->id; ?>"
                 class="btn btn-primary mt-auto">Go to profile</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($allCompanies)): ?>
        <div class="col-12">
          <div class="alert alert-secondary">No companies found.</div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require "partials/footer.php"; ?>
