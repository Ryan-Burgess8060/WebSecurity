<?php 
require "cookie.php";
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
		<form method="post">
		<fieldset>
		<legend>Please enter your User Name</legend>
			<label for="user">User Name:</label>
			<input type="text" class="txt" id="user" name="username" maxlength="30" required />
		</fieldset>
		<input type="submit" name="recover" value="Submit" />
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

		if(isset($_POST["recover"])){ 

			if( !empty($_POST["username"])) {
				$suser = sani($_POST["username"]);
				//if the user bypasses clientside character limit, stops their attempt and logs it
				if(strlen($_POST['username']) > 30) {
					echo "<p>You exceeded the maximum character limit!</p>";
					require_once "logging.php";
					auditlog($myDBconnection, "Password Recovery Attempt Exceeded Character Limit", 2, $suser, "NULL", "NULL", "NULL");
				} else {
					if( $suser != "") {
						session_start();
						$_SESSION["username"] = $suser;
						header("Location:password2.php");
					} else { //not all sanitized variables have values
						echo "<p>Bad data was inserted into the fields.</p>";
					}
				}
			} else { //not all fields were filled in
				echo "<p>Not all fields were filled in.</p>";
			}
		} else { //form not submitted
			echo "<p>Form has not been submitted yet.</p>";
		}
	?>
	</main>
</body>
</html>