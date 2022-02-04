<?php

if(empty($myJob) || get_class($myJob) !== 'Job') { return; }



$stage_age = $myJob->getStageAge();

$diff = $stage_age - $myJob->duration;

//print_r($myJob)

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script src="http://maps.google.com/maps/api/js?key=AIzaSyA1gf0GrUztgVDDVapXnJlcyYMCilJLubQ" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".fancybox").fancybox();
	});
</script>

<tr class="job-tab-content details" <?=@$show_content_style?>>

    <td colspan=11>

        <table width='100%' border=0 cellpadding=0 cellspacing=0 class="job-tab-content-header">

            <tr valign='top'>

                <td width="22%">Stage Details:</td>

                <td width="22%">Insured Info:</td>

                <td width="22%">Job Details:</td>

                <td width="22%">

                    Job Items:

<?php

if(ModuleUtil::checkJobModuleAccess('modify_job', $myJob)) {

	$jobActions = JobUtil::getActions(TRUE, TRUE);

?>

                    <i class="icon-plus inline-job-action-menu">

                        <ul>

<?php

	foreach($jobActions as $jobAction) {

		if(ModuleUtil::checkJobModuleAccess(MapUtil::get($jobAction, 'hook'), $myJob)) {

?>

                            <li onclick="applyOverlay('<?=MapUtil::get($jobAction, 'script')?>?id=<?=$myJob->job_id?>')">

                                <?=MapUtil::get($jobAction, 'action')?>

                            </li>

<?php

        }

    }

?>

                        </ul>

                    </i>

<?php

}

?>

                </td>

            </tr>

        </table>

    </td>

</tr>

<tr class="job-tab-content details" <?=@$show_content_style?>>

    <td colspan=11>

        <table width='100%' border=0 cellpadding=0 cellspacing=0>

            <tr valign='top'>

                <td width="22%" class="job-detail-column">

                    <table border=0 width='100%' border=0 cellpadding=0 cellspacing=0>

                        <tr valign='top'>

                            <td>

                                <table border=0 width='100%' cellpadding=0 cellspacing=0>

                                    <tr>

                                        <td width=100 class="listitemnoborder" valign='top'><b>Current Stage:</b></td>

                                        <td class="listrownoborder"><?=$myJob->getCSVStages()?></td>

                                    </tr>

                                    <tr>

                                        <td class="listitem"><b>Days @ Stage:</b></td>

                                        <td class="listrow"><?=$stage_age?></td>

                                    </tr>

                                    <tr>

                                        <td class="listitem"><b>Suggested Duration:</b></td>

                                        <td class="listrow">

                                            <?=$myJob->duration == 9999 ? 'No Limit' : $myJob->duration?>

                                        </td>

                                    </tr>

                                    <tr valign='top'>

                                        <td class="listitem"><b>Next Stage:</b></td>

                                        <td class="listrow"><?=$myJob->getNextStages()?></td>

                                    </tr>

                                    <?=$myJob->getNextStageReqs()?>

                                </table>

                            </td>

                        </tr>

                    </table>

                </td>

                <td width="22%" class="job-detail-column">

                    <table border=0 width='100%' border=0 cellpadding=0 cellspacing=0>

                        <tr valign='top'>

                            <td>

                                <table border=0 width='100%' cellspacing=0 cellpadding=0>

                                    <tr>

<?php
$address = str_replace(' ', '+', $myCustomer->get('address'));
$city = str_replace(' ', '+', $myCustomer->get('city'));
$map_url = " https://maps.google.com/maps/place/{$address},+{$city},+{$myCustomer->get('state')}+{$myCustomer->get('zip')}";

?>

                                        <td rowspan=3 class="listitemnoborder" style='text-align:center;' width=50><a href='<?=$map_url?>' target='blank'><img src='<?= ROOT_DIR ?>/images/icons/map_32.png' border=0></a></td>

                                        <td class="listrownoborder">

                                            <a href="customers.php?id=<?=$myCustomer->getMyId()?>" class="boldlink" tooltip>

                                                <?=$myCustomer->getDisplayName()?>

                                            </a>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'assign_job_customer', 'Edit customer')?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <td class="listrownoborder"><?=$myCustomer->get('address')?></td>

                                    </tr>

                                    <tr>

                                        <td class="listrownoborder"><?="{$myCustomer->get('city')}, {$myCustomer->get('state')} {$myCustomer->get('zip')}"?></td>

                                    </tr>

                                    <tr>

                                        <td class="listitem"><b>Phone:</b></td>

                                        <td class="listrow"><?=UIUtil::formatPhone($myCustomer->get('phone'))?></td>

                                    </tr>

                                    <tr>

<?php

