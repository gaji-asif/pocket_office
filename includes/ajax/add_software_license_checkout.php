<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);

$software_type = RequestUtil::get('software_type');
$software_link = RequestUtil::get('software_link');
$login=RequestUtil::get('login');
$password=RequestUtil::get('password');
$company_license_used_for=RequestUtil::get('company_license_used_for');


$errors = array();
if(RequestUtil::get("submit")) {
    
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
    else
    {
        /*
    $sql = "select knowledgebase_name from knowledgebase where knowledgebase_name like '".$knowledgebase_name."'  and delete_flag=0 order by knowledgebase_name asc";

$res = DBUtil::query($sql);

if(!mysqli_num_rows($res)==0)
{
    $errors[] = 'Knowledge base name exist';
}
*/
}
   if(!count($errors)) {
    



    
        $sql = "INSERT INTO software_license_checkout 
                VALUES (NULL, '$software_type', '$software_link', '$login','$password','$company_license_used_for','Available',NULL,NULL, '{$_SESSION['ao_accountid']}',0)";
       DBUtil::query($sql);
?>

<script>
$(document).ready(function()
{
    try{
       var opener = window.parent;
       opener.location.reload();
     var closebutton = $('.btn-close-modal');   
     closebutton.trigger('click');
     
     
     }
     catch(e){alert(e);}
});
  
</script>
<?php
    }
}

?>





<form method="post" name="software_license_checkout_add" action="?" >
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign="center">
                    <td>Add Software license</td>
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
                        <input type="text" name="software_type" id="software_type">
                    </td>
                </tr>

               
                <tr valign="top">
                    <td class="listitem">
                        <b>Software Link:</b>
                    </td>
                    <td class="listrow">
                        <input type="text" name="software_link" id="software_link">
                    </td>
                </tr>

                <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Login:</b>&nbsp;<span class="red">*</span>
                    </td>
                    <td class="listrownoborder">
                        <input type="text" name="login" id="login">
                    </td>
                </tr>

                <tr>
                <td width="25%" class="listitemnoborder">
                        <b>Password</b>&nbsp;<span class="red">*</span>
                    </td>
                <td >
                     <input type="password" name="password" id="password">
                </td>

                </tr>
                
                <tr>
                <td width="25%" class="listitemnoborder">
                        <b>Company license used for?</b>&nbsp;<span class="red">*</span>
                    </td>
                <td >
                     <input type="text" name="company_license_used_for" id="company_license_used_for">
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
