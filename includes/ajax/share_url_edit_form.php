<?php

include '../common_lib.php';

?>

<?php

$id = $_REQUEST['id'];

$userData = getUserData($id);

foreach($userData as $result){ 

?>

<form method="post" name="user" action="?">

<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">

    <tr valign="center">

        <td>

          Edit Item

        </td>

        <td align="right">

          <i class="icon-remove grey btn-close-modal"></i>

        </td>

    </tr>

</table>



<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">

    <tr valign="top">

        <td width="25%" class="listitem"><b>Event Name:</b>&nbsp;<span class="red">*</span></td>

        <td class="listrow"><input type="text" name="event_name" value="<?php echo $result['event_name']; ?>"></td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>User Select:</b>&nbsp;<span class="red">*</span></td>

        <td class="listrow">



        <select name="user_select[]" id="user_select" multiple>



    <?php

$users_array = UserModel::getAll(TRUE, $firstLast);

foreach($users_array as $user)

{

?>

              <option

              

	<?php

    $searchForValue = ',';

    $stringValue = $result['user_select'];

    

    if( strpos($stringValue, $user['user_id']) !== false ) {

    	echo 'selected="selected"';

    }

    

    ?> 

  value="<?=$user['user_id']?>"><?=$user['fname'].' '.$user['lname']; ?></option>

<?php

}

?>

  </select>

  

  </td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>Url:</b>&nbsp;<span class="red">*</span></td>

        <td class="listrow">

            <!--<input type="text" name="url" value="<?php //echo $result['url']; ?>">-->
<textarea name="url"  id="urlShowingText"  style="margin: 0px; height: 52px; width: 874px;" ><?php echo $result['url']; ?></textarea>
<a href="Javascript:void()"  onclick="copyText()">Copy text</a>

        </td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>Status:</b></td>

        <td class="listrow"> 

        <input type="radio" name="status" value="y" id="RadioGroup1_0" <?php if($result['status']=='y'){ echo 'checked="checked"'; } ?> />

        Enable

        <input type="radio" name="status" value="n" id="RadioGroup1_1" <?php if($result['status']=='n'){ echo 'checked="checked"'; } ?> />

        Disable

        </td>

    </tr>



    <tr>

        <td class="listrow" align="right" colspan="2">

         <input type="hidden" name="share_by" value="<?php echo $_SESSION['ao_userid']; ?>">

         <input type="hidden" name="id" value="<?php echo $result['id']; ?>">

         <input name="submit" type="submit" value="Save">

        </td>

    </tr>

</table>

</form>

<?php } ?>