$emailStr = '';

if($myCustomer->get('email')) {

    $emailStr = $myCustomer->get('email');

    if(strlen($emailStr) > 20) {

        $emailStr = substr(trim($emailStr), 0, 20) . "...";

    }

}

?>

                                        <td class="listitem"><b>Email:</b></td>

                                        <td class="listrow"><a href="mailto:<?=$myCustomer->get('email')?>"><?=$emailStr?></a></td>

                                    </tr>
 <tr>



                                        <td class="listitem"><b>Weather Forcast:</b></td>

                                        <td class="listrow">&nbsp;</td>

                                    </tr>
                                </table>

<div id="map" style="height: 200px; width: 268; margin-left: 22px;"><?php if(empty($myCustomer->get('lat')) or empty($myCustomer->get('long'))){ ?> <p>Coordinates are not available for the customer.</p><?php } ?></div>

<?php if(!empty($myCustomer->get('lat')) or !empty($myCustomer->get('long'))){ ?>
<?php 
//date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 5, date('Y')))
  $url ='https://graphical.weather.gov/xml/sample_products/browser_interface/ndfdXMLclient.php?whichClient=NDFDgenMultiZipCode&zipCodeList='.$myCustomer->get('zip').'&product=time-series&begin='.date('Y-m-d').'T00%3A00%3A00&end='.date('Y-m-d', time() + 86400).'T00%3A00%3A00&Unit=e&wspd=wspd&dryfireo=dryfireo&ptornado=ptornado&phail=phail&pxtornado=pxtornado&pxhail=pxhail&ptotsvrtstm=ptotsvrtstm&pxtotsvrtstm=pxtotsvrtstm&Submit=Submit'; 
// TRY THE REMOTE WEB SERVICE
$response = new GET_Response_Object($url);
if (!$response->document) var_dump($response);
$wobj = SimpleXML_Load_String($response->document);
$convectivehazard= (array) $wobj->data->parameters->{convective-hazard};
$t=$convectivehazard['convective-hazard'][0];
$h=$convectivehazard['convective-hazard'][1]; 
$et=$convectivehazard['convective-hazard'][2];
$eh=$convectivehazard['convective-hazard'][3];
// print_r($convectivehazard['convective-hazard']);
?>
<br>
<p><b>Data Shown Here from <?php echo date('Y-m-d'); ?> to <?php echo date('Y-m-d', time() + 86400); ?></b></p>
<p><?php echo $t->{'severe-component'}->{'name'}; ?> : <?php echo $t->{'severe-component'}->{'value'}; ?> <?php echo $t->{'severe-component'}->attributes()->units; ?></p>
<p><?php echo $h->{'severe-component'}->{'name'}; ?> : <?php echo $h->{'severe-component'}->{'value'}; ?> <?php echo $h->{'severe-component'}->attributes()->units; ?></p>
<p><?php echo $et->{'severe-component'}->{'name'}; ?> : <?php echo $et->{'severe-component'}->{'value'}; ?> <?php echo $et->{'severe-component'}->attributes()->units; ?></p>
<p><?php echo $eh->{'severe-component'}->{'name'}; ?> : <?php echo $eh->{'severe-component'}->{'value'}; ?> <?php echo $eh->{'severe-component'}->attributes()->units; ?></p>
<script type="text/javascript">

    var latlng='';
    var locations='';
    var locations2='';
    var latlongarray="";

 var locations = [
      ['<?php echo $myCustomer->get('address')." ". $myCustomer->get('city')." ". $myCustomer->get('state')." ". $myCustomer->get('zip');?>', <?php echo $myCustomer->get('lat');?>, <?php echo $myCustomer->get('long');?>, 1],
    ];
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 10,
      center: new google.maps.LatLng(<?php echo $myCustomer->get('lat');?>, <?php echo $myCustomer->get('long');?>),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

   var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) { 
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  </script><?php } ?>
                            </td>

                        </tr>

                    </table>

                </td>

                <td width="22%" class="job-detail-column">

                    <table border=0 width='100%' border=0 cellpadding=0 cellspacing=0>

                        <tr valign='top'>

                            <td>

                                <table border=0 width='100%' cellspacing=0 cellpadding=0>

                                    <tr>

                                        <td width=100 class="listitemnoborder"><b>ID Number:</b></td>

                                        <td class="listrownoborder">

                                            <?=$myJob->job_number?>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'modify_job_number', 'Edit job number')?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <td class="listitem"><b>Creator:</b></td>

                                        <td class="listrow">

                                            <a href="users.php?id=<?=$myJob->user_id?>" tooltip>

                                                <?=$myJob->user_fname?> <?=$myJob->user_lname?>

                                            </a>

                                        </td>

                                    </tr>

                                    <tr>

                                        <td class="listitem"><b>Origin:</b></td>

                                        <td class="listrow">

                                            <?=$myJob->origin?>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'modify_job_origin', 'Edit origin')?>

                                        </td>

                                    </tr>

