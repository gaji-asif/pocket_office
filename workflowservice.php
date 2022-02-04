<?php 
// SOFIKUL SARDAR API development started on 01/12/2021 ======//
 header('Access-Control-Allow-Origin: *');
 header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
 header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
 
 include 'includes/common_lib.php';
  $response = '';
  $link = '';
   if($_REQUEST['MethodName']) 
   {
      connect();
      $methodname = $_REQUEST['MethodName'];
      $response = $methodname();
      echo $response;
    }
    

    function connect()
    {
        if(!$link)
            $link = DBUtil::connect('pocketoffice_xactbid');
            
    }
    
    // 16-12-2021 SOFIKUL Validate Authentication Token 
    function validateAuthTokenAndGetUserId($token)
    {
        $token = trim($_REQUEST['access_tocken']);
        $sql="SELECT * FROM access_tocken WHERE token='{$token}' AND status = 'in'";
        $token_arr = DBUtil::queryToArray($sql);
        if(count($token_arr)>0)
        {
            $token_row = $token_arr[0];
            $user_id = $token_row['user_id'];
            return $user_id;
        }
        else
        {
            return false;
        }
    }
    
    
    //Login- User - 01-12-2021
    function Login()
    {	 
        
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            if(empty($ap['username'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Username is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['password'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Password is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
     
            $username = $ap['username'];
            $password = $ap['password'];
       
    
            //Related Methods: AuthModel::attemptToLogin($username, $password, $account);
            $sql= "SELECT users.user_id AS ao_userid, users.username AS ao_username, users.fname AS ao_fname, users.lname AS ao_lname, users.dba AS ao_dba,
                DATE_FORMAT(access.timestamp, '%c/%e %k:%i') AS ao_lastvisit, users.level AS ao_level, users.is_active, users.is_deleted,users.password as ao_password,
                accounts.account_name AS ao_accountname, users.account_id AS ao_accountid, users.founder AS ao_founder, settings.num_results AS ao_numresults,
                settings.browsing_results AS ao_browsingresults, settings.refresh AS ao_refresh, settings.widget_today AS ao_widget_today,
                settings.widget_announcements AS ao_widget_announcements, settings.widget_documents AS ao_widget_documents,
                settings.widget_bookmarks AS ao_widget_bookmarks, settings.widget_urgent AS ao_widget_urgent, settings.widget_inbox AS ao_widget_inbox,
                settings.widget_journals AS ao_widget_journals, accounts.logo AS ao_logo, accounts.job_unit AS ao_jobunit, accounts.is_active AS account_is_active,
                users.office_id AS ao_officeid, levels.level as ao_levelname
                FROM accounts, levels, users
                LEFT JOIN settings ON users.user_id = settings.user_id
                LEFT JOIN access ON access.user_id = users.user_id
                WHERE users.username = '$username'
                        AND users.password = '$password'
                        AND accounts.account_id = users.account_id
                        AND levels.level_id = users.level
                ORDER BY access.access_id DESC LIMIT 1";
     
            $results  =   DBUtil::fetchAssociativeArray(DBUtil::query($sql)); 
           
            if(!empty($results)) 
            {                   
                $levelid=$results["ao_level"];
    
                $accountid=$results["ao_accountid"];
                $userid=$results["ao_userid"];
      			$founderId = $results["ao_founder"];
                $sysLogo = $results["ao_logo"];
        		$accountname = $results["ao_accountname"];
        		$username = $results["ao_username"];
        		$job_unit=$results["ao_jobunit"];
    
                $sql = "SELECT a.is_active as a_isactive,u.is_active as u_isactive,u.is_deleted as u_isdeleted FROM accounts
                          a inner join users u on u.account_id=a.account_id where a.account_id='{$accountid}'  && u.user_id='{$userid}' ";
                //echo "<pre>";print_r($_SESSION);die;
                $Users = DBUtil::fetchAssociativeArray(DBUtil::query($sql)); 
                      
             
                if($Users["a_isactive"]==1 && $Users["u_isactive"]==1 && $Users["u_isdeleted"]==0)
                {                 
                    //preload module access
                    ModuleUtil::fetchModuleAccess();

                    //preload nav access
                    UserModel::fetchNavAccess();
               
                    //Related Methods: ModuleUtil::fetchModuleAccess();  -  preload module access
                    $sql = "SELECT m.hook, ma.module_access_id, e.onoff
                      FROM modules m
                      LEFT JOIN exceptions e ON e.module_id = m.module_id AND e.user_id = '{$userid}'
                      LEFT JOIN module_access ma ON ma.module_id = m.module_id AND ma.account_id = '{$accountid}' AND ma.level = '{$levelid}'";
  			
                    $modules = DBUtil::queryToArray($sql);
                    if(empty($modules))
                    {
                        $modules='0';
                    }
                    //Related Methods: UserModel::fetchNavAccess(); - preload nav access
                    $sql = "SELECT n.source
                      FROM nav_access na, navigation n
                      WHERE na.account_id = '{$accountid}'
                      AND na.level = '{$levelid}'
                      AND na.navigation_id = n.navigation_id";
                  
                    $navigations = DBUtil::queryToArray($sql);
             
                    if(empty($navigations))
                    {
                     $navigations='0';
                    }

                    $token = md5(time().uniqid());
                    $token_sql="INSERT INTO access_tocken VALUES(NULL,$userid,'$token',NOW(),NULL,'in')";
                    if(!DBUtil::query($token_sql))
                    {
                        $token = md5(time().uniqid());
                        $token_sql="INSERT INTO access_tocken VALUES(NULL,$userid,'$token',NOW(),NULL,'in')";
                        DBUtil::query($token_sql);
                    }

                    $result_set = array("User" =>$results,"modules" =>$modules,"navigations" =>$navigations);
                    $response = array('status'=>array('error_code'=>0,'message'=>'Success',"access_tocken"=>$token),'result'=>array('data_list'=> $result_set));
                    displayOutput($response);
          
                }
                else
                {
                    $response=array('status'=>array('error_code'=>0,'message'=>'Account Not Active.'),'result'=>array('data_list'=>'')); 
                    displayOutput($response);
                }
          }
          else
          {
            LogUtil::getInstance()->logNotice("Failed login - Invalid credentials: $account,$username,$password ");
            return json_encode(array("message" => "Invalid Credentials!",'status'=> 0));
          }
          }
                                                                                 
           
        else{
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        } 
    }

    //LogOut- User  - 01-12-2021
    function logoutUser()
    {  
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            $token = $ap['access_tocken'];
            $user_id = validateAuthTokenAndGetUserId($token);
            if($user_id)
            {
                $token_sql="UPDATE access_tocken SET status='out',logged_out=NOW() WHERE user_id=$user_id AND token='$token'";
                //echo "<pre>";print_r($token_sql);die;
                if(DBUtil::query($token_sql))
                {
                    $response=array('status'=>array('error_code'=>0,'message'=>'Success','access_tocken'=>''),'result'=>array('data_list'=>'')); 
                    displayOutput($response);
                }
            }
            else
            {
                $response=array('status'=>array('error_code'=>0,'message'=>'Invalid Token!'),'result'=>array('data_list'=>'')); 
                displayOutput($response);
            }                                                                        
        }     
        else{
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        } 
    }
    
    //Menu- Binding - 02-12-2021
    function BindMenu()
    {
        $ap = $_REQUEST;//json_decode(file_get_contents('php://input'), true);
        if(!sizeof($ap))
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
        $user_data = [];
        
        $token = $ap['access_tocken'];
        $sql="SELECT * FROM access_tocken WHERE token='$token' AND status = 'in'";
        $token_arr = DBUtil::queryToArray($sql);
        //echo "<pre>";print_r($sql);die;
        if(count($token_arr)>0)
        {
            $sql="SELECT t2.user_id,t2.account_id,t2.level FROM access_tocken as t1 
                JOIN users as t2 ON t2.user_id=t1.user_id
                WHERE t1.token='$token' AND t1.status = 'in'";
                
            $token_arr = DBUtil::queryToArray($sql);
            $user_data = $token_arr[0];
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'Invalid Token!'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }

        $accountid  =  $user_data['account_id'];
        $levelid  =  $user_data['level'];         
        $user_id  =  $user_data['user_id'];
	  
	       //Related Methods: UIModel::getNavList(); - preload nav access
        $sql = "SELECT distinct n.navigation_id,CASE WHEN n.title =  'Leads & Jobs' THEN  'Jobs' ELSE n.title END AS title, n.source, n.icon
                FROM navigation n, nav_access na
                WHERE 
                    na.account_id = {$accountid}
                    AND n.navigation_id = na.navigation_id
                    AND na.level = {$levelid}
                    ANd n.title!='Announcements' and n.title!='Customers' and n.title !='Reports'
                ORDER BY n.order_num ASC";
          
        $navigationArray = DBUtil::queryToArray($sql);
		 
		$sql = "select * from accounts where account_id='".$accountid."' limit 1";
		$userAcctDetail = DBUtil::queryToArray($sql);

		if(!empty($userAcctDetail))
		{
      		foreach($userAcctDetail as &$origin) 
            {
				$file_path = LOGOS_PATH.'/'.$origin['logo'];    				
				if(file_exists($file_path))
				{
					$origin['logo'] = $origin['logo'];
				}
				else
				{
					$origin['logo'] = 'no_img.png';
				}
      		}
    	}
		 
	    $userSql = "select * from users where user_id ='".$user_id."' limit 1";
	    $userDetail = DBUtil::queryToArray($userSql);
	    
        $result_set =array("lstMenu" =>$navigationArray, "userAcctDetail" => $userAcctDetail, "userDetail" => $userDetail);
        $response=array('status'=>array('error_code'=>0,'message'=>'Success','access_tocken'=>$token),'result'=>array('data_list'=>$result_set)); 
        displayOutput($response);
                    
    }
    
    //Get Urgent Job List for Dashboard   - 03-12-2021
    function GetUrgentJobList()
    {
          $user_data = [];
          $token = '';
          if(isset($_REQUEST['access_tocken'])) 
          {
            $token = trim($_REQUEST['access_tocken']);
            $sql="SELECT * FROM access_tocken WHERE token='$token' AND status = 'in'";
            $token_arr = DBUtil::queryToArray($sql);
            if(!empty($token_arr))
            {
              $sql="SELECT t2.user_id,t2.account_id,t2.level FROM access_tocken as t1 
              JOIN users as t2 ON t2.user_id=t1.user_id
              WHERE t1.token='$token' AND t1.status = 'in'";
              $token_arr = DBUtil::queryToArray($sql);
              $user_data = $token_arr[0];
            }
            else
            {
              return json_encode(array("message" => "Invalid Token!",'status'=> 0));
            }
          }
          else
          {
              return json_encode(array("message" => "Bad Request!",'status'=> 0));
          }

          $accountid = $user_data['account_id'];

          if(viewWidget('widget_urgent'))
          {
            $sql = "SELECT jobs.job_id, jobs.job_number,customers.fname,customers.lname,jobs.stage_num,
                (datediff(curdate(), jobs.stage_date) - stages.duration) AS days_past,
                datediff(curdate(), jobs.stage_date) as das,stages.stage,
                stages.duration,repairs.repair_id,jobs.pif_date,jobs.ins_approval,jobs.referral_paid
                FROM customers, jobs
                LEFT JOIN stages ON (stages.stage_num = jobs.stage_num)
                LEFT JOIN subscribers ON (subscribers.job_id = jobs.job_id)
                LEFT JOIN repairs ON (repairs.job_id = jobs.job_id AND repairs.completed IS NULL)
                LEFT JOIN status_holds ON (status_holds.job_id = jobs.job_id)
                WHERE jobs.customer_id = customers.customer_id
                AND jobs.account_id = $accountid
                AND (datediff(curdate(), jobs.stage_date) - stages.duration) > 0
                AND duration <> 9999
                AND duration IS NOT NULL
                AND duration <> ''
                AND status_holds.status_hold_id IS NULL
                GROUP BY jobs.job_id
                ORDER BY datediff(curdate(), jobs.stage_date) - (stages.duration)
                DESC LIMIT 10";

            $sqlUrgent = DBUtil::queryToArray($sql);
            if(empty($sqlUrgent))
            {
              return json_encode(array('access_tocken'=>$token,'status' => '-1','Post' => $sqlUrgent));
            }
            else
            {
              return json_encode(array('access_tocken'=>$token,'status' => '1','Post' => $sqlUrgent));
            }
          }
          else
          {
            return json_encode(array('access_tocken'=>$token,'status' => '0','Post' => ''));
          }
    }

    // Bind all Job Filter Dropdowns Job Listing - 06-12-2021
    function BindAllJobFilters()
    {
        $user_data = [];
        $token = '';
        if(isset($_REQUEST['access_tocken'])) 
        {
          $token = trim($_REQUEST['access_tocken']);
          $sql="SELECT * FROM access_tocken WHERE token='$token' AND status = 'in'";
          $token_arr = DBUtil::queryToArray($sql);
          if(!empty($token_arr))
          {
            $sql="SELECT t2.user_id,t2.account_id,t2.level FROM access_tocken as t1 
            JOIN users as t2 ON t2.user_id=t1.user_id
            WHERE t1.token='$token' AND t1.status = 'in'";
            $token_arr = DBUtil::queryToArray($sql);
            $user_data = $token_arr[0];
          }
          else
          {
            return json_encode(array("message" => "Invalid Token!",'status'=> 0));
          }
        }
        else
        {
            return json_encode(array("message" => "Bad Request!",'status'=> 0));
        }

        $_SESSION['ao_accountid'] = $user_data['account_id'];
        $_SESSION['ao_userid'] = $user_data['user_id'];
        $_SESSION['ao_level'] = $user_data['level'];

         
        $firstLast = UIUtil::getFirstLast();
        $accountMetaData = AccountModel::getAllMetaData();
        //get filter data
        $showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');
        $dropdownUserLevels = AccountModel::getMetaValue('job_salesman_filter_user_dropdown');
      
        $salesmen = !empty($dropdownUserLevels)
                  ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast)
                  : UserModel::getAll($showInactiveUsers, $firstLast);
          
        $drdsalesstr="<option value=' ' selected  >Salesman</option>";
        $drdsalesstr=$drdsalesstr."<option value='and j.salesman is null' >No Salesmen Assigned</option>";
         
        foreach($salesmen as $smen) {
          $drdsalesstr=$drdsalesstr."<option value='AND j.salesman= ".$smen['user_id'] ."'>".$smen['select_label']. "</option>"; 
        }
        
        $statuses = JobModel::getAllStatuses();
        $stages = StageModel::getAllStages(TRUE);
        $jobTypesArray = JobUtil::getAllJobTypes();
        $taskTypes = TaskModel::getAllTaskTypes();
        $warranties = JobUtil::getAllWarranties();
        
        $providers = InsuranceModel::getAllProviders();
        
        
        if(MetaUtil::get($accountMetaData, 'show_inactive_users_in_lists') == '1') {
            $canvasser = UserModel::getAll(TRUE, $firstLast);
        } 
        else {
            $canvasser = UserModel::getAll(FALSE, $firstLast);
        }
        $drdcanvas="<option value=' ' selected>Canvasser</option>";
        $drdcanvas=$drdcanvas."<option value='and cv.canvasser_id is null'>No Canvasser Assigned</option>";
        foreach($canvasser as $can) {
          $drdcanvas=$drdcanvas."<option value='AND cv.user_id= ".$can['user_id'] ."'>".$can['select_label']. "</option>"; 
        }
        
        if(MetaUtil::get($accountMetaData, 'show_inactive_users_in_lists') == '1') {
          $referral = UserModel::getAll(TRUE, $firstLast);
        } 
        else {
          $referral = UserModel::getAll(FALSE, $firstLast);
        }  
        reset($referral);
        $drdreferral="<option value=' ' selected>Referral</option>";
        $drdcreator="<option value=' ' selected>Creator</option>";
        
        $drdreferral=$drdreferral."<option value='and j.referral is null'>No Referral Assigned</option>";
         
        foreach($referral as $ref) {
          $drdreferral=$drdreferral."<option value='AND j.referral=".$ref['user_id']."'>".$ref['select_label']. "</option>"; 
          $drdcreator=$drdcreator."<option value='AND j.referral=".$ref['user_id']."'>".$ref['select_label']. "</option>"; 

        }
         
        $jurisdictions = CustomerModel::getAllJurisdictions();
       
      
        return json_encode(array('access_tocken'=>$token,'firstLast'=> $firstLast,'salesmen'=>$drdsalesstr,'statuses'=>$statuses,'stages'=>$stages,'jobTypesArray'=>$jobTypesArray,'taskTypes'=>$taskTypes,'warranties'=>$warranties,'providers'=>$providers,'canvasser'=>$drdcanvas,'referral'=>$drdreferral,'creator'=>$drdcreator,'jurisdictions'=>$jurisdictions)); 
    }
    
    
    //Bind Job Listing - 07-12-2021
    function GetFilterJoblist()
    {
        $user_data = [];
        $token = '';
        if(isset($_REQUEST['access_tocken'])) 
        {
          $token = trim($_REQUEST['access_tocken']);
          $sql="SELECT * FROM access_tocken WHERE token='$token' AND status = 'in'";
          $token_arr = DBUtil::queryToArray($sql);
          //echo "<pre>";print_r($sql);die;
          if(!empty($token_arr))
          {
            $sql="SELECT t2.user_id,t2.account_id,t2.level FROM access_tocken as t1 
            JOIN users as t2 ON t2.user_id=t1.user_id
            WHERE t1.token='$token' AND t1.status = 'in'";
            $token_arr = DBUtil::queryToArray($sql);
            $user_data = $token_arr[0];
          }
          else
          {
            return json_encode(array("message" => "Invalid Token!",'status'=> 0));
          }
        }
        else
        {
            return json_encode(array("message" => "Bad Request!",'status'=> 0));
        }
    
        $_SESSION['ao_accountid'] = $user_data['account_id'];
        $_SESSION['ao_userid'] = $user_data['user_id'];
        $_SESSION['ao_level'] = $user_data['level'];
    
        $limit= $_REQUEST['limit'];
        $req_page= $_REQUEST['page'];
        
        $offset = 0;
        $page = 0;
        if($req_page)
        {
          $page = $req_page-1;
          $offset = $page * $limit;
        }
        
        $jobs = JobModel::getList($offset,$limit);
        
        $totalJobs = DBUtil::getLastRowsFound();
       
        $jobids = array_column($jobs, 'job_id');
      
          
        $sql = "SELECT distinct j.job_id,j.job_number,c.customer_id, concat(c.fname,' ',c.lname) as custName,c.address,jt.job_type, CASE WHEN c.nickname is null or c.nickname ='' THEN  concat(c.fname,' ',c.lname) ELSE c.nickname END as cname, 
                    CASE WHEN u.lname is null or u.lname ='' THEN  u.fname   ELSE concat(u.lname,',',substr(u.fname,1,1)) end as sname,
                     j.stage_num,case when st.stage is null then '' else st.stage end as stagename,st.class,datediff(CURDATE(),j.timestamp) as Agedays,
                   datediff(CURDATE(),j.stage_date) as Stageage ,st.duration, 
                   CASE WHEN sh.status_id IS NOT NULL AND (sh.expires IS NULL OR sh.expires > CURDATE()) THEN 'clsHold' ELSE '' END AS IsHold,
                    case when (SELECT count(*)  FROM repairs r, fail_types ft WHERE r.job_id = j.job_id  AND r.fail_type = ft.fail_type_id ORDER BY r.timestamp DESC)=0 then '' else ',REPAIR' end as Repairs,
                    s.status_id, s.status, sh.timestamp, s.color, case when sh.expires is not null then DATE_FORMAT(sh.expires,'%m/%d/%y') else '' end as status_hold_expires
                 
                    from jobs j 
                    left join customers c on (j.customer_id=c.customer_id)
                    LEFT JOIN job_type jt ON (j.job_type = jt.job_type_id)
                    LEFT JOIN jurisdiction jur ON (j.jurisdiction = jur.jurisdiction_id)
                    LEFT JOIN permits p ON (j.job_id = p.job_id)
                    LEFT JOIN status_holds sh ON (sh.job_id = j.job_id)
                    LEFT JOIN status s ON (s.status_id = sh.status_id)
                    LEFT JOIN stages st ON (st.stage_num = j.stage_num and st.account_id = ".$_SESSION['ao_accountid'].")
                    LEFT JOIN repairs r ON (r.job_id = j.job_id AND r.completed IS NULL)
                    LEFT JOIN subscribers sb ON (sb.job_id = j.job_id)
                    LEFT JOIN users u ON (u.user_id = j.salesman)
                    LEFT JOIN tasks t ON (t.job_id = j.job_id)
                    LEFT JOIN task_type tt ON (tt.task_type_id = t.task_type)
                    LEFT JOIN canvassers cv ON (cv.job_id = j.job_id)  
                   
                    where j.job_id IN (" . implode(',', array_map('intval', $jobids)) . ") order by field(j.job_id,". implode(',', array_map('intval', $jobids)) .")";
              
                  $joblist=  DBUtil::queryToArray($sql); 
                    
                  //  ,case when sh.expires is null then CURDATE() else strtotime(sh.expires) as expirationDate
                  return json_encode(array('access_tocken'=>$token,'joblist'=> $joblist,'totalJobs'=>$totalJobs,'page'=>$req_page,'limit'=>$limit)); 
    
     
        
    }

    // Bindtabpagedetials  - 08-12-2021
	function Bindtabpagedetials()
	{
  	    $user_data = [];
        $token = '';
        if(isset($_REQUEST['access_tocken'])) 
        {
          $token = trim($_REQUEST['access_tocken']);
          $sql="SELECT * FROM access_tocken WHERE token='$token' AND status = 'in'";
          $token_arr = DBUtil::queryToArray($sql);
          //echo "<pre>";print_r($sql);die;
          if(!empty($token_arr))
          {
            $sql="SELECT t2.user_id,t2.account_id,t2.level FROM access_tocken as t1 
            JOIN users as t2 ON t2.user_id=t1.user_id
            WHERE t1.token='$token' AND t1.status = 'in'";
            $token_arr = DBUtil::queryToArray($sql);
            $user_data = $token_arr[0];
          }
          else
          {
            return json_encode(array("message" => "Invalid Token!",'status'=> 0));
          }
        }
        else
        {
            return json_encode(array("message" => "Bad Request!",'status'=> 0));
        }
    
        $_SESSION['ao_accountid'] = $user_data['account_id'];
        $_SESSION['ao_userid'] = $user_data['user_id'];
        $_SESSION['ao_level'] = $user_data['level'];
    
        $JobId= $_REQUEST['JobId'];   

        $sql = "SELECT * FROM jobs where job_id=".$JobId;     
        $result = DBUtil::fetchdata($sql);
        if(mysqli_num_rows($result))
        {
            $job=DBUtil::convertResultsToArray($result);
            $myJob=$job[0];
        
  		    $stages = StageModel::getJobStages($JobId);
        
            $sql = "SELECT stage_id, stage_num, stage FROM stages
                    WHERE account_id=".$myJob['account_id']."
                    GROUP BY stage ORDER BY order_num ASC";
      		  $stg = DBUtil::fetchdata($sql);
            $stages=array();
            if(mysqli_num_rows($stg))
            { 
              $stages=DBUtil::convertResultsToArray($stg); 
            }       	
            
            //$jobActions = JobUtil::getActions();   
            $numStages = count($stages)-1;   
            
            $stage = DBUtil::fetchdata('select order_num from stages where stage_num='.$myJob['stage_num']);
            $stage_order=0;
            if(mysqli_num_rows($stg))
            { 
              $stage=DBUtil::convertResultsToArray($stage); 
              foreach($stage as $row) {
                  $stage_order=$row['order_num'];
              }
            }  
            if($numStages>0){
                $percentage = floor(($stage_order / $numStages) * 100);    
            }else
                $percentage=0;
    
      	    $percentage = $percentage > 100 ? 100 : $percentage;
            
        	$stagedrd=[];
            if(!defined('NEW_JOB_ACTIONS') && ModuleUtil::checkAccess('full_job_stage_access')) 
            {
              reset($stages);
              foreach($stages as &$stage) {
                if(stageAdvanceAccess($stage['stage_num'])) {
                  $stage_name = StageModel::getStageNameById($stage['stage_id']);
                  $stagedrd[$stage['stage_num']]=$stage_name;
                }
              }
            }  	
  		
    		if($myJob['pif_date'] == '') 
            {
    		  $iconStr = "<img src='" . ROOT_DIR . "/images/icons/dollar_grey_16.png'>";
         
    		  if(ModuleUtil::checkAccess('mark_paid') || (moduleOwnership('mark_paid') && (JobUtil::isSubscriber($myJob['job_id']) || $myJob['salesman_id'] == $_SESSION['ao_userid'] || $myJob['user_id'] == $_SESSION['ao_userid'])))
    		   $iconStr = "<a onmouseover='this.style.cursor=\"pointer\";' id='lnkpaidjob'><img title='Mark Paid' src='" . ROOT_DIR . "/images/icons/dollar_grey_16.png' tooltip></a>";
    		   $iconStr =$iconStr. 'Job Not Paid';
    		}
    		else
    		{
    		  $iconStr = "<img src='" . ROOT_DIR . "/images/icons/dollar_32.png'>";
    		  if((ModuleUtil::checkAccess('mark_paid') || (moduleOwnership('mark_paid') && (JobUtil::isSubscriber($myJob['job_id']) || $myJob->salesman_id == $_SESSION['ao_userid'] || $myJob['user_id'] == $_SESSION['ao_userid']))))
    		  $iconStr ="<a onmouseover='this.style.cursor=\"pointer\";' id='lnkunpaidjob'><img title='Mark Unpaid' src='" . ROOT_DIR . "/images/icons/dollar_32.png' tooltip></a>";
    		  $iconStr =$iconStr.'<span class="paidtext">Job Paid in Full ';
    		  $iconStr =$iconStr.'<span class="smallnote">on '. DateUtil::formatDate($myJob['pif_date']).'</span></span>';
    		}        

    		if(!JobUtil::jobIsBookmarked($myJob['job_id'])) {
    			$bookmarkLinkText = 'Bookmark';
    		}
    		else {
    			$bookmarkLinkText = 'Remove Bookmark';    			
    		}
    		
    		$menustring=[];
    		$numLists = 1;
    		//$numJobAction = count($jobActions);
        
    		if(ModuleUtil::checkJobModuleAccess('modify_job', $myJob)) 
            {                      
                $jobActions = JobUtil::getActions();
                //echo "<pre>";print_r($jobActions);die;
                foreach($jobActions as $i => $jobAction) {
                  if(ModuleUtil::checkJobModuleAccess(MapUtil::get($jobAction, 'hook'), $myJob)) {
                      $menu = MapUtil::get($jobAction, 'action');
                      $menustring[]=$menu;                  
                  }
                }         
            }
            //print_r($stages);exit;   
            return (json_encode(array('access_tocken'=>$token,'myJob'=>array($myJob),'percentage'=>$percentage,'stages'=>$stages,'iconStr'=>$iconStr,'stagedrd'=>$stagedrd,'bookmarkLinkText'=> $bookmarkLinkText,'jobmenus'=>$menustring)));

      }		  
		 
}

    // Bindtabpagedetials  - 09-12-2021
    function Bindjobdetailssection()
    {
	    $user_data = [];
        $token = '';
        if(isset($_REQUEST['access_tocken'])) 
        {
            $token = trim($_REQUEST['access_tocken']);
            $sql="SELECT * FROM access_tocken WHERE token='$token' AND status = 'in'";
            $token_arr = DBUtil::queryToArray($sql);
            //echo "<pre>";print_r($sql);die;
            if(!empty($token_arr))
            {
              $sql="SELECT t2.user_id,t2.account_id,t2.level FROM access_tocken as t1 
              JOIN users as t2 ON t2.user_id=t1.user_id
              WHERE t1.token='$token' AND t1.status = 'in'";
              $token_arr = DBUtil::queryToArray($sql);
              $user_data = $token_arr[0];
            }
            else
            {
              return json_encode(array("message" => "Invalid Token!",'status'=> 0));
            }
        }
        else
        {
            return json_encode(array("message" => "Bad Request!",'status'=> 0));
        }

        $_SESSION['ao_accountid'] = $user_data['account_id'];
        $_SESSION['ao_userid'] = $user_data['user_id'];
        $_SESSION['ao_level'] = $user_data['level'];

        $JobId= $_REQUEST['JobId']; 
  
    	$myJob=new Job($JobId);
       
        $stage_age = $myJob->getStageAge();
    	$age_days=$myJob->getAgeDays();
        $diff = $stage_age - $myJob->duration;
        $nextstage=$myJob->getNextStages();
        $nextstageReq=$myJob->getNextStageReqs();
    	$stage_name=$myJob->getCSVStages(); 
    	$myCustomer = new Customer($myJob->customer_id);
	 
	 

        $customername=$myCustomer->getDisplayName();
        //  $map_url = "http://maps.yahoo.com/maps_result?addr={$myCustomer->get("address")}&csz={$myCustomer->get("city")}+{$myCustomer->get("state")}+{$myCustomer->get("zip")}";
        $map_url = "https://www.google.co.in/maps/place/{$myCustomer->get("address")}+{$myCustomer->get("city")}+{$myCustomer->get("state")}+{$myCustomer->get("zip")}";
    	$custaddress= $myCustomer->get("address").'<br/>';
    	$custaddress=$custaddress.$myCustomer->get("city").' '.$custaddress= $myCustomer->get("state").' '.$custaddress= $myCustomer->get("zip").'<br/>';
    	$string1 = UIUtil::formatPhone($myCustomer->get('phone'));
        $string2 = UIUtil::formatPhone($myCustomer->get('phone2'));                    
        
    	$custphoneno = '<a class="lnkdialphone" data-phone="'.$myCustomer->get('phone').'">'.$string1.'</a>'; 
    	$custphoneno2 ='';
    	if($string2 !='')
    		$custphoneno2 = '<a class="lnkdialphone" data-phone="'.$myCustomer->get('phone2').'">'.$string2.'</a>'; 
    	if($custphoneno2 !='')
    	{
    		$custphoneno=$custphoneno.','.$custphoneno2;
    	}
    
      
    	 
     	$custemail=$myCustomer->get("email");
    	
    	$customers = CustomerModel::getAllCustomers(UIUtil::getFirstLast());
    	$drdcustomers='<select id="drdjobcustomers" style="display:none;">';
    	foreach($customers as $customer) {
    			$isSelected= $myJob->customer_id == $customer['customer_id'] ? 'selected' : '';
                $drdcustomers=$drdcustomers.'<option value="'.$customer['customer_id'].'" '.$isSelected.' >'.$customer['select_label'].'</option>';
    	}
    	$drdcustomers=$drdcustomers.'</select>';
    
    	$drdorigins='<select id="drdjoborigin" style="display:none;">';
    	$origins = JobUtil::getAllOrigins();
    	foreach($origins as $origin) {
    		$isSelected= $myJob->origin_id == $origin['origin_id'] ? 'selected' : '';
    		$drdorigins=$drdorigins.'<option value="'.$origin['origin_id'].'" '.$isSelected.' >'.$origin['origin'].'</option>';
    	}
    	$drdorigins=$drdorigins.'</select>';
    	$caneditcustomer=ModuleUtil::checkJobModuleAccess("assign_job_customer", $myJob)?$drdcustomers.'<input type="button" class="inline_edit_btn"  value="" id="lnkeditjobcust">':'';
    	$lnkeditjobno=ModuleUtil::checkJobModuleAccess("modify_job_number", $myJob)?'<input type="text" id="txtjobno" value="'.$myJob->job_number.'" style="display:none;" class="form_input form-control validation validate[required[Job Number Required]],validate[funcCall[validatetext[Invalid Job Number]]],validate[length[6,15]]"><input type="button" class="inline_edit_btn" value="" , id="lnkeditjobno">':'';
    	$lnkeditjoborigin=ModuleUtil::checkJobModuleAccess("modify_job_origin", $myJob)?$drdorigins.'<input type="button" class="inline_edit_btn" value="" id="lnkeditjoborigin">':'';
    	$lnkeditjobreferal=ModuleUtil::checkJobModuleAccess("assign_job_referral", $myJob)?'<input type="button" class="inline_edit_btn" value="" id="lnkeditjobreferal">':'';
    	$canvasername=UserUtil::getDisplayName($myJob->get('canvasser_id'));
    	
    	
    	$drdcanvaser='<select  id="drdjobcanvaser"  style="display:none;"><option value="" >';
     
    	$showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');
    	$dropdownUserLevels = AccountModel::getMetaValue('assign_job_canvasser_user_dropdown');
    	$canvassers = !empty($dropdownUserLevels) 
                    ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast)
                    : UserModel::getAll($showInactiveUsers, $firstLast);
    	foreach($canvassers as $canvasser) {
    		$isSelected= $myJob->get('canvasser_id') == $canvasser['user_id'] ? 'selected' : '';
            $drdcanvaser=$drdcanvaser.'<option value="'.$canvasser['user_id'].'" '.$isSelected.'>'.$canvasser['select_label'].'</option>';
    	}
     
        $drdcanvaser=$drdcanvaser.'</select>';
    	$lnkeditjobcanvaser=ModuleUtil::checkJobModuleAccess("assign_job_canvasser", $myJob)?$drdcanvaser.'<input type="button" class="inline_edit_btn" value="" id="lnkeditjobcanvaser">':'';
    	$lnkeditjobsalesman=ModuleUtil::checkJobModuleAccess("assign_job_salesman", $myJob)?'<input type="button" fsalesmenId='.$myJob->salesman_id.' class="inline_edit_btn" value="" id="lnkeditjobsalesman">':'';
    	$defaultDate = $myJob->timestamp ?: DateUtil::formatMySQLDate();
    	$dt = new DateTime($defaultDate);
    	$createdate = $dt->format('Y-m-d');
    	$lnkeditjobdate=ModuleUtil::checkJobModuleAccess("edit_job_date", $myJob)?'<input type="text" class="form-control validation validate[required[Created Date Required]], datestamp  validate[funcCall[YYMMddDateFormat[Invalid Date Format]]]"  value="'.$createdate.'" style="display:none;" id="txtjobcreatedate" maxlength="10"><input type="button" value="" class="inline_edit_btn" id="lnkeditjobdate">':'';
    	
    	$drdjobjuridiction=$drdjobjuridiction.'<select  id="drdjobjuridiction"  style="display:none;"><option value="" >';
    		$jurisdictions = CustomerModel::getAllJurisdictions();
    		
    		foreach($jurisdictions as $jurisdiction){
    		$isSelected= $myJob->jurisdiction_id == $jurisdiction['jurisdiction_id'] ? 'selected' : '';
    			$drdjobjuridiction=$drdjobjuridiction.'<option value="'.$jurisdiction['jurisdiction_id'].'" '.$isSelected.'>'.$jurisdiction['location'].'</option>';
    		}
    	$drdjobjuridiction=$drdjobjuridiction.'</select>';
    	
     	$lnkeditjobjuridiction=ModuleUtil::checkJobModuleAccess("assign_job_jurisdiction", $myJob)?$drdjobjuridiction.'<input type="button" class="inline_edit_btn" value="" id="lnkeditjobjuridiction">':'';
    
    	$lnkeditjobpermit=ModuleUtil::checkJobModuleAccess("assign_job_permit", $myJob)?'<input type="text" class="form-control validation validate[funcCall[Onlydigitsex0[Enter numbers only]]]"  value="'.$myJob->permit.'" style="display:none;" id="txtjobpermit" maxlength="10"><input type="button" value="" class="inline_edit_btn" id="lnkeditjobpermit">':'';
    	$lnkeditjobtype=ModuleUtil::checkJobModuleAccess("modify_job_type", $myJob)?'<input type="button" value="" class="inline_edit_btn" id="lnkeditjobtype">':'';
    	$lnkeditjobinsurance=ModuleUtil::checkJobModuleAccess("modify_insurance", $myJob)?'<input type="button" value="" class="inline_edit_btn lnkeditjobinsurance" >':'';
    	 
    	$lnkeditjobwarranty=ModuleUtil::checkJobModuleAccess("assign_job_warranty", $myJob)?'<input type="button" value="" class="inline_edit_btn" id="lnkeditjobwarranty">':'';
    		
    	 
    	$materialSheets = $myJob->fetchMaterialSheets();
    	
    	$repairs = '';
    	if(empty($myJob) || get_class($myJob) !== 'Job') { return; }
    
    		$lstrepairs = $myJob->fetchRepairs();
    		 
    	foreach($lstrepairs as $repair) {
    		$isCompleted = !empty($repair['completed'])? ' line-through' : '';
    		$repairTooltip = $isCompleted ? 'Completed ' . DateUtil::formatDate($repair['completed']) . '. Click to view task details.' : 'View repair details';
    		$schedule = !empty($repair['startdate']) ? ' - ' . DateUtil::getScheduleWeekLink($repair['startdate']) : '';
    		$repairs=$repairs.'<li><i class="addquality"></i>&nbsp;<a class= "red '.$isCompleted.'"  href=get_repair.html?id='.$repair['repair_id'].'&JId='.$myJob->job_id.'&srcPage=schedule.html  style="text-decoration:underline;">'.$repair['fail_type'].'</a><span class="smallnote">'.$schedule.'</span></li>';
    	}
    	
    
    	 $appointments = '';
    	$lstappointments = $myJob->fetchAppointments();
    	foreach($lstappointments as $appointment) {
    		$appointments=$appointments.'<li><i class="icon-calendar"></i>&nbsp;<a href=get_appointment.html?id='.$appointment['appointment_id'].'&JId='.$myJob->job_id.'  style="text-decoration:underline;">'.$appointment['title'].'</a><span class="smallnote">-'.DateUtil::getScheduleWeekLink($appointment['datetime']).'</span></li>';
		}


		$tasks='';
		$lsttasks = $myJob->fetchTasks();
		foreach($lsttasks as $task) {
			$isCompleted = !empty($task['completed'])? 'class=line-through' : '';
			$isPaid = !empty($task['paid']);
			$tasks=$tasks.'<li class="'.UIUtil::getContrast($task['color']).'" style="background-color: '.$task['color'].';">';
			if($isCompleted) {
				$actionClass = 'action';
				$iconClass = 'light-gray';
				$rel = 'mark-paid';
				$paidTooltip = 'Click to mark paid';
				$taskTooltip = 'Completed ' . DateUtil::formatDate($task['completed']) . '. Click to view task details.';
				if($isPaid) {
					$iconClass = 'green ';
					$rel = 'undo-mark-paid';
					$paidTooltip = 'Paid ' . DateUtil::formatDate($task['paid']) . '. Click to undo mark paid.';
				}
    			if(!ModuleUtil::checkJobModuleAccess('edit_job_task', $myJob)) {
					$actionClass = '';
					$rel = '';
					$paidTooltip = '';
				}
				 $tasks=$tasks.'<i  data-taskid="'.$task['task_id'].'"  class="lnkchangetaskpaidstatus icon-usd  '.$iconClass.$actionClass.'" title="'.$paidTooltip.'" tooltip></i>&nbsp;';

			}
			$smallnote=!empty($task['start_date']) ? DateUtil::getScheduleWeekLink($task['start_date']) : 'Not Scheduled';
			$completedDt =!empty($task['completed']) ? DateUtil::getScheduleWeekLink($task['completed']) : '';
			
			if($completedDt != '' )
			{
				$completedDt = "  -   Completed on $completedDt";
			}

			    $tasks=$tasks.'<a '.$isCompleted. ' href=get_task.html?Id='.$task['task_id'].'&JId='.$myJob->job_id.'   style="text-decoration:underline;">'.$task['task'].'</a><span class=smallnote> - '.$smallnote.' '.$completedDt.'</span></li>';
		}
			$warranties='';
			 
			if(!empty($myJob->meta_data['job_warranty'])) {
				$warranties_array = JobUtil::getAllWarranties();
				$warranty_label = $warranties_array[$myJob->meta_data['job_warranty']['meta_value']]['label'];
				$warranty_color = $warranties_array[$myJob->meta_data['job_warranty']['meta_value']]['color'];
	
				$warranties=$warranties.'<li><i class="icon-star" style="color:'.$warranty_color.';"></i>&nbsp';

				if(!empty($myJob->meta_data['job_warranty_processed'])) {
					$warranties=$warranties.'<i class="icon-ok green" title="Processed '.DateUtil::formatDate($myJob->meta_data['job_warranty_processed']['meta_value']).'" tooltip></i>&nbsp;';
				}
			      $warranties=$warranties.$warranty_label;
				 
					$warranties=$warranties.'</li>';
			}
	  
	return (json_encode(array('access_tocken'=>$token,'myJob'=>array($myJob),'stage_name'=>$stage_name,'stage_age'=>$stage_age,'diff'=>$diff,'nextstage'=>$nextstage,'nextstageReq'=>$nextstageReq,'customername'=>$customername,'map_url'=>$map_url,
   'custaddress'=>$custaddress,'phonestring1'=>$string1,'phonestring2'=>$string2,'custphoneno'=>$custphoneno,'custphoneno2'=>$custphoneno2,'custemail'=>$custemail,
   'caneditcustomer'=>$caneditcustomer,'lnkeditjobno'=>$lnkeditjobno,'lnkeditjoborigin'=>$lnkeditjoborigin,'lnkeditjobreferal'=>$lnkeditjobreferal,'canvasername'=>$canvasername,
   'lnkeditjobcanvaser'=>$lnkeditjobcanvaser,'lnkeditjobsalesman'=>$lnkeditjobsalesman,'lnkeditjobdate'=>$lnkeditjobdate,'lnkeditjobjuridiction'=>$lnkeditjobjuridiction,
   'lnkeditjobpermit'=>$lnkeditjobpermit,'lnkeditjobtype'=>$lnkeditjobtype,'lnkeditjobinsurance'=>$lnkeditjobinsurance,'lnkeditjobwarranty'=>$lnkeditjobwarranty,
   'materialSheets'=>$materialSheets,'repairs'=>$repairs
   ,'appointments'=>$appointments,'tasks'=>$tasks,'warranties'=>$warranties,'drdcustomers'=>$drdcustomers,'age_days'=>$age_days)));
  
 }
    
    
    //Get RepairList for Dashboard    - 10-12-2021
    function GetRepairList()
    {        
        $user_data = [];
        $token = '';
        if(isset($_REQUEST['access_tocken'])) 
        {
            $token = trim($_REQUEST['access_tocken']);
            $sql="SELECT * FROM access_tocken WHERE token='$token' AND status = 'in'";
            $token_arr = DBUtil::queryToArray($sql);
            //echo "<pre>";print_r($sql);die;
            if(!empty($token_arr))
            {
              $sql="SELECT t2.user_id,t2.account_id,t2.level FROM access_tocken as t1 
              JOIN users as t2 ON t2.user_id=t1.user_id
              WHERE t1.token='$token' AND t1.status = 'in'";
              $token_arr = DBUtil::queryToArray($sql);
              $user_data = $token_arr[0];
            }
            else
            {
              return json_encode(array("message" => "Invalid Token!",'status'=> 0));
            }
        }
        else
        {
            return json_encode(array("message" => "Bad Request!",'status'=> 0));
        }

        $_SESSION['ao_accountid'] = $user_data['account_id'];
        $_SESSION['ao_userid'] = $user_data['user_id'];
        $_SESSION['ao_level'] = $user_data['level'];
        
    	//UserModel::isAuthenticated();
    	if(viewWidget('widget_today')) 
        {
      		  $accountid = $_SESSION['ao_accountid'];
      		  $UserID= $_SESSION['ao_userid'];
          
			  $loginuserid = $UserID;
			  $loginaccountid = $accountid;
			  $todayDate = date("Y-m-d");
                
              $firstLast = UIUtil::getFirstLast();
			 
			  $repairsArray =  ScheduleUtil::getRepairs("$todayDate", "$loginuserid", "$loginaccountid");
			  $tasksArray = ScheduleUtil::getTasks("$todayDate", "$loginuserid", "", "$loginaccountid");
			  $eventsArray = ScheduleUtil::getEvents("$todayDate", "$loginuserid", "$loginaccountid");
			  foreach($eventsArray as &$origin) {
				$origin['time']= $origin['all_day'] ? 'All day' : DateUtil::formatTime($event['date']);
			  }

			  $appointmentsArray = ScheduleUtil::getAppointments("$todayDate", "$loginuserid", "$loginaccountid");
			  foreach($appointmentsArray as &$origin) {
					$origin['time']= DateUtil::formatTime($origin['date']);
			  }
			  $deliveriesArray = ScheduleUtil::getDeliveries("$todayDate", "$loginuserid", "$loginaccountid");
               
			  return json_encode(array("access_tocken"=>$token,"firstLast"=>$firstLast,"status"=>1,"repairsArray" => $repairsArray, "tasksArray" => $tasksArray, "eventsArray" => $eventsArray, "appointmentsArray" => $appointmentsArray, "deliveriesArray" => $deliveriesArray));
  		}
  	    else
  		{
  		    return json_encode(array("access_tocken"=>$token,"status"=>0));
  		}
   }
  
    //  Display Output Message   - 01-12-2021
    function displayOutput($response){

        header('Content-Type: application/json');
        echo json_encode($response);
        exit(0);
		
    }
    
    /*
    Author : Sofikul 
    Date : 19.10.2021
    */
    function add_appointment()
    {
        $ap=json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            if(empty($ap['job_id'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Job Id is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['user_id'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'User Id is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['date'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Schedule date is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['time'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Schedule time is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['title'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Appointment Title is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['description'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Description  is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		
    		$JobId = $ap['job_id'];
    		$userid = $ap['user_id'];
    		$date = $ap['date'];
    		$time = $ap['time'];
    		$title = $ap['title'];
    		$description = $ap['description'];
    		$todayDate = date("Y-m-d h:m:s");
    		$datetime = DateUtil::formatMySQLTimestamp("$date $time");
    		
    		$sql = "INSERT INTO appointments VALUES(NULL, '$userid', '$JobId', '$datetime', '$title', '$description', '$todayDate')";
    	
    		$result = DBUtil::query($sql);
    		
            if($result == true)
            {
                $sql1 = "SELECT * FROM appointments WHERE user_id = ".$userid." AND job_id = ".$JobId." AND title = '".$title."'";
               
                $result = DBUtil::queryToArray($sql1);
                $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=> $result));
                displayOutput($response);
            }
    		else
    		{
    			$response=array('status'=>array('error_code'=>0,'message'=>'There are some error! , Unable to add Appointment'),'result'=>array('data_list'=>'')); 
                displayOutput($response);
    		}
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
            
        
        
    }
    
     /*
    Author : Sofikul 
    Date : 21.10.2021
    */
    function assign_salesman()
    {
        $ap=json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap)){
            
            if(empty($ap['job_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Job Id is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			if(empty($ap['salesman_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Salesman Id is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			$sql = "UPDATE jobs SET salesman ='".$ap['salesman_id']."' WHERE job_id = '".$ap['job_id']."'";
			
            if(DBUtil::query($sql))
            {
                $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>''));
                displayOutput($response);
            } else {
                $response=array('status'=>array('error_code'=>1,'message'=>'Failed'),'result'=>array('data_list'=>'')); 
                displayOutput($response);
            }
            
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    
    
     /*
    Author : Sofikul 
    Date : 25.10.2021
    */
    function assign_customer()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap)){
            
            if(empty($ap['job_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Job Id is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			if(empty($ap['customer_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Customer Id is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			$sql = "UPDATE jobs SET customer_id ='".$ap['customer_id']."' WHERE job_id = '".$ap['job_id']."'";
			//echo $sql;die;
            if(DBUtil::query($sql))
            {
                $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>''));
                displayOutput($response);
            } else {
                $response=array('status'=>array('error_code'=>1,'message'=>'Failed'),'result'=>array('data_list'=>'')); 
                displayOutput($response);
            }
            
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    
    
     /*
    Author : Sofikul 
    Date : 27.10.2021
    */
    function contact_header()
    {
        $ap=json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap)){
            
            if(empty($ap['user_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'User Id is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			$user_id = $ap['user_id'];
            $sql = "SELECT account_id FROM users  WHERE user_id = $user_id";
            $user = DBUtil::queryToArray($sql);
			
            $account_id = $user[0]['account_id'];
            
            $sql = "SELECT contact_header_id,contact_name FROM contacts
                    WHERE account_id = $account_id
                    ORDER BY contact_name asc";
            $contactheder = DBUtil::queryToArray($sql);
            
            if(!empty($contactheder)){
                $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$contactheder));
                displayOutput($response);
            } else {
               $response=array('status'=>array('error_code'=>0,'message'=>'Contact Header List Not Found.'),'result'=>array('data_list'=>'')); 
               displayOutput($response);
            }
        
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    
    /*
    Author : Sofikul 
    Date : 28.10.2021
    */
    function job_contacts_list()
    {
        $ap=json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            if(empty($ap['job_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Job Id is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
            $job_id = $ap['job_id'];
            $sql = "SELECT job_contacts.*,contact_name as contact_header,fname,lname
                    FROM job_contacts
                    JOIN contacts ON contacts.contact_header_id = job_contacts.contact_header_id
                    LEFT JOIN users ON users.user_id = job_contacts.created_by
                    WHERE job_id = $job_id
                    ORDER BY created_at desc";
            $contact_list =  DBUtil::queryToArray($sql);
            
            if(!empty($contact_list)){
                $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$contact_list));
                displayOutput($response);
            } else {
               $response=array('status'=>array('error_code'=>0,'message'=>'Job Contact List Not Found.'),'result'=>array('data_list'=>'')); 
               displayOutput($response);
            }
            
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
    }
    
    
    /*
    Author : Sofikul 
    Date : 01.11.2021
    */
    function add_job_contacts()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            $user_id = $ap['user_id'];
            $job_id = $ap['job_id'];
                
            $sql = "select * from jobs where job_id = '$job_id' limit 1";
            $myJob = DBUtil::queryToArray($sql);
            
            $myJob = $myJob[0];
                
            if(!empty($ap['contact_note']) && !empty($ap['contact_header_id']))
            {
                $contact_header = mysqli_real_escape_string(DBUtil::Dbcont(),$ap['contact_header_id']);
                $contact_note = mysqli_real_escape_string(DBUtil::Dbcont(),$ap['contact_note']);
                $sql = "insert into job_contacts (job_id,contact_header_id,contact_note,created_by)  VALUES ('$job_id','$contact_header','$contact_note','{$user_id}')";
                
                $status = DBUtil::query($sql);
                if($status)
                {
                    if(!empty($_REQUEST['send_conractor']))
                    {
                        $sql = "select fname,lname from customers where customer_id = '{$myJob['customer_id']}' limit 1";
                        $res = DBUtil::query($sql);
                        $custom = mysqli_fetch_row($res);
                        $data['message']='New Contact information has been posted  for job number <b>'.$myJob['job_number'].'</b> (Customer: <b>'.$custom[1].' '.$custom[0].'</b>)<br><br> Contact Note: '.$contact_note.'<br><br><br><a href="'.ACCOUNT_URL.'/?p=jobs&id='.$job_id.'">'.ACCOUNT_URL.'/?p=jobs&id='.$job_id.'</a>';
                          
                        NotifyUtil::emailFromTemplate('contact_note', $myJob['salesman_id'],'',$data);
                    }
                    
                    $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>""));
                    displayOutput($response);
                
                } 
                else 
                {
                   $response=array('status'=>array('error_code'=>0,'message'=>'Unable to add contact note.'),'result'=>array('data_list'=>'')); 
                   displayOutput($response);
                }
                
            }
            else 
            {
               $response=array('status'=>array('error_code'=>0,'message'=>'Mandatory data not submitted.'),'result'=>array('data_list'=>'')); 
               displayOutput($response);
            }
          
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
      
    }
    
    /* 
    Author : Sofikul 
    Date : 03.11.2021
    */
    function add_task()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            if(empty($ap['job_id'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Job Id is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['user_id'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'User Id is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
            if(empty($ap['task_type'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Task type is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['stage'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Stage is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['duration'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Duration Title is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
           
    		$schedule = $ap['schedule'];

            if($schedule) {
                if(empty($ap['start_date'])){
        			$response=array('status'=>array('error_code'=>1,'message'=>'Start date is required'),'result'=>array('data_list'=>''));
        			displayOutput($response);
        		}
        		if(empty($ap['end_date'])){
        			$response=array('status'=>array('error_code'=>1,'message'=>'End date is required'),'result'=>array('data_list'=>''));
        			displayOutput($response);
        		}
            }
            
            $job_id = $ap['job_id'];
            
            $user_id = $ap['user_id'];
            $sql = "SELECT account_id FROM users  WHERE user_id = $user_id";
            $user = DBUtil::queryToArray($sql);
			
            $account_id = $user[0]['account_id'];
            
            
            $taskType = $ap['task_type'];
            $contractor = $ap['contractor'];
            $stage = $ap['stage'];
            $notes = mysqli_real_escape_string(DBUtil::Dbcont(),$ap['notes']);
            $duration = $ap['duration'];
            
            $startDt = $ap['start_date'];
            $endDt = $ap['end_date'];
            $startTime = $ap['start_time']; 
            $endTime = $ap['end_time'];
            $is_notify = $ap['is_notify'];
            $errors = array();			
             
            $startTimestamp = DateUtil::formatMySQLTimestamp("$startDt $startTime");
            $endTimestamp = DateUtil::formatMySQLTimestamp("$endDt $endTime");
            
            $taskTypesToAdd = array_merge(array($taskType), MapUtil::pluck(TaskUtil::getAutoCreateTasks($taskType), 'task_type_id'));
            
            foreach($taskTypesToAdd as $taskTypeToAdd) 
            {
    		
        	    $sql = "INSERT INTO tasks VALUES (NULL, '$taskTypeToAdd', '{$job_id}', '$stage', '{$user_id}', '{$account_id}', $contractor, '$startDt', '$notes', '$duration', NULL, NULL, now(),'$startTime','$endDt','$endTime')";
               
        		$result = DBUtil::query($sql);
        		
                if($result == true)
                {
                    $sql1 = "SELECT * FROM tasks WHERE job_id = ".$job_id;
                    $result = DBUtil::queryToArray($sql1);
                    $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=> $result));
                    displayOutput($response);
                }
        		else
        		{
        			$response=array('status'=>array('error_code'=>0,'message'=>'There are some error! , Unable to add Task'),'result'=>array('data_list'=>'')); 
                    displayOutput($response);
        		}
            }
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
            
        
        
    }
    
    /* 
    Author : Sofikul 
    Date : 08.11.2021
    */
    function add_repair()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            if(empty($ap['job_id'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Job Id is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['user_id'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'User Id is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['fail_type'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Fail Type is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['priority'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Priority is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['contractor'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Contractor Title is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		
    		
    		$job_id = $ap['job_id'];
    		$user_id = $ap['user_id'];
    		$fail_type = $ap['fail_type'];
    		$priority = $ap['priority'];
    		$contractor = $ap['contractor'];
    		$notes = $ap['notes'];
    		
    		
    		$sql = "SELECT account_id FROM users  WHERE user_id = $user_id";
            $user = DBUtil::queryToArray($sql);
            $account_id = $user[0]['account_id'];
            
    		
    		$sql = "INSERT INTO repairs VALUES (NULL, '{$job_id}', '{$account_id}', '{$user_id}', '$contractor', '$priority', '$fail_type', '$notes', NULL,  now(), NULL)";
            $result = DBUtil::query($sql);
    		
            if($result == true)
            {
                $sql1 = "SELECT * FROM repairs WHERE user_id = ".$user_id." AND job_id = ".$job_id;
                $result = DBUtil::queryToArray($sql1);
                $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=> $result));
                displayOutput($response);
            }
    		else
    		{
    			$response=array('status'=>array('error_code'=>0,'message'=>'There are some error! , Unable to add Repair'),'result'=>array('data_list'=>'')); 
                displayOutput($response);
    		}
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
            
        
        
    }
    
    /* 
    Author : Sofikul 
    Date : 09.11.2021
    */
    function invoice_list()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            if(empty($ap['job_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Job Id is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
            $job_id = $ap['job_id'];
            
            $sql = "SELECT *
                    FROM invoices
                    WHERE job_id = $job_id
                    ORDER BY invoice_id desc";
                    
            $invoice_list =  DBUtil::queryToArray($sql);
            
            if(!empty($invoice_list)){
                $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$invoice_list));
                displayOutput($response);
            } else {
               $response=array('status'=>array('error_code'=>0,'message'=>'Job Invoice Not Found.'),'result'=>array('data_list'=>'')); 
               displayOutput($response);
            }
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
    
    
      
    }
    
    /* 
    Author : Sofikul 
    Date : 01.12.2021
    */
    function getInvoiceDetails()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            if(empty($ap['inv_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Invoice Id is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			$inv_id = $ap['inv_id'];
            $sql = "SELECT * FROM invoices WHERE invoice_id = $inv_id";
            $invoice =  DBUtil::queryToArray($sql);
            
			
			$totalCharges = getInvoiceChargesTotal($inv_id);
            $totalCredits = getInvoiceCreditsTotal($inv_id);
            $credits = fetchCredits($inv_id);
            $charges = fetchCharges($inv_id);
            
            $balance = getInvoiceBalance($inv_id);
            
            
            $results = array('invoice'=>$invoice,'totalCharges'=>$totalCharges,'charges'=>$charges,'totalCredits'=>$totalCredits,'credits'=>$credits,'balance'=>$balance);
			
			$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$results));
            displayOutput($response);
			
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
    }
    
    
    /* 
    Author : Sofikul 
    Date : 10.11.2021
    */
    function generate_invoice()
    {
        
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            if(empty($ap['job_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Job Id is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			if(empty($ap['user_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'User Id is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			if(empty($ap['invoice_name'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Invoice name is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
            $job_id = $ap['job_id'];
            $user_id = $ap['user_id'];
            
            $account_id = getAccountFromUser($user_id);
            
			
			$maxinvoice = UserModel::gnerateInvoice($job_id);
            $no = ((count($maxinvoice)>0)?$maxinvoice[0]['invoice_id']:0)+1;
            $invoice_no="INV2".str_pad($no, 6, "0", STR_PAD_LEFT);
            
            $sql = "INSERT INTO invoices (invoice_no, invoice_name, job_id, user_id, timestamp) VALUES('{$invoice_no}','{$ap['invoice_name']}','{$job_id}', '{$user_id}', now())";
            $result = DBUtil::query($sql);
		
            $invoice_id = DBUtil::getInsertId();
    
            JobModel::saveEvent($job_id, 'Invoice Created With Invoice no '.$invoice_no,$user_id,$account_id);
			
            
			//echo "<pre>";print_r($invoice_list);die;
			
            if($result){
                $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>array('inv_id'=>$invoice_id,'inv_no'=>$invoice_no)));
                displayOutput($response);
            } else {
               $response=array('status'=>array('error_code'=>0,'message'=>'Failed to Generate Invoice no, Please try again'),'result'=>array('data_list'=>'')); 
               displayOutput($response);
            }
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
    
    
      
    }
    
    
    /* 
    Author : Sofikul 
    Date : 11.11.2021
    */
    function invoiceitems()
    {
        //
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            if(empty($ap['inv_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Invoice Id is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			if(empty($ap['item'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Item is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			if(empty($ap['invoice_name'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Invoice name is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			if(empty($ap['amount'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Amount is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			if(empty($ap['type'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Item type is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			$inv_id = $ap['inv_id'];
            $item_id = $ap['itemid'];
            $amount = $ap['amount'];
            $item = $ap['item'];
            $type = $ap['type']; 
            $invoice_name = $ap['invoice_name'];
            
			$sql = "SELECT * FROM invoices WHERE invoice_id = $inv_id";
            $invoice =  DBUtil::queryToArray($sql);
            $job_id = $invoice[0]['job_id'];
            
            
            $user_id = $invoice[0]['user_id'];
            $account_id = getAccountFromUser($user_id);
            
            
            if($invoice_name)
            {
                $sql = "UPDATE  invoices SET invoice_name='{$invoice_name}' WHERE invoice_id='{$inv_id}'";
                DBUtil::query($sql);
            }
            
            
            if(!empty($item_id))
            {
                $note = base64_decode($note) ;
                $old_type=RequestUtil::get('old_type');
        
                if($old_type!=$type)
                {
                    if($type == 'credit') 
                    {
                        $sql = "INSERT INTO credits VALUES (0, '{$inv_id}', '$amount', '$item', now())";
                        DBUtil::query($sql);
                        $sql = "DELETE FROM  charges WHERE charge_id='{$item_id}' LIMIT 1";
                        DBUtil::query($sql);
                    }
                    elseif($type == 'charge') 
                    {
                        $sql = "INSERT INTO charges VALUES (0, '{$inv_id}', '$amount', '$item', now())";
                        DBUtil::query($sql);
                        $sql = "DELETE FROM credits WHERE credit_id = '$item_id' LIMIT 1";
                        DBUtil::query($sql);
                    }
                }
                elseif($type == 'credit') 
                {
                    $sql = "UPDATE  credits SET amount='{$amount}',note='{$item}' WHERE credit_id='{$item_id}'";
                }
                elseif($type == 'charge') 
                {            
                    $sql = "UPDATE charges SET amount='{$amount}',note='{$item}' WHERE charge_id='{$item_id}'";
                }
                //echo $sql;die;
                if(DBUtil::query($sql) or die(mysqli_error)) {
                    JobModel::saveEvent($job_id, "Invoice $type Updated the amount to ($amount)",$user_id,$account_id);
                }
            }
            else
            {
                $sql = "";
                $note = base64_decode($item) ;
            	if($type == 'credit') {
            		$sql = "INSERT INTO credits VALUES (0, '{$inv_id}', '$amount', '$item', now())";
            	}
            	else
            	{
                    $sql = "INSERT INTO charges VALUES (0, '{$inv_id}', '$amount', '$item', now())";
            	}
            	
                if(DBUtil::query($sql) or die(mysqli_error)) {
                    JobModel::saveEvent($job_id, "Invoice $type added ($amount)",$user_id,$account_id);
                }
            }
            
			
			$totalCharges = getInvoiceChargesTotal($inv_id);
            $totalCredits = getInvoiceCreditsTotal($inv_id);
            $credits = fetchCredits($inv_id);
            $charges = fetchCharges($inv_id);
            
            $balance = getInvoiceBalance($inv_id);
            
            
            $results = array('invoice'=>$invoice,'totalCharges'=>$totalCharges,'charges'=>$charges,'totalCredits'=>$totalCredits,'credits'=>$credits,'balance'=>$balance);
			
			$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$results));
            displayOutput($response);
			
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
    }
    
    function  getAccountFromUser($user_id)
    {
        $sql = "SELECT account_id FROM users  WHERE user_id = $user_id";
        $user = DBUtil::queryToArray($sql);
        return $user[0]['account_id'];
    }
    
    function getInvoiceChargesTotal($invoice_id) {
        $sql = "SELECT SUM(ch.amount) as charges
                FROM invoices i
                JOIN charges ch ON ch.invoice_id = i.invoice_id
                WHERE  i.invoice_id='{$invoice_id}'";
            
        return DBUtil::fetchScalar(DBUtil::query($sql)) ?: 0;
    }
    
    function getInvoiceCreditsTotal($invoice_id) {
        $sql = "SELECT SUM(cr.amount) as credits
                FROM invoices i
                JOIN credits cr ON cr.invoice_id = i.invoice_id
                WHERE  i.invoice_id='{$invoice_id}'";
            
        return DBUtil::fetchScalar(DBUtil::query($sql)) ?: 0;
    }
    
    function fetchCredits($invoice_id) {
		$sql = "SELECT c.*
                FROM credits c
                JOIN invoices i ON i.invoice_id = c.invoice_id
                WHERE  i.invoice_id='{$invoice_id}'";
        $credits = DBUtil::queryToArray($sql);
        return $credits;
	}
	
	function fetchCharges($invoice_id) {
		$sql = "SELECT c.*
                FROM charges c
                JOIN invoices i ON i.invoice_id = c.invoice_id
                WHERE i.invoice_id='{$invoice_id}'";
                
        $charges = DBUtil::queryToArray($sql);
        return $charges;
	}
	
	function getInvoiceBalance($invoice_id) 
	{
        $charges = getInvoiceChargesTotal($invoice_id);
        $credits = getInvoiceCreditsTotal($invoice_id);
        return CurrencyUtil::formatUSD($charges - $credits);
    }

	
    
    
    /* 
    Author : Sofikul 
    Date : 15.11.2021
    */
    function invoiceprint()
    {
      
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            if(empty($ap['inv_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Invoice Id is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			$inv_id = $ap['inv_id'];
            
			$sql = "SELECT * FROM invoices WHERE invoice_id = $inv_id";
            $invoice =  DBUtil::queryToArray($sql);
            $job_id = $invoice[0]['job_id'];
            
            $sql = "select * from jobs where job_id = '$job_id' limit 1";
            $myJob = DBUtil::queryToArray($sql);
            
            $myJob = $myJob[0];
            
            $myCustomer = new Customer($myJob['customer_id']);
            $account_id = $myJob->record['account_id'];
            $header=array();
            $company = DBUtil::getRecord('accounts', $account_id);
            $address = '';
            if(!empty($company))
            {
                $header=$company;
                $address = (!empty($header['city']))?$header['city']:'';
                $address .= (!empty($header['state']))?', '.$header['state']:'';
                $address .= (!empty($header['zip']))?' '.$header['zip']:'';
            }
           
            $logo = (!empty($header['logo']))?'/invoice_logo/'.$header['logo']:'';
            
			
			$totalCharges = getInvoiceChargesTotal($inv_id);
            $totalCredits = getInvoiceCreditsTotal($inv_id);
            $credits = fetchCredits($inv_id);
            $charges = fetchCharges($inv_id);
            
            $balance = getInvoiceBalance($inv_id);
            
            $results = array('claim_number'=>$myJob->claim,'customer'=>$myCustomer,'logo'=>$logo,'invoice_no'=>$invoice[0]['invoice_no'],'totalCharges'=>$totalCharges,'charges'=>$charges,'totalCredits'=>$totalCredits,'credits'=>$credits,'balance'=>$balance);
			
			$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$results));
            displayOutput($response);
			
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
    }
    
    
    /* 
    Author : Sofikul 
    Date : 17.11.2021
    */
    function getCustomers()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            $user_id = $ap['user_id'];
            $account_id = getAccountFromUser($user_id);
            
            $sql = "SELECT c.customer_id, c.fname,c.lname, CASE WHEN c.nickname IS NULL THEN '' ELSE c.nickname END AS nickname
                  FROM customers c
                  JOIN users u ON u.user_id = c.user_id  AND c.account_Id = {$account_id} 
                  ORDER BY c.lname ASC"; 
                  
            $custlist =  DBUtil::queryToArray($sql);  
                  
			$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$custlist));
            displayOutput($response);
    	    
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    
    
    /* 
    Author : Sofikul 
    Date : 18.11.2021
    */
    function add_job()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            $existingCustomer = $ap['customer_id'];
            if(empty($existingCustomer))
            {
                $fname = $ap['fname'];
            	$lname = $ap['lname'];
            	$nickname = $ap['nickname'];
            	$address = $ap['address'];
            	$city = $ap['city'];
            	$state = $ap['state'];
            	$zip = $ap['zip'];
            	$cross = $ap['cross'];
            	$phone = StrUtil::formatPhoneToSave($ap['phone']);
            	$phone2 = StrUtil::formatPhoneToSave($ap['phone2']);
            	$email = $ap['email'];
            	
            	if(empty($fname)){
    				$response=array('status'=>array('error_code'=>1,'message'=>'First name is required'),'result'=>array('data_list'=>''));
    				displayOutput($response);
    			}
    			if(empty($address)){
    				$response=array('status'=>array('error_code'=>1,'message'=>'Address is required'),'result'=>array('data_list'=>''));
    				displayOutput($response);
    			}
    			if(empty($city)){
    				$response=array('status'=>array('error_code'=>1,'message'=>'City is required'),'result'=>array('data_list'=>''));
    				displayOutput($response);
    			}
    			
    			if(empty($zip)){
    				$response=array('status'=>array('error_code'=>1,'message'=>'Zip is required'),'result'=>array('data_list'=>''));
    				displayOutput($response);
    			}
    			if(strlen($zip) != 5 || !ctype_digit($zip)) {
    				$response=array('status'=>array('error_code'=>1,'message'=>'Zip incorrect format'),'result'=>array('data_list'=>''));
    				displayOutput($response);
    			}
    			
    			if(!empty($email) && !ValidateUtil::validateEmail($email)) {
    				$response=array('status'=>array('error_code'=>1,'message'=>'Email incorrect format'),'result'=>array('data_list'=>''));
    				displayOutput($response);
    			}
    			if(!empty($email) && UserModel::emailExists($email) && $email != UserModel::getProperty($_SESSION['ao_userid'], 'email')) {
    			    $response=array('status'=>array('error_code'=>1,'message'=>'Email is in use'),'result'=>array('data_list'=>''));
    				displayOutput($response);
    			}
    			
    			if((strlen($phone) != 10 || !ctype_digit($phone)) && !empty($phone)) {
    			    $response=array('status'=>array('error_code'=>1,'message'=>'Phone incorrect format'),'result'=>array('data_list'=>''));
    				displayOutput($response);
    			}
    			if((strlen($phone2) != 10 || !ctype_digit($phone2)) && !empty($phone2)) {
    			    $response=array('status'=>array('error_code'=>1,'message'=>'Secondary Phone incorrect format'),'result'=>array('data_list'=>''));
    				displayOutput($response);
    			}
    			
            	
    			$newAddress = "$address $city $state $zip";
    			$gpsData = CustomerModel::getGPSCoords($newAddress);
    			
    			$user_id = $ap['user_id'];
                $account_id = getAccountFromUser($user_id);
                
    			$sql = "INSERT INTO customers
                        VALUES (NULL, '{$account_id}', '$fname', '$lname', '$nickname', '{$user_id}',
                        '$address', '$city', '$state', '$zip', '{$gpsData[0]}', '{$gpsData[1]}', '$phone',
                        '$phone2', '$email', '$cross', now())";
              
    			DBUtil::query($sql);
    
    			$existingCustomer = DBUtil::getInsertId();
            }
			
        	$type = $ap['type'];
            if(empty($type))
            {
                $type=0;
            }
        	$note = $ap['note'];
        	$origin = $ap['origin'];
            if(empty($origin))
            {
                $origin=0;
            }
        
            $jurisdiction = $ap['jurisdiction'];
            if(empty($jurisdiction))
            {
                $jurisdiction=0;
            }
            
    		$jobHash = md5(mt_rand() . mt_rand() . mt_rand());
    
    		$salesman = 'NULL';
    		if($ap['salesman']) {
    			$salesman = $ap['salesman'];
            }
    
    		$provider = 'NULL';
    		if($ap['provider']) {
    			$provider = $ap['provider'];
            }
    
            $referral = 'NULL';
    		if($ap['referral']) {
    			$referral = $ap['referral'];
            }
    
    		$jobNumber = strtoupper(substr(md5(rand() . rand()), 0, 8));
    		
    		$user_id = $ap['user_id'];
            $account_id = getAccountFromUser($user_id);
            
    		$sql = "INSERT INTO jobs VALUES (NULL, '$jobNumber', '$existingCustomer', '{$account_id}', 1, curdate(), 
                        '{$user_id}', $salesman, $referral, NULL, $provider, NULL, NULL,NULL,NULL,NULL,NULL, 0, '$type',
                        '$note', '$origin', '$jurisdiction', NULL, now(), '$jobHash')";
         
    		DBUtil::query($sql);
    
    		
            $sql = "SELECT * FROM jobs WHERE job_number = '$jobNumber'";
            $results = DBUtil::queryToArray($sql);
            $record = $results[0]; 
            
            storeSnapshot($record,$user_id);
    
    		JobModel::saveEvent($record['job_id'], 'Added New Job',$user_id,$account_id);
    		
    		$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$record['job_id']));
            displayOutput($response);
            
    	    
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    
    
    function storeSnapshot($record,$user_id) 
    {
        $tableData = DBUtil::getTableFields('jobs');
        
        $fields = array();
        $values = array();
        foreach($tableData as $field) {
            $fieldName = MapUtil::get($field, 'Field');
            $nullable = MapUtil::get($field, 'Null') === 'YES';
            $fields[] = $fieldName;
            
            $value = MapUtil::get($record, $fieldName);
            if(empty($value) && $nullable) {
                $values[] = 'NULL';
            } else {
                $values[] = "'$value'";
            }
        }
        
        //add extra stuff...
        $fields[] = 'canvasser';
        $values[] = "0";
        // echo "<pre>";print_r($fields);
        // echo "<pre>";print_r($values);die;
     
        $sql = "INSERT INTO job_audits (" . implode(',', $fields) . ", audit_user_id, audit_timestamp)
                VALUES (" . implode(",", $values) . ", '{$user_id}', NOW())";
        //echo $sql;die;
        DBUtil::query($sql);
    }
    
    
    /* 
    Author : Sofikul 
    Date : 26.11.2021
    */
    function getOriginList()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            $user_id = $ap['user_id'];
            $account_id = getAccountFromUser($user_id);
            
            $sql = "SELECT * FROM origins c WHERE account_id = $account_id ORDER BY c.origin ASC"; 
                  
            $datalist =  DBUtil::queryToArray($sql);  
                  
            //echo "<pre>";print_r($datalist);die;
			$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$datalist));
            displayOutput($response);
    	    
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    
    /* 
    Author : Sofikul 
    Date : 26.11.2021
    */
    function getJobtypeList()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            $user_id = $ap['user_id'];
            $account_id = getAccountFromUser($user_id);
            
            $sql = "SELECT * FROM job_type c WHERE account_id = $account_id ORDER BY c.job_type ASC"; 
                  
            $datalist =  DBUtil::queryToArray($sql);  
                  
            //echo "<pre>";print_r($datalist);die;
			$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$datalist));
            displayOutput($response);
    	    
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    
    /* 
    Author : Sofikul 
    Date : 26.11.2021
    */
    function getReferralList()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            $user_id = $ap['user_id'];
            $account_id = getAccountFromUser($user_id);
            
            $sql = "SELECT user_id, concat(lname, ', ', fname) as select_label FROM users c WHERE account_id = $account_id ORDER BY select_label ASC"; 
                  
            $datalist =  DBUtil::queryToArray($sql);  
                  
            //echo "<pre>";print_r($datalist);die;
			$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$datalist));
            displayOutput($response);
    	    
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    
    
    /* 
    Author : Sofikul 
    Date : 26.11.2021
    */
    function getSalesmanList()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            $user_id = $ap['user_id'];
            $account_id = getAccountFromUser($user_id);
            
            $sql = "SELECT user_id, concat(lname, ', ', fname) as select_label FROM users c WHERE account_id = $account_id ORDER BY select_label ASC"; 
                  
            $datalist =  DBUtil::queryToArray($sql);  
                  
            //echo "<pre>";print_r($datalist);die;
			$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$datalist));
            displayOutput($response);
    	    
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    
    
    /* 
    Author : Sofikul 
    Date : 26.11.2021
    */
    function getProviderList()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            $user_id = $ap['user_id'];
            $account_id = getAccountFromUser($user_id);
            
            $sql = "SELECT * FROM insurance c WHERE account_id = $account_id ORDER BY c.insurance ASC"; 
                  
            $datalist =  DBUtil::queryToArray($sql);  
                  
            //echo "<pre>";print_r($datalist);die;
			$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$datalist));
            displayOutput($response);
    	    
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    
    
    /* 
    Author : Sofikul 
    Date : 26.11.2021
    */
    function getJurisdictionList()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            $user_id = $ap['user_id'];
            $account_id = getAccountFromUser($user_id);
            
            $sql = "SELECT * FROM jurisdiction c WHERE account_id = $account_id ORDER BY c.location ASC"; 
                  
            $datalist =  DBUtil::queryToArray($sql);  
                  
            //echo "<pre>";print_r($datalist);die;
			$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$datalist));
            displayOutput($response);
    	    
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    
    /* 
    Author : Sofikul 
    Date : 01.12.2021
    */
    function getStateList()
    {
        $states = getStates();
		$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$states));
        displayOutput($response);
    }
    
    
    /* 
    Author : Sofikul 
    Date : 29.11.2021
    */
    function getUserList()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
        if(sizeof($ap))
        {
            if(empty($ap['user_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'User id  is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
            $user_id = $ap['user_id'];
            $account_id = getAccountFromUser($user_id);
            
            $sql = "SELECT user_id, concat(lname, ', ', fname) as select_label FROM users c WHERE account_id = $account_id ORDER BY select_label ASC"; 
                  
            $datalist =  DBUtil::queryToArray($sql);  
                  
            //echo "<pre>";print_r($datalist);die;
			$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$datalist));
            displayOutput($response);
    	    
        }
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    
    /* 
    Author : Sofikul 
    Date : 03.11.2021
    */
    function uploadFile()
    {	 
       $ap = $_REQUEST;//json_decode(file_get_contents('php://input'), true);
	   if(sizeof($ap))
       {	  
            if(empty($ap['user_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'User id  is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			if(empty($ap['job_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Job id  is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			if(empty($ap['upload_file'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Upload file is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
			
		    $job_id = $ap['job_id'];  
		    
		    $user_id = $ap['user_id'];  
            $account_id = getAccountFromUser($user_id);
            
		    $upload_name = $ap['upload_name'];
		    $upload_data = $ap['upload_file'];
    		$file_name = time().rand();
    		
		    $filename = base64ToImage($file_name,$upload_data);
          	 
          	$sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
                    VALUES ('{$job_id}', '{$user_id}', '{$account_id}', '$filename', '{$upload_name}', now())";
            
			if(DBUtil::query($sql))
			{
			    $sql = "SELECT * FROM uploads WHERE filename = '{$filename}' AND user_id = '{$user_id}'";
                $results = DBUtil::queryToArray($sql);
                $upload_data = $results[0]; 
            
			    $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$upload_data));
                displayOutput($response);
			}
			else
			{
			    $response=array('status'=>array('error_code'=>0,'message'=>'Query Failed to save data!'),'result'=>array('data_list'=>'')); 
                displayOutput($response);
			}
            
    	}
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
    	    
    }
    
    
    function base64ToImage($file_name,$upload_data)
	{
	    $upload_dir = $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$file_name;
        if (preg_match('/^data:image\/(\w+);base64,/', $upload_data, $type)) 
        {
            $upload_data = substr($upload_data, strpos($upload_data, ',') + 1);
            $image_type = strtolower($type[1]); // jpg, png, gif
            $image_base64 = base64_decode($upload_data);
            if ($upload_data === false) 
            {
                $response=array('status'=>array('error_code'=>0,'message'=>'base64_decode failed!'),'result'=>array('data_list'=>'')); 
                displayOutput($response);
                
            }
        } 
        else 
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'did not match data URI with image data!'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
        $file = $upload_dir . '.'.$image_type;
       
        file_put_contents($file, $image_base64);
        return $file_name.'.'.$image_type;
    }
    
    
    
    function getFile()
    {
        $file = "data:image/jpeg;base64,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";
        
        return $file;
    }
    
    
    
    /* 
    Author : Sofikul 
    Date : 30.11.2021
    */
    function getJobUploadFiles()
    {	 
       $ap = json_decode(file_get_contents('php://input'), true);
	   if(sizeof($ap))
       {	 
            if(empty($ap['job_id'])){
				$response=array('status'=>array('error_code'=>1,'message'=>'Job id  is required'),'result'=>array('data_list'=>''));
				displayOutput($response);
			}
			
		    $job_id = $ap['job_id'];  
		    
		    $sql = "SELECT * FROM jobs WHERE job_id = '$job_id'";
            $results = DBUtil::queryToArray($sql);
            $myJob = $results[0]; 
        
  		    $jobnumber= $myJob['job_number'];		 
    		$dir ='ServerDirectory/'.$jobnumber.'/';
    		$isVisible = "false";
            
  		    $images = array();

      		// Open a directory, and read its contents
      		if (is_dir($dir))
      		{
      		      if ($dh = opendir($dir))
      		      {
          		      while (($file = readdir($dh)) != false){
                  			if($file != "." && $file != "..")
                  			{
                  				$images[] = $file; 
                  			}
            		        //echo "filename:" . $file . "<br>";
          		      }
          		      closedir($dh);
      		      }
  		    }
  		
      		if(!empty($images))
      		{
      			$isVisible = "true";
      		}

  		    $sql = "SELECT up.*, u.fname, u.user_id, u.lname, concat(u.fname, ', ' ,u.lname) as FullName, sf.type, date_format( up.timestamp, '%b %d, %Y \@ %h:%i %p' ) as timestamp FROM uploads up 
  		    LEFT JOIN users u ON u.user_id = up.user_id
  			LEFT JOIN schedule_forms sf on sf.upload_id = up.upload_id
  			WHERE up.job_id='$job_id' AND up.active = 1 ORDER BY up.timestamp DESC";
  	 
  		    
            $uploads_array=array();
            $rslt = DBUtil::fetchdata($sql);
            if(mysqli_num_rows($rslt))
            { 
                $uploads_array=DBUtil::convertResultsToArray($rslt);
            }              	      
            
      		$finalupload = array();
      		
      		foreach($uploads_array as &$origin) 
            {			 
  			    $uploadType = JobUtil::getUploadType($origin['filename']);			
  			    $file_path = UPLOADS_DIR.'/'.$origin['filename'];
      		
        		if(file_exists($file_path))
                {
    				$file_size = ceil(filesize($file_path) / 1000);
    				$origin['IMGuploadType'] = $uploadType;
    				$origin['file_name'] = $origin['filename'];
    				$origin['file_size']=$file_size;
    				array_push($finalupload,$origin);
    			}      			 
    			
      		}
          	 
          	$response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>$finalupload));
            displayOutput($response);
            
    	}
        else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
    	    
    }

    /* 
    Author : Sofikul 
    Date : 01.11.2021
    */
    function updateJobUploadFileTitle()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
	    if(sizeof($ap))
        {	 
            if(empty($ap['job_id'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Job id  is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['upload_id'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Upload id  is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['title'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Title is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		$job_id = $ap['job_id'];
    		$upload_id = $ap['upload_id'];
    		$title = $ap['title'];

    		$sql = "SELECT * FROM uploads where upload_id ='$upload_id' AND job_id='$job_id'";
    		$result = DBUtil::fetchdata($sql);
    	    if(mysqli_num_rows($result))
    		{
                $sql = "update uploads set title='$title' where upload_id ='$upload_id' limit 1";
        		$result = DBUtil::fetchdata($sql);
    			if($result == "true")
    			{
    			    $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>''));
                    displayOutput($response);
    			}
    			else
    			{
    			    $response=array('status'=>array('error_code'=>0,'message'=>'Failed to update, Try again!'),'result'=>array('data_list'=>'')); 
                    displayOutput($response);
    			}
            }
    		else
            {
                $response=array('status'=>array('error_code'=>0,'message'=>'File not exist!'),'result'=>array('data_list'=>'')); 
                displayOutput($response);
            }
	    }
	    else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
    	  
    }
  
    /* 
    Author : Sofikul 
    Date : 01.11.2021
    */
    function deleteJobUploadFile()
    {
        $ap = json_decode(file_get_contents('php://input'), true);
	    if(sizeof($ap))
        {	 
            if(empty($ap['job_id'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Job id  is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		if(empty($ap['upload_id'])){
    			$response=array('status'=>array('error_code'=>1,'message'=>'Upload id  is required'),'result'=>array('data_list'=>''));
    			displayOutput($response);
    		}
    		
    		$upload_id = $ap['upload_id'];
    		$job_id = $ap['job_id'];
    		
    		$sql = "SELECT filename FROM uploads where upload_id ='$upload_id'  AND job_id='$job_id' ";
    		$result = DBUtil::query($sql);
    		
    	    if(mysqli_num_rows($result))
    		{	
    			list($filename) = mysqli_fetch_row($result);
    			unlink(UPLOADS_PATH . '/' . $filename);
    			
    			$sql = "DELETE FROM uploads WHERE upload_id = '$upload_id' AND job_id='$job_id' LIMIT 1";
    			$result = DBUtil::query($sql);
    			if($result == "true")
    			{	
    			    $response=array('status'=>array('error_code'=>0,'message'=>'Success'),'result'=>array('data_list'=>''));
                    displayOutput($response);
    			}
    			else
    			{
    			    $response=array('status'=>array('error_code'=>0,'message'=>'Failed to delete, Try again!'),'result'=>array('data_list'=>'')); 
                    displayOutput($response);
    			}
            }
    		else
            {
                $response=array('status'=>array('error_code'=>0,'message'=>'File not exist!'),'result'=>array('data_list'=>'')); 
                displayOutput($response);
            }
	    }
	    else
        {
            $response=array('status'=>array('error_code'=>0,'message'=>'No data submitted.'),'result'=>array('data_list'=>'')); 
            displayOutput($response);
        }
        
    }
    

?>