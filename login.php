<?php 
require "cookie.php";
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
	<?php
		require "nav.php";
	?>
	</nav>
	<main>
	<form method="post" class="for">
		<fieldset>
		<legend>Please enter your details to log in</legend>
			<label for="user">User Name:</label>
			<input type="text" class="txt" id="user" name="username" maxlength="30" required />
			<br class="bre">
			<label for="pass">Password:</label>
			<input type="password" id="pass" class="txt" name="password" maxlength="50" required /> <a href='password.php'>Forgot Password?</a>
		</fieldset>
		<input type="submit" name="login" value="Log In" />
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
		if(isset($_POST["login"])){ 
			//Sanitization process came from lab 16 from Hawkin's Web Programming class. Cannot get exact link since lab dropboxes are closed. https://georgiasouthern.desire2learn.com/d2l/home/539061
			if( !empty($_POST["username"]) && !empty($_POST["password"])) {
				$suser = sani($_POST["username"]);
				$spass = sani($_POST["password"]);
				//if the user bypasses clientside character limit, stops their attempt and logs it
				if(strlen($_POST['username']) > 30 || strlen($_POST['password']) > 50) {
					echo "<p>You exceeded the maximum character limit!</p>";
					$spass = password_hash($spass, PASSWORD_DEFAULT);
					require_once "logging.php";
					auditlog($myDBconnection, "Login Attempt Exceeded Character Limit", 2, $suser, $spass, "NULL", "NULL");
				} else {
					if( $suser != "" && $spass != "" ) {
						$query = 'SELECT Username, Password, Admin FROM accounts WHERE Username = :user;';
						$dbquery = $myDBconnection -> prepare($query);
						$dbquery -> bindValue(':user', $suser); 
						$dbquery -> execute();
						$result = $dbquery -> fetch();
						if ($suser == $result['Username'] && password_verify($spass, $result['Password'])) {
							$spass = password_hash($spass, PASSWORD_DEFAULT);
							require_once "logging.php";
							auditlog($myDBconnection, "User Login", 0, $suser, $spass, "NULL", "NULL");
							$token = bin2hex(random_bytes(10));
							$query = 'INSERT INTO sessions (Username, Token, Expiration, Admin) VALUES (:user, :token, DATE_ADD(NOW(), INTERVAL 7 DAY), :admin);';
							$dbquery = $myDBconnection -> prepare($query);
							$dbquery -> bindValue(':user', $suser); 
							$dbquery -> bindValue(':token', $token); 
							$dbquery -> bindValue(':admin', $result['Admin']); 
							$dbquery -> execute();
							setcookie('Authentication', $token, time() + (86400 * 7), "/");
							header('Location:index.php');
						} else { 
							echo 'Invalid Credentials';
							$spass = password_hash($spass, PASSWORD_DEFAULT);
							require_once "logging.php";
							auditlog($myDBconnection, "Login Attempt Failed", 1, $suser, $spass, "NULL", "NULL");
							session_unset($_SESSION['Username']);
							session_destroy();
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