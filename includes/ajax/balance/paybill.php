<?php
include '../../common_lib.php';

echo ViewUtil::loadView('doc-head');


$id = RequestUtil::get('id');
$action = RequestUtil::get('action');

if($_SESSION['ao_level']==3)
   $id = $_SESSION['ao_userid'];

$ac_id = $_SESSION['ao_accountid'];
    $sql = "SELECT t2.salesman,t3.fname, t3.lname,t3.phone,CONCAT(t3.fname, ' ', t3.lname) as customer_name,t3.rate,SUM(TIMESTAMPDIFF(MINUTE,t1.start_time,t1.end_time)) as total_time,FORMAT(SUM((TIMESTAMPDIFF(MINUTE,t1.start_time,t1.end_time)/60)*IF(t4.rate IS NULL,0,t4.rate)),2) as total_bill,IF(p.amnt IS NULL ,0,p.amnt) as total_paid
            FROM job_time_records as t1
            JOIN jobs as t2 on t2.job_id=t1.job_id
            LEFT JOIN users as t3 on t3.user_id=t2.salesman
            LEFT JOIN users as t4 on t4.user_id=t1.user_id
            LEFT JOIN 
            (
                SELECT t5.customer_id,SUM(t5.amount) as amnt 
                FROM tbl_customer_payment AS t5
                WHERE t5.payment_status='A'
                GROUP BY t5.customer_id
            ) as p ON p.customer_id=t2.salesman
            WHERE t2.account_id = '{$_SESSION['ao_accountid']}' AND  t2.salesman='{$id}' GROUP BY t2.salesman";

$billdetails = DBUtil::queryToArray($sql);
$row = $billdetails[0];
$errors = array();
$error = 0;
if($action=='submit')
{
  if(empty($_POST['amount']))
  {
     $errors[]='Please Enter Payable Amount!';
  }
  if(empty($_POST['payment_mode']))
  {
     $errors[]='Please Select Payment Mode!';
  }

  if(count($errors)==0)
  {
      $amount = $_POST['amount'];
      $payment_mode = $_POST['payment_mode'];

      $payment_status = 'P';
      if($_POST['payment_mode']=='cash' || $_POST['payment_mode']=='check' || $_POST['payment_mode']=='credit over the phone' || $_POST['payment_mode']=='cedit memo')
      {
        $payment_status = 'A';
      }

      $payment_date = date('Y-m-d');
      $remarks = $_POST['remarks'];
      $system_trn_id = time();
      $sql = "INSERT INTO tbl_customer_payment (payment_id,account_id,customer_id,amount,payment_mode,payment_date, remarks,system_trn_id, payment_status,created_by) 
      VALUES (NULL,'$ac_id', '$id', '$amount', '$payment_mode', '$payment_date','$remarks','$system_trn_id', '$payment_status', '{$_SESSION['ao_userid']}')";
      DBUtil::query($sql);
      if($_POST['payment_mode']=='paypal')
      {
        $item_name = "Pay Bill";
        $item_number = $system_trn_id;
        $amount = $amount;
  ?>
        <br>
        <br>
        <br>
        <br>
        <br>
        <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
          <tr>
            <td>
              <table  width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr valign="center">
                  <td>
                    <h2 style="text-align: center;"><b> Pay: <?=$amount?>USD </b></h2>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr id="add_contacts">
            <td class="infocontainernopadding">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td style="text-align: center;">
                    <form id="paypal_form" action="<?=PAYPAL_URL?>" method="post" target="_top">
                      <input type="hidden" name="cmd" value="<?=PAYPAL_CMD?>">
                      <input type="hidden" name="business" value="<?=PAYPAL_ID?>">
                      <input type="hidden" name="tax" value="0.00">
                      <input type="hidden" name="item_name" value="Online Payment">
                      <input type="hidden" name="shipping" value="0.00">
                      <input type="hidden" name="currency_code" value="<?=CURRENCY_CODE?>">
                      <input type="hidden" name="amount" value="<?=$amount?>">
                      <input type="hidden" name="text-186" value="<?=$row['fname']?>">
                      <input type="hidden" name="text-302" value="<?=$row['lname']?>">
                      <input type="hidden" name="tel-819" value="<?=$row['phone']?>">
                      <input type="hidden" name="textarea-267" value="Office">
                      <input type="hidden" name="custom" value="<?=$item_number?>">
                      <input type="hidden" name="notify_url" value="<?=NOTIFY_URL?>">
                      <input type="hidden" name="cancel_return" value="<?=CANCEL_URL?>">
                      <input type="hidden" name="return" value="<?=RETURN_URL?>">
                      <input type="submit" name="submit" value="Pay Now" title="PayPal - The safer, easier way to pay online!">
                  </form>

                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>


      <?php 
      die;
      }
      else
      {
  ?>
        <script>
            $(document).ready(function()
            {
                try{
                   var opener = window.parent;
                   opener.location.reload();
                   var closebutton = $('.btn-close-modal');   
                   closebutton.trigger('click');
                  }
                 catch(e)
                 {
                    alert(e);
                 }
            });        
          </script>

<?php
      }

  }
}

