<!--
Christopher Burgess
2/2/2021
login-handler.php
-->
<?php
//Database connection
require_once 'database.php'; 
try {
	$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
} catch (PDOException $e) {
	$error_message = $e->getMessage();
	print $error_message . "<br>";
}
//sanitize function
function sani($bad){
	$good =  htmlentities( strip_tags( stripslashes( $bad ) ) );
	return $good;
}
//Submission checker
if(isset($_POST["login"])){ 
	//Makes sure fields are filled out, even if the user takes off the clientside "required" tag
	if( !empty($_POST["username"]) && !empty($_POST["password"])) {
		$suser = sani($_POST["username"]);
		$spass = sani($_POST["password"]);
		//if the user bypasses clientside character limit, stops their attempt and logs it
		if(strlen($_POST['username']) > 30 || strlen($_POST['password']) > 50) {
			echo "<p>You exceeded the maximum character limit!</p>";
			require_once "logging.php";
			auditlog($myDBconnection, "Login Attempt Exceeded Character Limit", 2, $suser, $spass, "NULL", "NULL");
		} else {
			//do all the sanitized variables still have a value?
			if( $suser != "" && $spass != "" ) {
				//Here is where the submitted content makes it to the database portion of the file and is submitted to the database 
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