<?php
$servername = "localhost";
$username = "pocketoffice_xactbid";
$password = "Qs(o6X!;m8#L";
$dbname = "pocketoffice_stormdata_deep";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);


// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>