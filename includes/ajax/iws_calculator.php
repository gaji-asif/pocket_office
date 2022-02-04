<?php
include '../common_lib.php';

echo ViewUtil::loadView('doc-head');
$iws_base_path = '/workflow/contact';
$myJob = new Job(RequestUtil::get('id'));
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Calculate Form</title>
    <link href="<?=$iws_base_path?>/css/demo.css" rel="stylesheet" type="text/css">
    <!--Framework-->
    <script src="<?=$iws_base_path?>/js/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="<?=$iws_base_path?>/js/jquery-ui.js" type="text/javascript"></script>
    <!--End Framework-->
    
    <script src="<?=$iws_base_path?>/js/jquery.ffform2.js" type="text/javascript"></script>   
    <style type="text/css">
        .result_area{    
            float: left;
            width: 50%;
            text-align: center;
            font-size: 15px;
        }
    </style> 
</head>
<body style="padding-top: 0px!important;">
    
    <section id="getintouch" >
        <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
        <tr>
          <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
              <tr valign="center">
                <td>
                 &nbsp;
                </td>
                <td align="right">
                <i class="icon-remove grey btn-close-modal"></i>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
        <div class="container" style="border-bottom: 0; width:70%">
            <h1>
                <span>Ice and Water Shield Calculator</span>
            </h1>
        </div>
        <div class="container">
            <div class="row clearfix">
                <img src="<?=$iws_base_path?>/images/top.jpg" style="width: 100%; border: 1px solid;">
            </img">
            <?php
            $user_id = $_SESSION['ao_userid'];
            $job_id = RequestUtil::get('id');
            $action = RequestUtil::get('submit');
            $myJob = new Job($job_id);
            $error = 0;
            $area = 'form';
            $request_data = '';
            if($action=='calculate')
            {
                $area = 'result';
                $pitch = $_POST['pitch'];
                $overhang = $_POST['overhang'];
                $wall = $_POST['wall'];
                $interior_wall = $_POST['interior_wall'];        

                $iws = ($overhang + $wall + $interior_wall)*$pitch;

                $roll_value = (int)($iws/36) + 1;
                $final = ($iws%36==0)?$iws:($roll_value*36);
                $request_data = "pitch=".$pitch."&overhang=".$overhang."&wall=".$wall."&interior_wall=".$interior_wall;

            }
            ?>
            <div id="result" style="display:<?php echo ($area=='form')?'none':'block';?>;">
                <div class="row clearfix" style="margin-top: 50px;margin-bottom: 50px;">                
                    <div class="result_area" id="original-inputs">
                        <b>Pitch: </b><?=$pitch?><br>
                        <b>Overhang: </b><?=$overhang?><br>
                        <b>Wall: </b><?=$wall?><br>
                        <b>Interior Wall: </b><?=$interior_wall?>
                    </div>
                    <div class="result_area" id="calculation-result">
                        <b>Ice and Water Shield: </b><br> 
                        <?=$iws?> inches<br>
                        <b>So you would need: </b><br>
                            <?=$final?> inches
                    </div>
                </div>
                <span id="msg-close" onclick="openForm();">Reset</span>
                <a href="javascript:void(0);"onclick='window.open("iws_report.php?id=<?=$myJob->job_id; ?>&<?=$request_data?>");'><span id="msg-print">Print</span></a>
            </div>
            <form id="calculator_form" class="contact" action="" method="post" id="form"  style="display: <?php echo ($area=='result')?'none':'block';?>;">
            <div class="row clearfix">
                <div class="lbl">
                    <label for="pitch">
                        Pitch</label>
                </div>
                <div class="ctrl">
                    <?php
                      $sql = "SELECT * from iws_pitch";
                      $pitches = DBUtil::queryToArray($sql);
                    ?>
                    <select name="pitch"   style="width: 40%">
                        <option value="">--Select--</option>
                        <?php foreach($pitches as $row){?>
                        <option value="<?php echo $row['multiplier'];?>"><?php echo $row['pitch'].' - '.$row['multiplier'];?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            <div class="row clearfix">
                <div class="lbl" style="width: 10%">
                    <label for="overhang">
                        Overhang</label>
                </div>
                <div class="ctrl"  style="width: 40%">
                    <input type="text" id="overhang" name="overhang" data-required="true" data-validation="text"
                        data-msg="Invalid overhang vlaue" placeholder="Inches">
                </div>

                <div class="lbl" style="width: 10%">
                    <label for="wall">
                        Wall</label>
                </div>
                <div class="ctrl"  style="width: 40%">
                    <input type="text" id="wall" name="wall" data-required="true" data-validation="text"
                        data-msg="Invalid wall value" placeholder="Inches">
                </div>

                <div class="lbl" style="width: 10%">
                    <label for="interior_wall">
                        Interior Wall</label>
                </div>
                <div class="ctrl"  style="width: 40%">
                    <input type="text" name="interior_wall" id="interior_wall" readonly value="24">
                </div>
            </div>
            <div class="row clearfix">
                
            </div>            
            <div class="row clearfix">
                
            </div>
            
            <div class="row  clearfix">
                <div class="spna10 offset2">
                    <input type="submit" name="submit" id="calculate" class="calculate" value="calculate">
                </div>
            </div>
            </form>

            
            
        </div>
    </section>
</body>
</html>

<script type="text/javascript">
    function openForm()
    {
        $("#calculator_form").show();
        $("#result").hide();
    }
</script>
