<?php require "../partials/header.php"; ?>
<?php require "../config/config.php"; ?>

<?php 

    if(isset($_SESSION['type']) AND $_SESSION['type'] !== "Company") {

        header("location: ".APPURL."");
        
    } 

    if(isset($_GET['id'])) {

        $id = $_GET['id'];

        $delete = $conn->prepare("DELETE FROM jobs WHERE id='$id'");
        $delete->execute();

        header("location: ".APPURL."");
    } else {
        echo "404";
    }



?>

<?php require "../partials/footer.php"; ?>
