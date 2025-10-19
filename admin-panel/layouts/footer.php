<?php
// layouts/footer.php

// Respect the no-render guard set by logic endpoints
if (!defined('RENDER_LAYOUT')) { define('RENDER_LAYOUT', true); }
if (RENDER_LAYOUT !== true) { return; }
?>
  </div><!-- /.container -->
</div><!-- /#wrapper -->

<script>
  (function(){
    var btn = document.getElementById('adminMenuToggle');
    if (!btn) return;
    btn.addEventListener('click', function(e){
      e.preventDefault();
      document.body.classList.toggle('admin-offcanvas');
    });
    document.addEventListener('click', function(e){
      if (!document.body.classList.contains('admin-offcanvas')) return;
      var inside = e.target.closest('.navbar-nav.side-nav') || e.target.closest('#adminMenuToggle');
      if (!inside) document.body.classList.remove('admin-offcanvas');
    });
  })();
</script>

</body>
</html>
