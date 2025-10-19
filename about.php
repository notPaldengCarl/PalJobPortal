<?php require "partials/header.php"; ?>
<?php require "config/config.php"; ?>
<?php 

  $select = $conn->query("SELECT * FROM jobs WHERE status = 1 ORDER BY created_at DESC LIMIT 5");
  $select->execute();
  $jobs = $select->fetchAll(PDO::FETCH_OBJ);

  $searches = $conn->query("SELECT COUNT(keyword) AS count, keyword FROM searches
   GROUP BY keyword ORDER BY count DESC LIMIT 4");
  $searches->execute();
  $allSearches = $searches->fetchAll(PDO::FETCH_OBJ);
?>
          
<!-- HOME -->
<section class="section-hero overlay inner-page bg-image" style="background-image: url('images/4k.jpg');" id="home-section">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <h1 class="text-white font-weight-bold">About PalJob Philippines</h1>
        <div class="custom-breadcrumbs">
          <a href="index.php">Home</a> <span class="mx-2 slash">/</span>
          <span class="text-white"><strong>About Us</strong></span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5 bg-image overlay-primary fixed overlay" id="next-section" style="background-image: url('images/hero_ph_stats.jpg');">
  <div class="container">
    <div class="row mb-5 justify-content-center">
      <div class="col-md-7 text-center">
        <h2 class="section-title mb-2 text-white">PalJob Philippines Stats</h2>
        <p class="lead text-white">Helping Filipino talent find work across Metro Manila, Cebu, Davao, Iloilo, and more—covering tech, BPO, retail, healthcare, and government.</p>
      </div>
    </div>
    <div class="row pb-0 block__19738 section-counter">
      <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
        <div class="d-flex align-items-center justify-content-center mb-2">
          <strong class="number" data-number="1930">0</strong>
        </div>
        <span class="caption">Candidates</span>
      </div>
      <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
        <div class="d-flex align-items-center justify-content-center mb-2">
          <strong class="number" data-number="54">0</strong>
        </div>
        <span class="caption">Jobs Posted</span>
      </div>
      <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
        <div class="d-flex align-items-center justify-content-center mb-2">
          <strong class="number" data-number="120">0</strong>
        </div>
        <span class="caption">Jobs Filled</span>
      </div>
      <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
        <div class="d-flex align-items-center justify-content-center mb-2">
          <strong class="number" data-number="550">0</strong>
        </div>
        <span class="caption">Companies</span>
      </div>
    </div>
  </div>
</section>

<section class="site-section pb-0">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-5 mb-lg-0">
        <a data-fancybox data-ratio="2" href="https://www.youtube.com/watch?v=Kccn1AvHDHY" class="None">
          <img src="images/ph_office_team_1.png" alt="Music" class="img-fluid img-shadow">
        </a>
      </div>
      <div class="col-lg-5 ml-auto">
        <h2 class="section-title mb-3">Built for Filipino Freelancers and Devs</h2>
        <p class="lead">Find remote and onsite roles with PH-based employers and global teams: web development, design, QA, product, and DevOps.</p>
        <p>Set location filters for Metro Manila, Cebu, Davao, Iloilo, Cagayan de Oro, and more. Get alerts for remote roles and salary bands in PHP to plan your next career move.</p>
      </div>
    </div>
  </div>
</section>

<section class="site-section pt-0">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-5 mb-lg-0 order-md-2">
        <a data-fancybox data-ratio="2" href="https://www.youtube.com/watch?v=Kccn1AvHDHY" class="None">

          <img src="images/ph_office_team_1.png" alt="Workers and teams across the Philippines" class="img-fluid img-shadow">
        </a>
      </div>
      <div class="col-lg-5 mr-auto order-md-1 mb-5 mb-lg-0">
        <h2 class="section-title mb-3">For Workers Across the PH</h2>
        <p class="lead">Search by region and job type—Full Time, Part Time, Freelance, or Remote—and apply with one profile for multiple postings.</p>
        <p>Employers can post jobs, manage applicants, and hire faster with localized screening questions and messaging tailored to Filipino applicants.</p>
      </div>
    </div>
  </div>
</section>

<section class="site-section">
  <div class="container">
    <div class="row mb-5">
      <div class="col-12 text-center" data-aos="fade">
        <h2 class="section-title mb-3">Our Team</h2>
      </div>
    </div>

    <div class="row align-items-center block__69944">
      <div class="col-md-6">
        <img src="images/team_ph_lead_1.jpg" alt="Team leader" class="img-fluid mb-4 rounded">
      </div>
      <div class="col-md-6">
        <h3>Carl Paldeng</h3>
        <p class="text-muted">Country Manager, Philippines</p>
        <p>Drives partnerships with universities, BPOs, and startups to open more opportunities for Filipino candidates nationwide.</p>
        <div class="social mt-4">
          <a href="#"><span class="icon-facebook"></span></a>
          <a href="#"><span class="icon-twitter"></span></a>
          <a href="#"><span class="icon-instagram"></span></a>
          <a href="#"><span class="icon-linkedin"></span></a>
        </div>
      </div>

  
</section>

<?php require "partials/footer.php"; ?>
</div>

<!-- SCRIPTS -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/isotope.pkgd.min.js"></script>
<script src="js/stickyfill.min.js"></script>
<script src="js/jquery.fancybox.min.js"></script>
<script src="js/jquery.easing.1.3.js"></script>
<script src="js/jquery.waypoints.min.js"></script>
<script src="js/jquery.animateNumber.min.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/quill.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>
<script src="js/custom.js"></script>
</body>
</html>
