<?php
require "partials/header.php";
require "config/config.php";

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    if ($q !== '') {
        $sql = "SELECT id, username, title, img, bio, email FROM users
                WHERE type = 'Company' AND (username LIKE :q OR title LIKE :q OR bio LIKE :q OR email LIKE :q)
                ORDER BY username ASC LIMIT 200";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':q' => "%{$q}%"]);
    } else {
        $stmt = $conn->query("SELECT id, username, title, img, bio, email FROM users WHERE type='Company' ORDER BY username ASC LIMIT 200");
    }
    $allCompanies = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (Exception $e) {
    $allCompanies = [];
}
?>
<section class="section-hero overlay inner-page bg-image" style="background-image: url('<?php echo APPURL; ?>/images/4k.jpg');" id="home-section">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <h1 class="text-white font-weight-bold">Companies</h1>
        <div class="custom-breadcrumbs">
          <a href="<?php echo APPURL; ?>/index.php">Home</a> <span class="mx-2 slash">/</span>
          <span class="text-white"><strong>Companies</strong></span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="site-section" id="home-section">
  <div class="container">
    <div class="row mb-4 align-items-center">
      <div class="col-md-6 position-relative">
        <form method="get" class="form-inline" onsubmit="">
          <div class="input-group w-100">
            <input id="searchInputComp" name="q" value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="search" placeholder="Search companies by name, title, bio or email..." aria-label="Search" autocomplete="off">
            <div class="input-group-append">
              <button class="btn btn-primary" type="submit">Search</button>
            </div>
          </div>
        </form>

        <div id="liveSuggestionsComp" class="list-group position-absolute w-100" style="z-index:1050; display:none; max-height:320px; overflow:auto;"></div>
      </div>
      <div class="col-md-6 text-right text-muted">
        <?php if ($q !== ''): ?>
          Showing results for: <strong><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></strong>
        <?php else: ?>
          Showing all companies
        <?php endif; ?>
      </div>
    </div>

    <div class="row">
      <?php if (empty($allCompanies)): ?>
        <div class="col-12">
          <div class="alert alert-secondary">No companies found.</div>
        </div>
      <?php else: ?>
        <?php foreach ($allCompanies as $company): ?>
          <div class="col-6 col-md-4 mb-4">
            <div class="card h-100">
              <div class="card-body d-flex flex-column align-items-center text-center">
                <img src="<?php echo APPURL; ?>/users/user-images/<?php echo htmlspecialchars($company->img ?: 'default.png', ENT_QUOTES, 'UTF-8'); ?>" alt="" class="rounded-circle mb-2" style="width:88px;height:88px;object-fit:cover;">
                <h5 class="card-title mb-1"><?php echo htmlspecialchars($company->username, ENT_QUOTES, 'UTF-8'); ?></h5>
                <small class="text-muted d-block mb-2"><?php echo htmlspecialchars($company->title ?: '', ENT_QUOTES, 'UTF-8'); ?></small>
                <p class="card-text small text-truncate" style="max-width:220px;"><?php echo htmlspecialchars(mb_strimwidth((string)($company->bio ?? ''), 0, 80, '…', 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?></p>
                <a class="btn btn-sm btn-outline-primary mt-auto" href="<?php echo APPURL; ?>/users/public-profile.php?id=<?php echo (int)$company->id; ?>">View profile</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require "partials/footer.php"; ?>

<script>
(function(){
  var input = document.getElementById('searchInputComp');
  var box = document.getElementById('liveSuggestionsComp');
  var timeout = null;

  function hideBox(){ box.style.display = 'none'; box.innerHTML = ''; }

  input.addEventListener('input', function(){
    var q = this.value.trim();
    if (timeout) clearTimeout(timeout);
    if (q.length < 2) { hideBox(); return; }
    timeout = setTimeout(function(){
      fetch('<?php echo APPURL; ?>/ajax/search-profiles.php?type=Company&q=' + encodeURIComponent(q))
        .then(resp => resp.json())
        .then(data => {
          if (!data || !data.length) { hideBox(); return; }
          box.innerHTML = '';
          data.forEach(function(item){
            var a = document.createElement('a');
            a.href = item.url;
            a.className = 'list-group-item list-group-item-action d-flex align-items-center';
            a.innerHTML = '<img src="' + item.img + '" style="width:44px;height:44px;object-fit:cover;border-radius:50%;margin-right:10px;">' +
                          '<div><strong>' + escapeHtml(item.username) + '</strong><br><small class="text-muted">' + escapeHtml(item.email) + (item.title ? ' • ' + escapeHtml(item.title) : '') + '</small></div>';
            box.appendChild(a);
          });
          box.style.display = 'block';
        })
        .catch(()=> hideBox());
    }, 240);
  });

  document.addEventListener('click', function(e){
    if (!box.contains(e.target) && e.target !== input) hideBox();
  });

  function escapeHtml(s){ return String(s).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; }); }
})();
</script>
