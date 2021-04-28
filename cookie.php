<!--
Christopher Burgess
2/28/2021
cookie.php
-->
<?php 
require "database.php";
$loggedIn = False;
$Admin = False;
if(isset($_COOKIE['Authentication'])) {
	try {
		$token = $_COOKIE['Authentication'];
		$query = 'SELECT * FROM sessions WHERE Token = :token AND Expiration > NOW();';
		$dbquery = $myDBconnection -> prepare($query);
		$dbquery -> bindValue(':token', $token); 
		$dbquery -> execute();
		$result = $dbquery -> fetch();
		if($result != "") {
			$loggedInUser = $result["Username"];
			$loggedIn = True;
			if($result['Admin'] == "Yes") {
				$Admin = True;
			}
		}
	} catch (PDOException $e) {
		$error_message = $e->getMessage();
	}
}
?>