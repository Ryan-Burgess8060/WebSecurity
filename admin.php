<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en-US">
<!--
Christopher Burgess
1/29/2021
admin.php
-->
<head>
	<title>Webserver Admin</title>
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
		require_once 'database.php'; 
		try {
			$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
		} catch (PDOException $e) {
			$error_message = $e->getMessage();
			print $error_message . "<br>";
		}
		try {
			$query = "SELECT Username FROM accounts;";
			$dbquery = $myDBconnection -> prepare($query);
			$dbquery -> execute();
			$results = $dbquery -> fetchAll();
		} catch (PDOException $e) {
			$error_message = $e -> getMessage();
			echo $error_message . "<br>";
		}
		foreach ($results as &$arr) {
		?>
		<ul>
		<li>
		<?php echo $arr['Username'];?> 
		<form method="post">
		<input type="submit" name="delete<?php echo $arr['Username']; ?>" value="Delete User" />
		</form>
		</li>
		</ul>
		<?php
		$d = 'delete' . $arr['Username'];
		if(isset($_POST[$d])) {
			try {
				$query = 'DELETE Username FROM accounts WHERE Username = :user;';
				$dbquery = $myDBconnection -> prepare($query);
				$dbquery -> bindValue(':user', $user); 
				$dbquery -> execute();
				$result = $dbquery -> fetch();
			} catch (PDOException $e) {
				$error_message = $e->getMessage();
				echo "<p>An error occurred while trying to delete data from the table: $error_message </p>";
			}
		}
		}
		?>
	</main>
</body>
</html>