<?php
//Database.php came from lab 14 from Hawkin's Web Programming class. Cannot get exact link since lab dropboxes are closed. https://georgiasouthern.desire2learn.com/d2l/home/539061 
$HOST_NAME = "localhost";
$DATABASE_NAME = "WebSecurity";
$USERNAME = "phpmyadmin";
$PASSWORD = "maximus8060";
$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
?>