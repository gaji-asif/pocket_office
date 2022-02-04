<?php
include 'includes/common_lib.php';
$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));
pageSecure($this_page->source);
$firstLast = UIUtil::getFirstLast();

$month_array = array();
for($x=1; $x<=12; $x++) {
  $month_array[] = date('F', mktime(0, 0, 0, $x, 1));
}

?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-bar-chart"></i><?= $this_page->title ?></h1>
<table cellpadding=0 cellspacing=0 border=0 class="main-view-table">
<tr valign='top'>
    <td>
        <table cellpadding=0 cellspacing=0 border=0 width='100%'>
            <tr><td>&nbsp;</td></tr>
            <tr>
                <td class='containertitle'>Global Statistics</td>
            </tr>
        </table>
        <table border=0 cellpadding=0 cellspacing=0 class='infocontainernopadding' width='100%'>
            <tr valign='top'>
                <td width=300>
                    <table border=0 cellpadding=0 cellspacing=0 id='globalcontainer' width='100%'></table>
                </td>
                <td style='border-left: solid 1px #cccccc;' width=100>
                    <table border="0" width="100%">
                        <tr>
                            <td class='listrownoborder'><div id="chart_div"></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>
<?php
$sql = "SELECT count(t.task_id) as total, tt.task, tt.color
        FROM tasks t, task_type tt
        WHERE tt.account_id = '{$_SESSION['ao_accountid']}'
            AND t.task_type = tt.task_type_id
        GROUP BY t.task_type";
$tasksData = DBUtil::queryToArray($sql);
$tasks = array();
foreach($tasksData as $data) {
  $tasks[] = "['{$data['task']}', {$data['total']}]";
}
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load('visualization', '1', {'packages':['corechart']});
    google.setOnLoadCallback(drawChart);

    function drawChart() {
        // Create our data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Number');
        data.addRows([<?=implode(', ', $tasks)?>]);

        //data.pieSliceTextStyle{color:'black'};
        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, {title:"Workload Percentages", width: 375, height: 220, is3D:true});
    }
</script>


