<?php 
require "cookie.php";
setcookie("Authentication", $token, time() - 3600);
header('Location:index.php');
?>