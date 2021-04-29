<!--
Ryan Burgess
2/1/2021
logging.php
-->
<?php 
function auditlog($myDBconnection, $event, $severity, $username, $password, $secquestion, $secanswer) {
// $IP = $_SERVER['REMOTE_ADDR'];
	//Username, Password, Security Question, and Security Answer can be NULL in case they aren't used in certain events or are left blank
	$query = 'INSERT INTO auditLog (Event, Severity, IP, Time, Username, Password, SecQuestion, SecAnswer) VALUES (:event, :severity, :ip, NOW(), :user, :pass, :question, :answer);';
	$dbquery = $myDBconnection -> prepare($query);
	$dbquery -> bindValue(':event', $event);
	$dbquery -> bindValue(':severity', $severity);
	$dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
	$dbquery -> bindValue(':user', $username); 
	$dbquery -> bindValue(':pass', $password);
	$dbquery -> bindValue(':question', $secquestion);
	$dbquery -> bindValue(':answer', $secanswer);
	$dbquery -> execute();
}
?>