<table cellpadding=0 cellspacing=0 border=0 class="main-view-table">
<tr valign='top'>
    <td>
        <table cellpadding=0 cellspacing=0 border=0 width='100%'>
            <tr><td>&nbsp;</td></tr>
            <tr>
                <td class='containertitle'>Sales Statistics</td>
            </tr>
        </table>
        <table border=0 cellpadding=0 cellspacing=0 class='infocontainernopadding' width='100%'>
            <tr valign='top'>
                <td>
                    <table border="0" width="100%">
                        <tr>
                            <td class='listrownoborder'><div id="chart_div2"></div></td>
                        </tr>
                    </table>
                </td>
                <td style='border-left: solid 1px #cccccc;'>
                    <table border="0" width="100%">
                        <tr>
                            <td class='listrownoborder'><div id="chart_div3"></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
          <script type="text/javascript">

            // Load the Visualization API and the piechart package.
            google.load('visualization', '1', {'packages':['corechart']});

            // Set a callback to run when the Google Visualization API is loaded.
            google.setOnLoadCallback(drawChart);

            // Callback that creates and populates a data table,
            // instantiates the pie chart, passes in the data and
            // draws it.
            function drawChart() {
<?php
  $sql = "select invoice_id from invoices, jobs where invoices.job_id=jobs.job_id and jobs.account_id='".$_SESSION['ao_accountid']."'";
  $res = DBUtil::query($sql)or die(mysqli_error());

  $total_charges = 0;
  $total_credits = 0;
  while(list($invoice_id)=mysqli_fetch_row($res))
  {
    $sql = "select sum(amount) from credits where invoice_id='".$invoice_id."'";
    $nums_res = DBUtil::query($sql)or die(mysqli_error());
    list($credits)=mysqli_fetch_row($nums_res);
    $sql = "select sum(amount) from charges where invoice_id='".$invoice_id."'";
    $nums_res = DBUtil::query($sql)or die(mysqli_error());
    list($charges)=mysqli_fetch_row($nums_res);

    $total_charges+=$charges;
    $total_credits+=$credits;
  }
?>
            // Create our data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Charge/Credit');
            data.addColumn('number', 'Amount');
            data.addRows([
              ['Pending', <?php echo ($total_charges-$total_credits) ?>],
              ['Paid', <?php echo $total_credits; ?>]
            ]);

            //data.pieSliceTextStyle{color:'black'};
            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.PieChart(document.getElementById('chart_div2'));
            chart.draw(data, {title:"Collection Balance", width: 375, height: 220, is3D:true, <?php //echo $colors_str; ?>});
          }
          </script>

          <script type="text/javascript">

            // Load the Visualization API and the piechart package.
            google.load('visualization', '1', {'packages':['corechart']});

            // Set a callback to run when the Google Visualization API is loaded.
            google.setOnLoadCallback(drawChart);

            // Callback that creates and populates a data table,
            // instantiates the pie chart, passes in the data and
            // draws it.
            function drawChart() {
<?php
$sql = "select count(jobs.job_id), users.fname, users.lname from jobs, users where date_format(jobs.timestamp, '%Y')='".date('Y')."' and users.user_id=jobs.salesman and jobs.account_id='".$_SESSION['ao_accountid']."' group by jobs.salesman order by count(jobs.job_id) desc limit 5";
$res = DBUtil::query($sql)or die(mysqli_error());
?>
            // Create our data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Salesman');
            data.addColumn('number', 'Jobs');
            data.addRows(5);
<?php
$i=0;
while(list($num_jobs, $fname, $lname)=mysqli_fetch_row($res))
{
?>
            data.setValue(<?php echo $i; ?>, 0, '<?php echo $fname[0].". ".$lname; ?>');
            data.setValue(<?php echo $i; ?>, 1, <?php echo $num_jobs; ?>);
<?php
  $i++;
}
?>
            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div3'));
            chart.draw(data, {title:"YTD Top Salesman", width: 475, height: 220, hAxis: {title: 'Salesman', titleTextStyle: {color: 'red'}}, vAxis: {title: 'Jobs', titleTextStyle: {color: 'red'}}});
          }
          </script>
          <!--Div that will hold the pie chart-->
        </td>
      </tr>
    </table>

<?php
//get some data
$allUsers = UserModel::getAll(TRUE, $firstLast);
?>

    <table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
      <tr><td>&nbsp;</td></tr>
      <tr>
        <td>
          <table class='containertitle' width='100%'>
            <tr>
              <td>System Reports</td>
            </tr>
          </table>
          <table border=0 width='100%' class='infocontainernopadding' cellpadding=2 cellspacing=0>
            <tr class='odd'>
              <td>
                <table border="0" width="100%">
                  <tr>
                    <td class='smalltitle' width=175>Basic Jobs:</td>
                    <td width=10>
                      <form name='basicjobreport' action='includes/reports/jobs.php' method="get">
                      <b>Salesman:</b>
                    </td>
                    <td width=10 style='padding-right: 10px;'>
                      <select name="salesman">
                        <option value=""></option>
<?php
foreach($allUsers as $user) {
?>
                      <option value="<?=$user['user_id']?>"><?=$user['lname']?>, <?=$user['fname']?></option>
<?php
}
?>
                      </select>
                    </td>
                    <td width=10><b>Stage:</b></td>
                    <td width=10 style='padding-right: 10px;'>
                      <select name='stage'>
                        <option value=''></option>
<?php

$sql = "select stage_num, stage from stages where account_id='".$_SESSION['ao_accountid']."' order by stage_num asc";
$res = DBUtil::query($sql)or die(mysqli_error());

while(list($stage_num, $stage)=mysqli_fetch_row($res))
{
?>
                      <option value='<?php echo $stage_num; ?>'><?php echo $stage; ?></option>
<?php
}
?>
                      </select>
                    </td>
                    <td width=10><b>Sort:</b></td>
                    <td width=10 style='padding-right: 10px;'>
                      <select name='order'>
                        <option value='order by j.timestamp desc'>Newest First</option>
                        <option value='order by j.timestamp asc'>Oldest First</option>
                        <option value='order by j.job_number asc'>ID Number Asc</option>
                        <option value='order by j.job_number desc'>ID Number Desc</option>
                        <option value='order by c.lname asc'>Last Name A-Z</option>
                        <option value='order by c.lname desc'>Last Name Z-A</option>
                      </select>
                    </td>
                    <td align='right'>
                      <input type='submit' value='Go' />
                      <input type='reset' value='Reset' />
                      </form>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td>
                <form name='detailedjobreport' action='includes/reports/fulljobs.php' method="get">
                <table border="0" width="100%">
                  <tr class='even'>
                    <td class='smalltitle' width=175>Full Jobs:</td>
                    <td width=60>
                      <b>Start Date:</b>
                    </td>
                    <td width=160 style='padding-right: 10px;'>
                      <script type='text/javascript'>DateInput('full_startdate', true, 'YYYY-MM-DD');</script>
                    </td>
                    <td width=55><b>End Date:</b></td>
                    <td width=160 style='padding-right: 10px;'>
                      <script type='text/javascript'>DateInput('full_enddate', true, 'YYYY-MM-DD');</script>
                    </td>
                    <td width=10><b>Sort:</b></td>
                    <td width=10 style='padding-right: 10px;'>
                      <select name='order'>
                        <option value='order by jobs.timestamp desc'>Newest First</option>
                        <option value='order by jobs.timestamp asc'>Oldest First</option>
                        <option value='order by jobs.job_number asc'>ID Number Asc</option>
                        <option value='order by jobs.job_number desc'>ID Number Desc</option>
                        <option value='order by customers.lname asc'>Last Name A-Z</option>
                        <option value='order by customers.lname desc'>Last Name Z-A</option>
                        <option value='order by jurisdiction.location desc'>Jurisdiction</option>
                      </select>
                    </td>
                    <td align='right'>
                      <input type='submit' value='Go' />
                      <input type='reset' value='Reset' />
                    </td>
                  </tr>
                </table>
                </form>
              </td>
            </tr>
            <tr class='odd'>
              <td>
                <table border="0" width="100%">
                  <tr>
                    <td class="smalltitle" width="175">
                      <form name='incompletetasks' action='includes/reports/incomplete_tasks.php' method="get">
                      Incomplete Tasks:
                    </td>
                    <td align="right" width="10"><b><nobr>Task Type:</nobr></b></td>
                    <td width="10">
                      <select name="task_type">
                        <option value="">All</option>
<?php
$taskTypes = TaskModel::getAllTaskTypes();
foreach($taskTypes as $taskType) {
?>
                        <option value="<?=$taskType['task_type_id']?>"><?=$taskType['task']?></option>
<?php
}
?>
                      </select>
                    </td>
                    <td width=10><b>Sort:</b></td>
                    <td width=10 style='padding-right: 10px;'>
                      <select name="order">
                        <option value='ORDER BY t.timestamp DESC'>Newest First</option>
                        <option value='ORDER BY t.timestamp ASC'>Oldest First</option>
                        <option value='ORDER BY js.location ASC'>Jurisdiction Ascending</option>
                        <option value='ORDER BY js.location DESC'>Jurisdiction Descending</option>
                      </select>
                    </td>
                    <td align="right">
                      <input type="submit" value="Go" />
                      </form>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr class='even'>
              <td>
                <table border="0" width="100%">
                  <tr>
                    <td class='smalltitle'>
                      <form name='incompleterepair' action='includes/reports/incomplete_repairs.php' method="get">
                      Incomplete Repairs:
                    </td>
                    <td align='right'>
                      <input type='submit' value='Go' />
                      </form>
                    </td>
                  </tr>
                </table>

              </td>
            </tr>
            <tr class='odd'>
              <td>
                <table border=0 width='100%' cellpadding=0 cellspacing=0>
                  <tr>
                    <td class='smalltitle' width=175>Completed Tasks:</td>
                    <td width=60>
                      <form name='detailedjobreport' action='includes/reports/completedtasks.php' method="get">
                      <b>Start Date:</b>
                    </td>
                    <td width=160 style='padding-right: 10px;'>
                      <script type='text/javascript'>DateInput('completed_startdate', true, 'YYYY-MM-DD'<?php echo $date_str;?>);</script>
                    </td>
                    <td width=55><b>End Date:</b></td>
                    <td width=160 style='padding-right: 10px;'>
                      <script type='text/javascript'>DateInput('completed_enddate', true, 'YYYY-MM-DD'<?php echo $date_str;?>);</script>
                    </td>
                    <td width=10><b>Sort:</b></td>
                    <td width=10 style='padding-right: 10px;'>
                      <select name='order'>
                        <option value='order by jobs.timestamp desc'>Newest First</option>
                        <option value='order by jobs.timestamp asc'>Oldest First</option>
                        <option value='order by jobs.job_number asc'>ID Number Asc</option>
                        <option value='order by jobs.job_number desc'>ID Number Desc</option>
                        <option value='order by customers.lname asc'>Last Name A-Z</option>
                        <option value='order by customers.lname desc'>Last Name Z-A</option>
                        <option value='order by jurisdiction.location desc'>Jurisdiction</option>
                      </select>
                    </td>
                    <td align='right'>
                      <input type='submit' value='Go' />
                      <input type='reset' value='Reset' />
                      </form>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr class='even'>
              <td>
                <table border=0 width='100%' cellpadding=0 cellspacing=0>
                  <tr>
                    <td class='smalltitle' width=160 rowspan=2>Unpaid Referrals:</td>
                    <td align="right">
                      <form name='unpaidref' action='includes/reports/unpaidref.php' method="get">
                      <b>Referral:</b>
                    </td>
                    <td width=10 style='padding-right: 10px;'>
                      <select name="referral">
                        <option value=""></option>
<?php
foreach($allUsers as $user) {
?>
                      <option value="<?=$user['user_id']?>"><?=$user['lname']?>, <?=$user['fname']?></option>
<?php
}
?>
                      </select>
                    </td>
                    <td align="right"><b>Minimum Stage:</b></td>
                    <td width=10 style='padding-right: 10px;'>
                      <select name='stage'>
                        <option value=''></option>
<?php

$sql = "select stage_num, stage from stages where account_id='".$_SESSION['ao_accountid']."' order by stage_num asc";
$res = DBUtil::query($sql)or die(mysqli_error());

while(list($stage_num, $stage)=mysqli_fetch_row($res))
{
?>
                      <option value='<?php echo $stage_num; ?>'><?php echo $stage; ?></option>
<?php
}
?>
                      </select>
                    </td>
                    <td align="right"><b>Sort:</b></td>
                    <td width=10 style='padding-right: 10px;'>
                      <select name='order'>
                        <option value='order by users.lname asc'>Referral Last Name A-Z</option>
                        <option value='order by users.lname desc'>Referral Name Z-A</option>
                        <option value='order by jobs.timestamp desc'>Newest First</option>
                        <option value='order by jobs.timestamp asc'>Oldest First</option>
                        <option value='order by jobs.job_number asc'>ID Number Asc</option>
                        <option value='order by jobs.job_number desc'>ID Number Desc</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td align="right"><b>Salesman:</b></td>
                    <td>
                      <select name="salesman">
                        <option value=""></option>
<?php
foreach($allUsers as $user) {
?>
                      <option value="<?=$user['user_id']?>"><?=$user['lname']?>, <?=$user['fname']?></option>
<?php
}
?>
                      </select>
                    </td>
                    <td align="right"><b>Hide Holds:</b></td>
                    <td>
                      <input type="checkbox" name="holds" value="yes" checked />
                    </td>
                    <td></td>
                    <td></td>
                    <td align='right' rowspan=2>
                      <input type='submit' value='Go' />
                      <input type='reset' value='Reset' />
                      </form>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr class='odd'>
              <td>
                <table border="0" width="100%">
                  <tr>
                    <td class='smalltitle' width=175>
                      <form name='unconfirmedorders' action='includes/reports/unconfirmed_orders.php' method="get">
                      Orders Not Confirmed:
                    </td>
                    <td align="right" width=68><b>Hide Holds:</b></td>
                    <td width="10">
                      <input type="checkbox" name="holds" value="yes" checked />
                    </td>
                    <td align="right" width=68><b>Sort:</b></td>
					<td width=100>
						<select name="order">
							<option value='order by jobs.timestamp desc'>Newest First</option>
							<option value='order by jobs.timestamp asc'>Oldest First</option>
							<option value='order by (datediff(curdate(),jobs.stage_date)-tasks.duration) desc'>Urgent First</option>
							<option value='order by jobs.job_number asc'>ID Number Asc</option>
							<option value='order by jobs.job_number desc'>ID Number Desc</option>
							<option value='order by customers.lname asc'>Last Name A-Z</option>
							<option value='order by customers.lname desc'>Last Name Z-A</option>
						</select>
					</td>
                    <td align='right'>
                      <input type='submit' value='Go' />
                      </form>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr class='even'>
              <td>
                <table border="0" width="100%">
                  <tr>
                    <td class='smalltitle' width=175>
                      <form name='jobsbymaterial' action='includes/reports/jobsbymaterial.php' method="get">
                      Jobs by Material:
                    </td>
                    <td align="right"><b>Material:</b></td>
                    <td>
                    	<select name="material" onchange="getReportColorDropdown(this, $('#jobsbymaterial_color'))">
<?php
$sql = "select material_id, material from materials where account_id='".$_SESSION['ao_accountid']."' order by material asc";
$res = DBUtil::query($sql)or die(mysqli_error());

while(list($material_id, $material)=mysqli_fetch_row($res))
{
?>
                      	<option value='<?php echo $material_id; ?>'><?php echo stripslashes($material); ?></option>
<?php
}
?>
                    	</select>
                    </td>
                    <td align="right"><b>Color:</b></td>
                    <td>
                    	<select name="color" id="jobsbymaterial_color">
                    		<option value=''>No Color</option>
                    	</select>
                    </td>
<?php
if(1==1)
{
?>
                    <td align="right"><b>City:</b></td>
                    <td>
                    	<select name="city">
                    		<option value=''>All Cities</option>
<?php
$sql = "select city from customers where account_id='".$_SESSION['ao_accountid']."' group by city order by city asc";
$res = DBUtil::query($sql)or die(mysqli_error());

while(list($city)=mysqli_fetch_row($res))
{
?>
                      	<option value='<?php echo $city; ?>'><?php echo stripslashes($city); ?></option>
<?php
}
?>
                    	</select>
                    </td>
<?php
}
?>
                    <td align="right">
                      <input type='submit' value='Go' />
                      <input type='reset' value='Reset' />
                      </form>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr class="odd">
                <td>
                    <table border="0" width="100%">
                        <tr>
                            <td class="smalltitle">Jobs Needing Estimate Approval:</td>
                            <td align="right">
                                <form name='estimate_approval_needed' action='includes/reports/estimate_approval_needed.php' method="get">
                                <input type='submit' value='Go' />
                                </form>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="even">
                <form name="job_salesaman_map" action="includes/reports/job_salesman_map.php" method="get">
                <td>
                    <table border="0" width="100%">
                        <tr>
                            <td class="smalltitle" width="175">Job/Salesman Map:</td>
                            <td width="10"><b>Salesman:</b></td>
                            <td>
                                <select name="salesman">
                                    <option value="">All</option>
<?php
foreach($allUsers as $user) {
?>
                                    <option value="<?=$user['user_id']?>"><?=$user['lname']?>, <?=$user['fname']?></option>
<?php
}
?>
                                </select>
                            </td>
                            <td align="right">
                                <input type="submit" value="Go" />
                            </td>
                        </tr>
                    </table>
                </td>
                </form>
            </tr>
          </table>
        </td>
      </tr>
<tr><td>&nbsp;</td></tr>
</table>

<script type='text/javascript'>
    $(function(){
        Request.make('<?=AJAX_DIR?>/get_allstatistics.php', 'globalcontainer', true, true);
    })();
</script>
</body>
</html>