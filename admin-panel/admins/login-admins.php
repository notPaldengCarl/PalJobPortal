<?php 
require "../layouts/header.php";          
require "../../config/config.php";  


if (isset($_SESSION['adminname'])) {
  header("Location: " . ADMINURL . "");
  exit;
}


$errors = [
  'email' => '',
  'password' => '',
  'general' => ''
];
$old = [
  'email' => ''
];


if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {


  $email = isset($_POST['email']) ? trim($_POST['email']) : '';
  $password = isset($_POST['password']) ? $_POST['password'] : '';
  $token = $_POST['csrf_token'] ?? '';

  $old['email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');


  if (!hash_equals($_SESSION['csrf_token'], $token)) {
    $errors['general'] = 'Invalid request. Please try again.';
  }


  if ($email === '') {
    $errors['email'] = 'Email is required.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Enter a valid email address.';
  }


  if ($password === '') {
    $errors['password'] = 'Password is required.';
  } elseif (strlen($password) < 6) {
    $errors['password'] = 'Password must be at least 6 characters.';
  }

  
  if ($errors['email'] === '' && $errors['password'] === '' && $errors['general'] === '') {
    try {
      $stmt = $conn->prepare("SELECT adminname, email, mypassword FROM admins WHERE email = :email LIMIT 1");
      $stmt->execute([':email' => $email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user && password_verify($password, $user['mypassword'])) {
        session_regenerate_id(true);
        $_SESSION['adminname'] = $user['adminname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header("Location: " . ADMINURL . "");
        exit;
      } else {

        $errors['general'] = 'Invalid email or password.';
      }
    } catch (Throwable $e) {
      $errors['general'] = 'Something went wrong. Please try again.';
    }
  }
}
?>

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mt-5">Login</h5>

        <?php if ($errors['general']): ?>
          <div class="alert alert-danger" role="alert"><?php echo $errors['general']; ?></div>
        <?php endif; ?>

        <form method="POST" action="login-admins.php" novalidate>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

          <div class="form-group">
            <label for="formEmail">Email</label>
            <input 
              type="email" 
              name="email" 
              id="formEmail" 
              class="form-control <?php echo $errors['email'] ? 'is-invalid' : ''; ?>" 
              placeholder="Email" 
              value="<?php echo $old['email']; ?>"
              required
            >
            <?php if ($errors['email']): ?>
              <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
          </div>

          <div class="form-group">
            <label for="formPassword">Password</label>
            <input 
              type="password" 
              name="password" 
              id="formPassword" 
              class="form-control <?php echo $errors['password'] ? 'is-invalid' : ''; ?>" 
              placeholder="Password" 
              minlength="6" 
              required
            >
            <?php if ($errors['password']): ?>
              <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
            <?php endif; ?>
          </div>

          <button type="submit" name="submit" class="btn btn-primary">Login</button>
        </form>

      </div>
    </div>
  </div>
</div>

<?php require "../layouts/footer.php"; ?>
