<?php
require "../config/config.php";


// Already logged in? Redirect BEFORE output
if (!empty($_SESSION['username'])) {
  header("Location:: /index.php");
  exit;
}

// Handle POST BEFORE including header/HTML
if (isset($_POST['submit'])) {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($email === '' || $password === '') {
    $_SESSION['flash_error'] = "Some inputs are empty.";
    header("Location: login.php");
    exit;
  }

  $stmt = $conn->prepare("SELECT id, username, email, mypassword, img, cv, type FROM users WHERE email = :email LIMIT 1");
  $stmt->execute([':email' => $email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['mypassword'])) {
    // Regenerate session ID only AFTER session_start()
    if (session_status() === PHP_SESSION_ACTIVE) {
      session_regenerate_id(true);
    }

    $_SESSION['username'] = $user['username'];
    $_SESSION['id'] = (int)$user['id'];
    $_SESSION['type'] = $user['type'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['image'] = $user['img'] ?? null;
    $_SESSION['cv'] = $user['cv'] ?? null;

    header("Location: " . APPURL . "/index.php");
    exit;
  } else {
    $_SESSION['flash_error'] = "Invalid user";
    header("Location: login.php");
    exit;
  }
}


require_once "../partials/header.php";

// Flash UI
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);
?>

<section class="section-hero overlay inner-page bg-image" style="background-image: url('<?php echo APPURL; ?>/images/4k.jpg');" id="home-section">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <h1 class="text-white font-weight-bold">Log In</h1>
        <div class="custom-breadcrumbs">
          <a href="<?php echo APPURL; ?>/index.php">Home</a> <span class="mx-2 slash">/</span>
          <span class="text-white"><strong>Log In</strong></span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="site-section">
  <div class="container">
    <?php if ($flash_error): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <div class="row">
      <div class="col-md-12">
        <form action="login.php" class="p-4 border rounded" method="POST" autocomplete="on">
          <div class="row form-group">
            <div class="col-md-12 mb-3 mb-md-0">
              <label class="text-black" for="email">Email</label>
              <input type="email" id="email" class="form-control" placeholder="Email address" name="email" required>
            </div>
          </div>
          <div class="row form-group mb-4">
            <div class="col-md-12 mb-3 mb-md-0">
              <label class="text-black" for="password">Password</label>
              <input type="password" id="password" class="form-control" placeholder="Password" name="password" required>
            </div>
          </div>
          <div class="row form-group">
            <div class="col-md-12">
              <input type="submit" name="submit" value="Log In" class="btn px-4 btn-primary text-white">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once "../partials/footer.php"; ?>
