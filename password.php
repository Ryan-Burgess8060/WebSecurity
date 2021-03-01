<?php 
require cookie.php;
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
		<legend>Please enter your User Name, Security Question, and Security Answer to recover your password.</legend>
			<label for="user">User Name:</label>
			<input type="text" class="txt" id="user" name="username" maxlength="30" required />
			<br class="bre">
			<label for="pass">New Password:</label>
			<input type="password" id="pass" class="txt" name="password" maxlength="50" required />
			<br class="bre">
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
		<input type="submit" name="recover" value="Recover Password" />
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

			if( !empty($_POST["username"]) && !empty($_POST['password']) && !empty($_POST['question']) && !empty($_POST['answer'])) {
				$suser = sani($_POST["username"]);
				$spass = sani($_POST["password"]);
				$squest = sani( $_POST['question']);
				$sans = sani( $_POST['answer']);
				//if the user bypasses clientside character limit, stops their attempt and logs it
				if(strlen($_POST['username']) > 30 || strlen($_POST['password']) > 50 || strlen($_POST['answer']) > 50) {
					echo "<p>You exceeded the maximum character limit!</p>";
					$spass = password_hash($spass, PASSWORD_DEFAULT);
					$sans = password_hash($sans, PASSWORD_DEFAULT);
					require_once "logging.php";
					auditlog($myDBconnection, "Password Recovery Attempt Exceeded Character Limit", 2, $suser, $spass, $squest, $sans);
				} else {
					if( $suser != "" && $spass != "" && $squest != "" && $sans != "") {
						try {
							$query = 'SELECT Username, Password, SecQuestion, SecAnswer FROM accounts WHERE Username = :user';
							$dbquery = $myDBconnection -> prepare($query);
							$dbquery -> bindValue(':user', $suser); 
							$dbquery -> execute();
							$result = $dbquery -> fetch();
						} catch (PDOException $e) {
							$error_message = $e->getMessage();
							echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
						}
						if ($suser == $result['Username'] && $squest == $result['SecQuestion'] && password_verify($sans, $result['SecAnswer'])) {
							$spass = password_hash($spass, PASSWORD_DEFAULT);
							$query = 'UPDATE accounts SET Password = :pass WHERE Username = :user;';
							$dbquery = $myDBconnection -> prepare($query);
							$dbquery -> bindValue(':user', $suser); 
							$dbquery -> bindValue(':pass', $spass); 
							$dbquery -> execute();
							$sans = password_hash($sans, PASSWORD_DEFAULT);
							echo "Password Updated!";
							require_once "logging.php";
							auditlog($myDBconnection, "User Password Recovered", 1, $suser, $spass, $squest, $sans);
						} else { 
							echo 'Invalid Credentials';
							$spass = password_hash($spass, PASSWORD_DEFAULT);
							$sans = password_hash($sans, PASSWORD_DEFAULT);
							require_once "logging.php";
							auditlog($myDBconnection, "Password Recovery Failed", 1, $suser, $spass, $squest, $sans);
						}
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