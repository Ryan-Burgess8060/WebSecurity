<?php 
require "cookie.php";
session_start();
if ($_SESSION["username"] == "" || $_SESSION["quest"] == "" || $_SESSION["answer"] == "") {
	session_unset();
	session_destroy();
	header("Location:password.php");
}
?>
<!DOCTYPE html>
<html lang="en-US">
<!--
Christopher Burgess
1/29/2021
password.php
-->
<head>
	<title>Webserver Forgot Password</title>
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
		<form method="post" class="for">
		<fieldset>
		<legend>Please enter your new password.</legend>
			<label for="pass">New Password:</label>
			<input type="password" id="pass" class="txt" name="password" maxlength="50" required />	
		</fieldset>
		<input type="submit" name="recover" value="Change Password" />
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

		if(isset($_POST["recover"])){ 
			//Sanitization process came from lab 16 from Hawkin's Web Programming class. Cannot get exact link since lab dropboxes are closed. https://georgiasouthern.desire2learn.com/d2l/home/539061
			if(!empty($_POST['password'])) {
				$spass = sani($_POST["password"]);
				//if the user bypasses clientside character limit, stops their attempt and logs it
				if(strlen($_POST['password']) > 50) {
					echo "<p>You exceeded the maximum character limit!</p>";
					$spass = password_hash($spass, PASSWORD_DEFAULT);
					require_once "logging.php";
					auditlog($myDBconnection, "Password Recovery Attempt Exceeded Character Limit", 2, $_SESSION["username"], $spass, $_SESSION["quest"], $_SESSION["answer"]);
				} else {
					if($spass != "") {
						$user = $_SESSION["username"];
						$spass = password_hash($spass, PASSWORD_DEFAULT);
						$query = 'UPDATE accounts SET Password = :pass WHERE Username = :user;';
						$dbquery = $myDBconnection -> prepare($query);
						$dbquery -> bindValue(':user', $user); 
						$dbquery -> bindValue(':pass', $spass); 
						$dbquery -> execute();
						require_once "logging.php";
						auditlog($myDBconnection, "User Password Recovered", 1, $user, $spass, $_SESSION["quest"], $_SESSION["answer"]);
						session_unset();
						session_destroy();
						header("Location:index.php");
						
					} else { //not all sanitized variables have values
						echo "<p>Bad data was inserted into the fields.</p>";
					}
				}
			} else { //not all fields were filled in
				echo "<p>Not all fields were filled in.</p>";
			}
		}
	?>
	</main>
</body>
</html>