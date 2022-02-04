<?php

include '../common_lib.php';

echo ViewUtil::loadView('doc-head');

ModuleUtil::checkAccess('add_user', TRUE);



$errors = array();

if(RequestUtil::get('submit')) {

    $fname = ucwords(strtolower(trim(RequestUtil::get('fname'))));

    $lname = ucwords(strtolower(trim(RequestUtil::get('lname'))));

    $dba = RequestUtil::get('dba');

    $username = RequestUtil::get('username');

    $email = RequestUtil::get('email');

    $phone = StrUtil::formatPhoneToSave(RequestUtil::get('phone'));

    $sms = RequestUtil::get('sms');

    if(empty($sms))

        $sms=0;

    

    $level = RequestUtil::get('level');

    $founder = RequestUtil::get('founder', 0);

    $notes = RequestUtil::get('notes');

    $office = RequestUtil::get('office', 'NULL');





			@$userSelect2 = $_REQUEST['user_select'];

			@$userSelect = implode(',', $userSelect2); 

			$id = trim(RequestUtil::get('id')); //exit;

			$event_name = trim(RequestUtil::get('event_name'));

		    @$user_select = trim(RequestUtil::get('user_select'));

			$url = trim(RequestUtil::get('url').'&jay=1');

			$share_by = trim(RequestUtil::get('share_by'));

			$status = trim(RequestUtil::get('status'));

				 

		

			if($id > 0 || $id != ""){

				

			/////////////////////////////////////// Update >>

			$sql = "UPDATE share_url set

			

			event_name = '".$event_name."',

			user_select = '".$userSelect."',

			url = '".$url."',

			share_by = '".$share_by."',

			status = '".$status."'

			where id = '".$id."'";

			

			DBUtil::query($sql);

			$newUserID = DBUtil::getInsertId();



		

			} else { 

				if($event_name == ""){

					echo 'All fields are required.';

				} else if($userSelect == ""){

					echo 'All fields are required.';

				} else {

					////////////////////////////////////// Insert >>	

					$sql = "INSERT INTO share_url set

					

					event_name = '".$event_name."',

					user_select = '".$userSelect."',

					url = '".$url."',

					share_by = '".$share_by."',

					status = '".$status."'";  //exit;

					

					DBUtil::query($sql);

					$newUserID = DBUtil::getInsertId();

				}

			

			}	









?>



<script>

//function test(){

//	Request.makeModal('test.php', 'testHtml', true, true, true);

//	}

  //  Request.makeModal('<?=AJAX_DIR?>/get_userlist.php', 'userscontainer', true, true, true);

	  

</script>

<?php

        //die();

 //   }

}



?>


<script>
setTimeout(function(){ getUrl(); }, 1000);
function getUrl(){
var decodedString = atob('<?php echo $_SESSION['urlResultText']; ?>');
document.getElementById("urlShowingText").value = decodedString;
}
</script>
<div id="editForm" >

<form method="post" name="user" action="?">

<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">

    <tr valign="center">

        <td>

          Add New Item

        </td>

        <td align="right">

          <i class="icon-remove grey btn-close-modal"></i>

        </td>

    </tr>

</table>

<!--<?=AlertUtil::generate($errors, 'error', TRUE)?>-->

<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">

    <tr valign="top">

        <td width="25%" class="listitem"><b>Event Name:</b>&nbsp;<span class="red">*</span></td>

        <td class="listrow"><input type="text" name="event_name" value=""></td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>User Select:</b>&nbsp;<span class="red">*</span></td>

        <td class="listrow"><select name="user_select[]" id="user_select" multiple>



    <?php

$users_array = UserModel::getAll(TRUE, $firstLast);

foreach($users_array as $user)

{

?>

              <option value="<?=$user['user_id']?>"><?=$user['fname'].' '.$user['lname']; ?></option>

<?php

}