<?php

if($myJob->get('referral')) {

?>

                                    <tr>

                                        <td class="listitem"><b>Referral:</b></td>

                                        <td class="listrow">

                                            <a href="users.php?id=<?=$myJob->referral_id?>" tooltip>

                                                <?=$myJob->referral_fname?> <?=$myJob->referral_lname?>

                                            </a>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'assign_job_referral', 'Edit referral')?>

                                        </td>

                                    </tr>

<?php

}

if($myJob->get('canvasser_id')) {

?>

                                    <tr>

                                        <td class="listitem"><b>Canvasser:</b></td>

                                        <td class="listrow">

                                            <?=UserUtil::getDisplayName($myJob->get('canvasser_id'))?>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'assign_job_canvasser', 'Edit canvasser')?>

                                        </td>

                                    </tr>

<?php

}

if($myJob->get('salesman')) {

?>

                                    <tr>

                                        <td class="listitem"><b>Customer:</b></td>

                                        <td class="listrow">

                                            <a href="users.php?id=<?=$myJob->salesman_id?>" tooltip>

                                                <?=$myJob->salesman_fname?> <?=$myJob->salesman_lname?>

                                            </a>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'assign_job_salesman', 'Edit salesman')?>

                                        </td>

                                    </tr>

<?php

}

?>

                                    <tr>

                                        <td class="listitem"><b>Created:</b></td>

                                        <td class="listrow">

                                            <?=$myJob->dob?>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'edit_job_date', 'Edit creation date')?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <td class="listitem"><b>Age:</b></td>

                                        <td class="listrow"><?=$myJob->getAgeDays()?></td>

                                    </tr>

<?php

if(!empty($myJob->jurisdiction)) {

?>

                                    <tr>

                                        <td class="listitem"><b>Jurisdiction:</b></td>

                                        <td class="listrow">
                                            <?php if(!empty($myJob->jurisdiction_id)) { ?>
                                            <a data-fancybox data-type="ajax" data-src="http://pocketofficepro.com/xactbid/includes/ajax/get_tooltip.php?type=jurisdiction&id=<?=$myJob->jurisdiction_id?>"  href="javascript:;"> <?=$myJob->jurisdiction?></a>
                                 <?php }else{ ?>
                                                 
                                            <?=$myJob->jurisdiction?>
                                             
                                            <?php } ?>
                                            <?=UIUtil::inlineJobActionLink($myJob, 'assign_job_jurisdiction', 'Edit jurisdiction')?>

                                        </td>

                                    </tr>

<?php

}

if(!empty($myJob->permit)) {

?>

                                    <tr>

                                        <td class="listitem"><b>Permit #:</b></td>

                                        <td class="listrow">

                                            <?=$myJob->permit?>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'assign_job_permit', 'Edit permit')?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <td class="listitem"><b>Expires:</b></td>

                                        <td class="listrow"><?=$myJob->permit_expire?></td>

                                    </tr>

<?php

    if(!empty($myJob->midroof) || !empty($myJob->midroofLadder)) {

        $midroofStr = !empty($myJob->midroof) ? $myJob->midroof : '';

        $midroofStr .= !empty($myJob->midroofLadder) ? (!empty($midroofStr) ? ' - ' : '') . "$myJob->midroofLadder story min." : '';

?>

                                    <tr>

                                        <td class="listitem"><b>Midroof:</b></td>

                                        <td class="listrow"><?=$midroofStr?></td>

                                    </tr>

<?php

    }

}

?>

                                    <tr>

                                        <td class="listitem"><b>Job Type:</b></td>

                                        <td class="listrow">

                                            <?=$myJob->job_type?>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'modify_job_type', 'Edit type')?>

                                        </td>

                                    </tr>
                                    <?php

if(!empty($myJob->po_number))

{

?>
                                     <tr>

                                        <td class="listitem"><b>PO Number:</b></td>

                                        <td class="listrow">

                                            <?=$myJob->po_number?>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'add_po_number', 'Edit PO Number')?>

                                        </td>

                                    </tr>

<?php
}
if(!empty($myJob->job_type_note))

{

?>

                                    <tr>

                                        <td class="listitem"><b>Job Type Note:</b></td>

                                        <td class="listrow"><?=$myJob->job_type_note?></td>

                                    </tr>

<?php

}

if(!empty($myJob->insurance))

