<?php require "../layouts/header.php"; ?>           
<?php require "../../config/config.php"; ?>
<?php 

if(!isset($_SESSION['adminname'])) {
  echo '<div class="alert alert-danger">Not authorized. Redirecting...</div>';
  echo '<script>window.location.href="'.ADMINURL.'/admins/login-admins.php";</script>';
  exit;
}

// Initial query loads all categories
$select = $conn->query("SELECT id, name FROM categories ORDER BY id DESC");
$select->execute();
$categories = $select->fetchAll(PDO::FETCH_OBJ);

?>
<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h5 class="card-title mb-0">Categories</h5>
          <a href="<?php echo ADMINURL; ?>/categories-admins/create-category.php" class="btn btn-primary">Create Category</a>
        </div>

        <!-- Search bar -->
        <div class="mb-3">
          <input id="categorySearch" type="text" class="form-control" placeholder="Search categories by ID or name...">
        </div>

        <div class="table-responsive">
          <table id="categoriesTable" class="table table-hover align-middle">
            <thead class="thead-light">
              <tr>
                <th style="width:90px;">ID</th>
                <th>Category</th>
                <th style="width:220px;" class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($categories): ?>
                <?php foreach($categories as $category): ?>
                  <tr>
                    <td class="text-muted"><?php echo (int)$category->id; ?></td>
                    <td><?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="text-right text-nowrap">
                      <a href="<?php echo ADMINURL; ?>/categories-admins/update-category.php?id=<?php echo (int)$category->id; ?>" class="btn btn-sm btn-outline-primary">Update</a>
                      <form action="<?php echo ADMINURL; ?>/categories-admins/delete-category.php" method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?php echo (int)$category->id; ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this category?');">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="3" class="text-muted text-center py-4">No categories found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Client-side filtering script -->
<script>
  (function () {
    const input = document.getElementById('categorySearch');
    const table = document.getElementById('categoriesTable');
    if (!input || !table) return;
    const tbody = table.querySelector('tbody');

    function normalize(s) {
      return (s || '').toLowerCase().trim();
    }

    input.addEventListener('input', function () {
      const q = normalize(this.value);
      const rows = tbody.querySelectorAll('tr');

      rows.forEach(function (tr) {
        // Only check ID and Category columns
        const idCell = tr.cells[0] ? tr.cells[0].textContent : '';
        const nameCell = tr.cells[1] ? tr.cells[1].textContent : '';
        const haystack = normalize(idCell + ' ' + nameCell);

        tr.style.display = haystack.indexOf(q) > -1 ? '' : 'none';
      });
    });
  })();
</script>

<?php require "../layouts/footer.php"; ?>
