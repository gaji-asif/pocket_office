<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);

$id = RequestUtil::get('id');
$_POST['midroof'] = RequestUtil::get('midroof_timing') ? 1 : 0;
$location = RequestUtil::get('location');
$length = RequestUtil::get('permit_days');

$website_of_jurisdiction = RequestUtil::get('website_of_jurisdiction'); 
$roofing_handout = RequestUtil::get('roofing_handout'); 
$phone_number_of_jurisdiction = RequestUtil::get('phone_number_of_jurisdiction');
$email_of_jurisdiction = RequestUtil::get('email_of_jurisdiction');  
$code_enforced = RequestUtil::get('code_enforced');
$codevalue=RequestUtil::get('codevalue');
$link_to_code_book_irc=RequestUtil::get('link_to_code_book_irc'); 
$commercial_ibc=   RequestUtil::get('commercial_ibc');
$link_to_code_book_ibc=   RequestUtil::get('link_to_code_book_ibc');
$link_to_iecc =   RequestUtil::get('link_to_iecc');
$drip_edge_rakes =   RequestUtil::get('drip_edge_rakes');
$drip_edge_eaves =   RequestUtil::get('drip_edge_eaves');
$valley_liner =   RequestUtil::get('valley_liner');
$step_flashing =   RequestUtil::get('step_flashing');
$kickouts_required =   RequestUtil::get('kickouts_required');
$insulation_wall =   RequestUtil::get('insulation_wall');
$insulation_ceiling =   RequestUtil::get('insulation_ceiling');
$threetabs_ok =   RequestUtil::get('3tabs_ok');
$require_ir_shingle =   RequestUtil::get('require_ir_shingle');
$ice_and_water_shield_required =   RequestUtil::get('ice_and_water_shield_required');
$plank_deck_max_gap =   RequestUtil::get('plank_deck_max_gap');
$allow_shakes =   RequestUtil::get('allow_shakes');
$class_a_requirement =   RequestUtil::get('class_a_requirement');
$ventilation_enforced =   RequestUtil::get('ventilation_enforced');
$drip_edge_rakes_commercial =   RequestUtil::get('drip_edge_rakes_commercial');
$drip_edge_eaves_commercial =   RequestUtil::get('drip_edge_eaves_commercial');
$valley_liner_commercial =   RequestUtil::get('valley_liner_commercial');
$step_flashing_commercial =   RequestUtil::get('step_flashing_commercial');
$kickouts_required_commercial =   RequestUtil::get('kickouts_required_commercial');
$insulation_wall_commercial =   RequestUtil::get('insulation_wall_commercial');
$insulation_ceiling_commercial =   RequestUtil::get('insulation_ceiling_commercial');
$threetabs_ok_commercial =   RequestUtil::get('3tabs_ok_commercial');
$require_ir_shingle_commercial =   RequestUtil::get('require_ir_shingle_commercial');
$ice_and_water_shield_required_commercial =   RequestUtil::get('ice_and_water_shield_required_commercial');
$plank_deck_max_gap_commercial =   RequestUtil::get('plank_deck_max_gap_commercial');
$allow_shakes_commercial =   RequestUtil::get('allow_shakes_commercial');
$class_a_requirement_commercial =   RequestUtil::get('class_a_requirement_commercial');
$ventilation_enforced_commercial =   RequestUtil::get('ventilation_enforced_commercial');
$notes =   RequestUtil::get('notes');



$jurisdiction = DBUtil::getRecord('jurisdiction');

//print_r($jurisdiction);

if(empty($jurisdiction)) {

    UIUtil::showModalError('Jurisdiction not found!');

}
//echo $id;
$jurisdiction_additionals = DBUtil::getRecord('jurisdiction_additionals', $id, 'jurisdiction_id');
 //print_r($jurisdiction_additionals); die;
 