?>

    <?=AlertUtil::generate($errors, 'error', TRUE)?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                Bill Payment
              </td>
              <td align="right">
              <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr id="add_contacts">
        <td class="infocontainernopadding">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php
$query_str='id='.$id.'&action=submit';
//echo $_SESSION['ao_level'];die;
?>

          <tr>

            <td>
              <form method="post" name='stop_timer' action='?<?php echo $query_str;?>' enctype="multipart/form-data">
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr> 
                    <td>&nbsp;
                    </td> 
                    </tr>

                    <tr>    
                    <td class="listitem" width="200"><b>Customer Name</b></td>
                    <td class="listitemnoborder">
                    <?php echo $row['customer_name'];?>
                    </td>
                  </tr>     

                  <tr>
                      <td class="listitemnoborder"><b>Total Billl Amount:</b></td>
                      <td class="listitemnoborder">
                          <?php echo '$'.$row['total_bill'];?>
                      </td>
                  </tr>

                  <tr>
                      <td class="listitemnoborder"><b>Total Paid Amount:</b></td>
                      <td class="listitemnoborder">
                          <?php echo '$'.$row['total_paid'];?>
                      </td>
                  </tr>

                  <tr>
                      <td class="listitemnoborder"><b>Total Due Amount:</b></td>
                      <td class="listitemnoborder">
                          <?php echo '$'.($row['total_bill']-$row['total_paid']);?>
                      </td>
                  </tr>

                  <tr>
                      <td class="listitemnoborder"><b>Payable Amount:</b><span class="red">*</span></td>
                      <td class="listitemnoborder">
                          <input type="text" name="amount" value="<?php echo ($row['total_bill']-$row['total_paid']);?>" />$
                      </td>
                  </tr>

                  <tr>    
                    <td class="listitem"><b>Payment Mode:</b> <span class="red">*</span></td>
                    <td class="listitemnoborder">                    
                        <select style="width:152px;"  name="payment_mode">
                            <option  value="paypal">Credit Card</option>
                            <?php if($_SESSION['ao_level']==1){?>
                            <option  value="cash">Cash</option>
                            <option  value="check">Check</option>
                            <option  value="credit over the phone">Credit over the phone</option>
                            <option  value="cedit memo">Credit Memo</option>
                            <?php }?>
                        </select>
                    </td>
                  </tr>   
                  <!-- <tr>    
                    <td class="listitem"><b>Status:</b></td>
                    <td class="listitemnoborder">                    
                        <select style="width:152px;" name="status">
                            <option value="P">Pending</option>
                            <option  value="A">Approve</option>
                        </select>
                    </td>
                  </tr>    -->

                  <tr>
                    <td class="listitem" ><b>Remarks:</b></td>
                    <td class="listitemnoborder">
                        <textarea style="width: 152px;" rows="2" name="remarks"></textarea>
                    </td>
                  </tr>  

                </table>

              </td>

            </tr>

          </table>

          <table border="0" width="100%" cellpadding="0" cellspacing="0">

            <tr>
              <td></td>
              <td align="right" class="listrow">
                  <input type="submit" value="Proceed">
              </td>
            </tr>

          </table>
          </form>
        </td>

      </tr>

    </table>

  </body>

</html>

