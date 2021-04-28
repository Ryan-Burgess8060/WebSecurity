<?php 
require "cookie.php";
session_start();
if ($_SESSION["username"] == "") {
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
		<form method="post">
		<fieldset>
		<legend>Please enter your Security Question, and Security Answer.</legend>
			<label for="question">Security Question:</label>
			<input type="text" list="questionOptions" name="question" id="age" class="question" required />
				<datalist id="questionOptions">
					<option value="What is the name of your first pet?">
					<option value="What is your mother's maiden name?">
					<option value="What is your favorite book?">
					<option value="What was your first car?">
					<option value="What is the name of the town your were born in?">
				</datalist>
			<br class="bre">
			<label for="answer">Security Answer:</label>
			<input type="text" class="txt" name="answer" id="answer" maxlength="50" required />
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

			if(!empty($_POST['question']) && !empty($_POST['answer'])) {
				$squest = sani( $_POST['question']);
				$sans = sani( $_POST['answer']);
				//if the user bypasses clientside character limit, stops their attempt and logs it
				if(strlen($_POST['answer']) > 50) {
					echo "<p>You exceeded the maximum character limit!</p>";
					$sans = password_hash($sans, PASSWORD_DEFAULT);
					require_once "logging.php";
					auditlog($myDBconnection, "Password Recovery Attempt Exceeded Character Limit", 2, $_SESSION["username"], "NULL", $squest, $sans);
				} else {
					if($squest != "" && $sans != "") {
						try {
							$user = $_SESSION["username"];
							$query = 'SELECT Username, SecQuestion, SecAnswer FROM accounts WHERE Username = :user';
							$dbquery = $myDBconnection -> prepare($query);
							$dbquery -> bindValue(':user', $user); 
							$dbquery -> execute();
							$result = $dbquery -> fetch();
						} catch (PDOException $e) {
							$error_message = $e->getMessage();
							echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
						}
						if ($user == $result['Username'] && $squest == $result['SecQuestion'] && password_verify($sans, $result['SecAnswer'])) {
							$_SESSION["quest"] = $squest;
							$sans = password_hash($sans, PASSWORD_DEFAULT);
							$_SESSION["answer"] = $sans;
							header("Location:password3.php");
						} else { 
							echo 'Invalid Credentials';
							$sans = password_hash($sans, PASSWORD_DEFAULT);
							require_once "logging.php";
							auditlog($myDBconnection, "Password Recovery Failed", 1, $user, "NULL", $squest, $sans);
						}
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