$errors = array();
if(RequestUtil::get("submit")) {
    if(empty($location) || empty($length)) {
       // $errors[] = 'Required fields missing';
    }
       if (!empty($length)) {    
    if (!ctype_digit($length)) {
        $errors[] = 'Length must be a number';
    }
      }
  if(!ctype_digit($phone_number_of_jurisdiction))  {
          $errors[] = 'Invalid phone number';
     }
 if (!empty($email_of_jurisdiction)) {    
        if (!filter_var($email_of_jurisdiction, FILTER_VALIDATE_EMAIL)) {
             $errors[] = 'Invalid email'.$email_of_jurisdiction;
        }
    }
    if(!count($errors)) {

        FormUtil::update('jurisdiction');
        $sql = "DELETE FROM jurisdiction_additionals WHERE jurisdiction_id=".$id;
         DBUtil::query($sql);
        $sql = "INSERT INTO jurisdiction_additionals
                VALUES (NULL, '$id', '{$website_of_jurisdiction}', '{$roofing_handout}', '{$phone_number_of_jurisdiction}', '{$email_of_jurisdiction}', '{$code_enforced}', '{$codevalue}', '{$link_to_code_book_irc}', '{$commercial_ibc}', 
'{$link_to_code_book_ibc}', '{$link_to_iecc}', '{$drip_edge_rakes}', '{$drip_edge_eaves}', '{$valley_liner}', '{$step_flashing}', '{$kickouts_required}', '{$insulation_wall}', '{$insulation_ceiling}', '{$threetabs_ok}', '{$require_ir_shingle}', '{$ice_and_water_shield_required}', '{$plank_deck_max_gap}', '{$allow_shakes}', '{$class_a_requirement}', '{$ventilation_enforced}', '{$drip_edge_rakes_commercial}', '{$drip_edge_eaves_commercial}', '{$valley_liner_commercial}', '{$step_flashing_commercial}', '{$kickouts_required_commercial}', '{$insulation_wall_commercial}', '{$insulation_ceiling_commercial}', '{$threetabs_ok_commercial}', '{$require_ir_shingle_commercial}', '{$ice_and_water_shield_required_commercial}', '{$plank_deck_max_gap_commercial}', '{$allow_shakes_commercial}', '{$class_a_requirement_commercial}', '{$ventilation_enforced_commercial}', '{$notes}')";
           DBUtil::query($sql);

?>



<script>

    parent.window.location.href = '/workflow/jurisdictions.php';

</script>

<?php

        die();

    }

}

list($jurisdiction_additionals_id,$jurisdiction_id,$website_of_jurisdiction ,$roofing_handout ,$phone_number_of_jurisdiction ,$email_of_jurisdiction, $code_enforced ,$codevalue ,$link_to_code_book_irc ,
$commercial_ibc ,$link_to_code_book_ibc ,$link_to_iecc, $drip_edge_rakes ,$drip_edge_eaves ,$valley_liner ,
$step_flashing ,$kickouts_required  ,$insulation_wall , $insulation_ceiling  ,$threetabs_ok  ,$require_ir_shingle  ,$ice_and_water_shield_required  ,$plank_deck_max_gap, $allow_shakes , $class_a_requirement , $ventilation_enforced ,$drip_edge_rakes_commercial ,$drip_edge_eaves_commercial ,$valley_liner_commercial ,$step_flashing_commercial ,$kickouts_required_commercial,$insulation_wall_commercial,$insulation_ceiling_commercial,$threetabs_ok_commercial,$require_ir_shingle_commercial,$ice_and_water_shield_required_commercial,$plank_deck_max_gap_commercial,$allow_shakes_commercial,$class_a_requirement_commercial,$ventilation_enforced_commercial,$notes) = array_values($jurisdiction_additionals);


?>

<form method="post" name="jurisdiction" action="?id=<?=$id?>">


