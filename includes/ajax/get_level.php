<?php
include '../common_lib.php';
UserModel::isAuthenticated();
if($_SESSION['ao_founder']!=1 || $_GET['id']=='')
  die("Insufficient Rights");

$sql = "select level_id, level from levels where level_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' limit 1";
$res = DBUtil::query($sql);
?>
<table width="100%" border="0" class="data-table" cellpadding=2 cellspacing="0">
<?php
list($level_id, $level)=mysqli_fetch_row($res);
if(mysqli_num_rows($res)==0) {
?>
    <tr><td align="center" colspan=2><b>Level Not Found</b></td></tr>
<?php
} 
else if(isset($_GET['action']) && $_GET['action']=='toggle' && $_GET['module']!='')
{
  if($_GET['checked'] == 'checked')
    $sql = "delete from module_access where module_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['module'])."' and level='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' and account_id='".$_SESSION['ao_accountid']."' limit 1";
  else $sql = "insert into module_access values(0, '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['module'])."', '".$_SESSION['ao_accountid']."', '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."', 0)";
  DBUtil::query($sql);
}
else if(isset($_GET['action']) && $_GET['action']=='allmodules')
{
  $sql = "delete from module_access where level='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' and account_id='".$_SESSION['ao_accountid']."'";
  DBUtil::query($sql);
  if($_GET['select'] == 'all')
  {
    $sql = "select module_id from modules";
    $res = DBUtil::query($sql);
    while(list($module_id)=mysqli_fetch_row($res))
    {
      $sql = "insert into module_access values(0, '".$module_id."', '".$_SESSION['ao_accountid']."', '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."', 0)";
      DBUtil::query($sql);
    }
  }
}
else if(isset($_GET['action']) && $_GET['action']=='allstages')
{
  $sql = "delete from stage_access where level_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' and account_id='".$_SESSION['ao_accountid']."'";
  DBUtil::query($sql);
  if($_GET['select'] == 'all')
  {
    $sql = "select stage_id from stages where account_id='".$_SESSION['ao_accountid']."'";
    $res = DBUtil::query($sql);
    while(list($stage_id)=mysqli_fetch_row($res))
    {
      $sql = "insert into stage_access values(0, '".$stage_id."', '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."', '".$_SESSION['ao_accountid']."')";
      DBUtil::query($sql);
    }
  }
}
else if(isset($_GET['action']) && $_GET['action']=='ownership' && $_GET['module']!='')
{
  if($_GET['checked'] == 'checked')
    $sql = "update module_access set ownership=0 where module_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['module'])."' and account_id='".$_SESSION['ao_accountid']."' and level='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' limit 1";
  else $sql = "update module_access set ownership=1 where module_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['module'])."' and account_id='".$_SESSION['ao_accountid']."' and level='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' limit 1";
  DBUtil::query($sql);
}
else if(isset($_GET['action']) && $_GET['action']=='navigation' && $_GET['nav']!='')
{
  if($_GET['checked'] == 'checked')
    $sql = "delete from nav_access where navigation_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['nav'])."' and level='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' and account_id='".$_SESSION['ao_accountid']."' limit 1";
  else $sql = "insert into nav_access values(0, '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['nav'])."', '".$_SESSION['ao_accountid']."', '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."')";
  DBUtil::query($sql);
}
else if(isset($_GET['action']) && $_GET['action']=='stage' && $_GET['stage']!='')
{
  if($_GET['checked'] == 'checked')
    $sql = "delete from stage_access where stage_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['stage'])."' and level_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' limit 1";
  else $sql = "insert into stage_access values(0, '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['stage'])."', '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."', '".$_SESSION['ao_accountid']."')";
  DBUtil::query($sql);
}
elseif(isset($_GET['action']) && $_GET['action']=='codegeneratorownership')
{
 if($_GET['checked'] == 'checked')
    $sql = "delete from code_generator_ownership_access where level_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' and tbl_codegenerator_job_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['codegenerator_id'])."' limit 1";
  else $sql = "insert into code_generator_ownership_access values(0, '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."', '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['codegenerator_id'])."', '1')";
  DBUtil::query($sql);
    
}
elseif(isset($_GET['action']) && $_GET['action']=='ventcalculatorownership')
{
 if($_GET['checked'] == 'checked')
   $sql = "delete from vent_calculator_ownership_access where level_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' and tbl_ventcalculator_job_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['ventcalculator_id'])."' limit 1";
  else 
   $sql = "insert into vent_calculator_ownership_access values(0, '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."', '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['ventcalculator_id'])."', '1')";
  DBUtil::query($sql);
    
}

?>

  <tr valign='top'>
    <td width=16 style='padding: 5px;'><img src='<?=IMAGES_DIR?>/icons/key_16.png'></td>
    <td class="smalltitle" style='padding: 5px;'><?php echo $level; ?></td>
  </tr>
  <tr>
    <td align="center" colspan=2>
      <table width='95%' border="0">
        <tr>
          <td colspan=2>
            <b>Module Access:</b>
            <span class='smallnote'>
              <br />A = Access
              <br />O = Ownership required
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <table border="0" width="100%">
              <tr valign='top'>
                <td width='50%'>
                  <table border="0" width="100%">
                    <tr>
                      <td align="center" width=20><b>A</b></td>
                      <td>&nbsp;</td>
                    </tr>

