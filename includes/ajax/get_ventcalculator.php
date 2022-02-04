<?php

include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
if(!ModuleUtil::checkAccess('edit_users'))
    die("Insufficient Rights");

$ac_id = $_SESSION['ao_accountid'];
//get data
$userId = RequestUtil::get('userid');
$job_id = RequestUtil::get('id');
$item_id = RequestUtil::get('item_id');
$type = RequestUtil::get('type');
$tab = RequestUtil::get('tab');
$status = RequestUtil::get('status');
  $sqljob = "SELECT salesman from  jobs where job_id=".$job_id;
    
        $salesman = DBUtil::queryToArray($sqljob);
        $salesman=$salesman[0]['salesman'];
          $sqllevel = "SELECT level from users where user_id='".$salesman."'";
        $sqllevel = DBUtil::queryToArray($sqllevel);
        $salesmanlevel=!empty($sqllevel)?$sqllevel[0]['level']:'';
 
if(empty($tab))
    $tab = 1;

if(!empty($item_id) && !empty($type)) 
{   
    if($type=='ca' || $type=='na' || $type=='ra')
    {
           $sql = "SELECT t1.tbl_ventcalculator_job_id from tbl_ventcalculator_job as t1 
                left join tbl_ventcalculator as t2 on t2.ventcalculator_id=t1.ventcalculator_id  
                join user_ventcalculator_job_access as t3 on t3.tbl_ventcalculator_job_id=t1.tbl_ventcalculator_job_id 
                where t1.account_id=".$ac_id." and t1.ventcalculator_id=".$item_id." and t1.is_deleted='0' AND t3.user_id='".$userId."'
                order by t1.order_num asc";
        $ventcalculator_job = DBUtil::queryToArray($sql);
       
        foreach($ventcalculator_job as $c_job)
        {
            $c_job_id = $c_job['tbl_ventcalculator_job_id'];
             $sql="SELECT id,completed, na, reviewer_approval from ventcalculator_job_status where user_id='$userId' AND job_id='$job_id' AND ventcalculator_job_id='$c_job_id'";
            $is_exist = DBUtil::queryToArray($sql);
            if(!empty($is_exist)) 
            {
                $comp = ($type!='ca')?$is_exist[0]['completed']:(empty($status)?1:0);
                $na = ($type!='na')?$is_exist[0]['na']:(empty($status)?1:0);
                $rev = ($type!='ra')?$is_exist[0]['reviewer_approval']:(empty($status)?1:0);
                
                $sql = "UPDATE ventcalculator_job_status
                        SET completed='$comp', na='$na', reviewer_approval='$rev' 
                        WHERE user_id='$userId' AND job_id='$job_id' AND ventcalculator_job_id='$c_job_id'";

            }
            else 
            {        
                $comp = ($type=='ca' && empty($status))?1:0;
                $na = ($type=='na' && empty($status))?1:0;
                $rev = ($type=='ra' && empty($status))?1:0;
                $sql = "INSERT INTO ventcalculator_job_status (user_id, job_id, ventcalculator_job_id, completed, na, reviewer_approval)
                            VALUES ('$userId', '$job_id', '$c_job_id','$comp', '$na', '$rev')";
                
            }
            DBUtil::query($sql);
        }
    }
    else
    {
        $sql="SELECT id,completed, na, reviewer_approval from ventcalculator_job_status where user_id='$userId' AND job_id='$job_id' AND ventcalculatorjob_id='$item_id'";
        $is_exist = DBUtil::queryToArray($sql);
        if(!empty($is_exist)) 
        {
            $comp = ($type!='c')?$is_exist[0]['completed']:(($is_exist[0]['completed']=='1')?0:1);
            $na = ($type!='n')?$is_exist[0]['na']:(($is_exist[0]['na']=='1')?0:1);
            $rev = ($type!='r')?$is_exist[0]['reviewer_approval']:(($is_exist[0]['reviewer_approval']=='1')?0:1);
            
            $sql = "UPDATE ventcalculator_job_status
                    SET completed='$comp', na='$na', reviewer_approval='$rev' 
                    WHERE user_id='$userId' AND job_id='$job_id' AND ventcalculator_job_id='$item_id'";

        }
        else 
        {        
            $comp = ($type=='c')?1:0;
            $na = ($type=='n')?1:0;
            $rev = ($type=='r')?1:0;
            $sql = "INSERT INTO ventcalculator_job_status (user_id, job_id, ventcalculator_job_id, completed, na, reviewer_approval)
                        VALUES ('$userId', '$job_id', '$item_id','$comp', '$na', '$rev')";
            
        }
        DBUtil::query($sql);
    }
}

 $sql = "SELECT ventcalculator_id,ventcalculator_name from tbl_ventcalculator where account_id=".$ac_id." order by order_num asc";

$ventcalculator = DBUtil::queryToArray($sql);

?>
<script src="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="../../css/style.css">
</style>
   <div class="text-center">
  <h3>
Vent Calculator</h3>
 
</div>
  
