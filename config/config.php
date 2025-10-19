
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

    try {
        
        $host = "localhost";

        $dbname = "paljob";
    
        $user = "root";
    
        $pass = "";
    
    
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch(PDOException $e) {
        echo $e->getMessage();
    }
   
    // if($conn == true) {
    //     echo "connected successfully";
    // } else {
    //     echo "err";
    // }