<?php
$sql = "select modules.module_id, modules.title, module_access.module_access_id, module_access.ownership, modules.ownership".
       " from modules".
       " left join module_access".
       " on modules.module_id=module_access.module_id and module_access.account_id='".$_SESSION['ao_accountid']."' and module_access.level='".$level_id."'".
       " group by modules.module_id order by modules.title asc";
$res = DBUtil::query($sql);

$rowBreak = round(mysqli_num_rows($res)/2);
$i=0;
while(list($module_id, $title, $access, $owner, $has_ownership)=mysqli_fetch_row($res))
{
    if($title=="Job - Add Repair"||$title=="Job - Assign Canvasser"||$title=="Job - Assign Permit"||$title=="Job - Assign Referral"||$title=="Job - Assign Warranty"
    ||$title=="Job - Delete Material Sheet"    ||$title=="Job - Delete Repair"||$title=="Job - Edit Repair"||$title=="Job - Print Summary"||
    $title=="Job - Read/Write Profitability"||$title=="Job - Schedule Gutter Job"||$title=="Job - Schedule Repair Job"    ||$title=="Job - Schedule Roofing Job"||
    $title=="Job - Schedule Siding Job"||$title=="Job - Schedule Window Job"||$title=="Job - Send Material Sheet"||$title=="Materials - View"||
    $title=="Suppliers - Modify"||$title=="Suppliers - View")
    continue;
  if($i==$rowBreak)
  {
?>
                  </table>
                </td>
                <td width='50%'>
                  <table border="0" width="100%">
                    <tr>
                      <td align="center" width=20><b>A</b></td>
                      <td align="center" width=20><b>O</b></td>
                      <td>&nbsp;</td>
                    </tr>
<?php
    $i=0;
  }

  $checked = '';
  if($access!='')
    $checked = "checked";
?>
        <tr>
          <td align="center" width=20>
            <input type='checkbox' name='<?php echo $module_id; ?>' <?php echo $checked; ?> onclick='Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=toggle&checked=<?php echo $checked; ?>&module=<?php echo $module_id; ?>","levelscontainer","","yes")'>
          </td>
<?php
  $disabled = 'disabled';
  if($access!='' && $has_ownership==1)
    $disabled = '';
  $checked_owner = '';
  if($owner==1)
    $checked_owner = "checked";
?>
          <td align="center" width=20>
            <input <?php echo $disabled; ?> type='checkbox' name='<?php echo $module_id; ?>' <?php echo $checked_owner; ?> onclick='Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=ownership&checked=<?php echo $checked_owner; ?>&module=<?php echo $module_id; ?>","levelscontainer","","yes")'>
          </td>
          <td><?php echo $title; ?></td>
        </tr>
<?php
  $i++;
}
?>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <a href='javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=allmodules&select=all","levelscontainer","","yes")' class='basiclink'>Check All</a>
            |
            <a href='javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=allmodules","levelscontainer","","yes")' class='basiclink'>Uncheck All</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td align="center" colspan=2>
      <table width='95%' border="0">
        <tr>
          <td colspan=2>
            <b>Stage Advancement Access:</b>
          </td>
        </tr>
        <tr>
          <td>
            <table border="0" width="100%">
              <tr valign='top'>
                <td width='50%'>
                  <table border="0" width="100%">

<?php
$sql = "select stages.stage_id, stages.stage, stage_access.level_id".
       " from stages".
       " left join stage_access".
       " on (stage_access.stage_id=stages.stage_id and stage_access.level_id='".$level_id."')".
       " where stages.account_id='".$_SESSION['ao_accountid']."'".
       " order by stages.stage_num asc";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
{
?>
                    <tr>
                      <td>No Stages</td>
                    </tr>
<?php
}

$rowBreak = round(mysqli_num_rows($res)/2);
$i=0;
while(list($stage_id, $stage, $access)=mysqli_fetch_row($res))
{
  if($i==$rowBreak)
  {
?>
                  </table>
                </td>
                <td width='50%'>
                  <table border="0" width="100%">
<?php
    $i=0;
  }

  $checked = '';
  if($access!='')
    $checked = "checked";
?>
        <tr>
          <td align="center" width=20>
            <input type='checkbox' name='<?php echo $stage_id; ?>' <?php echo $checked; ?> onclick='Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=stage&checked=<?php echo $checked; ?>&stage=<?php echo $stage_id; ?>","levelscontainer","","yes")'>
          </td>
          <td><?php echo $stage; ?></td>
        </tr>
<?php
  $i++;
}
?>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
<?php
if(mysqli_num_rows($res)!=0)
{
?>
        <tr>
          <td>
            <a href='javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=allstages&select=all","levelscontainer","","yes")' class='basiclink'>Check All</a>
            |
            <a href='javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=allstages","levelscontainer","","yes")' class='basiclink'>Uncheck All</a>
          </td>
        </tr>
<?php
}
?>
      </table>
    </td>
  </tr>


  <tr>
    <td align="center" colspan=2>
      <table width='95%' border="0">
        <tr>
          <td colspan=2>
            <b>Navigation Access:</b>
          </td>
        </tr>
        <tr>
          <td>
            <table border="0" width="100%">
              <tr valign='top'>
                <td width='50%'>
                  <table border="0" width="100%">
                    <tr>
                    </tr>

