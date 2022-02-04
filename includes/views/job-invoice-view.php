<?php
if(empty($myJob) || get_class($myJob) !== 'Job') { return; }

$action = RequestUtil::get('action');
$invoice_id = RequestUtil::get('invoice_id');


?>

<tr class="job-tab-content invoice" <?=@$show_content_style?>>
    <td colspan=11>
        <div>
            <ul class="nav nav-tabs">
                <li id="invoice_list_tab" class="active"><a href="javascript:void(0);" onclick="showInvList();">List Invoice </a></li>
                <li id="add_tab" rel="open-modal" data-script="add_job_invoice.php?id=<?=$myJob->job_id?>">
                    <a title="Add Job Invoice" class="btn btn-success" href="javascript:void(0);"> <i class="icon-plus"></i> </a>
                </li>
            </ul>
            <div class="clearfix">
                <div id="list_inv" style="min-height:30px;">
                    
                    
                    <div class="clearfix" style="padding:15px;">
    
                        <div id="list_meas" style="min-height:30px;">
                            
                            <table class="table-bordered table-condensed table-padded table-striped" width="100%">
        
                            <thead>
                                <tr>
                                    <th data-sort="string">#Id</th>
                                    <th data-sort="string">Invoice No</th>
                                    <th data-sort="string">Invoice Title</th>
                                    <th data-sort="string">Date</th>
                                    <th data-sort="string">Action</th>
                                </tr>
                            </thead>
        
                            <tbody id="contacts-list">
                                <?php
                                $invoices=UserModel::getInvoiceList($myJob->job_id);
                                if(count($invoices)){
                            
                                $i=0;
                                foreach($invoices as $row) 
                                {
                                    $i++;
                                ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$row['invoice_no']?></td>
                                        <td><?=prepareText($row['invoice_name'])?></td>
                                        <td><?=date('M d,Y @ h:i A',strtotime($row['timestamp']))?></td>
                                        <td>
                                            <div class="btn btn-small btn-success"  rel="open-modal"  data-script="job_invoice.php?id=<?=$myJob->job_id?>&inv_id=<?=$row['invoice_id']?>&action=edit"
                                                title="Edit Invoice" tooltip>
                                                <i class="icon-pencil"></i>
                                            </div>
                                            
                                            <div class="btn btn-small btn-primary" onclick='window.open("includes/ajax/get_invoiceprint.php?id=<?php echo $myJob->job_id; ?>&inv=<?=$row['invoice_id']?>");'
                                                title="Print Invoice" tooltip>
                                                <i class="icon-print"></i>
                                            </div>
                                            
                                            <div class="btn btn-small btn-danger" onclick="if(confirm('Are you sure?')){$(this).closest('tr').remove();Request.make('<?=AJAX_DIR?>/get_invoicelist.php?id=<?=$myJob->job_id?>&inv=<?= $row['invoice_id']?>&action=d', 'invoicecontainer', true, true);}"
                                                title="Delete Invoice" tooltip>
                                                <i class="icon-trash"></i>
                                            </div>
                                        </td>
                                    </tr>
                                <?php 
                                    
                                }
                                }
                                else
                                {
                                ?>
                                
                                <tr>
                                        <td style="text-align:center" colspan="5"><br>No Invoice created for this job<br><br></td>
                                        
                                    </tr>
                                <?php 
                                }
                                ?>
        
        
                            </tbody>
        
                            </table>
        
        
        
                        </div>
    
                    
    
                    </div>
                </div>
            </div>
        </div>        
        
    </td>
</tr>


<script type="text/javascript">

    $("#add_inv").hide();
    function showInvList()
    {
        $("#list_inv").show();
        $("#add_inv").hide();
        $("#invoice_list_tab").addClass('active');
        $("#invoice_add_tab").removeClass('active');
    }
    function showInvAdd()
    {
        $("#list_inv").hide();
        $("#add_inv").show();
        $("#invoice_add_tab").addClass('active');
        $("#invoice_list_tab").removeClass('active');
    }

</script>