<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>Edit Jurisdiction '<?=$jurisdiction['location']?>'</td>
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
        <td clas="infocontainernopadding">
            <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
                <tr>
                    <td width="25%" class="listitemnoborder"><b>Title:</td>
                    <td class="listrownoborder">
                        <input name="location" type="text" value="<?=$jurisdiction['location']?>">
                    </td>
                </tr>
                <tr>
                    <td class="listitem"><b>Mid Inspection:</b> </td>
                    <td class="listrow">
                        <input name="midroof_timing" type="text" value="<?=$jurisdiction['midroof_timing']?>">
                    </td>
                </tr>
                <tr>
                    <td class="listitem"><b>Min Ladder Req: </b></td>
                    <td class="listrow">
                        <select name="ladder">
                            <option value="0">None</option>
<?php
for($i = 1; $i <= 5; $i++) {
?>
                            <option value="<?=$i?>" <?=$i == $jurisdiction['ladder'] ? 'selected' : ''?>><?=$i?></option>
<?php
}
?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="listitem"><b>Length:</b></td>
                    <td class="listrow">
                        <input name="permit_days" type="text" value="<?=$jurisdiction['permit_days']?>"> days
                    </td>
                </tr>
                 
                        <input name="permit_url" type="hidden" value="">
                




 <tr>
        <td class="listitem"><b>Website of jurisdiction: </b></td>
        <td class="listrow">
            <input name="website_of_jurisdiction" type="text" value="<?= $website_of_jurisdiction;?>">
        </td>
    </tr>
     <tr>
        <td class="listitem"><b>Roofing Handout: </b></td>
        <td class="listrow">
            <input name="roofing_handout" type="text" value="<?=$roofing_handout?>">
        </td>
    </tr>
     <tr>
        <td class="listitem"><b>Phone number of jurisdiction: </b></td>
        <td class="listrow">
            <input name="phone_number_of_jurisdiction" type="text" value="<?=$phone_number_of_jurisdiction
?>">
        </td>
    </tr>
     <tr>
        <td class="listitem"><b>Email of jurisdiction: </b></td>
        <td class="listrow">
            <input name="email_of_jurisdiction" type="text" value="<?=$email_of_jurisdiction?>">
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Code enforced?    </b></td>
        <td class="listrow">
            <input type="radio" name="code_enforced" <?php if($code_enforced =='Y'){ ?> checked <?php } ?> id="codeenforcedyes" value="Y">
<label for="codeenforcedyes">Yes</label><br>
<input type="radio" name="code_enforced" id="codeenforcedno" <?php if($code_enforced =='N'){ ?> checked <?php } ?> value="N" >
<label for="codeenforcedyesno">No</label>
        </td>
    </tr>
 
      
    <tr>  
<td class="listitem"><b>Residential Choose Codes</b></td>
        <td class="listrow">
            <select name="codevalue">
                 <option value="">Select One</option>
                <option <?php if($codevalue =='IRC 2006'){ ?> selected <?php } ?> value="IRC 2006"  >IRC 2006</option>
                <option <?php if($codevalue =='2009'){ ?> selected <?php } ?> value="2009">2009</option>
                <option <?php if($codevalue =='2012'){ ?> selected <?php } ?> value="2012">2012</option>
                <option <?php if($codevalue =='2015'){ ?> selected <?php } ?> value="2015">2015</option>
                <option <?php if($codevalue =='FBC 2017'){ ?> selected <?php } ?> value="FBC 2017">FBC 2017</option>
                <option <?php if($codevalue =='2018'){ ?> selected <?php } ?> value="2018">2018 </option>
                
            </select>
            
            </td>
    </tr>
  
     <tr>  
<td class="listitem"><b>Link to code book IRC</b></td>
        <td class="listrow">
            <input name="link_to_code_book_irc" value="<?=$link_to_code_book_irc?>" type="text">
            </td>
    </tr>

       <tr>  
