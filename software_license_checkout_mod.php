<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if ($_SESSION['ao_founder'] != 1)
	die("Insufficient Rights");
?>

<?= ViewUtil::loadView('doc-head') ?>

<div class="btn-group pull-right page-menu">
    
    <div rel="open-modal" data-script="add_software_license_checkout.php" class="btn btn-success" title="Add Software license" tooltip>
        <i class="icon-plus"></i>
    </div>
</div>

<?php
$display="";
if(!$_SESSION['ao_founder'])$display="style='display:none;'";
?>

<h1 class="page-title"><i class="icon-signin"></i><?=$this_page->title?></h1>

    <table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
      <tr><td colspan=2>&nbsp;</td></tr>
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign='center' >
              <td id="headername" name="headername">
                Software license
              </td>
              <td style="text-align:right;padding-right:10px;padding-top:5px;" id="searchbox" name="searchbox">
              <?php
              $account_id=$_SESSION['ao_accountid'];
              ?>
              <input type="text" id="s_search_id" name="s_search_id" onkeyup="search_software_license(this.value)" />
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class='infocontainer' id='softwarelicensecontainer'></td>
      </tr>
      <tr>
        <td>
          &nbsp;
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/get_softwarelicenselist.php', 'softwarelicensecontainer', true, true);
          </script>
        </td>
      </tr>
    </table>

    <script>
    function search_softwarelicense(n)
    {
//alert(accountid);
var xmlhttp = new XMLHttpRequest();

xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {

      var p="";
      if(this.responseText=="0")
      {
        document.getElementById("not_found_header").innerHTML="<b>No Software License found</b>";
      }
        else
          {
            document.getElementById("not_found_header").innerHTML="";
            var myObj = JSON.parse(this.responseText);
        
      
        console.log(myObj);
       
for(var i=0;i<myObj.softwarelicense.length;i++)
{
var class1='odd';
  if(i%2==0)
    class1='even';

 



    p+="<table width='100%'>";
    p+="<tr class='"+class1+"' valign='top'>";
    p+="<td width='50%'>";
    p+="<a href='javascript: Request.make(\"includes/ajax/get_softwarelicenselist.php?";
    p+="softwarelicense="+myObj.softwarelicense[i].id+"\",\"softwarelicensecontainer\",\"yes\",\"yes\");'  ";
    p+=" class='basiclink'>"+myObj.softwarelicense[i].softname+ "</a>";
    p+="</td>";
    p+="<td width='25%'  <?=$display?>>";
    p+="<div rel='open-modal' data-script='edit_softwarelicense.php?softwarelicense_id="+myObj.softwarelicense[i].id;
    p+="' class=''  tooltip='Edit'><i class='icon-pencil'></i></div></td>";
    p+="<td <?=$display?> width='25%'><a href='javascript:if(confirm(\"Are you sure?\")) Request.make(\"includes/ajax/get_softwarelicenselist.php?softwarelicenselist=";
    p+=myObj.softwarelicenselist[i].id+"&delete_flag=1\",\"softwarelicensecontainer\",\"yes\",\"yes\");' ";
    p+="tooltip='Delete'><i class='icon-trash' style='color:red' > </i></a></td></tr></table>";     
            
          


}


           
      }
      document.getElementById("softlicenseresult").innerHTML=p;

    }
};
xmlhttp.open("GET", "<?=AJAX_DIR?>/get_softwarelicense_searched_list.php?search_key="+n, true);
xmlhttp.send();

    }
    </script>
