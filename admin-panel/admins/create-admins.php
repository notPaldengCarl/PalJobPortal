<?php require "../layouts/header.php"; ?>           
<?php require "../../config/config.php"; ?>
<?php 

if(!isset($_SESSION['adminname'])) {
  echo '<div class="alert alert-danger">Not authorized. Redirecting...</div>';
  echo '<script>window.location.href="'.ADMINURL.'/admins/login-admins.php";</script>';
  exit;
}

$errors = ['adminname'=>'','email'=>'','password'=>'','general'=>''];
$old = ['adminname'=>'','email'=>''];

if(isset($_POST['submit'])) {

  $adminname = trim($_POST['adminname'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  $old['adminname'] = htmlspecialchars($adminname, ENT_QUOTES, 'UTF-8');
  $old['email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

  if ($adminname === '' || $email === '' || $password === '') {
    echo "<script>alert('some inputs are empty')</script>";
  }

  if ($adminname === '') { $errors['adminname'] = 'Username is required.'; }
  if ($email === '') { $errors['email'] = 'Email is required.'; }
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Enter a valid email.'; }
  if ($password === '') { $errors['password'] = 'Password is required.'; }
  elseif (strlen($password) < 6) { $errors['password'] = 'Password must be at least 6 characters.'; }


  if (!$errors['adminname'] && !$errors['email'] && !$errors['password']) {
    try {
      $emailNorm = mb_strtolower($email, 'UTF-8');
      $check = $conn->prepare("SELECT 1 FROM admins WHERE LOWER(email) = :email LIMIT 1");
      $check->execute([':email'=>$emailNorm]);

      if ($check->fetch()) {
        $errors['email'] = 'Email is already taken.';
      } else {
        $insert = $conn->prepare("INSERT INTO admins (adminname, email, mypassword) 
                                  VALUES (:adminname, :email, :mypassword)");
        $insert->execute([
          ':adminname' => $adminname,
          ':email' => $emailNorm,
          ':mypassword' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $_SESSION['flash_success'] = 'Admin created successfully.';
        echo '<div class="alert alert-success">Admin created successfully. Redirecting...</div>';
        echo '<script>window.location.href="'.ADMINURL.'/admins/admins.php";</script>';
        exit;
      }
    } catch (Throwable $e) {
      $errors['general'] = 'Could not create admin. Please try again.';
  
    }
  }
}

?>        
<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-5 d-inline">Create Admins</h5>

        <?php if ($errors['general']): ?>
          <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
        <?php endif; ?>

        <form method="POST" action="create-admins.php">
          <div class="form-outline mb-4 mt-4">
            <input type="email" name="email" id="formEmail" class="form-control <?php echo $errors['email']?'is-invalid':''; ?>" placeholder="Email" value="<?php echo $old['email']; ?>" />
            <?php if ($errors['email']): ?><div class="invalid-feedback d-block"><?php echo $errors['email']; ?></div><?php endif; ?>
          </div>

          <div class="form-outline mb-4">
            <input type="text" name="adminname" id="formUser" class="form-control <?php echo $errors['adminname']?'is-invalid':''; ?>" placeholder="Username" value="<?php echo $old['adminname']; ?>" />
            <?php if ($errors['adminname']): ?><div class="invalid-feedback d-block"><?php echo $errors['adminname']; ?></div><?php endif; ?>
          </div>

          <div class="form-outline mb-4">
            <input type="password" name="password" id="formPass" class="form-control <?php echo $errors['password']?'is-invalid':''; ?>" placeholder="Password" />
            <?php if ($errors['password']): ?><div class="invalid-feedback d-block"><?php echo $errors['password']; ?></div><?php endif; ?>
          </div>
          
          <button type="submit" name="submit" class="btn btn-primary  mb-4 text-center">Create</button>
        </form>

      </div>
    </div>
  </div>
</div>
<?php require "../layouts/footer.php"; ?>