<td class="listitem"><b>Commercial IBC</b></td>
        <td class="listrow">
            <select name="commercial_ibc">
                 <option value="">Select One</option>
                <option <?php if($commercial_ibc =='2006'){ ?> selected <?php } ?> value="2006">2006</option>
                <option <?php if($commercial_ibc =='2009'){ ?> selected <?php } ?> value="2009">2009</option>
                <option <?php if($commercial_ibc =='2012'){ ?> selected <?php } ?> value="2012">2012</option>
                <option <?php if($commercial_ibc =='2015'){ ?> selected <?php } ?> value="2015">2015</option>
                <option <?php if($commercial_ibc =='FBC 2017'){ ?> selected <?php } ?> value="FBC 2017">FBC 2017</option>
                <option <?php if($commercial_ibc =='2018'){ ?> selected <?php } ?> value="2018">2018</option>
            </select>
            </td>
    </tr>
 
     <tr>  
<td class="listitem"><b>Link to code book IBC</b></td>
        <td class="listrow">
            <input name="link_to_code_book_ibc" value="<?=$link_to_code_book_ibc?>" type="text">
            </td>
    </tr>

     <tr>  
<td class="listitem"><b>Link to IECC</b></td>
        <td class="listrow">
            <select name="link_to_iecc">
                 <option value="">Select One</option>
                <option <?php if($link_to_iecc =='IECC 2006'){ ?> selected <?php } ?> value="IECC 2006" >IECC 2006</option>
                <option<?php if($link_to_iecc =='2009'){ ?> selected <?php } ?> value="2009">2009</option>
                <option<?php if($link_to_iecc =='2012'){ ?> selected <?php } ?> value="2012">2012</option>
                <option<?php if($link_to_iecc =='2015'){ ?> selected <?php } ?> value="2015">2015</option>
                <option<?php if($link_to_iecc =='2018'){ ?> selected <?php } ?> value="2018">2018</option>
            </select>
            </td>
    </tr>
  <tr>      
<td class="listrow custom-bally"><h4>Residential</h4>

 <div>
           
<div class="my-person">
    <div class="mypersonone">Drip Edge Rakes</div> 
     <div class="mypersontwo">
<input type="radio" name="drip_edge_rakes" <?php if($drip_edge_rakes =='Y'){ ?> checked <?php } ?> value="Y">
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="drip_edge_rakes" <?php if($drip_edge_rakes =='N'){ ?> checked <?php } ?> value="N" >
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person">
    <div class="mypersonone">Drip Edge Eaves</div>
         <div class="mypersontwo"> <input type="radio" <?php if($drip_edge_eaves  =='Y'){ ?> checked <?php } ?> name="drip_edge_eaves" value="Y">
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="drip_edge_eaves" <?php if($drip_edge_eaves  =='N'){ ?> checked <?php } ?>  value="N" >
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person">
    <div class="mypersonone"> Valley Liner</span></div> 
    <div class="mypersontwo"><input type="radio" <?php if($valley_liner  =='Y'){ ?> checked <?php } ?> name="valley_liner" value="Y">
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="valley_liner" <?php if($valley_liner  =='N'){ ?> checked <?php } ?> value="N" >
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person">
    <div class="mypersonone"> Step Flashing</div> 
<div class="mypersontwo"><input type="radio" <?php if($step_flashing  =='Y'){ ?> checked <?php } ?> name="step_flashing" value="Y">
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="step_flashing" <?php if($step_flashing  =='N'){ ?> checked <?php } ?>  value="N" >
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person">
    <div class="mypersonone"> Kickouts Required?</div>
   <div class="mypersontwo"> <input type="radio" <?php if($kickouts_required  =='Y'){ ?> checked <?php } ?> name="kickouts_required" value="Y">
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="kickouts_required" <?php if($kickouts_required  =='N'){ ?> checked <?php } ?>  value="N" >
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person">
    <div class="mypersonone"> Insulation Wall</div> <div class="mypersontwo"><input type="text"  name="insulation_wall" value="<?=$insulation_wall?>"></div></div><br><br>

<div class="my-person">
    <div class="mypersonone">Insulation Ceiling</div> <div class="mypersontwo"><input type="text" name="insulation_ceiling" value="<?=$insulation_ceiling?>"></div></div><br><br>

