<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');

$sql = "select job_id,job_number from jobs where account_id='{$_SESSION['ao_accountid']}'";
$result = DBUtil::queryToArray($sql);

$id = RequestUtil::get('id');
$errors = array();
$success = array();
if(!empty($_POST['job_to']))
{ 
        
    $sql = "UPDATE gmail_import set job_id='" . $_POST['job_to'] . "' WHERE id='" . $_POST['mail_id'] . "'"; 

    if(DBUtil::query($sql))
    {
        $success[] = 'Email attached with job successfully done.';
    }
    else
    {
        $errors[] = 'Failed to attach with job!';
    }

    ?>

    <script>
          Request.makeModal('<?=AJAX_DIR?>/get_emaillist.php?id=<?php echo $id; ?>', 'emailscontainer',true,true,true);
    </script>

 <?php  
    
}

?>

    <?=AlertUtil::generate($errors, 'error', TRUE)?>

    <?=AlertUtil::generate($success, 'success', TRUE)?>

    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">


      <tr id="add_contacts">

        <td class="infocontainernopadding">

          <table width="100%" border="0" cellspacing="0" cellpadding="0">

            <tr>

              <td>

                <form method="post" name='edit_trip' action='<?=$_SERVER['PHP_SELF'];?>' enctype="multipart/form-data">

                  <table border="0" width="100%" cellspacing="0" cellpadding="0">

                    <tr> 

                      <td>&nbsp;</td> 

                    </tr>

                    <input  type="hidden" name="mail_id" value="<?php if(isset($id)) echo $id; ?>">
                    
                    <tr>
                      <td class="listitem" ><b>Job Number:</b> <span class="red">*</span></td>
                      <td class="listitemnoborder">
                        <style type="text/css">
                          .tss-multiselect-search {
                            width: 500px;
                            height: 30px;
                          }
                          .testuser .removeuser {
                             display: block; 
                            margin-bottom: 10px;
                          }

                          .testuser .removeuser .icon-remove {
                             display: none; 
                          }
                        </style>
                          <div class="testuser"></div>
                          <select name="job_to" required="" class="tss-multiselect-search" data-placeholder="search Job">
                            <?php foreach($result as $job){?>
                                  <option value="<?= $job['job_id'] ?>"><?= $job['job_number'] ?></option>
                            <?php }?>
                          </select>
                      </td>
                    </tr>

                    

                    <tr>

                      <td class="listitem"></td>

                      <td class="listitemnoborder" >

                          <input type="submit" class="btn btn-primary" value="Submit">

                      </td>

                    </tr>

                  </table>

                  </form>                  

                </td>

            </tr>

          </table>

        </td>

      </tr>

    </table>



  </body>

</html>
