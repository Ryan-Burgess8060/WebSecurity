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
			echo "<p>Welcome, " . $_SESSION['Username'] . "</p>";
		} else {
			echo "<p>Please log in or register.</p>";
		}
		?>
		<h1>Forum Topics</h1>
		<ul>
			<li><a href="topic.php?t=1">How to get better at games.</a></li>
			<li><a href="topic.php?t=2">I need help with this project!</a></li>
			<li><a href="topic.php?t=3">Destress: A Guide to Relaxing</a></li>
			<li><a href="topic.php?t=4">What even is this forum about?</a></li>
			<li><a href="topic.php?t=5">Wow I hate it here!</a></li>
		</ul>
	</main>
</body>
</html>