<?php
$sql = "select navigation.navigation_id, navigation.title, navigation.icon, nav_access.navaccess_id".
       " from navigation".
       " left join nav_access".
       " on navigation.navigation_id=nav_access.navigation_id and nav_access.account_id='".$_SESSION['ao_accountid']."' and nav_access.level='".$level_id."'".
       " order by navigation.order_num asc";
$res = DBUtil::query($sql);

$navigationArray = UIModel::getNavList();

$rowBreak = round(count($navigationArray) / 2);
$i = 0;
while(list($navigation_id, $title, $icon, $access)=mysqli_fetch_row($res))
{
    if($title=="Jobs Map"||$title=="Materials"||$title=="Suppliers")continue;
    
    if($i == $rowBreak) {
?>
                  </table>
                </td>
                <td width='50%'>
                  <table border="0" width="100%">
<?php
        $i = 0;
    }

    $checked = '';
    if($access) {
        $checked = "checked";
    }
?>
        <tr>
          <td align="center" width=20>
            <input type='checkbox' name='<?php echo $navigation_id; ?>' <?php echo $checked; ?> onclick='Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=navigation&checked=<?php echo $checked; ?>&nav=<?php echo $navigation_id; ?>","levelscontainer","","yes")'>
          </td>
          <!--<td align="center" width=20><img src='<?=IMAGES_DIR?>/icons/<?php echo $icon; ?>_16.png'></td>-->
          <td><i class="icon-<?=$icon?>"></i>&nbsp;<?php echo $title; ?></td>
        </tr>
<?php
    $i++;
}
?>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  
<?php 
$ac_id = $_SESSION['ao_accountid'];
$chcklistjob_id = RequestUtil::get('chcklistjob_id');

$sql = "SELECT tbl_checklist_job_id from checklist_level_access where account_id=".$ac_id." and level_id=".$level_id;
$access_list = DBUtil::queryToArray($sql);
$levelaccess=array();
foreach($access_list as $access)
{
    $levelaccess[]=$access['tbl_checklist_job_id'];
}

//edit
if($chcklistjob_id) 
{
    //remove from exceptions table
    if(in_array($chcklistjob_id,$levelaccess)) {
        $sql = "DELETE FROM checklist_level_access 
                WHERE level_id = '$level_id' AND tbl_checklist_job_id = '$chcklistjob_id'
                LIMIT 1";
    }
    else {        
        $sql = "INSERT INTO checklist_level_access (level_id, account_id, tbl_checklist_job_id)
                    VALUES ('$level_id', '{$_SESSION['ao_accountid']}', '$chcklistjob_id')";
        
    }
    DBUtil::query($sql);

    $sql = "SELECT user_id from users where account_id=".$ac_id." and level=".$level_id;
    $user_list = DBUtil::queryToArray($sql);
    foreach($user_list as $user)
    {
        $user_id = $user['user_id'];
        if(!empty($_GET['checked'])) 
        {
            $sql = "DELETE FROM user_checklist_job_access WHERE user_id = '$user_id' AND tbl_checklist_job_id = '$chcklistjob_id'";
        }
        else 
        {            
            $sql = "INSERT INTO user_checklist_job_access (user_id, account_id, tbl_checklist_job_id)
                        VALUES ('$user_id', '{$_SESSION['ao_accountid']}', '$chcklistjob_id')";
            
        }
        DBUtil::query($sql);
    }
}
if($_GET['action']=='allchecklist')
{
  
  if($_GET['select'] == 'all')
  {
    $sql = "select tbl_checklist_job_id from tbl_checklist_job where account_id='".$ac_id."'";
    $res = DBUtil::query($sql);
    while(list($tbl_checklist_job_id)=mysqli_fetch_row($res))
    {
      $sql = "INSERT INTO checklist_level_access (level_id, account_id, tbl_checklist_job_id)
                    VALUES ('$level_id', '{$_SESSION['ao_accountid']}', '$tbl_checklist_job_id')";
      DBUtil::query($sql);  

      $sql = "SELECT user_id from users where account_id=".$ac_id." and level=".$level_id;
      $user_list = DBUtil::queryToArray($sql);
      foreach($user_list as $user)
      {
          $user_id = $user['user_id'];
          $sql = "INSERT INTO user_checklist_job_access (user_id, account_id, tbl_checklist_job_id)
                          VALUES ('$user_id', '{$_SESSION['ao_accountid']}', '$tbl_checklist_job_id')";
           
          DBUtil::query($sql);
      }

    }
  }
  else
  {      
      $sql = "select tbl_checklist_job_id from tbl_checklist_job where account_id='".$ac_id."'";
      $res = DBUtil::query($sql);
      while(list($tbl_checklist_job_id)=mysqli_fetch_row($res))
      {
          $sql = "delete from checklist_level_access where level_id='".$level_id."' and account_id='".$ac_id."'";
          DBUtil::query($sql);

          $sql = "SELECT user_id from users where account_id=".$ac_id." and level=".$level_id;
          $user_list = DBUtil::queryToArray($sql);
          foreach($user_list as $user)
          {
              $user_id = $user['user_id'];
              $sql = "DELETE FROM user_checklist_job_access WHERE user_id ='$user_id' AND tbl_checklist_job_id = '$tbl_checklist_job_id'";
               
              DBUtil::query($sql);
          }
      }
  }
}

