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
        <h1 class="text-white font-weight-bold">Contact PalJob Philippines</h1>
        <div class="custom-breadcrumbs">
          <a href="index.php">Home</a> <span class="mx-2 slash">/</span>
          <span class="text-white"><strong>Contact Us</strong></span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="site-section" id="next-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 mb-5 mb-lg-0">
        <form action="#" class="">

          <div class="row form-group">
            <div class="col-md-6 mb-3 mb-md-0">
              <label class="text-black" for="fname">First Name</label>
              <input type="text" id="fname" class="form-control" placeholder="Juan">
            </div>
            <div class="col-md-6">
              <label class="text-black" for="lname">Last Name</label>
              <input type="text" id="lname" class="form-control" placeholder="Dela Cruz">
            </div>
          </div>

          <div class="row form-group">
            <div class="col-md-12">
              <label class="text-black" for="email">Email</label> 
              <input type="email" id="email" class="form-control" placeholder="you@example.com">
            </div>
          </div>

          <div class="row form-group">
            <div class="col-md-12">
              <label class="text-black" for="subject">Subject</label> 
              <input type="subject" id="subject" class="form-control" placeholder="Partner inquiry, job posting, or support">
            </div>
          </div>

          <div class="row form-group">
            <div class="col-md-12">
              <label class="text-black" for="message">Message</label> 
              <textarea name="message" id="message" cols="30" rows="7" class="form-control" placeholder="Tell us how we can help: posting a job, account support, billing, or media."></textarea>
            </div>
          </div>

          <div class="row form-group">
            <div class="col-md-12">
              <input type="submit" value="Send Message" class="btn btn-primary btn-md text-white">
            </div>
          </div>

        </form>
      </div>

      <div class="col-lg-5 ml-auto">
        <div class="p-4 mb-3 bg-white">
          <p class="mb-0 font-weight-bold">Address</p>
          <p class="mb-4">PalJob PH, 24F Net Park, 5th Ave, Bonifacio Global City, Taguig, Metro Manila 1630</p>

          <p class="mb-0 font-weight-bold">Phone</p>
          <p class="mb-4"><a href="tel:+63282456789">+63 (2) 8245 6789</a> • <a href="tel:+639171234567">+63 917 123 4567</a></p>

          <p class="mb-0 font-weight-bold">Email Address</p>
          <p class="mb-0"><a href="mailto:support@paljob.ph">support@paljob.ph</a> • <a href="mailto:partners@paljob.ph">partners@paljob.ph</a></p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="site-section bg-light">
  <div class="container">
    <div class="row mb-5">
      <div class="col-12 text-center" data-aos="fade">
        <h2 class="section-title mb-3">What Candidates Say</h2>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">
        <div class="block__87154 bg-white rounded">
          <blockquote>
            <p>“Nakahanap ako ng remote QA role habang nasa Cebu—madali ang apply at mabilis ang response ng employer.”</p>
          </blockquote>
          <div class="block__91147 d-flex align-items-center">
            <figure class="mr-4"><img src="images/team_ph_lead_1.jpg" alt="Image" class="img-fluid"></figure>
            <div>
              <h3>Maria Santos</h3>
              <span class="position">QA Engineer, Cebu</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="block__87154 bg-white rounded">
          <blockquote>
            <p>“Nag‑match agad ang skills ko sa isang FinTech sa Makati—great filters for salary and job type.”</p>
          </blockquote>
          <div class="block__91147 d-flex align-items-center">
            <figure class="mr-4"><img src="images/team_ph_lead_1.jpg" alt="Image" class="img-fluid"></figure>
            <div>
              <h3>Carl Paldeng</h3>
              <span class="position">Frontend Developer, Makati</span>
            </div>
          </div>
        </div>
      </div>

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
