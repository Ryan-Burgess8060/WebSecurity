<?php 
session_start();
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
		<legend>Please enter your User Name, Security Question, and Security Answer to recover your password.</legend>
			<label for="user">User Name:</label>
			<input type="text" class="txt" id="user" name="username" maxlength="30" required />
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
		//Connect to DB
		require_once 'database.php'; 
		try {
			$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
		} catch (PDOException $e) {
			$error_message = $e->getMessage();
			print $error_message . "<br>";
		}
		//sanitize function (to clean up malicious data)
		function sani($bad){
			$good =  htmlentities( strip_tags( stripslashes( $bad ) ) );
			return $good;
		}
		//has the form been submitted?
		if(isset($_POST["recover"])){ 
			//are all the fields filled out?
			if( !empty($_POST["username"]) && !empty($_POST['question']) && !empty($_POST['answer'])) {
				$suser = sani($_POST["username"]);
				$squest = sani( $_POST['question']);
				$sans = sani( $_POST['answer']);
				//do all the sanitized variables still have a value?
				if(strlen($_POST['username']) > 30 || strlen($_POST['answer']) > 50) {
					echo "<p>You exceeded the maximum character limit!</p>";
				} else {
					if( $suser != "" && $squest != "" && $sans != "") {
						//try to insert the information into the database
						try {
							//check to see if your table has the same fields & is spelled the same way
							$query = 'SELECT Username, Password, SecQuestion, SecAnswer FROM accounts WHERE Username = :user AND SecQuestion = :question AND SecAnswer = :answer';
							$dbquery = $myDBconnection -> prepare($query);
							$dbquery -> bindValue(':user', $suser); 
							$dbquery -> bindValue(':question', $squest);
							$dbquery -> bindValue(':answer', $sans);
							$dbquery -> execute();
							$result = $dbquery -> fetch();
						} catch (PDOException $e) {
							$error_message = $e->getMessage();
							echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
						}
						//Does the username match the data in the table?
						if ($suser == $result['Username'] && $squest == $result['SecQuestion'] && $sans == $result['SecAnswer']) {
							echo 'Your password is ' . $result['Password'];
							require_once "logging.php";
							auditlog($myDBconnection, "User Password Recovered", 1, $suser, "NULL", $squest, $sans);
						} else { 
							echo 'Invalid Credentials';
							require_once "logging.php";
							auditlog($myDBconnection, "Password Recovery Failed", 1, $suser, "NULL", $squest, $sans);
						}
						//remember to close IF statement
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