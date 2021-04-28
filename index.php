<?php 
require "cookie.php";
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
	<?php
		require "nav.php";
	?>
	</nav>
	<main>
		<?php
		if ($loggedIn == True) {
			$query = 'SELECT Username FROM sessions WHERE Token = :token;';
			$dbquery = $myDBconnection -> prepare($query);
			$dbquery -> bindValue(':token', $token); 
			$dbquery -> execute();
			$result = $dbquery -> fetch();
			echo "<p>Welcome, " . $result['Username'] . "</p>";
			$user = $result['Username'];
		} else {
			echo "<p>Please log in or register.</p>";
		}
		?> <h1>Forum Topics</h1> <ul> <?php
		try {
			$query = "SELECT ID, Title FROM topics";
			$dbquery = $myDBconnection -> prepare($query);
			$dbquery -> execute();
			$results = $dbquery -> fetchAll();
		} catch (PDOException $e) {
			$error_message = $e -> getMessage();
			echo $error_message . "<br>";
		}
		foreach ($results as &$arr) {
		?>
			<li>
			<a href="topics.php?t=<?php echo $arr['ID']; ?>"><?php echo $arr['Title']; ?></a>
			</li>
		<?php } ?>
		</ul>
		<?php
		if ($loggedIn == True) {
		?>
		<br>
		<form action="index.php" method="post" enctype="multipart/form-data">
		<fieldset>
		<legend>Make your topics here!</legend>
			<label for="title">Title:</label>
			<input type="text" name="title" id="title" maxlength="100" required />
			<br>
			<label for="text">Text:</label>
			<input type="textarea" name="text" id="text" maxlength="2000" required />
			<br>
			<label for="image">Image:</label>
			<input type="file" id="image" name="image" />
		</fieldset>
		<input type="submit" name="Post" value="Post" />
		</form>
		<?php 
		require_once 'database.php'; 
		try {
			$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
		} catch (PDOException $e) {
			$error_message = $e->getMessage();
			print $error_message . "<br>";
		}
		function sani($bad){
			$good =  htmlentities( strip_tags( stripslashes( $bad ) ) );
			return $good;
		}

		if(isset($_POST['Post'])) {

			if(!empty($_POST['title']) && !empty($_POST['text'])){

				$stitle = sani( $_POST['title'] );
				$stext= sani( $_POST['text'] );
				
				if(!empty($_POST['image'])) {
					echo "image is not empty";
					$simage = sani($_POST['image']);
					if($simage != "") {
						echo "image passed sanitization";
						$file = "images/" . $_FILES["image"]["name"];
						if(move_uploaded_file($_FILES["image"]["tmp_name"], $file)) 
						{
							echo "<img src=".$file."  />";
						} else {
							echo "error uploading image";
						}
						$dimage = $simage;
					}
				}
				//if the user bypasses clientside character limit, stops their attempt and logs it
				if(strlen($_POST['title']) > 100 || strlen($_POST['text']) > 2000) {
					echo "<p>You exceeded the maximum character limit!</p>";
					require_once "logging.php";
					auditlog($myDBconnection, "Topic Creation Attempt Exceeded Character Limit", 2, $user, "NULL", "NULL", "NULL");
				} else {
					if($stitle != "" && $stext != ""){
						try {
							$query = "INSERT INTO topics (Username, Date, Title, Text, Image) VALUES (:user, NOW(), :title, :text, :image);";
							$dbquery = $myDBconnection -> prepare($query);
							$dbquery -> bindValue(':user', $user);
							$dbquery -> bindValue(':title', $stitle);
							$dbquery -> bindValue(':text', $stext);
							$dbquery -> bindValue(':image', $dimage);
							$dbquery -> execute();
							echo "Form Posted!";
							require_once "logging.php";
							auditlog($myDBconnection, "New Topic Posted", 0, $user, "NULL", "NULL", "NULL");
						} catch (PDOException $e) {
							$error_message = $e -> getMessage();
							echo $error_message . "<br>";
						}
					} else {
						echo "Not all fields passed sanitization";
					}
				}
			}
		}
		}
	?>
	</main>
</body>
</html>