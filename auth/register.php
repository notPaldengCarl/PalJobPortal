<?php
// Optional guard: buffer accidental output to prevent "headers already sent"
ob_start();

require "../config/config.php";


if (isset($_SESSION['username'])) {
  header("Location: " . APPURL);
  exit;
}

// Handle registration POST BEFORE including header
if (isset($_POST['submit'])) {
  $username   = trim($_POST['username'] ?? '');
  $email      = trim($_POST['email'] ?? '');
  $password   = $_POST['password'] ?? '';
  $repassword = $_POST['re-password'] ?? '';
  $type       = trim($_POST['type'] ?? '');
  $img        = 'web-coding.png';

  // Basic presence validation
  if ($username === '' || $email === '' || $password === '' || $repassword === '' || $type === '') {
    $_SESSION['flash_error'] = "Some inputs are empty.";
    header("Location: register.php");
    exit;
  }

  // Length checks
  if (strlen($email) > 255 || strlen($username) > 30) {
    $_SESSION['flash_error'] = "Email or username is too long.";
    header("Location: register.php");
    exit;
  }

  // Password match
  if ($password !== $repassword) {
    $_SESSION['flash_error'] = "Passwords do not match.";
    header("Location: register.php");
    exit;
  }

  // Uniqueness check (prepared)
  $validate = $conn->prepare("SELECT 1 FROM users WHERE email = :email OR username = :username LIMIT 1");
  $validate->execute([
    ':email'    => $email,
    ':username' => $username
  ]);

  if ($validate->fetchColumn()) {
    $_SESSION['flash_error'] = "Email or username is already taken.";
    header("Location: register.php");
    exit;
  }

  // Insert user (prepared)
  $insert = $conn->prepare("
    INSERT INTO users (username, email, mypassword, img, type)
    VALUES (:username, :email, :mypassword, :img, :type)
  ");
  $insert->execute([
    ':username'   => $username,
    ':email'      => $email,
    ':mypassword' => password_hash($password, PASSWORD_DEFAULT),
    ':img'        => $img,
    ':type'       => $type,
  ]);

  $_SESSION['flash_success'] = "Account created. Please log in.";
  header("Location: login.php");
  exit;
}

// Include header only AFTER redirects/POST handling
require "../partials/header.php";

// Flash messages for UI feedback
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error   = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<!-- HOME -->
<section class="section-hero overlay inner-page bg-image" style="background-image: url('../images/4k.jpg');" id="home-section">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <h1 class="text-white font-weight-bold">Register</h1>
        <div class="custom-breadcrumbs">
          <a href="<?php echo APPURL; ?>">Home</a> <span class="mx-2 slash">/</span>
          <span class="text-white"><strong>Register</strong></span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="site-section">
  <div class="container">
    <?php if ($flash_success): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($flash_success, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-12 mb-5">
        <form action="register.php" class="p-4 border rounded bg-white needs-validation" method="POST" novalidate>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required minlength="3" maxlength="30" autocomplete="username">
            <label for="username">Username</label>
            <div class="invalid-feedback">Please enter a username (3–30 characters).</div>
          </div>

          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required maxlength="255" autocomplete="email">
            <label for="email">Email</label>
            <div class="invalid-feedback">Please enter a valid email address.</div>
          </div>

          <div class="mb-3">
            <label class="text-black mb-2" for="user-type">User Type</label>
            <select name="type" class="selectpicker border rounded form-select" id="user-type" data-style="btn-black" data-width="100%" data-live-search="true" title="Select User Type" required>
              <option value="" disabled selected>Select User Type</option>
              <option>Worker</option>
              <option>Company</option>
            </select>
            <div class="invalid-feedback d-block">Please select a user type.</div>
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required minlength="8" autocomplete="new-password">
            <label for="password">Password</label>
            <div class="invalid-feedback">Minimum 8 characters.</div>
          </div>

          <div class="form-floating mb-4">
            <input type="password" class="form-control" id="re-password" name="re-password" placeholder="Re-type Password" required autocomplete="new-password">
            <label for="re-password">Re-Type Password</label>
            <div class="invalid-feedback">Please retype your password.</div>
          </div>

          <div class="row form-group">
            <div class="col-md-12">
              <input type="submit" name="submit" value="Sign Up" class="btn px-4 btn-primary text-white">
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</section>

<script>
// Client-side validation hints
(() => {
  'use strict';
  const form = document.querySelector('.needs-validation');
  if (!form) return;

  form.addEventListener('submit', (event) => {
    const pwd = form.querySelector('#password');
    const repwd = form.querySelector('#re-password');
    if (pwd && repwd && pwd.value !== repwd.value) {
      repwd.setCustomValidity('Passwords do not match');
    } else if (repwd) {
      repwd.setCustomValidity('');
    }

    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add('was-validated');
  }, false);
})();
</script>

<?php
require "../partials/footer.php";

// Flush buffered output at the very end
ob_end_flush();
