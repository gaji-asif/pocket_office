<?php

include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('profitability_readwrite'))
	die("Insufficient Rights");

$myJob = new Job(RequestUtil::get('id'));


$sql = "select commission from profit_sheets where profit_sheet_id='" . $myJob->profit_sheet_id . "' limit 1";
$res = DBUtil::query($sql);
list($commission_percentage) = mysqli_fetch_row($res);

if(moduleOwnership('profitability_readwrite') && ($myJob->salesman_id != $_SESSION['ao_userid'] && $myJob->user_id != $_SESSION['ao_userid']))
	die("Insufficient Rights");

$action = $_GET['action'];
$type = $_GET['type'];
$item_id = $_GET['itemid'];

if($action == 'del')
{
	if($type == 'credit')
	{
		$sql = "delete from profit_credits where profit_credit_id='" . $item_id . "' limit 1";
	}
	else if($type == 'charge')
	{
		$sql = "delete from profit_charges where profit_charge_id='" . $item_id . "' limit 1";
	}
	DBUtil::query($sql);
}


if($_GET['a'] != '' && $_GET['n'] != '' && $_GET['t'] != '')
{
	$a = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['a']);
	$n = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['n']);
	$t = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['t']);

	$sql = "insert into profit_charges values(0, '" . $myJob->profit_sheet_id . "', '" . $a . "', '" . $n . "', now())";
	if($t == 'credit')
	{
		$sql = "insert into profit_credits values(0, '" . $myJob->profit_sheet_id . "', '" . $a . "', '" . $n . "', now())";
	}

	DBUtil::query($sql) or die(mysqli_error);
}

$sql = "select profit_charge_id, note, amount from profit_charges where profit_sheet_id='" . $myJob->profit_sheet_id . "' order by amount asc";
$res_charges = DBUtil::query($sql);
$num_charges = mysqli_num_rows($res_charges);

$sql = "select profit_credit_id, note, amount from profit_credits where profit_sheet_id='" . $myJob->profit_sheet_id . "' order by amount asc";
$res_credits = DBUtil::query($sql);
$num_credits = mysqli_num_rows($res_credits);

$materials_total = number_format($myJob->materials_total, 2);
?>
<tr class='' valign="center">
	<td width=20>&nbsp;</td>
	<td>
		Materials
	</td>
	<td width="25%">&nbsp;</td>
	<td width="25%" style='color: red;'><?=$materials_total?></td>
</tr>
<?php

$i = 1;
$total_charges = 0;
if($num_charges != 0)
{
	$total_charges = $total_cost;
	while (list($charge_id, $note, $amt) = mysqli_fetch_row($res_charges))
	{
		$class = 'odd';
		if($i % 2 == 0)
			$class = 'even';
		$total_charges+=$amt;
		?>
<tr class='<?=$class?>' valign="center">
	<td width=20>
		<a href='javascript: Request.make("<?=AJAX_DIR?>/get_profitlist.php?id=<?=$myJob->job_id?>&action=del&type=charge&itemid=<?=$charge_id?>","profitcontainer","","yes");'>
			<img border="0" src='<?=IMAGES_DIR?>/icons/delete.png'>
		</a>
	</td>
	<td colspan=2>
		<?=$note?>
	</td>
	<td width="25%" style='color: red;'><?=$amt?></td>
</tr>
		<?php

		$i++;
	}
}
if($num_credits != 0)
{
	$total_credits = 0;
	while (list($credit_id, $note, $amt) = mysqli_fetch_row($res_credits))
	{
		$class = 'odd';
		if($i % 2 == 0)
		{
			$class = 'even';
		}
		$total_credits += $amt;

?>
<tr class='<?=$class?>' valign="center">
	<td width=20>
		<a href='javascript: Request.make("<?=AJAX_DIR?>/get_profitlist.php?id=$myJob->job_id&action=del&type=credit&itemid=$credit_id","profitcontainer","","yes");'>
			<img border="0" src='<?=IMAGES_DIR?>/icons/delete.png'>
		</a>
	</td>
	<td>
		<?=$note?>
	</td>
	<td width="25%" style='color: green;'>(<?=$amt?>)</td>
	<td width="25%">&nbsp;</td>
</tr>
<?php
		$i++;
	}
}
$total_charges = number_format($total_charges, 2, '.', '');
$total_credits = number_format($total_credits, 2, '.', '');
$gross = number_format(($total_credits - ($total_charges + $materials_total)), 2, '.', '');
$commission = number_format(($gross * ($commission_percentage / 100)), 2, '.', '');
if($commission < 0)
{
	$commission = '0.00';
}

$net = number_format(($gross - $commission), 2, '.', '');
if($net < 0)
	$net = '0.00';

?>
<tr class='<?=$class?>' valign="center">
	<td colspan=2 align="right" style='border-top:1px solid #cccccc;'>
		<b>Totals:</b>
	</td>
	<td width="25%" style='font-weight:bold; color: green; border-top:1px solid #cccccc;'>(<?=$total_credits?>)</td>
	<td width="25%" style='font-weight:bold; color: red; border-top:1px solid #cccccc;'><?=$total_charges?></td>
</tr>
<tr valign="center">
	<td colspan=3 align="right" class="smalltitle" style='border-top:1px solid #cccccc;'>
		<b>Gross Profit:</b>
	</td>
	<td width=60 class="smalltitle" style='border-top:1px solid #cccccc;'><?=$gross?></td>
</tr>
<tr valign="center">
	<td colspan=3 align="right" class="smalltitle">
		<b><?=$commission_percentage?>% Commission TBP:</b>
	</td>
	<td width=60 class="smalltitle"><?=$commission?></td>
</tr>
<tr valign="center">
	<td colspan=3 align="right" class="smalltitle">
		<b>Net Profit:</b>
	</td>
	<td width=60 class="smalltitle"><?=$net?></td>
</tr>