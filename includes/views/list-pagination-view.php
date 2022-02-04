<table border=0 width='100%' class="job_list_pagination">
    <tr>
        <td align='left' width='250'>
<?php
if($limit > 1) {
    $prev_number = $results_per_page;
    if(($total_results - $limit) < $results_per_page) {
        $prev_number = $total_results - $limit;
    }
    $query_string_params['limit'] = $limit - $results_per_page;
?>
    <a href='javascript:Request.make("includes/ajax/<?=@$script?>.php?<?=http_build_query($query_string_params)?>", "<?=@$destination?>", "yes", "yes");' class='basiclink'>
		<i class="icon-double-angle-left"></i>
		&nbsp;Prev <?php echo $prev_number; ?>
	</a>
<?php
}
?>
        </td>
        <td align='center' width=200>
<?php
if(($limit + $results_per_page) > $total_results) {
    echo "<b>Showing: " . ($limit + 1) . " - $total_results of $total_results</b>";
}
else
{
    echo "<b>Showing: " . ($limit + 1) . " - " . ($limit + $results_per_page) . " of $total_results</b>";
}
?>
        </td>
        <td align='right' width='250'>
<?php
$next_number = $total_results - ($limit + $results_per_page);
if($next_number >= $results_per_page) {
    $next_number = $results_per_page;
}

if(($limit + $results_per_page) < $total_results) {
    $query_string_params['limit'] = $limit + $results_per_page;
?>
			<a href='javascript:Request.make("includes/ajax/<?=@$script?>.php?<?=http_build_query($query_string_params)?>", "<?=@$destination?>", "yes", "yes");' class='basiclink'>
				Next <?php echo $next_number; ?>&nbsp;
				<i class="icon-double-angle-right"></i>
			</a>
<?php
}
?>
        </td>
    </tr>
</table>