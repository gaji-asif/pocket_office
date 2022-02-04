<?php
include '../../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);
$ss="";
$name = RequestUtil::get('name');
 $ventcalculator_id = RequestUtil::get('type');
$description = RequestUtil::get('description');
$id = RequestUtil::get('id');

$errors = array();
if(RequestUtil::get("submit")) 
{
    if(empty($name)) 
    {
        $errors[] = 'Vent calculator Job name cannot be blank';
    }
    
    if(!count($errors)) 
    {
      /* attachment uploads*/
      
    
            $sql = "INSERT INTO tbl_ventcalculator_job (account_id,name,ventcalculator_id,description,attachment,attachment_desc, created_by,created_at,is_deleted)
              VALUES ('{$_SESSION['ao_accountid']}','$name', '$ventcalculator_id','$description','', 
        '','{$_SESSION['ao_userid']}',NOW(),'0')";  
      
      DBUtil::query($sql);
      ?>

      <script>
        $(document).ready(function()
        {
            try{
               var opener = window.parent;
               opener.location.href="<?=ROOT_DIR?>/vent-calculator-job.php?id=<?=$id?>";
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




<script type="text/javascript">
tinymce.init({
    branding: false,
    selector: "textarea",

plugins: [
    "advlist autolink lists link image charmap print preview anchor",
    "searchreplace visualblocks code fullscreen",
    "insertdatetime media table contextmenu paste "
],
toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter      alignright alignjustify | bullist numlist outdent indent | link image"
});
</script>
<script type="text/javascript">
function add_attachment_field()
{
	var s="";
	s+="<tr><td class='listrownoborder'><input width='25%'' type='file' name='myFile[]'>";
    s+="</td></tr></table></td></tr>";
	$("#attachment-container").append(s);
}
</script>
<!--<?=$ss?>-->
<form method="post" name="knowledgebase" action="?id=<?=$id?>" enctype='multipart/form-data'>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign="center">
                    <td>Add vent calculator Job</td>
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
                        <b>Name:</b>&nbsp;<span class="red">*</span>
                    </td>
                    <td class="listrownoborder">
                        <input type="text" name="name" id="name">
                    </td>
                </tr>

                <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Sq. Ft. :</b>&nbsp;<span class="red">*</span>
                    </td>
                    <td class="listrownoborder">
                         <input class="form-control" type="text" name="a" id="a">
                    </td>
                </tr>
                  <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Pitch :</b>
                    </td>
                    <td class="listrownoborder">
                        <select class="form-control" name="b" id="b">
							<option>Select</option>
							<option value="1.031">3</option>
							<option value="1.054">4</option>
							<option value="1.083">5</option>
							<option value="1.118">6</option>
							<option value="1.158">7</option>
							<option value="1.202">8</option>
							<option value="1.250">9</option>
							<option value="1.302">10</option>
							<option value="1.356">11</option>
							<option value="1.414">12</option>
							<option value="1.474">13</option>
							<option value="1.537">14</option>
							<option value="1.601">15</option>
							<option value="1.734">17</option>
							<option value="1.803">18</option>
							<option value="1.873">19</option>
							<option value="1.944">20</option>
							<option value="2.016">21</option>
							<option value="2.088">22</option>
							<option value="2.162">23</option>
							<option value="2.236">24</option>
						</select> 
                         
                    </td>
                </tr>

                <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Net free Ventilation area :</b>
                    </td>
                    <td class="listrownoborder">
                        <select class="form-control" name="c" id="c">
				<option>Select</option>
				<option value="150">150</option>
				<option value="300">300</option>
	   </select>
                    </td>
                </tr>
                 <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Net Free Area of Vent:</b>
                    </td>
                     <td class="listrownoborder">
                       &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Assign vent job:</b>
                    </td>
                    <td class="listrownoborder">
                        <?php
                        $sql = "SELECT ventcalculator_id, ventcalculator_name from tbl_ventcalculator
                              where account_id='{$_SESSION['ao_accountid']}' order by order_num asc";
                        $ventcalculator = DBUtil::queryToArray($sql);                        
                        ?>
                        <select class="form-control" name="type"  onchange=""  id="vt">
                          <option value="">Assign vent job</option>
                        <?php
                        foreach($ventcalculator as $row)
                        {
                        ?>
                          <option value="<?=$row['ventcalculator_id']?>"><?=$row['ventcalculator_name']?></option>
                        <?php
                        }
                        ?>
                      </select>
                    </td>
                </tr>
<tr class="sv" style="display:none">
                    <td width="25%"  class="listitemnoborder">
                        <b>Static Vent:</b>
                    </td>
 <td class="listrownoborder">
 <input class="form-control" type="text" value="50" name="sv" id="sv">
   </td>
                     </tr>
       
             

                <tr class="rv" style="display:none">
                    <td width="25%"  class="listitemnoborder">
                        <b>Ridge Vent:</b>
                    </td>
 <td class="listrownoborder">

<input class="form-control" type="text" name="rv" id="rv" value="12.5">                     </td>
     
                </tr>
      <tr  >
                    <td width="25%"  class="listitemnoborder">
                        <b>Calculate : </b>
                    </td>
                    <td class="listrownoborder">
     <button type="button" style=""  class="btn btn-primary svbtn" onclick="getTurtleVent(a.value,b.value,c.value,sv.value);">Turtle Vent</button>
     <button type="button" style=""   class="btn btn-primary rvbtn" onclick="getRidgeVent(a.value,b.value,c.value,rv.value);">Ridge Vent</button>
      </td>
  </tr> 

    
                <tr valign="top">
                    <td class="listitem">
                        <b>Description:</b>
                    </td>
                    <td class="listrow">
                        <textarea name="description" id="description" style="width:100%;" rows="4"></textarea>
                    </td>
                </tr> 

              <!--  <tr>
                  <td width="25%" class="listitemnoborder">
                          <b>Attachment</b>&nbsp;<span class="red">*</span>
                  </td>
                  <td >
                  <table id="attachment-container" name="attachment-container" width="50%">
                  <tr>
                   
                    <td class="listrownoborder">
                        <input width="25%" type="file" name="myFile[]">                        
                    </td>
                    <td>&nbsp;&nbsp;
                    <input type="text" name="myFileDescription[]">
                    </td>
                  </tr>
                  </table>
                  </td>

                </tr>
                <tr>
                    <td width="25%" class="listitemnoborder">             
                      <input type="button"  onclick="add_attachment_field()" value="Add Attachment" ></input>
                    </td>
                   <td class="listrownoborder">
                   </td>
                </tr>-->

                <tr>
                    <td align="right" colspan="2" class="listrow">
                        <input name="submit" type="submit" value="Submit">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>
<script>
function getTurtleVent(aval,bval,cval,sv){
			var tval = (aval/bval);
			var tval3 = (tval/cval);
			var tval4 = ((tval/cval)*144);
			var tval5 = (((tval/cval)*144)/sv);
			var tval2 = ((((tval/cval)*144)/sv)/2);
			var value = jQuery( "#b option:selected" ).text();

			//jQuery("#description").val("TURTLE FIGURES ARE AS FOLLOWS - <br>"+aval+" sq. ft. of tear off/"+bval+" pitch factor for "+value+" pitch="+tval.toFixed(3)+"/"+cval+" amount of total ventilation per sq.="+tval3.toFixed(3)+"*144 convert to sq. in.="+tval4.toFixed(3)+"/"+sv+" amount of sq. inches per vent="+tval5.toFixed(2)+"/2="+tval2.toFixed(3)+" exhaust vent only - ROUND TO "+Math.round(tval2)+".");
             var tv="<p>TURTLE FIGURES ARE AS FOLLOWS - <br>"+aval+" sq. ft. of tear off/"+bval+" pitch factor for "+value+" pitch="+tval.toFixed(3)+"/"+cval+" amount of total ventilation per</p> <p>sq.="+tval3.toFixed(3)+"*144 convert to sq. in.="+tval4.toFixed(3)+"/"+sv+" amount of sq. inches per vent="+tval5.toFixed(2)+"/2="+tval2.toFixed(3)+" exhaust vent only</p> <p>- ROUND TO "+Math.round(tval2)+".</p>";
			tinyMCE.get('description').setContent(tv);
			//jQuery("#tval").append("<br>"+tval+"<br>"+tval3+"<br>"+tval4+"<br>"+tval5+"<br>"+tval2);
			//715.5635062611806 tval
            //2.3852116875372684 tval3
           // 343.47048300536665  tval4
           /// 6.869409660107333  tval5
            //3.4347048300536667  tval2
			//alert(tval2);
			}

	function getRidgeVent(aval,bval,cval,rv){
			var tval = (aval/bval);
			var tval3 = (tval/cval);
			var tval4 = ((tval/cval)*144);
			var tval5 = (((tval/cval)*144)/rv);
			var tval2 = ((((tval/cval)*144)/rv)/2);
			var value = jQuery( "#b option:selected" ).text();
			var rv="<p>RIDGE FIGURES ARE AS FOLLOWS - <br>"+aval+" sq. ft. of tear off/"+bval+" pitch factor for "+value+" pitch="+tval.toFixed(3)+"/"+cval+" amount of total ventilation per</p> <p>sq.="+tval3.toFixed(3)+"*144 convert  to sq. in.="+tval4.toFixed(3)+"/"+rv+" amount of sq. inches per vent="+tval5.toFixed(2)+"/2="+tval2.toFixed(3)+" exhaust vent only</p> <p>-ROUND TO "+Math.round(tval2)+"LF.</p>";
			tinyMCE.get('description').setContent(rv);
			//alert(tval2);
			//jQuery("#description").val("RIDGE FIGURES ARE AS FOLLOWS - <br>"+aval+" sq. ft. of tear off/"+bval+" pitch factor for "+value+" pitch="+tval.toFixed(3)+"/"+cval+" amount of total ventilation per sq.="+tval3.toFixed(3)+"*144 convert to sq. in.="+tval4.toFixed(3)+"/"+rv+" amount of sq. inches per vent="+tval5.toFixed(2)+"/2="+tval2.toFixed(3)+" exhaust vent only - ROUND TO "+Math.round(tval2)+"LF.");
}



</script>

</body>
</html>