<div class="my-person">
    <div class="mypersonone"> 3-Tabs ok?</div> 
    <div class="mypersontwo"><input type="radio"  <?php if($threetabs_ok  =='Y'){ ?> checked <?php } ?> name="3tabs_ok" value="Y">
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="3tabs_ok"  value="N" <?php if($threetabs_ok  =='N'){ ?> checked <?php } ?>>
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person">
    <div class="mypersonone"> Require IR shingle?</div>
   <div class="mypersontwo"> <input type="radio"  <?php if($require_ir_shingle  =='Y'){ ?> checked <?php } ?> name="require_ir_shingle" value="Y">
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="require_ir_shingle" <?php if($require_ir_shingle  =='N'){ ?> checked <?php } ?> value="N" >
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person">
    <div class="mypersonone"> Ice and Water Shield required?</div> <div class="mypersontwo"><input type="text" name="ice_and_water_shield_required" value="<?=$ice_and_water_shield_required?>"></div></div><br><br>

<div class="my-person">
    <div class="mypersonone"> Plank Deck Max Gap</div> <div class="mypersontwo"><input type="text" name="plank_deck_max_gap" value="<?=$plank_deck_max_gap?>"></div></div><br><br>

<div class="my-person">
    <div class="mypersonone"> Allow Shakes?</div> <div class="mypersontwo"><input type="radio" name="allow_shakes" <?php if($allow_shakes  =='Y'){ ?> checked <?php } ?> value="Y">
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="allow_shakes"  value="N" <?php if($allow_shakes  =='N'){ ?> checked <?php } ?>>
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person">
    <div class="mypersonone"> Class A requirement?</div> <div class="mypersontwo"><input type="text" name="class_a_requirement" value="<?=$class_a_requirement?>"></div></div><br><br>

<div class="my-person">
    <div class="mypersonone"> Ventilation Enforced?</div> <div class="mypersontwo"><input type="radio" name="ventilation_enforced" <?php if($ventilation_enforced =='Y'){ ?> checked <?php } ?> value="Y">
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="ventilation_enforced"  value="N" <?php if($ventilation_enforced  =='N'){ ?> checked <?php } ?>>
<label for="codeenforcedyesno">No</label></div></div><br><br>
       </div>


</td>


<td class="listrow custom-bally"><h4>Commercial</h4>


 <div>
    <div class="my-person">
        <div class="mypersonone">Drip Edge Rakes</div> 
<div class="mypersontwo">
<input type="radio" name="drip_edge_rakes_commercial" value="Y" <?php if($drip_edge_rakes_commercial =='Y'){ ?> checked <?php } ?>>
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="drip_edge_rakes_commercial" value="N" <?php if($drip_edge_rakes_commercial =='N'){ ?> checked <?php } ?>>
<label for="codeenforcedyesno">No</label>
</div>
</div><br><br>

<div class="my-person">
    <div class="mypersonone">Drip Edge Eaves</div>
    <div class="mypersontwo"><input type="radio" name="drip_edge_eaves_commercial" value="Y" <?php if($drip_edge_eaves_commercial =='Y'){ ?> checked <?php } ?>>
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="drip_edge_eaves_commercial"  value="N"  <?php if($drip_edge_eaves_commercial =='N'){ ?> checked <?php } ?>>
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person"> 
<div class="mypersonone">Valley Liner</div>
<div class="mypersontwo"><input type="radio" name="valley_liner_commercial" value="Y" <?php if($valley_liner_commercial =='Y'){ ?> checked <?php } ?>>
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="valley_liner_commercial"  value="N" <?php if($valley_liner_commercial =='N'){ ?> checked <?php } ?>>
<label for="codeenforcedyesno">No</label></div></div><br><br>
<div class="my-person"> 
<div class="mypersonone"> Step Flashing</div>
<div class="mypersontwo"> <input type="radio" name="step_flashing_commercial" value="Y" <?php if($step_flashing_commercial =='Y'){ ?> checked <?php } ?>>
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="step_flashing_commercial"  value="N" <?php if($step_flashing_commercial =='N'){ ?> checked <?php } ?>>
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person"> 
<div class="mypersonone">Kickouts Required?</div> 
<div class="mypersontwo"><input type="radio" name="kickouts_required_commercial" value="Y" <?php if($kickouts_required_commercial =='Y'){ ?> checked <?php } ?>>
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="kickouts_required_commercial"  value="N" <?php if($kickouts_required_commercial =='N'){ ?> checked <?php } ?>>
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person"> <div class="mypersonone">Insulation Wall</div> <div class="mypersontwo"><input type="text" name="insulation_wall_commercial" value="<?=$insulation_wall_commercial?>"></div></div><br><br>

