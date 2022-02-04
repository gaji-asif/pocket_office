<?php
include 'includes/common_lib.php';


$curDate = DateUtil::formatMySQLDate();

$sql = "SELECT user_id, fname, lname, email, level, generalins, workerins FROM users";
$fectchdata = DBUtil::queryToArray($sql);

$sql1 = "SELECT email FROM users WHERE level=1";
$fectchdata1 = DBUtil::queryToArray($sql1);
$em=array();
if(count($fectchdata1)>0)
{
	foreach($fectchdata1 as $data1)
	{
		$em[] = $data1['email'];
	}
}
$days = "+7 day";
//echo '<pre>'; print_r($fectchdata);
$nextdate = strtotime($days, strtotime($curDate));
$nextdate = date('Y-m-d', $nextdate);
		
	
if(count($fectchdata)>0)
{
		//$to = implode(',',$em);
		$to = "nilesh@renustechnologix.com";
		$headers = "From: amish@renustechnologix.com" . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		$subject = "Insurance Expiration Information";
		$html= "<html><body>";
		$html.= "Hello Admin,<br /><br />";
		$html .= "<table border='1' cellpadding='4' style='border-collapse:collapse;' >";
		$html .= "<tr>";
		$html .= "<th>User Name</th>";
		$html .= "<th>Insurance Type</th>";
		$html .= "<th>Expiry Date</th>";
		$html .= "</tr>";

		//echo $html;
		//print_r($html);
	foreach($fectchdata as $data)
	{
		$gendate = date('Y-m-d',strtotime($data['generalins']));
		$workdate = date('Y-m-d',strtotime($data['workerins']));


		if($curDate == $gendate || $nextdate==$gendate)
		{	
			$today = ($curDate == $gendate) ? 'Today' : $gendate;
			//echo "General liability insurance expires ".$today." for User ".$data['fname']."  ".$data['lname']."<br />";	

		
			$html .="<tr align='center'>
						<td>".$data['fname']." ".$data['lname']."</td>
						<td>General liability insurance</td>
						<td>". $today ."</td>
					</tr>";
		}

		if($curDate == $workdate || $nextdate==$workdate)
		{
			$today = ($curDate == $workdate) ? 'Today' : $workdate;
			//echo "Workers Compensations insurance expires ".$today." for User ".$data['fname']."  ".$data['lname']."<br />";
		
			 $html .="<tr align='center'>
							<td>". $data['fname']." ".$data['lname'] ."</td>
							<td>Workers Compensations insurance</td>
							<td>". $today ."</td>
					 </tr>";
		}
	}

		$html .= "</table>
		<br />Regards,<br />
		Workflow Team.
		</body></html>";
		/*$result = mail($to,$subject,$html,$headers);
		if(!$result)
		{
			echo "Error Sending Mail";
		}
		else
		{
			echo "Mail Sent Successfully";
		} */

}
