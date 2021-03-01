<?php 
setcookie("Authentication", "", time() - 3600);
header('Location:index.php');
?>