$sql = "SELECT tbl_checklist_job_id from checklist_level_access where account_id=".$ac_id." and level_id=".$level_id;
$access_list = DBUtil::queryToArray($sql);
$levelaccess=array();
foreach($access_list as $access)
{
    $levelaccess[]=$access['tbl_checklist_job_id'];
}
?>


  <tr>
    <td align="center" colspan=2>
      <table width='95%' border="0">
        <tr>
          <td colspan=2>
            <b>Checklist Job Access:</b>
          </td>
        </tr>
        <tr>
          <td>
              
              
              <input type="checkbox" id="boxchecklist" checked="true" onclick="checkuncheckchecklist()"> <b>Check All</b>
              
              <script>
              $(function() {
               $( ".checklistcheck" ).each(function() {
                            if (!$(this).is(':checked')) {
                                $('#boxchecklist').prop('checked', false);
                            }
                          });
                });
                  function checkuncheckchecklist()
                  {
                       if (document.getElementById('boxchecklist').checked) {
                        javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=allchecklist&select=all","levelscontainer","","yes");
                       } else {
                        javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=allchecklist&select=","levelscontainer","","yes");
                       }
                  }
              </script>
           </td>
        </tr>
        <tr>
          <td>
            <table border="0" width="100%">
              <tr valign='top'>
                <td width='50%'>
                  <table border="0" width="100%">

                    <?php
                    $sql = "SELECT checklist_id,checklist_name from tbl_checklist where account_id=".$ac_id." order by order_num asc";
                    //echo $sql;
                    $checklist = DBUtil::queryToArray($sql);

                    if(count($checklist)==0)
                    {
                    ?>
                                        <tr>
                                          <td>No Checklist Item Found</td>
                                        </tr>
                    <?php
                    }

                    
                    $wdth = (100/count($checklist)).'%';

                    foreach($checklist as $r)
                    {?>
                    <th width="<?=$wdth?>" style="text-align: left;padding-left: 20px;"><?=$r['checklist_name']?></th>

                    <?php } ?>
                    </tr>

                    <tr>
                    <?php foreach($checklist as $row)
                    {?>
                    <td width="<?=$wdth?>" style="vertical-align:top;">
                    <table>

                    <?php 
                    $sql = "SELECT t1.tbl_checklist_job_id,t1.name from tbl_checklist_job as t1 left 
                                                join tbl_checklist as t2 on t2.checklist_id=t1.checklist_id  
                                                where t1.account_id=".$ac_id." and t1.checklist_id=".$row['checklist_id']." and t1.is_deleted='0'
                                                order by t1.order_num asc";
                      
                    $joblist = DBUtil::queryToArray($sql);
                    foreach($joblist as $job_row)
                    {
                        $class = '';
                        $checked = '';
                        $exception = false;
                        if(in_array($job_row['tbl_checklist_job_id'],$levelaccess)) {
                            $checked = 'checked';
                            $exception = true;
                        }

                        if($exception) {
                            $class = "red";
                        }
                    ?>
                        <tr>
                            <td width="20"><input onclick='Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=checklist&checked=<?=$checked?>&chcklistjob_id=<?=$job_row['tbl_checklist_job_id']?>","levelscontainer","","yes")' type="checkbox" class="checklistcheck"  <?=$checked?> /></td>
                            <td class="<?=$class?>"><?=$job_row['name']?></td>
                        </tr>
                    <?php }?>
                    </table>
                    </td>
                    <?php }?>

              </tr>
            </table>
          </td>
        </tr>
        <?php
        if(count($checklist)!=0)
        {
        ?>
        <!--<tr>
          <td>
            <a href='javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=allchecklist&select=all","levelscontainer","","yes")' class='basiclink'>Check All</a>
            |
            <a href='javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=allchecklist&select=","levelscontainer","","yes")' class='basiclink'>Uncheck All</a>
          </td>
        </tr>-->
        <?php
        }
        ?>
      </table>
    </td>
  </tr>

      </table>
    </td>
  </tr>
  <!--code generator section -->
<?php 
$ac_id = $_SESSION['ao_accountid'];
$codegenerator_id = RequestUtil::get('codegenerator_id');

$sql = "SELECT tbl_codegenerator_job_id from codegenerator_level_access where account_id=".$ac_id." and level_id=".$level_id;
$access_list = DBUtil::queryToArray($sql);
$levelaccess=array();
foreach($access_list as $access)
{
    $levelaccess[]=$access['tbl_codegenerator_job_id'];
}


