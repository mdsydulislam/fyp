<?php 
   session_start();
   include "db_conn.php";
   if (isset($_SESSION['email']) && isset($_SESSION['id'])) {   
?><!DOCTYPE html>
<html>
<head>
	<title>HOME</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>
<body>
      	<?php 
        if ($_SESSION['role'] == 'student') {?>
      		<!-- For Student -->
            <?php
            include("template.php");
            ?>	
      	<?php }
        else { ?>
      		<!-- for Staff -->
            <?php
            include("template.php");
            ?>
      	<?php } ?>
</body>
</html>
<?php }else{
	header("Location: index.php");
} ?>