<?php 
require "cookie.php";
if ($Admin == True) {
} else {
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
		//myDBconnection came from lab 14 from Hawkin's Web Programming class. Cannot get exact link since lab dropboxes are closed. https://georgiasouthern.desire2learn.com/d2l/home/539061 
		$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
		$query = "SELECT Username FROM accounts WHERE Admin = 'No';";
		$dbquery = $myDBconnection -> prepare($query);
		$dbquery -> execute();
		$results = $dbquery -> fetchAll();
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
			$query = 'DELETE FROM accounts WHERE Username = :user AND Admin = "No";';
			$dbquery = $myDBconnection -> prepare($query);
			$dbquery -> bindValue(':user', $arr['Username']); 
			$dbquery -> execute();
			header('Location:admin.php');
		}
		}
		?>
	</main>
</body>
</html>