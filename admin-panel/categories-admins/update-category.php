<?php require "../layouts/header.php"; ?>           
<?php require "../../config/config.php"; ?>

<?php 
if(!isset($_SESSION['adminname'])) {
  echo '<div class="alert alert-danger">Not authorized. Redirecting...</div>';
  echo '<script>window.location.href="'.ADMINURL.'/admins/login-admins.php";</script>';
  exit;
}

if(isset($_GET['id'])) {
  $id = (int)$_GET['id'];

  // Safe load
  $select = $conn->prepare("SELECT id, name FROM categories WHERE id = :id LIMIT 1");
  $select->execute([':id'=>$id]);
  $category = $select->fetch(PDO::FETCH_OBJ);

  if(!$category) {
    echo '<div class="alert alert-danger">Category not found. Redirecting...</div>';
    echo '<script>window.location.href="'.ADMINURL.'/categories-admins/show-categories.php";</script>';
    exit;
  }

  if(isset($_POST['submit'])) {
    if(empty($_POST['name'])) {
      echo "<script>alert('input is empty');</script>";
    } else {
      $name = trim($_POST['name']);

      $update = $conn->prepare("UPDATE categories SET name = :name WHERE id = :id");
      $update->execute([
        ':name' => $name,
        ':id' => $id
      ]);

      $_SESSION['flash_success'] = 'Category updated.';
      echo '<div class="alert alert-success">Category updated. Redirecting...</div>';
      echo '<script>window.location.href="'.ADMINURL.'/categories-admins/show-categories.php";</script>';
      exit;
    }
  }

} else {
  echo '<div class="alert alert-danger">Invalid request. Redirecting...</div>';
  echo '<script>window.location.href="'.ADMINURL.'/categories-admins/show-categories.php";</script>';
  exit;
}
?>
<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-5 d-inline">Update Categories</h5>
        <form method="POST" action="update-category.php?id=<?php echo (int)$id; ?>">
          <div class="form-outline mb-4 mt-4">
            <input type="text" value="<?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>" name="name" id="form2Example1" class="form-control" placeholder="name" />
          </div>
          <button type="submit" name="submit" class="btn btn-primary mb-4 text-center">update</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require "../layouts/footer.php"; ?>