if($codegenerator_id and $_GET['action']!='codegeneratorownership') 
{
    //remove from exceptions table
    if(in_array($codegenerator_id,$levelaccess)) {
         $sql = "DELETE FROM codegenerator_level_access 
                WHERE level_id = '$level_id' AND tbl_codegenerator_job_id = '$codegenerator_id'
                LIMIT 1";
    }
    else {        
        $sql = "INSERT INTO codegenerator_level_access (level_id, account_id, tbl_codegenerator_job_id)
                    VALUES ('$level_id', '{$_SESSION['ao_accountid']}', '$codegenerator_id')";
        
    }
    DBUtil::query($sql);

    $sql = "SELECT user_id from users where account_id=".$ac_id." and level=".$level_id;
    $user_list = DBUtil::queryToArray($sql);
    foreach($user_list as $user)
    {
        $user_id = $user['user_id'];
        if(!empty($_GET['checked'])) 
        {
            $sql = "DELETE FROM user_codegenerator_job_access WHERE user_id = '$user_id' AND tbl_codegenerator_job_id = '$codegenerator_id'";
        }
        else 
        {            
            $sql = "INSERT INTO user_codegenerator_job_access (user_id, account_id, tbl_codegenerator_job_id, has_access)
                        VALUES ('$user_id', '{$_SESSION['ao_accountid']}', '$codegenerator_id','1')";
            
        }
        DBUtil::query($sql);
    }
}
if($_GET['action']=='allcodegenerator')
{
  
  if($_GET['select'] == 'all')
  {
    $sql = "select tbl_codegenerator_job_id from tbl_codegenerator_job where account_id='".$ac_id."'";
    $res = DBUtil::query($sql);
    while(list($tbl_codegenerator_job_id)=mysqli_fetch_row($res))
    {
      $sql = "INSERT INTO code_generator_ownership_access (level_id, tbl_codegenerator_job_id, ownership)
                    VALUES ('$level_id', '$tbl_codegenerator_job_id','1')";
      DBUtil::query($sql);  

      

    }
  }
  else
  {      
      $sql = "select tbl_codegenerator_job_id from tbl_codegenerator_job where account_id='".$ac_id."'";
      $res = DBUtil::query($sql);
      while(list($tbl_codegenerator_job_id)=mysqli_fetch_row($res))
      {
          $sql = "delete from code_generator_ownership_access where level_id='".$level_id."'";
          DBUtil::query($sql);

         
      }
  }
}

$sql = "SELECT tbl_codegenerator_job_id from codegenerator_level_access where account_id=".$ac_id." and level_id=".$level_id;
$access_list = DBUtil::queryToArray($sql);
$levelaccess=array();
foreach($access_list as $access)
{
    $levelaccess[]=$access['tbl_codegenerator_job_id'];
}
?>
 <tr>
    <td align="center" colspan=2>
      <table width='95%' border="0">
          
          <tr>
          <td colspan=2>
           &nbsp;
          </td>
        </tr>
       <tr>
          <td colspan=2>
            <b>Code Generator Job Access:</b>
            <span class='smallnote'>
              <br />A = Access
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <table border="0" width="100%">
              <tr valign='top'>
                <td width='50%'>
                  <table border="0" width="100%">
                    <?php
                    $sql = "SELECT codegenerator_id,codegenerator_name from tbl_codegenerator where account_id=".$ac_id." order by order_num asc";
                    //echo $sql;
                    $codegenerator = DBUtil::queryToArray($sql);

                    if(count($codegenerator)==0)
                    {
                    ?>
                                        <tr>
                                          <td>No code generator Item Found</td>
                                        </tr>
                    <?php
                    }

                    
                    $wdth = (100/count($codegenerator)).'%';

                    foreach($codegenerator as $r)
                    {?>
                    <th width="<?=$wdth?>" style="text-align: left;padding-left: 20px;"><?=$r['codegenerator_name']?></th>

                    <?php } ?>
                    </tr>

                    <tr>
                    <?php foreach($codegenerator as $row)
                    {?>
                    <td width="<?=$wdth?>" style="vertical-align:top;">
                    <table>
                      <tr>
                      <td width="20" align="center"><b>A</b></td>
                      <!--<td width="20" align="center"><b>O</b></td>-->
                      <td>&nbsp;</td>
                    </tr>
                    <?php 
                    $sql = "SELECT t1.tbl_codegenerator_job_id,t1.name from tbl_codegenerator_job as t1 left 
                                                join tbl_codegenerator as t2 on t2.codegenerator_id=t1.codegenerator_id 
                                                where t1.account_id=".$ac_id." and t1.codegenerator_id=".$row['codegenerator_id']." and t1.is_deleted='0'
                                                order by t1.order_num asc";
                      
                    $joblist = DBUtil::queryToArray($sql);
                    foreach($joblist as $job_row)
                    {
                        $class = '';
                        $checked = '';
                        $exception = false;
                        if(in_array($job_row['tbl_codegenerator_job_id'],$levelaccess)) {
                            $checked = 'checked';
                            $exception = true;
                        }

                        if($exception) {
                            $class = "red";
                        }
                         $sql2 = "SELECT * FROM `code_generator_ownership_access` WHERE level_id=".$level_id." AND tbl_codegenerator_job_id=".$job_row['tbl_codegenerator_job_id'];
                         $levelwiseownership = DBUtil::queryToArray($sql2);
                         //print_r($levelwiseownership);
                        // echo in_array($job_row['tbl_codegenerator_job_id'],$levelwiseownership);
                        $checked2 = '';
                          if($job_row['tbl_codegenerator_job_id']==$levelwiseownership[0]['tbl_codegenerator_job_id']) {
                             $checked2 = 'checked';
                            
                        }
                    ?>
                        <tr>
                            <!--<td width="20"><input onclick='Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=codegenerator&checked=<?=$checked?>&codegenerator_id=<?=$job_row['tbl_codegenerator_job_id']?>","levelscontainer","","yes")' type="checkbox"  <?=$checked?> /></td>
                             --><td align="center" width=20>
           <input type="checkbox" onclick='Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=codegeneratorownership&checked=<?=$checked2?>&codegenerator_id=<?=$job_row['tbl_codegenerator_job_id']?>","levelscontainer","","yes")' type="checkbox"  <?=$checked2?>/>
          </td>
                            <td class="<?=$class?>"><?=$job_row['name']?></td>
                        </tr>
                    <?php }?>
                    </table>
                    </td>
                    <?php }?>

              </tr>
            </table>
          </td>
        </tr>
        <?php
        if(count($codegenerator)!=0)
        {
        ?>
       <tr>
          <td>
            <a href='javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=allcodegenerator&select=all","levelscontainer","","yes")' class='basiclink'>Check All</a>
            |
            <a href='javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=allcodegenerator&select=","levelscontainer","","yes")' class='basiclink'>Uncheck All</a>
          </td>
        </tr>
        <?php
        }
        ?>
      </table>
    </td>
  </tr>

      </table>
    </td>
  </tr>
