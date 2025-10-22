<?php
// ✅ Session + constants
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
if (!defined('APPURL')) {
  define("APPURL", "http://localhost/paljob");
}
?>

<!doctype html>
<html lang="en">
  <head>
    <title>PalJob — Carl Paldeng</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="ftco-32x32.png">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="<?php echo APPURL; ?>/css/custom-bs.css">
    <link rel="stylesheet" href="<?php echo APPURL; ?>/css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="<?php echo APPURL; ?>/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="<?php echo APPURL; ?>/fonts/icomoon/style.css">
    <link rel="stylesheet" href="<?php echo APPURL; ?>/fonts/line-icons/style.css">
    <link rel="stylesheet" href="<?php echo APPURL; ?>/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo APPURL; ?>/css/animate.min.css">
    <link rel="stylesheet" href="<?php echo APPURL; ?>/css/quill.snow.css">
    <link rel="stylesheet" href="<?php echo APPURL; ?>/css/style.css">

    <!-- Responsive & Mobile Fixes -->
    <style>
      @media (max-width: 991.98px) {
        .site-navbar .site-navigation { display: none; }
        .site-navbar .site-menu-toggle { display: inline-block; }
      }
      @media (min-width: 992px) {
        .site-mobile-menu { display: none; }
      }
      body.offcanvas-menu { overflow: hidden; }
      /* ✅ Animation for mobile menu */
      .site-mobile-menu {
        transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
        transform: translateX(100%);
        opacity: 0;
      }
      body.offcanvas-menu .site-mobile-menu {
        transform: translateX(0);
        opacity: 1;
      }
      .site-mobile-menu a {
        pointer-events: auto;
        z-index: 10000;
      }
    </style>
  </head>

  <body id="top">
  <div class="site-wrap">

    <!-- ✅ MOBILE MENU -->
    <div class="site-mobile-menu site-navbar-target" aria-hidden="true">
      <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-logo">
          <a href="<?php echo APPURL; ?>">PalJob</a>
        </div>
        <div class="site-mobile-menu-close mt-3">
          <a href="#" class="site-menu-toggle js-menu-toggle" aria-label="Close" aria-expanded="true">
            <span class="icon-close2"></span>
          </a>
        </div>
      </div>
      <div class="site-mobile-menu-body">
        <ul class="site-nav-wrap">
          <li><a href="<?php echo APPURL; ?>">Home</a></li>
          <li><a href="<?php echo APPURL; ?>/about.php">About</a></li>
          <li><a href="<?php echo APPURL; ?>/contact.php">Contact</a></li>
          <li><a href="<?php echo APPURL; ?>/workers.php">Workers</a></li>
          <li><a href="<?php echo APPURL; ?>/companies.php">Companies</a></li>

          <?php if(isset($_SESSION['username'])) : ?>
            <?php if(isset($_SESSION['type']) && $_SESSION['type'] == "Company") : ?>
              <li><a href="<?php echo APPURL; ?>/jobs/post-job.php">Post a Job</a></li>
            <?php endif; ?>
            <li class="has-children">
              <a href="#"><?php echo $_SESSION['username']; ?></a>
              <ul>
                <li><a href="<?php echo APPURL; ?>/users/public-profile.php?id=<?php echo $_SESSION['id']; ?>">Public profile</a></li>
                <li><a href="<?php echo APPURL; ?>/auth/logout.php">Logout</a></li>
              </ul>
            </li>
          <?php else: ?>
            <li><a href="<?php echo APPURL; ?>/auth/login.php">Log In</a></li>
            <li><a href="<?php echo APPURL; ?>/auth/register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <!-- ✅ DESKTOP NAVBAR -->
    <header class="site-navbar mt-3">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="site-logo col-6">
            <a href="<?php echo APPURL; ?>">PalJob</a>
          </div>

          <nav class="mx-auto site-navigation">
            <ul class="site-menu d-inline d-xl-block ml-0 pl-0">
              <li><a href="<?php echo APPURL; ?>" class="nav-link active">Home</a></li>
              <li><a href="<?php echo APPURL; ?>/about.php">About</a></li>
              <li><a href="<?php echo APPURL; ?>/contact.php">Contact</a></li>
              <li><a href="<?php echo APPURL; ?>/workers.php">Workers</a></li>
              <li><a href="<?php echo APPURL; ?>/companies.php">Companies</a></li>

              <?php if(isset($_SESSION['username'])) : ?>
                <?php if(isset($_SESSION['type']) && $_SESSION['type'] == "Company") : ?>
                  <li><a href="<?php echo APPURL; ?>/jobs/post-job.php">Post a Job</a></li>
                <?php endif; ?>
                <li class="has-children">
                  <a href="#"><?php echo $_SESSION['username']; ?></a>
                  <ul class="dropdown">
                    <li><a href="<?php echo APPURL; ?>/users/public-profile.php?id=<?php echo $_SESSION['id']; ?>">Public profile</a></li>
                    <li><a href="<?php echo APPURL; ?>/auth/logout.php">Logout</a></li>
                  </ul>
                </li>
              <?php else: ?>
                <li><a href="<?php echo APPURL; ?>/auth/login.php">Log In</a></li>
                <li><a href="<?php echo APPURL; ?>/auth/register.php">Register</a></li>
              <?php endif; ?>
            </ul>
          </nav>

          <!-- ✅ BURGER ICON -->
          <div class="site-burger-menu right-cta-menu d-xl-none">
            <a href="#" class="site-menu-toggle js-menu-toggle" aria-label="Menu" aria-expanded="false">
              <span class="icon-menu"></span>
            </a>
          </div>
        </div>
      </div>
    </header>
  </div><!-- /.site-wrap -->


  <!-- ✅ MOBILE MENU SCRIPT -->
  <script>
  (function () {
    const body = document.body;
    const panel = document.querySelector('.site-mobile-menu');
    const toggles = document.querySelectorAll('.js-menu-toggle');

    function openMenu() {
      body.classList.add('offcanvas-menu');
      panel?.setAttribute('aria-hidden', 'false');
      toggles.forEach(t => t.setAttribute('aria-expanded', 'true'));
    }
    function closeMenu() {
      body.classList.remove('offcanvas-menu');
      panel?.setAttribute('aria-hidden', 'true');
      toggles.forEach(t => t.setAttribute('aria-expanded', 'false'));
    }

    toggles.forEach(t => t.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      body.classList.contains('offcanvas-menu') ? closeMenu() : openMenu();
    }));

    document.addEventListener('click', (e) => {
      if (!body.classList.contains('offcanvas-menu')) return;
      const inside = e.target.closest('.site-mobile-menu');
      const isToggle = e.target.closest('.js-menu-toggle');
      if (!inside && !isToggle) closeMenu();
    });

    // ✅ Allow links to navigate properly
    document.querySelectorAll('.site-mobile-menu a').forEach(link => {
      link.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href && href !== '#' && !href.startsWith('javascript')) {
          e.preventDefault();
          closeMenu();
          setTimeout(() => {
            window.location.href = href;
          }, 200); // smooth transition
        }
      });
    });

    // ✅ Dropdown toggle for mobile
    document.querySelectorAll('.site-mobile-menu .has-children').forEach(li => {
      const trigger = li.querySelector(':scope > a');
      const submenu = li.querySelector(':scope > ul');
      if (trigger && submenu) {
        submenu.style.display = 'none';
        trigger.addEventListener('click', (e) => {
          e.preventDefault();
          submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        });
      }
    });
  })();
  </script>

  </body>
</html>
