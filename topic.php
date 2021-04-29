<?php 
require "cookie.php";
?>
<!DOCTYPE html>
<html lang="en-US">
<!--
Christopher Burgess
4/28/2021
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
			//myDBconnection came from lab 14 from Hawkin's Web Programming class. Cannot get exact link since lab dropboxes are closed. https://georgiasouthern.desire2learn.com/d2l/home/539061 
			$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
			
			$query = "SELECT Username, Date, Title, Text, Image FROM topics WHERE ID = :id;";
			$dbquery = $myDBconnection -> prepare($query);
			$dbquery -> bindValue(':id', $t);
			$dbquery -> execute();
			$results = $dbquery -> fetchAll();
			
			if ($results != "") {
				foreach ($results as &$arr) {
				?>
					<h2><?php echo $arr['Title']; ?></h2>
					<h3><?php echo "Posted by " . $arr['Username'] . " on " . $arr['Date']; ?></h3>
					<p><?php echo $arr['Text']; ?></p>
					<img src="images/<?php echo $arr['Image']; ?>" alt="Topic Image">
				<?php } 
			} else {
				echo "<p>Sorry! This topic does not exist!</p>";
			}
			?>
	</main>
</body>
</html>