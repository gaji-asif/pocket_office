<?php
class DBController {
	private $host = "xactimates.com";
	private $database = "xactimates_workflow";
	private $user = "xactimates_wp";
	private $password = "wkPg9TxnQI5X";
	
	function __construct() {
		$conn = $this->connectDB();
		if(!empty($conn)) {
			$this->selectDB($conn);
		}
	}
	
	function connectDB() {
		$conn = mysqli_connect($this->host,$this->user,$this->password);
		return $conn;
	}
	
	function selectDB($conn) {
		mysqli_select_db($this->database,$conn);
	}
	
	function selectQuery($query) {
		$result = mysqli_query($query);
		while($row=mysqli_fetch_assoc($result)) {
			$resultset[] = $row;
		}		
		if(!empty($resultset))
			return $resultset;
	}
	
	function numRows($query) {
		$result  = mysqli_query($query);
		$rowcount = mysqli_num_rows($result);
		return $rowcount;	
	}
	
	function insertQuery($query) {
		$result = mysqli_query($query);
		if(!empty($result)) {
			$insert_id = mysqli_insert_id();
			return $insert_id;
		}
	}
	
	function updateQuery($query) {
		$result = mysqli_query($query);
		return $result;
	}
}
?>