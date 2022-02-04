<?php
if(!isset($info) || empty($info) || !is_array($info)) { return; }

$info = array_replace($info,
    array_fill_keys(
        array_keys($info, 'Y'),
        'Yes'
    )
);
$info = array_replace($info,
    array_fill_keys(
        array_keys($info, 'N'),
        'No'
    )
);
//print_r($info); 
?>



<h2 class="custom-header"><?php echo  $info['Title']; ?></h2>

<ul class="custom-tooltip">
   
    <?php if(!empty($info['Website of jurisdiction'])) { ?><li> Website of jurisdiction: <span><?php echo $info['Website of jurisdiction']; ?></span></li><?php } ?>
    <?php if(!empty($info['Phone number of jurisdiction'])) { ?><li> Phone number of jurisdiction: <span><?php echo $info['Phone number of jurisdiction']; ?></span></li><?php } ?>
    <?php if(!empty($info['Email of jurisdiction'])) { ?><li> Email of jurisdiction: <span><?php echo $info['Email of jurisdiction']; ?></span></li><?php } ?>
    <?php if(!empty($info['Code enforced?'])) { ?><li> Code enforced?: <span><?php echo $info['Code enforced?']; ?></span></li><?php } ?>
    <?php if(!empty($info['Link to code book IRC'])) { ?><li> Link to code book IRC: <span><?php echo $info['Link to code book IRC']; ?></span></li><?php } ?> 
    <?php if(!empty($info['Commercial IBC'])) { ?><li> Commercial IBC: <span><?php echo $info['Commercial IBC']; ?></span></li><?php } ?>
    <?php if(!empty($info['Link to code book IBC'])) { ?><li> Link to code book IBC: <span><?php echo $info['Link to code book IBC']; ?></span></li><?php } ?>
    <?php if(!empty($info['Link to IECC'])) { ?><li> Link to IECC: <span><?php echo $info['Link to IECC']; ?></span></li><?php } ?>
    <?php if(!empty($info['Commercial IBC'])) { ?><li> Commercial IBC: <span><?php echo $info['Commercial IBC']; ?></span></li><?php } ?>
</ul>
<ul class="residentiol">
    <h3>Residential</h3>
   <?php if(!empty($info['Drip Edge Rakes'])) { ?><li> Drip Edge Rakes: <span><?php echo $info['Drip Edge Rakes']; ?></span></li><?php } ?>
   <?php if(!empty($info['Drip Edge Eaves'])) { ?><li> Drip Edge Eaves: <span><?php echo $info['Drip Edge Eaves']; ?></span></li><?php } ?>
   <?php if(!empty($info['Valley Liner'])) { ?><li> Valley Liner: <span><?php echo $info['Valley Liner']; ?></span></li><?php } ?>
   <?php if(!empty($info['Step Flashing'])) { ?><li> Step Flashing: <span><?php echo $info['Step Flashing']; ?></span></li><?php } ?>
   <?php if(!empty($info['Kickouts Required?'])) { ?><li> Kickouts Required?: <span><?php echo $info['Kickouts Required?']; ?></span></li><?php } ?>
   <?php if(!empty($info['Insulation Wall'])) { ?><li> Insulation Wall: <span class="r-btn"><?php echo $info['Insulation Wall']; ?></span></li><?php } ?>
   <?php if(!empty($info['Insulation Ceiling'])) { ?><li> Insulation Ceiling: <span><?php echo $info['Insulation Ceiling']; ?></span></li><?php } ?>
   <?php if(!empty($info['Require IR shingle?'])) { ?><li> Require IR shingle?: <span><?php echo $info['Require IR shingle?']; ?></span></li><?php } ?>
   <?php if(!empty($info['Ice and Water Shield required?'])) { ?><li> Ice and Water Shield required?: <span class="r-btn"><?php echo $info['Ice and Water Shield required?']; ?></span></li><?php } ?>
   <?php if(!empty($info['Allow Shakes?'])) { ?><li>Allow Shakes?: <span><?php echo $info['Allow Shakes?']; ?></span></li><?php } ?>
   <?php if(!empty($info['Class A requirement?'])) { ?><li> Class A requirement?: <span class="r-btn"><?php echo $info['Class A requirement?']; ?></span></li><?php } ?>
   <?php if(!empty($info['Ventilation Enforced?'])) { ?><li> Ventilation Enforced?: <span><?php echo $info['Ventilation Enforced?']; ?></span></li><?php } ?>
   
</ul>
<ul class="commercial">
    <h3>Commercial</h3>
      <?php if(!empty($info['Drip Edge Rakes-c'])) { ?><li> Drip Edge Rakes: <span><?php echo $info['Drip Edge Rakes-c']; ?></span></li><?php } ?>
   <?php if(!empty($info['Drip Edge Eaves-c'])) { ?><li> Drip Edge Eaves: <span><?php echo $info['Drip Edge Eaves-c']; ?></span></li><?php } ?>
   <?php if(!empty($info['Valley Liner-c'])) { ?><li> Valley Liner: <span><?php echo $info['Valley Liner-c']; ?></span></li><?php } ?>
   <?php if(!empty($info['Step Flashing-c'])) { ?><li> Step Flashing: <span><?php echo $info['Step Flashing-c']; ?></span></li><?php } ?>
   <?php if(!empty($info['Kickouts Required?-c'])) { ?><li> Kickouts Required?: <span><?php echo $info['Kickouts Required?-c']; ?></span></li><?php } ?>
   <?php if(!empty($info['Insulation Wall-c'])) { ?><li> Insulation Wall: <span class="r-btn"><?php echo $info['Insulation Wall-c']; ?></span></li><?php } ?>
   <?php if(!empty($info['Insulation Ceiling-c'])) { ?><li> Insulation Ceiling: <span class="r-btn"><?php echo $info['Insulation Ceiling-c']; ?></span></li><?php } ?>
   <?php if(!empty($info['Require IR shingle?-c'])) { ?><li> Require IR shingle?: <span><?php echo $info['Require IR shingle?-c']; ?></span></li><?php } ?>
   <?php if(!empty($info['Ice and Water Shield required?'])) { ?><li> Ice and Water Shield required?: <span class="r-btn"><?php echo $info['Ice and Water Shield required?-c']; ?></span></li><?php } ?>
   <?php if(!empty($info['Allow Shakes?-c'])) { ?><li>Allow Shakes?: <span><?php echo $info['Allow Shakes?-c']; ?></span></li><?php } ?>
   <?php if(!empty($info['Class A requirement?-c'])) { ?><li> Class A requirement?: <span class="r-btn"><?php echo $info['Class A requirement?-c']; ?></span></li><?php } ?>
   <?php if(!empty($info['Ventilation Enforced?-c'])) { ?><li> Ventilation Enforced?: <span><?php echo $info['Ventilation Enforced?-c']; ?></span></li><?php } ?>
</ul>
<?php if(!empty($info['notes'])) { ?><h5> Notes about this jurisdiction : <span><?php echo $info['notes']; ?></span></h5><?php } ?>