?>

  </select>

  

  </td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>Url:</b>&nbsp;<span class="red">*</span></td>

        <td class="listrow">

            <!--<input type="text" name="url" value="<?php //echo @$_REQUEST['url']; ?><?php //echo $_SESSION['urlResultText']; ?>">-->

           <textarea name="url" id="urlShowingText" style="margin: 0px; height: 52px; width: 874px;" ><?php echo $_SESSION['urlResultText']; ?></textarea>

<a href="Javascript:void()"  onclick="copyText()">Copy text</a>
        </td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>Status:</b></td>

        <td class="listrow"> 

        <input checked="checked" type="radio" name="status" value="y" id="RadioGroup1_0" />

        Enable

        <input type="radio" name="status" value="n" id="RadioGroup1_1" />

        Disable

        </td>

    </tr>



    <tr>

        <td class="listrow" align="right" colspan="2">

         <input type="hidden" name="share_by" value="<?php echo $_SESSION['ao_userid']; ?>">

         <input name="submit" type="submit" value="Save">

        </td>

    </tr>

</table>

</form>

</div>









<div id="deleteRow">

<table width="100%" border="0" cellspacing="3" cellpadding="3">

  <tr>

    <td width="12%"><strong>ID</strong></td>

    <td width="23%"><strong>Event Name</strong></td>

    <td width="41%"><strong>URL</strong></td>

    <!--<td width="20%"><strong>Share By</strong></td>-->

    <td width="14%"><strong>Status</strong></td>

    <td width="10%"><strong>Action</strong></td>

  </tr>

<?php

foreach(getShareUrl() as $result){

	

	?>



     <tr >

        <td onclick="Request.make('<?=AJAX_DIR?>/share_url_edit_form.php?id=<?php echo $result['id']; ?>', 'editForm', true, true);"><?php echo $result['id']; ?></td>

        <td onclick="Request.make('<?=AJAX_DIR?>/share_url_edit_form.php?id=<?php echo $result['id']; ?>', 'editForm', true, true);"><?php echo $result['event_name']; ?></td>

        <td onclick="Request.make('<?=AJAX_DIR?>/share_url_edit_form.php?id=<?php echo $result['id']; ?>', 'editForm', true, true);"><?php echo substr($result['url'], 0, 40); ?></td>

        <!--<td onclick="Request.make('<?=AJAX_DIR?>/share_url_edit_form.php?id=<?php echo $result['id']; ?>', 'editForm', true, true);"><?php echo $result['fname'].' '.$result['lname']; ?></td>-->

        <td onclick="Request.make('<?=AJAX_DIR?>/share_url_edit_form.php?id=<?php echo $result['id']; ?>', 'editForm', true, true);"><?php if($result['status'] == 'y'){ echo 'Enable'; } else { echo 'Disable'; } ?></td>

        

         <td><a  onclick="getDeleteData('<?php echo $result['id']; ?>')" href="Javascript:void(0)"><!--[Delete]--> <i class="icon-trash" style="color:red"> </i></a></td>

         

       <!-- <td><a  onclick="Request.make('<?=AJAX_DIR?>/share_url_delete_form.php?id=<?php echo $result['id']; ?>', 'deleteRow', true, true);" href="Javascript:void(0)">[Delete]</a></td>-->

    </tr>

    <?php

	}

?>

   <tr>

    <td>&nbsp;</td>

    <td>&nbsp;</td>

    <td>&nbsp;</td>

    <!--<td>&nbsp;</td>-->

    <td>&nbsp;</td>

    <td>&nbsp;</td>

  </tr>

</table>

</div>





<script>

function getEditData(id){

	

 // test();

 document.getElementById('formAdd').style.display="none";

 document.getElementById('formEdit').style.display="block";



}



function getDeleteData(id){

	

	var result = confirm("Want to delete?");

	if (result) {

		

		Request.make('<?=AJAX_DIR?>/share_url_delete_form.php?id='+id, 'deleteRow', true, true);	

		

	}

	

}

function copyText() {
  var copyText = document.getElementById("urlShowingText");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  //alert("Copied the text: " + copyText.value);
}

</script>





</body>

</html>