<?php 
session_start();
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
		<ul>
		<li><a href="index.php">Home</a></li>
		<li><a href="login.php">Login</a></li>
		<li><a href="register.php">Register</a></li>
		<li><a href="password.php">Forgot Password</a></li>
		<li><a href="logout.php">Logout</a></li>
		<li><a href="admin.php">Admin</a></li>
		</ul>
	</nav>
	<main>
	<form method="post" class="for">
		<fieldset>
		<legend>Please enter your details to register</legend>
			<label for="username">User Name:</label>
			<input type="text" class="txt" name="username" id="username" required />
			<br class="bre">
			<label for="password">Password:</label>
			<input type="password" class="txt" name="password" id="password" required />
			<br class="bre">
			<label for="question">Security Question:</label>
			<input type="text" list="questionOptions" name="question" id="age" class="question" />
				<datalist id="questionOptions">
					<option value="What is the name of your first pet?">
					<option value="What is your mother's maiden name?">
					<option value="What is your favorite book?">
					<option value="What was your first car?">
					<option value="What is the name of the town your were born in?">
				</datalist>
			<br class="bre">
			<label for="answer">Security Answer:</label>
			<input type="text" class="txt" name="answer" id="answer" required />
		</fieldset>
		<input type="submit" name="register" value="Register" />
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
		//is form submitted?
		if(isset($_POST['register'])) {
			//do the form fields have data?
			if( !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['question']) && !empty($_POST['answer'])){
				//send each field to the sani function
				$suser = sani( $_POST['username'] );
				$spass = sani( $_POST['password'] );
				$squest = sani( $_POST['question'] );
				$sans = sani( $_POST['answer'] );
				//do all the sanitized variables of form fields still hold data?
				if($suser != "" && $spass != "" && $squest != "" && $sans != ""){
					//try to run the insert query with the sanitized data
					try {
						$query = "SELECT Username FROM accounts WHERE Username = :user";
						$dbquery = $myDBconnection -> prepare($query);
						$dbquery -> bindValue(':user',$suser);
						$dbquery -> execute();
						$result = $dbquery -> fetch();
						if (in_array($suser, $result)) {
							echo "User already registered";
						} else {
							$query = "INSERT INTO accounts (Username, Password, SecQuestion, SecAnswer) VALUES (:user, :pass, :question, :answer);";
							$dbquery = $myDBconnection -> prepare($query);
							$dbquery -> bindValue(':user', $suser);
							$dbquery -> bindValue(':pass', $spass);
							$dbquery -> bindValue(':question', $squest);
							$dbquery -> bindValue(':answer', $sans);
							$dbquery -> execute();
							echo "You have been successfully Registered! Please try logging in.";
							$event = 'New Account Registered';
							$severity = 0;
							require_once "logging.php";
							auditlog($event, $severity, $suser, $spass, $squest, $sans);
						}
					} catch (PDOException $e) {
						$error_message = $e -> getMessage();
						echo $error_message . "<br>";
					}
				} else {
					echo "Not all fields passed sanitization";
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