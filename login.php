<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en-US">
<!--
Christopher Burgess
1/29/2021
login.php
-->
<head>
	<title>Webserver Login</title>
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
		<li><a href="admin.php">Admin</a></li>
		</ul>
	</nav>
	<main>
	<form method="post" class="for">
		<fieldset>
		<legend>Please enter your details to log in</legend>
			<label for="user">User Name:</label>
			<input type="text" class="txt" id="user" name="username" required />
			<br class="bre">
			<label for="pass">Password:</label>
			<input type="password" id="pass" class="txt" name="password" required />
		</fieldset>
		<input type="submit" name="login" value="Log In" />
	</form>
	<?php
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
		if(isset($_POST["login"])){ 
			//are all the fields filled out?
			if( !empty($_POST["username"]) && !empty($_POST["password"])) {
				$suser = sani($_POST["username"]);
				$spass = sani($_POST["password"]);
				//do all the sanitized variables still have a value?
				if( $suser != "" && $spass != "" ) {
					//try to insert the information into the database
					try {
						$query = 'SELECT Username, Password FROM accounts WHERE Username = :user AND Password = :pass;';
						$dbquery = $myDBconnection -> prepare($query);
						$dbquery -> bindValue(':user', $suser); 
						$dbquery -> bindValue(':pass', $spass);
						$dbquery -> execute();
						$result = $dbquery -> fetch();
					} catch (PDOException $e) {
						$error_message = $e->getMessage();
						echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
					}
					//Does the username match the data in the table?
					if ($suser == $result['Username'] && $spass == $result['Password']) {
						echo 'Authorized User';
						$_SESSION['Username'] = $suser;
						header('Location:index.php');
					} else { 
						echo 'Unauthorized User';
						session_unset($_SESSION['Username']);
						session_destroy();
					}
					
				} else { //not all sanitized variables have values
					echo "<p>Bad data was inserted into the fields.</p>";
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