<!--code generator section -->

<tr><td colspan=10>&nbsp;</td></tr>



<tr><td colspan=10>&nbsp;</td></tr>

<!--Start To Do List -->
<?php 
$ac_id = $_SESSION['ao_accountid'];
$todolistjob_id = RequestUtil::get('todolistjob_id');

$sql = "SELECT tbl_todolist_job_id from todolist_level_access where account_id=".$ac_id." and level_id=".$level_id;
$access_list = DBUtil::queryToArray($sql);
$levelaccess=array();
foreach($access_list as $access)
{
    $levelaccess[]=$access['tbl_todolist_job_id'];
}

//edit
if($todolistjob_id) 
{
    //remove from exceptions table
    if(in_array($todolistjob_id,$levelaccess)) {
        $sql = "DELETE FROM todolist_level_access 
                WHERE level_id = '$level_id' AND tbl_todolist_job_id = '$todolistjob_id'
                LIMIT 1";
    }
    else {        
        $sql = "INSERT INTO todolist_level_access (level_id, account_id, tbl_todolist_job_id)
                    VALUES ('$level_id', '{$_SESSION['ao_accountid']}', '$todolistjob_id')";
        
    }
    DBUtil::query($sql);

    $sql = "SELECT user_id from users where account_id=".$ac_id." and level=".$level_id;
    $user_list = DBUtil::queryToArray($sql);
    foreach($user_list as $user)
    {
        $user_id = $user['user_id'];
        if(!empty($_GET['checked'])) 
        {
            $sql = "DELETE FROM todolist_user_access WHERE user_id = '$user_id' AND tbl_todolist_job_id = '$todolistjob_id'";
        }
        else 
        {            
            $sql = "INSERT INTO todolist_user_access (user_id, account_id, tbl_todolist_job_id)
                        VALUES ('$user_id', '{$_SESSION['ao_accountid']}', '$todolistjob_id')";
            
        }
        DBUtil::query($sql);
    }
}
if(isset($_GET['action']) && $_GET['action']=='alltodolist')
{
  
  if($_GET['select'] == 'all')
  {
    $sql = "select tbl_todolist_job_id from tbl_todolist_job where account_id='".$ac_id."'";
    $res = DBUtil::query($sql);
    while(list($tbl_todolist_job_id)=mysqli_fetch_row($res))
    {
      $sql = "INSERT INTO todolist_level_access (level_id, account_id, tbl_todolist_job_id)
                    VALUES ('$level_id', '{$_SESSION['ao_accountid']}', '$tbl_todolist_job_id')";
      DBUtil::query($sql);  

      $sql = "SELECT user_id from users where account_id=".$ac_id." and level=".$level_id;
      $user_list = DBUtil::queryToArray($sql);
      foreach($user_list as $user)
      {
          $user_id = $user['user_id'];
          $sql = "INSERT INTO todolist_user_access (user_id, account_id, tbl_todolist_job_id)
                          VALUES ('$user_id', '{$_SESSION['ao_accountid']}', '$tbl_todolist_job_id')";
           
          DBUtil::query($sql);
      }

    }
  }
  else
  {      
      $sql = "select tbl_todolist_job_id from tbl_todolist_job where account_id='".$ac_id."'";
      $res = DBUtil::query($sql);
      while(list($tbl_todolist_job_id)=mysqli_fetch_row($res))
      {
          $sql = "delete from todolist_level_access where level_id='".$level_id."' and account_id='".$ac_id."'";
          DBUtil::query($sql);

          $sql = "SELECT user_id from users where account_id=".$ac_id." and level=".$level_id;
          $user_list = DBUtil::queryToArray($sql);
          foreach($user_list as $user)
          {
              $user_id = $user['user_id'];
              $sql = "DELETE FROM todolist_user_access WHERE user_id ='$user_id' AND tbl_todolist_job_id = '$tbl_todolist_job_id'";
               
              DBUtil::query($sql);
          }
      }
  }
}

