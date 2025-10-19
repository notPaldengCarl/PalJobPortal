<?php require "../layouts/header.php"; ?>
<?php require "../../config/config.php"; ?>
<?php
if (!isset($_SESSION['adminname'])) {
  echo '<div class="alert alert-danger">Not authorized. Redirecting...</div>';
  echo '<script>window.location.href="'.ADMINURL.'/admins/login-admins.php";</script>';
  exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  $_SESSION['flash_error'] = 'Invalid admin id.';
  echo '<div class="alert alert-danger">Invalid admin id. Redirecting...</div>';
  echo '<script>window.location.href="'.ADMINURL.'/admins/admins.php";</script>';
  exit;
}

$errors = ['adminname'=>'','email'=>'','password'=>'','general'=>''];
$old = ['adminname'=>'','email'=>''];


if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
$csrf_token = $_SESSION['csrf_token'];


try {
  $stmt = $conn->prepare("SELECT id, adminname, email FROM admins WHERE id = :id LIMIT 1");
  $stmt->execute([':id'=>$id]);
  $current = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$current) {
    $_SESSION['flash_error'] = 'Admin not found.';
    echo '<div class="alert alert-danger">Admin not found. Redirecting...</div>';
    echo '<script>window.location.href="'.ADMINURL.'/admins/admins.php";</script>';
    exit;
  }
 
  $old['adminname'] = htmlspecialchars($current['adminname'], ENT_QUOTES, 'UTF-8');
  $old['email'] = htmlspecialchars($current['email'], ENT_QUOTES, 'UTF-8');
} catch (Throwable $e) {
  $_SESSION['flash_error'] = 'Could not load admin.';
  echo '<div class="alert alert-danger">Could not load admin. Redirecting...</div>';
  echo '<script>window.location.href="'.ADMINURL.'/admins/admins.php";</script>';
  exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
  $adminname = trim($_POST['adminname'] ?? '');
  $emailRaw = trim($_POST['email'] ?? '');
  $email = mb_strtolower($emailRaw, 'UTF-8'); 
  $password = $_POST['password'] ?? ''; 
  $token = $_POST['csrf_token'] ?? '';

  $old['adminname'] = htmlspecialchars($adminname, ENT_QUOTES, 'UTF-8');
  $old['email'] = htmlspecialchars($emailRaw, ENT_QUOTES, 'UTF-8');

  if (!hash_equals($_SESSION['csrf_token'], $token)) {
    $errors['general'] = 'Invalid request. Please try again.';
  }
  if ($adminname === '') { $errors['adminname'] = 'Username is required.'; }
  if ($email === '') { $errors['email'] = 'Email is required.'; }
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Enter a valid email.'; }
  if ($password !== '' && strlen($password) < 6) { $errors['password'] = 'Password must be at least 6 characters.'; }

  if (!$errors['adminname'] && !$errors['email'] && !$errors['password'] && !$errors['general']) {
    try {

      $chk = $conn->prepare("SELECT 1 FROM admins WHERE LOWER(email) = :email AND id <> :id");
      $chk->execute([':email'=>$email, ':id'=>$id]);
      if ($chk->fetch()) {
        $errors['email'] = 'Email is already taken.';
      } else {
        if ($password !== '') {
          $upd = $conn->prepare("UPDATE admins SET adminname=:adminname, email=:email, mypassword=:pwd WHERE id=:id");
          $upd->execute([
            ':adminname'=>$adminname,
            ':email'=>$email,
            ':pwd'=>password_hash($password, PASSWORD_DEFAULT),
            ':id'=>$id
          ]);
        } else {
          $upd = $conn->prepare("UPDATE admins SET adminname=:adminname, email=:email WHERE id=:id");
          $upd->execute([
            ':adminname'=>$adminname,
            ':email'=>$email,
            ':id'=>$id
          ]);
        }

        $_SESSION['flash_success'] = 'Admin updated successfully.';
        echo '<div class="alert alert-success">Admin updated successfully. Redirecting...</div>';
        echo '<script>window.location.href="'.ADMINURL.'/admins/admins.php";</script>';
        exit;
      }
    } catch (Throwable $e) {
      $errors['general'] = 'Could not update admin. Please try again.';
    }
  }
}
?>
<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-5 d-inline">Edit Admin</h5>

        <?php if ($errors['general']): ?>
          <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
        <?php endif; ?>

        <form method="POST" action="edit-admin.php?id=<?php echo (int)$id; ?>" novalidate>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

          <div class="form-group">
            <label for="emailInp">Email</label>
            <input type="email" name="email" id="emailInp" class="form-control <?php echo $errors['email']?'is-invalid':''; ?>" value="<?php echo $old['email']; ?>" required>
            <?php if ($errors['email']): ?><div class="invalid-feedback"><?php echo $errors['email']; ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label for="nameInp">Username</label>
            <input type="text" name="adminname" id="nameInp" class="form-control <?php echo $errors['adminname']?'is-invalid':''; ?>" value="<?php echo $old['adminname']; ?>" required>
            <?php if ($errors['adminname']): ?><div class="invalid-feedback"><?php echo $errors['adminname']; ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label for="passInp">New Password (optional)</label>
            <input type="password" name="password" id="passInp" class="form-control <?php echo $errors['password']?'is-invalid':''; ?>" placeholder="Leave blank to keep current">
            <?php if ($errors['password']): ?><div class="invalid-feedback"><?php echo $errors['password']; ?></div><?php endif; ?>
          </div>

          <button type="submit" name="submit" class="btn btn-primary mb-4">Save</button>
          <a href="<?php echo ADMINURL; ?>/admins/admins.php" class="btn btn-outline-secondary mb-4">Cancel</a>
        </form>

      </div>
    </div>
  </div>
</div>
<?php require "../layouts/footer.php"; ?>
