<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if ($_SESSION['ao_founder'] != 1)
	die("Insufficient Rights");

$id = RequestUtil::get('id');
?>

<?= ViewUtil::loadView('doc-head') ?>

<div class="btn-group pull-right page-menu">
    
    <div rel="open-modal" data-script="ventcalculator_job/add_job.php?id=<?=$id?>" class="btn btn-success" title="Add Vent Calculator job" tooltip>
        <i class="icon-plus"></i>
    </div>
</div>

<?php
$display="";
if(!$_SESSION['ao_founder'])$display="style='display:none;'";


?>

<h1 class="page-title"><i class="icon-question"></i>Vent Calculator job</h1>

    <table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
      <tr><td colspan=2>&nbsp;</td></tr>
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign='center' >
              <td id="headername" name="headername">
                Vent Calculator Job list
              </td>
              <td style="text-align:right;padding-right:10px;padding-top:5px;" id="searchbox" name="searchbox">
              <?php
              $account_id=$_SESSION['ao_accountid'];
              ?>
              <input type="button" value="Set Order" class="btn btn-blue pull-right"  data-script="ventcalculator_job_order.php?id=<?=$id?>" rel="open-modal" ></input>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class='infocontainer' id='knowledgebasecontainer'></td>
      </tr>
      <tr>
        <td>
          &nbsp;
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/ventcalculator_job/get_joblist.php?id=<?=$id?>', 'knowledgebasecontainer', true, true);
          </script>
        </td>
      </tr>
    </table>

    <script>
    function search_checklistjob(n)
    {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) 
        {
          var p="";
          if(this.responseText=="0")
          {
            document.getElementById("not_found_header").innerHTML="<b>No Job found</b>";
          }
          else
          {
              document.getElementById("not_found_header").innerHTML="";
              var myObj = JSON.parse(this.responseText);
              console.log(myObj);
         
              for(var i=0;i<myObj.joblist.length;i++)
              {
                var class1='odd';
                if(i%2==0)
                  class1='even';

                  p+="<table width='100%'>";
                  p+="<tr class='"+class1+"' valign='top'>";
                  p+="<td width='60%'>";
                  p+="<a href='javascript: Request.make(\"includes/ajax/ventcalculator_job/get_joblist.php?";
                  p+="id=<?=$id?>&checklist_job_id="+myObj.joblist[i].id+"\",\"knowledgebasecontainer\",\"yes\",\"yes\");'  ";
                  p+=" class='basiclink'>"+myObj.joblist[i].kname+ "</a>";
                  p+="</td>";
                  p+="<td width='20%'  <?=$display?>>"+myObj.joblist[i].type+ "</td>";
                  p+="<td width='20%'  <?=$display?>>";
                  p+="<div style='cursor: pointer;float: left;'  rel='open-modal' data-script='ventcalculator_job/edit_job.php?checklist_job_id="+myObj.joblist[i].id;
                  p+="' class=''  tooltip='Edit'><i class='icon-pencil'></i></div>";
                  p+="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:if(confirm(\"Are you sure?\")) Request.make(\"includes/ajax/ventcalculator_job/get_joblist.php?id=<?=$id?>&ventcalculator_job_id=";
                  p+=myObj.joblist[i].id+"&delete_flag=1\",\"knowledgebasecontainer\",\"yes\",\"yes\");' ";
                  p+="tooltip='Delete'><i class='icon-trash' style='color:red' > </i></a></td></tr></table>";     
                     
              }           
            }
            document.getElementById("kresult").innerHTML=p;
          }
          };
          xmlhttp.open("GET", "<?=AJAX_DIR?>/ventcalculator_job/get_job_searched_list.php?id=<?=$id?>&search_key="+n, true);
          xmlhttp.send();
        
    }
    </script>
