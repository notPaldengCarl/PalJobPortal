<?php require "../layouts/header.php"; ?>           
<?php require "../../config/config.php"; ?>

<?php
if (!isset($_SESSION['adminname'])) {
  echo '<div class="alert alert-danger">Not authorized. Redirecting...</div>';
  echo '<script>window.location.href="'.ADMINURL.'/admins/login-admins.php";</script>';
  exit;
}

$error = '';
$nameOld = '';

if (isset($_POST['submit'])) {
  $name = trim($_POST['name'] ?? '');
  $nameOld = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

  if ($name === '') {
    echo "<script>alert('input is empty');</script>";
    $error = 'Name is required.';
  } elseif (mb_strlen($name) < 2) {
    $error = 'Name must be at least 2 characters.';
  } else {
    try {
      // Prevent duplicates (case-insensitive)
      $check = $conn->prepare("SELECT 1 FROM categories WHERE LOWER(name) = :n LIMIT 1");
      $check->execute([':n'=>mb_strtolower($name, 'UTF-8')]);
      if ($check->fetch()) {
        $error = 'Category already exists.';
      } else {
        $insert = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
        $insert->execute([':name'=>$name]);

        $_SESSION['flash_success'] = 'Category created.';
        // JS redirect to avoid header() after layout output
        echo '<div class="alert alert-success">Category created. Redirecting...</div>';
        echo '<script>window.location.href="'.ADMINURL.'/categories-admins/show-categories.php";</script>';
        exit;
      }
    } catch (Throwable $e) {
      $error = 'Could not create category. Please try again.';
      // For debugging only:
      // $error .= ' ['.$e->getMessage().']';
    }
  }
}
?>
<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-5 d-inline">Create Categories</h5>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="create-category.php">
          <div class="form-outline mb-4 mt-4">
            <input type="text" name="name" id="formCategoryName" class="form-control <?php echo $error?'is-invalid':''; ?>" placeholder="name" value="<?php echo $nameOld; ?>" />
            <?php if ($error): ?><div class="invalid-feedback d-block"><?php echo $error; ?></div><?php endif; ?>
          </div>

          <button type="submit" name="submit" class="btn btn-primary mb-4 text-center">create</button>
        </form>

      </div>
    </div>
  </div>
</div>
<?php require "../layouts/footer.php"; ?>
