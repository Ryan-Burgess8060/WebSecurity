<?php 
session_start();
if ($_SESSION['Admin'] == "Yes") {
} else {
	require_once "logging.php";
	auditlog($myDBconnection, "Unauthorized User in Admin Page", 2, "NULL", "NULL", "NULL", "NULL");
	session_destroy();
	header('Location:index.php');
}
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
	<?php
		require "nav.php";
	?>
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
			$query = "SELECT Username FROM accounts WHERE Admin = 'No';";
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
				$query = 'DELETE FROM accounts WHERE Username = :user;';
				$dbquery = $myDBconnection -> prepare($query);
				$dbquery -> bindValue(':user', $arr['Username']); 
				$dbquery -> execute();
				header('Location:admin.php');
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