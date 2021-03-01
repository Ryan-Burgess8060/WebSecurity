<!--
Christopher Burgess
2/28/2021
nav.php
-->
<?php require "database.php" ?>
<?php
	if ($_SESSION['Admin'] == "True") {
		echo '<ul><li><a href="index.php">Home</a></li><li><a href="logout.php">Logout</a></li><li><a href="admin.php">Admin</a></li></ul>';
	} elseif (isset($_SESSION['Username'])) {
		echo '<ul><li><a href="index.php">Home</a></li><li><a href="logout.php">Logout</a></li></ul>';
	} else {
		echo '<ul><li><a href="index.php">Home</a></li><li><a href="login.php">Login</a></li><li><a href="register.php">Register</a></li></ul>';
	}
?>