<?php require "../layouts/header.php"; ?>           
<?php require "../../config/config.php"; ?>
<?php 

    if(!isset($_SESSION['adminname'])) {

      header("location: ".ADMINURL."/admins/login-admins.php");

    }

    $select = $conn->query("SELECT * FROM jobs");
    $select->execute();

    $jobs = $select->fetchAll(PDO::FETCH_OBJ);

?>
      <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-4 d-inline">Jobs</h5>
            
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Job title</th>
                    <th scope="col">Category</th>
                    <th scope="col">Company</th>
                    <th scope="col">Status</th>
                    <th scope="col">Delete</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($jobs as $job) : ?>
                  <tr>
                    <th scope="row"><?php echo $job->id; ?></th>
                    <td><?php echo $job->job_title; ?></td>
                    <td><?php echo $job->job_category; ?></td>
                    <td><?php echo $job->company_name; ?></td>
                    <?php if($job->status == 1) : ?>
                      <td><a href="<?php echo ADMINURL; ?>/jobs-admins/status-jobs.php?id=<?php echo $job->id; ?>&status=<?php echo $job->status; ?>" class="btn btn-danger  text-center ">Not Verfied</a></td>
                    <?php else : ?>

                     <td><a href="<?php echo ADMINURL; ?>/jobs-admins/status-jobs.php?id=<?php echo $job->id; ?>&status=<?php echo $job->status; ?>" class="btn btn-success  text-center ">Verified</a></td>
                    <?php endif; ?>
                     <td><a href="<?php echo ADMINURL; ?>/jobs-admins/delete-jobs.php?id=<?php echo $job->id; ?>" class="btn btn-danger  text-center ">Delete</a></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table> 
            </div>
          </div>
        </div>
      </div>



<?php require "../layouts/footer.php"; ?>           
