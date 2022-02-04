<?php

include '../common_lib.php';

$res=null;

$limit = (int)RequestUtil::get('limit', 0);

$limit = $limit >= 0 ? $limit : 0;

//$sort = RequestUtil::get('sort') ?: 'ORDER BY lname ASC';

//$searchStr = RequestUtil::get('search');

$windowHeight = RequestUtil::get('window_height', DEFAULT_WINDOW_HEIGHT);

$_RES_PER_PAGE = 5;

$maxRecord = 150;

$limitStr = (($limit+$_RES_PER_PAGE) > $maxRecord) ? "LIMIT $maxRecord, $_RES_PER_PAGE" : "LIMIT $limit, $_RES_PER_PAGE";





if($res == null) {

  /*$sql  = "select messages.message_id, messages.subject, users.fname, users.lname, message_link.timestamp, messages.timestamp, '' as jid".

           " from messages, users, message_link".

           " where message_link.delete=0 and messages.message_id=message_link.message_id and messages.user_id=users.user_id and message_link.user_id='".$_SESSION['ao_userid']."'".

           " order by messages.timestamp desc".

           " limit 10 "; /* UNION SELECT '', journals.text, users.fname, users.lname, journals.user_id, journals.timestamp as time, journals.job_id AS jid FROM journals,users WHERE recipientid LIKE '%".$_SESSION['ao_userid']."%' and journals.user_id=users.user_id" ;

	

	$sql  = "SELECT messages.message_id, messages.subject, users.fname, users.lname,".

  		   " message_link.timestamp as user_id, messages.timestamp, '' AS journal_id, '' AS job_id, '' AS job_number".

           " from messages, users, message_link".

           " where message_link.delete=0 and messages.message_id=message_link.message_id".

           " and messages.user_id=users.user_id and message_link.user_id='".$_SESSION['ao_userid']."'".

           

           " UNION ".

           

            "SELECT '' AS message_id, journals.text, users.fname, users.lname,".

            "journals.user_id, journals.timestamp, journals.journal_id, journals.job_id, jobs.job_number FROM journals,users, jobs".

            " WHERE recipientid LIKE '%".$_SESSION['ao_userid']."%' and journals.user_id=users.user_id AND jobs.job_id=journals.job_id AND journals.flag=1".

            " ORDER BY timestamp DESC ".$limitStr;*/

  $sql  = "SELECT messages.message_id, messages.subject, users.fname, users.lname,".

          " message_link.timestamp as user_id, messages.timestamp, '' AS journal_id, '' AS job_id, '' AS job_number".

            " from messages, users, message_link".

            " where message_link.delete=0 and messages.message_id=message_link.message_id".

            " and messages.user_id=users.user_id and message_link.user_id='".$_SESSION['ao_userid']."'".

           

            " UNION ".

           

            "SELECT '' AS message_id, journals.text, users.fname, users.lname,".

            "journals.user_id, journals.timestamp, journals.journal_id, journals.job_id, jobs.job_number FROM journals,users, jobs".

            " WHERE (recipientid LIKE '%,".$_SESSION['ao_userid']."%' or recipientid=".$_SESSION['ao_userid'].") and journals.user_id=users.user_id AND jobs.job_id=journals.job_id AND journals.flag=1".

            " ORDER BY timestamp DESC ".$limitStr;

	

	

    $res = DBUtil::query($sql);

	

}



/*if($res1 == null) 

{

	$sql1 = "SELECT journals.journal_id, journals.job_id, journals.text, journals.user_id, journals.timestamp, users.fname, users.lname FROM journals,users WHERE recipientid LIKE '%".$_SESSION['ao_userid']."%' and journals.user_id=users.user_id AND journals.flag=1";

	$res1 = DBUtil::query($sql1);

}*/



$totalmsg1 = DBUtil::getLastRowsFound();

$totalmsg = ($totalmsg1 > $maxRecord) ? $maxRecord : $totalmsg1;

//$users = DBUtil::queryToArray($res);



if($totalmsg1 > $maxRecord)

{

	echo '<div style="text-align: center; background-color: #FF0000; color: #FFFFFF; padding: 3px 0px;">';

	echo 'Exceed maximum limit of '.$maxRecord.' messages, Please delete some messages';

	echo '</div>';

}



//echo mysqli_num_rows($res);

//if(mysqli_num_rows($res) == 0)

	

if(mysqli_num_rows($res) <= 0)

{

?>

	<h1 class="widget">No Messages Found</h1>

<?php

}

else

{

?>

<table class="table table-condensed table-striped table-hover table-widget data-table" id="journalcontainer">

	<thead>

		<tr>

			<th>Job Number</th>

			<th>Subject</th>

			<th>From</th>

			<th>Timestamp</th>

			<th>&nbsp;</th>

		</tr>

	</thead>

<tbody>

	<?php

		while(list($id, $subject, $fname, $lname, $read, $sent, $journal_id, $job_id, $job_number)=mysqli_fetch_row($res))

		{

			$timestamp = DateUtil::formatDateTime($sent);



			$row_class = '';

			if(empty($read))

			{

				$row_class = 'info';

			}



			$view_data = array(

				'row_class' => $row_class,

				'id' => $id,

				'subject' => $subject,

				'lname' => $lname,

				'fname' => $fname,

				'timestamp' => $timestamp,	

				'journal_id' => $journal_id,

				'job_id' => $job_id,

				'job_number' => $job_number

			);

			echo ViewUtil::loadView('widgets/inbox-row', $view_data);

		}

	?>

</tbody>



			<?php

	$viewDatamsg = array(

				'limit' => $limit,

				'query_string_params' => $_GET,

				'results_per_page' => $_RES_PER_PAGE,

				'total_results' => $totalmsg,

				'script' => 'widget_inbox',

				'destination' => 'widget-inbox'

			);

			echo ViewUtil::loadView('list-pagination', $viewDatamsg);

	

}

?>

<?php

/*	

			



 if(mysqli_num_rows($res1) == 0)

{

?>

	<h1 class="widget">No Journals Found</h1>

<?php

}

else

{

?>

	<table class="table table-condensed table-striped table-hover table-widget">

				<!-- <thead>

					 <tr>

						<th>Subject</th>

						<th>From</th>

						<th>Timestamp</th>



					</tr> 

					 </thead> -->

		<tbody>

		<?php

			while(list($journal_id, $job_id, $text, $id1, $timestampp, $fname, $lname)=mysqli_fetch_row($res1))

			{

				$timestamp = DateUtil::formatDateTime($timestampp);



				$row_class = '';

				if(empty($read1))

				{

					$row_class1 = 'info';

				}



				$view_data1 = array(

					'journal_id' => $journal_id,

					'job_id'=> $job_id,

					'text' => $text,

					'user_id' => $id1,

					'timestamp' => $timestamp,

					'lname' => $lname,

					'fname' => $fname,

					);



				echo ViewUtil::loadView('widgets/inbox-row', $view_data1);

					

			}

		?>



		</tbody>

</table>

<?php



}

*/

?> 