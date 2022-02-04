<?php
include 'includes/common_lib.php';
$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));
pageSecure($this_page->source);
$firstLast = UIUtil::getFirstLast();
?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-calendar"></i><?=$this_page->title?></h1>

    <table border="0" cellpadding="5" cellspacing="0" width="100%">
      <tr>
        <td colspan=2>
          <form style='margin-bottom:0;' name='form'>
            <input type="hidden" class="list-filter-input ignore-filter-reset" id="m" value=""/>
            <input type="hidden" class="list-filter-input ignore-filter-reset" id="Y" value=""/>
            <select id='customer' class="list-filter-input">
                <option value="0">All Customer</option>
                <?php
                
                $where = '';
                if($_SESSION['ao_level']==3)
                  $where = "AND t2.salesman='{$_SESSION['ao_userid']}'";

                $ac_id = $_SESSION['ao_accountid'];
                $sql = "SELECT t1.user_id,CONCAT(t1.fname, ' ', t1.lname) as customer_name
                        FROM users as t1
                        JOIN jobs as t2 on t2.salesman=t1.user_id
                        JOIN job_time_records as t3 on t3.job_id=t2.job_id
                        WHERE t2.account_id = '{$_SESSION['ao_accountid']}' $where  GROUP BY t1.user_id ORDER BY  t2.job_id DESC";
                $users_array = DBUtil::queryToArray($sql);

                foreach($users_array as $user)
                {?>
                    <option value="<?=$user['user_id']?>"><?=$user['customer_name']?></option>
                <?php
                }?>
            </select>
            <input type='button' value='Search' onclick="filterList('<?=AJAX_DIR?>/billing/get_timerweek.php?id='+document.getElementById('customer').value, 'schedulecontainer');">

            <input type='button' value='Clear Filters' onclick="resetFilterListInputs(); filterList('<?=AJAX_DIR?>/billing/get_timerweek.php', 'schedulecontainer');">
          </form>
        </td>
      </tr>
    </table>
<?php
if(ModuleUtil::checkAccess('view_billing')) {
?>
    <table border=0 cellspacing=0 cellpadding=0 class="schedule-table">
      <tr>
        <td id='schedulecontainer'></td>
      </tr>
      <tr>
        <td>
          &nbsp;
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/billing/get_timerweek.php?ws=<?=RequestUtil::get('ws')?>', 'schedulecontainer', true, true);
          </script>
        </td>
      </tr>
    </table>
<?php
} else {
    echo ModuleUtil::showInsufficientRightsAlert('view_billing', TRUE);
}
?>
  </body>
</html>
