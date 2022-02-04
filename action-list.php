<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if($_SESSION['ao_founder'] != 1) {
    die('Insufficient Rights');
}
?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - Actions</h1>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="main-view-table">
    <tr>
        <td>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="containertitle">
                <tr>
                    <td width="10%">
                      #Id
                    </td>
                    <td width="75%">
                      Action
                    </td>
                    <td >
                      Status
                    </td>
                  
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table align="center" border="0" cellpadding="2" cellspacing="0" width="100%" class="infocontainernopadding">
<?php
//$actions = DBUtil::queryToArray('select * from job_actions');
$actions = DBUtil::queryToArray('select * from job_actions where job_action_id not in(8,11,12,13,17,24,25,26,27,28,32,33)');
 

$count=0;
foreach($actions as $key => $action) {
    $class = $key % 2 == 0 ? 'even' : 'odd';
    $count++;
    ?>
   
	<tr class="<?=$class?>">
        <form method="post" action="?">
    	    <td width="10%">
                   <?php echo $count;?>
            </td>
		    <td width="75%" style="padding:10px;">
                    <?=$action['action']?>
            </td>
            <td>
                <a class="<?php echo $status=$action['active']?'btn btn-blue':'btn btn-danger'; ?>" id="status_field_<?php echo $action['job_action_id'];?>" href="javascript:void(0);" onclick="changeStatus(<?php echo $action['job_action_id'];?>,<?php echo $action['active'];?>);"  ><?php echo $status=$action['active']?'Active':'Inactive'; ?></a>
            </td>
		   
        </form>
	</tr>
	    
<?php
}
if(!count($actions)) {
?>
    <tr>
        <td align="center"><b>No action Found</b></td>
    </tr>
<?php
}
?>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

<script type="text/javascript">
function changeStatus(id,status)
{   
    var st=$(this).text();
    var level_id=$("#level").val();
    var query_str="id=" + id+"&status="+status+"&update='update'";    
    $.ajax({
        url: '<?php echo AJAX_DIR;?>/action_status.php',
        data: query_str,        
        type: 'POST',
        success: function(result){

            if(status)
            {
                var str='changeStatus('+id+',0)';
                $("#status_field_"+id).attr('onclick',str);                
                $("#status_field_"+id).text('Inactive');
                $("#status_field_"+id).removeClass('btn btn-blue');
                $("#status_field_"+id).addClass('btn btn-danger');
            }
            else
            {
                var str='changeStatus('+id+',1)';
                $("#status_field_"+id).attr('onclick',str);
                $("#status_field_"+id).text('Active');
                $("#status_field_"+id).removeClass('btn btn-danger');
                $("#status_field_"+id).addClass('btn btn-blue');
            }

        }
    });        
}
</script>
