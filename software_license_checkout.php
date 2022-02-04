<?php

include 'includes/common_lib.php';

$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));

pageSecure($this_page->source);

?>

<?=ViewUtil::loadView('doc-head')?>
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
                Software License
              </td>
              <td style="text-align:right;padding-right:10px;padding-top:5px;" id="searchbox" name="searchbox">
              <?php
              $account_id=$_SESSION['ao_accountid'];
              ?>
              <input type="text" id="s_search_id" name="s_search_id" onkeyup="search_softwarelicense_list(this.value)" />
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
            Request.make('<?=AJAX_DIR?>/get_softwarelicense_display_list.php', 'softwarelicensecontainer', true, true);
          </script>
        </td>
      </tr>
    </table>

    <script>
    function search_softwarelicense_list(n)
    {
//alert(n);
//return;
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
        
        p+="<style> .listtable th,td {     text-align:left; } </style>";
        p+="<table  class='listtable' width='100%'>";
        p+="<tr valign='top' >";
        p+="<th width='10%' >Software Type</th>";
        p+="<th width='10%'>Software Link</th>";
        p+="<th width='15%'>Login</th>";
        p+="<th width='15%'>Password</th>";
        p+="<th width='20%'>Company License is used for</th>";
        p+="<th width='10%'>Checked out by</th>";
        p+="<th width='20%'>How Long Has it been Checked out? </th>";
        p+="</tr>";
        p+="</table>";
       
for(var i=0;i<myObj.softwarelicense.length;i++)
{
var class1='odd';
  if(i%2==0)
    class1='even';

 
      p+="<table  class='listtable' width='100%'>";
      p+="<tr class='"+class1+"' valign='top'>";
      p+="<td width='10%' >"+myObj.softwarelicense[i].software_type+"</td>";
      p+="<td width='10%'><a target='_blank' href='microsoft-edge:"+myObj.softwarelicense[i].software_link+"'>"+myObj.softwarelicense[i].software_link+"</a></td>";
      p+="<td width='15%'>"+myObj.softwarelicense[i].login+"</td>";
      p+="<td width='15%'>"+myObj.softwarelicense[i].password+"</td>";
      p+="<td width='20%'>"+myObj.softwarelicense[i].company_license_used_for+"</td>";
      p+="<td width='10%'><a href='javascript: Request.make(\"includes/ajax/get_softwarelicense_display_list.php?software_license_checkout_id_clicked="+myObj.softwarelicense[i].software_license_checkout_id+"\",\"softwarelicensecontainer\",\"yes\",\"yes\");' class='basiclink'>"+myObj.softwarelicense[i].checked_out_by+"</a></td>";
      p+="<td width='20%'>"+myObj.softwarelicense[i].checked_out_time+" </td>";
      p+="</tr>";
      p+="</table>";
          






   
            
          


}


           
      }
      document.getElementById("softresult").innerHTML=p;

    }
};
xmlhttp.open("GET", "<?=AJAX_DIR?>/get_softwarelicense_searched_list.php?search_key="+n, true);
xmlhttp.send();

    }
    </script>
  </body>
</html>