$sql = "SELECT tbl_todolist_job_id from todolist_level_access where account_id=".$ac_id." and level_id=".$level_id;
$access_list = DBUtil::queryToArray($sql);
$levelaccess=array();
foreach($access_list as $access)
{
    $levelaccess[]=$access['tbl_todolist_job_id'];
}
?>


  <tr>
    <td align="center" colspan=2>
      <table width='95%' border="0">
        <tr>
          <td colspan=2>
            <b>To Do List Job Access:</b>
          </td>
        </tr>
        <tr>
          <td>
            <table border="0" width="100%">
              <tr valign='top'>
                <td width='50%'>
                  <table border="0" width="100%">
                    <tr>
                      <?php
                      $sql = "SELECT todolist_id,todolist_name from tbl_todolist where account_id=".$ac_id." order by order_num asc";
                      //echo $sql;
                      $todolist = DBUtil::queryToArray($sql);

                              if(count($todolist)==0)
                              {
                              ?>
                                            <td>No todolist Item Found</td>
                              <?php
                              }

                      
                      $wdth = (100/count($todolist)).'%';

                      foreach($todolist as $r)
                      {?>
                      <th width="<?=$wdth?>" style="text-align: left;padding-left: 20px;"><?=$r['todolist_name']?></th>

                      <?php } ?>
                    </tr>

                    <tr>
                      <?php foreach($todolist as $row)
                      {?>
                      <td width="<?=$wdth?>" style="vertical-align:top;">
                      <table>

                      <?php 
                      $sql = "SELECT t1.tbl_todolist_job_id,t1.name from tbl_todolist_job as t1 left 
                                                  join tbl_todolist as t2 on t2.todolist_id=t1.todolist_id  
                                                  where t1.account_id=".$ac_id." and t1.todolist_id=".$row['todolist_id']." and t1.is_deleted='0'
                                                  order by t1.order_num asc";
                        
                      $joblist = DBUtil::queryToArray($sql);
                      foreach($joblist as $job_row)
                      {
                          $class = '';
                          $checked = '';
                          $exception = false;
                          if(in_array($job_row['tbl_todolist_job_id'],$levelaccess)) {
                              $checked = 'checked';
                              $exception = true;
                          }

                          if($exception) {
                              $class = "red";
                          }
                          ?>
                          <tr>
                              <td width="20"><input onclick='Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=todolist&checked=<?=$checked?>&todolistjob_id=<?=$job_row['tbl_todolist_job_id']?>","levelscontainer","","yes")' type="checkbox"  <?=$checked?> /></td>
                              <td class="<?=$class?>"><?=$job_row['name']?></td>
                          </tr>
                      <?php }?>
                      </table>
                      </td>
                      <?php }?>

                    </tr>
                  </table>
                </td>
              </tr>
              <?php
              if(count($todolist)!=0)
              {
              ?>
                <tr>
                  <td>
                    <a href='javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=alltodolist&select=all","levelscontainer","","yes")' class='basiclink'>Check All</a>
                    |
                    <a href='javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=alltodolist&select=","levelscontainer","","yes")' class='basiclink'>Uncheck All</a>
                  </td>
                </tr>
              <?php
              }
              ?>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <!--End To Do List -->
<?php
//print_r($_GET);
$ac_id = $_SESSION['ao_accountid'];
$ventcalculator_id = RequestUtil::get('ventcalculator_id');

$sql = "SELECT tbl_ventcalculator_job_id from ventcalculator_level_access where account_id=".$ac_id." and level_id=".$level_id;
$access_list = DBUtil::queryToArray($sql);
$levelaccess=array();
foreach($access_list as $access)
{
    $levelaccess[]=$access['tbl_ventcalculator_job_id'];
}

if($ventcalculator_id and $_GET['action']!='ventcalculatorownership') 
{
  

    //remove from exceptions table
    if(in_array($ventcalculator_id,$levelaccess)) {
         $sql = "DELETE FROM ventcalculator_level_access 
                WHERE level_id = '$level_id' AND tbl_ventcalculator_job_id = '$ventcalculator_id'
                LIMIT 1";
    }
    else {        
        $sql = "INSERT INTO ventcalculator_level_access (level_id, account_id, tbl_ventcalculator_job_id)
                    VALUES ('$level_id', '{$_SESSION['ao_accountid']}', '$ventcalculator_id')";
        
    }
    DBUtil::query($sql);

    $sql = "SELECT user_id from users where account_id=".$ac_id." and level=".$level_id;
    $user_list = DBUtil::queryToArray($sql);
    foreach($user_list as $user)
    {
        $user_id = $user['user_id'];
        if(!empty($_GET['checked'])) 
        {
            $sql = "DELETE FROM user_ventcalculator_job_access WHERE user_id = '$user_id' AND tbl_ventcalculator_job_id = '$ventcalculator_id'";
        }
        else 
        {            
            $sql = "INSERT INTO user_ventcalculator_job_access (user_id, account_id, tbl_ventcalculator_job_id, has_access)
                        VALUES ('$user_id', '{$_SESSION['ao_accountid']}', '$ventcalculator_id','1')";
            
        }
        DBUtil::query($sql);
    }
}  
  if($_GET['action']=='allventcalculator')
{
  
  if($_GET['select'] == 'all')
  {
    $sql = "select tbl_ventcalculator_job_id from tbl_ventcalculator_job where account_id='".$ac_id."'";
    $res = DBUtil::query($sql);
    while(list($tbl_ventcalculator_job_id)=mysqli_fetch_row($res))
    {
      $sql = "INSERT INTO vent_calculator_ownership_access (level_id, tbl_ventcalculator_job_id, ownership)
                    VALUES ('$level_id', '$tbl_ventcalculator_job_id','1')";
      DBUtil::query($sql);  

      

    }
  }
  else
  {      
      $sql = "select tbl_ventcalculator_job_id from tbl_ventcalculator_job where account_id='".$ac_id."'";
      $res = DBUtil::query($sql);
      while(list($tbl_ventcalculator_job_id)=mysqli_fetch_row($res))
      {
          $sql = "delete from vent_calculator_ownership_access where level_id='".$level_id."'";
          DBUtil::query($sql);

         
      }
  }
}

 $sql = "SELECT tbl_ventcalculator_job_id from ventcalculator_level_access where account_id=".$ac_id." and level_id=".$level_id;
