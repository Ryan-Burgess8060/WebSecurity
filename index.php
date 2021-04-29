<?php 
require "cookie.php";
?>
<!DOCTYPE html>
<html lang="en-US">
<!--
Christopher Burgess
4/28/2021
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
			<a href="topic.php?t=<?php echo $arr['ID']; ?>"><?php echo $arr['Title']; ?></a>
			</li>
		<?php } ?>
		</ul>
		<?php
		if ($loggedIn == True) {
		?>
		<br>
		<!-- Multipart ectype came from w3c. https://www.w3schools.com/tags/att_form_enctype.asp -->
		<form action="index.php" enctype="multipart/form-data" method="post">
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
		<input type="submit" name="Post" value="Post Topic" />
		</form>
		<?php 
		require_once 'database.php'; 
		//myDBconnection came from lab 14 from Hawkin's Web Programming class. Cannot get exact link since lab dropboxes are closed. https://georgiasouthern.desire2learn.com/d2l/home/539061 
		$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
		
		//This specific sanitization function came from lab 16 from Hawkin's Web Programming class. Cannot get exact link since lab dropboxes are closed. https://georgiasouthern.desire2learn.com/d2l/home/539061
		function sani($bad){
			$good =  htmlentities( strip_tags( stripslashes( $bad ) ) );
			return $good;
		}

		if(isset($_POST['Post'])) {
			//Sanitization process came from lab 16 from Hawkin's Web Programming class. Cannot get exact link since lab dropboxes are closed. https://georgiasouthern.desire2learn.com/d2l/home/539061
			if(!empty($_POST['title']) && !empty($_POST['text'])){
				$stitle = sani( $_POST['title'] );
				$stext= sani( $_POST['text'] );
				if(!empty($_FILES["image"]['name'])) {
					$simage = sani($_FILES["image"]['name']);
					if($simage != "") {
						// File extension check came from here: https://stackoverflow.com/questions/31782832/i-cant-upload-some-pictures-with-php
						$imageFileType = pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION);
						if ($imageFileType == "jpg" || $imageFileType == "JPG" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
							$file = "images/" . $_FILES["image"]["name"];
							if(move_uploaded_file($_FILES["image"]["tmp_name"], $file)) 
							{
							} else {
								echo "error uploading image";
								exit();
							}
							$dimage = $simage;
						} else {
							echo "Invalid File Type";
							exit();
						}
					} else {
						echo "Please change your image name.";
						exit();
					}
				}
				//if the user bypasses clientside character limit, stops their attempt and logs it
				if(strlen($_POST['title']) > 100 || strlen($_POST['text']) > 2000) {
					echo "<p>You exceeded the maximum character limit!</p>";
					require_once "logging.php";
					auditlog($myDBconnection, "Topic Creation Attempt Exceeded Character Limit", 2, $user, "NULL", "NULL", "NULL");
				} else {
					if($stitle != "" && $stext != ""){
						$query = "INSERT INTO topics (Username, Date, Title, Text, Image) VALUES (:user, NOW(), :title, :text, :image);";
						$dbquery = $myDBconnection -> prepare($query);
						$dbquery -> bindValue(':user', $user);
						$dbquery -> bindValue(':title', $stitle);
						$dbquery -> bindValue(':text', $stext);
						$dbquery -> bindValue(':image', $dimage);
						$dbquery -> execute();
						require_once "logging.php";
						auditlog($myDBconnection, "New Topic Posted", 0, $user, "NULL", "NULL", "NULL");
						header('Location:index.php');
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