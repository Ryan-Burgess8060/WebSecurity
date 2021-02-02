<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en-US">
<!--
Christopher Burgess
1/29/2021
index.php
-->
<head>
	<title>Webserver Home</title>
	<meta charset="utf-8"/>
	<!-- <link href="style.css" rel="css/stylesheet"/> -->
</head>
<body>
	<header>
	<h1>Ryan's Forums</h1>
	</header>
	<nav>
		<ul>
		<li><a href="index.php">Home</a></li>
		<li><a href="login.php">Login</a></li>
		<li><a href="register.php">Register</a></li>
		<li><a href="password.php">Forgot Password</a></li>
		<li><a href="logout.php">Logout</a></li>
		<li><a href="admin.php">Admin</a></li>
		</ul>
	</nav>
	<main>
		<?php
		if (isset($_SESSION['Username'])) {
			echo "<p>Welcome," . $_SESSION['Username'] . "</p>";
		} else {
			echo "<p>Please log in or register.</p>";
		}
		?>
		<p>This is the forum.</p>
	</main>
</body>
</html>