<?php
include('../storm-db-conn/conn.php');
//SELECT *, ( 3959 * acos ( cos ( radians(38.45) ) * cos( radians( BEGIN_LAT ) ) * cos( radians( BEGIN_LON ) - radians(-91.00) ) + sin ( radians(38.45) ) * sin( radians( BEGIN_LAT ) ) ) ) AS distance FROM storm_details HAVING distance < 7 ORDER BY distance LIMIT 0 , 20
//https://gis.stackexchange.com/questions/31628/find-features-within-given-coordinates-and-distance-using-mysql
//http://localhost:8080/stormdata/result.php?year=1988&search=MISSOURI
//and EVENT_TYPE='Hail' and STATE=".strtoupper($_POST['state'])." and BEGIN_YEARMONTH LIKE '1988%'
 //echo $sql  = "SELECT ( 3959 * acos ( cos ( radians(".number_format($_POST['latitude'], 2).") ) * cos( radians( BEGIN_LAT ) ) * cos( radians( BEGIN_LON ) - radians(".number_format($_POST['longitude'], 2).") ) + sin ( radians(".number_format($_POST['latitude'], 2).") ) * sin( radians( BEGIN_LAT ) ) ) ) AS distance FROM storm_details HAVING distance < 7 ORDER BY distance LIMIT 0 , 20"; 
//$sql  = "SELECT count(*) as tot  FROM storm_details WHERE STATE='".strtoupper($_POST['state'])."' and EVENT_TYPE='Hail' and BEGIN_YEARMONTH LIKE '1988%'";
//SELECT DISTINCT(YEAR) FROM storm_details order by YEAR
$sql1="SELECT DISTINCT(YEAR) FROM storm_details order by YEAR";
$result1 = $conn->query($sql1);
if ($result1->num_rows > 0) {
while($row1 = $result1->fetch_assoc()) {   
$sql  = "Select count(a.distance) as tot from ( SELECT  *, ( 3959 * acos ( cos ( radians(".number_format($_POST['latitude'], 2).") ) * cos( radians( BEGIN_LAT ) ) * cos( radians( BEGIN_LON ) - radians(".number_format($_POST['longitude'], 2).") ) + sin ( radians('".number_format($_POST['latitude'], 2)."') ) * sin( radians( BEGIN_LAT ) ) ) ) AS distance FROM storm_details where (`EVENT_TYPE`='HAIL' or `EVENT_TYPE`='Tornado' or `EVENT_TYPE`='High Wind') and STATE='".strtoupper($_POST['state'])."' and YEAR='".$row1['YEAR']."' HAVING distance < ".$_POST['distance']." ORDER BY distance) as a";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
if($row ['tot']!=0){
$str.= "&nbsp; &nbsp;&nbsp;<a target='_blank' href='map.php?year=".$row1['YEAR']."&longitude=".number_format($_POST['longitude'], 2)."&latitude=".number_format($_POST['latitude'], 2)."&state=".strtoupper($_POST['state'])."&address=".strtoupper($_POST['address'])."&distance=".strtoupper($_POST['distance'])."'><b>YEAR ".$row1['YEAR']."  &nbsp;".$row ['tot']." Hail/Tornado/High Wind Report(s)</b></a><br>";
  
}else{
 $str.= "&nbsp; &nbsp;&nbsp;<a   href='javascript:void(0)'><b>YEAR ".$row1['YEAR']."  &nbsp;".$row ['tot']." Hail/Tornado Report(s)</b></a><br>";
    
}
  
    
}  	  
}
echo $str;
$conn->close();
?>