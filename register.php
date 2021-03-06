<?php 
require "cookie.php";
?>
<!DOCTYPE html>
<html lang="en-US">
<!--
Christopher Burgess
1/29/2021
register.php
-->
<head>
	<title>Webserver Register</title>
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
		<legend>Please enter your details to register</legend>
			<label for="username">User Name:</label>
			<input type="text" name="username" id="username" maxlength="30" required />
			<br>
			<label for="password">Password:</label>
			<input type="password" name="password" id="password" maxlength="50" required />
			<br>
			<label for="question">Security Question:</label>
			<input type="text" list="questionOptions" name="question" id="question" required />
				<datalist id="questionOptions">
					<option value="What is the name of your first pet?">
					<option value="What is your mother's maiden name?">
					<option value="What is your favorite book?">
					<option value="What was your first car?">
					<option value="What is the name of the town your were born in?">
				</datalist>
			<br>
			<label for="answer">Security Answer:</label>
			<input type="text" name="answer" id="answer" maxlength="50" required />
		</fieldset>
		<input type="submit" name="register" value="Register" />
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

		if(isset($_POST['register'])) {
			//Sanitization process came from lab 16 from Hawkin's Web Programming class. Cannot get exact link since lab dropboxes are closed. https://georgiasouthern.desire2learn.com/d2l/home/539061
			if( !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['question']) && !empty($_POST['answer'])){

				$suser = sani( $_POST['username'] );
				$spass = sani( $_POST['password'] );
				$squest = sani( $_POST['question'] );
				$sans = sani( $_POST['answer'] );
				
				//if the user bypasses clientside character limit, stops their attempt and logs it
				if(strlen($_POST['username']) > 30 || strlen($_POST['password']) > 50 || strlen($_POST['answer']) > 50) {
					echo "<p>You exceeded the maximum character limit!</p>";
					$spass = password_hash($spass, PASSWORD_DEFAULT);
					$sans = password_hash($sans, PASSWORD_DEFAULT);
					require_once "logging.php";
					auditlog($myDBconnection, "Register Attempt Exceeded Character Limit", 2, $suser, $spass, $squest, $sans);
				} else {
					if($suser != "" && $spass != "" && $squest != "" && $sans != ""){
						$query = "SELECT Username FROM accounts WHERE Username = :user";
						$dbquery = $myDBconnection -> prepare($query);
						$dbquery -> bindValue(':user',$suser);
						$dbquery -> execute();
						$result = $dbquery -> fetch();
						if (in_array($suser, $result)) {
							echo "User already registered";
						} else {
							$spass = password_hash($spass, PASSWORD_DEFAULT);
							$sans = password_hash($sans, PASSWORD_DEFAULT);
							$query = "INSERT INTO accounts (Username, Password, SecQuestion, SecAnswer, Admin) VALUES (:user, :pass, :question, :answer, 'No');";
							$dbquery = $myDBconnection -> prepare($query);
							$dbquery -> bindValue(':user', $suser);
							$dbquery -> bindValue(':pass', $spass);
							$dbquery -> bindValue(':question', $squest);
							$dbquery -> bindValue(':answer', $sans);
							$dbquery -> execute();
							echo "You have been successfully Registered! Please try logging in.";
							require_once "logging.php";
							auditlog($myDBconnection, "New Account Registered", 0, $suser, $spass, $squest, $sans);
							}
					} else {
						echo "Not all fields passed sanitization";
					}
				}
			} else {
				echo "Not all fields were filled in.";
			}
		} else {
			echo "The form has not been submitted.";
		}
	?>
	</main>
</body>
</html>