<div class="my-person"><div class="mypersonone"> Insulation Ceiling</div> <div class="mypersontwo"><input type="text" name="insulation_ceiling_commercial" value="<?=$insulation_wall_commercial?>"></div></div><br><br>

<div class="my-person"> 
<div class="mypersonone">3-Tabs ok?</div> 
<div class="mypersontwo"><input type="radio" name="3tabs_ok_commercial" value="Y" <?php if($threetabs_ok_commercial =='Y'){ ?> checked <?php } ?>>
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="3tabs_ok_commercial"  value="N" <?php if($threetabs_ok_commercial =='N'){ ?> checked <?php } ?>>
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person"><div class="mypersonone"> Require IR shingle?</div>
<div class="mypersontwo"><input type="radio" name="require_ir_shingle_commercial" value="Y" <?php if($require_ir_shingle_commercial =='Y'){ ?> checked <?php } ?>>
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="require_ir_shingle_commercial"  value="N" <?php if($require_ir_shingle_commercial =='N'){ ?> checked <?php } ?>>
<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person"> <div class="mypersonone">Ice and Water Shield required?</div> <div class="mypersontwo"><input type="text" name="ice_and_water_shield_required_commercial" value="<?=$ice_and_water_shield_required_commercial?>">
</div></div><br><br>

<div class="my-person"> <div class="mypersonone"> Plank Deck Max Gap</div> <div class="mypersontwo"><input type="text" name="plank_deck_max_gap_commercial" value="<?=$plank_deck_max_gap_commercial?>"></div></div><br><br>

<div class="my-person"> <div class="mypersonone"> Allow Shakes?</div>
<div class="mypersontwo"><input type="radio" name="allow_shakes_commercial" value="Y" <?php if($allow_shakes_commercial =='Y'){ ?> checked <?php } ?>>
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="allow_shakes_commercial"  value="N" <?php if($allow_shakes_commercial =='N'){ ?> checked <?php } ?>>

<label for="codeenforcedyesno">No</label></div></div><br><br>

<div class="my-person"> <div class="mypersonone"> Class A requirement?</div> <div class="mypersontwo"><input type="text" name="class_a_requirement_commercial" value="<?=$class_a_requirement_commercial?>"></div></div><br><br>

<div class="my-person"> <div class="mypersonone"> Ventilation Enforced?</div> <div class="mypersontwo">
    <input type="radio" name="ventilation_enforced_commercial" value="Y" <?php if($ventilation_enforced_commercial =='Y'){ ?> checked <?php } ?>>
<label for="codeenforcedyes">Yes</label>
<input type="radio" name="ventilation_enforced_commercial"  value="N" <?php if($ventilation_enforced_commercial =='N'){ ?> checked <?php } ?>>
<label for="codeenforcedyesno">No</label></div></div><br><br>

       </div> 

</td>


   
<tr>
<td class="listitem"><b>Notes about this jurisdiction </b></td>
    <td class="listrow">
       <div>
        <textarea name="notes"><?=$notes?></textarea>
       </div> 
    </td>
</tr>                
      
      
      
      






                <tr>

                    <td colspan="2" align="right" class="listrow">

                          <input name="submit" type="submit" value="Save">

                    </td>

                </tr>

            </table>

        </td>

    </tr>

</table>

</form>

</body>

</html>