$access_list = DBUtil::queryToArray($sql);
$levelaccess=array();
foreach($access_list as $access)
{
    $levelaccess[]=$access['tbl_ventcalculator_job_id'];
}
?>
 <tr>
    <td align="center" colspan=2>
      <table width='95%' border="0">
          
          <tr>
          <td colspan=2>
           &nbsp;
          </td>
        </tr>
       <tr>
          <td colspan=2>
            <b>Vent Calculator Job Access:</b>
            <span class='smallnote'>
              <br />A = Access
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <table border="0" width="100%">
              <tr valign='top'>
                <td width='50%'>
                  <table border="0" width="100%">
                    <?php
                    $sql = "SELECT ventcalculator_id,ventcalculator_name from tbl_ventcalculator where account_id=".$ac_id." order by order_num asc";
                    //echo $sql;
                    $ventcalculator = DBUtil::queryToArray($sql);

                    if(count($ventcalculator)==0)
                    {
                    ?>
                                        <tr>
                                          <td>No Vent Calculator Item Found</td>
                                        </tr>
                    <?php
                    }

                    
                    $wdth = (100/count($ventcalculator)).'%';

                    foreach($ventcalculator as $r)
                    {?>
                    <th width="<?=$wdth?>" style="text-align: left;padding-left: 20px;"><?=$r['ventcalculator_name']?></th>

                    <?php } ?>
                    </tr>

                    <tr>
                    <?php foreach($ventcalculator as $row)
                    {?>
                    <td width="<?=$wdth?>" style="vertical-align:top;">
                    <table>
                      <tr>
                      <td width="20" align="center"><b>A</b></td>
                      <!--<td width="20" align="center"><b>O</b></td>-->
                      <td>&nbsp;</td>
                    </tr>
                    <?php 
                    $sql = "SELECT t1.tbl_ventcalculator_job_id,t1.name from tbl_ventcalculator_job as t1 left 
                                                join tbl_ventcalculator as t2 on t2.ventcalculator_id=t1.ventcalculator_id 
                                                where t1.account_id=".$ac_id." and t1.ventcalculator_id=".$row['ventcalculator_id']." and t1.is_deleted='0'
                                                order by t1.order_num asc";
                      
                    $joblist = DBUtil::queryToArray($sql);
                    foreach($joblist as $job_row)
                    {
                        $class = '';
                        $checked = '';
                        $exception = false;
                        if(in_array($job_row['tbl_ventcalculator_job_id'],$levelaccess)) {
                            $checked = 'checked';
                            $exception = true;
                        }

                        if($exception) {
                            $class = "red";
                        }
                         $sql2 = "SELECT * FROM `vent_calculator_ownership_access` WHERE level_id=".$level_id." AND tbl_ventcalculator_job_id=".$job_row['tbl_ventcalculator_job_id'];
                         $levelwiseownership = DBUtil::queryToArray($sql2);
                         //print_r($levelwiseownership);
                         //echo in_array($job_row['tbl_codegenerator_job_id'],$levelwiseownership);
                         $checked2 = '';
                          if($job_row['tbl_ventcalculator_job_id']==$levelwiseownership[0]['tbl_ventcalculator_job_id']) {
                             $checked2 = 'checked';
                            
                        }
                        
                        //print_r($job_row);
                    ?>
                        <tr>
                               <td align="center" width=20>
           <input type="checkbox" onclick='Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=ventcalculatorownership&checked=<?=$checked2?>&ventcalculator_id=<?=$job_row['tbl_ventcalculator_job_id']?>","levelscontainer","","yes")' type="checkbox"  <?=$checked2?>/>
          </td>
                            <td class="<?=$class?>"><?=$job_row['name']?></td>
                        </tr>
                    <?php }?>
                    </table>
                    </td>
                    <?php }?>

              </tr>
            </table>
          </td>
        </tr>
        <?php
        if(count($ventcalculator)!=0)
        {
        ?>
       <tr>
          <td>
            <a href='javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=allventcalculator&select=all","levelscontainer","","yes")' class='basiclink'>Check All</a>
            |
            <a href='javascript:Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>&action=allventcalculator&select=","levelscontainer","","yes")' class='basiclink'>Uncheck All</a>
          </td>
        </tr>
        <?php
        }
        ?>
      </table>
    </td>
  </tr>

      </table>
    </td>
  </tr>
<!--vent calculator section -->
  <tr><td colspan=10>&nbsp;</td></tr>
  <tr>
    <td colspan=10 class='infofooter'>
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td>
			<a href="javascript:Request.make('includes/ajax/get_levelslist.php', 'levelscontainer', true, true);" class='basiclink'>
				<i class="icon-double-angle-left"></i>&nbsp;Back
			</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
