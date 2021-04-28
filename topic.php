<?php 
require "cookie.php";
?>
<!DOCTYPE html>
<html lang="en-US">
<!--
Christopher Burgess
2/28/2021
topic.php
-->
<head>
	<title>Forum Topic</title>
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
		$t = $_GET["t"];
		require_once 'database.php'; 
		try {
			$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
		} catch (PDOException $e) {
			$error_message = $e->getMessage();
			print $error_message . "<br>";
		}
		try {
			$query = "SELECT Username, Date, Title, Text, Image FROM topics WHERE ID = :id;";
			$dbquery = $myDBconnection -> prepare($query);
			$dbquery -> bindValue(':id', $t);
			$dbquery -> execute();
			$results = $dbquery -> fetchAll();
		} catch (PDOException $e) {
			$error_message = $e -> getMessage();
			echo $error_message . "<br>";
		}
			if ($results != "") {
				foreach ($results as &$arr) {
				?>
					<h2><?php echo $arr['Title']; ?></h2>
					<h3><?php echo "Posted by " . $arr['Username'] . "on" . $arr['Date']; ?></h3>
					<p><?php echo $arr['Text']; ?></p>
					<img src="<?php echo $arr['Image']; ?>" alt="Topic Image">
				<?php } 
			} else {
				echo "<p>Sorry! This topic does not exist!</p>";
			}
			?>
	</main>
</body>
</html>