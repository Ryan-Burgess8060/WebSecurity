<?php 
function auditlog($event, $severity, $username, $password, $secquestion, $secanswer) {
$IP = $_SERVER['REMOTE_ADDR'];
echo $IP;
echo $event;
echo $severity;
echo $username;
echo $password;
echo $secquestion;
echo $secanswer;
try {
	//Username, Password, Security Question, and Security Answer can be NULL in case they aren't used in certain events or are left blank
	$query = 'INSERT INTO auditLog (Event, Severity, IP, Time, Username, Password, SecQuestion, SecAnswer) VALUES (:event, :severity, :ip, NOW(), :user, :pass, :question, :answer);';
	$dbquery = $myDBconnection -> prepare($query);
	$dbquery -> bindValue(':event', $event);
	$dbquery -> bindValue(':severity', $severity);
	$dbquery -> bindValue(':ip', $IP);
	$dbquery -> bindValue(':user', $username); 
	$dbquery -> bindValue(':pass', $password);
	$dbquery -> bindValue(':question', $secquestion);
	$dbquery -> bindValue(':answer', $secanswer);
	$dbquery -> execute();
} catch (PDOException $e) {
	$error_message = $e->getMessage();
	echo "<p>An error occurred while trying to log data to the table: $error_message </p>";
}
}
?>