<?php
require "../layouts/header.php";
require "../../config/config.php";

if (!isset($_SESSION['adminname'])) {
  echo '<div class="alert alert-danger">Not authorized. Redirecting...</div>';
  echo '<script>window.location.href="'.ADMINURL.'/admins/login-admins.php";</script>';
  exit;
}


$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;


$whereSql = '';
$params = [];
if ($q !== '') {
  $whereSql = "WHERE adminname LIKE :q OR email LIKE :q";
  $params[':q'] = '%' . $q . '%';
}


$countSql = "SELECT COUNT(*) FROM admins " . $whereSql;
$countStmt = $conn->prepare($countSql);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));


if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }


$listSql = "SELECT id, adminname, email FROM admins $whereSql ORDER BY id DESC LIMIT :limit OFFSET :offset";
$listStmt = $conn->prepare($listSql);
foreach ($params as $k=>$v) { $listStmt->bindValue($k, $v, PDO::PARAM_STR); }
$listStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$listStmt->execute();
$admins = $listStmt->fetchAll(PDO::FETCH_OBJ);

// Flash
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Helper to keep q param in pagination links
function qs(array $extra = []) {
  $base = $_GET;
  foreach ($extra as $k=>$v) {
    if ($v === null) unset($base[$k]);
    else $base[$k] = $v;
  }
  $qstr = http_build_query($base);
  return $qstr ? ('?'.$qstr) : '';
}
?>

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">

        <div class="d-flex align-items-center justify-content-between mb-3">
          <h5 class="card-title mb-0">Admins</h5>
          <a href="<?php echo ADMINURL; ?>/admins/create-admins.php" class="btn btn-primary">Create Admins</a>
        </div>

        <!-- Server-side search (keeps pagination + DB filtering) -->

        <!-- Client-side instant filter for the current page -->
        <div class="mb-3">
          <div class="input-group">
            <span class="input-group-text"></span>
            <input id="adminSearch" type="text" class="form-control" placeholder="Type to filter ID, username, or email (client)">
          </div>
        </div>

        <?php if ($flash_success): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($flash_success, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($flash_error): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
          <table id="adminsTable" class="table table-hover align-middle">
            <thead>
              <tr>
                <th scope="col" style="width:80px;">ID</th>
                <th scope="col">Admin Name</th>
                <th scope="col">Email</th>
                <th scope="col" style="width:220px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($admins): ?>
                <?php foreach ($admins as $admin): ?>
                  <tr>
                    <th scope="row"><?php echo (int)$admin->id; ?></th>
                    <td><?php echo htmlspecialchars($admin->adminname, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($admin->email, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="text-nowrap">
                      <a class="btn btn-sm btn-outline-primary" href="<?php echo ADMINURL; ?>/admins/edit-admin.php?id=<?php echo (int)$admin->id; ?>">Edit</a>
                      <form action="<?php echo ADMINURL; ?>/admins/delete-admin.php" method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?php echo (int)$admin->id; ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this admin?');">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-muted">
                    <?php echo $q!=='' ? 'No results for "'.htmlspecialchars($q, ENT_QUOTES, 'UTF-8').'"' : 'No admins found.'; ?>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
          <nav aria-label="Admins pagination">
            <ul class="pagination mb-0">
              <li class="page-item <?php echo $page<=1?'disabled':''; ?>">
                <a class="page-link" href="admins.php<?php echo qs(['page'=>($page-1<1?1:$page-1)]); ?>" aria-label="Previous">&laquo;</a>
              </li>
              <?php
                // Simple windowed pagination
                $start = max(1, $page - 2);
                $end = min($pages, $page + 2);
                if ($start > 1) {
                  echo '<li class="page-item"><a class="page-link" href="admins.php'.qs(['page'=>1]).'">1</a></li>';
                  if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                }
                for ($i=$start; $i<=$end; $i++) {
                  $active = $i === $page ? ' active' : '';
                  echo '<li class="page-item'.$active.'"><a class="page-link" href="admins.php'.qs(['page'=>$i]).'">'.$i.'</a></li>';
                }
                if ($end < $pages) {
                  if ($end < $pages-1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                  echo '<li class="page-item"><a class="page-link" href="admins.php'.qs(['page'=>$pages]).'">'.$pages.'</a></li>';
                }
              ?>
              <li class="page-item <?php echo $page>=$pages?'disabled':''; ?>">
                <a class="page-link" href="admins.php<?php echo qs(['page'=>($page+1>$pages?$pages:$page+1)]); ?>" aria-label="Next">&raquo;</a>
              </li>
            </ul>
          </nav>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<!-- Client-side filtering script -->
<script>
(function () {
  const input = document.getElementById('adminSearch');
  const table = document.getElementById('adminsTable');
  if (!input || !table) return;
  const tbody = table.querySelector('tbody');

  function normalize(s) { return (s || '').toLowerCase().trim(); }

  input.addEventListener('input', function () {
    const q = normalize(this.value);
    const rows = tbody.querySelectorAll('tr');

    rows.forEach(function (tr) {
      const idCell = tr.cells[0] ? tr.cells[0].textContent : '';
      const nameCell = tr.cells[1] ? tr.cells[1].textContent : '';
      const emailCell = tr.cells[2] ? tr.cells[2].textContent : '';
      const haystack = normalize(idCell + ' ' + nameCell + ' ' + emailCell);
      tr.style.display = haystack.indexOf(q) > -1 ? '' : 'none';
    });
  });
})();
</script>

<?php require "../layouts/footer.php"; ?>
