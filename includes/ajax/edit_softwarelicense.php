<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);
$ss="";
$software_type = RequestUtil::get('software_type');
$software_link = RequestUtil::get('software_link');
$login=RequestUtil::get('login');
$password=RequestUtil::get('password');
$company_license_used_for=RequestUtil::get('company_license_used_for');
$software_license_checkout_id=RequestUtil::get('software_license_checkout_id');



$errors = array();
if(RequestUtil::get("submit"))
{
      if(empty($software_type)) {
        $errors[] = 'Software Type cannot be blank';
    }
    elseif(empty($software_link)) {
        $errors[] = 'Software Link cannot be blank';
    }
    elseif(empty($login)) {
        $errors[] = 'Login cannot be blank';
    }
    elseif(empty($password)) {
        $errors[] = 'Password cannot be blank';
    }

    
   


    if(!count($errors)) {
        $sql = "update software_license_checkout set software_type='".$software_type."', software_link='".$software_link."',login='".$login."',password='".$password."',company_license_used_for='".$company_license_used_for."' where software_license_checkout_id=".$software_license_checkout_id;
           DBUtil::query($sql);
?>

<!-- <script>
    window.location = 'edit_knowledgebase.php?id=<?=DBUtil::getInsertId()?>';
</script> -->

<script>
$(document).ready(function()
{
    try{
       // alert('loaded');
       var opener = window.parent;
       opener.location.reload();
       var closebutton = $('.btn-close-modal');   
       closebutton.trigger('click');
      }
     catch(e)
     {
      alert(e);
     }
});
  
</script>
<?php
   } 
}


?>






<?php
if(RequestUtil::get("software_license_checkout_id")) 
{
$software_license_checkout_id = RequestUtil::get('software_license_checkout_id');

$sql = "select software_license_checkout_id,software_type,software_link,login,password,company_license_used_for from software_license_checkout where software_license_checkout_id=".$software_license_checkout_id;
//echo $sql;
$res = DBUtil::query($sql);


//$res = DBUtil::query($sql);
list($software_license_checkout_id, $software_type,$software_link,$login,$password,$company_license_used_for)=mysqli_fetch_row($res);


}

?>
<form method="post" name="software_license_checkout_update" action="?" >
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign="center">
                    <td>Update Software license</td>
                    <td align="right">
                        <i class="icon-remove grey btn-close-modal"></i>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
<?php
if(count($errors)) {
?>
    <tr>
        <td><?=AlertUtil::generate($errors)?></td>
    </tr>
<?php
}
?>
    <tr>
        <td class="infocontainernopadding">
            <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
                <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Software Type:</b>&nbsp;<span class="red">*</span>

                    </td>
                    <td class="listrownoborder">
                        <input type="hidden" id="software_license_checkout_id" name="software_license_checkout_id" value="<?=$software_license_checkout_id?>" />
                        <input type="text" name="software_type" id="software_type" value="<?=$software_type?>">
                    </td>
                </tr>

               
                <tr valign="top">
                    <td class="listitem">
                        <b>Software Link:</b>
                    </td>
                    <td class="listrow">
                        <input type="text" name="software_link" id="software_link" value="<?=$software_link?>">
                    </td>
                </tr>

                <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Login:</b>&nbsp;<span class="red">*</span>
                    </td>
                    <td class="listrownoborder">
                        <input type="text" name="login" id="login" value="<?=$login?>">
                    </td>
                </tr>

                <tr>
                <td width="25%" class="listitemnoborder">
                        <b>Password</b>&nbsp;<span class="red">*</span>
                    </td>
                <td >
                     <input type="password" name="password" id="password" value="<?=$password?>">
                </td>

                </tr>
                
                <tr>
                <td width="25%" class="listitemnoborder">
                        <b>Company license used for?</b>&nbsp;<span class="red">*</span>
                    </td>
                <td >
                     <input type="text" name="company_license_used_for" id="company_license_used_for" value="<?=$company_license_used_for?>">
                </td>

                </tr>
                
                


                <tr>
                    <td align="right" colspan="2" class="listrow">
                        <input name="submit" type="submit" value="Submit and Close">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>
<script>

</script>

</body>
</html>