<div class="container">
    <div class="popclose"><i class="icon-remove grey btn-close-modal"></i></div>
  <div class="row">
    <div class="col-sm-12">
      <form onsubmit="return false;" class="custom-for-tab" action="#">
  <div class="form-group">
    <label for="email">Sq. Ft. :</label>
	<input class="form-control" type="text" name="a" id="a">
  </div>
  <div class="form-group">
    <label for="pwd">Pitch :</label>
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
  </div>
  <div class="form-group">
     <label for="pwd">Net free Ventilation area :</label>
	   <select class="form-control" name="c" id="c">
				<option>Select</option>
				<option value="150">150</option>
				<option value="300">300</option>
	   </select>
  </div>
   <fieldset>
  <legend>Net Free Area of Vent:</legend>
   <div class="form-group">
     <label for="pwd">Choose Vent type :</label>
	   <select class="form-control" onchange="managedisplay(this.value)" name="vt" id="vt">
				<option value="">Select</option>
				<option value=".sv">Turtle Vent</option>
				<option value=".rv">Ridge Vent</option>
	   </select>
  </div>
 
   <div class="form-group sv" style="display:none">
     <label for="pwd"> Static Vent:</label>
	   <input class="form-control" type="text" value="50" name="sv" id="sv">
  </div>
   <div class="form-group rv" style="display:none">
     <label for="pwd"> Ridge Vent:</label>
	   <input class="form-control" type="text" name="rv" id="rv" value="12.5">
  </div>
  </fieldset>
   <div class="form-group form-check">
     <button type="submit" style="display:none" class="btn btn-primary svbtn" onclick="getTurtleVent(a.value,b.value,c.value,sv.value);">turtleVent</button>
	  <button type="submit" style="display:none" class="btn btn-primary rvbtn" onclick="getRidgeVent(a.value,b.value,c.value,rv.value);">Ridge Vent</button>
  </div>
  <div id="tval">
</div>
<br>
<a  target="_blank" style="display:none;" class="btn btn-primary dowpdf btn btn-blue btn-block">Click Here To Download</a>
 <input type="button"  style="display:none" onclick="createpdf()" class="pdfgen" value="Generate Report">
   
</form>

    </div>
  </div>
</div>
<script>
    function getTurtleVent(aval,bval,cval,sv){
        	jQuery(".pdfgen").hide();
			var tval = (aval/bval);
			var tval3 = (tval/cval);
			var tval4 = ((tval/cval)*144);
			var tval5 = (((tval/cval)*144)/sv);
			var tval2 = ((((tval/cval)*144)/sv)/2);
			var value = jQuery( "#b option:selected" ).text();
			jQuery("#tval").html("TURTLE FIGURES ARE AS FOLLOWS - <br>"+aval+" sq. ft. of tear off/"+bval+" pitch factor for "+value+" pitch="+tval.toFixed(3)+"/"+cval+" amount of total ventilation <br>per sq.="+tval3.toFixed(3)+"*144 convert to sq. in.="+tval4.toFixed(3)+"/"+sv+" amount of sq. inches per vent="+tval5.toFixed(2)+"/2="+tval2.toFixed(3)+" <br>exhaust vent only - ROUND TO "+Math.round(tval2)+".");
			//jQuery("#tval").append("<br>"+tval+"<br>"+tval3+"<br>"+tval4+"<br>"+tval5+"<br>"+tval2);
			//715.5635062611806 tval
            //2.3852116875372684 tval3
           // 343.47048300536665  tval4
           /// 6.869409660107333  tval5
            //3.4347048300536667  tval2
			//alert(tval2);
			jQuery(".pdfgen").show();
			 jQuery("#tval").show();
			}

	function getRidgeVent(aval,bval,cval,rv){
	    	jQuery(".pdfgen").hide();
			var tval = (aval/bval);
			var tval3 = (tval/cval);
			var tval4 = ((tval/cval)*144);
			var tval5 = (((tval/cval)*144)/rv);
			var tval2 = ((((tval/cval)*144)/rv)/2);
			var value = jQuery( "#b option:selected" ).text();
			//alert(tval2);
			
			jQuery("#tval").html("RIDGE FIGURES ARE AS FOLLOWS - <br>"+aval+" sq. ft. of tear off/"+bval+" pitch factor for "+value+" pitch="+tval.toFixed(3)+"/"+cval+" amount of total ventilation <br>per sq.="+tval3.toFixed(3)+"*144 convert to sq. in.="+tval4.toFixed(3)+"/"+rv+" amount of sq. inches per vent="+tval5.toFixed(2)+"/2="+tval2.toFixed(3)+" <br>exhaust vent only - ROUND TO "+Math.round(tval2)+"LF.");
           	jQuery(".pdfgen").show();
           	 jQuery("#tval").show();
	    
	}


function managedisplay(elm)
{
	//alert(elm);
	if(elm=='.sv')
	{
	jQuery("#tval").hide();
	jQuery(".pdfgen").hide();
	jQuery(".rv").hide();
	jQuery(".sv").show();
	jQuery(".svbtn").show();
	jQuery(".rvbtn").hide();
	 $(".dowpdf").hide();
	
	}
	else
	{
	jQuery("#tval").hide();
	jQuery(".pdfgen").hide();
	jQuery(".rv").show();
	jQuery(".sv").hide();
	jQuery(".svbtn").hide();
	jQuery(".rvbtn").show();
	 $(".dowpdf").hide();
	}
}

function createpdf()
    { 
        $(".dowpdf").hide();
        var string=JSON.stringify($("#tval").html());
               $.ajax({
                    type: "POST",
                    url: "<?=AJAX_DIR?>/ventcalculator_job/ventcalculator_pdf.php",
                    data: {data : string}, 
                    cache: false,
            
                    success: function(r){
                        if(r!='error'){
                            $(".dowpdf").attr("href",r);
                            $(".dowpdf").show();
                            $(".pdfgen").hide();
                        }
                        else
                        {
                            alert('Error!!!');
                        }
                    }
                });
        
    }
</script>