{

?>

                                    <tr>

                                        <td class="listitem"><b>Provider:</b></td>

                                        <td class="listrow">

                                            <?=$myJob->insurance?>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'modify_insurance', 'Edit insurance information')?>

                                        </td>

                                    </tr>

                                    <tr>

                                        <td class="listitem"><b>Provider's Phone:</b></td>

                                        <td class="listrow">
                                            <?php
                                            $InsuranceDetail = InsuranceModel::getProviderById($myJob->insurance_id);
                                           // print_r($InsuranceDetail);
                                            ?>

                                            <?=UIUtil::formatPhone($InsuranceDetail['phone_no']);?>                                            

                                        </td>

                                    </tr>
                                    <tr>

                                        <td class="listitem"><b>Provider's Fax:</b></td>

                                        <td class="listrow">

                                            <?=UIUtil::formatPhone($InsuranceDetail['fax_no']);?>                                            

                                        </td>

                                    </tr>
                                    <tr>

                                        <td class="listitem"><b>Provider's Email:</b></td>

                                        <td class="listrow">

                                            <?=$InsuranceDetail['email'];?>                                            

                                        </td>

                                    </tr>
                                   

<?php

}

$policy = MetaUtil::get($myJob->meta_data, 'insurance_policy');

if($policy) {

?>

                                    <tr>

                                        <td class="listitem"><b>Policy:</b></td>

                                        <td class="listrow">

                                            <?=$policy?>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'modify_insurance', 'Edit insurance information')?>

                                        </td>

                                    </tr>

<?php

}

if(!empty($myJob->claim)) {

?>

                                    <tr>

                                        <td class="listitem"><b>Claim:</b></td>

                                        <td class="listrow">

                                            <?=$myJob->claim?>

                                            <?=UIUtil::inlineJobActionLink($myJob, 'modify_insurance', 'Edit insurance information')?>

                                        </td>

                                    </tr>
                                    
                                     

<?php
}
if(!empty($myJob->date_of_loss)) {?>
    <tr>
        <td class="listitem"><b>DOL:</b></td>
        <td class="listrow">
            <?=$myJob->date_of_loss?>
            <?=UIUtil::inlineJobActionLink($myJob, 'modify_insurance', 'Edit insurance information')?>
        </td>
    </tr> 
<?php
} 
 if(!empty($myJob->adjuster_name)) {?>
                                  <tr>

                                        <td class="listitem"><b>Adjuster Name:</b></td>

                                        <td class="listrow">

                                            <?=$myJob->adjuster_name?>                                          

                                        </td>

                                    </tr>
                     <?php }?>
                     <?php if(!empty($myJob->adjuster_email)) {?>
                                  <tr>

                                        <td class="listitem"><b>Adjuster Email:</b></td>

                                        <td class="listrow">

                                            <?=$myJob->adjuster_email?>                                          

                                        </td>

                                    </tr>
                     <?php }?>
                     <?php if(!empty($myJob->adjuster_phone)) {?>
                                  <tr>

                                        <td class="listitem"><b>Adjuster Phone Number:</b></td>

                                        <td class="listrow">

                                            <?=str_replace(':',' ex:',UIUtil::formatPhone($myJob->adjuster_phone))?>                                          

                                        </td>

                                    </tr>
                     <?php }?>
                                    

                                </table>

                            </td>

                        </tr>

                    </table>

                </td>

                <td width="22%" class="job-detail-column">

                    <table border=0 width='100%' border=0 cellpadding=0 cellspacing=0>

                        <tr>

                            <td>

                                <ul class="job-items-list">

                                    <?=$myJob->getJobItemsList()?>

<?php

if(!empty($myJob->meta_data['job_warranty'])) {

	$warranties_array = JobUtil::getAllWarranties();

	$warranty_label = $warranties_array[$myJob->meta_data['job_warranty']['meta_value']]['label'];

	$warranty_color = $warranties_array[$myJob->meta_data['job_warranty']['meta_value']]['color'];

?>

									<li>

                                        <i class="icon-star" style="color: <?=$warranty_color?>;"></i>&nbsp;

<?php

	if(!empty($myJob->meta_data['job_warranty_processed'])) {

?>

                                        

<?php

	}

	if(ModuleUtil::checkJobModuleAccess('assign_job_warranty', $myJob)) {

?>

                                        <a href="" rel="open-modal" data-script="assign_jobwarranty.php?id=<?=$myJob->job_id?>"><?=$warranty_label?></a>

<?php

	} else {

?>

                                        <?=$warranty_label?>

<?php

	}

?>

									</li>

<?php

}

?>

                                </ul>

                            </td>

                        </tr>

                    </table>

                </td>

            </tr>

        </table>

    </td>

</tr>

