<!--
Christopher Burgess
2/28/2021
nav.php
-->
<?php 
	//This nav method came from lab 17 from Hawkin's Web Programming class. Cannot get exact link since lab dropboxes are closed. https://georgiasouthern.desire2learn.com/d2l/home/539061 
	require "database.php";
	if ($Admin == True) {
		echo '<ul><li><a href="index.php">Home</a></li><li><a href="logout.php">Logout</a></li><li><a href="admin.php">Admin</a></li></ul>';
	} elseif ($loggedIn == True) {
		echo '<ul><li><a href="index.php">Home</a></li><li><a href="logout.php">Logout</a></li></ul>';
	} else {
		echo '<ul><li><a href="index.php">Home</a></li><li><a href="login.php">Login</a></li><li><a href="register.php">Register</a></li></ul>';
	}
?>