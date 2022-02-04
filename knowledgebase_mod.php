<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if ($_SESSION['ao_founder'] != 1)
	die("Insufficient Rights");
?>

<?= ViewUtil::loadView('doc-head') ?>

<div class="btn-group pull-right page-menu">
    
    <div rel="open-modal" data-script="add_knowledgebase.php" class="btn btn-success" title="Add Knowledgebase" tooltip>
        <i class="icon-plus"></i>
    </div>
</div>

<?php
$display="";
if(!$_SESSION['ao_founder'])$display="style='display:none;'";
?>

<h1 class="page-title"><i class="icon-question"></i><?=$this_page->title?></h1>

    <table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
      <tr><td colspan=2>&nbsp;</td></tr>
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign='center' >
              <td id="headername" name="headername">
                Knowledge base
              </td>
              <td style="text-align:right;padding-right:10px;padding-top:5px;" id="searchbox" name="searchbox">
              <?php
              $account_id=$_SESSION['ao_accountid'];
              ?>
              <input type="text" id="k_search_id" name="k_search_id" onkeyup="search_knowledgebase(this.value)" />
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
            Request.make('<?=AJAX_DIR?>/get_knowledgebaselist.php', 'knowledgebasecontainer', true, true);
          </script>
        </td>
      </tr>
    </table>

    <script>
    function search_knowledgebase(n)
    {
//alert(accountid);
var xmlhttp = new XMLHttpRequest();

xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {

      var p="";
      if(this.responseText=="0")
      {
        document.getElementById("not_found_header").innerHTML="<b>No Knowledgebase found</b>";
      }
        else
          {
            document.getElementById("not_found_header").innerHTML="";
            var myObj = JSON.parse(this.responseText);
        
      
        console.log(myObj);
       
for(var i=0;i<myObj.knowledgebase.length;i++)
{
var class1='odd';
  if(i%2==0)
    class1='even';

 



    p+="<table width='100%'>";
    p+="<tr class='"+class1+"' valign='top'>";
    p+="<td width='50%'>";
    p+="<a href='javascript: Request.make(\"includes/ajax/get_knowledgebaselist.php?";
    p+="knowledgebase="+myObj.knowledgebase[i].id+"\",\"knowledgebasecontainer\",\"yes\",\"yes\");'  ";
    p+=" class='basiclink'>"+myObj.knowledgebase[i].kname+ "</a>";
    p+="</td>";
    p+="<td width='25%'  <?=$display?>>";
    p+="<div rel='open-modal' data-script='edit_knowledgebase.php?knowledgebase_id="+myObj.knowledgebase[i].id;
    p+="' class=''  tooltip='Edit'><i class='icon-pencil'></i></div></td>";
    p+="<td <?=$display?> width='25%'><a href='javascript:if(confirm(\"Are you sure?\")) Request.make(\"includes/ajax/get_knowledgebaselist.php?knowledgebase=";
    p+=myObj.knowledgebase[i].id+"&delete_flag=1\",\"knowledgebasecontainer\",\"yes\",\"yes\");' ";
    p+="tooltip='Delete'><i class='icon-trash' style='color:red' > </i></a></td></tr></table>";     
            
          


}


           
      }
      document.getElementById("kresult").innerHTML=p;

    }
};
xmlhttp.open("GET", "<?=AJAX_DIR?>/get_knowledgebase_searched_list.php?search_key="+n, true);
xmlhttp.send();

    }
    </script>
