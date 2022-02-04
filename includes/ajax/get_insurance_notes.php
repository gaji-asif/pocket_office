<?php
include '../common_lib.php'; 
$insurance_id=$_GET['insurance_id'];

$sql="select * from insurance_notes where insurance_id='$insurance_id' order by insurance_note_id desc";
$notes = DBUtil::queryToArray($sql);
if(count($notes)>0)
{
$html_area ='<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Insurance Notes</td>
    </tr>
</table>

<table class="table-bordered table-condensed table-striped" width="100%">
    <thead>
        <tr>
            <th>#</th>
            <th>Note</th>
        </tr>

    </thead>

    <tbody>';
    $i=1;
    foreach($notes as $row) {
    
        $html_area .='<tr>
            <td>'.$i.'</td>
            <td >'.$row['notes'].'</td>
        </tr>';
        
      $i++;
    }
$html_area .='</tbody></table>';


echo $html_area;
}
else
{
    echo '';
}

?>