<?php
 include 'includes/common_lib.php';
  $response = '';
   $methodname = $_POST['MethodName'];
   
    $response = $methodname();
    echo $response;
    $userid;
    
    
      $link = DBUtil::connect('workflow365test');
      require_once("dbcontroller.php");
      $db_handle = new DBController();
    
    
          function GetAllCrmUsers()
        {
        $result = mysqli_query("SELECT * FROM users");
         $posts = array();
        if(mysqli_num_rows($result)) {
            while($post = mysqli_fetch_assoc($result)) {
                $posts[] = array('post'=>$post);
            }
        return json_encode($posts);
        } 
         }
		 
    function connect()
    {
     $link = DBUtil::connect('workflow365test');
    }
    
    
    //Login- Jigar
    function Login()
    {
	 
     if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['account'])) {
      if(AuthModel::processLogin()) {
           $account = $_POST['account'];
           $username = $_POST['username'];
           $password = $_POST['password'];
           
        
           //Related Methods: AuthModel::attemptToLogin($username, $password, $account);
           $sql= "SELECT users.user_id AS ao_userid, users.username AS ao_username, users.fname AS ao_fname, users.lname AS ao_lname, users.dba AS ao_dba,
                DATE_FORMAT(access.timestamp, '%c/%e %k:%i') AS ao_lastvisit, users.level AS ao_level, users.is_active, users.is_deleted,
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
                        AND accounts.account_name ='$account'
                        AND accounts.account_id = users.account_id
                        AND levels.level_id = users.level
                ORDER BY access.access_id DESC LIMIT 1";
                  
          $results  =   DBUtil::fetchAssociativeArray(DBUtil::query($sql)); 
             
           if(!empty($results)) {
                   
                 $levelid=$results["ao_level"];
                 $accountid=$results["ao_accountid"];
                 $userid=$results["ao_userid"];
				 $founderId = $results["ao_founder"];
				 $sysLogo = $results["ao_logo"];
				 $accountname = $results["ao_accountname"];

                 $_SESSION['ao_accountid']=$accountid;
                 $_SESSION['ao_userid']=$userid;
                 $_SESSION['ao_level']=$levelid;
				 $_SESSION['ao_founder']=$founderId;
				 $_SESSION['ao_logo']=$sysLogo;
				 $_SESSION['ao_accountname']=$accountname;
                
                $sql = "SELECT a.is_active as a_isactive,u.is_active as u_isactive,u.is_deleted as u_isdeleted FROM accounts
                        a inner join users u on u.account_id=a.account_id where a.account_id='{$accountid}'  && u.user_id='{$userid}' ";
                
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
                    return json_encode(array("User" =>$results,"modules" =>$modules,"navigations" =>$navigations,'status'=> 1));
            
                }
                else
                {
                   return json_encode(array("message" => "Account Not Active",'status'=> 0));
                }
           }
          }
          else
          {
          	LogUtil::getInstance()->logNotice("Failed login - Invalid credentials: $account,$username,$password ");
            return json_encode(array("message" => "Invalid Credentials!",'status'=> 0));
          }                                                                        
      }
      
    }
    
    function BindMenu()
    {
    echo $userid;
     connect();
     $accountid='1';
     $levelid= '1';
        //Related Methods: UIModel::getNavList(); - preload nav access
        $sql = "SELECT n.navigation_id, n.title, n.source, n.icon
                FROM navigation n, nav_access na
                WHERE 
                    na.account_id = {$accountid}
                    AND n.navigation_id = na.navigation_id
                    AND na.level = {$levelid}
                ORDER BY n.order_num ASC";
                
         $navigationArray = DBUtil::queryToArray($sql);
         return json_encode(array("lstMenu" =>$navigationArray));
      }
   
    //Get customer list imran
    function GetCustomerList()
    {       
          global $UId;
            connect();
          $searchtype = $_POST['SearchType'];
          $searchtext='';
          
          if(isset($_POST['SearchText']))
          {
              $searchtext = $_POST['SearchText'];       
          }
          $limit= $_POST['limit'];
          $offset= $_POST['offset'];          
          $sort = !empty($searchtype) ? $searchtype : 'ORDER BY c.lname ASC';
           
          if(!empty($searchtext))
          {
              $term = trim($searchtext);
              $totalCountquery = "SELECT c.customer_id
                  FROM customers c
                  JOIN users u ON u.user_id = c.user_id
                  AND (c.fname LIKE '%$term%' OR c.lname LIKE '%$term%')                         
                  $sort";
              
              $result = DBUtil::query($totalCountquery);
              $num_rows = mysqli_num_rows($result);
               
              $sql = "SELECT c.customer_id, c.fname,c.lname,CASE WHEN c.nickname IS NULL THEN '' ELSE c.nickname END AS nickname,c.timestamp, c.zip, c.address
                  FROM customers c
                  JOIN users u ON u.user_id = c.user_id AND c.account_Id = '1'
                  AND (c.fname LIKE '%$term%' OR c.lname LIKE '%$term%')                          
                  $sort                  
                  LIMIT $limit OFFSET $offset";                   
              $custlist=  DBUtil::queryToArray($sql); 
             
              return json_encode(array("custlist" => $custlist,'status'=>1,'totalrecord' => $num_rows,'currentofset'=>$offset));
                  
             
          }
          else
          {
              $totalCountquery = "SELECT c.customer_id
                  FROM customers c
                  JOIN users u ON u.user_id = c.user_id  
                  $sort";              
              $result = DBUtil::query($totalCountquery);
              $num_rows = mysqli_num_rows($result);
             
              $sql = "SELECT c.customer_id, c.fname,c.lname, CASE WHEN c.nickname IS NULL THEN '' ELSE c.nickname END AS nickname,c.timestamp, c.zip, c.address
                  FROM customers c
                  JOIN users u ON u.user_id = c.user_id  AND c.account_Id = '1'                 
                  $sort
                  LIMIT $limit OFFSET $offset"; 
                  $custlist =  DBUtil::queryToArray($sql);  
             
              return json_encode(array("custlist" => $custlist,'status'=> 1,'totalrecord' => $num_rows,'currentofset'=>$offset));
          }         
          
    }
    
   //Get RepairList for Dashboard  By VIR
   function GetRepairList()
   {  
		UserModel::isAuthenticated();
		if(viewWidget('widget_today')) {
		  $accountid = $_SESSION['ao_accountid'];
		  $UserID= $_SESSION['ao_userid'];
      
			  $loginuserid = $UserID;
			  $loginaccountid = $accountid;
			  $todayDate = date("Y-m-d");
          
			  $repairsArray =  ScheduleUtil::getRepairs("$todayDate", "$loginuserid", "$loginaccountid");
			  $tasksArray = ScheduleUtil::getTasks("$todayDate", "$loginuserid", "", "$loginaccountid");
			  $eventsArray = ScheduleUtil::getEvents("$todayDate", "$loginuserid", "$loginaccountid");

			  foreach($eventsArray as &$origin) {
					$origin['time']= DateUtil::formatTime($origin['date']);
				}

			  $appointmentsArray = ScheduleUtil::getAppointments("$todayDate", "$loginuserid", "$loginaccountid");
			  $deliveriesArray = ScheduleUtil::getDeliveries("$todayDate", "$loginuserid", "$loginaccountid");
               
			  return json_encode(array("status"=>1,"repairsArray" => $repairsArray, "tasksArray" => $tasksArray, "eventsArray" => $eventsArray, "appointmentsArray" => $appointmentsArray, "deliveriesArray" => $deliveriesArray));
		  }
		  else
		  {
			return json_encode(array("status"=>0));
		  }
   }
  
  //Get DocumentList for Dashboard  By VIR
  function GetDocumentList()
  {		
       UserModel::isAuthenticated();
       if(viewWidget('widget_documents'))
	   {
			$sql = "SELECT document_id, document, timestamp, date_format(timestamp, '%b %d, %Y @ %h:%i %p') as ForamtDate  FROM documents WHERE account_id = '{$_SESSION['ao_accountid']}' ORDER BY timestamp DESC LIMIT 10";
			$sqlDoc = DBUtil::queryToArray($sql);
			
			if(empty($sqlDoc))
			{
				return json_encode(array('status' => '0','Post' => $sqlDoc));
			}
			else
			{
				return json_encode(array('status' => '1','Post' => $sqlDoc));
			}
		}
		else
		{
			return json_encode(array('status' => '0','Post' => ''));
		}
}

//Get Inbox List for Dashboard  By VIR
function GetInboxList()
{           
            UserModel::isAuthenticated();
        $accountid = $_SESSION['ao_accountid'];
        $userid = $_SESSION['ao_userid'];
        $loginuserid = $userid;           
        
		if(viewWidget('widget_inbox'))
	    {
			$sql = "SELECT messages.message_id, messages.subject, users.fname, users.lname, messages.user_id, 
					date_format(messages.timestamp, '%b %d, %Y @ %h:%i %p') as timestamp , '' AS journal_id, '' AS job_id, '' AS job_number from messages, users, message_link
					where message_link.delete=0 and messages.message_id=message_link.message_id
					and messages.user_id=users.user_id and message_link.user_id=$userid
					UNION
					SELECT '' AS message_id, journals.text, users.fname, users.lname,
					journals.user_id, journals.timestamp, journals.journal_id, journals.job_id, jobs.job_number FROM journals,users, jobs
					WHERE recipientid LIKE '%$userid%' and journals.user_id=users.user_id AND jobs.job_id=journals.job_id AND journals.flag=1
					ORDER BY timestamp DESC LIMIT 10";
			
			$sqlInbox = DBUtil::queryToArray($sql);
			if(empty($sqlInbox))
			{
				return json_encode(array('status' => '0','Post' => $sqlInbox));
			}
			else
			{
				return json_encode(array('status' => '1','Post' => $sqlInbox));
			}
		}
		else
		{
			return json_encode(array('status' => '0','Post' => ''));
		} 
     
}

//Get Urgent Job List for Dashboard  By VIR
function GetUrgentJobList()
{
            UserModel::isAuthenticated();
			$accountid = $_SESSION['ao_accountid'];
			$loginaccountid = $_SESSION['ao_accountid'];
			
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
					return json_encode(array('status' => '0','Post' => $sqlUrgent));
				}
				else
				{
					return json_encode(array('status' => '1','Post' => $sqlUrgent));
				}
			}
			else
			{
				return json_encode(array('status' => '0','Post' => ''));
			}
}

//Add new Supplier By VIR
   function SaveSupplierDetail()
   { 
          if(isset($_POST['Supplier']) && isset($_POST['Contact']) && isset($_POST['Phone']) && isset($_POST['Fax']) && isset($_POST['Email'])) 
          {   
              connect();
              $accountid='1';
              
              $loginaccountid = $accountid;
              $name = $_POST['Supplier'];
              $contact = $_POST['Contact'];
              $phone = $_POST['Phone'];
              $fax = $_POST['Fax'];
              $email = $_POST['Email'];
           
              $sql = mysqli_query("select supplier_id from suppliers where email = '$email'");
              if(mysqli_num_rows($sql))
              {
                  return json_encode(array('status'=> 0,'message'=>"Email in use"));
              }
              else
              {
                  $sql = "INSERT INTO suppliers VALUES (NULL, '$name', '$contact', '$email', '$phone', '$fax')";
                  $result = DBUtil::query($sql);
              
                  if($result == true)
                  {
                      $sql = "INSERT INTO suppliers_link VALUES (NULL,'" . DBUtil::getInsertId() . "' , $accountid)";
                      $result1 = DBUtil::query($sql);
                  }
              
                  if($result == true && $result1 == true)
                  {
                      return json_encode(array('result'=> $result,'result1'=> $result1, 'status'=> 1,'message'=>"Record Saved Successfully")); 
                  }
                  else
                  {
                      return json_encode(array('result'=> $result,'result1'=> $result1, 'status'=> 0,'message'=>"There are some error, can't save Supplier!"));
                  }
              }
          }
    }
  
  function GetSupplierList()
{
        
        $accountid = $_SESSION['ao_accountid'];
        
		$sql = "SELECT s . * FROM suppliers s, suppliers_link sl WHERE s.supplier_id = sl.supplier_id AND sl.account_id = '$accountid' ORDER BY s.supplier ASC";
		$result = DBUtil::queryToArray($sql);
       
	    if(!empty($result))
		{
			foreach($result as &$origin) {
				$formatPhn = UIUtil::formatPhone($origin['phone']);
				$formatFax = UIUtil::formatPhone($origin['fax']);
				$origin['formatPhn'] = $formatPhn;
				$origin['formatFax'] = $formatFax;
			}
			
		}
		
		return json_encode(array('status'=> 0,'result'=>$result));
}
 

  // Bind Supplier Detail for Edit By VIR
  function BindSupplierDetailByID()
  {     
        if(isset($_POST['SupplierID'])) 
        {   
            connect();
            $SupplierID = $_POST['SupplierID'];
            $result = mysqli_query("select * from suppliers where supplier_id = $SupplierID");
            
              $posts = array();
 	            if(mysqli_num_rows($result)) {
 		          while($post = mysqli_fetch_assoc($result)) {
 		          	$posts[] = array('Post'=>$post);
		          }
              return json_encode($posts);
 	          }
        }
  }      
  
  // Update Supplier Detail By VIR
  function UpdateSupplierDetail()
  {       
         if(isset($_POST['SupplierID']) && isset($_POST['Supplier']) && isset($_POST['Contact']) && isset($_POST['Phone']) && isset($_POST['Fax']) && isset($_POST['Email'])) 
         {    
              connect();
              $SupplierID = $_POST['SupplierID'];
              $Supplier = $_POST['Supplier'];
              $Contact = $_POST['Contact'];
              $Phone = $_POST['Phone'];
              $Fax = $_POST['Fax'];
              $Email = $_POST['Email'];
                            
              $sql = mysqli_query("select supplier_id from suppliers where email = '$Email' && supplier_id != $SupplierID ");
              if(mysqli_num_rows($sql))
              {
                return json_encode(array('status'=> 0,'message'=>"Email in use"));
              }
              else
              {
                $sql = "update suppliers set supplier='$Supplier', contact='$Contact', email='$Email', phone='$Phone', fax='$Fax' where supplier_id=$SupplierID"; 
                $result = DBUtil::query($sql);
                if($result == true)
                {
                      return json_encode(array('status'=> 1,'message'=>"Record updated successfully!")); 
                }
                else
                {
                      return json_encode(array('status'=> 0,'message'=>"Not Updated"));
                }
              }
         }
  }
   // Delete Supplier By VIR
   function DeleteSupplierById()
   {             
        if(isset($_POST['SupplierID'])) 
        {
              connect();
              $accountid='1';
              
              $SupplierID = $_POST['SupplierID'];
              $loginaccountid = $accountid;
        
              $sql = mysqli_query("select sheet_id from sheets where supplier_id=$SupplierID");
              if(mysqli_num_rows($sql))
              {
                return json_encode(array('status'=> 0,'message'=>"Jobs Currently Associated - Cannot Remove"));
              }
              else
              {
                $sql = "delete from suppliers where supplier_id=$SupplierID limit 1";
                $result = DBUtil::query($sql);
              
                $sql = "delete from suppliers_link where supplier_id=$SupplierID and account_id=$accountid limit 1";
                $result1 = DBUtil::query($sql);
              
                if($result == true && $result1 == true){
                      return json_encode(array('status'=> 1,'message'=>"Supplier deleted successfully!")); }
                else{
                      return json_encode(array('status'=> 0,'message'=>"There are some error, can't delete Supplier!"));}
              }
        }
  }
   // Get Material Detail of the Category By VIR
   function GetMatDetailForCategory()
   {  
      if(isset($_POST['CategoryID'])) 
      {   
          connect();
          $accountid='1';
          $categoryId = $_POST['CategoryID'];
          $loginaccountid = $accountid;
          
          $catSql = "select category from categories where category_id=$categoryId";
          $brandSql = "select brand_id, brand from brands where account_id=$accountid order by brand asc";        
          $matDetailSql = "SELECT b.brand,b.brand_id, u.unit, m .* FROM materials m LEFT JOIN brands b ON b.brand_id = m.brand_id
                           LEFT JOIN units u ON u.unit_id = m.unit_id
                           WHERE m.category_id = $categoryId  ORDER BY m.brand_id desc";

		  $BrandCount = "SELECT COUNT(brand_id) as count,brand_id FROM materials WHERE category_id = $categoryId group by brand_id";
		  $BrandCountArray =   DBUtil::queryToArray($BrandCount);
          
          $categoryArray=   DBUtil::queryToArray($catSql);
          $brandlistArray=   DBUtil::queryToArray($brandSql);
          $matDetailSql=   DBUtil::queryToArray($matDetailSql);
          
          return json_encode(array("categoryArray" => $categoryArray,"brandlistArray" => $brandlistArray,"matDetailSql" => $matDetailSql,"brandcount" => $BrandCountArray));
      }
 }
 // Get List for Material Category
 function GetCatListforMaterial()
 { 
       
       
        $accountid='1';
        
        $loginaccountid = $accountid;
        $result = mysqli_query("select category_id, category from categories where account_id=$accountid order by category asc");
        
          $posts = array();
 	        if(mysqli_num_rows($result)) {
 		      while($post = mysqli_fetch_assoc($result)) {
 		      	$posts[] = array('Post'=>$post);
		      }
          return json_encode($posts);
 	      } 
    
 }
 
 // Get List of Stages By VIR
 function GetStages()
 {
       
           connect();
           $accountid='1';
          
          $loginaccountid = $accountid;
          $stagesSql = "SELECT stage_id, stage_num, stage FROM stages WHERE account_id =$accountid order by stage_num asc";        
          $stageArray=   DBUtil::queryToArray($stagesSql);
          
          $sql = "SELECT * FROM stage_reqs sr INNER JOIN stage_reqs_link srl ON srl.stage_req_id = sr.stage_req_id WHERE srl.account_id =$accountid";
          $reqArray=   DBUtil::queryToArray($sql);      
          
          return json_encode(array("stageArray" => $stageArray,"reqArray" => $reqArray));
      
 }
 
 //Add new Stage By VIR
   function SaveStage()
   { 
          if(isset($_POST['stage']) && isset($_POST['description']) && isset($_POST['duration'])) 
          {   
            
              connect();
              $accountid='1';
              
              $loginaccountid = $accountid;
              $stage = $_POST['stage'];
              $description = $_POST['description'];
              $duration = $_POST['duration'];
                
              $last_stage = DBUtil::queryToArray('select stage_num from stages order by stage_num DESC limit 1');
              $stage_num = $last_stage[0]['stage_num'] + 1;
              
              $sql = "INSERT INTO stages (stage_num, account_id, stage, description, duration) VALUES ('$stage_num', '$accountid', '$stage', '$description', '$duration')";
              $result = DBUtil::query($sql);
              
              if($result == true)
              {
                  return json_encode(array('status' => 1,'message' => "Record Saved Successfully!"));
              }
              else
              {
                  return json_encode(array('status' => 1,'message' => "There are some error, please try again!"));
              }
          }
    }
    
  // Update Stage Detail By VIR
  function UpdateStageDetail()
  {       
         if(isset($_POST['StageID']) && isset($_POST['stage'])) 
         {    
              connect();
              $StageID = $_POST['StageID'];
              $stage = $_POST['stage'];
              
              $sql = "update stages set stage = '$stage' where stage_id = $StageID"; 
              
              $result = DBUtil::query($sql);
              if($result == true)
              {
                    return json_encode(array('status'=> 1,'message'=>"Stage modified")); 
              }
              else
              {
                    return json_encode(array('status'=> 0,'message'=>"Required information missing"));
              }
            
         }
  }
  // Delete Stage By VIR
   function DeleteStageById()
   {             
        if(isset($_POST['StageID'])) 
        {
              connect();
              $accountid='1';
              
              $StageID = $_POST['StageID'];
              $loginaccountid = $accountid;
        
              $sql = "delete from stages where stage_id=$StageID and account_id=$accountid limit 1";
              $result = DBUtil::query($sql);
              
              if($result == true){
                      return json_encode(array('status'=> 1,'message'=>"Stage deleted successfully!")); }
              else{
                      return json_encode(array('status'=> 0,'message'=>"There are some error, can't delete Stage!"));}
             
        }
  }
  
// Get List of Customer Details By VIR
 function GetCustomerDetails()
 {      
        if(isset($_POST['CustomerID'])) 
        {
          connect();
          $accountid='1';
          $customerId = $_POST['CustomerID'];
          $stateArray = getStates();
          $custSql = "SELECT j.job_number, u.fname AS uFname, u.lname AS uLname, concat(u.fname, ', ' ,u.lname) as FullName , c.* FROM customers c
                      LEFT JOIN jobs j ON j.customer_id = c.customer_id
                      LEFT JOIN users u ON u.user_id = c.user_id
                      WHERE c.customer_id = $customerId limit 1";
          $customerArray=   DBUtil::queryToArray($custSql);
          
          if(!empty($customerArray))
         {   UserModel::storeBrowsingHistory($customerArray[0]['u.fname'], 'address_16', 'customers.php', $customerId);  }
          
          return json_encode(array("stateArray"=> $stateArray,"customerArray" => $customerArray));
        }
 }
 
 // Update Customer Detail By VIR
  function UpdateCustomerDetail()
  {       
         if(isset($_POST['CustomerID']) && isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['nickname']) && isset($_POST['address']) && isset($_POST['city']) && isset($_POST['zip']) && isset($_POST['state']) && isset($_POST['cross_street']) && isset($_POST['phone']) && isset($_POST['phone2']) && isset($_POST['email'])) 
         {    
              connect();
              $CustomerID = $_POST['CustomerID'];
              $fname = $_POST['fname'];
              $lname = $_POST['lname'];
              $nickname = $_POST['nickname'];
              $address = $_POST['address'];
              $city = $_POST['city'];
              $state = $_POST['state'];
              $zip = $_POST['zip'];
              $cross_street = $_POST['cross_street'];
              $phone = $_POST['phone'];
              $phone2 = $_POST['phone2'];
              $email = $_POST['email'];
                         
              $sql = "update customers set fname='$fname', lname='$lname', nickname='$nickname', address='$address', city='$city', state='$state', zip='$zip', cross_street='$cross_street', phone='$phone', phone2='$phone2', email='$email' where customer_id=$CustomerID"; 
              $result = DBUtil::query($sql);
              if($result == true)
              {
                    return json_encode(array('status'=> 1,'message'=>"Customer detail updated successfully!")); 
              }
              else
              {
                    return json_encode(array('status'=> 0,'message'=>"Can't update customer details"));
              }
         }
  }
 
  // Get Document List By VIR
 function GetListOfDocuments()
 {     
    
        connect();
        $accountid='1';
        
        $searchtype = $_POST['SearchType'];
        $searchtext='';
          
        if(isset($_POST['SearchText']))
        {
            $searchtext = $_POST['SearchText'];       
        }
        $limit= $_POST['limit'];
        $offset= $_POST['offset'];    
        $sort='';
        if(!empty($searchtype))
        {
         $sort = "AND (dg.label LIKE '%$searchtype%' OR dg.label LIKE '%$searchtype%')";
        }
        
        
        
        if(!empty($searchtext))
          {
              $term = trim($searchtext);
              $totalCountquery = "SELECT d.document_id FROM documents d
                  LEFT JOIN users u ON u.user_id = d.user_id where d.account_Id = $accountid 
                  AND (d.document LIKE '%$term%' OR d.description LIKE '%$term%')
                  $sort
                  order by d.document ASC ";
                  
              
              $result = DBUtil::query($totalCountquery);
              $num_rows = mysqli_num_rows($result);
               
              $sql = "SELECT d.document_id, d.document, d.filetype, d.timestamp, concat(u.lname, ', ', u.fname) as owner, d.user_id, CASE WHEN dg.label IS NULL THEN '' ELSE dg.label END AS label
                  FROM users u, documents d
                  LEFT JOIN document_group_link dgl ON (dgl.document_id = d.document_id)
                  LEFT JOIN document_groups dg ON (dgl.document_group_id = dg.document_group_id)
                  WHERE d.account_id = $accountid AND d.user_id = u.user_id
                  AND (d.document LIKE '%$term%' OR d.description LIKE '%$term%')
                  $sort
                  order by d.document ASC                 
                  LIMIT $limit OFFSET $offset";
                  
              $doclist=  DBUtil::queryToArray($sql); 
              
              $groupSql = "select * from document_groups where account_id = $accountid";
              $groupArray=  DBUtil::queryToArray($groupSql);
              
              return json_encode(array('doclist' => $doclist,'status'=>1,'totalrecord' => $num_rows,'currentofset'=>$offset, 'groupList' => $groupArray));
          }
          else
          {
              $term = trim($searchtext);
              $totalCountquery = "SELECT d.document_id ,dg.label
                  FROM users u, documents d
                  LEFT JOIN document_group_link dgl ON (dgl.document_id = d.document_id)
                  LEFT JOIN document_groups dg ON (dgl.document_group_id = dg.document_group_id)
                  WHERE d.account_id = $accountid AND d.user_id = u.user_id 
                  $sort
                  order by d.document ASC";     
              
              $result = DBUtil::query($totalCountquery);
              $num_rows = mysqli_num_rows($result);
             
              $sql = "SELECT d.document_id, d.document, d.filetype, d.timestamp, concat(u.lname, ', ', u.fname) as owner, d.user_id, CASE WHEN dg.label IS NULL THEN '' ELSE dg.label END AS label
                  FROM users u, documents d
                  LEFT JOIN document_group_link dgl ON (dgl.document_id = d.document_id)
                  LEFT JOIN document_groups dg ON (dgl.document_group_id = dg.document_group_id)
                  WHERE d.account_id = $accountid AND d.user_id = u.user_id 
                  $sort
                  order by document asc 
                  LIMIT $limit OFFSET $offset"; 
              $doclist =  DBUtil::queryToArray($sql);
              
              $groupSql = "select * from document_groups where account_id = $accountid";
              $groupArray=  DBUtil::queryToArray($groupSql);
              
              return json_encode(array('doclist' => $doclist,'status'=> 1,'totalrecord' => $num_rows,'currentofset'=>$offset, 'groupList' => $groupArray));
          } 
    
 }
 
 // Get List of Document Details By VIR
 function GetDetailsForDocuments()
 {      
        if(isset($_POST['DocumentID'])) 
        {
          connect();
          $accountid='1';
          $documentID = $_POST['DocumentID'];
          $DOCUMENTS_PATH = "http://workflow365.co/docs/";
          $docSql = "SELECT d.document_id, d.document, d.description, d.filename, d.filetype, d.timestamp, concat(u.lname, ', ', u.fname) as owner,
                    d.user_id, CASE WHEN dg.label IS NULL THEN '' ELSE dg.label END AS label, CASE WHEN s.stage IS NULL THEN '' ELSE s.stage END AS stage, s.stage_id, s.stage_num, dg.document_group_id
                  FROM users u, documents d
                  LEFT JOIN document_group_link dgl ON (dgl.document_id = d.document_id)
                  LEFT JOIN document_groups dg ON (dgl.document_group_id = dg.document_group_id)
                  LEFT JOIN stages s ON (s.stage_num = d.stage_num)
                  WHERE d.account_id = $accountid  AND d.user_id = u.user_id and d.document_id = $documentID LIMIT 1";
         $docDetailArray=   DBUtil::queryToArray($docSql);
         
         if(!empty($docDetailArray))
         {   UserModel::storeBrowsingHistory($docDetailArray[0]['document'], $docDetailArray[0]['filetype'], 'documents.php', $documentID);  }
         
         $documentGroups = DocumentModel::getAllDocumentGroups();
         $stages = StageModel::getAllStages();
          
         return json_encode(array("docDetailArray"=> $docDetailArray,"documentGroups" => $documentGroups,"stages" => $stages,"DOCUMENTS_PATH" => $DOCUMENTS_PATH));
        }
 }
 // Update Document Detail By VIR
  function UpdateDocumentDetail()
  {       
         if(isset($_POST['DocumentID']) && isset($_POST['title']) && isset($_POST['group']) && isset($_POST['stage']) && isset($_POST['description'])) 
         {    
              connect();
              $DocumentID = $_POST['DocumentID'];
              $title = $_POST['title'];
              $groupId = $_POST['group'];
              $stage_num = $_POST['stage'];
              $description = $_POST['description'];
              $accountid='1';
              
              $docGroupIDSql = "SELECT d.document_id, dg.document_group_id FROM documents d
                                LEFT JOIN document_group_link dgl ON (dgl.document_id = d.document_id)
                                LEFT JOIN document_groups dg ON (dgl.document_group_id = dg.document_group_id)
                                WHERE d.account_id = $accountid and d.document_id = $DocumentID";
              $docGroupIDArray=   DBUtil::queryToArray($docGroupIDSql);
                         
              $sql = "update documents set document='$title', stage_num='$stage_num', description='$description' where document_id=$DocumentID"; 
              $result = DBUtil::query($sql);
              
              //change group if applicable
		          if($groupId != MapUtil::get($docGroupIDArray, 'document_group_id')) 
              {
                  $sql = "DELETE FROM document_group_link WHERE document_id = $DocumentID LIMIT 1";
			            DBUtil::query($sql);
			            if(!empty($groupId)) {
				              $sql = "INSERT INTO document_group_link (document_id, document_group_id) VALUES ('$DocumentID', '$groupId')";
                      DBUtil::query($sql);
			            }
		          }
              
              if($result == true)
              {
                    return json_encode(array('status'=> 1,'message'=>"Document detail updated successfully!")); 
              }
              else
              {
                    return json_encode(array('status'=> 0,'message'=>"Can't update document details"));
              }
         }
  }
  // Delete Documen Details By VIR
   function DeleteDocumentDetails()
   {             
        if(isset($_POST['DocumentID'])) 
        {
              connect();
              $accountid='1';
              $DocumentID = $_POST['DocumentID'];
              
              $docSql = "select filename from documents where document_id=$DocumentID and account_id=$accountid limit 1";
              $docRes = DBUtil::query($docSql);
              
              if(mysqli_num_rows($docRes) != 0) {
                list($filename) = mysqli_fetch_row($docRes);
                unlink(DOCUMENTS_PATH . '/' . $filename);
                
                $sql = "delete from documents where document_id=$DocumentID and account_id=$accountid limit 1";
                $result = DBUtil::query($sql);

                $sql = "delete from document_group_link where document_id=$DocumentID";
                DBUtil::query($sql);
              
                if($result == true){
                      return json_encode(array('status'=> 1,'message'=>"Document deleted successfully!")); }
                else{
                      return json_encode(array('status'=> 0,'message'=>"There are some error, can't delete Document!"));}
              }
        }
  }
  
  //Add Document Group By VIR
    function AddDocumentGroup()
    {
     if(isset($_POST['title'])) 
      {
        connect();
        $title = $_POST['title'];
        $account = 1;
        
         $sql = "INSERT INTO document_groups (label, account_id)
                 VALUES ('$title', '$account')";
         $result = DBUtil::query($sql);
         
         if($result == "true")
         {
            return json_encode(array('status'=> 1,'message'=>"Document group '$title' successfully added")); 
         }
         else
         {
            return json_encode(array('status'=> 0,'message'=>"Record not saved successfully")); 
         }
      }
    }
   
   // Get Document Group List For Edit
   function GetDocumentGroupListForEdit()
   {
      connect();
      $documentGroups = DocumentModel::getAllDocumentGroups();
      return json_encode(array('documentGroups'=> $documentGroups));
   }
   
   //Edit Document Group By VIR
    function EditDocumentGroupByID()
    {
      if(isset($_POST['DocumentGroupID']) && isset($_POST['DocumentGroup'])) 
      {
        connect();
        $DocumentGroupID = $_POST['DocumentGroupID'];
        $Title = $_POST['DocumentGroup'];
        $account = 1;
        
         $sql = "UPDATE document_groups SET label = '$Title'
                WHERE document_group_id = '$DocumentGroupID'
                    AND account_id = $account
                LIMIT 1";
        $result = DBUtil::query($sql);
         
         if($result == "true")
         {
            return json_encode(array('status'=> 1,'message'=>"Document group successfully modified")); 
         }
         else
         {
            return json_encode(array('status'=> 0,'message'=>"Document group not modified!")); 
         }
      }
    }
    
    //Delete Document Group By VIR
    function DeleteDocumentGroupByID()
    {
      connect();
      if(isset($_POST['DocumentGroupID'])) 
      {
        $DocumentGroupID = $_POST['DocumentGroupID'];
        $Title = $_POST['DocumentGroup'];
        $account = 1;
        
        $documentGroupLinks = DBUtil::getRecord('document_group_link', $DocumentGroupID, 'document_group_id');
        if(count($documentGroupLinks)) {
                return json_encode(array('status'=> 0,'message'=>"Documents currently associated - cannot remove!")); 
        }
        else
        {
            $sql = "DELETE FROM document_groups WHERE document_group_id = $DocumentGroupID AND account_id = $account LIMIT 1";
            $result = DBUtil::query($sql);
        
            if($result == "true")
            {
                return json_encode(array('status'=> 1,'message'=>"Document group successfully deleted")); 
            }
            else
            {
                return json_encode(array('status'=> 0,'message'=>"Documents currently associated - cannot remove!")); 
            }
        }
        
      }
    }
   
    
    // Get Stage and Group List For Document Upload
   function GetListForGroupandStagesForDocumentUpload()
   {
      connect();
      $documentGroups = DocumentModel::getAllDocumentGroups();
      $stages = StageModel::getAllStages();
      return json_encode(array('documentGroups'=> $documentGroups, 'stages' => $stages));
   }
   
   
 // Get Message List By VIR
 function GetMessagingList()
 {     
        connect();
        $accountid='1';
        $userid = $_SESSION['ao_userid'];
        $searchtext='';
        $searchtype = '';
        if(isset($_POST['SearchType']))
        {
            $searchtype = $_POST['SearchType'];       
        }
        if(isset($_POST['SearchText']))
        {
            $searchtext = $_POST['SearchText'];       
        }
        $limit= $_POST['limit'];
        $offset= $_POST['offset'];    
        
        if($searchtype == '' || $searchtype == 'Inbox')
        {
          if(!empty($searchtext))
          {
              $term = trim($searchtext);
              $totalCountquery = "select count(messages.message_id) from messages, users, message_link
                                  where message_link.delete=0 and (messages.subject like '%$term%' || messages.body like '%$term%')
                                  and message_link.user_id='$userid'
                                  and messages.message_id=message_link.message_id
                                  and messages.user_id=users.user_id
                                  order by messages.timestamp desc";
              $result = DBUtil::query($totalCountquery);
              $num_rows = mysqli_num_rows($result);

              $sql = "select messages.message_id, messages.subject, messages.timestamp, users.fname, users.lname, message_link.timestamp as sentTime
                      from messages, users, message_link
                      where message_link.delete=0 and (messages.subject like '%$term%' || messages.body like '%$term%')
                      and message_link.user_id='$userid'
                      and messages.message_id=message_link.message_id
                      and messages.user_id=users.user_id
                      order by messages.timestamp desc
                      LIMIT $limit OFFSET $offset";    
              $messageList=  DBUtil::queryToArray($sql); 
              
              return json_encode(array('messageList' => $messageList,'status'=>1,'totalrecord' => $num_rows,'currentofset'=>$offset));
          }
          else
          {
              $term = trim($searchtext);
              $totalCountquery = "select count(messages.message_id) from messages, users, message_link
                     where message_link.delete=0 and message_link.user_id='$userid'
                     and messages.message_id=message_link.message_id
                     and messages.user_id=users.user_id
                     order by messages.timestamp desc";
              
              $result = DBUtil::query($totalCountquery);
              $num_rows = mysqli_num_rows($result);
            
              $sql = "select messages.message_id, messages.subject, messages.timestamp, users.fname, users.lname, message_link.timestamp as sentTime
                     from messages, users, message_link
                     where message_link.delete=0 and message_link.user_id='$userid'
                     and messages.message_id=message_link.message_id
                     and messages.user_id=users.user_id
                     order by messages.timestamp desc
                     LIMIT $limit OFFSET $offset ";
              
              $messageList=  DBUtil::queryToArray($sql); 
              
              return json_encode(array('messageList' => $messageList,'status'=>1,'totalrecord' => $num_rows,'currentofset'=>$offset));
          } 
        }
        else if($searchtype == 'Sent')
        {
            if(!empty($searchtext))
          {
              $term = trim($searchtext);
              $SenttotalCountquery = "select count(messages.message_id) from messages, users, message_link
                      where (messages.subject like '%$term%' || messages.body like '%$term%')
                      and messages.user_id='$userid'
                      and messages.message_id=message_link.message_id
                      and message_link.user_id=users.user_id";                   
                                  
              $result = DBUtil::query($SenttotalCountquery);
              $num_rows = mysqli_num_rows($result);

              $sql = "select messages.message_id, message_link.message_link_id, messages.subject, messages.timestamp, users.fname, users.lname, message_link.timestamp as sentTime, message_link.delete
                      from messages, users, message_link
                      where (messages.subject like '%$term%' || messages.body like '%$term%')
                      and messages.user_id='$userid'
                      and messages.message_id=message_link.message_id
                      and message_link.user_id=users.user_id
                      order by messages.timestamp desc
                      LIMIT $limit OFFSET $offset"; 
              
              $messageList=  DBUtil::queryToArray($sql); 
              
              return json_encode(array('messageList' => $messageList,'status'=>1,'totalrecord' => $num_rows,'currentofset'=>$offset));
          }
          else
          {
              $term = trim($searchtext);
              $SenttotalCountquery = "select count(messages.message_id) from messages, users, message_link
                     where messages.user_id='$userid'
                     and messages.message_id=message_link.message_id
                     and message_link.user_id=users.user_id";
             
              $result = DBUtil::query($SenttotalCountquery);
              $num_rows = mysqli_num_rows($result);
            
              $sql = "select messages.message_id, message_link.message_link_id, messages.subject, messages.timestamp, users.fname, users.lname, message_link.timestamp as sentTime, message_link.delete
                      from messages, users, message_link
                      where messages.user_id='$userid'
                      and messages.message_id=message_link.message_id
                      and message_link.user_id=users.user_id
                      order by messages.timestamp desc
                      LIMIT $limit OFFSET $offset ";
              
              $messageList=  DBUtil::queryToArray($sql); 
              
              return json_encode(array('messageList' => $messageList,'status'=>1,'totalrecord' => $num_rows,'currentofset'=>$offset));
          }
        }
        else if($searchtype == 'Trash')
        {
          if(!empty($searchtext))
          {
              $term = trim($searchtext);
              $totalCountquery = "select count(messages.message_id) from messages, users, message_link
                                  where message_link.delete=1 and (messages.subject like '%$term%' || messages.body like '%$term%')
                                  and message_link.user_id='$userid'
                                  and messages.message_id=message_link.message_id
                                  and messages.user_id=users.user_id
                                  order by messages.timestamp desc";
              $result = DBUtil::query($totalCountquery);
              $num_rows = mysqli_num_rows($result);

              $sql = "select messages.message_id, messages.subject, messages.timestamp, users.fname, users.lname, message_link.timestamp as sentTime
                       from messages, users, message_link
                       where message_link.delete=1 and (messages.subject like '%$term%' || messages.body like '%$term%')
                       and message_link.user_id='$userid'
                       and messages.message_id=message_link.message_id
                       and messages.user_id=users.user_id
                       order by messages.timestamp desc
                       LIMIT $limit OFFSET $offset";
              $messageList=  DBUtil::queryToArray($sql); 
              
              return json_encode(array('messageList' => $messageList,'status'=>1,'totalrecord' => $num_rows,'currentofset'=>$offset));
          }
          else
          {
              $term = trim($searchtext);
              $totalCountquery = "select count(messages.message_id) from messages, users, message_link
                                  where message_link.delete=1 and message_link.user_id='$userid'
                                  and messages.message_id=message_link.message_id
                                  and messages.user_id=users.user_id
                                  order by messages.timestamp desc";
              $result = DBUtil::query($totalCountquery);
              $num_rows = mysqli_num_rows($result);
            
              $sql = "select messages.message_id, messages.subject, messages.timestamp, users.fname, users.lname, message_link.timestamp as sentTime
                      from messages, users, message_link
                      where message_link.delete=1 and message_link.user_id='$userid'
                      and messages.message_id=message_link.message_id
                      and messages.user_id=users.user_id
                      order by messages.timestamp desc
                      LIMIT $limit OFFSET $offset ";
              $messageList=  DBUtil::queryToArray($sql); 
              
              return json_encode(array('messageList' => $messageList,'status'=>1,'totalrecord' => $num_rows,'currentofset'=>$offset));
          } 
        }
 }
// Get list of Users and User Group BY VIR
function GetlistofUsersAndUserGroupForComposeMsg()
{
  $firstLast = UIUtil::getFirstLast();
  $users_array = UserModel::getAll(TRUE, $firstLast);
  $groups = UserModel::getAllUserGroups();
  return json_encode(array('users_array' => $users_array,'groups' => $groups));
}
// Send Message BY VIR
function SendMail()
{
  if(isset($_POST['User']) && isset($_POST['UserGroup']) && isset($_POST['Subject']) && isset($_POST['Message'])) 
  {
    connect();
    $subject = $_POST['Subject'];
	  $body = $_POST['Message'];
	  $to = $_POST['User'];
	  $group = $_POST['UserGroup'];
    $accountid='1';
    $userid = $_SESSION['ao_userid'];
        
    if($to == '' && $group == '')
		  UIUtil::showAlert('Please choose a recipient or group');
	  else if($subject == '')
		  UIUtil::showAlert('Please enter a subject');
	  else if($body == '')
		  UIUtil::showAlert('Please enter a message');
	  else
	  {
	  	if($group == '')
	  	{
	  		$sendStatus = NotifyUtil::emailFromTemplate('new_message', $to);
        if($sendStatus == "true") 
        {
          $sql = "insert into messages values(0, '$accountid', '$userid', '$subject', '$body', now())";
	  	    DBUtil::query($sql);
          $message_id = DBUtil::getInsertId();
          $sql = "insert into message_link values(0, '$message_id', '$to', null, 0)";
	  	   	DBUtil::query($sql);
                   return json_encode(array('sendStatus' => $sendStatus, 'status'=> 1, 'message' => 'email has been sent successfully!'));
	      }
        else 
        {		            
                   return json_encode(array('sendStatus' => $sendStatus, 'status'=> 0, 'message' => 'Email failed to send'));
	      }
	  	}
	  	else
	  	{
	  		if($group == 'ALL')
	  			$sql = "select user_id from users where account_id='$accountid' and user_id<>'$userid' and is_active=1 and is_deleted=0 order by lname asc";
	  		else
	  			$sql = "select usergroups_link.user_id from usergroups_link, users where usergroups_link.user_id=users.user_id and usergroups_link.usergroup_id='$group' and users.is_active=1 and users.is_deleted=0";
	  		$res = DBUtil::query($sql);
	  		while (list($user_id) = mysqli_fetch_row($res))
	  		{
	  			$sendStatus = NotifyUtil::emailFromTemplate('new_message', $user_id);
          if($sendStatus == "true") 
          {	
                   $sql = "insert into message_link values(0, '" . $message_id . "', '" . $user_id . "', null, 0)";
	  			         DBUtil::query($sql);
                   return json_encode(array('sendStatus' => $sendStatus, 'status'=> 1, 'message' => 'email has been sent successfully!'));
	        }
          else 
          {		            
                   return json_encode(array('sendStatus' => $sendStatus, 'status'=> 0, 'message' => 'Email failed to send'));
	        }
	  		}
	  	}
    }
  }
}
//Get Message Details BY VIR
function GetAllMessageDetails()
{
  if(isset($_POST['MessageID']) && isset($_POST['MessageType'])) 
  {
    connect();
    $accountid=1;
    $userid = $_SESSION['ao_userid'];
    $messageid = $_POST['MessageID'];
    $messagetype = $_POST['MessageType'];
    
    if($messagetype == 'inbox')
    {
      $sql = "select messages.message_id, messages.subject, messages.body, messages.timestamp, users.fname, users.lname, message_link.timestamp, messages.user_id
              from messages, message_link, users
              where message_link.delete=0 and messages.message_id='$messageid'
              and message_link.message_id='$messageid'
              and messages.user_id=users.user_id
              limit 1";
      $InboxArray = DBUtil::queryToArray($sql);
                    
      if(empty($InboxArray))
      { 
          return json_encode(array('InboxArray' => $InboxArray, 'message' => 'Message Not Found'));  
      }
      else
      {   
          UserModel::storeBrowsingHistory($InboxArray[0]['subject'], 'email_16', 'messaging.php', $messageid);
          $todayDate = date("Y-m-d h:m:s");
          $sql = "update message_link set message_link.timestamp = '$todayDate' where message_link.message_id='$messageid' and message_link.user_id='$userid' limit 1";
          DBUtil::query($sql);
          return json_encode(array('InboxArray' => $InboxArray, 'message' => '$num_rows Message Found'));   
      }
    }
    else if($messagetype == 'sent')
    {
      $sql = "select messages.message_id, messages.subject, messages.body, messages.timestamp, users.fname, users.lname, message_link.timestamp as SentTime, messages.user_id
              from messages, message_link, users
              where messages.message_id=message_link.message_id
              and message_link.message_id='$messageid'
              and messages.user_id=users.user_id
              limit 1";
             
      $SentArray = DBUtil::queryToArray($sql);
      $num_rows = mysqli_num_rows($SentArray);
       
      if(mysqli_num_rows($SentArray) == 0)
      {
          return json_encode(array('SentArray' => $SentArray, 'message' => 'Sent Message Not Found'));
      }
      else
      {
        
        return json_encode(array('SentArray' => $SentArray));
      }
    }
    else if($messagetype == 'trash')
    {
      $sql = "select messages.message_id, messages.subject, messages.body, messages.timestamp, users.fname, users.lname, message_link.timestamp as SentTime, messages.user_id
              from messages, message_link, users
              where message_link.delete=1 and messages.message_id=message_link.message_id
              and message_link.message_id='$messageid'
              and messages.user_id=users.user_id
              limit 1";
      
      $TrashArray = DBUtil::queryToArray($sql);
      $num_rows = mysqli_num_rows($TrashArray);
       
      if(mysqli_num_rows($TrashArray) == 0)
      {
          return json_encode(array('TrashArray' => $TrashArray, 'message' => 'Trash Message Not Found'));
      }
      else
      {
        
        return json_encode(array('TrashArray' => $TrashArray));
      }
    }
   }
}
// Message Unread BY VIR
function UpdateMsgStatusAsUnread()
{
  if(isset($_POST['UnreadMsgId'])) 
  {
      connect();
      $userid = $_SESSION['ao_userid'];
      $messageid = $_POST['UnreadMsgId'];
      $sql = "update message_link set message_link.timestamp=null where message_link.message_id='$messageid' and message_link.user_id='$userid' limit 1";
      
      $result = DBUtil::query($sql);
      if($result == "true")
      {
          return json_encode(array('status'=> 1,'message'=>"Message status updated as Unread!")); 
      }
      else
      {
          return json_encode(array('status'=> 0,'message'=>"Message status can't updated")); 
      }
  }
}

// Message Deleted BY VIR
function UpdateMsgStatusAsDelete()
{
  if(isset($_POST['DeleteMsgId'])) 
  {
      connect();
      $userid = $_SESSION['ao_userid'];
      $messageid = $_POST['DeleteMsgId'];
      $sql = "update message_link set message_link.delete='1' where message_link.message_id='$messageid' and message_link.user_id='$userid' limit 1";
      $result = DBUtil::query($sql);
      if($result == "true")
      {
          return json_encode(array('status'=> 1,'message'=>"Message status updated as Unread!")); 
      }
      else
      {
          return json_encode(array('status'=> 0,'message'=>"Message status can't updated")); 
      }
  }
}

// Message Unread BY VIR
function RecoverDeletedMessages()
{
  if(isset($_POST['RecoverMsgId'])) 
  {
      connect();
      $userid = $_SESSION['ao_userid'];
      $messageid = $_POST['RecoverMsgId'];
  
      $sql = "update message_link set message_link.delete='0' where message_link.message_id='$messageid' and message_link.user_id='$userid' limit 1";
      $result = DBUtil::query($sql);
      if($result == "true")
      {
          return json_encode(array('status'=> 1,'message'=>"Message Recover Succesfully!")); 
      }
      else
      {
          return json_encode(array('status'=> 0,'message'=>"Can't recover this message!")); 
      }
  }
}
// Send Message BY VIR
function ReplyMail()
{
  if(isset($_POST['User']) && isset($_POST['UserGroup']) && isset($_POST['Subject']) && isset($_POST['Message'])) 
  {
    connect();
    $subject = $_POST['Subject'];
	  $body = $_POST['Message'];
	  $to = $_POST['User'];
	  $group = $_POST['UserGroup'];
    $accountid='1';
    $userid = $_SESSION['ao_userid'];
        
    if($to == '' && $group == '')
		  UIUtil::showAlert('Please choose a recipient or group');
	  else if($subject == '')
		  UIUtil::showAlert('Please enter a subject');
	  else if($body == '')
		  UIUtil::showAlert('Please enter a message');
	  else
	  {
	  	
	  	
	  	if($group == '')
	  	{
	  		$sendStatus = NotifyUtil::emailFromTemplate('new_message', $to);
        if($sendStatus == "true") 
        {		            
                   return json_encode(array('sendStatus' => $sendStatus, 'status'=> 1, 'message' => 'email has been sent successfully!'));
	      }
        else 
        {		            
                   return json_encode(array('sendStatus' => $sendStatus, 'status'=> 0, 'message' => 'Email failed to send'));
	      }
	  	}
	  	else
	  	{
	  		if($group == 'ALL')
	  			$sql = "select user_id from users where account_id='$accountid' and user_id<>'$userid' and is_active=1 and is_deleted=0 order by lname asc";
	  		else
	  			$sql = "select usergroups_link.user_id from usergroups_link, users where usergroups_link.user_id=users.user_id and usergroups_link.usergroup_id='$group' and users.is_active=1 and users.is_deleted=0";
	  		$res = DBUtil::query($sql);
	  		while (list($user_id) = mysqli_fetch_row($res))
	  		{
	  			$sendStatus = NotifyUtil::emailFromTemplate('new_message', $user_id);
          if($sendStatus == "true") 
          {		            
                   return json_encode(array('sendStatus' => $sendStatus, 'status'=> 1, 'message' => 'email has been sent successfully!'));
	        }
          else 
          {		            
                   return json_encode(array('sendStatus' => $sendStatus, 'status'=> 0, 'message' => 'Email failed to send'));
	        }
	  		}
	  	}
    }
  }
}

  // Get All USers List By VIR
 function GetAllUserList()
 {     
        connect();
        $accountid='1';
        $searchtype = $_POST['SearchType'];
        $searchtext='';
        if(isset($_POST['SearchText']))
        {
            $searchtext = $_POST['SearchText'];       
        }
        $limit= $_POST['limit'];
        $offset= $_POST['offset'];    
        $sort='order by lname asc';
        if(!empty($searchtype))
        {
         $sort = $searchtype;
        }
        $todayDate = date("M d,Y");
        if(!empty($searchtext))
          {
              $term = trim($searchtext);
              $totalCountquery = "SELECT user_id FROM users WHERE (fname LIKE '%$term%' || lname LIKE '%$term%' || dba LIKE '%$term%')
                                  AND account_id = '$accountid' $sort";
              
              $result = DBUtil::query($totalCountquery);
              $num_rows = mysqli_num_rows($result);
             
              $sql = "SELECT *, CASE WHEN dba IS NULL THEN '' ELSE dba END AS dba, '$todayDate' as todayDate FROM users WHERE (fname LIKE '%$term%' || lname LIKE '%$term%' || dba LIKE '%$term%')
                                  AND account_id = '$accountid' $sort LIMIT $limit OFFSET $offset";
              $userlist=  DBUtil::queryToArray($sql); 
              
              return json_encode(array('userlist' => $userlist,'status'=> 1,'totalrecord' => $num_rows,'currentofset'=>$offset));
          }
          else
          {
              $term = trim($searchtext);
              $totalCountquery = "SELECT user_id FROM users  WHERE account_id = '$accountid' $sort";
              $result = DBUtil::query($totalCountquery);
              $num_rows = mysqli_num_rows($result);
             
              $sql = "SELECT *, CASE WHEN dba IS NULL THEN '' ELSE dba END AS dba, '$todayDate' as todayDate FROM users  WHERE account_id = '$accountid' $sort LIMIT $limit OFFSET $offset"; 
              $userlist =  DBUtil::queryToArray($sql);
              
              return json_encode(array('userlist' => $userlist,'status'=> 1,'totalrecord' => $num_rows,'currentofset'=>$offset));
          } 
    
 }
 // Get Detail for User By VIR
 function GetDetailsForUser()
 {      
        if(isset($_POST['UserID'])) 
        {
          connect();
          $accountid='1';
          $levelid= '1';
          $user_id = $_POST['UserID'];
          $todayDate = date("M d,Y");
          $userSql = "SELECT u.*, of.title, CASE WHEN u.dba IS NULL THEN '' ELSE u.dba END AS dba, lv.level, CASE WHEN u.notes IS NULL THEN '' ELSE u.notes END AS notes, '$todayDate' as todayDate FROM users u
                      left join levels lv on lv.level_id = u.level
                      left join offices of on of.office_id = u.office_id
                      WHERE u.user_id= '$user_id' AND u.account_id = '$accountid'";
          $userlist=  DBUtil::queryToArray($userSql);
          
          if(!empty($userlist))
          {   UserModel::storeBrowsingHistory($userlist[0]['username'], 'icon-user', 'users.php', $user_id);  }
          
          $taskSql = "select task_type.task from task_type, task_link where task_type.task_type_id=task_link.task_type_id and task_link.user_id='$user_id' order by task_type.task asc";
          $taskRes = DBUtil::queryToArray($taskSql);
          
          $accesSql = "select navaccess_id from nav_access where navigation_id=23 and account_id='$accountid' and level='$levelid' limit 1";
          $accessRes = DBUtil::queryToArray($accesSql);
         
          $userNamesql = "SELECT fname FROM users  WHERE user_id= '$user_id' and account_id = '$accountid'";
          $userNameArray = DBUtil::query($userNamesql);
                    
          return json_encode(array("userArray"=> $userlist,"taskArray" => $taskRes,"accessArray" => $accessRes));
        }
 }
 
 // Send User Compose Message BY VIR
function SendUserComposeEMail()
{
  if(isset($_POST['User']) && isset($_POST['Subject']) && isset($_POST['Message'])) 
  {
    connect();
    $subject = $_POST['Subject'];
	  $body = $_POST['Message'];
	  $to = $_POST['User'];
	  $accountid='1';
    $userid = $_SESSION['ao_userid'];
        
      $sendStatus = NotifyUtil::emailFromTemplate('new_message', $to);
      if($sendStatus == "true") 
      {
        $sql = "insert into messages values(0, '$accountid', '$userid', '$subject', '$body', now())";
	              DBUtil::query($sql);
                $message_id = DBUtil::getInsertId();
        $sql = "insert into message_link values(0, '$message_id', '$to', null, 0)";
	    	        DBUtil::query($sql);
                return json_encode(array('sendStatus' => $sendStatus, 'status'=> 1, 'message' => 'email has been sent successfully!'));
	    }
      else 
      {		            
                 return json_encode(array('sendStatus' => $sendStatus, 'status'=> 0, 'message' => 'Email failed to send'));
	    }
  }
}

// Below Functions After ==> 23/09/2015 
// Get Users Browse History By VIR
function GetUsersBrowsingHistory()
{ 
  connect();
  $userid = $_SESSION['ao_userid'];
  $sql = "SELECT browsing_id, title, icon, script, item_id, timestamp FROM browsing WHERE user_id = '$userid' and title != ''
                GROUP BY CONCAT(script, item_id)
                ORDER BY MAX(timestamp) DESC
                LIMIT 100";
  $browsingHistory = DBUtil::queryToArray($sql);
  
  return json_encode(array('browsingHistory' => $browsingHistory));
}

// Get Users Access History By VIR
function GetUsersAccessHistory()
{ 
  connect();
  $user_id = $_POST['UserID'];
  $sql = "select user_id,date_format(timestamp, '%c-%e-%Y @ %r') as timestamp, ip_address from access where user_id='$user_id' order by timestamp desc";
  $accessHistory = DBUtil::queryToArray($sql);
  
  if(!empty($accessHistory))
  {
    return json_encode(array('accessHistory' => $accessHistory,'currUser' => $user_id));
  }
  else
  {
    return json_encode(array('accessHistory' => $accessHistory, 'message' => 'No History Found','currUser' => $user_id));
  }
}

// Get Users Activity History By VIR
function GetUsersActivityHistory()
{ 
  connect();
  $user_id = $_POST['UserID'];
  $sql = "select history.user_id,history.action, history.job_id, jobs.job_number, date_format(history.timestamp, '%c-%e-%Y @ %r') as timestamp  from history, jobs where jobs.job_id=history.job_id and history.user_id='$user_id' order by history.timestamp desc";
  $activityHistory = DBUtil::queryToArray($sql);
  
  if(!empty($activityHistory))
  {
    return json_encode(array('activityHistory' => $activityHistory,'currUser' => $user_id));
  }
  else
  {
    return json_encode(array('activityHistory' => $activityHistory, 'message' => 'No History Found','currUser' => $user_id));
  }
}

//Get User Detail for Send Credential
function SendUserCredentials()
{ 
  connect();
  
  $user_id = $_POST['UserID'];
  $accountid = 1;
  $sql = "select * from users where account_id='$accountid' and user_id='$user_id' limit 1";
    
  $userArray = DBUtil::queryToArray($sql);
  if(!empty($userArray))
  {
    $sendStatus = NotifyUtil::emailFromTemplate('new_user', $user_id);
    if($sendStatus == "true") 
    {
      return json_encode(array('status' => 1, 'message' => 'credentials sent'));
    } 
    else
    {
      return json_encode(array('status' => 0, 'message' => 'Something went wrong - please try again!'));
    }
  }
  else
  {
    return json_encode(array('userArray' => $userArray,'status' => 0 ,'message' => 'User data not found!'));  
  }
  
}

// Below Functions After ==> 25/09/2015 
 // Get Detail for User By VIR
 function GetDetailOfUserForEdit()
 {      
		connect();
        if(isset($_POST['UserID'])) 
        {
          connect();
          $accountid= $_SESSION['ao_accountid'];
          $levelid= '1';
          $user_id = $_POST['UserID'];
          $userSql = "SELECT * from users
                      WHERE user_id= '$user_id' AND account_id = '$accountid'";
          $userlist=  DBUtil::queryToArray($userSql);
          
		  $carriers = getAllSmsCarriers();
		  $userLevels = UserModel::getAllLevels();
		  $offices = AccountModel::getAllOffices();

          return json_encode(array("userArray"=> $userlist,"carriersArray" => $carriers,"userLevelsArray" => $userLevels,"officesArray" => $offices));
        }
 }

 // Edit User Detail By VIR
  function UpdateEditUserDetails()
  {       
		 connect();
         if(isset($_POST['UserID']) && isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['Dba']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['smsCarrier']) && isset($_POST['accessLevel']) && isset($_POST['office']) && isset($_POST['journal']) && isset($_POST['founder']) && isset($_POST['Active']) && isset($_POST['libInsExp']) && isset($_POST['txtLibInsExpOn']) && isset($_POST['comInsExp']) && isset($_POST['txtComInsExpOn']) && isset($_POST['txtNotes'])) 
         {    
              
              $UserID = $_POST['UserID'];
              $fname = $_POST['fname'];
              $lname = $_POST['lname'];
              $Dba = $_POST['Dba'];
              $email = $_POST['email'];
              $phone = $_POST['phone'];
			  $smsCarrier = $_POST['smsCarrier'];
              $accessLevel = $_POST['accessLevel'];
              $office = $_POST['office'];
			  $journal = $_POST['journal'];
			  $founder = $_POST['founder'];
			  $Active = $_POST['Active'];
              $libInsExp = $_POST['libInsExp'];
			  $txtLibInsExpOn = $_POST['txtLibInsExpOn'];
              $comInsExp = $_POST['comInsExp'];
			  $txtComInsExpOn = $_POST['txtComInsExpOn'];
              $txtNotes = $_POST['txtNotes'];
              
			  $emailSql = mysqli_query("select email from users where email = '$email' && user_id != $UserID ");
              $phoneSql = mysqli_query("select phone from users where phone='$phone' && user_id != $UserID ");
			  
			  if(mysqli_num_rows($emailSql))
              {
                return json_encode(array('status'=> 0,'message'=>"Email in use"));
              }
			  else if(mysqli_num_rows($phoneSql))
              {
                return json_encode(array('status'=> 0,'message'=>"Phone Number in use"));
              }
              else
              {
				$sql = "update users set fname='$fname', lname='$lname', dba='$Dba', email='$email', phone='$phone', sms_carrier='$smsCarrier',
				level='$accessLevel', office_id='$office', journal='$journal', founder='$founder', is_active='$Active', generalinsbox='$libInsExp',
				generalins='$txtLibInsExpOn', workerinsbox='$comInsExp', workerins='$txtComInsExpOn', notes='$txtNotes' where user_id='$UserID'"; 
				
				$result = DBUtil::query($sql);
				if($result == true)
				{
				      return json_encode(array('status'=> 1,'message'=>"Record updated successfully!")); 
				}
				else
				{
				      return json_encode(array('status'=> 0,'message'=>"Not Updated"));
				}
              }
         }
  }

  //Edit Password By VIR
  function UpdateUserPassword()
  {
	connect();
	if(isset($_POST['UserID']) && isset($_POST['Password']))
	{
		$UserID = $_POST['UserID'];
		$Password = $_POST['Password'];
		$userDetailSql = mysqli_query("select Password from users where user_id = $UserID ");
		
		if(mysqli_num_rows($userDetailSql))
		{	
            $sql = "update users set password='$Password' where user_id = $UserID limit 1";
			$result = DBUtil::query($sql);
			if($result == "true")
			{
			    return json_encode(array('status'=> 1,'message'=>"Password updated Successfully!")); 
			}
			else
			{
			    return json_encode(array('status'=> 0,'message'=>"Can't update password!")); 
			}
        }
		else
        {
                return json_encode(array('status'=> 0,'message'=>"User not exist!"));
        }
	}
  }	

  //Bind Upload Insurance PDF detail By VIR
  function UserInsurancepdfUpload()
  {
	 connect();
	 if(isset($_POST['UserID']))
	 {
	  $userid = $_POST['UserID'];
	  $sql = "SELECT pdf_id,pdfname, date_format(datecreated, '%c-%e-%Y @ %r') as datecreated FROM insurancepdfupload WHERE user_id = '$userid'";
      $userPdfList=  DBUtil::queryToArray($sql);
	  if(!empty($userPdfList))
	  {			
			return json_encode(array('userPdfArray' => $userPdfList));
	  }
	  else
      {
			return json_encode(array('userPdfArray' => $userPdfList));
	  }
	}
  }

  // Delete User By VIR
  function DeleteUserById()
  {
     connect();
	 if(isset($_POST['UserID']))
	 {
		$userid = $_POST['UserID'];
		$userDelSql = mysqli_query("select * from users where user_id = $userid ");
		if(mysqli_num_rows($userDelSql))
		{	
            $sql = "update users set is_deleted='1' where user_id = $userid limit 1";
			$result = DBUtil::query($sql);
			if($result == "true")
			{
			    return json_encode(array('status'=> 1,'message'=>"User deleted successfully!")); 
			}
			else
			{
			    return json_encode(array('status'=> 0,'message'=>"Can't delete User!")); 
			}
        }
		else
        {
                return json_encode(array('status'=> 0,'message'=>"User not exist!"));
        }
	 }
  }
   //Get Users Edit Permissions By VIR
  function GetUsersEditPermissions()
  {
	 connect();
	 if(isset($_POST['UserID']))
	 {
	  $userid = $_POST['UserID'];
	  $accountId = $_SESSION['ao_accountid'];
	  $sql = "select modules.module_id, modules.title, module_access.module_access_id, module_access.ownership as access_ownership, modules.ownership as ownership_enabled, 
			  exceptions.exception_id, exceptions.ownership as exception_ownership, exceptions.onoff as exception_onoff from modules
			  left join
			  exceptions on(modules.module_id=exceptions.module_id and exceptions.user_id='$userid')
			  left join
			  module_access on (modules.module_id=module_access.module_id)
			  and module_access.account_id='$accountId' and module_access.level='loginlevelid'
			  group by modules.module_id
			  order by modules.title asc";
		
      $userPermArray=  DBUtil::queryToArray($sql);
	  if(!empty($userPdfList))
	  {			
			return json_encode(array('userPermArray' => $userPermArray));
	  }
	  else
      {
			return json_encode(array('userPermArray' => $userPermArray));
	  }
	}
  }
  
  //Update User Permission By VIR
  function UpdateUserPermissionByAction()
  {
	connect();
	 if(isset($_POST['UserID']) && isset($_POST['ModuleId']) && isset($_POST['Action']))
	 {
		$ActionName = $_POST['Action'];
		$ModuleId = $_POST['ModuleId'];
		$userid = $_POST['UserID'];
		
		if($ActionName == 'add_ex_on')
		{
			$sql = "insert into exceptions values('', '$ModuleId', '$userid', 0, 1)";
			$result = DBUtil::query($sql);
            if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permission")); 
			}
		}
		else if($ActionName == 'add_ex_off')
		{
			$sql = "insert into exceptions values('', '$ModuleId', '$userid', 0, 0)";
			$result = DBUtil::query($sql);
            if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permission")); 
			}
		}
		else if($ActionName == 'add_ex_ownon')
		{
			$sql = "insert into exceptions values('', '$ModuleId', '$userid', 1, 1)";
			$result = DBUtil::query($sql);
            if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permission")); 
			}
		}
		else if($ActionName == 'add_ex_ownoff')
		{
			$sql = "insert into exceptions values('', '$ModuleId', '$userid', 0, 1)";
			$result = DBUtil::query($sql);
            if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permission")); 
			}
		}
		else if($ActionName == 'del_ex')
		{
			$sql = "delete from exceptions where module_id='$ModuleId' and user_id='$userid' limit 1";
			$result = DBUtil::query($sql);
            if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permissiony!")); 
			}
			
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=>"Can't perform any action!")); 
		}
	 }
  }

   // Get Stage List By VIR
   function GetAllStagesForEditUser()
   {
      connect();
	 if(isset($_POST['UserID']))
	 {
		$userid = $_POST['UserID'];
        $accountid = $_SESSION['ao_accountid'];
		$levelid = $_SESSION['ao_level'];
		
		$stagesSql = "SELECT * FROM stages WHERE account_id =$accountid order by stage_num asc";        
		$stageArray=   DBUtil::queryToArray($stagesSql);

		$stage_access = "SELECT * FROM stage_access WHERE account_id =$accountid and level_id =$levelid ";  
		$stage_accessArray=   DBUtil::queryToArray($stage_access);

		$user_stage_access = "SELECT * FROM user_stage_access WHERE account_id =$accountid and user_id =$userid ";  
		$user_stage_accessArray=   DBUtil::queryToArray($user_stage_access);
		
		return json_encode(array('stageArray' => $stageArray, 'stage_accessArray' => $stage_accessArray, 'user_stage_accessArray' => $user_stage_accessArray));
		
		}
   }

   // Update User Stage Advance Access By VIR
   function UpdateUserStageAdvAccessStatus()
   {
	
	 if(isset($_POST['UserID']) && isset($_POST['stageId']))
	 {
		$userid = $_POST['UserID'];
        $stageId = $_POST['stageId'];
		$accountid = $_SESSION['ao_accountid'];
		$levelid = $_SESSION['ao_level'];

		$user_stage_access = "SELECT * FROM user_stage_access WHERE user_id =$userid and stage_id = $stageId "; 
		$userStageaccess_Array =  DBUtil::queryToArray($user_stage_access);
		if(!empty($userStageaccess_Array))
		{			
				$sql = "DELETE FROM user_stage_access WHERE user_id = '$userid' AND stage_id = '$stageId' LIMIT 1";
				$result = DBUtil::query($sql);
				
				if($result == true)
				{
					return json_encode(array('status'=> 1,'message'=>"stage access updated successfully!")); 
				}
				else
				{
					return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update stage access!")); 
				}
		}
		else
		{
				//$stage_access = "SELECT * FROM user_stage_access WHERE stage_id = '$stageId' and level_id =$levelid ";  
				$stage_access = "SELECT * FROM user_stage_access WHERE user_id =$userid and stage_id = '$stageId'";  
				$stage_accessArray=   DBUtil::queryToArray($stage_access);

				
				if(!empty($userStageaccess_Array))
				{
					$sql = "INSERT INTO user_stage_access (user_id, account_id, stage_id, has_access) VALUES ('$userid', '$accountid', '$stageId', '1')";
					$result = DBUtil::query($sql);
					if($result == true)
					{
						return json_encode(array('status'=> 1,'message'=>"stage access updated successfully!")); 
					}
					else
					{
						return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update stage access!")); 
					}
				}
				else	
				{
					$sql = "INSERT INTO user_stage_access (user_id, account_id, stage_id) VALUES ('$userid', '$accountid', '$stageId')";
					$result = DBUtil::query($sql);
					if($result == true)
					{
						return json_encode(array('status'=> 1,'message'=>"stage access updated successfully!")); 
					}
					else
					{
						return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update stage access!")); 
					}
				}
		}
	 }
   }

   // Get Stage Notification List By VIR
   function BindStageNotificationDetails()
   {
      connect();
	 if(isset($_POST['UserID']))
	 {
		$userid = $_POST['UserID'];
        $accountid = $_SESSION['ao_accountid'];
		$levelid = $_SESSION['ao_level'];
		
		$stagesNotifySql = "SELECT * FROM stages WHERE account_id =$accountid order by stage_num asc";        
		$stageNotifyArray =   DBUtil::queryToArray($stagesNotifySql);

		$emailNotifications = "SELECT * FROM stage_notifications WHERE user_id =$userid ";  
		$emailNotifyArray =   DBUtil::queryToArray($emailNotifications);

		$smsNotifications = "SELECT * FROM stage_notifications_sms WHERE user_id =$userid ";  
		$smsNotifyArray =   DBUtil::queryToArray($smsNotifications);
		
		return json_encode(array('stageNotifyArray' => $stageNotifyArray, 'emailNotifyArray' => $emailNotifyArray, 'smsNotifyArray' => $smsNotifyArray));
		
		}
   }

   //Update User Permission By VIR
  function UpdateUserStageNotificationStatus()
  {
	
	 if(isset($_POST['action']) && isset($_POST['stageNum']) && isset($_POST['UserID']))
	 {
		$actionName = $_POST['action'];
		$stageNum = $_POST['stageNum'];
		$userid = $_POST['UserID'];
		
		if($actionName == 'emailAction')
		{
			$emailNotifications = "SELECT * FROM stage_notifications WHERE user_id =$userid ";  
			$emailNotifyArray =   DBUtil::queryToArray($emailNotifications);
			if(!empty($emailNotifyArray))
			{
				$sql = "DELETE FROM stage_notifications WHERE stage_num = '$stageNum' AND user_id='$userid' LIMIT 1";
				$result = DBUtil::query($sql);

			}
			else	
			{
				$sqlInsertEmailNotify = "INSERT INTO stage_notifications (stage_num, user_id) VALUES ('$stageNum', '$userid')";
				DBUtil::query($sqlInsertEmailNotify);
			}
			
		    return json_encode(array('status'=> 1,'message'=>"stage email notification update successfully!")); 
		}
		else if($actionName == 'smsAction')
		{
			$smsNotifications = "SELECT * FROM stage_notifications_sms WHERE user_id =$userid ";  
			$smsNotifyArray =   DBUtil::queryToArray($smsNotifications);
			if(!empty($smsNotifyArray))
			{
				$sql = "DELETE FROM stage_notifications_sms WHERE stage_num = '$stageNum' AND user_id='$userid' LIMIT 1";
				$result = DBUtil::query($sql);
			}
			else
			{
				$sqlInsertSmsNotify = "INSERT INTO stage_notifications_sms (stage_num, user_id) VALUES ('$stageNum', '$userid')";
				DBUtil::query($sqlInsertSmsNotify);
			}
			
		    return json_encode(array('status'=> 1,'message'=>"stage SMS notification update successfully!")); 
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=>"Can't perform any action!")); 
		}
	 }
  }



//View Appoinment Detail By VIR
 function ViewAppoinmentDetail()
 {
	if(isset($_POST['ApptID']))
	{
		$ApptId = $_POST['ApptID'];
		$appointment = "SELECT ap.job_id ,ap.title,  concat(u.lname, ', ' ,u.fname) as Creator, ap.datetime, j.job_number,  concat(ut.lname, ', ' ,ut.fname) as salesman, ap.text, ap.timestamp
						FROM appointments ap 
						left join users u on ap.user_id = u.user_id
						left join jobs j on ap.job_id = j.job_id
						left join users ut on j.salesman = ut.user_id
						where ap.appointment_id = '$ApptId'";
		
		$apptArray =   DBUtil::queryToArray($appointment);
	    return json_encode(array('apptArray'=> $apptArray)); 
	}
 }

  // Bind Dropdown list for Add Job Repair By VIR
 function GetAddJobRepairDDList()
 {      
		$firstLast = UIUtil::getFirstLast();
        $failTypes = JobUtil::getAllFailTypes();
		$priorities = JobUtil::getAllProrities();
		$showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');
		$dropdownUserLevels = AccountModel::getMetaValue('assign_repair_contractor_user_dropdown');
		$contractors = !empty($dropdownUserLevels) 
                ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast)
                : UserModel::getAll($showInactiveUsers, $firstLast);
		$contractors = UserUtil::sortUsersByDBA($contractors);

        return json_encode(array("failTypes"=> $failTypes,"priorities" => $priorities,"contractors" => $contractors));
       
 }
 //SavedAddRepairJob By VIR
 function SavedAddRepairJob()
 {
	if(isset($_POST['JobID']) && isset($_POST['failType']) && isset($_POST['priority']) && isset($_POST['contractor']) && isset($_POST['notes']))
	{
		$jobId = $_POST['JobID'];
		$failType = $_POST['failType'];
		$priority = $_POST['priority'];
		$contractor = $_POST['contractor'];
		$notes = $_POST['notes'];
		$accountid = $_SESSION['ao_accountid'];
        $userid = $_SESSION['ao_userid'];

		$sql = "INSERT INTO repairs
                VALUES (NULL, '$jobId', '$accountid', '$userid', '$contractor', '$priority', '$failType', '$notes', NULL,  now(), NULL)";
        $result = DBUtil::query($sql);

		if($result == true)
		{
			UserModel::startWatchingConversation($jobId, 'job');
			if($contractor) {
				UserModel::startWatchingConversation($jobId, 'job', $contractor);
			}

			JobModel::saveEvent($jobId, 'Added New Repair');
			NotifyUtil::notifySubscribersFromTemplate('add_repair', $userid, array('job_id' => $jobId));

			return json_encode(array('status'=> 1,'message'=>"Repair job added successfully!")); 

		}
		else	
		{
			return json_encode(array('status'=> 0,'message'=>"There are some error while adding repair job.")); 
		}
	}
 }
 //GetSupplierForJobMatSheet By VIR
  function GetSupplierForJobMatSheet()
  {
	 
	 $suppliers = MaterialModel::getAllSuppliers();
	 return json_encode(array("supplierList"=> $suppliers));
  }
  //SavedJobMaterialSheet By VIR
  function SavedJobMaterialSheet()
  {
	if(isset($_POST['JobID']) && isset($_POST['supplier']) && isset($_POST['label']) && isset($_POST['size']) && isset($_POST['notes']))
	{
		$jobId = $_POST['JobID'];
		$supplier = $_POST['supplier'];
		$label = $_POST['label'];
		$size = $_POST['size'];
		$notes = $_POST['notes'];
		$accountid = $_SESSION['ao_accountid'];
        $userid = $_SESSION['ao_userid'];

		$sql = "INSERT INTO sheets
                VALUES (NULL, '$jobId', '$userid', '$accountid', $supplier, '$label', '$size', '$notes', NULL, NULL, now())";
		$result = DBUtil::query($sql);
		$newSheetId = DBUtil::getInsertId();
        
		if($result == true)
		{
			
			try {
					$materialForm = new MaterialForm();
					$materialForm->initByUploadId($uploadId);
				}
		   catch (Exception $e) {
					$newForm = TRUE;
					$materialForm = new MaterialForm();
					$materialForm->setType('material');
				}

			$myJob = new Job($materialForm->exists ? $materialForm->getJobId() : $jobId);
			$myCustomer = new Customer($myJob->customer_id);
			$materialForm->setJobId($jobId)->setUploadId(-1)->store();

			foreach($_POST as $key => $dataPoint) {
				JobModel::setMetaValue($materialForm->getMyMetaId(), "material_sheet_$key", $dataPoint);
			}

			$viewData = array('meta_data' => $materialForm->getMetaData(), 'job_number' => $myJob->job_number, 'logo' => LOGOS_PATH . '/' . $_SESSION['ao_logo']);
			$html = ViewUtil::loadView('pdf/material-sheet', $viewData);
			$fileName = PdfUtil::generatePDFFromHtml($html, 'Material Sheet', true, UPLOADS_PATH);
			$title = RequestUtil::get('upload_title', $label);

			$sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
                VALUES ('$jobId', '$userid', '$accountid', '$fileName', '$title', now())";
			DBUtil::query($sql);
			$materialForm->setUploadId(DBUtil::getInsertId())->store();

			return json_encode(array('status'=> 1,'message'=>"Job material sheet added successfully!")); 

		}
		else	
		{
			return json_encode(array('status'=> 0,'message'=>"There are some error while adding Job material sheet.")); 
		}
		
	}
  }
  //RemoveAppointmentByID By VIR
  function RemoveAppointmentByID()
  {
	if(isset($_POST['ApptId']))
	{	
		$apptId = $_POST['ApptId'];
		$apptSql = "SELECT * FROM appointments WHERE appointment_id =$apptId ";  
			$apptArray =   DBUtil::queryToArray($apptSql);
			if(!empty($apptArray))
			{
				
				$jobId = $apptArray[0]['job_id'];
				$sql = "DELETE FROM appointments WHERE appointment_id = $apptId LIMIT 1";
				$result = DBUtil::query($sql);

				return json_encode(array('status'=> 1,'message'=>"Appointment removed successfully!",'jobId' => $jobId)); 
			}
			else
			{
				return json_encode(array('status'=> 0,'message'=>"Appointment not found!")); 
			}
	}
  }
  //GetRepairJobDetailForView By VIR
 function GetRepairJobDetailForView()
 {
	if(isset($_POST['RepairEditJobID']))
	{	
		$repairEditJobId = $_POST['RepairEditJobID'];  
		$todayDate = date("M d,Y");
		$myRepair = new Repair($repairEditJobId);

		$repairJobSql = "SELECT rj.*, date_format(rj.completed, '%M-%d-%Y') as formatDate, date_format(rj.completed, 'M d,Y') as formatDate2, rj.fail_type as failTypeVal, rj.priority as propVal, rj.contractor as contVal ,concat(u.lname, ', ' ,u.fname) as Creator, ft.fail_type, p.priority, CASE WHEN concat(ut.fname, ' ' ,ut.lname) IS NULL THEN '' ELSE concat(ut.fname, ' ' ,ut.lname) END as contractor, concat(ut.fname, ' ' ,ut.lname) as contractor2, CASE WHEN ut.dba IS NULL THEN '' ELSE ut.dba END as dba FROM repairs rj
						 left join users u on u.user_id = rj.user_id 
						 left join fail_types ft on ft.fail_type_id = rj.fail_type
						 left join priority p on p.priority_id = rj.priority
						 left join users ut on ut.user_id = rj.contractor
						 WHERE rj.repair_id =$repairEditJobId ";  
		

			$repairJobArray =   DBUtil::queryToArray($repairJobSql);
		
			if(!empty($repairJobArray))
			{
				return json_encode(array('repairJobArray'=> $repairJobArray,'myRepair' => $myRepair)); 
			}
			else
			{
				return json_encode(array('repairJobArray'=> $repairJobArray,'myRepair' => $myRepair)); 
			}

	}
 }
 //DeleteRepairJobByRepairId By VIR
 function DeleteRepairJobByRepairId()
 {
	if(isset($_POST['RepairDelJobID']))
	{	
		$repairDelJobId = $_POST['RepairDelJobID'];
		$accountid = $_SESSION['ao_accountid'];
        $userid = $_SESSION['ao_userid'];

		$repairJobSql ="select repair_id, job_id, user_id, contractor  from repairs where repair_id = $repairDelJobId ";
		$repairJobArray =   DBUtil::queryToArray($repairJobSql);
		$jobId = $repairJobArray[0]['job_id'];
		if(!empty($repairJobArray))
		{
			$jobId = $repairJobArray[0]['job_id'];
			$userId = $repairJobArray[0]['user_id'];
			$conId = $repairJobArray[0]['contractor'];
			$repairid = $repairJobArray[0]['repair_id'];

			$delSql = "delete from repairs where repair_id='$repairDelJobId' limit 1";
			NotifyUtil::notifySubscribersFromTemplate('del_repair', '$userid', array('job_id' => $jobId, 'repair_id' => $repairid));
			DBUtil::query($delSql);
    
			$myJob = new Job($jobId);
			if(!$myJob->shouldBeWatching($userId)) {
				UserModel::stopWatchingConversation(job_id, 'job', $userId);
			}
			if(!$myJob->shouldBeWatching($conId)) {
				UserModel::stopWatchingConversation(job_id, 'job', $conId);
			}
			return json_encode(array('status'=> 1,'message'=>"Repair Job removed successfully!",'job_id' => $jobId)); 
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=>"Repair job not exist, can't remove it!",'job_id' => $jobId)); 
		}
		
	}
 }
 // SaveRepairJobEditDetail By VIR
 function SaveRepairJobEditDetail()
 {
	if(isset($_POST['RepairEditJobID']) && isset($_POST['fail_type']) && isset($_POST['priority']) && isset($_POST['contractor']) && isset($_POST['startDate']) && isset($_POST['completed']) && isset($_POST['notes']) && isset($_POST['isComplete']))
	{
		$repairId = $_POST['RepairEditJobID'];
		$fail_type = $_POST['fail_type'];
		$priority = $_POST['priority'];
		$contractor = $_POST['contractor'];
		$startDate = $_POST['startDate'];
		$completed = $_POST['completed'];
		$notes = $_POST['notes'];
		$accountid = $_SESSION['ao_accountid'];
        $userid = $_SESSION['ao_userid'];
		$isComplete = $_POST['isComplete'];

		$repairJobSql ="select * from repairs where repair_id = $repairId ";
		$repairJobArray =   DBUtil::queryToArray($repairJobSql);
		$jobId = $repairJobArray[0]['job_id'];
		$repCompltd = $repairJobArray[0]['completed'];
		$contractorId = $repairJobArray[0]['contractor'];

		$strDtcomplete = '';
		$strDtStart = '';

		if(!empty($repairJobArray))
		{	
			$dateComplete = '';
			if($completed == '1')
			{
				$dateComplete = date("Y-m-d h:m:s");
				
				$strDtcomplete = ", completed='$dateComplete'";
			}
			else	
			{
				$strDtcomplete = ", completed=NULL";
			}

			if($isComplete == '1')
			{
				$strDtStart = ", startdate='$startDate'";
			}
			else
			{
			    $strDtStart = ", startdate=NULL";
			}

			$sql = "update repairs set contractor='$contractor', priority='$priority', fail_type='$fail_type', notes='$notes' $strDtStart $strDtcomplete where repair_id = '$repairId' limit 1";
			$result = DBUtil::query($sql);
			
			if($result == "true")
			{
				
				JobModel::saveEvent($jobId, 'Repair Details Modified');
				if(empty($repCompltd) && $completed == '1') {
					NotifyUtil::notifySubscribersFromTemplate('repair_completed', $userid, array('job_id' => $jobId));
				} else {
					NotifyUtil::notifySubscribersFromTemplate('modify_repair', $userid, array('job_id' => $jobId));
					NotifyUtil::notifyFromTemplate('modify_repair', $contractorId, $userid, array('job_id' => $jobId));
				}

				if ($contractor != $contractorId) {
					if($contractor) {
					    NotifyUtil::notifyFromTemplate('repair_assigned',$contractor, $userid, array('job_id' => $jobId));
					}
					NotifyUtil::notifyFromTemplate('repair_unassigned', $contractorId, $userid, array('job_id' => $jobId));
					
					if($contractor) {
					    UserModel::startWatchingConversation($jobId, 'job', $contractor);
					}
					$myJob = new Job($jobId);
					if(!$myJob->shouldBeWatching($contractorId)) {
					    UserModel::stopWatchingConversation($jobId, 'job', $contractorId);
					}
				}

				return json_encode(array('status'=> 1,'message'=>"Repair Details Modified",'job_id' => $jobId)); 
			}
			else
			{
			    return json_encode(array('status'=> 0,'message'=>"Can't Modified Repair Details!")); 
			}
        }
		else
        {
                return json_encode(array('status'=> 0,'message'=>"Can't Modified Repair Details!"));
        }
	}
  }
  //GetJobMatSheetDetailBySheetId By VIR
  function GetJobMatSheetDetailBySheetId()
  {
	  if(isset($_POST['SheetId']) && isset($_POST['JobId']))
	  {
		 $sheetId = $_POST['SheetId'];
		 $jobId = $_POST['JobId'];

			$mySheet = new Sheet($sheetId);
		    $myJob = new Job($jobId);
		    
		    $supplierId = $mySheet->supplier_id;
		    $sql = "select contact, email, phone, fax from suppliers where supplier_id='$supplierId' limit 1";
		    $res =   DBUtil::queryToArray($sql);
		    
		    return json_encode(array("mySheet"=> $mySheet, "myJob"=> $myJob, "supplier" => $res));
       
	  }
  }
  //GetAllDDListForAddNewJob By VIR
  function GetAllDDListForAddNewJob()
  {
      $firstLast = UIUtil::getFirstLast();
      $accountMetaData = AccountModel::getAllMetaData();
      $accountMetaValue = AccountModel::getMetaValue('add_job_referral_user_dropdown');
      
      $customers = CustomerModel::getAllCustomers($firstLast);
      $origins = JobUtil::getAllOrigins();
      $salesmen = UserModel::getAll(FALSE, $firstLast);
      
      $sql = "SELECT user_id, fname, lname, dba, is_active, is_deleted, CONCAT(lname, ', ', fname) AS select_label
                FROM users
                WHERE account_id = '{$_SESSION['ao_accountid']}'
                AND is_active = 1 AND is_deleted = 0 ORDER BY lname ASC";
      $salesmen = DBUtil::queryToArray($sql);
      
      $salesmenByLevel = UserModel::getAllByLevel($accountMetaData['add_job_referral_user_dropdown']['meta_value'], FALSE, $firstLast);
      $jobType = JobUtil::getAllJobTypes();
      $providers = InsuranceModel::getAllProviders();
      $jurisdictions = CustomerModel::getAllJurisdictions();
      
      $stateArray = getStates();
      
      return json_encode(array("customers" => $customers, "origins" => $origins, "salesmen" => $salesmen, "salesmenByLevel" => $salesmenByLevel, "jobType" => $jobType, "providers" => $providers, "jurisdictions" => $jurisdictions,"accountMetaValue" => $accountMetaValue, "stateArray" => $stateArray));
  }
  //AddNewJobItem By VIR
  function AddNewJobItem()
  {
    if(isset($_POST['CustomerID']) && isset($_POST['OriginId']) && isset($_POST['Referral']) && isset($_POST['JobType']) && isset($_POST['JobTypeNote']) && isset($_POST['salesman']) && isset($_POST['Provider']) && isset($_POST['Jurisdiction']))
	  {
      $jobHash = md5(mt_rand() . mt_rand() . mt_rand());
      $jobNumber = strtoupper(substr(md5(rand() . rand()), 0, 8));
      
      $salesman = 'NULL';
      $provider = 'NULL';
      $referral = 'NULL';
      $jurisdiction = 'NULL';
      
      $existingCustomer = $_POST['CustomerID'];
      $salesman = $_POST['salesman'];
      $referral = $_POST['Referral'];
      $provider = $_POST['Provider'];
      $type = $_POST['JobType'];
      $note = $_POST['JobTypeNote'];
      $origin = $_POST['OriginId'];
      $jurisdiction = $_POST['Jurisdiction'];
      $accountid = $_SESSION['ao_accountid'];
      $userid= $_SESSION['ao_userid'];
      
              $sql = "INSERT INTO jobs VALUES (NULL, '$jobNumber', '$existingCustomer', '$accountid', 1, curdate(), 
                            '$userid', $salesman, $referral, NULL, $provider, NULL, NULL, 0, '$type',
                            '$note', '$origin', '$jurisdiction', NULL, now(), '$jobHash')";
		          $result = DBUtil::query($sql);

              if($result == "true")
              {
		               $newJobId = DBUtil::getInsertId();
                   
                   $myJob = new Job($newJobId);
                   $myJob->storeSnapshot();

                   UserModel::startWatchingConversation($newJobId, 'job');
		               if($salesman) {
			                    NotifyUtil::notifyFromTemplate('add_job_salesman', $salesman, NULL, array('job_id' => $newJobId));
                         UserModel::startWatchingConversation($newJobId, 'job', $salesman);
		               }

		               if($referral) {
			                    NotifyUtil::notifyFromTemplate('referral_assigned', $referral, NULL, array('job_id' => $newJobId));
                         UserModel::startWatchingConversation($newJobId, 'job', $referral);
		               }

		               JobModel::saveEvent($newJobId, 'Added New Job');
                   
                   return json_encode(array('status'=> 1,'message'=>"New Job Added Successfully!",'JobId' => $newJobId)); 
              }
              else
              {
                  return json_encode(array('status'=> 0,'message'=>"There are some error, can't add Job!")); 
              }
        
    
    }
  }
  //AddNewJobItemWithCustomer By VIR
  function AddNewJobItemWithCustomer()
  {
    if(isset($_POST['Fname']) && isset($_POST['Lname']) && isset($_POST['nickName']) && isset($_POST['address']) && isset($_POST['city']) && isset($_POST['state']) && isset($_POST['zip']) && isset($_POST['street']) && isset($_POST['Phone']) && isset($_POST['SecPhone']) && isset($_POST['Email']) && isset($_POST['CustomerID']) && isset($_POST['OriginId']) && isset($_POST['Referral']) && isset($_POST['JobType']) && isset($_POST['JobTypeNote']) && isset($_POST['salesman']) && isset($_POST['Provider']) && isset($_POST['Jurisdiction']))
    {
      $jobHash = md5(mt_rand() . mt_rand() . mt_rand());
      $jobNumber = strtoupper(substr(md5(rand() . rand()), 0, 8));
      
      $salesman = 'NULL';
      $provider = 'NULL';
      $referral = 'NULL';
      $jurisdiction = 'NULL';
      
      
      $Fname = $_POST['Fname'];
      $Lname = $_POST['Lname'];
      $nickName = $_POST['nickName'];
      $address = $_POST['address'];
      $city = $_POST['city'];
      $state = $_POST['state'];
      $zip = $_POST['zip'];
      $street = $_POST['street'];
      $Phone = $_POST['Phone'];
      $SecPhone = $_POST['SecPhone'];
      $Email = $_POST['Email'];
            
      $existingCustomer = $_POST['CustomerID'];
      $salesman = $_POST['salesman'];
      $referral = $_POST['Referral'];
      $provider = $_POST['Provider'];
      $type = $_POST['JobType'];
      $note = $_POST['JobTypeNote'];
      $origin = $_POST['OriginId'];
      $jurisdiction = $_POST['Jurisdiction'];
      $accountid = $_SESSION['ao_accountid'];
      $userid= $_SESSION['ao_userid'];
      
      $emailSql = mysqli_query("select email from users where email = '$Email' && user_id != $userid ");
        
		 if(mysqli_num_rows($emailSql))
     {
        return json_encode(array('status'=> 0,'message'=>"Email in use"));
     }
     else
     {
         if($existingCustomer == '' || $existingCustomer == 'null')
         {
              $newAddress = "$address $city $state $zip";
			        $gpsData = CustomerModel::getGPSCoords($newAddress);
              
			        $sql = "INSERT INTO customers
                            VALUES (NULL, '$accountid', '$Fname', '$Lname', '$nickName', '$userid',
                            '$address', '$city', '$state', '$zip', '{$gpsData[0]}', '{$gpsData[1]}', '$Phone',
                            '$SecPhone', '$Email', '$street', now())";
			        DBUtil::query($sql);
              
			        $existingCustomer = DBUtil::getInsertId();
         }
         
              $sql = "INSERT INTO jobs VALUES (NULL, '$jobNumber', '$existingCustomer', '$accountid', 1, curdate(), 
                            '$userid', $salesman, $referral, NULL, $provider, NULL, NULL, 0, '$type',
                            '$note', '$origin', '$jurisdiction', NULL, now(), '$jobHash')";
		          $result = DBUtil::query($sql);

              if($result == "true")
              {
		               $newJobId = DBUtil::getInsertId();
                   
                   $myJob = new Job($newJobId);
                   $myJob->storeSnapshot();

                   UserModel::startWatchingConversation($newJobId, 'job');
		               if($salesman) {
			                    NotifyUtil::notifyFromTemplate('add_job_salesman', $salesman, NULL, array('job_id' => $newJobId));
                         UserModel::startWatchingConversation($newJobId, 'job', $salesman);
		               }

		               if($referral) {
			                    NotifyUtil::notifyFromTemplate('referral_assigned', $referral, NULL, array('job_id' => $newJobId));
                         UserModel::startWatchingConversation($newJobId, 'job', $referral);
		               }

		               JobModel::saveEvent($newJobId, 'Added New Job');
                   
                   return json_encode(array('status'=> 1,'message'=>"New Job Added Successfully!",'JobId' => $newJobId)); 
              }
              else
              {
                  return json_encode(array('status'=> 0,'message'=>"There are some error, can't add Job!")); 
              }
        
      }
    }
  }
  //UpdateJobStageID By VIR
  function UpdateJobStageID()
  {
	if(isset($_POST['JobId']) && isset($_POST['StageNum']))
	  {
		    $jobId = $_POST['JobId'];
		    $stageNum = $_POST['StageNum'];
		    $new_stage = '';
			if($stageNum != ''){
			
				$new_stage = $stageNum;
			}
			else{
			
				$sql ="select stage_num from job_stages where job_id = '$jobId' order by job_stage_num asc";
				$job_stages = DBUtil::queryToArray('$sql');
				
				foreach($job_stages as $key => $job_stage){
					if($stageNum == $job_stage['stage_num']){
						$new_stage = $job_stages[$key + 1]['stage_num'];
					}
				}
			} 

			if($new_stage != '')
			{
				$sql = "update jobs set stage_num='$new_stage', stage_date=curdate() where job_id='$jobId' limit 1";
				$result = DBUtil::query($sql);
				
				JobModel::saveEvent($jobId, "Moved to stage $new_stage");
				
				NotifyUtil::notifySubscribersFromTemplate('stage_moved', null, array('job_id' => $jobId), true);

				if($result == "true")
				{
					return json_encode(array('status'=> 1,'message'=>"stage_moved!", 'jobId' => $jobId)); 
				}
				else
				{
					return json_encode(array('status'=> 0,'message'=>"There are some error, can't moved stage!")); 
				}
			}
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error, can't moved stage!")); 
			}
		}
  }

  //Bind Upload Insurance PDF detail By VIR
  function GetUploadFilesForSelectedJob()
  {
	 
	 if(isset($_POST['JobID']))
	 {
	  $jobId = $_POST['JobID'];

	  $sql = "SELECT up.*, u.fname, u.user_id, u.lname, concat(u.fname, ', ' ,u.lname) as FullName, sf.type, date_format( up.timestamp, '%b %d, %Y \@ %h:%i %p' ) as timestamp FROM uploads up LEFT JOIN users u ON u.user_id = up.user_id
			  LEFT JOIN schedule_forms sf on sf.upload_id = up.upload_id
			  WHERE up.job_id='$jobId' AND up.active = 1 ORDER BY up.timestamp DESC";
	 
	  $uploads_array = DBUtil::queryToArray($sql);
	  
	  
	  	$file_path ='http://'. $_SERVER['SERVER_NAME']. "/uploads/modify_material_sheet_1444038798.pdf";
		$fs = filesize($file_path);
		
		
		foreach($uploads_array as &$origin) {
			$origin['file_size']='180kb';
			$uploadType = JobUtil::getUploadType($origin['filename']);
			$origin['IMGuploadType'] = $uploadType;

			$file_path = "http://workflow365.co/uploads/modify_material_sheet_1444038798.pdf";
			$origin['file_size2'] = ceil(filesize($file_path) / 1000);

		}

	  if(!empty($uploads_array))
	  {			
			return json_encode(array('uploads_array' => $uploads_array, 'uploadType' => $uploadType));
	  }
	  else
      {
			return json_encode(array('uploads_array' => $uploads_array));
	  }
	}
  }
  //updateJobUploadFileTitle By VIR
  function updateJobUploadFileTitle()
  {
     
	 if(isset($_POST['JobID']) && isset($_POST['UploadID']) && isset($_POST['Title']))
	 {
		$jobId = $_POST['JobID'];
		$uploadId = $_POST['UploadID'];
		$title = $_POST['Title'];

		$sql = "SELECT * FROM uploads where upload_id ='$uploadId' AND job_id='$jobId'";
		$result = DBUtil::query($sql);

	    if(mysqli_num_rows($result))
		{	
            $sql = "update uploads set title='$title' where upload_id ='$uploadId' limit 1";
			$result = DBUtil::query($sql);
			if($result == "true")
			{
			    return json_encode(array('status'=> 1,'message'=>"title updated successfully!")); 
			}
			else
			{
			    return json_encode(array('status'=> 0,'message'=>"Can't updated!")); 
			}
        }
		else
        {
                return json_encode(array('status'=> 0,'message'=>"file not exist!"));
        }
	 }
  }
  // deleteJobUploadFile By VIR
  function deleteJobUploadFile()
  {
     if(isset($_POST['uploadId']))
	 {
		$uploadId = $_POST['uploadId'];
		
		$sql = "SELECT filename FROM uploads where upload_id ='$uploadId'";
		$result = DBUtil::query($sql);
		
	    if(mysqli_num_rows($result))
		{	
            list($filename) = mysqli_fetch_row($result);
			unlink(UPLOADS_PATH . '/' . $filename);
			
            //$sql = "UPDATE uploads SET active = 0 WHERE upload_id = '$uploadId'";
			$sql = "DELETE FROM uploads WHERE upload_id = '$uploadId' LIMIT 1";
			$result = DBUtil::query($sql);

			if($result == "true")
			{
				LogUtil::getInstance()->logNotice(UserModel::getUserDetailsForLogger() . " deactivated upload ID: $uploadId");
			    return json_encode(array('status'=> 1,'message'=>"Upload succesfully removed!")); 
			}
			else
			{
			    return json_encode(array('status'=> 0,'message'=>"Operation failed!")); 
			}
        }
		else
        {
                return json_encode(array('status'=> 0,'message'=>"Invalid reference"));
        }
	 }
  }
  // getMaterialsListBySheetID By VIR
  function getMaterialsListBySheetID()
  {
	if(isset($_POST['sheetId']))
	{
		$sheetId = $_POST['sheetId'];
		
		$sql = "select sheet_items.sheet_item_id, sheet_items.quantity, materials.material, CASE WHEN colors.color IS NULL THEN '' ELSE colors.color END AS color , materials.price, truncate((materials.price * sheet_items.quantity),2) as total, units.unit, materials.brand_id
                from units,materials,sheet_items
                left join colors on colors.color_id = sheet_items.color_id
				where units.unit_id=materials.unit_id and sheet_items.sheet_id='$sheetId' and sheet_items.material_id=materials.material_id";
        $res = DBUtil::queryToArray($sql);	
		if (!empty($res))
        {	
			foreach($res as &$brand) 
			{
				$brandId = $res[0]['brand_id'];
				if($brandId != '-1')
				{
					$sqlBrand = "select brand from brands where brand_id = '$brandId'";
					$resBrand = DBUtil::queryToArray($sqlBrand);	
					$brand['brand']=$resBrand[0]['brand'];
				}
				else
				{	
					$brand['brand']='varies';
				}
			}
			return json_encode(array('matArray'=> $res, 'status'=> 1));
		}
		else
		{
			return json_encode(array('status'=> 0,'matArray'=> $res,'message'=>"No Materials"));
		}
   }
  }
  // updateMatSheetItemDetail By VIR
  function updateMatSheetItemDetail()
  {
     if(isset($_POST['sheetId']) && isset($_POST['action']) && isset($_POST['jobId']))
	 {
		$sheetId = $_POST['sheetId'];
		$action = $_POST['action'];
		$jobId = $_POST['jobId'];
		
		$sql = "SELECT * FROM sheet_items where sheet_item_id ='$sheetId'";
		$result = DBUtil::queryToArray($sql);
		$qty = $result[0]['quantity'];
		
	    if(!empty($result))
		{	
			if($action == 'plus')
			{	
				$qty = $qty + 1;
				$sql = "update sheet_items set quantity='$qty' where sheet_item_id='$sheetId' limit 1";
				DBUtil::query($sql);
				JobModel::saveEvent($sheetId, "Modified Material Quantity");
				return json_encode(array('status'=> 1,'message'=>"Modified Material Quantity!")); 
			}
			else if($action == 'minus')
			{	
				$qty = $qty - 1.00;
				if($qty > 0)
				{	
					$sql = "update sheet_items set quantity='$qty' where sheet_item_id='$sheetId' limit 1";
					DBUtil::query($sql);
					JobModel::saveEvent($sheetId, "Modified Material Quantity");
					return json_encode(array('status'=> 1,'message'=>"Modified Material Quantity!")); 
			    }
				else
				{
					$sql = "delete from sheet_items where sheet_item_id='$sheetId' limit 1";
					DBUtil::query($sql);
					JobModel::saveEvent($sheetId, "Deleted Materials");
					return json_encode(array('status'=> 1,'message'=>"Deleted Materials!")); 	
				}
			}
			else if($action == 'del')
			{
				$sql = "delete from sheet_items where sheet_item_id='$sheetId' limit 1";
				DBUtil::query($sql);
				JobModel::saveEvent($sheetId, "Deleted Materials");
				return json_encode(array('status'=> 1,'message'=>"Deleted Materials!")); 	
			}
		}
		else
        {
                return json_encode(array('status'=> 0,'message'=>"Material sheet not found!"));
        }
	 }
  }
  // ModifyJobMaterialSheet By VIR
  function ModifyJobMaterialSheet()
  {
	if(isset($_POST['job_id']) && isset($_POST['sheet_id']) && isset($_POST['supplier']) && isset($_POST['label']) && isset($_POST['notes']) && isset($_POST['deliveryDate']) && isset($_POST['confirm']))
	{
		$JobId = $_POST['job_id'];
		$sheetId = $_POST['sheet_id'];
		
		$supplier = $_POST['supplier'];
		$label = $_POST['label'];
		$notes = $_POST['notes'];
		$deliveryDate = $_POST['deliveryDate'];
		$confirm = $_POST['confirm'];
		$accountid = $_SESSION['ao_accountid'];
        $userid = $_SESSION['ao_userid'];
		$myJob = new Job($_POST['job_id']);
		

		if($confirm == '1')
			{
				$dateComplete = date("Y-m-d h:m:s");
				$strDtcomplete = ", confirmed='$dateComplete'";
			}
			else	
			{
				$strDtcomplete = ", confirmed=NULL";
			}
		if($deliveryDate != 'null')
			{
				$strdDate = ", confirmed='$deliveryDate'";
			}
			else	
			{
				$strdDate = ", confirmed=NULL";
			}

		$sql = "UPDATE sheets SET label = '$label' $strDtcomplete $strdDate, notes = '$notes', supplier_id ='$supplier' WHERE sheet_id='$sheetId' LIMIT 1";
		$result = DBUtil::query($sql);
		
		if($result == "1")
		{
			try {
					$modifymaterialForm = new ModifyMaterialForm();
					$modifymaterialForm->initByUploadId($uploadId);
				}
		    catch (Exception $e) {
					$newForm = "TRUE";
					
					$modifymaterialForm = new ModifyMaterialForm();
					$modifymaterialForm->setType('material');
			}

			if(!$modifymaterialForm->exists) {
				$modifymaterialForm->setJobId($JobId)->setUploadId(-1)->store();
			}  
			 
			foreach($_POST as $key => $dataPoint) {
				JobModel::setMetaValue($modifymaterialForm->getMyMetaId(), "modify_material_sheet_$key", $dataPoint);
			}
			
			 $viewData = array(
					'meta_data' => $modifymaterialForm->getMetaData(),
					'job_number' => $myJob->job_number,
					'logo' => LOGOS_PATH . '/' . $_SESSION['ao_logo']
			);
			
			$html = ViewUtil::loadView('pdf/modify-material-sheet', $viewData);
			$fileName = PdfUtil::generatePDFFromHtml($html, 'Modify Material Sheet', true, UPLOADS_PATH);
			$title = RequestUtil::get('upload_title', $label);
						
			if($newForm)
			{
				 $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
					VALUES ('$JobId', '$userid', '$accountid', '$fileName', '$title', now())";
                 DBUtil::query($sql);
				 $modifymaterialForm->setUploadId(DBUtil::getInsertId())->store();	 
			}
			
			return json_encode(array('status'=> 1,'message'=>"Job material sheet modified successfully!")); 

		}
		else	
		{
			return json_encode(array('status'=> 0,'message'=>"There are some error while modifying Job material sheet.")); 
		}
		
	}
  }
  // getAllJobMatSheet By VIR
  function getAllJobMatSheet()
  {
	
		$JobId= $_POST['JobId'];
		$myJob=new Job($JobId);
		$hasAccess = ModuleUtil::checkJobModuleAccess('job_material_sheet', $myJob);
		$canDelete = ModuleUtil::checkJobModuleAccess('delete_material_sheet', $myJob);
		$materialSheets = $myJob->fetchMaterialSheets();

		return json_encode(array('hasAccess'=> $hasAccess, 'canDelete'=> $canDelete, 'matSheetArray'=> $materialSheets));
  }
  // RemoveMaterialsSheetBySheetID By VIR
  function RemoveMaterialsSheetBySheetID()
  {
		$JobId= $_POST['JobId'];
		$sheetId= $_POST['sheetId'];
		$mySheet = JobUtil::getMaterialSheetById($sheetId);
		$myJob=new Job($JobId);
		
		if (!$mySheet || !$myJob->exists()) {
			return json_encode(array('status'=> 0,'message'=>"Invalid reference" ));
		}
		else
		{
			if(!ModuleUtil::checkJobModuleAccess('delete_material_sheet', $myJob)) {
					return json_encode(array('status'=> 0,'message'=>"Invalid permissions" ));
			}
			else
			{
				$sql = "DELETE FROM sheets WHERE sheet_id = '$sheetId' LIMIT 1";
				$results_sheets = DBUtil::query($sql);
				$sql = "DELETE FROM sheet_items WHERE sheet_id = '$sheetId' LIMIT 1";
				$results_sheet_items = DBUtil::query($sql);
				
				if (!$results_sheets || !$results_sheet_items) {
					return json_encode(array('status'=> 0,'message'=>"Operation failed!" ));
				} else {
					return json_encode(array('status'=> 1,'message'=>"Material sheet succesfully removed" ));
				}
			}
		}
  }
  // Get List for Material Category By VIR
 function GetCatListforJobMaterial()
 { 
       $loginaccountid = $_SESSION['ao_accountid'];
       $sql= "select category_id, category from categories where account_id=$loginaccountid order by category asc";
       $res = DBUtil::queryToArray($sql);	
         
	   return json_encode(array('matCategory'=> $res));
 } 
   // Get List for Material Category By VIR
 function GetColorListByMaterialId()
 { 
       $material_id = $_POST['material_id'];
       $sql= "select * from colors where material_id = $material_id order by color asc";
	   $res = DBUtil::queryToArray($sql);	

	   $matSql= "select m.*, u.unit from materials m
			  left join units u on u.unit_id = m.unit_id
			  where m.material_id = $material_id";
       $matArray = DBUtil::queryToArray($matSql);	
         
	   return json_encode(array('colorArray'=> $res, 'matArray' => $matArray));
 }
 // InsertNewMatSheetItem By VIR
 function InsertNewMatSheetItem()
 {
	if(isset($_POST['SheetId']) && isset($_POST['MatId']) && isset($_POST['ColorId']) && isset($_POST['qty']))
	{
		$sheetID = $_POST['SheetId'];
		$matID = $_POST['MatId'];
		$colorID = $_POST['ColorId'];
		$qty = $_POST['qty'];
		$userid = $_SESSION['ao_userid'];

		$sql = "insert into sheet_items values('', '$sheetID', '$userid', '$matID', '$colorID', '$qty', now())";
        $result = DBUtil::query($sql);

		if($result == "true")
		{
            $sql = "select job_id from sheets where sheet_id='$sheetID' limit 1";
            $res = DBUtil::query($sql);
            list($id)=mysqli_fetch_row($res);
			JobModel::saveEvent($id, "Added Materials");
			return json_encode(array('status'=> 1,'message'=>"Added Materials" ));
		}
		else
		{
			return json_encode(array('status'=> 1,'message'=>"There are error while add material sheet item!" ));
		}
	}
 }
 // GenerateMatSheetPrintForm By VIR
 function GenerateMatSheetPrintForm()
 {
	if(isset($_POST['JobId']) && isset($_POST['SheetId']))
	{
		$sheetID = $_POST['SheetId'];
		$jobID = $_POST['JobId'];
		$userid = $_SESSION['ao_userid'];
		$accountid = $_SESSION['ao_accountid'];
		
		if(ModuleUtil::checkAccess('job_material_sheet'))
		{
			$myJob = new Job($jobID);
			if(moduleOwnership('job_material_sheet') && (!JobUtil::isSubscriber($myJob->job_id) && $myJob->salesman_id!=$userid && $myJob->user_id!=$userid))
			{
				return json_encode(array('status'=> 0,'message'=>"Insufficient Rights!" ));
			}
			else
			{
				$myCustomer = new Customer($myJob->customer_id);
				$me = UserModel::getMe();
				$addressObj = $me->get('office_id') ? new Office($me->get('office_id')) : new Account($accountid);
				ob_start();
				$strPrintForm = '<html><body>';
				$strPrintForm = $strPrintForm. '<table border="0" cellspacing="0" cellpadding="0" width="800" align="center">';
				$mySheet = new Sheet($sheetID);
				$strPrintForm = $strPrintForm. '<tr valign=bottom><td align="center">'.AccountModel::getLogoImageTag().'<br>'.$addressObj->getFullAddress().'<br>Phone: '.UIUtil::formatPhone($addressObj->get('phone'));
				if($addressObj->get('fax')) 
				{
					$strPrintForm = $strPrintForm. '<br><b>Fax:</b> '.UIUtil::formatPhone($addressObj->get('fax'));
				}
				$strPrintForm = $strPrintForm. '</td><td style="font-size: 35px; font-weight: bold;" width=800 align="right">Material Order Summary</td></tr></table>';
				$strPrintForm = $strPrintForm. '<table width=800 align="center"  cellspacing="0" cellpadding="0">';
				$strPrintForm = $strPrintForm. '<tr><td style="border: 1px solid black;"><table width="100%" style="font-size: 16px;"><tr><td width=100><b>Customer:</b></td><td width=300>'.$myCustomer->getDisplayName().'</td><td width=120><b>Job Number:</b></td><td width=300>'.$myJob->job_number.'</td></tr>';
				$strPrintForm = $strPrintForm. '<tr><td width=100><b>Address:</b></td><td colspan=3>'.$myCustomer->getFullAddress().'</td></tr>';
				$strPrintForm = $strPrintForm. '<tr><td width=100><b>Phone:</b></td><td width=300>'.UIUtil::formatPhone($myCustomer->get('phone')).'</td><td width=120><b>Email:</b></td><td width=300>'.$myCustomer->get('email').'</td></tr></table></td></tr><tr><td>&nbsp;</td></tr>';
				$strPrintForm = $strPrintForm. '<tr><td style="border: 1px solid black;"><table width="100%" style="font-size: 16px;" border="0"><tr><td width=100><b>Salesman:</b></td><td width=300>'.$myJob->salesman_fname." ".$myJob->salesman_lname.'</td><td width=120><b>Phone:</b></td><td width=300>'.UIUtil::formatPhone(UserModel::getProperty($myJob->salesman_id, 'phone')).'</td></tr>';
				$strPrintForm = $strPrintForm. '<tr><td width=100><b>Job Type:</b></td><td width=300>'.$myJob->job_type.'</td><td width=120><b>Job DOB:</b></td><td width=300>'.$myJob->dob.'</td></tr>';
				$strPrintForm = $strPrintForm. '<tr><td width=100><b>Jurisdiction:</b></td><td width=300>'.$myJob->jurisdiction.'</td><td width=120><b>Permit #:</b></td><td width=300>'.$myJob->permit.'</td></tr>';
				$strPrintForm = $strPrintForm. '<tr><td width=100><b>Order Notes:</b></td><td colspan=3>'.UIUtil::cleanOutput($mySheet->notes, FALSE).'</td></table></td></tr>';
				if(!empty($mySheet->supplier_id))
				{
					$strPrintForm = $strPrintForm. '<tr><td>&nbsp;</td></tr><tr><td style="border: 1px solid black;"><table width="100%" style="font-size: 16px;">';
					$strPrintForm = $strPrintForm. '<tr><td width=100><b>Supplier:</b></td><td width=300>'.$mySheet->supplier_name.'</td><td width=120><b>Phone:</b></td><td width=300>'.UIUtil::formatPhone($mySheet->supplier_phone).'</td></tr>';
					$strPrintForm = $strPrintForm. '<tr><td width=100><b>Contact:</b></td><td width=300>'.$mySheet->supplier_contact.'</td><td width=120><b>Fax:</b></td><td width=300>'.UIUtil::formatPhone($mySheet->supplier_fax).'</td></tr>';
					$strPrintForm = $strPrintForm. '<tr><td width=100><b>Email:</b></td><td colspan=3>'.$mySheet->supplier_email.'</td></tr></table></td></tr>';
				}
				$strPrintForm = $strPrintForm. '<tr><td>&nbsp;</td></tr>tr><td><table width="100%" border="0" style="font-size: 16px;"><tr><td width=90><b>Order Date:</b></td><td width=120>'.DateUtil::formatDate().'</td><td width=110><b>Delivery Date:</b></td><td>';
				if($mySheet->delivery_date!='')
				{
					$strPrintForm = $strPrintForm.DateUtil::formatDate($mySheet->delivery_date);
				}
				$strPrintForm = $strPrintForm. '</td></td></table></td></tr>';
				$strPrintForm = $strPrintForm. '<tr><td style="border: 1px solid black;"><table border="0" width="100%" style="font-size: 16px; font-weight: bold;"><tr><td width=60 align="center">Qty</td><td>Item</td><td width=200>Manufacturer</td><td width=200>Color</td><td width=75 align="right">Cost</td><td width=10>&nbsp;</td></tr></table></td></tr>';
				$strPrintForm = $strPrintForm. '<tr height=200 valign=top><td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">';
				$strPrintForm = $strPrintForm. '<table border="0" width="100%" cellpadding=2 cellspacing="0">';
				$sql = "select materials.material_id, materials.material, sheet_items.quantity, colors.color, materials.price from materials, sheet_items
						left join colors on (sheet_items.color_id=colors.color_id)
						where sheet_items.material_id=materials.material_id and sheet_items.sheet_id='$sheetID'";
						$res = DBUtil::query($sql);
						$i=1;
						$total_qty=0;
						$total_cost=0;
				while(list($material_id, $material, $qty, $color, $price)=mysqli_fetch_row($res))
				{
						$sql = "select brands.brand from brands, materials where brands.brand_id=materials.brand_id and materials.material_id='".$material_id."' limit 1";
						$res_brand = DBUtil::query($sql);
						list($brand)=mysqli_fetch_row($res_brand);
	
						$class='odd';
						if($i%2==0)
						  $class='even';

						$material = stripslashes($material);

						$total_cost += ($price*$qty);
						$price = number_format(($price*$qty), 2, '.', '');

						$strPrintForm = $strPrintForm. '<tr class='.$class.'><td width=60 align="center">'.$qty.'</td><td><b>'.$material.'</b></td><td width=200>'.$brand.'</td><td width=200>'.$color.'</td><td width=75 align="right">$ '.$price.'</td><td width=10>&nbsp;</td></tr>';
						
						$i++;
						$total_qty++;
			    }
				$total_cost = number_format($total_cost, 2, '.', '');

				$strPrintForm = $strPrintForm. '</table></td></tr><tr valign=top><td style="border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">';
				$strPrintForm = $strPrintForm. '<table border="0" width="100%" cellspacing="0" cellpadding=2>';
				$strPrintForm = $strPrintForm. '<tr class=odd><td width=60 align="center" style="font-size: 16px;"><b>'.$total_qty.'</b></td>';
				$strPrintForm = $strPrintForm. '<td style="font-size: 16px;">Total Items</td><td align="right" style="font-size: 16px;"><b>$ '.$total_cost.'</b></td>';
				$strPrintForm = $strPrintForm. '<td width=10>&nbsp;</td></tr></table></td></tr></table><br /><br />';
				$strPrintForm = $strPrintForm. '<table border="0" width=800 align="center"><tr><td><center>Generated by <b>'.APP_NAME.'</b></center></td></tr></table>';
				
				$strPrintForm = $strPrintForm. '</body></html>';
				$str = ob_get_clean();
				
				return json_encode(array('status'=> 1,'materialPrintForm'=>$strPrintForm ));
			}
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=>"Insufficient Rights!" ));
		}
	}
 }
 // AddAppointmentDetails By VIR
 function AddAppointmentDetails()
 {
	if(isset($_POST['JobID']) && isset($_POST['date']) && isset($_POST['time']) && isset($_POST['title']) && isset($_POST['description']))
	{
		$JobId = $_POST['JobID'];
		$date = $_POST['date'];
		$time = $_POST['time'];
		$title = $_POST['title'];
		$description = $_POST['description'];
		$userid = $_SESSION['ao_userid'];
		$todayDate = date("Y-m-d h:m:s");
		$datetime = DateUtil::formatMySQLTimestamp("$date $time");
		
			$sql = "INSERT INTO appointments VALUES(NULL, '$userid', '$JobId', '$datetime', '$title', '$description', '$todayDate')";
			
			$result = DBUtil::query($sql);
			
            if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Appointment added successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error! , can't add Appointment")); 
			}
	}
 }
 // getAppointmentTimeList By VIR
 function getAppointmentTimeList()
 {
		$timeArray = FormUtil::getTimePicklist();
	    return json_encode(array('timeList'=> $timeArray)); 
}
// GetScheduleJobDetails By VIR
function GetScheduleJobDetails()
{
	if(isset($_POST['JobID']) && isset($_POST['flag']))
	{
		$flag = $_POST['flag'];
		$JobId = $_POST['JobID'];
		$myJob = new Job($JobId);
		$CustomerID = $myJob->customer_id;
		
		$sql ="SELECT * , concat( fname, ', ', lname ) AS FullName, date_format( timestamp, '%b %d, %Y' ) AS startDate FROM customers WHERE customer_id = '$CustomerID'";
		$myCustomer = DBUtil::queryToArray($sql);	
		
		$scheduleForm = new ScheduleForm();
		if($flag == '1')
		{
			$scheduleForm->setType('gutter');
		}
		else if($flag == '2')
		{
			$scheduleForm->setType('repair');
		}
		else if($flag == '3')
		{
			$scheduleForm->setType('roofing');
		}
		else if($flag == '4')
		{
			$scheduleForm->setType('window');
			
		}

		$UploadTitle = $scheduleForm->getUploadTitle();
				
		if($flag == '4')
		{
			$numOfWindow = $scheduleForm->getMetaData('schedule_window_job_no_window');
			$marked = $scheduleForm->getMetaData('schedule_window_job_marked');
			$windowStory = $scheduleForm->getMetaData('schedule_window_job_window_story');
			$sideOfHouse = $scheduleForm->getMetaData('schedule_window_job_window_side');
			return json_encode(array('myJob'=> $myJob, 'myCustomer' => $myCustomer, 'UploadTitle' => $UploadTitle, 'numOfWindow' => $numOfWindow, 'marked' => $marked, 'windowStory' => $windowStory, 'sideOfHouse' => $sideOfHouse)); 
			
		}
		else
		{
			return json_encode(array('myJob'=> $myJob, 'myCustomer' => $myCustomer, 'UploadTitle' => $UploadTitle)); 
		}
	}
}
// InsertScheduleGutterJob By VIR
function InsertScheduleGutterJob()
{
	if(isset($_POST['JobID']) && isset($_POST['upload_title']) && isset($_POST['customer']) && isset($_POST['salesman']) && isset($_POST['job']) && isset($_POST['address'])
	&& isset($_POST['phone']) && isset($_POST['startdate']) && isset($_POST['city']) && isset($_POST['state']) && isset($_POST['zip']) && isset($_POST['phone2'])
	&& isset($_POST['gutter_l_f']) && isset($_POST['downspout_l_f']) && isset($_POST['gutter_color']) && isset($_POST['gutter_size']) && isset($_POST['gutter_material']) && isset($_POST['downspout_size'])
	&& isset($_POST['cover_type']) && isset($_POST['cover_lineal_footage']) && isset($_POST['pitch']) && isset($_POST['electrical_outlet_location']) && isset($_POST['stories']) && isset($_POST['agreed_upon_price'])
	&& isset($_POST['tear_off_notes']) && isset($_POST['job_details']) && isset($_POST['UploadId']))
	{
		$JobId = $_POST['JobID'];
		$myJob = new Job($JobId);

		$upload_title = $_POST['upload_title'];
		$customer = $_POST['customer'];
		$salesman = $_POST['salesman'];
		$job = $_POST['job'];
		$address = $_POST['address'];
		$phone = $_POST['phone'];
		$startdate = $_POST['startdate'];
		$city = $_POST['city'];
		$state = $_POST['state'];
		$zip = $_POST['zip'];
		$phone2 = $_POST['phone2'];
		$gutter_l_f = $_POST['gutter_l_f'];
		$downspout_l_f = $_POST['downspout_l_f'];
		$gutter_color = $_POST['gutter_color'];
		$gutter_size = $_POST['gutter_size'];
		$gutter_material = $_POST['gutter_material'];
		$downspout_size = $_POST['downspout_size'];
		$cover_type = $_POST['cover_type'];
		$cover_lineal_footage = $_POST['cover_lineal_footage'];
		$pitch = $_POST['pitch'];
		$electrical_outlet_location = $_POST['electrical_outlet_location'];
		$stories = $_POST['stories'];
		$agreed_upon_price = $_POST['agreed_upon_price'];
		$tear_off_notes = $_POST['tear_off_notes'];
		$job_details = $_POST['job_details'];
		$scheduleGutterJob = $_POST['scheduleGutterJob'];
		
		$uploadId =  $_POST['UploadId'];
		
		try {
		    $scheduleForm = new ScheduleForm();
		    $scheduleForm->initByUploadId($uploadId);
		} catch (Exception $e) {
		    $newForm = TRUE;
		    $scheduleForm = new ScheduleForm();
		    $scheduleForm->setType('gutter');
		}

		$myJob = new Job($scheduleForm->exists ? $scheduleForm->getJobId() : $JobId);
		$myCustomer = new Customer($myJob->customer_id);

		
		if(!$scheduleForm->exists) {
				$scheduleForm->setJobId($JobId)->setUploadId(-1)->store();
		}
    
		foreach($_POST as $key => $dataPoint) {
			if($key != 'UploadId')
			{
				JobModel::setMetaValue($scheduleForm->getMyMetaId(), "schedule_gutter_job_$key", $dataPoint);
			}
		}

		$viewData = array(
			'meta_data' => $scheduleForm->getMetaData(),
			'job_number' => $myJob->job_number,
			'logo' => LOGOS_PATH . '/' . $_SESSION['ao_logo']
		);
		$html = ViewUtil::loadView('pdf/schedule-gutter-job', $viewData);
		$fileName = PdfUtil::generatePDFFromHtml($html, 'Gutter Job Schedule Form', true, UPLOADS_PATH);
		$title = RequestUtil::get('upload_title', 'Schedule Gutter Job');
		
		if($newForm) {
		    $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
		            VALUES ('$JobId', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$fileName', '$title', now())";
		    $result = DBUtil::query($sql);
		    
			if($result == true)
            {
				$insertId = DBUtil::getInsertId();
				$scheduleForm->setUploadId($insertId)->store();
                return json_encode(array('status'=> 1,'message'=>"Record added successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error!")); 
			}
		}
		else {
			$sql = "UPDATE uploads SET filename = '$fileName', timestamp = now(), active = 1, title = '$title' WHERE upload_id = '{$scheduleForm->getUploadId()}'";
			$result = DBUtil::query($sql);

			if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Record updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error!")); 
			}
		}

	}
}
// InsertScheduleRepairJob By VIR
function InsertScheduleRepairJob()
{
	if(isset($_POST['JobID']) && isset($_POST['upload_title']) && isset($_POST['customer']) && isset($_POST['salesman']) && isset($_POST['job']) && isset($_POST['address'])
	&& isset($_POST['phone']) && isset($_POST['startdate']) && isset($_POST['city']) && isset($_POST['state']) && isset($_POST['zip']) && isset($_POST['phone2'])
	&& isset($_POST['house']) && isset($_POST['garage']) && isset($_POST['shed']) && isset($_POST['patio']) && isset($_POST['gutters']) && isset($_POST['color'])
	&& isset($_POST['total_l_f']) && isset($_POST['downspout']) && isset($_POST['repair_details']) && isset($_POST['upon_price']) && isset($_POST['UploadId']))
	{
		$JobId = $_POST['JobID'];
		$myJob = new Job($JobId);

		$upload_title = $_POST['upload_title'];
		$customer = $_POST['customer'];
		$salesman = $_POST['salesman'];
		$job = $_POST['job'];
		$address = $_POST['address'];
		$phone = $_POST['phone'];
		$startdate = $_POST['startdate'];
		$city = $_POST['city'];
		$state = $_POST['state'];
		$zip = $_POST['zip'];
		$phone2 = $_POST['phone2'];

		$house = $_POST['house'];
		$garage = $_POST['garage'];
		$shed = $_POST['shed'];
		$patio = $_POST['patio'];
		$gutters = $_POST['gutters'];
		$color = $_POST['color'];
		$total_l_f = $_POST['total_l_f'];
		$downspout = $_POST['downspout'];
		$repair_details = $_POST['repair_details'];
		$upon_price = $_POST['upon_price'];
				
		$uploadId =  $_POST['UploadId'];
		
		try {
				$scheduleForm = new ScheduleForm();
				$scheduleForm->initByUploadId($uploadId);
			} catch (Exception $e) {
				$newForm = TRUE;
				$scheduleForm = new ScheduleForm();
				$scheduleForm->setType('repair');
			}

		$myJob = new Job($scheduleForm->exists ? $scheduleForm->getJobId() : $JobId);
		$myCustomer = new Customer($myJob->customer_id);

		if(!$scheduleForm->exists) {
				$scheduleForm->setJobId($JobId)->setUploadId(-1)->store();
		}
    
		foreach($_POST as $key => $dataPoint) {
			if($key != 'UploadId')
			{
				JobModel::setMetaValue($scheduleForm->getMyMetaId(), "schedule_repair_job_$key", $dataPoint);
			}
		}

		$viewData = array(
			'meta_data' => $scheduleForm->getMetaData(),
			'job_number' => $myJob->job_number,
			'logo' => LOGOS_PATH . '/' . $_SESSION['ao_logo']
		);

		
		$html = ViewUtil::loadView('pdf/schedule-repair-job', $viewData);
		$fileName = PdfUtil::generatePDFFromHtml($html, 'Repair Job Schedule Form', true, UPLOADS_PATH);
		$title = RequestUtil::get('upload_title', 'Schedule Repair Job');

		if($newForm) {
		    $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
                VALUES ('$JobId', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$fileName', '$title', now())";
			$result = DBUtil::query($sql);
		    
			if($result == true)
            {
				$insertId = DBUtil::getInsertId();
				$scheduleForm->setUploadId($insertId)->store();
                return json_encode(array('status'=> 1,'message'=>"Record added successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error!")); 
			}
		}
		else {
			$sql = "UPDATE uploads SET filename = '$fileName', timestamp = now(), active = 1, title = '$title' WHERE upload_id = '{$scheduleForm->getUploadId()}'";
			$result = DBUtil::query($sql);

			if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Record updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error!")); 
			}
		}

	}
}
// InsertScheduleRoofingJob By VIR
function InsertScheduleRoofingJob()
{
	if(isset($_POST['JobID']) && isset($_POST['upload_title']) && isset($_POST['customer']) && isset($_POST['salesman']) && isset($_POST['job']) && isset($_POST['address'])
	&& isset($_POST['phone']) && isset($_POST['startdate']) && isset($_POST['city']) && isset($_POST['state']) && isset($_POST['zip']) && isset($_POST['phone2'])
	&& isset($_POST['permit']) && isset($_POST['need_a_production_manager']) && isset($_POST['stories']) && isset($_POST['existing_roof']) && isset($_POST['house'])
	&& isset($_POST['house_squares']) && isset($_POST['garage']) && isset($_POST['garage_squares']) && isset($_POST['shed']) && isset($_POST['shed_squares'])
	&& isset($_POST['patio']) && isset($_POST['patio_squares']) && isset($_POST['new_roof']) && isset($_POST['job_new_roof_color'])
	&& isset($_POST['squares']) && isset($_POST['pitch']) && isset($_POST['roofings_tear_off']) && isset($_POST['roofings_color'])
	&& isset($_POST['drip_edge']) && isset($_POST['agreed_upon_price']) && isset($_POST['tear_off_notes']) && isset($_POST['job_details']) && isset($_POST['UploadId']))
	{
		$JobId = $_POST['JobID'];
		$myJob = new Job($JobId);

		$upload_title = $_POST['upload_title'];
		$customer = $_POST['customer'];
		$salesman = $_POST['salesman'];
		$job = $_POST['job'];
		$address = $_POST['address'];
		$phone = $_POST['phone'];
		$startdate = $_POST['startdate'];
		$city = $_POST['city'];
		$state = $_POST['state'];
		$zip = $_POST['zip'];
		$phone2 = $_POST['phone2'];

		$permit = $_POST['permit'];
		$need_a_production_manager = $_POST['need_a_production_manager'];
		$stories = $_POST['stories'];
		$existing_roof = $_POST['existing_roof'];
		$house = $_POST['house'];
		$house_squares = $_POST['house_squares'];
		$garage = $_POST['garage'];
		$garage_squares = $_POST['garage_squares'];
		$shed = $_POST['shed'];
		$shed_squares = $_POST['shed_squares'];

		$patio = $_POST['patio'];
		$patio_squares = $_POST['patio_squares'];
		$new_roof = $_POST['new_roof'];
		$job_new_roof_color = $_POST['job_new_roof_color'];
		$squares = $_POST['squares'];
		$pitch = $_POST['pitch'];
		$roofings_tear_off = $_POST['roofings_tear_off'];
		$roofings_color = $_POST['roofings_color'];
		$drip_edge = $_POST['drip_edge'];
		$agreed_upon_price = $_POST['agreed_upon_price'];
		$tear_off_notes = $_POST['tear_off_notes'];
		$job_details = $_POST['job_details'];
						
		$uploadId = $_POST['UploadId'];
		
		try {
		    $scheduleForm = new ScheduleForm();
		    $scheduleForm->initByUploadId($uploadId);
		} catch (Exception $e) {
		    $newForm = TRUE;
		    $scheduleForm = new ScheduleForm();
		    $scheduleForm->setType('roofing');
		}

		$myJob = new Job($scheduleForm->exists ? $scheduleForm->getJobId() : $JobId);
		$myCustomer = new Customer($myJob->customer_id);

		if(!$scheduleForm->exists) {
				$scheduleForm->setJobId($JobId)->setUploadId(-1)->store();
		}
		
		foreach($_POST as $key => $dataPoint) {
			if($key != 'UploadId')
			{
				JobModel::setMetaValue($scheduleForm->getMyMetaId(), "schedule_roofing_job_$key", $dataPoint);
			}
		}

		$viewData = array(
			'meta_data' => $scheduleForm->getMetaData(),
			'job_number' => $myJob->job_number,
			'logo' => LOGOS_PATH . '/' . $_SESSION['ao_logo']
		);

		$html = ViewUtil::loadView('pdf/schedule-roofing-job', $viewData);
		$fileName = PdfUtil::generatePDFFromHtml($html, 'Roofing Job Schedule Form', true, UPLOADS_PATH);
		$title = RequestUtil::get('upload_title', 'Schedule Roofing Job');
		
		if($newForm) {
		    $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
					VALUES ('$JobId', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$fileName', '$title', now())";
			$result = DBUtil::query($sql);
		    
			if($result == true)
            {
				$insertId = DBUtil::getInsertId();
				$scheduleForm->setUploadId($insertId)->store();
                return json_encode(array('status'=> 1,'message'=>"Record added successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error!")); 
			}
		}
		else {
			$sql = "UPDATE uploads SET filename = '$fileName', timestamp = now(), active = 1, title = '$title' WHERE upload_id = '{$scheduleForm->getUploadId()}'";
			$result = DBUtil::query($sql);

			if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Record updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error!")); 
			}
		}

	}
}
// InsertScheduleWindowJob By VIR
function InsertScheduleWindowJob()
{
	if(isset($_POST['JobID']) && isset($_POST['upload_title']) && isset($_POST['customer']) && isset($_POST['salesman']) && isset($_POST['job']) && isset($_POST['address'])
	&& isset($_POST['phone']) && isset($_POST['startdate']) && isset($_POST['city']) && isset($_POST['state']) && isset($_POST['zip']) && isset($_POST['phone2'])
	&& isset($_POST['no_window']) && isset($_POST['marked']) && isset($_POST['window_story']) && isset($_POST['window_side']) && isset($_POST['window_type'])
	&& isset($_POST['window_color']) && isset($_POST['window_dimension_x']) && isset($_POST['window_dimension_y']) && isset($_POST['window_screen']) && isset($_POST['glazing_bead'])
	&& isset($_POST['agreed_upon_price']) && isset($_POST['des_damage']) && isset($_POST['specific_detail']) && isset($_POST['UploadId']))
	{
		$JobId = $_POST['JobID'];
		$myJob = new Job($JobId);

		$upload_title = $_POST['upload_title'];
		$customer = $_POST['customer'];
		$salesman = $_POST['salesman'];
		$job = $_POST['job'];
		$address = $_POST['address'];
		$phone = $_POST['phone'];
		$startdate = $_POST['startdate'];
		$city = $_POST['city'];
		$state = $_POST['state'];
		$zip = $_POST['zip'];
		$phone2 = $_POST['phone2'];

		$no_window = $_POST['no_window'];
		$marked = $_POST['marked'];
		$window_story = $_POST['window_story'];
		$window_side = $_POST['window_side'];
		$window_type = $_POST['window_type'];
		$window_color = $_POST['window_color'];
		$window_dimension_x = $_POST['window_dimension_x'];
		$window_dimension_y = $_POST['window_dimension_y'];
		$window_screen = $_POST['window_screen'];
		$glazing_bead = $_POST['glazing_bead'];
		$agreed_upon_price = $_POST['agreed_upon_price'];
		$des_damage = $_POST['des_damage'];
		$specific_detail = $_POST['specific_detail'];
		
		$uploadId =  $_POST['UploadId'];
		
		try {
		    $scheduleForm = new ScheduleForm();
		    $scheduleForm->initByUploadId($uploadId);
		} catch (Exception $e) {
		    $newForm = TRUE;
		    $scheduleForm = new ScheduleForm();
		    $scheduleForm->setType('window');
		}

		$myJob = new Job($scheduleForm->exists ? $scheduleForm->getJobId() : $JobId);
		$myCustomer = new Customer($myJob->customer_id);

		if(!$scheduleForm->exists) {
				$scheduleForm->setJobId($JobId)->setUploadId(-1)->store();
		}
    
		foreach($_POST as $key => $dataPoint) {
			if($key != 'UploadId')
			{
				JobModel::setMetaValue($scheduleForm->getMyMetaId(), "schedule_window_job_$key", $dataPoint);
			}
		}

		$viewData = array(
			'meta_data' => $scheduleForm->getMetaData(),
			'job_number' => $myJob->job_number,
			'logo' => LOGOS_PATH . '/' . $_SESSION['ao_logo']
		);

		$html = ViewUtil::loadView('pdf/schedule-window-job', $viewData);
		$fileName = PdfUtil::generatePDFFromHtml($html, 'Window Job Schedule Form', true, UPLOADS_PATH);
		$title = RequestUtil::get('upload_title', 'Schedule Window Job');
				
		if($newForm) {
		    $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
                VALUES ('$JobId', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$fileName', '$title', now())";
			$result = DBUtil::query($sql);
		    
			if($result == true)
            {
				$insertId = DBUtil::getInsertId();
				$scheduleForm->setUploadId($insertId)->store();
                return json_encode(array('status'=> 1,'message'=>"Record added successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error!")); 
			}
		}
		else {
			$sql = "UPDATE uploads SET filename = '$fileName', timestamp = now(), active = 1, title = '$title' WHERE upload_id = '{$scheduleForm->getUploadId()}'";
			$result = DBUtil::query($sql);

			if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Record updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error!")); 
			}
		}

	}
}
// GetScheduleJobDetailsForEDIT By VIR
function GetScheduleJobDetailsForEDIT()
{
	if(isset($_POST['JobID']) && isset($_POST['flag']) && isset($_POST['UploadId']))
	{
		$flag = $_POST['flag'];
		$JobId = $_POST['JobID'];
		$myJob = new Job($JobId);
		$CustomerID = $myJob->customer_id;
		$uploadId = $_POST['UploadId'];

		$sql ="SELECT * , concat( fname, ', ', lname ) AS FullName, date_format( timestamp, '%b %d, %Y' ) AS startDate FROM customers WHERE customer_id = '$CustomerID'";
		$myCustomer = DBUtil::queryToArray($sql);	
		
		$scheduleForm = new ScheduleForm();
		if($flag == '1')
		{
			$scheduleForm->setType('gutter');
		}
		else if($flag == '2')
		{
			$scheduleForm->setType('repair');
		}
		else if($flag == '3')
		{
			$scheduleForm->setType('roofing');
		}
		else if($flag == '4')
		{
			$scheduleForm->setType('window');
			
		}

		$UploadTitle = $scheduleForm->getUploadTitle();
				
		if($flag == '4')
		{
			$scheduleForm = new ScheduleForm();
			$scheduleForm->initByUploadId($uploadId);
			$scheduleForm->setType('window');
			$numOfWindow = $scheduleForm->getMetaData('schedule_window_job_no_window');
			$marked = $scheduleForm->getMetaData('schedule_window_job_marked');
			$windowStory = $scheduleForm->getMetaData('schedule_window_job_window_story');
			$sideOfHouse = $scheduleForm->getMetaData('schedule_window_job_window_side');
			
			return json_encode(array('myJob'=> $myJob, 'myCustomer' => $myCustomer, 'UploadTitle' => $UploadTitle, 'numOfWindow' => $numOfWindow, 'marked' => $marked, 'windowStory' => $windowStory, 'sideOfHouse' => $sideOfHouse)); 
			
		}
		else
		{
			return json_encode(array('myJob'=> $myJob, 'myCustomer' => $myCustomer, 'UploadTitle' => $UploadTitle)); 
		}
	}
}
// BindScheduleJobDetailForEdit By VIR
function BindScheduleJobDetailForEdit()
{
	if(isset($_POST['JobID']) && isset($_POST['UploadId']) && isset($_POST['scheduleType']) )
	{
		$JobId = $_POST['JobID'];
		$uploadId = $_POST['UploadId'];
		$scheduleType = $_POST['scheduleType'];
		
		$scheduleForm = new ScheduleForm();
		$scheduleForm->initByUploadId($uploadId);

		$viewData = $scheduleForm->getMetaData();
		
		return json_encode(array('viewData'=> $viewData, 'scheduleType' => $scheduleType)); 
	}
}
// AddNewJobInvoice By VIR
function AddNewJobInvoice()
{
	if(isset($_POST['JobId']) && isset($_POST['InvDesc']) && isset($_POST['InvAmt']) && isset($_POST['InvType']))
	{
		$JobId = $_POST['JobId'];
		$note = $_POST['InvDesc'];
		$amount = $_POST['InvAmt'];
		$type = $_POST['InvType'];

		$myJob = new Job($JobId);

		if(!ModuleUtil::checkAccess('invoice_readwrite')) {
			return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
		}
		else
		{
			if(moduleOwnership('invoice_readwrite') && ($myJob->salesman_id != $_SESSION['ao_userid'] && $myJob->user_id != $_SESSION['ao_userid'])) {
				return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
			}
			else
			{	
				if(empty($myJob->invoice_id)) {
					$sql = "INSERT INTO invoices (job_id, user_id, timestamp) VALUES('{$myJob->job_id}', '{$_SESSION['ao_userid']}', now())";
				    DBUtil::query($sql);
				    
				    $myJob->invoice_id = DBUtil::getInsertId();
				    JobModel::saveEvent($myJob->job_id, 'Invoice Created');
					$myJob = new Job($JobId);
				}
				    

				if($type == 'credit')
				{
					$sql = "INSERT INTO credits VALUES (0, '{$myJob->invoice_id}', '$amount', '$note', now())";
				}
				else
				{
					$sql = "INSERT INTO charges VALUES (0, '{$myJob->invoice_id}', '$amount', '$note', now())";
				}
				
				$result = DBUtil::query($sql);
				
				if($result) {
					JobModel::saveEvent($myJob->job_id, "Invoice $type added ($$amount)");
					return json_encode(array('status'=> 1,'message'=>"Invoice $type added ($$amount)"));
				}
				else
				{
					return json_encode(array('status'=> 0,'message'=>"There was an error. Try again please!"));
				}
			}
		}
	}
}
// GetJobInvoiceForAll By VIR
function GetJobInvoiceForAll()
{
	if(isset($_POST['JobID']))
	{
		$JobId = $_POST['JobID'];
		$myJob = new Job($JobId);
		if(ModuleUtil::checkJobModuleAccess('invoice_readwrite', $myJob, TRUE))
		{
			$totalCharges = $myJob->getInvoiceChargesTotal();
			$totalCredits = $myJob->getInvoiceCreditsTotal();
			$balance = $myJob->getInvoiceBalance();
			$credits = $myJob->fetchCredits();
			$charges = $myJob->fetchCharges();

			$totalCharges = number_format($totalCharges, 2, '.', '');
			
			$sql = "select office_id, title from offices where account_id='".$_SESSION['ao_accountid']."' order by title asc";
			$resOffice = DBUtil::queryToArray($sql);	

			$sql = "select office_id from users where user_id='".$_SESSION['ao_userid']."'";
			$resUser = DBUtil::queryToArray($sql);	
										
			return json_encode(array('status'=> 1,'totalCharges'=> $totalCharges, 'totalCredits'=> $totalCredits, 'credits'=> $credits, 'charges'=> $charges, 'balance' => $balance, 'resOffice' => $resOffice, 'resUser' => $resUser));
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
		}
	}
}
// DeleteJobInvoiceByTypeID By VIR
function DeleteJobInvoiceByTypeID()
{
	if(isset($_POST['JobID']) && isset($_POST['InvType']) && isset($_POST['InvTypeID']))
	{
		$JobID = $_POST['JobID'];
		$type = $_POST['InvType'];
		$itemId = $_POST['InvTypeID'];

		if($type == 'credit') {
            $sql = "DELETE FROM credits WHERE credit_id = '$itemId' LIMIT 1";
		}
		else if($type == 'charge') {
			$sql = "DELETE FROM charges WHERE charge_id = '$itemId' LIMIT 1";
		}

		$result = DBUtil::query($sql);
		if($result) 
		{
				return json_encode(array('status'=> 1,'message'=>"Record Deleted Successfully!"));
		}
		else
		{
				return json_encode(array('status'=> 0,'message'=>"There was an error. Try again please!"));
		}
	}
}
// AddUpdateJobProfitComm By Vir
function AddUpdateJobProfitComm()
{
	if(isset($_POST['JobId']) && isset($_POST['Commission']))
	{
		$JobId = $_POST['JobId'];
		$Commission = $_POST['Commission'];
		$myJob = new Job($JobId);

		if(empty($myJob->profit_sheet_id)) {
		 
				$sql = "insert into profit_sheets values(0, '$JobId', '".$_SESSION['ao_userid']."', '$Commission', now())";
				$result = DBUtil::query($sql);
				$myJob->profit_sheet_id = DBUtil::getInsertId();
		}
		else
		{
				$sql = "update profit_sheets set commission='$Commission' where profit_sheet_id='".$myJob->profit_sheet_id."' limit 1";
				$result = DBUtil::query($sql);
		}

		if($result) 
		{
				return json_encode(array('status'=> 1,'message'=>"Record Updated Successfully!"));
		}
		else
		{
				return json_encode(array('status'=> 0,'message'=>"There was an error. Try again please!"));
		}
	}
}
// AddNewJobInvProfit By VIR
function AddNewJobInvProfit()
{
	if(isset($_POST['JobId']) && isset($_POST['PrfDesc']) && isset($_POST['PrfAmt']) && isset($_POST['PrfType']))
	{
		$JobId = $_POST['JobId'];
		$note = $_POST['PrfDesc'];
		$amount = $_POST['PrfAmt'];
		$type = $_POST['PrfType'];

		$myJob = new Job($JobId);

		if(!ModuleUtil::checkAccess('profitability_readwrite')) {
			return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
		}
		else
		{
			if(moduleOwnership('profitability_readwrite') && ($myJob->salesman_id != $_SESSION['ao_userid'] && $myJob->user_id != $_SESSION['ao_userid'])) {
				return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
			}
			else
			{	
				if($type == 'credit')
				{
					$sql = "insert into profit_credits values(0, '{$myJob->profit_sheet_id}', '$amount', '$note', now())";
				}
				else
				{
					$sql = "insert into profit_charges values(0, '{$myJob->profit_sheet_id}', '$amount', '$note', now())";
				}
				
				$result = DBUtil::query($sql);
				
				if($result) {
					return json_encode(array('status'=> 1,'message'=>"Invoice $type added ($$amount)"));
				}
				else
				{
					return json_encode(array('status'=> 0,'message'=>"There was an error. Try again please!"));
				}
			}
		}
	}
}
// GetJobInvProfitForAll By VIR
function GetJobInvProfitForAll()
{
	$JobId = $_POST['JobID'];
	$myJob = new Job($JobId);
	if(ModuleUtil::checkJobModuleAccess('profitability_readwrite', $myJob, TRUE))
	{
		$sql = "select commission from profit_sheets where profit_sheet_id='$myJob->profit_sheet_id' limit 1";
		$res = DBUtil::query($sql);
		list($commission_percentage) = mysqli_fetch_row($res);

		$sql = "select profit_charge_id, note, amount from profit_charges where profit_sheet_id='$myJob->profit_sheet_id' order by amount asc";
		$res_charges = DBUtil::query($sql);
		$num_charges = mysqli_num_rows($res_charges);
		
		$sql = "select profit_credit_id, note, amount from profit_credits where profit_sheet_id='$myJob->profit_sheet_id' order by amount asc";
		$res_credits = DBUtil::query($sql);
		$num_credits = mysqli_num_rows($res_credits);
		
		$materials_total = number_format($myJob->materials_total, 2);
		
		$strJobInvProfit= '<table class="table">';
		$strJobInvProfit=  $strJobInvProfit. '<tr><th>&nbsp;</th><th>Description</th><th>Credit</th><th>Charge</th></tr>';
		$strJobInvProfit = $strJobInvProfit. '<tr class="" valign="center"><td width=20>&nbsp;</td><td>Materials</td><td width="25%">&nbsp;</td><td width="25%" style="color: red;">'.$materials_total.'</td></tr>';
		$i = 1;
		$total_charges = 0;
		if($num_charges != 0)
		{
			
			while (list($charge_id, $note, $amt) = mysqli_fetch_row($res_charges))
		    {
				$class = "odd";
				if($i % 2 == 0)
				$class = "even";
				$total_charges+=$amt;

		$strJobInvProfit= $strJobInvProfit. '<tr class="$class" valign="center"><td width=20><a class=aDelJobInvProfitChargeItem id=aDelJobInvProfitChargeItem_'. $charge_id .'><i class="icon-remove"></i></a></td>';
		$strJobInvProfit= $strJobInvProfit. '<td colspan=2>'.$note.'</td><td width="25%" style="color: red;">'.$amt.'</td></tr>';
		$i++;
			}
		}
		if($num_credits != 0)
		{
			$total_credits = 0;
			while (list($credit_id, $note, $amt) = mysqli_fetch_row($res_credits))
			{
				$class = "odd";
				if($i % 2 == 0)
				{
					$class = "even";
				}
				$total_credits += $amt;

				$strJobInvProfit= $strJobInvProfit. '<tr class='.$class.' valign="center"><td width=20><a class=aDelJobInvProfitCreditItem id=aDelJobInvProfitCreditItem_' . $credit_id . '><i class="icon-remove"></i></a></td>';
				$strJobInvProfit= $strJobInvProfit. '<td>'.$note.'</td><td width="25%" style="color: green;">('.$amt.')</td><td width="25%">&nbsp;</td></tr>';
				$i++;
			}
		}
		$total_charges = number_format($total_charges, 2, '.', '');
		$total_credits = number_format($total_credits, 2, '.', '');
		$gross = number_format(($total_credits - ($total_charges + $materials_total)), 2, '.', '');
		$commission = number_format(($gross * ($commission_percentage / 100)), 2, '.', '');
		if($commission < 0)
		{
			$commission = "0.00";
		}

		$net = number_format(($gross - $commission), 2, '.', '');
		if($net < 0)
			{ $net = "0.00"; }
		
		$strJobInvProfit= $strJobInvProfit. '<tr class='.$class.' valign="center"><td colspan=2 align="right" style="border-top:1px solid #cccccc; text-align:right;"><b>Totals:</b></td>';
		$strJobInvProfit= $strJobInvProfit. '<td width="25%" style="font-weight:bold; color: green; border-top:1px solid #cccccc;">('.$total_credits.')</td>';
		$strJobInvProfit= $strJobInvProfit. '<td width="25%" style="font-weight:bold; color: red; border-top:1px solid #cccccc;">'.$total_charges.'</td></tr>';
		$strJobInvProfit= $strJobInvProfit. '<tr valign="center"><td colspan=3 align="right" class="smalltitle" style="border-top:1px solid #cccccc;  text-align:right;"><b>Gross Profit:</b></td>';
		$strJobInvProfit= $strJobInvProfit. '<td width=60 class="smalltitle" style="border-top:1px solid #cccccc;">'.$gross.'</td></tr>';
		$strJobInvProfit= $strJobInvProfit. '<tr valign="center"><td colspan=3 align="right" class="smalltitle" style=" text-align:right;"><b>'.$commission_percentage.'% Commission TBP:</b></td>';
		$strJobInvProfit= $strJobInvProfit. '<td width=60 class="smalltitle">'.$commission.'</td></tr><tr valign="center">';
		$strJobInvProfit= $strJobInvProfit. '<td colspan=3 align="right" class="smalltitle" style=" text-align:right;"><b>Net Profit:</b></td><td width=60 class="smalltitle">'.$net.'</td></tr>';
		$strJobInvProfit= $strJobInvProfit. '</table>';
		
		return json_encode(array('status'=> 1,'strJobInvProfit'=> $strJobInvProfit));
	}
	else
	{
		return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
	}
}
// DeleteJobInvProfitByTypeID By VIR
function DeleteJobInvProfitByTypeID()
{
	if(isset($_POST['JobID']) && isset($_POST['InvType']) && isset($_POST['InvTypeID']))
	{
		$JobID = $_POST['JobID'];
		$type = $_POST['InvType'];
		$itemId = $_POST['InvTypeID'];

		if($type == 'credit') {
			$sql = "delete from profit_credits where profit_credit_id='$itemId' limit 1";
        }
		else if($type == 'charge') {
			$sql = "delete from profit_charges where profit_charge_id='$itemId' limit 1";
		}

		$result = DBUtil::query($sql);
		if($result) 
		{
				return json_encode(array('status'=> 1,'message'=>"Record Deleted Successfully!"));
		}
		else
		{
				return json_encode(array('status'=> 0,'message'=>"There was an error. Try again please!"));
		}
	}
}
// GetCommissionList By VIR
function GetCommissionList()
{	
	$JobId = $_POST['JobID'];
	$myJob = new Job($JobId);
	
	$sql = "select commission from profit_sheets where profit_sheet_id='$myJob->profit_sheet_id' limit 1";
	$res = DBUtil::query($sql);
	list($commission_percentage) = mysqli_fetch_row($res);

	$strCommissionLst ='<select name="ddlcommission">';
		for($i=0; $i<101; $i+=5)
		{
			$selected = '';
			if($i==$commission_percentage)
				$selected = 'selected';
	    		$strCommissionLst = $strCommissionLst .'<option value='. $i .' '.$selected.'>' .$i.'%</option>';
		}
	$strCommissionLst = $strCommissionLst .'</select>';
	
	return json_encode(array('status'=> 1,'strCommissionLst'=> $strCommissionLst));
}

//GetSalesmanList By VIR
function GetJobSalesmanList()
{	
		$JobId = $_POST['JobID'];
		$myJob = new Job($JobId);
		$strJobSalesmanList = '<select id="salesman-picklist" name="salesman"><option value=""></option>';
		$firstLast = UIUtil::getFirstLast();
		$showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');
		$dropdownUserLevels = AccountModel::getMetaValue('assign_job_salesman_user_dropdown');
		$salesmen = !empty($dropdownUserLevels) ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast) : UserModel::getAll($showInactiveUsers, $firstLast);
		
		$selected = '';
		foreach($salesmen as $salesman) {
			if($salesman['is_active'] == 1 && $salesman['is_deleted'] == 0) {
				if($myJob->salesman_id == $salesman['user_id'])
				{ $selected = 'selected'; }
				else {$selected = '';}
				$strJobSalesmanList = $strJobSalesmanList . '<option value='. $salesman['user_id'] .' '.$selected.'>'. $salesman['select_label']. '</option>';
			} 
			else if($myJob->salesman_id == $salesman['user_id']) {
			
				$userStatus = $salesman['is_deleted'] ? 'Deleted' : 'Inactive';
				$strJobSalesmanList = $strJobSalesmanList . '<option value='. $salesman['user_id'] .' selected  {'. $salesman['lname'].'}, {'.$salesman['fname'].'} ('.$userStatus.')</option>';
			}
		}
		$strJobSalesmanList = $strJobSalesmanList . '</select>';
		
		$sql = "select count(job_id) from jobs where date_format(timestamp, '%Y')='".date('Y')."' and salesman='".intval($myJob->salesman_id)."'";
		$res = DBUtil::query($sql);
  
		$num_rows = mysqli_num_rows($res);
				
		return json_encode(array( 'jobsTotal' => $num_rows, 'status'=> 1,'strJobSalesmanList'=> $strJobSalesmanList));
}

//UpdateJobSalesmanInfo By VIR
function UpdateJobSalesmanInfo()
{
		$JobId = $_POST['JobId'];
		
		$myJob = new Job($JobId);
		$salesman = $_POST['SalesmanID'];
		
		
		$sql = "update jobs set salesman = '$salesman' where job_id = '$JobId' limit 1";
		$result = DBUtil::query($sql);
		$myJob->storeSnapshot();

		if($salesman) {
		    NotifyUtil::notifyFromTemplate('add_job_salesman', $salesman, null, array('job_id' => $myJob->job_id));
		} else {
		    NotifyUtil::notifyFromTemplate('remove_job_salesman', $salesman, null, array('job_id' => $myJob->job_id));
		}

		JobModel::saveEvent($JobId, 'Assigned New Job Salesman');
		if($result)
		{
			return json_encode(array('status'=> 1,'message'=> "Job Detail Updated!"));
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=>"There was an error. Try again please!"));
		}

}

//GetCustomersJobForMap By VIR
function GetCustomersJobForMap()
{
	$JobId = $_POST['JobID'];
	$myJob = new Job($JobId);
	$job_radius = 10;
	if(ModuleUtil::checkJobModuleAccess('assign_job_salesman', $myJob, TRUE))
	{
		$myCustomer = new Customer($myJob->customer_id);
		
		ViewUtil::loadView('maps/basic', $view_data);
		$bounds = JobUtil::getBoundingRectangle($myCustomer->get('lat'), $myCustomer->get('long'), $job_radius);
		
		$sql = "SELECT c.*, j.*, ((ACOS(SIN({$myCustomer->get('lat')} * PI() / 180) * SIN(c.lat * PI() / 180) + COS({$myCustomer->get('lat')} * PI() / 180) * COS(c.lat * PI() / 180) * COS(({$myCustomer->get('long')} - c.long) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance
				FROM jobs j, customers c
				WHERE j.account_id = '".$_SESSION['ao_accountid']."'
				AND c.customer_id != '{$myCustomer->getMyId()}'
				AND c.customer_id = j.customer_id
				AND c.long >= ".mysqli_real_escape_string(DBUtil::Dbcont(),$bounds['lon_min'])."
				AND c.long <= ".mysqli_real_escape_string(DBUtil::Dbcont(),$bounds['lon_max'])."
				AND c.lat >= ".mysqli_real_escape_string(DBUtil::Dbcont(),$bounds['lat_min'])."
				AND c.lat <= ".mysqli_real_escape_string(DBUtil::Dbcont(),$bounds['lat_max'])."
				AND j.timestamp
				HAVING distance <= $job_radius
				ORDER BY j.timestamp DESC
				LIMIT 100";
				
				$results = DBUtil::queryToArray($sql);
                
				
				return json_encode(array('status'=> 1,'mapContent' => $results));
	}
	else
	{
		return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
	}
}

//GetJobMessageHistory By VIR
function GetJobMessageHistory()
{
	$JobId = $_POST['jobId'];
	$myJob = new Job($JobId);
	$strJobMsgHistory = '';
	if(ModuleUtil::checkJobModuleAccess('job_message_history', $myJob, TRUE))
	{
		$jobMessages = JobUtil::getMessageHistory($myJob->job_id);

		$strJobMsgHistory ='<table ><tr><td class="infocontainernopadding">';
					foreach($jobMessages as $message) 
					{		
							$message['body'] = str_replace("<html>", "", MapUtil::get($message, 'body'));
							$message['body'] = str_replace("</html>", "", MapUtil::get($message, 'body'));
							
							$message['body'] = preg_replace("#<a.*?>([^>]*)</a>#i", "", MapUtil::get($message, 'body'));

							$message['timestamp'] = DateUtil::formatDateTime(MapUtil::get($message, 'timestamp'));

							$strJobMsgHistory = $strJobMsgHistory. '<div><table class="table" style='. $style .'>';
							$strJobMsgHistory = $strJobMsgHistory. '<tr><td class="listitemnoborder" width="25%"><strong>Message Type:</strong></td><td class="listrownoborder">'.MapUtil::get($message, 'type').'</td></tr>';
							$strJobMsgHistory = $strJobMsgHistory. '<tr><td class="listitem"><b>Timestamp:</b></td><td class="listrow">'.MapUtil::get($message, 'timestamp').'</td></tr>';
							$strJobMsgHistory = $strJobMsgHistory. '<tr><td class="listitem"><b>Delivery Address:</b></td><td class="listrow">'.MapUtil::get($message, 'to_email').'</td></tr>';
							$strJobMsgHistory = $strJobMsgHistory. '<tr><td class="listitem"><b>Subject:</b></td><td class="listrow">'.MapUtil::get($message, 'subject').'</td></tr>';
							$strJobMsgHistory = $strJobMsgHistory. '<tr valign="top"><td class="listitem"><b>Message Body:</b></td><td class="listrow">'.MapUtil::get($message, 'body').'<a id="alinkViewJobMsgHistory" href="jobdetails.html?JId='.$JobId.'">Click here to view the Job Details</a></td></tr>';
							$strJobMsgHistory = $strJobMsgHistory. '</table></div>';
					}
		$strJobMsgHistory =$strJobMsgHistory.  '</td></tr></table>';
	}

	return json_encode(array('strJobMsgHistory' => $strJobMsgHistory));
}

//GetSystemModuleDetails By VIR
function GetSystemModuleDetails()
{
	if($_SESSION['ao_founder']!=1)
	{
		return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
	}
	else
	{
		$sql = "select count(user_id) from users where account_id='".$_SESSION['ao_accountid']."' and is_deleted<>1";
		$res = DBUtil::query($sql);
		list($total_num_users)=mysqli_fetch_row($res);

		$sql = "select * from accounts where account_id='".$_SESSION['ao_accountid']."' limit 1";
		$userAcctDetail = DBUtil::queryToArray($sql);

		//build array
		$configArray = array(
			'App Configuration' => 'app-configuration',
			'Email Templates' => 'emailtemplates',
			'SMS Templates' => 'smstemplates',
			'Insurance Providers' => 'providers',
			'Job Origins' => 'joborigins',
			'Job Types' => 'job_types',
			'Jurisdictions' => 'jurisdictions',
			'Materials' => 'modmaterials',
			'Offices' => 'modoffices',
			'Status Holds' => 'statusholds',
			'Task Types' => 'tasktypes',
			'User Groups' => 'usergroups',
			'Warranties' => 'warranties',
			'Stages' => 'stages-list'
		);

		//sort
		ksort($configArray);

		//break into columns
		$cols = 4;
		$span = 12 / $cols;
		$configArray = array_chunk($configArray, ceil(count($configArray) / $cols), true);

		$sysConfigList = '<div class="user_inf">Configuration</div><div><table class=table width="100%"><tr><td><div class="container"><div class="row pillbox">';
		foreach($configArray as $column)
		{
			$sysConfigList = $sysConfigList. '<div class=col span-'.$span.'>';
			foreach($column as $label => $script)
			{
				$sysConfigList = $sysConfigList. '<div><a href='.$script.'.html class="btn btn-blue">'.$label.'</a></div><div>&nbsp;</div>';
			}
				$sysConfigList = $sysConfigList. '</div>';
		}
		$sysConfigList = $sysConfigList. '</div></div></td></tr></table></div>';

		$sql = "select level_id, level from levels order by level_id asc";
		$res = DBUtil::query($sql);

		$sysLevelList = '<div class="user_inf">Security Levels</div><table width="100%" border="0" class="table" cellpadding=5 cellspacing="0">';
		if(mysqli_num_rows($res)==0) {
			$sysLevelList = $sysLevelList. '<tr><td align="center"><b>No Levels Found</b></td></tr>';
		}
			$i=1;
			while(list($level_id, $level)=mysqli_fetch_row($res))
			{
				$class='odd';
				if($i%2==0)
				$class='even';
				$sysLevelList = $sysLevelList. '<tr class='.$class.' valign=top>';
				$sysLevelList = $sysLevelList. '<td><a href=get_level.html?id='.$level_id.' class=basiclink>'.$level.'</a></td>';
				$sysLevelList = $sysLevelList. '</tr>';
				$i++;
		}
		$sysLevelList = $sysLevelList. '</table>';

		$stateArray = getStates();
		
		return json_encode(array('status'=> 1, 'userAcctDetail'=> $userAcctDetail,'usersCount'=> $total_num_users, 'sysConfigList' => $sysConfigList, 'sysLevelList' => $sysLevelList, 'stateArray' => $stateArray));
	}
}

//UpdateCompanyProfileDetails By VIR
function UpdateCompanyProfileDetails()
{
		$CompanyTitle = $_POST['Title'];
		$PrimaryContact = $_POST['PContact'];
		$Email = $_POST['Email'];
		$Phone = $_POST['Phone'];
		$Address = $_POST['Address'];
		$City = $_POST['City'];
		$State = $_POST['State'];
		$Zip = $_POST['Zip'];
		$JobUnit = $_POST['JobUnit'];

		$sql = "update accounts set account_name='$CompanyTitle', primary_contact='$PrimaryContact', email='$Email', phone='$Phone', address='$Address', city='$City', state='$State', zip='$Zip', job_unit='$JobUnit' where account_id= '".$_SESSION['ao_accountid']."'"; 
                $result = DBUtil::query($sql);
                if($result == true)
                {
                      return json_encode(array('status'=> 1,'message'=>"Profile details updated successfully!")); 
                }
                else
                {
                      return json_encode(array('status'=> 0,'message'=>"There are some error, can't save Supplier!"));
                }
		
}

//GetSystemConfigDetails By VIR
function GetSystemConfigDetails()
{
	if($_SESSION['ao_founder']!=1)
	{
		return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
	}
	else
	{
		//user list configuration array
		$userListConfiguration = array(
			'assign_job_salesman_user_dropdown' => 'Assign Job Salesman User List',
			'assign_job_referral_user_dropdown' => 'Assign Job Referral User List',
			'assign_job_subscriber_user_dropdown' => 'Assign Job Subscriber User List',
			'assign_journal_recipient_user_dropdown' => 'Assign Journal Recipient User List',
			'assign_repair_contractor_user_dropdown' => 'Assign Repair Contractor User List',
			'assign_task_contractor_user_dropdown' => 'Assign Task Contractor User List',
			'assign_job_canvasser_user_dropdown' => 'Assign Job Canvasser User List',
			'assign_job_referral_user_dropdown' => 'Assign Job Referral User List',
			'assign_job_salesman_user_dropdown' => 'Assign Job Salesman User List',
			'assign_task_contractor_user_dropdown' => 'Assign Task Contractor User List',
			'job_salesman_filter_user_dropdown' => 'Job Salesman Filter User List'
		);
		//get data
		$userlevels = UserModel::getAllLevels();
		$accountMetaData = AccountModel::getAllMetaData();
		
		$strAppConfigDetails = '<table border="0" cellpadding="0" cellspacing="0" class="main-view-table"><tr><td>';
		$strAppConfigDetails = $strAppConfigDetails. '<table border=0 width="100%" align="left" cellpadding=0 cellspacing=0 class="containertitle"><tr><td>Tasks</td></tr></table></td></tr>';
		$strAppConfigDetails = $strAppConfigDetails. '<tr><td><table border=0 width="100%" align="left" cellpadding=2 cellspacing=0 class="infocontainernopadding" id="interface-config">';
		$strAppConfigDetails = $strAppConfigDetails. '<tr><td class="listitemnoborder" width="30%"><b>Require Task Stage:</b></td><td class="listrownoborder">';
					$selUserVal = MetaUtil::get($accountMetaData, "require_task_stage") ? "checked" : "" ;
		$strAppConfigDetails = $strAppConfigDetails. '<input type="checkbox" data-key="require_task_stage" data-alert="true" class="onchange-set-meta updateRqrTaskStageMetaVal" data-type="account" '.$selUserVal.'/>';
		$strAppConfigDetails = $strAppConfigDetails. '</td></tr></table></td></tr></table><br />';
		$strAppConfigDetails = $strAppConfigDetails. '<table border="0" cellpadding="0" cellspacing="0" class="main-view-table "><tr><td>';
				
		$strAppConfigDetails = $strAppConfigDetails. '<table border=0 width="100%" align="left" cellpadding=0 cellspacing=0 class="containertitle"><tr><td>Users</td></tr></table></td></tr>';
		$strAppConfigDetails = $strAppConfigDetails. '<tr><td><table border=0 width="100%" align="left" cellpadding=2 cellspacing=0 class="infocontainernopadding" id="interface-config"><tr><td class="listitemnoborder" width="30%"><b>User Session Expiration:</b></td>';
		$strAppConfigDetails = $strAppConfigDetails. '<td class="listrownoborder"><select id ="ddlUserSessionExpMetaVal" data-key="user_session_timeout" data-alert="true" class="onchange-set-meta" data-type="account">';
		$strAppConfigDetails = $strAppConfigDetails. '<option value='.(60*15).'>15 minutes</option><option value='.(60*30).'>30 minutes</option><option value='.(60*60*1).'>1 hour</option>';
		$strAppConfigDetails = $strAppConfigDetails. '<option value='.(60*60*2).'>2 hours</option><option value='.(60*60*4).'>4 hours</option><option value='.(60*60*6).'>6 hours</option>';
		$strAppConfigDetails = $strAppConfigDetails. '<option value='.(60*60*8).'>8 hours</option><option value='.(60*60*12).'>12 hours</option><option value='.(60*60*24*1).'>1 day</option>';
		$strAppConfigDetails = $strAppConfigDetails. '<option value='.(60*60*24*7).'>1 Week</option><option value='.(60*60*24*14).'>2 Weeks</option><option value='.(60*60*24*20).'>1 Month</option></select>';
		$strAppConfigDetails = $strAppConfigDetails. '</td></tr>';
		$strAppConfigDetails = $strAppConfigDetails. '<tr><td class="listitem" width="30%"><b>Allow Mentions:</b></td>';
				$selMentionVal = MetaUtil::get($accountMetaData, "allow_mentions") ? "checked" : "";
		$strAppConfigDetails = $strAppConfigDetails. '<td class="listrow"><input type="checkbox" data-key="allow_mentions" data-alert="true" class="onchange-set-meta updateAllowMentionMetaVal" data-type="account" '.$selMentionVal.' /></td>';
		$strAppConfigDetails = $strAppConfigDetails. '</tr></table></td></tr></table><br />';
		$strAppConfigDetails = $strAppConfigDetails. '<table border="0" cellpadding="0" cellspacing="0" class="main-view-table"><tr><td>';
		$strAppConfigDetails = $strAppConfigDetails. '<table border=0 width="100%" align="left" cellpadding=0 cellspacing=0 class="containertitle"><tr><td>User Lists</td></tr></table></td></tr>';
		$strAppConfigDetails = $strAppConfigDetails. '<tr><td><table border=0 width="100%" align="left" cellpadding=2 cellspacing=0 class="infocontainernopadding app_con" id="interface-config">';
				
		$strAppConfigDetails = $strAppConfigDetails. '<tr><td class="listitemnoborder" width="30%"><b>Show Inactive Users In Lists:</b></td><td class="listrownoborder">';
				$selInactiveVal = MetaUtil::get($accountMetaData, "show_inactive_users_in_lists") ? "checked" : "";
		
		$strAppConfigDetails = $strAppConfigDetails. '<input type="checkbox" data-key="show_inactive_users_in_lists" data-alert="true" class="onchange-set-meta updateInactiveUserMetaVal" data-type="account" '.$selInactiveVal.' />';
		$strAppConfigDetails = $strAppConfigDetails. '</td></tr>';
		$index = 1;
				foreach($userListConfiguration as $metaKey => $title) 
				{
						$selectedVals = explode(',', MetaUtil::get($accountMetaData, $metaKey, ''));
						$strAppConfigDetails = $strAppConfigDetails. '<tr><td class="listitem" width="30%"><label><b>'.$title.':</b></label>';
						$strAppConfigDetails = $strAppConfigDetails. '<label class="smallnote">No groups selected will show all users.</label></td>';
				
						$strAppConfigDetails = $strAppConfigDetails. '<td class="listrow"><select id="drddefaultdmeta_'.$index.'" data-key='.$metaKey.' data-alert="true" class="onchange-set-meta multi-select drddefaultdmeta" data-type="account" multiple="true">';
						$seconddrd='<select id="drdselectedmeta_'.$index.'" data-key='.$metaKey.' data-alert="true" class="onchange-set-meta multi-select drdselectedmeta" data-type="account" multiple="true">';
						foreach($userlevels as $userLevel) 
						{
							$selectVal=in_array(MapUtil::get($userLevel, "level_id"), $selectedVals) ? "selected" : "";

							if($selectVal!="")
								$seconddrd = $seconddrd. '<option value='.$userLevel["level_id"].' '.$selectVal.'>'.MapUtil::get($userLevel, "level").'</option>';
							else
								$strAppConfigDetails = $strAppConfigDetails. '<option value='.$userLevel["level_id"].' '.$selectVal.'>'.MapUtil::get($userLevel, "level").'</option>';
						}
						reset($userlevels);
						$strAppConfigDetails = $strAppConfigDetails. '</select><div><img alt="img" src="images/switch.png" /></div>'.$seconddrd.'</select></td></tr>';
						$index++;
				}
		$strAppConfigDetails = $strAppConfigDetails. '</table></td></tr></table>';
		
		return json_encode(array('strAppConfigDetails'=> $strAppConfigDetails,'accountMetaData' => $accountMetaData)); 
	}
	 
}

//UpdateUserAccountMetaValue By VIR
function UpdateUserAccountMetaValue()
{
	if(isset($_POST['meta_key']) && isset($_POST['meta_value'])) {
		$meta_key = $_POST['meta_key'];
		$meta_value = $_POST['meta_value'];

		$sql = "select id, account_id, meta_key, meta_value from account_meta where meta_key = '$meta_key' AND account_id = '".$_SESSION['ao_accountid']."' ";
		$userMetaArray = DBUtil::queryToArray($sql);
        $metaId = $userMetaArray[0]['id'];
		
		if(empty($userMetaArray))
		{	
			
			$sql = "INSERT INTO account_meta VALUES (NULL, '".$_SESSION['ao_accountid']."', '$meta_key', '$meta_value')";
			$result = DBUtil::query($sql);
			
		    if($result == true)
			{		
					
					$meta_key = str_replace('_', ' ', $meta_key);
					return json_encode(array('status'=> 1,'message'=>"$meta_key saved ")); 
			}
			else
			{
			      return json_encode(array('status'=> 0,'message'=>"Operation failed!"));
			}
		}
		else
		{   
			 $sql = "delete from account_meta where id = '$metaId' limit 1";
             $isDelete = DBUtil::query($sql);
			 
			if($isDelete)
				{
					
					$sql = "INSERT INTO account_meta VALUES (NULL, '".$_SESSION['ao_accountid']."', '$meta_key', '$meta_value')";
					$result = DBUtil::query($sql);
					if($result == true)
					{	  
						  $meta_key = str_replace('_', ' ', $meta_key);
					      return json_encode(array('status'=> 1,'message'=>"$meta_key saved ")); 
					}
					else
					{
					      return json_encode(array('status'=> 0,'message'=>"Operation failed!"));
					}
				}
				else
					{
					      return json_encode(array('status'=> 0,'message'=>"Operation failed!"));
					}
		} 
	}
}

//GetJobTypeList By VIR
function GetJobTypeList()
{
	if($_SESSION['ao_founder']!=1)
	{
		return json_encode(array('status'=> 0,'message'=>"You do not have permission to access this."));
	}
	else
	{
        $jobTypes = JobUtil::getAllJobTypes();
        return json_encode(array('jobTypes'=> $jobTypes));
	}
}

	//AddJobType By VIR
    function AddJobType()
    {
     if(isset($_POST['title'])) 
      {
        $title = $_POST['title'];
        $accountid = $_SESSION['ao_accountid'];
        
         $sql = "INSERT INTO job_type (account_id, job_type)
                VALUES ('$accountid', '$title')";
         $result = DBUtil::query($sql);
         
         if($result == "true")
         {
            return json_encode(array('status'=> 1,'message'=>" '$title' successfully added")); 
         }
         else
         {
            return json_encode(array('status'=> 0,'message'=>"Record not saved successfully")); 
         }
      }
    }

 //DeleteJobTypeById By VIR
    function DeleteJobTypeById()
    {
     if(isset($_POST['JobTypeID'])) 
      {
        $JobTypeID = $_POST['JobTypeID'];
        $accountid = $_SESSION['ao_accountid'];
        
		$job = DBUtil::getRecord('jobs', $JobTypeID, 'job_type');

		if(count($job)) {
                return json_encode(array('status'=> 0,'message'=>"Job currently associated - cannot remove!")); 
        }
        else
        {
            $sql = "DELETE FROM job_type  WHERE job_type_id = '$JobTypeID' AND account_id = '$accountid' LIMIT 1";
            $result = DBUtil::query($sql);
        
            if($result == "true")
            {
                return json_encode(array('status'=> 1,'message'=>"Job type successfully removed")); 
            }
            else
            {
                return json_encode(array('status'=> 0,'message'=>"Job currently associated - cannot remove!")); 
            }
        }
        
      }
    }  
    
	function GetSecurityLevelDetails()
	{
		$levelID = $_POST['levelId'];
		if($_SESSION['ao_founder']!=1)
		{
			return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
		}
		else
		{
			$sql = "select level_id, level from levels where level_id='$levelID' limit 1";
			$res = DBUtil::query($sql);

		    $strSecurityLevels = '<table width="100%" border="0" class="data-table" cellpadding=2 cellspacing="0">';
				list($level_id, $level)=mysqli_fetch_row($res);
				if(mysqli_num_rows($res)==0) {
					$strSecurityLevels = $strSecurityLevels. '<tr><td align="center" colspan=2><b>Level Not Found</b></td></tr>';
				}
				else
				{

					$strSecurityLevels = $strSecurityLevels. '<tr valign=top><td width=16 style="padding: 5px;"><img src=../../images/icons/key_16.png></td><td class="smalltitle" style="padding: 5px;">'. $level.'</td></tr>';
					$strSecurityLevels = $strSecurityLevels. '<tr><td align="center" colspan=2><table width=95% border="0">';
					$strSecurityLevels = $strSecurityLevels. '<tr><td colspan=2><b>Module Access</b><span class="smallnote"><br />A = Access<br />O = Ownership required </span></td></tr>';
					$strSecurityLevels = $strSecurityLevels. '<tr><td><table border="0" width="100%"><tr valign=top><td width=50%><table border="0" width="100%"><tr><td align="center" width=20><b>A</b></td><td align="center" width=20><b>O</b></td><td>&nbsp;</td></tr>';

						$sql = "select modules.module_id, modules.title, module_access.module_access_id, module_access.ownership, modules.ownership from modules
								left join module_access
								on modules.module_id=module_access.module_id and module_access.account_id='".$_SESSION['ao_accountid']."' and module_access.level='".$level_id."'
								group by modules.module_id order by modules.title asc";
						$res = DBUtil::query($sql);

						$rowBreak = round(mysqli_num_rows($res)/2);
						$i=0;
						while(list($module_id, $title, $access, $owner, $has_ownership)=mysqli_fetch_row($res))
						{
						  if($i==$rowBreak)
						  {
							$strSecurityLevels = $strSecurityLevels. '</table> </td><td width="50%"><table border="0" width="100%">';
							$strSecurityLevels = $strSecurityLevels. '<tr><td align="center" width=20><b>A</b></td>';
							$strSecurityLevels = $strSecurityLevels. '<td align="center" width=20><b>O</b></td><td>&nbsp;</td></tr>';

							$i=0;
						  }

							$checked = '';
							if($access!='')
							$checked = "checked";
							
							$strSecurityLevels = $strSecurityLevels. '<tr><td align="center" width=20>';
							$strSecurityLevels = $strSecurityLevels. '<input id=chkToggle_'.$module_id.'  type="checkbox"  class="chkToggle"  name='.$module_id.'  '.$checked.'>';
							$strSecurityLevels = $strSecurityLevels. '</td>';
							
							$disabled = 'disabled';
							if($access!='' && $has_ownership==1)
							  $disabled = '';
							$checked_owner = '';
							if($owner==1)
							  $checked_owner = "checked";

							$strSecurityLevels = $strSecurityLevels. '<td align="center" width=20>';
							$strSecurityLevels = $strSecurityLevels. '<input  id="chkownership_'.$module_id.'"  type="checkbox"  class="chkownership"  '.$disabled.  ' name='.$module_id.' '.$checked_owner.'>';
							$strSecurityLevels = $strSecurityLevels. '</td><td>'. $title .'</td></tr>';
					
							$i++;
						}

					$strSecurityLevels = $strSecurityLevels. '</table></td></tr></table></td></tr>';
					$strSecurityLevels = $strSecurityLevels. '<tr><td>';
					$strSecurityLevels = $strSecurityLevels. '<a id="aLinkChkAllModules"  class=basiclink>Check All</a>';
					$strSecurityLevels = $strSecurityLevels. '&nbsp;&nbsp; | &nbsp;&nbsp;';
					$strSecurityLevels = $strSecurityLevels. '<a  id="aLinkUnChkAllModules"  class=basiclink>Uncheck All</a>';

					$strSecurityLevels = $strSecurityLevels. '</td></tr></table></td></tr>';
					$strSecurityLevels = $strSecurityLevels. '<tr><td align="center" colspan=2><table width="95%" border="0"><tr><td colspan=2><b>Stage Advancement Access</b></td></tr>';
					$strSecurityLevels = $strSecurityLevels. '<tr><td><table border="0" width="100%"><tr valign="top"><td width="50%"><table border="0" width="100%">';
							$sql = "select stages.stage_id, stages.stage, stage_access.level_id from stages
									left join stage_access
									on (stage_access.stage_id=stages.stage_id and stage_access.level_id='".$level_id."')
									where stages.account_id='".$_SESSION['ao_accountid']."'
									order by stages.stage_num asc";
							$res = DBUtil::query($sql);

							if(mysqli_num_rows($res)==0)
							{
								$strSecurityLevels = $strSecurityLevels. '<tr><td>No Stages</td></tr>';
							}

							$rowBreak = round(mysqli_num_rows($res)/2);
							$i=0;
							while(list($stage_id, $stage, $access)=mysqli_fetch_row($res))
							{
								if($i==$rowBreak)
								{
									$strSecurityLevels = $strSecurityLevels. ' </table></td><td width="50%"><table border="0" width="100%">';
									$i=0;
								}
								$checked = '';
								if($access!='')
									$checked = "checked";

								$strSecurityLevels = $strSecurityLevels. '<tr><td align="center" width=20>';
								$strSecurityLevels = $strSecurityLevels. '<input  id="chkstage_'.$stage_id.'"  type="checkbox" class="chkstage" name='. $stage_id .' '.$checked.' &stage='. $stage_id.'>';
								$strSecurityLevels = $strSecurityLevels. '</td><td>'. $stage .'</td></tr>';
								
								$i++;
							}
							$strSecurityLevels = $strSecurityLevels. '</table></td></tr></table></td></tr>';
							if(mysqli_num_rows($res)!=0)
							{
								$strSecurityLevels = $strSecurityLevels. '<tr><td>';
								$strSecurityLevels = $strSecurityLevels. '<a id="aLinkChkAllStages" class=basiclink>Check All</a>';
								$strSecurityLevels = $strSecurityLevels. '&nbsp; | &nbsp;';
								$strSecurityLevels = $strSecurityLevels. '<a id="aLinkUnChkAllStages"  class=basiclink>Uncheck All</a>';
								$strSecurityLevels = $strSecurityLevels. '</td></tr>';
							}
							$strSecurityLevels = $strSecurityLevels. '</table></td></tr>';
							$strSecurityLevels = $strSecurityLevels. '<tr><td align="center" colspan=2><table width="95%" border="0"><tr><td colspan=2><b>Navigation Access</b></td></tr>';
							$strSecurityLevels = $strSecurityLevels. '<tr><td><table border="0" width="100%"><tr valign="top"><td width="50%"><table border="0" width="100%"><tr></tr>';
							
							$sql = "select navigation.navigation_id, navigation.title, navigation.icon, nav_access.navaccess_id
								    from navigation
									left join nav_access
									on navigation.navigation_id=nav_access.navigation_id and nav_access.account_id='".$_SESSION['ao_accountid']."' and nav_access.level='".$level_id."'
									order by navigation.order_num asc";
							$res = DBUtil::query($sql);

							$navigationArray = UIModel::getNavList();

							$rowBreak = round(count($navigationArray) / 2);
							$i = 0;
							while(list($navigation_id, $title, $icon, $access)=mysqli_fetch_row($res))
							{
								if($i == $rowBreak) 
								{
									$strSecurityLevels = $strSecurityLevels. '</table></td><td width="50%"><table border="0" width="100%">';
									$i = 0;
								}
								$checked = '';
								if($access) {
									$checked = "checked";
								}

								$strSecurityLevels = $strSecurityLevels. '<tr><td align="center" width=20>';
								$strSecurityLevels = $strSecurityLevels. '<input  id="chkNavigation_'.$navigation_id.'"  type="checkbox" class="chkNavigation" name='. $navigation_id .' '. $checked .'>';

								$strSecurityLevels = $strSecurityLevels. '</td><td><i class=icon-'.$icon.'></i>&nbsp;'.$title.'</td></tr>';

								$i++;
							}
							$strSecurityLevels = $strSecurityLevels. '</table></td></tr></table></td></tr></table></td></tr>';
							$strSecurityLevels = $strSecurityLevels. '<tr><td colspan=10>&nbsp;</td></tr><tr>';
							$strSecurityLevels = $strSecurityLevels. '<td colspan=10 class="infofooter">';
							$strSecurityLevels = $strSecurityLevels. '</td></tr></table>';
			}

		    return json_encode(array('status'=> 1,'strSecurityLevels'=> $strSecurityLevels));
		}
	}

	//Update User Permission By VIR
  function UpdateSecurityLevelPermission()
  {
	if(isset($_POST['ModuleId']) && isset($_POST['LevelID']) && isset($_POST['Action']) && isset($_POST['checked']))
	 {
		$ActionName = $_POST['Action'];
		$ModuleId = $_POST['ModuleId'];
		$levelId = $_POST['LevelID'];
		$isChecked = $_POST['checked'];
		
		if($ActionName == 'chkToggle')
		{	
			
			if($isChecked == 'false')
			{
				$sql = "delete from module_access where module_id='$ModuleId' and level='$levelId' and account_id='".$_SESSION['ao_accountid']."' limit 1";
			}
			else
			{	
				$sql = "insert into module_access values(0, '$ModuleId', '".$_SESSION['ao_accountid']."', '$levelId', 0)";
			}
			$result = DBUtil::query($sql);
			
            if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permission")); 
			}
		}
		else if($ActionName == 'chkAllModules')
		{
			 $sql = "delete from module_access where level='$levelId' and account_id='".$_SESSION['ao_accountid']."'";
			 $result = DBUtil::query($sql);
			 
			 if($result == true)
			 {
				 $sql = "select module_id from modules";
			     $res = DBUtil::query($sql);
			     while(list($module_id)=mysqli_fetch_row($res))
			     {
			       $sql = "insert into module_access values(0, '".$module_id."', '".$_SESSION['ao_accountid']."', '$levelId', 0)";
			       DBUtil::query($sql);
			     }
			     return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
			 }
			 else
			 {
			 	return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permission")); 
			 }
		}
		else if($ActionName == 'UnChkAllModules')
		{
			 $sql = "delete from module_access where level='$levelId' and account_id='".$_SESSION['ao_accountid']."'";
			 $result = DBUtil::query($sql);
			 
			 if($result == true)
			 {
				 return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
			 }
			 else
			 {
			 	return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permission")); 
			 }
		}
		else if($ActionName == 'chkallstages')
		{
			$sql = "delete from stage_access where level_id='$levelId' and account_id='".$_SESSION['ao_accountid']."'";
			$result = DBUtil::query($sql);
            if($result == true)
            {
				$sql = "select stage_id from stages where account_id='".$_SESSION['ao_accountid']."'";
				$res = DBUtil::query($sql);
				while(list($stage_id)=mysqli_fetch_row($res))
				{
				  $sql = "insert into stage_access values(0, '".$stage_id."', '$levelId', '".$_SESSION['ao_accountid']."')";
				  DBUtil::query($sql);
				}
                return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permission")); 
			}
		}
		else if($ActionName == 'UnChkallstages')
		{
			 $sql = "delete from stage_access where level_id='$levelId' and account_id='".$_SESSION['ao_accountid']."'";
			 $result = DBUtil::query($sql);
			 
			 if($result == true)
			 {
				 return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
			 }
			 else
			 {
			 	return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permission")); 
			 }
		}
		else if($ActionName == 'chkownership')
		{
			if($isChecked == 'false')
			{
				$sql = "update module_access set ownership=0 where module_id='$ModuleId' and account_id='".$_SESSION['ao_accountid']."' and level='$levelId' limit 1";
			}
			else
			{
				$sql = "update module_access set ownership=1 where module_id='$ModuleId' and account_id='".$_SESSION['ao_accountid']."' and level='$levelId' limit 1";
			}
			
			$result = DBUtil::query($sql);
			
            if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permission")); 
			}
		}
		else if($ActionName == 'chknavigation')
		{	
			// Here $module_id contains the value for navigation_id
			if($isChecked == 'false')
			{	
				$sql = "delete from nav_access where navigation_id='$ModuleId' and level='$levelId' and account_id='".$_SESSION['ao_accountid']."' limit 1";
			}
			else 
			{
				$sql = "insert into nav_access values(0, '$ModuleId', '".$_SESSION['ao_accountid']."', '$levelId')";
			}
			$result = DBUtil::query($sql);
			
			if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permissiony!")); 
			}
			
		}
		else if($ActionName == 'chkstage')
		{
			// Here $module_id contains the value for stage_id
			if($isChecked == 'false')
			{
				$sql = "delete from stage_access where stage_id='$ModuleId' and level_id='$levelId' limit 1";
			}
			else
			{
				$sql = "insert into stage_access values(0, '$ModuleId', '$levelId', '".$_SESSION['ao_accountid']."')";
			}
			$result = DBUtil::query($sql);

			if($result == true)
            {
                return json_encode(array('status'=> 1,'message'=>"Permission updated successfully!")); 
            }
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error! , can't update Permissiony!")); 
			}
			
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=>"Can't perform any action!")); 
		}
	 }
  }

//GetSMSTextTemplates By VIR
function GetSMSTextTemplates()
{
	if($_SESSION['ao_founder']!=1)
	{
		return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
	}
	else
	{
        $account_hooks = array('[>ACCOUNTURL<]', '[>ACCOUNTNAME<]');
		$account_values = array(ACCOUNT_URL, $_SESSION['ao_accountname']);
		
		$from_hooks = array('[>FROMFNAME<]', '[>FROMLNAME<]', '[>FROMEMAIL<]', '[>FROMUSERNAME<]', '[>FROMPASSWORD<]');
		$from_values = array('John', 'Smith', 'john.smith@' . DOMAIN, 'john.smith', 'password123');
		
		$to_hooks = array('[>TOFNAME<]', '[>TOLNAME<]', '[>TOEMAIL<]', '[>TOUSERNAME<]', '[>TOPASSWORD<]');
		$to_values = array('Jane', 'Smith', 'jane.smith@' . DOMAIN, 'jane.smith', 'password123');
		
		$job_hooks = array('[>JOBNUMBER<]', '[>JOBID<]', '[>CUSTFNAME<]', '[>CUSTLNAME<]', '[>CUSTADDRESS<]', '[>CUSTCITY<]', '[>CUSTSTATE<]', '[>CUSTZIP<]', '[>CUSTPHONE<]', '[>SALESMANFNAME<]', '[>SALESMANLNAME<]', '[>HASH<]', '[>STAGENUM<]', '[>CSVSTAGES<]');
		$job_values = array('ABC12345', '1111', 'John', 'Doe', '123 Any Street', 'Anytown', 'AL', '12345', '555-555-5555', 'Dave', 'Miller', '30cfbbc70a63835d9d0a83132ddb1111', '2', 'Estimate Request');
		
		$task_hooks = array('[>CONTRACTORFNAME<]', '[>CONTRACTORLNAME<]', '[>TASKTYPE<]', '[>DURATION<]', '[>NOTES<]');
		$task_values = array('Mike', 'Johnson', 'Gutters', '5', 'Notes on the Gutter Task');
		
		$event_hooks = array('[>EVENTTITLE<]', '[>EVENTSTARTDATE<]', '[>EVENTENDDATE<]', '[>EVENTDESCRIPTION<]', '[>EVENTTIME<]');
		$event_values = array('Company Event', '12-05-2013', '12-05-2013', 'Company event to celebrate', '1:00 PM');
		
		$journal_hooks = array('[>JOURNALTEXT<]');
		$journal_values = array('Went to property and assessed damage on roof and gutters');
        
		$strSmsTemplates = '<table border="0" cellpadding="0" cellspacing="0" class="main-view-table">';
		$strSmsTemplates = $strSmsTemplates. '<tr><td><b>Jump to:</b><select id="ddljump">';
		$strSmsTemplates = $strSmsTemplates. '<option value=""></option>';
						   $sql = "select sms_template_id, subject from sms_templates where account_id='".$_SESSION['ao_accountid']."' order by subject asc";
						   $res = DBUtil::query($sql);
								  while(list($id, $subject)=mysqli_fetch_row($res))
								  {
										$strSmsTemplates = $strSmsTemplates. '<option value="template_'. $id .'">'.$subject.'</option>';
								  }
		$strSmsTemplates = $strSmsTemplates. '</select>';
		$strSmsTemplates = $strSmsTemplates. '<input type="button" value="REVERT ALL TEMPLATES" id="btnRevertAllTemplates" class="greybtn_comn">';
		$strSmsTemplates = $strSmsTemplates. '</td></tr><tr><td>';
		$strSmsTemplates = $strSmsTemplates. '<table border=0 width="100%" align="left" cellpadding=0 cellspacing=0 class="containertitle">';
		$strSmsTemplates = $strSmsTemplates. '<tr><td width=250>Subject</td><td>Body</td></tr></table>';
		$strSmsTemplates = $strSmsTemplates. '</td></tr><tr><td>';
		$strSmsTemplates = $strSmsTemplates. '<table border=0 width="100%" align="left" cellpadding=5 cellspacing=0 class="infocontainernopadding">';
							$sql = "select sms_template_id, subject, text, is_active from sms_templates where account_id='".$_SESSION['ao_accountid']."' order by subject asc";
							$res = DBUtil::query($sql);
							$i=1;
							while(list($id, $subject, $body, $is_active)=mysqli_fetch_row($res))
							{
							  $class='odd';
							  if($i%2==0)
							    $class='even';
							
							  $checked = '';
							  if($is_active == '1')
							  {
							      $checked = 'checked';
							  }

		$strSmsTemplates = $strSmsTemplates. '<tr id="tempTr'. $id.'" valign=top class='.$class.'><td class="smalltitle" width=239>';
		$strSmsTemplates = $strSmsTemplates. '<a name="template'. $id .'"></a>';
		$strSmsTemplates = $strSmsTemplates. '<form method="post" name="template'. $id.'" style="margin-bottom:0;">';
		$strSmsTemplates = $strSmsTemplates. '<b>'. $subject .'</b>';
		$strSmsTemplates = $strSmsTemplates. '<input class="form_input form-control validation validate[required[Subject cannot be empty]]" type=text id="subject_'.$id.'"  name="subject_'.$id.'"  size=30 value="'. $subject.'">';
		$strSmsTemplates = $strSmsTemplates. '<input name="id"  value='.$id.' type="hidden">';
		$strSmsTemplates = $strSmsTemplates. '<input type="submit" value="Save" id="btnSaveSmsTemplate_'.$id.'" class="btnSaveSmsTemplate  form_submit bulebtn_comn">';
		$strSmsTemplates = $strSmsTemplates. '<div class="navuserinfo">Active: <input type="checkbox" id="chkActive_'.$id.'" name="chkActive_'.$id.'" value="1"  '.$checked.' /></div>';
		$strSmsTemplates = $strSmsTemplates. '<input type=button value="Revert to Original" id="btnRevertToOriginal_'.$id.'" class="btnRevertToOriginal greybtn_comn">';
		$strSmsTemplates = $strSmsTemplates. '</td><td><textarea class=" form-control validation validate[required[Template body cannot be empty]]" id="tempBody_'.$id.'" name="tempBody_'.$id.'" style="width:100%;" rows=5>'.trim($body).'</textarea>';
		$strSmsTemplates = $strSmsTemplates. '</form></td></tr>';

							$body = str_replace($account_hooks, $account_values, $body);
							$body = str_replace($from_hooks, $from_values, $body);
							$body = str_replace($to_hooks, $to_values, $body);
							$body = str_replace($job_hooks, $job_values, $body);
							$body = str_replace($task_hooks, $task_values, $body);
							$body = str_replace($event_hooks, $event_values, $body);
							$body = str_replace($journal_hooks, $journal_values, $body);

		$strSmsTemplates = $strSmsTemplates. '<tr valign=top class='. $class .'><td></td>';
		$strSmsTemplates = $strSmsTemplates. '<td><b>Preview:</b>';
		$strSmsTemplates = $strSmsTemplates. '<table style="background: #ffffff; border: 1px solid #0085CB; font-family: courier; font-size: 12px; margin-left: 2px;" width=200 cellspacing=0 cellpadding=2>';
		$strSmsTemplates = $strSmsTemplates. '<tr><td>Subject: '. $subject .'<br>'.$body.'</td></tr></table></td></tr>';
		$strSmsTemplates = $strSmsTemplates. '<tr valign=top  class='.$class.'>';
		$strSmsTemplates = $strSmsTemplates. '<td colspan=2  align=center>';
		$strSmsTemplates = $strSmsTemplates. '<a id="aLinkTop"  class="aLinkTop">Top</a></td></tr>';
							$i++;
						}

		$strSmsTemplates = $strSmsTemplates. '</table></td></tr><tr><td colspan=2>&nbsp;</td></td></table>';
		
		return json_encode(array('status'=> 1,'strSmsTemplates'=> $strSmsTemplates));
	}
}

//UpdateSMSTextTemplates By VIR
function UpdateSMSTextTemplates()
{
	if(isset($_POST['id']) && isset($_POST['subject']) && isset($_POST['body']) && isset($_POST['active']))
	{
		$id = $_POST['id'];
		$subject = $_POST['subject'];
		$body = $_POST['body'];
		$is_active = $_POST['active'];

		$body = mysqli_real_escape_string(DBUtil::Dbcont(),$body);
		$sql = 'update sms_templates set subject="'.$subject.'", text="'.$body.'", is_active="'.$is_active.'" where sms_template_id="'.$id.'" and account_id="'.$_SESSION['ao_accountid'].'" limit 1';
		$res = DBUtil::query($sql);
		if($res)
		{
			return json_encode(array('status'=> 1,'message'=> "Template Updated"));
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=> "There are some error, while updating Template!"));
		}
	}
}

//RevertSmsTextTemplateContent By VIR
function RevertSmsTextTemplateContent()
{
	if(isset($_POST['id']) && isset($_POST['action']))
	{
		$id = $_POST['id'];
		$action = $_POST['action'];
		
		
			$sql = "select hook from sms_templates where sms_template_id='".$id."' and account_id='".$_SESSION['ao_accountid']."' limit 1";
			$res = DBUtil::query($sql);
			list($hook)=mysqli_fetch_row($res);
			
			if($hook!='')
			{
			  $sql = "select subject, text from sms_templates_default where hook='".$hook."' limit 1";
			  $res = DBUtil::query($sql);
			  
			  list($subject, $text, $is_active)=mysqli_fetch_row($res);
			  
			  $sql = 'update sms_templates set subject="'.$subject.'", text="'.$text.'", is_active="'.$is_active.'" where sms_template_id="'.$id.'" and account_id="'.$_SESSION['ao_accountid'].'" limit 1';
			  $res = DBUtil::query($sql);
			  
			  if($res)
				{
					return json_encode(array('status'=> 1,'message'=> "Template successfully reverted to original"));
				}
				else
				{
					return json_encode(array('status'=> 0,'message'=> "There are some error, while reverting Template to original!"));
				}
			}
		
	}
}

//RevertALLSmsTextTemplateContent By VIR
function RevertALLSmsTextTemplateContent()
{
		$action = $_POST['action'];
		$sql = "select subject, text, hook from sms_templates_default";
		$res = DBUtil::query($sql);
		list($hook)=mysqli_fetch_row($res);

		while(list($subject, $text, $hook, $is_active)=mysqli_fetch_row($res))
		{
		   $sql = 'update sms_templates set subject="'.$subject.'", text="'.$text.'", is_active="'.$is_active.'" where hook="'.$hook.'" and account_id="'.$_SESSION['ao_accountid'].'"';
		   DBUtil::query($sql);
		}
		return json_encode(array('status'=> 1,'message'=> "All templates successfully reverted to originals"));
}
//GetUserGroupList By VIR
function GetUserGroupList()
{
		
		if($_SESSION['ao_founder']!=1)
		{
				return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
		}
		else
		{
				$sql = "select usergroups.usergroup_id, usergroups.label, count(usergroups_link.usergroups_link_id) as count from usergroups left outer join usergroups_link on (usergroups_link.usergroup_id=usergroups.usergroup_id) where account_id='".$_SESSION['ao_accountid']."' group by usergroups.usergroup_id order by label asc";
				$res = DBUtil::queryToArray($sql);
				return json_encode(array('status'=> 1,'userGroupList'=>$res));
		}
}
//DeleteUserGroupById By VIR
function DeleteUserGroupById()
{
	if(isset($_POST['UserGroupID']))
	{
		$groupId = $_POST['UserGroupID'];
		$sql = "delete from usergroups where usergroup_id='$groupId' and account_id='".$_SESSION['ao_accountid']."' limit 1";
		$res = DBUtil::query($sql);

		if($res)
		{
			return json_encode(array('status'=> 1,'message'=> "User Group Deleted"));
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=> "There are some error, while deleting User Group!"));
		}
	}
}
//AddUserGroup By VIR
function AddUserGroup()
{
	if(isset($_POST['title']))
	{
		$groupTitle = $_POST['title'];
		$sql = " insert into usergroups values(0, '".$_SESSION['ao_accountid']."', '$groupTitle')";
		$res = DBUtil::query($sql);
		if($res)
		{
			return json_encode(array('status'=> 1,'message'=> "New Group Added"));
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=> "There are some error, while add new User Group!"));
		}
	}
}
//assignUserToUserGroupList By VIR
function assignUserToUserGroupList()
{	
	if(isset($_POST['UserGroupID']))
	{
		$groupId = $_POST['UserGroupID'];
		if($_SESSION['ao_founder']!=1)
		{
				return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
		}
		else
		{
				$sql = "select users.fname, users.lname, concat(users.fname, ', ' ,users.lname) as FullName, users.user_id, usergroups_link.usergroups_link_id, usergroups.label from users 
						left join usergroups_link on (usergroups_link.user_id=users.user_id and usergroups_link.usergroup_id='$groupId') 
						left join usergroups on (usergroups.usergroup_id='$groupId')
						where users.account_id='".$_SESSION['ao_accountid']."' and users.is_active=1 and users.is_deleted=0 order by users.lname asc";
				$userList = DBUtil::queryToArray($sql);
				
				$sqlLabel = "select usergroup_id, label from usergroups where usergroup_id='$groupId'";
				$groupTitle = DBUtil::queryToArray($sqlLabel);

				return json_encode(array('userGroupList'=>$userList, 'groupTitle' => $groupTitle));
		}
	}
}
//UpdateUserGroupsUserDetails By VIR
function UpdateUserGroupsUserDetails()
{
	if(isset($_POST['groupID']) && isset($_POST['userList']))
	{
		$groupId = $_POST['groupID'];
		$user_list = $_POST['userList'];
		
		if($groupId != '' && $groupId != 'null')
		{
			$sql = "delete from usergroups_link where usergroup_id='$groupId'";
			DBUtil::query($sql);
			
			$myArray = explode(',', $user_list);
			
			foreach($myArray as $myArray){
				$sql = " insert into usergroups_link values(0, '$groupId', '".$myArray."')";
						 DBUtil::query($sql);
			}
			
			return json_encode(array('status'=> 1,'message'=> "List Updated"));
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=> "User Group can not be empty!"));
		}
	}
}

//GetEmailTemplates By VIR
function GetEmailTemplates()
{
	if($_SESSION['ao_founder']!=1)
	{
		return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
	}
	else
	{
        $account_hooks = array('[>ACCOUNTURL<]', '[>ACCOUNTNAME<]');
		$account_values = array(ACCOUNT_URL, $_SESSION['ao_accountname']);
		
		$from_hooks = array('[>FROMFNAME<]', '[>FROMLNAME<]', '[>FROMEMAIL<]', '[>FROMUSERNAME<]', '[>FROMPASSWORD<]');
		$from_values = array('John', 'Smith', 'john.smith@'. DOMAIN, 'john.smith', 'password123');
		
		$to_hooks = array('[>TOFNAME<]', '[>TOLNAME<]', '[>TOEMAIL<]', '[>TOUSERNAME<]', '[>TOPASSWORD<]');
		$to_values = array('Jane', 'Smith', 'jane.smith@'. DOMAIN, 'jane.smith', 'password123');
		
		$job_hooks = array('[>JOBNUMBER<]', '[>JOBID<]', '[>CUSTFNAME<]', '[>CUSTLNAME<]', '[>CUSTADDRESS<]', '[>CUSTCITY<]', '[>CUSTSTATE<]', '[>CUSTZIP<]', '[>CUSTPHONE<]', '[>SALESMANFNAME<]', '[>SALESMANLNAME<]', '[>HASH<]', '[>STAGENUM<]', '[>CSVSTAGES<]');
		$job_values = array('ABC12345', '1111', 'John', 'Doe', '123 Any Street', 'Anytown', 'AL', '12345', '555-555-5555', 'Dave', 'Miller', '30cfbbc70a63835d9d0a83132ddb1111', '2', 'Estimate Request');
		
		$task_hooks = array('[>CONTRACTORFNAME<]', '[>CONTRACTORLNAME<]', '[>TASKTYPE<]', '[>DURATION<]', '[>NOTES<]');
		$task_values = array('Mike', 'Johnson', 'Gutters', '5', 'Notes on the Gutter Task');
		
		$event_hooks = array('[>EVENTTITLE<]', '[>EVENTSTARTDATE<]', '[>EVENTENDDATE<]', '[>EVENTDESCRIPTION<]', '[>EVENTTIME<]');
		$event_values = array('Company Event', '12-05-2013', '12-05-2013', 'Company event to celebrate', '1:00 PM');
		
		$journal_hooks = array('[>JOURNALTEXT<]');
		$journal_values = array('Went to property and assessed damage on roof and gutters');



        
		$strEmailTemplates = '<table border="0" cellpadding="0" cellspacing="0" class="main-view-table">';
		$strEmailTemplates = $strEmailTemplates. '<tr><td><b>Jump to:</b><select id="ddlEmailJump">';
		$strEmailTemplates = $strEmailTemplates. '<option value=""></option>';
						   $sql = "select email_template_id, subject from email_templates where account_id='".$_SESSION['ao_accountid']."' order by subject asc";
						   $res = DBUtil::query($sql);
								  while(list($id, $subject)=mysqli_fetch_row($res))
								  {
										$strEmailTemplates = $strEmailTemplates. '<option value="template_'. $id .'">'.$subject.'</option>';
								  }
		$strEmailTemplates = $strEmailTemplates. '</select>';
		$strEmailTemplates = $strEmailTemplates. '<input type="button" value="REVERT ALL TEMPLATES" id="btnRevertAllTemplates" class="greybtn_comn">';
		$strEmailTemplates = $strEmailTemplates. '</td></tr><tr><td>';
		$strEmailTemplates = $strEmailTemplates. '<table border=0 width="100%" align="left" cellpadding=0 cellspacing=0 class="containertitle">';
		$strEmailTemplates = $strEmailTemplates. '<tr><td width=250>Subject</td><td>Body</td></tr></table>';
		$strEmailTemplates = $strEmailTemplates. '</td></tr><tr><td>';
		$strEmailTemplates = $strEmailTemplates. '<table border=0 width="100%" align="left" cellpadding=5 cellspacing=0 class="infocontainernopadding">';
							$sql = "select email_template_id, subject, text, is_active from email_templates where account_id='".$_SESSION['ao_accountid']."' order by subject asc";
							$res = DBUtil::query($sql);
							$i=1;
							while(list($id, $subject, $body, $isActive)=mysqli_fetch_row($res))
							{
							  $class='odd';
							  if($i%2==0)
								$class='even';
							
							  $checked = '';
							  if($isActive == '1')
							  {
							      $checked = 'checked';
							  }
							  
		$strEmailTemplates = $strEmailTemplates. '<tr id="tempTr'. $id.'" valign=top class='.$class.'><td class="smalltitle" width=239>';
		$strEmailTemplates = $strEmailTemplates. '<a name="template'. $id .'"></a>';
		$strEmailTemplates = $strEmailTemplates. '<form method="post" name="template'. $id.'" style="margin-bottom:0;">';
		$strEmailTemplates = $strEmailTemplates. '<b>'. $subject .'</b>';
		$strEmailTemplates = $strEmailTemplates. '<input class="form_input form-control validation validate[required[Subject cannot be empty]]" type=text id="subject_'.$id.'"  name="subject_'.$id.'"  size=30 value="'. $subject.'">';
		$strEmailTemplates = $strEmailTemplates. '<input name="id"  value='.$id.' type="hidden">';
		//$strEmailTemplates = $strEmailTemplates. '<input type="submit" value="Save" id="btnSaveEmailTemplate_'.$id.'" class="btnSaveEmailTemplate_  form_submit bulebtn_comn">';
		//$strEmailTemplates = $strEmailTemplates. '<div class="navuserinfo">Active: <input type="checkbox" id="chkActive_'.$id.'" name="chkActive_'.$id.'" value="1"  '.$checked.' /></div>';
		//$strEmailTemplates = $strEmailTemplates. '<input type=button value="Revert to Original" id="btnRevertToOriginal_'.$id.'" class="btnRevertToOriginal greybtn_comn">';
		$strEmailTemplates = $strEmailTemplates. '</td><td><textarea class=" form-control validation validate[required[Template body cannot be empty]]" id="tempBody_'.$id.'" name="tempBody_'.$id.'" style="width:100%;" rows=5>'.trim($body).'</textarea>';
		$strEmailTemplates = $strEmailTemplates. '</form></td></tr>';

							$body = str_replace($account_hooks, $account_values, $body);
							$body = str_replace($from_hooks, $from_values, $body);
							$body = str_replace($to_hooks, $to_values, $body);
							$body = str_replace($job_hooks, $job_values, $body);
							$body = str_replace($task_hooks, $task_values, $body);
							$body = str_replace($event_hooks, $event_values, $body);
							$body = str_replace($journal_hooks, $journal_values, $body);
							
							$body = preg_replace("#<a.*?>([^>]*)</a>#i", "", $body);

		$strEmailTemplates = $strEmailTemplates. '<tr valign=top class='. $class .'><td></td>';
		$strEmailTemplates = $strEmailTemplates. '<td><b>Preview:</b>';
		$strEmailTemplates = $strEmailTemplates. '<table style="background: #ffffff; border: 1px solid #0085CB; font-family: courier; font-size: 12px; margin-left: 2px;" width=200 cellspacing=0 cellpadding=2>';
		if($id != 24 && $id != 55 && $id != 51 && $id != 35 && $id != 29 && $id != 37 && $id != 1)
		{   
			$strEmailTemplates = $strEmailTemplates. '<tr><td>'. $subject .'<br>'.$body.'<br /><a id="alinkViewJobMsgHistory" href="jobdetails.html?JId=1111">Click here to view the Job Details</a></td></tr></table></td></tr>';
		}
		else
		{
			$strEmailTemplates = $strEmailTemplates. '<tr><td>'. $subject .'<br>'.$body.'</td></tr></table></td></tr>';
		}
		$strEmailTemplates = $strEmailTemplates. '<tr valign=top  class='.$class.'>';
		$strEmailTemplates = $strEmailTemplates. '<td colspan=2>Active: <input type="checkbox" id="chkActive_'.$id.'" name="chkActive_'.$id.'" value="1"  '.$checked.' /></td><td colspan=2  align=center><input type="submit" value="Save" id="btnSaveEmailTemplate_'.$id.'" class="btnSaveEmailTemplate_  form_submit bulebtn_comn"><input type=button value="Revert to Original" id="btnRevertToOriginal_'.$id.'" class="form_submit btnRevertToOriginal greybtn_comn">';
		$strEmailTemplates = $strEmailTemplates. '<a id="aLinkTop"  class="aLinkTop">Top</a></td></tr>';
							$i++;
						}

		$strEmailTemplates = $strEmailTemplates. '</table></td></tr><tr><td colspan=2>&nbsp;</td></td></table>';
		
		return json_encode(array('status'=> 1,'strEmailTemplates'=> $strEmailTemplates));
	}
}

//UpdateEmailTemplates By VIR
function UpdateEmailTemplates()
{
	if(isset($_POST['tempId']) && isset($_POST['subject']) && isset($_POST['body']) && isset($_POST['active']))
	{
		$id = $_POST['tempId'];
		$subject = $_POST['subject'];
		$body = $_POST['body'];
		$is_active = $_POST['active'];

		$body  = str_replace("_and_","&",$body);
		
		$body = mysqli_real_escape_string(DBUtil::Dbcont(),$body);
		
		$sql = 'update email_templates set subject="'.$subject.'", text="'.$body.'", is_active="'.$is_active.'" where email_template_id="'.$id.'" and account_id="'.$_SESSION['ao_accountid'].'" limit 1';
		$res = DBUtil::query($sql);
		
		if($res)
		{
			return json_encode(array('status'=> 1,'message'=> "Template Updated"));
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=> "There are some error, while updating Template!"));
		}
	}
}

//RevertEmailTemplateContent By VIR
function RevertEmailTemplateContent()
{
	if(isset($_POST['id']) && isset($_POST['action']))
	{
		$id = $_POST['id'];
		$action = $_POST['action'];
		
			$sql = "select hook from email_templates where email_template_id='".$id."' and account_id='".$_SESSION['ao_accountid']."' limit 1";
			$res = DBUtil::query($sql);
			list($hook)=mysqli_fetch_row($res);
			
			if($hook!='')
			{
			  $sql = "select subject, text, is_active from email_templates_default where hook='".$hook."' limit 1";
			  $res = DBUtil::query($sql);
			  
			  list($subject, $text, $isActive)=mysqli_fetch_row($res);
			  
			  $sql = 'update email_templates set subject="'.$subject.'", text="'.$text.'", is_active="'.$isActive.'" where email_template_id="'.$id.'" and account_id="'.$_SESSION['ao_accountid'].'" limit 1';
			  $res = DBUtil::query($sql);
			  
			  if($res)
				{
					return json_encode(array('status'=> 1,'message'=> "Template successfully reverted to original"));
				}
				else
				{
					return json_encode(array('status'=> 0,'message'=> "There are some error, while reverting Template to original!"));
				}
			}
		
	}
}

//RevertALLEmailTemplateContent By VIR
function RevertALLEmailTemplateContent()
{
		
		$sql = "select subject, text, hook, is_active from email_templates_default";
		$res = DBUtil::query($sql);
		list($hook)=mysqli_fetch_row($res);

		while(list($subject, $text, $hook, $isActive)=mysqli_fetch_row($res))
		{
		   $sql = 'update email_templates set subject="'.$subject.'", text="'.$text.'", is_active="'.$isActive.'" where hook="'.$hook.'" and account_id="'.$_SESSION['ao_accountid'].'"';
		   DBUtil::query($sql);
		}
		return json_encode(array('status'=> 1,'message'=> "All templates successfully reverted to originals"));
}

//GetJurisdictionList By VIR
function GetJurisdictionList()
{
	if(!ModuleUtil::checkIsFounder()) {
		return json_encode(array('status'=> 0,'message'=>"You do not have permission to access this."));
	}
	else
	{
		  $jurisdictions = CustomerModel::getAllJurisdictions();
          return json_encode(array('status'=> 1, 'jurisdictions' => $jurisdictions));
	}
}

//GetJurisdictionDetailForEDIT By VIR
function GetJurisdictionDetailForEDIT()
{	
	if(isset($_POST['juryId']))
	{
		$id = $_POST['juryId'];
		
		if(!ModuleUtil::checkIsFounder()) {
			return json_encode(array('status'=> 0,'message'=>"You do not have permission to access this."));
		}
		else
		{
			$sql = "select * FROM jurisdiction WHERE jurisdiction_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
			$jurisdictionDetails = DBUtil::queryToArray($sql);
			
			return json_encode(array('status'=> 1, 'jurisdictionDetails'=> $jurisdictionDetails));
		}
	}
	else
	{}
}

//deleteJurisdictionItem By VIR
function deleteJurisdictionItem()
{
	if(isset($_POST['id']))
	{
		$id = $_POST['id'];
		$job = DBUtil::getRecord('jobs', $id, 'jurisdiction');
		if(count($job)) {
			return json_encode(array('status'=> 0,'message'=>"Jobs currently associated - cannot remove"));
		}
		else {
			$sql = "DELETE FROM jurisdiction WHERE jurisdiction_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
			DBUtil::query($sql);
			return json_encode(array('status'=> 1,'message'=>"Jurisdiction successfully removed"));
		}
	}
}

//AddJurisdictionItem By VIR
function AddJurisdictionItem()
{
	if(isset($_POST['title']) && isset($_POST['midroof_timing']) && isset($_POST['length']) && isset($_POST['ladder']) && isset($_POST['url']))
	{
		$title = $_POST['title'];
		$midroof_timing = $_POST['midroof_timing'];
		$midroof = $midroof_timing ? 1 : 0;
		$length = $_POST['length'];
		$ladder = $_POST['ladder'];
		$url = $_POST['url'];

		$sql = "INSERT INTO jurisdiction
                VALUES (NULL, '{$_SESSION['ao_accountid']}', '{$title}', '{$midroof}', '{$midroof_timing}', '{$ladder}', '{$url}', '{$length}')";
        $result = DBUtil::query($sql);
		
		if($result)
		{
			return json_encode(array('status'=> 1,'message'=>"Jurisdiction successfully added"));
		}
		else
		{
			return json_encode(array('status'=> 1,'message'=>"There are some error, while add new Jurisdiction!"));
		}
	}
}

//UpdateJurisdictionDetails By VIR
function UpdateJurisdictionDetails()
{
	if(isset($_POST['id']) && isset($_POST['title']) && isset($_POST['midroof_timing']) && isset($_POST['length']) && isset($_POST['ladder']) && isset($_POST['url']))
	{
		$id = $_POST['id'];
		$title = $_POST['title'];
		$midroof_timing = $_POST['midroof_timing'];
		$midroof = $midroof_timing ? 1 : 0;
		$length = $_POST['length'];
		$ladder = $_POST['ladder'];
		$url = $_POST['url'];

		$jurisdiction = DBUtil::getRecord('jurisdiction');
		if(empty($jurisdiction)) {
				return json_encode(array('status'=> 0,'message'=>"Jurisdiction not found!"));
		}
		else
		{
				$sql = "update jurisdiction set account_id = '{$_SESSION['ao_accountid']}', location = '$title', midroof = '$midroof', midroof_timing = '$midroof_timing', ladder = '$ladder', permit_url = '$url', permit_days = '$length' where jurisdiction_id = '$id' LIMIT 1";
				$result = DBUtil::query($sql);
				
				if($result)
				{
					return json_encode(array('status'=> 1,'message'=>"Jurisdiction updated successfully"));
				}
				else
				{
					return json_encode(array('status'=> 0,'message'=>"There are some error, while updating Jurisdiction!"));
				}
		}
	}
}
//GetInsProviderList By VIR
function GetInsProviderList()
{
	if(!ModuleUtil::checkIsFounder()) {
		return json_encode(array('status'=> 0,'message'=>"You do not have permission to access this."));
	}
	else
	{
		  $providers = InsuranceModel::getAllProviders();
          return json_encode(array('InsProviders' => $providers));
	}
}
//DeleteInsProvider By VIR
function DeleteInsProvider()
{
	if(isset($_POST['ProviderID']))
	{
		$id = $_POST['ProviderID'];
		$job = DBUtil::getRecord('jobs', $id, 'insurance_id');
		if(count($job)) {
			return json_encode(array('status'=> 0,'message'=>"Jobs currently associated - cannot remove"));
		}
		else {
			$sql = "DELETE FROM insurance WHERE insurance_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
			DBUtil::query($sql);
			return json_encode(array('status'=> 1,'message'=>"Insurance provider successfully removed"));
		}
	}
}

//AddInsProvider By VIR
function AddInsProvider()
{
	if(isset($_POST['title']))
	{
		$name = $_POST['title'];

		$sql = "INSERT INTO insurance (account_id, insurance) VALUES ('{$_SESSION['ao_accountid']}', '$name')";
        $res = DBUtil::query($sql);
		
		if($res)
		{
			return json_encode(array('status'=> 1,'message'=> "'$name' successfully added"));
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=> "There are some error, while add new provider!"));
		}
	}
}

//GetJobOriginList By VIR
function GetJobOriginList()
{
	if(!ModuleUtil::checkIsFounder()) {
		return json_encode(array('status'=> 0,'message'=>"You do not have permission to access this."));
	}
	else
	{
		  $origins = JobUtil::getAllOrigins();
          return json_encode(array('jobOrigins' => $origins));
	}
}

//DeleteJobOrigin By VIR
function DeleteJobOrigin()
{
	if(isset($_POST['JOriginID']))
	{
		$id = $_POST['JOriginID'];
		$job = DBUtil::getRecord('jobs', $id, 'origin');
		if(count($job)) {
			return json_encode(array('status'=> 0,'message'=>"Jobs currently associated - cannot remove"));
		}
		else {
			$sql = "DELETE FROM origins WHERE origin_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
			DBUtil::query($sql);
			return json_encode(array('status'=> 1,'message'=>"Job origin successfully removed"));
		}
	}
}

//AddInsProvider By VIR
function AddJobOrigin()
{
	if(isset($_POST['title']))
	{
		$name = $_POST['title'];

		$sql = "INSERT INTO origins (account_id, origin) VALUES ('{$_SESSION['ao_accountid']}', '$name')";
		$res = DBUtil::query($sql);
		
		if($res)
		{
			return json_encode(array('status'=> 1,'message'=> "'$name' successfully added"));
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=> "There are some error, while add Job Origin!"));
		}
	}
}

//GetJobOriginList By VIR
function GetModOfficeList()
{
	if(!ModuleUtil::checkIsFounder()) {
		return json_encode(array('status'=> 0,'message'=>"You do not have permission to access this."));
	}
	else
	{
		  $offices = AccountModel::getAllOffices();
          return json_encode(array('modOffice' => $offices));
	}
}

//DeleteJobOrigin By VIR
function DeleteModOffice()
{
	if(isset($_POST['officeID']))
	{
		$id = $_POST['officeID'];
		$user = DBUtil::getRecord('users', $id, 'office_id');
		if(count($user)) {
			return json_encode(array('status'=> 0,'message'=>"Users currently associated - cannot remove"));
		}
		else {
			$sql = "DELETE FROM offices WHERE office_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
			DBUtil::query($sql);
			return json_encode(array('status'=> 1,'message'=>"Office successfully removed"));
		}
	}
}

// GetStateListforOffice By VIR
function GetStateListforOffice()
{
	$stateArray = getStates();
	return json_encode(array('stateArray' => $stateArray));
}

// GetOfficeDetailForEDIT By VIR
function GetOfficeDetailForEDIT()
{
	if(isset($_POST['officeID']))
	{
		$id = $_POST['officeID'];
		
		if(!ModuleUtil::checkIsFounder()) {
			return json_encode(array('status'=> 0,'message'=>"You do not have permission to access this."));
		}
		else
		{

			$OfficeSql = "select * from offices where office_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
			$officeDetails = DBUtil::queryToArray($OfficeSql);
			if(empty($officeDetails))
			{
				$officeDetails='0';
			}
			return json_encode(array('officeDetails'=> $officeDetails));
		}
	}
}

// UpdateModOfficeDetails By VIR
function UpdateModOfficeDetails()
{
	if(isset($_POST['id']) && isset($_POST['title']) && isset($_POST['phone']) && isset($_POST['fax']) && isset($_POST['address']) && isset($_POST['city']) && isset($_POST['state']) && isset($_POST['zip']))
	{	
		$id = $_POST['id'];
		$title = $_POST['title'];
		$phone = $_POST['phone'];
		$fax = $_POST['fax'];
		$address = $_POST['address'];
		$city = $_POST['city'];
		$state = $_POST['state'];
		$zip = $_POST['zip'];
		
		$OfficeSql = "select * from offices where office_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
		$officeDetails = DBUtil::queryToArray($OfficeSql);
		if(empty($officeDetails))
		{
			return json_encode(array('status' => '0','message' => 'Office not found!', 'officeDetails'=> $officeDetails));
		}
		else
		{
			$sql = "update offices set account_id = '{$_SESSION['ao_accountid']}', title = '$title', phone = '$phone', fax = '$fax', address = '$address', city = '$city', state = '$state', zip = '$zip' where office_id = '$id' LIMIT 1";
				$result = DBUtil::query($sql);
				
				if($result)
				{
					return json_encode(array('status'=> 1,'message'=>"Office details updated successfully."));
				}
				else
				{
					return json_encode(array('status'=> 0,'message'=>"There are some error, while updating Office!"));
				}
		}
	}
}

// AddModOfficeDetails By VIR
function AddModOfficeDetails()
{
	if(isset($_POST['title']) && isset($_POST['phone']) && isset($_POST['fax']) && isset($_POST['address']) && isset($_POST['city']) && isset($_POST['state']) && isset($_POST['zip']))
	{	
		$title = $_POST['title'];
		$phone = $_POST['phone'];
		$fax = $_POST['fax'];
		$address = $_POST['address'];
		$city = $_POST['city'];
		$state = $_POST['state'];
		$zip = $_POST['zip'];
		
		
		$sql = "insert into offices values (NULL,'{$_SESSION['ao_accountid']}', '$title', '$phone', '$fax', '$address', '$city', '$state', '$zip')";
		$result = DBUtil::query($sql);
		
		if($result)
		{
			return json_encode(array('status'=> 1,'message'=>"Office added successfully."));
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=>"There are some error, while add Office!"));
		}
		
	}
}


//GetTaskTypeList By VIR
function GetTaskTypeList()
{
	if(!ModuleUtil::checkIsFounder()) {
		return json_encode(array('status'=> 0,'message'=>"You do not have permission to access this."));
	}
	else
	{
		  $strTaskTypeList = '<table class="table table-bordered table-condensed table-striped"><thead><tr><th>Name</th><th>Auto Tasks</th><th width="10%" class="acenter">Actions</th></tr></thead><tbody>';
		  
		  $taskTypes = TaskModel::getAllTaskTypes();
		  foreach($taskTypes as $taskType) {
			$autoCreateTasks = TaskUtil::getAutoCreateTasks(MapUtil::get($taskType, "task_type_id"));
			$autoCreateTasksArr = array();
			foreach($autoCreateTasks as $autoCreateTask) {
			    $autoCreateTasksArr[] = '<i class="icon-circle" style="color: ' . MapUtil::get($autoCreateTask, "color") . ' ;"></i>&nbsp;' . MapUtil::get($autoCreateTask, 'task');
			}

			$strTaskTypeList = $strTaskTypeList. '<tr><td><i class="icon-circle" style="color: ' . MapUtil::get($taskType, "color") . '"></i>&nbsp;&nbsp;'. MapUtil::get($taskType, "task");
			$taskName = implode(', ', $autoCreateTasksArr);
			
			$strTaskTypeList = $strTaskTypeList. '<td>'. $taskName .'</td>';
			$strTaskTypeList = $strTaskTypeList. '<td class="acenter"><div class="btn-group">';
			$strTaskTypeList = $strTaskTypeList. '<a class="aEditTaskType" href="javascript:;" id="aEditTaskType_' . MapUtil::get($taskType, "task_type_id") . '"><i class="icon-pencil"></i></a>';
			$strTaskTypeList = $strTaskTypeList. '&nbsp;&nbsp;<a class="aRemoveTaskType" href="javascript:;" id="aRemoveTaskType_' . MapUtil::get($taskType, "task_type_id") . '"><i class="icon-remove"></i></a>';
			$strTaskTypeList = $strTaskTypeList. '</div></td></tr>';
		  }
		  $strTaskTypeList = $strTaskTypeList. '</tbody></table></div>';
		  return json_encode(array('status'=> 1, 'strTaskTypeList' => $strTaskTypeList));
	}
}

function getAllTaskTypeForDDl()
{
	$taskTypes = TaskModel::getAllTaskTypes();
	return json_encode(array('taskTypes' => $taskTypes));
}

//DeleteTaskType By VIR
function DeleteTaskType()
{
	if(isset($_POST['taskTypeID']))
	{
		$id = $_POST['taskTypeID'];
		$task = DBUtil::getRecord('tasks', $id, 'task_type');
		if(count($task)) {
			return json_encode(array('status'=> 0,'message'=>"Tasks currently associated - cannot remove"));
		}
		else {
			$sql = "DELETE FROM task_type WHERE task_type_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
			DBUtil::query($sql);

			//auto-create
			$taskSql = "UPDATE auto_create_tasks SET active = 0 WHERE task_type_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}'";
			DBUtil::query($taskSql);

			return json_encode(array('status'=> 1,'message'=>"Task type successfully removed"));
		}
	}
}

// UpdateTaskTypeDetails By VIR
function UpdateTaskTypeDetails()
{
	if(isset($_POST['id']) && isset($_POST['taskName']) && isset($_POST['taskColor']))
	{	
		$id = $_POST['id'];
		$name = $_POST['taskName'];
		$color = $_POST['taskColor'];
		$taskList =  $_POST['taskList'];
				
		$sql = "select * from task_type where task_type_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
		$tasktype = DBUtil::queryToArray($sql);
		if(empty($tasktype))
		{
			return json_encode(array('status' => '0','message' => 'Could not retrieve task type data'));
		}
		else
		{
			$sql = "update task_type set account_id = '{$_SESSION['ao_accountid']}', task = '$name', color = '$color' where task_type_id = '$id' LIMIT 1";
				$result = DBUtil::query($sql);
				
				if($result)
				{	
					//$autoCreateTasks = TaskUtil::getAutoCreateTasks($id);
					//TaskUtil::updateAutoCreateTasks($id, $autoCreateTasks);

					//$sqlDelautoTask = "delete from auto_create_tasks where task_type_id = '$id'";
					//DBUtil::query($sqlDelautoTask);

					$myArray = explode(',', $taskList);
					$myArray = array_filter($myArray);

					if (!empty($myArray)) {
						foreach($myArray as $myArray){

							$taskSql = "select * from auto_create_tasks where task_type_id = '$id' AND child_task_type_id = '".$myArray."' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
							$tasktype = DBUtil::queryToArray($taskSql);
							
							if(empty($tasktype))
							{	
								$sql = " insert into auto_create_tasks values(NULL,'{$_SESSION['ao_accountid']}', '{$_SESSION['ao_userid']}' , $id ,  '".$myArray."',1)";
								DBUtil::query($sql);
							}
						}
					}
										
					return json_encode(array('status'=> 1,'message'=>"Task type updated successfully."));
				}
				else
				{
					return json_encode(array('status'=> 0,'message'=>"There are some error, while updating task type!"));
				}
		}
	}
}

// AddTaskTypeDetails By VIR
function AddTaskTypeDetails()
{
	if(isset($_POST['taskName']) && isset($_POST['taskColor']))
	{	
		$name = $_POST['taskName'];
		$color = $_POST['taskColor'];
		$taskList =  $_POST['taskList'];

		$sql = "INSERT INTO task_type (account_id, task, color) VALUES ('{$_SESSION['ao_accountid']}', '$name', '$color')";
        $result = DBUtil::query($sql);
		$taskId = DBUtil::getInsertId();

		
		$myArray = explode(',', $taskList);
		$myArray = array_filter($myArray);

		if (!empty($myArray)) {
			foreach($myArray as $myArray){
				$sql = " insert into auto_create_tasks values(NULL,'{$_SESSION['ao_accountid']}', '{$_SESSION['ao_userid']}' , $taskId ,  '".$myArray."',1)";
				DBUtil::query($sql);
			}
		}
		
		if($result)
		{
			return json_encode(array('status'=> 1,'message'=>"Task type added successfully."));
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=>"There are some error, while add Task type!"));
		}
		
	}
}

// GetTaskTypeDetailForEDIT By VIR
function GetTaskTypeDetailForEDIT()
{
	if(isset($_POST['taskTypeID']))
	{
		$id = $_POST['taskTypeID'];
		
		if(!ModuleUtil::checkIsFounder()) {
			return json_encode(array('status'=> 0,'message'=>"You do not have permission to access this."));
		}
		else
		{
			$autoCreateTasks = TaskUtil::getAutoCreateTasks($id);
			
			//$strAutoCreateTask = '<ul class="auto_create_task_ids" id="auto-create-tasks">';
			//$strAutoCreateTask = '';
			foreach($autoCreateTasks as $autoCreateTask) {
				$strAutoCreateTask = $strAutoCreateTask. '<li style="padding-bottom:5px;" class="liAutoCreateTaskList" value=' . MapUtil::get($autoCreateTask, "task_type_id") . '> <a class="aRemoveAutoTaskType" href="javascript:;" id="aRemoveAutoTaskType_' . MapUtil::get($autoCreateTask, "task_type_id") . '"><i class="icon-remove"></i></a>';
				$strAutoCreateTask = $strAutoCreateTask. '&nbsp;<i class="icon-circle" style="color: ' . MapUtil::get($autoCreateTask, "color") . '"></i>';
				$taskTitle = MapUtil::get($autoCreateTask, "task");
				$strAutoCreateTask = $strAutoCreateTask.   $taskTitle;
				$strAutoCreateTask = $strAutoCreateTask. '<input type="hidden" name="auto_create_tasks_id" value="' . MapUtil::get($autoCreateTask, "task_type_id") . '" />';
				$strAutoCreateTask = $strAutoCreateTask. '</li>';
			}
			//$strAutoCreateTask = $strAutoCreateTask. '</ul>';
			
			$taskSql = "select * from task_type where task_type_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
			$taskDetails = DBUtil::queryToArray($taskSql);
			if(empty($taskDetails))
			{
				$taskDetails='0';
			}
			return json_encode(array('taskDetails'=> $taskDetails, 'strAutoCreateTask' => $strAutoCreateTask));
		}
	}
}

// DeleteAutoCreateTaskById By VIR
   function DeleteAutoCreateTaskById()
   {             
        if(isset($_POST['deleteTaskID']) && isset($_POST['parentId'])) 
        {
              $taskId = $_POST['deleteTaskID'];
                      
              $sql = "delete from auto_create_tasks where task_type_id = '$parentId' and child_task_type_id = '$taskId' LIMIT 1";
              $result = DBUtil::query($sql);
			  
              if($result == true){
                      return json_encode(array('status'=> 1,'message'=>"Auto Create task deleted successfully!")); }
              else{
                      return json_encode(array('status'=> 0,'message'=>"There are some error, can't delete auto create task!"));}
             
        }
  }

    	// GetStatusHoldList By VIR
  function GetStatusHoldList()
  {
	$statusHolds = JobModel::getAllStatuses();
	return json_encode(array( 'statusHolds' => $statusHolds));
  }

    	// EditStatusHoldByID By VIR
  function EditStatusHoldByID()
  {
	if(isset($_POST['status']) && isset($_POST['statusColor']) && isset($_POST['statusId'])) 
	{
		$status = $_POST['status'];
		$color = $_POST['statusColor'];
		$statusId = $_POST['statusId'];

		$sql = "select * from status where status_id = '$statusId' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
		$statusDet = DBUtil::queryToArray($sql);
		if(empty($statusDet))
		{
			return json_encode(array('status' => '0','message' => 'Could not retrieve Status Hold details!'));
		}
		else
		{
			$sql = "update status set account_id = '{$_SESSION['ao_accountid']}', status = '$status', color = '$color' where status_id = '$statusId' LIMIT 1";
			$result = DBUtil::query($sql);

			if($result)
			{
				return json_encode(array('status'=> 1,'message'=>"Status hold modified"));
			}
			else
			{
				return json_encode(array('status'=> 0,'message'=>"There are some error, while modified Status Hold!"));
			}
		}
	}
  }

  	// DeleteStatusHoldByID By VIR
  function DeleteStatusHoldByID()
  {
	if(isset($_POST['statusID'])) 
	{
		$statusId = $_POST['statusID'];
		$statusHold = DBUtil::getRecord('status_holds', $statusId, 'status_id');
		if(count($statusHold)) {
		    return json_encode(array('status' => '0','message' => 'Jobs currently associated - cannot remove'));
		} else {
		    $sql = "DELETE FROM status WHERE status_id = '$statusId' and account_id='{$_SESSION['ao_accountid']}' LIMIT 1";
		    DBUtil::query($sql);
		    return json_encode(array('status' => '1','message' => 'Status hold has been removed.'));
		}
	}
  }

	// AddStatusHold By VIR
  function AddStatusHold()
  {
	if(isset($_POST['status']) && isset($_POST['statusColor'])) 
	{
		$status = $_POST['status'];
		$color = $_POST['statusColor'];

		$sql = "INSERT INTO status VALUES (NULL, '{$_SESSION['ao_accountid']}', '$status', '$color')";
        DBUtil::query($sql);
		return json_encode(array('status' => '1','message' => 'New status hold added.'));
    }
  }

     // GetSysModMaterialsCatList By VIR
  function GetSysModMaterialsCatList()
  {
	if($_SESSION['ao_founder']!=1)
	{
		return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
	}
	else
	{
		$categories_array = MaterialModel::getAllCategories();
		return json_encode(array('status'=> 1,'categoryList' => $categories_array ));
	}
  }

   // GetSysModMatDetailForCategory By VIR
   function GetSysModMatDetailForCategory()
   {  
      if(isset($_POST['CategoryID'])) 
      {   
	      $categoryId = $_POST['CategoryID'];
          $accountid =  $_SESSION['ao_accountid'];

          $catSql = "select category,category_id from categories where category_id=$categoryId";
          $brandSql = "select brand_id, brand from brands where account_id=$accountid order by brand asc";        
          $matDetailSql = "SELECT b.brand,b.brand_id, u.unit, m .* FROM materials m LEFT JOIN brands b ON b.brand_id = m.brand_id
                           LEFT JOIN units u ON u.unit_id = m.unit_id
                           WHERE m.category_id = $categoryId  ORDER BY m.brand_id desc";
          
          $categoryArray=   DBUtil::queryToArray($catSql);
          $brandlistArray=   DBUtil::queryToArray($brandSql);
          $matDetailSql=   DBUtil::queryToArray($matDetailSql);
          
          return json_encode(array("categoryArray" => $categoryArray,"brandlistArray" => $brandlistArray,"matDetailSql" => $matDetailSql));
      }
   }

  function DeleteSysModMaterial()
  {
	if(isset($_POST['materialID'])) 
    {
		$material_id = $_POST['materialID'];
		$sql = "select sheet_item_id from sheet_items where material_id='" . $material_id . "' limit 1";
		$res = DBUtil::query($sql);
		if (mysqli_num_rows($res) != 0) {
			return json_encode(array('status' => 0, 'message' => 'Jobs Currently Associated - Cannot Remove'));
		}
		else
		{
			$sql = "delete from materials where material_id='" . $material_id . "' and account_id='" . $_SESSION['ao_accountid'] . "' limit 1";
			DBUtil::query($sql);

			$sql = "delete from colors where material_id='" . $material_id . "'";
			DBUtil::query($sql);

			return json_encode(array('status' => 1, 'message' => 'Material deleted successfully!'));
		}
	}
  }

  function GetMaterialsBrand_Category_Unit()
  {
	$brands = MaterialModel::getAllBrands();
	$categories = MaterialModel::getAllCategories();
	$sql = "select unit_id, unit from units order by unit asc";
	$units = DBUtil::queryToArray($sql);

	return json_encode(array('brands' => $brands, 'categories' => $categories, 'units' => $units));
  }

  function AddNewModMaterialItem()
  {
	if($_SESSION['ao_founder']!=1)
	{
		return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
	}
	else
	{
		if(!count(MaterialModel::getAllBrands()) || !count(MaterialModel::getAllCategories())) 
		{ 
			return json_encode(array('status'=> 0,'message' => 'Please add brands and cateories first' )); 
		}
		else
		{
			$category = $_POST['CatId'];
			$brand = $_POST['BrandID'];
			$unit = $_POST['UnitID'];
			$materialName = $_POST['MatName'];
			$description = $_POST['desc'];
			$price = $_POST['Price'];

			$sql = "INSERT INTO materials VALUES (NULL, '$category', '$brand', '$unit', '{$_SESSION['ao_accountid']}', '$materialName', '$description', '$price', 1)";
			$result = DBUtil::query($sql);
			if($result)
			{ return json_encode(array('status'=> 1,'message' => 'New material added successfully!' )); }
			else
			{ return json_encode(array('status'=> 0,'message' => 'There are some error, while add new material' )); }
		}
	}
  }

  function UpdateModMaterialDetails()
  {
	if($_SESSION['ao_founder']!=1)
	{
		return json_encode(array('status'=> 0,'message'=>"Insufficient Rights"));
	}
	else
	{	
		$material_id = $_POST['MaterialID'];
		$category = $_POST['CatId'];
		$brand = $_POST['BrandID'];
		$unit = $_POST['UnitID'];
		$materialName = $_POST['MatName'];
		$description = $_POST['desc'];
		$price = $_POST['Price'];
		$active = $_POST['IsActive'];

		$sql = "UPDATE materials set category_id = '$category', brand_id = '$brand', unit_id = '$unit', account_id = '{$_SESSION['ao_accountid']}', material = '$materialName', info = '$description', price = '$price', active = '$active' where material_id = '$material_id' ";
        $result = DBUtil::query($sql);

		if($result)
		{ return json_encode(array('status'=> 1,'message' => 'Material details updated successfully!' )); }
		else
		{ return json_encode(array('status'=> 0,'message' => 'There are some error, while updating material details.' )); }
	}
  }

  function AddModMaterialColor()
  {
	
		$id = $_POST['MaterialID']; 
		$color = $_POST['color']; 

		$sql = "INSERT INTO colors (material_id, color) VALUES ('$id', '$color')";
        $result = DBUtil::query($sql);

		if($result)
		{
			return json_encode(array('status'=> 1,'message' => 'Material color "'.$color.'" successfully added' ));
		}
		else
		{
			return json_encode(array('status'=> 0,'message' => 'There are some error, while add new color for material' ));
		}
    
  }

  function DeleteModMaterialColor()
  {
	
		$colorId = $_POST['colorId']; 
		
		$sheets = DBUtil::getRecord('sheet_items', $colorId, 'color_id');
		if(!empty($sheets)) {
			return json_encode(array('status'=> 0,'message' => 'Jobs currently associated - cannot remove' ));
		}
		else
		{
			$sql = "DELETE FROM colors WHERE color_id = '$colorId' LIMIT 1";
			$result = DBUtil::query($sql);

			if($result)
			{
				return json_encode(array('status'=> 1,'message' => 'Material color successfully deleted' ));
			}
			else
			{
				return json_encode(array('status'=> 0,'message' => 'There are some error, while deleting material color' ));
			}
		}
  }


  function GetModMaterialEditDetailByID()
  {
	$materialID = $_POST['materialID'];

	$sql = "select * from materials where material_id = '$materialID'";
	$matDetails = DBUtil::queryToArray($sql);

	$sql = "select * from colors where material_id = '$materialID'";
	$matColors = DBUtil::queryToArray($sql);

	$colors = MaterialModel::getMaterialColors($materialID);
	$strColor = '';
	foreach($colors as $color) {
		$strColor = $strColor. '<li>';		
		$strColor = $strColor. '<a id="aRemovelinkMatColor_' . $color['color_id']  .'" class="aRemovelinkMatColor"><i class="icon-remove"></i></a>&nbsp;' . $color['color'];		
		$strColor = $strColor. '</li>';		
	}

	return json_encode(array('matDetails' => $matDetails, 'matColors' => $matColors, 'strColor' => $strColor));
  }

function GetBrandAndCategoryListforModifyMaterial()
{
	$brands_array = MaterialModel::getAllBrands();
	$categories = MaterialModel::getAllCategories();

	return json_encode(array('brands_array' => $brands_array, 'categories' => $categories));
}

function deleteBrandFromModifyMaterial()
{
	$id = $_POST['brandID'];
	$materials = DBUtil::getRecord('materials', $id, 'brand_id');
    if(count($materials)) {
        return json_encode(array('status' => 0, 'message' => "Materials currently Associated - Cannot Remove"));
    }
    else
	{
		$sql = "DELETE FROM brands WHERE brand_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
        $result = DBUtil::query($sql);
		if($result)
		{
			return json_encode(array('status' => 1, 'message' => "Brand deleted successfully"));
		}
		else
		{
			return json_encode(array('status' => 0, 'message' => "There are some error, while deleting Brand!"));
		}
    }
		
}

function insertBrandFromModifyMaterial()
{
	$title = $_POST['title'];
	$sql = "INSERT INTO brands VALUES (NULL, '{$_SESSION['ao_accountid']}', '$title')";
	DBUtil::query($sql);

	return json_encode(array('status' => 1, 'message' => "Brand added successfully"));
}

function updateBrandFromModifyMaterial()
{
	$id = $_POST['brandID'];
	$editTitle = $_POST['title'];
	$sql = "UPDATE brands SET brand = '$editTitle'  WHERE brand_id = '$id' AND account_id = '{$_SESSION['ao_accountid']}' LIMIT 1";
	DBUtil::query($sql);

	return json_encode(array('status' => 1, 'message' => "Brand updated successfully"));

}

function deleteCategoryFromModifyMaterial()
{
	$id = $_POST['catID'];
	$sql = "SELECT material_id FROM materials WHERE category_id = '$id' AND active = 1 LIMIT 1";
	$results = DBUtil::query($sql);
    if(DBUtil::hasRows($results)) {
        return json_encode(array('status' => 0, 'message' => "Materials currently Associated - Cannot Remove"));
    }
    else
	{
		$sql = "DELETE FROM categories WHERE category_id = '$id' AND account_id='{$_SESSION['ao_accountid']}' LIMIT 1";
		$result = DBUtil::query($sql);
		if($result)
		{
			return json_encode(array('status' => 1, 'message' => "Category deleted successfully"));
		}
		else
		{
			return json_encode(array('status' => 0, 'message' => "There are some error, while deleting Category!"));
		}
    }
		
}

function insertCategoryFromModifyMaterial()
{
	$title = $_POST['title'];
	$sql = "INSERT INTO categories VALUES (NULL, '{$_SESSION['ao_accountid']}', '$title')";
	DBUtil::query($sql);

	return json_encode(array('status' => 1, 'message' => "Category added successfully"));
}

function updateCategoryFromModifyMaterial()
{
	$id = $_POST['catID'];
	$editTitle = $_POST['title'];

	$sql = "UPDATE categories SET category = '$editTitle' WHERE category_id = '$id' AND account_id='{$_SESSION['ao_accountid']}' LIMIT 1";
	DBUtil::query($sql);

	return json_encode(array('status' => 1, 'message' => "Category updated successfully"));

}

function getUsersOffice_Level_SmsCarrier()
{
	$carriers = getAllSmsCarriers();
	$userLevels = UserModel::getAllLevels();
	$offices = AccountModel::getAllOffices();

    return json_encode(array("carriersArray" => $carriers,"userLevelsArray" => $userLevels,"officesArray" => $offices));
}

//InsertNewUser By VIR
function InsertNewUser()
{       
	 if(isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['uname']) && isset($_POST['Dba']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['smsCarrier']) && isset($_POST['Notes']) && isset($_POST['accessLevel']) && isset($_POST['office']) && isset($_POST['founder'])) 
       {    
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
		    $uname = $_POST['uname'];
            $Dba = $_POST['Dba'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
		    $smsCarrier = $_POST['smsCarrier'];
            $accessLevel = $_POST['accessLevel'];
            $office = $_POST['office'];
		    $founder = $_POST['founder'];
		    $notes = $_POST['Notes'];
            
		    $userNameSql = mysqli_query("select username from users where username = '$uname'");
		    $emailSql = mysqli_query("select email from users where email = '$email'");
            $phoneSql = mysqli_query("select phone from users where phone= '$phone'");
		  
		    if(mysqli_num_rows($userNameSql))
            {
              return json_encode(array('status'=> 0,'message'=>"Username in use"));
            }
		    else if(mysqli_num_rows($emailSql))
            {
              return json_encode(array('status'=> 0,'message'=>"Email in use"));
            }
		    else if(mysqli_num_rows($phoneSql))
            {
              return json_encode(array('status'=> 0,'message'=>"Phone Number in use"));
            }
            else
            {
				$password = UserUtil::generatePassword();
				
				$sql = "INSERT INTO users (username, fname, lname, password, dba, email, phone, sms_carrier, level, reg_date, account_id, founder, notes, office_id)
						VALUES ('$uname', '$fname', '$lname', '$password', '$Dba', '$email', '$phone', '$smsCarrier', '$accessLevel', now(), '{$_SESSION['ao_accountid']}', '$founder', '$notes', $office)";
				
				$result = DBUtil::query($sql);
				if($result == true)
				{		
					  $newUserID = DBUtil::getInsertId();
					  $sql = "INSERT INTO settings VALUES (0, '$newUserID', 15, 5, 180, 400, 1, 1, 1, 1, 1, 1, 1)";
					  DBUtil::query($sql);
				      UserModel::logAccess($newUserID);
					  NotifyUtil::emailFromTemplate('new_user', $newUserID);

				      return json_encode(array('status'=> 1,'message'=>"Record Inserted successfully!")); 
				}
				else
				{
				      return json_encode(array('status'=> 0,'message'=>"There are some error, while inserting new user!"));
				}
            }
       }
  }


  function GetScheduleUserAndTypeList()
  {
		$users_array = UserModel::getAll(TRUE, $firstLast);
		$taskTypes = TaskModel::getAllTaskTypes();

		return json_encode(array('userList'=> $users_array,'typeList'=> $taskTypes)); 
  }

  function GetSchedulerDetails()
  {
   
	if(!ModuleUtil::checkAccess('view_schedule'))
	{
			return json_encode(array('status'=> 0,'message'=>"Insufficient Rights")); 
	}
	else
	{
		$curDate = DateUtil::formatMySQLDate();	
	
		//get week start date
		if(date('w') == 1 &&  (!RequestUtil::get('ws')) ||  (RequestUtil::get('ws') == strtotime('12:00:00'))) {
			$week_start_date = strtotime('12:00:00');
		} else {
			$week_start_date = RequestUtil::get('ws') ?: strtotime('previous monday');
		}
		
		$scheduleView = $_POST['view'];
		if($scheduleView == 'weekview')
		{
			//get previous week start date and next week start date
			$previous_week_start_date = $week_start_date - 604800;
			$next_week_start_date = $week_start_date + 604800;
			
			//get filters
			//$user = RequestUtil::get('user');
			//$type = RequestUtil::get('type');
			$user = $_POST['userId'];
			$type = $_POST['typeId'];

			//set nav vars
			//$next = $_GET;
			//$prev = $_GET;
			$next = '';
			$prev = '';
			$prev["ws"] = $previous_week_start_date;
			$next["ws"] = $next_week_start_date;

			$monthVal = date('n', $week_start_date);
			$yearVal = date('y', $week_start_date);
			$ws = $week_start_date;
			
		//	$strSchedule = '<table class="containertitle" width="100%">' ;
//			$strSchedule = $strSchedule. '<tr><td style="width: 10%;">' ;
//			$strSchedule = $strSchedule. '<a class="btn btn-blue btn-small" href="" id="alinkPrev"  data-month='. $monthVal .'  data-year='. $yearVal .'  data-ws='. $prev["ws"] .'><i class="icon-angle-left"></i></a>' ;
//			$strSchedule = $strSchedule. '</td><td style="width: 60%;" align="center">' ;
//			$strSchedule = $strSchedule. 'Week Of  '. DateUtil::formatDate($week_start_date) .' ' ;
//			$strSchedule = $strSchedule. '</td><td style="width: 10%;">&nbsp;</td><td style="width: 10%;">&nbsp;</td><td align="right" style="width: 10%;">' ;
//			$strSchedule = $strSchedule. '<a class="btn btn-blue btn-small" href ="" id="alinkNext"  data-month='. $monthVal .'  data-year='. $yearVal .'  data-ws='. $next["ws"] .'><i class="icon-angle-right"></i></a>' ;
//			$strSchedule = $strSchedule. '</td></tr>' ;
//			$strSchedule = $strSchedule. '</table>' ;

			$strSchedule = $strSchedule. '<table border="0" class="schedulecontainer" width="100%">' ;
	$strSchedule = $strSchedule. '<tr><td  colspan="1" style="text-align: left; float: left;">' ;
			$strSchedule = $strSchedule. '<a class="btn btn-blue btn-small" style="float:left" href="" id="alinkPrev"  data-month='. $monthVal .'  data-year='. $yearVal .'  data-ws='. $prev["ws"] .'><i class="icon-angle-left"></i></a>' ;
			$strSchedule = $strSchedule. '</td><td align="center" colspan="5">' ;
			$strSchedule = $strSchedule. 'Week Of  '. DateUtil::formatDate($week_start_date) .' ' ;
			$strSchedule = $strSchedule. '</td><td align="left" colspan="1" >' ;
			$strSchedule = $strSchedule. '<a class="btn btn-blue btn-small" href ="" id="alinkNext"  data-month='. $monthVal .'  data-year='. $yearVal .'  data-ws='. $next["ws"] .'><i class="icon-angle-right"></i></a>' ;
			$strSchedule = $strSchedule. '</td></tr>' ;
			

			$strSchedule = $strSchedule. '<tr id="checkCalenderHeader">' ;
			$strSchedule = $strSchedule. '<td align="center" style="font-weight: bold;">Mon</td><td align="center" style="font-weight: bold;">Tue</td>' ;
			$strSchedule = $strSchedule. '<td align="center" style="font-weight: bold;">Wed</td><td align="center" style="font-weight: bold;">Thu</td>' ;
			$strSchedule = $strSchedule. '<td align="center" style="font-weight: bold;">Fri</td><td align="center" style="font-weight: bold;">Sat</td>' ;
			$strSchedule = $strSchedule. '<td align="center" style="font-weight: bold;">Sun</td>' ;
			$strSchedule = $strSchedule. '</tr>' ;
			
			$strSchedule = $strSchedule. '<tr valign="top" style="vertical-align: top !important;">' ;
			$current_date_in_iteration = $week_start_date;
							for($i = 0; $i < 7; $i++)
							{
								$date = date("Y-m-d", $current_date_in_iteration);
								
								$border = "border: 1px solid #999999; background-color: #ffffff;";
								$style ="style = 'border: 1px solid #999999; background-color: #ffffff;  width: 200px !important; height: 200px !important; vertical-align: top !important;'";
								
								if(date("Y-m-d") == $date)
								{
									$border = "border: 2px solid #0086CC; background-color: #ffffff;";
									$style ="style = 'border: 2px solid #0086CC; background-color: #ffffff;  width: 200px  !important; height: 200px !important; vertical-align: top !important;'";
								}
									$strSchedule = $strSchedule. '<td class="calendar-cell week"  '. $style .'>' ;
										$strSchedule = $strSchedule. '<table border="0" width="100%" cellspacing="0" cellpadding="0" class="table">' ;
											$strSchedule = $strSchedule. '<tr>' ;

												if(ModuleUtil::checkAccess('event_readwrite'))
												{
													$strSchedule = $strSchedule. '<td class="smallnote" align="left" width="100%" style="background-color: #ededed; border-bottom: 1px solid #cccccc;">' ;
													$strSchedule = $strSchedule. '<a class="boldlink" href="add_event.html?date='. $date .'&id=0" title="Add Event" tooltip>+</a>' ;
													$strSchedule = $strSchedule. '</td>' ;
												}
												else
												{
													$strSchedule = $strSchedule. '<td style="background-color: #ededed; border-bottom: 1px solid #cccccc;">&nbsp;</td>' ;
												}
												
												$strSchedule = $strSchedule. '<td class="smallnote" align="right" width="100%" style="background-color: #ededed; border-bottom: 1px solid #cccccc;">' ;
												$strSchedule = $strSchedule. '<b>'. date("j", $current_date_in_iteration) .'</b>' ;
												$strSchedule = $strSchedule. '</td>' ;
											
											$strSchedule = $strSchedule. '</tr>' ;
											$strSchedule = $strSchedule. '<tr>' ;
												$strSchedule = $strSchedule. '<td colspan=2>' ;
													
													//initialize empty arrays
													  $repairsArray = array();
													  $tasksArray = array();
													  $eventsArray = array();
													  $appointmentsArray = array();
													  $deliveriesArray = array();
													  $insuracenotification =array();
													  $insuracenotification2 =array();

													//get events
													if(empty($type)) 
													{
														$repairsArray = ScheduleUtil::getRepairs($date, $user);
														$tasksArray = ScheduleUtil::getTasks($date, $user);
														$eventsArray = ScheduleUtil::getEvents($date, $user);
														$appointmentsArray = ScheduleUtil::getAppointments($date, $user);
														$deliveriesArray = ScheduleUtil::getDeliveries($date, $user);
														$insuracenotification = ScheduleUtil::getInsuracenotification();

														$insuracenotification2 = ScheduleUtil::getInsuracenotification2();

														$days = "+7 day";
														//echo "<pre>"; print_r($fectchdata);
														$nextdate = strtotime($days, strtotime($curDate));
														$nextdate = date("Y-m-d", $nextdate);
														$em=array();

															if(count($insuracenotification2)>0)
															{
																foreach($insuracenotification2 as $data1)
																{
																	$em[] = $data1["email"];
																}
															}
													
													} 
													else 
													{
															if(strpos($type, "task_type=") !== false) 
															{
																$task_type_id = str_replace("task_type=", "", $type);
																$tasksArray = ScheduleUtil::getTasks($date, $user, $task_type_id);
															}
															switch($type) 
															{
																case "appointment":
																	$appointmentsArray = ScheduleUtil::getAppointments($date, $user);
																	break;
																case "delivery":
																	$deliveriesArray = ScheduleUtil::getDeliveries($date, $user);
																	break;
																case "event":
																	$eventsArray = ScheduleUtil::getEvents($date, $user);
																	break;
																case "repair":
																	$repairsArray = ScheduleUtil::getRepairs($date, $user);
																	break;
															}
													}
													foreach($repairsArray as $repair) {
														$repairTooltip = !empty($repair["completed"]) ? "Completed " . DateUtil::formatDate($repair["completed"]) . ". Click to view task details." : "View repair details"; 

														$strSchedule = $strSchedule. '<div class="schedule-item repair" style="border-left: 3px solid #ff3232; font-size: 12px; line-height: 15px; margin: 2px; padding: 4px 6px; position: relative;">' ;

														$strSchedule = $strSchedule. '<p style="padding-bottom:0px;">' ;
														if($repair["completed"] != '' && $repair["completed"] != 'null' )
														{
															$repairClass = "line-through";
														}
														else
														{
															$repairClass = "";
														}
														$strSchedule = $strSchedule. '<a class="'. $repairClass .'" href="get_repair.html?id='. $repair["repair_id"] .'&JId='. $repair["job_id"] .'" title="'. $repairTooltip .'" tooltip>'. $repair["fail_type"] .'</a>' ;
														$strSchedule = $strSchedule. '</p>' ;
														$strSchedule = $strSchedule. '<p style="width: 95%;  display: inline-block;"><a href="jobtabs.html?JId='. $repair["job_id"] .'" tooltip>'.$repair["job_number"].'</a></p>' ;
			
														if(MapUtil::get($repair, 'contractor')) {	
															$strSchedule = $strSchedule. '<p>' .UIUtil::getUserLink(MapUtil::get($repair, "contractor")) .'</p>';
														}

														$strSchedule = $strSchedule. '<i class="icon-wrench schedule-item-type" title="Repair" tooltip></i>' ;
														$strSchedule = $strSchedule. '</div>' ;
													}

													foreach($appointmentsArray as $appointment) {
														$strSchedule = $strSchedule. '<div class="schedule-item appointment" style=" border-left: 3px solid lightgray; font-size: 11px; line-height: 15px; margin: 2px; padding: 4px 6px; position: relative;">' ;
															$strSchedule = $strSchedule. '<p style="padding-bottom:0px;">'.DateUtil::formatTime($appointment["datetime"]).'</p>' ;
															$strSchedule = $strSchedule. '<p style="padding-bottom:0px;">' ;
															$strSchedule = $strSchedule. '<a rel="open-modal" href="get_appointment.html?id='.$appointment["appointment_id"].'&JId='.$appointment["job_id"].'" title="View appointment details" tooltip>'.stripslashes($appointment["title"]).'</a>' ;
															$strSchedule = $strSchedule. '</p>' ;
															$strSchedule = $strSchedule. '<p style="width: 95%;  display: inline-block;"><a href="jobtabs.html?JId='.$appointment["job_id"].'" tooltip>'.$appointment["job_number"].'</a></p>' ;
															$strSchedule = $strSchedule. '<i class="icon-time schedule-item-type" title="Appointment" tooltip></i>' ;
														$strSchedule = $strSchedule. '</div>' ;
													}
													
													foreach($eventsArray as $event) {
														$strSchedule = $strSchedule. '<div class="schedule-item event" style="border-left: 3px solid lightgray; font-size: 11px; line-height: 15px; margin: 2px; padding: 4px 6px; position: relative;">' ;
															$scheduleDay = ( $event['all_day'] > 0 ) ? 'All day' : DateUtil::formatTime($event['date']);
															$strSchedule = $strSchedule. '<p style="padding-bottom:0px;"> '. $scheduleDay.' </p>' ;
															$strSchedule = $strSchedule. '<p style="width: 95%;  display: inline-block;" class="event_teax">';
															$strSchedule = $strSchedule. '<a href="add_event.html?date='. $date .'&id='. $event["event_id"] .'" title="View event details" tooltip> '. stripslashes($event["title"]).' </a>' ;
															$strSchedule = $strSchedule. '</p>' ;

															$strSchedule = $strSchedule. '<i class="icon-calendar schedule-item-type" title="Event" tooltip></i>' ;
														$strSchedule = $strSchedule. '</div>' ;
												    }
													
													foreach($insuracenotification as $data)
													{
														$gendate = date("Y-m-d",strtotime($data["generalins"]));
														$workdate = date("Y-m-d",strtotime($data["workerins"]));
																												
														if($gendate == $date )
														{
															$firstName = $data['fname'];
															$lasstName = $data['lname'];
																														
															if($curDate == $gendate )
															{	
																$today = ($curDate == $gendate) ? "Today" : $gendate;
																
																$strSchedule = $strSchedule. '<p>' ;
																$strSchedule = $strSchedule. '<a href="get_insurance_detail.html?type=1&dt='. $today.' &id='. $data["user_id"] .'" title="Insurance Expiration Details" tooltip>' ;
																//$strSchedule = $strSchedule. '. $data['fname'] .  $data['lname'] .' s General liability insurance Expire On '. $today .';
																$strSchedule = $strSchedule.  $firstName .'  '. $lasstName .'&#39;s General liability insurance Expire On ' . $today ;
																$strSchedule = $strSchedule. '</a>' ;
																$strSchedule = $strSchedule. '</p>' ;
															}

															if($curDate == $workdate )
															{
																$today = ($curDate == $workdate) ? "Today" : $workdate;
																
																$strSchedule = $strSchedule. '<p>' ;
																$strSchedule = $strSchedule. '<a href="get_insurance_detail.html?type=2&dt='. $today .'&id= '. $data["user_id"] .'" title="Insurance Expiration Details" tooltip>' ;
																//$strSchedule = $strSchedule. '. $data["fname"] . $data["lname"] .' s Workers Compensations insurance Expire On '. $today .'  ;
																$strSchedule = $strSchedule.  $firstName .'  '. $lasstName .'&#39;s Workers Compensations insurance Expire On ' . $today ;
																$strSchedule = $strSchedule. '</a>' ;
																$strSchedule = $strSchedule. '</p>' ;
															}
														}
														if($nextdate == $date)
														{
															if($nextdate == $gendate )
															{
																$today = ($curDate == $gendate) ? "Today" : $gendate;
																$strSchedule = $strSchedule. '<p>' ;
																$strSchedule = $strSchedule. '<a href="get_insurance_detail.html?type=1&dt='. $today .'&id= '. $data["user_id"] .'" title="Insurance Expiration Details" tooltip>' ;
																//$strSchedule = $strSchedule. '. $data["fname"]   $data["lname"] . s General liability insurance Expire On . $today .' ;
																$strSchedule = $strSchedule.  $firstName .'  '. $lasstName .'&#39;s General liability insurance Expire On ' . $today ;
																$strSchedule = $strSchedule. '</a>' ;
																$strSchedule = $strSchedule. '</p>' ;
															}

															if($nextdate == $workdate )
															{
																$today = ($curDate == $workdate) ? "Today" : $workdate;
																$strSchedule = $strSchedule. '<p>' ;
																$strSchedule = $strSchedule. '<a href="get_insurance_detail.html?type=2&dt='. $today .'&id= '. $data["user_id"] .'" title="Insurance Expiration Details" tooltip>' ;
																//$strSchedule = $strSchedule. '. $data["fname"]   $data["lname"] . s Workers Compensations insurance Expire On . $today .' ;
																$strSchedule = $strSchedule.  $firstName .'  '. $lasstName .'&#39;s Workers Compensations insurance Expire On ' . $today ;
																$strSchedule = $strSchedule. '</a>' ;
																$strSchedule = $strSchedule. '</p>' ;
															}
														}	
													}

													foreach($tasksArray as $task) 
													{
														$job = new Job($task["job_id"], FALSE);
														$icons = "";
														if($job) {
															if($job->hasCredit("final")) {
																$icons = '<i class="icon-smile green" title="Job final payment received" tooltip></i>';
															} else if($job->hasCredit("1st")) {
																$icons = '<i class="icon-smile yellow" title="Job 1st payment received" tooltip></i>';
															}
														}
														
														//$icons = $icons . ' empty($task["paid"]) ? "" : "<i class="icon-usd green" title="Task paid on ' . DateUtil::formatDate($task["paid"]) . '" tooltip></i>"';
														//$icons = $icons . ' !empty($task["midroof"]) ? "<span title="' . $task["location"] . '" - " '. $task["midroof_timing"] . '" tooltip>mr</span>" : "" ';

														if($task["paid"] != '' && $task["paid"] != 'null' )
														{
															$icons = $icons . '<i class="icon-usd green" title="Task paid on ' . DateUtil::formatDate($task["paid"]) . '" tooltip></i>';
														}
														else
														{
															$icons = $icons . "";
														}

														if($task["midroof"] != '' && $task["midroof"] != 'null' )
														{
															$icons = $icons . '<span title="' . $task["location"] . '" - " '. $task["midroof_timing"] . '" tooltip>mr</span>';
														}
														else
														{
															$icons = $icons . "";
														}

														$strSchedule = $strSchedule. '<div class="schedule-item task '. UIUtil::getContrast($task["color"]) .'" style="border-left: 3px solid lightgray; font-size: 11px; line-height: 15px; margin: 2px; padding: 4px 6px; position: relative;  background-color: '. $task["color"] .' ">' ;
														
														$taskClass = '';
														if($task["completed"] != '' && $task["completed"] != 'null' )
														{
															$taskClass = "line-through";
														}
														else
														{
															$taskClass = "";
														}

														$strSchedule = $strSchedule. '<p style="padding-bottom:0px;">' ;
														$strSchedule = $strSchedule. '<a class="'. $taskClass .'"  href="get_task.html?Id='. $task["task_id"] .'&JId='. $task["job_id"] .'"  tooltip>'. stripslashes($task["task"]) .'</a>' ;

														$strSchedule = $strSchedule. '</p>' ;
														$strSchedule = $strSchedule. '<p style="padding-bottom:0px;">' ;
														$strSchedule = $strSchedule. '<a href="jobtabs.html?JId='. $task["job_id"] .'" tooltip>'. $task["job_number"] .'</a>' ;
														$strSchedule = $strSchedule. '</p>' ;

														if(MapUtil::get($task, "contractor")) {
																$strSchedule = $strSchedule. '<p>'. UIUtil::getUserLink(MapUtil::get($task, "contractor")).' </p>' ;
														}
														if(!empty($icons)) {
																$strSchedule = $strSchedule. '<div class="icons" style="background-color: white; border-bottom-left-radius: 5px; color: #ff3232; cursor: help; font-size: 0; line-height: 100%; opacity: 0.9; padding: 2px 2px 5px 5px; position: absolute; right: 0; top: 0;"> '. $icons .' </div> ' ;
														}

														$strSchedule = $strSchedule. '<i class="icon-briefcase schedule-item-type" style="color: '. $task['color'] .'" title="Task - '. stripslashes($task['task']) .'" tooltip></i>' ;
														$strSchedule = $strSchedule. '</div>' ;
													}
													
													foreach($deliveriesArray as $delivery) {
														//$icons = empty($delivery['confirmed']) ? '' : '<i class="icon-ok green" title="Delivery confirmed on ' . DateUtil::formatDate($delivery['confirmed']) . '" tooltip></i>';
														
														if($delivery['confirmed'] != '' && $delivery['confirmed'] != 'null' )
														{
															$icons = '<i class="icon-ok green" title="Delivery confirmed on ' . DateUtil::formatDate($delivery['confirmed']) . '" tooltip   style="#35aa47 !important"></i>';
														}
														else
														{
															$icons = "";
														}

														$strSchedule = $strSchedule. '<div class="schedule-item delivery" style=" border-left: 3px solid lightgray; font-size: 11px; line-height: 15px; margin: 2px; padding: 4px 6px; position: relative;">' ;
														$strSchedule = $strSchedule. '<p style="overflow: hidden; margin: 0; padding: 0; text-overflow: ellipsis; white-space: nowrap; width: 90%;"><a href="jobtabs.html?JId='. $delivery["job_id"] .'" tooltip>'. $delivery["label"] .'</a></p>' ;
															
															if(MapUtil::get($delivery, "salesman")) {
																	$strSchedule = $strSchedule. '<p style="overflow: hidden; margin: 0; padding: 0; text-overflow: ellipsis; white-space: nowrap; width: 90%;"><a href="get_user.html?UserID='. MapUtil::get($delivery, "salesman") .'" tooltip>'. MapUtil::get($delivery, 'fname') .'</a></p>' ;
															}
														$strSchedule = $strSchedule. '<div class="icons">'. $icons .'</div>' ;
														$strSchedule = $strSchedule. '<i class="icon-truck schedule-item-type" title="Delivery" tooltip></i>' ;
														$strSchedule = $strSchedule. '</div>' ;
													}
													
												$strSchedule = $strSchedule. '</td>' ;
			
											$strSchedule = $strSchedule. '</tr>' ;
										$strSchedule = $strSchedule. '</table>' ;
									$strSchedule = $strSchedule. '</td>' ;
									$current_date_in_iteration = strtotime("+1 day", $current_date_in_iteration);
							}
							$strSchedule = $strSchedule. '</tr>' ;
							$strSchedule = $strSchedule. '</table>' ;
					
					return json_encode(array('status'=> 1,'strSchedule'=> $strSchedule)); 
		}
	}
  }

 function GetSchedulerMonthView()
 {
	if(!ModuleUtil::checkAccess('view_schedule'))
	{
			return json_encode(array('status'=> 0,'message'=>"Insufficient Rights")); 
	}
	else
	{
		$m=$_POST['m'];
		$y=$_POST['y'];

		//$m = '';
		//$y = '';

		if($m=="")
			$m = date("n");
		if($y=="")
			$y = date("Y");

		$user = $_POST['userId'];
		$type = $_POST['typeId'];

		$date = strtotime($y."/".$m."/1");
		$day = date("D",$date);
		$month = date("F",$date);
		$totaldays = date("t",$date); //get the total day of specified date
				

		$next = $_GET;
		$prev = $_GET;
		//$next = '';
		//$prev = '';
		
		$prev['m'] = $m-1;
		$next['m'] = $m+1;
		$prev['y'] = $y;
		$next['y'] = $y;
		
		if($prev['m']==0)
		{
		  $prev['y']--;
		  $prev['m'] = 12;
		}
		if($next['m']==13)
		{
		  $next['y']++;
		  $next['m'] = 1;
		}
				

		$strScheduleMonth = '<table class="containertitle" width="100%">';
		$strScheduleMonth = $strScheduleMonth. '<tr>';
		$strScheduleMonth = $strScheduleMonth. '<td>';
		$strScheduleMonth = $strScheduleMonth. '<a class="btn btn-blue btn-small" href="" id="alinkPrevMonth"  data-month='. $prev['m'] .'  data-year='. $prev['y'] .'><i class="icon-angle-left"></i></a>' ;
		$strScheduleMonth = $strScheduleMonth. '</td>';
		$strScheduleMonth = $strScheduleMonth. '<td width="140" align="center">'. $month." ".$y .'</td>';
		$strScheduleMonth = $strScheduleMonth. '<td align="right">';
		$strScheduleMonth = $strScheduleMonth. '<a class="btn btn-blue btn-small" href ="" id="alinkNextMonth"  data-month='. $next['m'] .'  data-year='. $next['y'] .'><i class="icon-angle-right"></i></a>' ;
		
		$strScheduleMonth = $strScheduleMonth. '</td>';
		$strScheduleMonth = $strScheduleMonth. '</tr>';
		$strScheduleMonth = $strScheduleMonth. '</table>';

		$strScheduleMonth = $strScheduleMonth. '<table border="0" class="schedulecontainer" width="100%">';
		$strScheduleMonth = $strScheduleMonth. '<tr>';
		$strScheduleMonth = $strScheduleMonth. '<td align="center" style="font-weight: bold;">Sun</td>';
		$strScheduleMonth = $strScheduleMonth. '<td align="center" style="font-weight: bold;">Mon</td>';
		$strScheduleMonth = $strScheduleMonth. '<td align="center" style="font-weight: bold;">Tue</td>';
		$strScheduleMonth = $strScheduleMonth. '<td align="center" style="font-weight: bold;">Wed</td>';
		$strScheduleMonth = $strScheduleMonth. '<td align="center" style="font-weight: bold;">Thu</td>';
		$strScheduleMonth = $strScheduleMonth. '<td align="center" style="font-weight: bold;">Fri</td>';
		$strScheduleMonth = $strScheduleMonth. '<td align="center" style="font-weight: bold;">Sat</td>';
		$strScheduleMonth = $strScheduleMonth. '</tr>';

		

		if($day=="Sun") $st=1;
		if($day=="Mon") $st=2;
		if($day=="Tue") $st=3;
		if($day=="Wed") $st=4;
		if($day=="Thu") $st=5;
		if($day=="Fri") $st=6;
		if($day=="Sat") $st=7;
		
		if($st >= 6 && $totaldays == 31)
		  $tl=42;
		elseif($st == 7 && $totaldays == 30)
		  $tl = 42;
		else
		  $tl = 35;
		
		$ctr = 1;
		$d=1;
		
		for($i=1;$i<=$tl;$i++)
		{
			if($ctr==1)
			{
			  $strScheduleMonth = $strScheduleMonth. '<tr height=100 valign="top" style="vertical-align: top !important;">';
			}
			
			if($i >= $st && $d <= $totaldays)
			{
				$date = date("Y-m-d", mktime(0,0,0,$m,$d,$y));
				$border = "border: 1px solid #999999; background-color: #ffffff;";
				$style ="style = 'border: 1px solid #999999; background-color: #ffffff;  width: 200px !important; height: 200px !important; vertical-align: top !important;'";
				if($date == date("Y-m-d"))
				{
				  $border = "border: 2px solid #0086CC; background-color: #ffffff;";
				  $style ="style = 'border: 1px solid #0086CC; background-color: #ffffff;  width: 200px !important; height: 200px !important; vertical-align: top !important;'";
				}

				$strScheduleMonth = $strScheduleMonth. '<td class="calendar-cell month"  '. $style .'>';
				$strScheduleMonth = $strScheduleMonth. '<table border="0" cellspacing="0" cellpadding="0" width="100%">';
				$strScheduleMonth = $strScheduleMonth. '<tr>';
				
				if(ModuleUtil::checkAccess('event_readwrite'))
				{
					$strScheduleMonth = $strScheduleMonth. ' <td class="smallnote" align="left" width="100%" style="background-color: #ededed; border-bottom: 1px solid #cccccc;">';
					$strScheduleMonth = $strScheduleMonth. '<a class="boldlink"  href="add_event.html?date='. $date .'&id=0" title="Add Event" tooltip>+</a>';
					$strScheduleMonth = $strScheduleMonth. '</td>';
				}
				
				$strScheduleMonth = $strScheduleMonth. '<td class="smallnote" align="right" width="100%" style="background-color: #ededed; border-bottom: 1px solid #cccccc;">';
				$strScheduleMonth = $strScheduleMonth. ' <b>'. $d .'</b>';
				$strScheduleMonth = $strScheduleMonth. '</td>';
				$strScheduleMonth = $strScheduleMonth. '</tr>';

				//initialize empty arrays
				$repairs_array = array();
				$tasks_array = array();
				$events_array = array();
				$appointments_array = array();
				$deliveries_array = array();

				//get events
				if(empty($type))
				{
					$repairs_array = ScheduleUtil::getRepairs($date, $user);
					$tasks_array = ScheduleUtil::getTasks($date, $user);
					$events_array = ScheduleUtil::getEvents($date, $user);
					$appointments_array = ScheduleUtil::getAppointments($date, $user);
					$deliveries_array = ScheduleUtil::getDeliveries($date, $user);
				}
				else
				{
					if(strpos($type, "task_type=") !== false)
					{
						$task_type_id = str_replace("task_type=", "", $type);
						$tasks_array = ScheduleUtil::getTasks($date, $user, $task_type_id);
					}
					
					switch($type)
					{
						case "appointment":
							$appointments_array = ScheduleUtil::getAppointments($date, $user);
							break;
						case "delivery":
							$deliveries_array = ScheduleUtil::getDeliveries($date, $user);
							break;
						case "event":
							$events_array = ScheduleUtil::getEvents($date, $user);
							break;
						case "repair":
							$repairs_array = ScheduleUtil::getRepairs($date, $user);
							break;
					}
				}

				foreach($repairs_array as $repair)
				{
					if(!empty($repair["completed"]))
					{
						$strScheduleMonth = $strScheduleMonth. '<tr>';
						$strScheduleMonth = $strScheduleMonth. '<td>';
						$strScheduleMonth = $strScheduleMonth. '<s><a class="repairschedulelink" href="get_repair.html?id='. $repair["repair_id"] .'&JId='. $repair["job_id"] .'" style="color: red; font-weight: bold; font-size: 10px; text-decoration: none;">'. $repair["fail_type"] .'</a></s>';
						$strScheduleMonth = $strScheduleMonth. '</td>';
						$strScheduleMonth = $strScheduleMonth. '</tr>';
					}
					else
					{
						$strScheduleMonth = $strScheduleMonth. '<tr>';
						$strScheduleMonth = $strScheduleMonth. '<td>';
						$strScheduleMonth = $strScheduleMonth. '<a class="repairschedulelink" href="get_repair.html?id='. $repair["repair_id"] .'&JId='. $repair["job_id"] .'" style="color: red; font-weight: bold; font-size: 10px; text-decoration: none;">'. $repair["fail_type"] .'</a>';
						$strScheduleMonth = $strScheduleMonth. '</td>';
						$strScheduleMonth = $strScheduleMonth. '</tr>';
					}
				}

				foreach($appointments_array as $appointment)
				{
						$strScheduleMonth = $strScheduleMonth. '<tr>';
						$strScheduleMonth = $strScheduleMonth. '<td colspan=2 style="background-color: #ffffff"><a href="get_appointment.html?id='.$appointment["appointment_id"].'&JId='.$appointment["job_id"].'" class="schedulelink" style="color: #0086cc; font-size: 10px; font-weight: bold; text-decoration: none;">'.  $appointment["title"] .'</a></td>';
						$strScheduleMonth = $strScheduleMonth. '</tr>';
				}
				
				foreach($events_array as $event)
				{
						$strScheduleMonth = $strScheduleMonth. '<tr>';
						$strScheduleMonth = $strScheduleMonth. '<td colspan=2 style="background-color: #ffffff"><a href="add_event.html?date='. $date .'&id='. $event["event_id"] .'" class="schedulelink" style="color: #0086cc; font-size: 10px; font-weight: bold; text-decoration: none;">'. stripslashes($event["title"]) .'</a></td>';
						$strScheduleMonth = $strScheduleMonth. '</tr>';
				}
				
				foreach($tasks_array as $task)
				{
						$paid_str = '';
						if($task['paid']!='')
						{
							$paid_str = "<img src='" . ROOT_DIR . "/images/icons/dollar_10.png'>";
						}

						if($task['completed']!='')
						{
							$strScheduleMonth = $strScheduleMonth. '<tr>';
							$strScheduleMonth = $strScheduleMonth. '<td colspan=2 style="background-color: '. $task["color"] .'">'. $paid_str .'<s><a href="get_task.html?Id='. $task["task_id"] .'&JId='. $task["job_id"] .'" class="schedulelink" style="color: #0086cc; font-size: 10px; font-weight: bold; text-decoration: none;">'. $task["lname"] .'</s> <span class="smallnote" style="color: #ffffff  !important; font-size: 10px !important;">('. $task["task"] .')</span></a></td>';
							$strScheduleMonth = $strScheduleMonth. '</tr>';
						}
						else
						{
							$strScheduleMonth = $strScheduleMonth. ' <tr>';
							$strScheduleMonth = $strScheduleMonth. '<td colspan=2 style="background-color: '. $task["color"] .'">'. $paid_str .'<a href="get_task.html?Id='. $task["task_id"] .'&JId='. $task["job_id"] .'" class="schedulelink" style="color: #0086cc; font-size: 10px; font-weight: bold; text-decoration: none;">'. $task["lname"] .'<span class="smallnote" style="color: #ffffff  !important; font-size: 10px !important;">('. $task["task"] .')</span></a></td>';
							$strScheduleMonth = $strScheduleMonth. '</tr>';
						}
				}
				
				foreach($deliveries_array as $delivery)
				{
					if($delivery['confirmed']=='')
					{
						$strScheduleMonth = $strScheduleMonth. '<tr>';
						$strScheduleMonth = $strScheduleMonth. '<td colspan=2><a href="jobtabs.html?JId='. $delivery["job_id"] .'" class="schedulelink" tooltip style="color: #0086cc; font-size: 10px; font-weight: bold; text-decoration: none;">Material Delivery</a></td>';
						$strScheduleMonth = $strScheduleMonth. '</tr>';
					}
					else
					{
						$strScheduleMonth = $strScheduleMonth. '<tr>';
						$strScheduleMonth = $strScheduleMonth. '<td colspan=2><b><span style="font-family: wingdings; font-size: 10px;">&#10004;</span></b> <a href="jobtabs.html?JId='. $delivery["job_id"] .'" class="schedulelink" tooltip style="color: #0086cc; font-size: 10px; font-weight: bold; text-decoration: none;">Material Delivery</a></td>';
						$strScheduleMonth = $strScheduleMonth. '</tr>';
					}
				}
				
				$strScheduleMonth = $strScheduleMonth. '</table>';
				$strScheduleMonth = $strScheduleMonth. '</td>';
				$d++;
				
			}
			else
			{
				$strScheduleMonth = $strScheduleMonth. '<td>&nbsp</td>';
			}
			$ctr++;
				
			if($ctr > 7)
			{
				$ctr=1;
				$strScheduleMonth = $strScheduleMonth. '</tr>';
			}
		}
		$strScheduleMonth = $strScheduleMonth. ' </table>';
			
			return json_encode(array('status'=> 1,'strScheduleMonth'=> $strScheduleMonth)); 	
	}
	
 }

 // getEventTimeList By VIR
 function getEventTimeList()
 {
		$timeArray = FormUtil::getTimePicklist();
		$groups = UserModel::getAllUserGroups();
	    return json_encode(array('timeList'=> $timeArray, 'groups' => $groups)); 
}
 
// addEventForScheduleDate By VIR
function addEventForScheduleDate()
{
	$title = $_POST['title'];
	$startDate = $_POST['startDt'];
	$endDate = $_POST['endDt'];
	$startTime = $_POST['startTime'];
	$endTime = $_POST['endTime'];
	$global = $_POST['global'];
	$groups = $_POST['groupId'];
	$description = $_POST['Desc'];
	
	$allDay = 0;
	if(empty($startTime) && empty($endTime)) {
		$allDay = 1;
	}

	$startTimestamp = DateUtil::formatMySQLTimestamp("$startDate $startTime");
	$endTimestamp = DateUtil::formatMySQLTimestamp("$endDate $endTime");

	$sql = "INSERT INTO events VALUES (0, '{$_SESSION['ao_accountid']}', '{$_SESSION['ao_userid']}', '$startTimestamp', '$endTimestamp', '$title', '$description', now(), '$global', '$allDay')";
	$result = DBUtil::query($sql);
	
	if($result)
	{	
		if($global == 1) 
		{
			$eventId = DBUtil::getInsertId();

			if(empty($groups)) 
			{	
					$sql = "SELECT user_id FROM users WHERE account_id = '{$_SESSION['ao_accountid']}' AND user_id <> '{$_SESSION['ao_userid']}'
		                    AND is_active = 1  AND is_deleted = 0  ORDER BY lname ASC";
			}
			else
			{		
					$groupsStr = implode(',', $groups);
					$sql = "SELECT ugl.user_id FROM usergroups_link ugl, users u WHERE ugl.user_id = u.user_id
		                    AND ugl.usergroup_id in ($groupsStr)  AND u.is_active = 1  AND u.is_deleted = 0
							GROUP BY u.user_id";
		            
		            //add to event meta
					ScheduleModel::setEventMetaValue($eventId, 'usergroup', $groups);

		            //foreach($groups as $group) {
					//    ScheduleModel::setEventMetaValue($eventId, 'usergroup', $group);
		            //}
			}
			
			$userIds = DBUtil::pluck(DBUtil::query($sql), 'user_id');
			foreach($userIds as $userId) {
				NotifyUtil::notifyFromTemplate('new_event', $userId, $_SESSION['ao_userid'], array('event_id' => $eventId), true);
			}
		}
		return json_encode(array('status'=> 1,'message'=> "Event has been saved successfully"));
	}
	else
	{
		return json_encode(array('status'=> 0,'message'=> "There are some error, while add new Event"));
	}
}

function GetEventDetailsById()
{
	$event_id = $_POST['Id'];
	$sql = "SELECT e.*,date_format(date, '%b %d, %Y') as StartDate, date_format(date, '%h:%i %p') as startTime, date_format(end_date, '%b %d, %Y') as EndDate, date_format(end_date, '%h:%i %p') as EndTime, date_format(date, '%Y-%m-%d') as EditStartDate, date_format(end_date, '%Y-%m-%d') as EditEndDate, date_format(date, '%h:%i:%s') as EditStartTime, date_format(end_date, '%h:%i:%s') as EditEndTime, u.fname, u.lname FROM events e, users u WHERE event_id = '$event_id' AND u.user_id = e.user_id LIMIT 1";
	$res = DBUtil::queryToArray($sql);
	
	if(empty($res)) {
		return json_encode(array('status'=> 0,'message'=> "Invalid Event Data"));
	}
	else
	{
		$canEditEvent = ModuleUtil::checkAccess('event_readwrite') && ModuleUtil::canAccessObject('event_readwrite', $event);
		
		
		//get usergroup data
		$user_groups = ScheduleModel::getEventUserGroups($event_id);
		
		$user_groups_str = '';
		//build user groups string
		if(!empty($user_groups)) {
		    $labels = array();
		    foreach($user_groups as $user_group) {
			    $labels[] = $user_group['label'];
		    }
		    $user_groups_str = implode(', ', $labels);
		}
		

		return json_encode(array('status'=> 1,'event'=> $res, 'canEditEvent' => $canEditEvent, 'user_groups_str' => $user_groups_str ));
	}
}

function DeleteEventDetailByID()
{
	$event_id = $_POST['Id'];
	$sql = "delete from events where event_id='$event_id' limit 1";
    $result = DBUtil::query($sql);

	if($result)
	{
		return json_encode(array('status'=> 1,'message'=> "Event deleted successfully"));
	}
	else
	{
		return json_encode(array('status'=> 0,'message'=> "There are error, while deleting event!"));
	}
}

// UpdateEventDetailsForScheduleDate By VIR
function UpdateEventDetailsForScheduleDate()
{
	$event_id = $_POST['eventID'];
	$title = $_POST['title'];
	$startDate = $_POST['startDt'];
	$endDate = $_POST['endDt'];
	$startTime = $_POST['startTime'];
	$endTime = $_POST['endTime'];
	$global = $_POST['global'];
	$groups = $_POST['groupId'];
	$description = $_POST['Desc'];
	
	$allDay = 0;
	if(empty($startTime) && empty($endTime)) {
		$allDay = 1;
	}

	$startTimestamp = DateUtil::formatMySQLTimestamp("$startDate $startTime");
	$endTimestamp = DateUtil::formatMySQLTimestamp("$endDate $endTime");
		
	$sql = "SELECT * from events WHERE event_id = '$event_id' LIMIT 1";
	$res = DBUtil::queryToArray($sql);
	
	if(empty($res)) {
		return json_encode(array('status'=> 0,'message'=> "Event not found!"));
	}
	else
	{
			$updateSQL = "update events SET account_id = '{$_SESSION['ao_accountid']}', user_id = '{$_SESSION['ao_userid']}', date = '$startTimestamp', end_date = '$endTimestamp', title = '$title', text = '$description', timestamp = now(), global = '$global', all_day = '$allDay'  where event_id = '$event_id' ";
			
			$result = DBUtil::query($updateSQL);
			if($result)
			{
				return json_encode(array('status'=> 1,'message'=> "Event updated successfully"));
			}
			else
			{
				return json_encode(array('status'=> 0,'message'=> "There are some error, while updating event"));
			}	
	}
}

function GetUserInsuranceDetails()
{
	$userID = $_POST['userId'];

	$sql = "SELECT *, date_format(users.generalins, '%Y-%m-%d') as generalins, date_format(users.workerins, '%Y-%m-%d') as workerins from users WHERE user_id = '$userID' LIMIT 1";
	$res = DBUtil::queryToArray($sql);
	if(empty($res))
	{	
		return json_encode(array('message' => 'Users not found!','userDetails'=> $res));
	}
	else
	{
		return json_encode(array('message' => 'Users Insurance Detail Found','userDetails'=> $res));
	}
}

function sendIcalEvent()
{
	
	$sql = "select user_id, fname, lname, email from users where user_id='".$_SESSION['ao_userid']."' and account_id = '{$_SESSION['ao_accountid']}'";
	$resUser = DBUtil::queryToArray($sql);	
	
	
    $domain = 'renustechnologix.com';
	$from_name= $resUser[0]['fname'] . ' ' . $resUser[0]['lname'];
	$from_address= $resUser[0]["email"];
	$to_name= $_POST['to_name'];
	$to_address= $_POST['to_address'];
	$DTStart= $_POST['startDate'];
	$DTEnd= $_POST['endDate'];
	$startTime1= $_POST['startTime'];
	$endTime1= $_POST['endTime'];
	$subject= $_POST['subject'];
	$description= $_POST['description'];
	$location= $_POST['location'];
	$guestList= $_POST['guestList'];
	
	

	$myArray = explode(',', $guestList);

	$startTime = $DTStart . ' ' . $startTime1;        
	$endTime = $DTEnd . ' ' . $endTime1;    

	$eventTime = $startTime;
	if($endTime != null || $endTime != '')
	{
		$eventTime = $eventTime . ' = ' . $endTime;
	}

	
    //Create Email Headers
    $mime_boundary = "----Workflow----".MD5(TIME());

    $headers = "From: ".$from_name." <".$from_address.">\n";
    $headers .= "Reply-To: ".$from_name." <".$from_address.">\n";
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
    $headers .= "Content-class: urn:content-classes:calendarmessage\n";
    
    //Create Email Body (HTML)
    $message = "--$mime_boundary\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\n";
    $message .= "Content-Transfer-Encoding: 8bit\n\n";
    $message .= "<html>\n";
    $message .= "<body>\n";
    $message .= '<p>Dear '.$to_name.',</p>';
    $message .= '<p>'.$description.'</p>';
    $message .= "</body>\n";
    $message .= "</html>\n";
    $message .= "--$mime_boundary\r\n";

    $ical = 'BEGIN:VCALENDAR' . "\r\n" .
    'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
    'VERSION:2.0' . "\r\n" .
    'METHOD:REQUEST' . "\r\n" .
    'BEGIN:VTIMEZONE' . "\r\n" .
    'TZID:Eastern Time' . "\r\n" .
    'BEGIN:STANDARD' . "\r\n" .
    'DTSTART:20091101T020000' . "\r\n" .
	'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=11' . "\r\n" .
    'TZOFFSETFROM:-0400' . "\r\n" .
    'TZOFFSETTO:-0500' . "\r\n" .
    'TZNAME:EST' . "\r\n" .
    'END:STANDARD' . "\r\n" .
    'BEGIN:DAYLIGHT' . "\r\n" .
    'DTSTART:20090301T020000' . "\r\n" .
	'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=2SU;BYMONTH=3' . "\r\n" .
    'TZOFFSETFROM:-0500' . "\r\n" .
    'TZOFFSETTO:-0400' . "\r\n" .
    'TZNAME:EDST' . "\r\n" .
    'END:DAYLIGHT' . "\r\n" .
    'END:VTIMEZONE' . "\r\n" .	
    'BEGIN:VEVENT' . "\r\n" .
    'ORGANIZER;CN="'.$from_name.'":MAILTO:'.$from_address. "\r\n" .
	'ATTENDEE;CN="'.$to_name.'";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:'.$to_address. "\r\n" .
    'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
    'UID:'.date("Ymd\TGis", strtotime($startTime)).rand()."@".$domain."\r\n" .
    'DTSTAMP:'.date("Ymd\TGis"). "\r\n" .
    'DTSTART;TZID="Eastern Time":'.date("Ymd\THis", strtotime($startTime)). "\r\n" .
	'DTEND;TZID="Eastern Time":'.date("Ymd\THis", strtotime($endTime)). "\r\n" .
	
	
    'TRANSP:OPAQUE'. "\r\n" .
    'SEQUENCE:1'. "\r\n" .
    'SUMMARY:' . $subject . "\r\n" .
    'When:' . $eventTime . "\r\n" .
	'Who:' . $to_name . "\r\n" .
	'Organizer:' . $from_name . "\r\n" .
	'Description:' . $description . "\r\n" .
	'Attendees:' . $to_address . "\r\n" .
    'CLASS:PUBLIC'. "\r\n" .
    'PRIORITY:5'. "\r\n" .
    'BEGIN:VALARM' . "\r\n" .
    'TRIGGER:-PT15M' . "\r\n" .
    'ACTION:DISPLAY' . "\r\n" .
    'DESCRIPTION:Reminder' . "\r\n" .
    'END:VALARM' . "\r\n" .
    'END:VEVENT'. "\r\n" .
    'END:VCALENDAR'. "\r\n";
    $message .= 'Content-Type: text/calendar;name="workflow_Invitation.ics";method=REQUEST'."\n";
    $message .= "Content-Transfer-Encoding: 8bit\n\n";
    $message .= $ical;

	
	$mailsent = mail($to_address, $subject, $message, $headers);
	
    //return ($mailsent)?(true):(false);
	if($mailsent)
	{
		return json_encode(array('status' => 1, 'message' => 'Invitation Send Successfully'));
	}
	else
	{
		return json_encode(array('status' => 0, 'message' => 'Mail sending fail'));
	}
}


function getSwitchUserList()
{
		$userID = $_SESSION['ao_userid'];
		$accountId = $_SESSION['ao_accountid'];
		$sql = "SELECT * FROM users u  LEFT JOIN accounts a ON a.account_id = u.account_id  LEFT JOIN levels l ON l.level_id = u.level  WHERE a.account_id = '$accountId'";
		//$sql = "SELECT * FROM users u  LEFT JOIN accounts a ON a.account_id = u.account_id  LEFT JOIN levels l ON l.level_id = u.level";
		$userArray = DBUtil::queryToArray($sql);
		
		return json_encode(array('userlist' => $userArray, 'currentUser' => $userID));
}

function SwitchUser()
{
	 
		$selVal = $_POST['selVal'];
		$userSql = "select users.user_id, users.username, users.password, accounts.account_name from users, accounts 
					   where users.user_id = '$selVal' AND  accounts.account_id = users.account_id LIMIT 1";
		$userArray = DBUtil::queryToArray($userSql);

		$account = "";
		$username = "";
		$password = "";

		foreach($userArray as $userVal)
		{
			$account= $userVal['account_name'];
			$username= $userVal['username'];
			$password= $userVal['password'];
        }
            //Related Methods: AuthModel::attemptToLogin($username, $password, $account);
            $sql= "SELECT users.user_id AS ao_userid, users.username AS ao_username, users.fname AS ao_fname, users.lname AS ao_lname, users.dba AS ao_dba,
                DATE_FORMAT(access.timestamp, '%c/%e %k:%i') AS ao_lastvisit, users.level AS ao_level, users.is_active, users.is_deleted,
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
                        AND accounts.account_name ='$account'
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

                 $_SESSION['ao_accountid']=$accountid;
                 $_SESSION['ao_userid']=$userid;
                 $_SESSION['ao_level']=$levelid;
				 $_SESSION['ao_founder']=$founderId;
				 $_SESSION['ao_logo']=$sysLogo;
				 $_SESSION['ao_accountname']=$accountname;
                 $_SESSION['ao_username'] = $username; 

                $sql = "SELECT a.is_active as a_isactive,u.is_active as u_isactive,u.is_deleted as u_isdeleted FROM accounts
                        a inner join users u on u.account_id=a.account_id where a.account_id='{$accountid}'  && u.user_id='{$userid}' ";
                
                $Users = DBUtil::fetchAssociativeArray(DBUtil::query($sql)); 
                        
               
                 
                //if($Users["a_isactive"]==1 && $Users["u_isactive"]==1 && $Users["u_isdeleted"]==0)
                //{
                 
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
                    return json_encode(array("User" =>$results,"modules" =>$modules,"navigations" =>$navigations,'status'=> 1));
            
                //}
                //else
                //{
                //   return json_encode(array("message" => "Account Not Active",'status'=> 0));
                //}
           }
           
}


function UpdateJobToNextStage()
{
	 	$jobId = $_POST['JobId'];
		
		$myJob = new Job($jobId);
		$new_stage = '';
		$job_stages = DBUtil::queryToArray("Select stage_num from job_stages where job_id = '$jobId' order by job_stage_num asc");
		
		if(!empty($job_stages))
		{
			foreach($job_stages as $key => $job_stage){
				if($myJob->stage_num == $job_stage['stage_num']){
					$new_stage = $job_stages[$key + 1]['stage_num'];
				}
			}
		}

		$sql = "update jobs set stage_num='$new_stage', stage_date=curdate() where job_id='$jobId' limit 1";
		
		$result = DBUtil::query($sql);
		
		if($result)
		{
			JobModel::saveEvent($jobId, "Moved to stage $new_stage");
			//NotifyUtil::notifySubscribersFromTemplate('stage_moved', null, array('job_id' => $jobId), true);
			return json_encode(array('status'=> 1,'message'=> "Moved to stage next stage!"));
		}
		else
		{
			return json_encode(array('status'=> 0,'message'=> "There are error, moving to next stage!"));
		}
		
}


//GetUploadFilesForUploadFromServer By VIR
 function GetUploadFilesForUploadFromServer()
 {
	if(isset($_POST['JobID']))
	{
		$jobId = $_POST['JobID'];
		$myJob = new Job($jobId);
		$jobnumber= $myJob->job_number;
	 	$dir =  'ServerDirectory/'.$jobnumber.'/';
	 
		$images = array();

		// Open a directory, and read its contents
		if (is_dir($dir)){
		  if ($dh = opendir($dir)){
	  
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
	 
		return json_encode(array('uploads_array' => $images,'JobNo'=>$jobnumber));
	}
	else{
		return json_encode(array('status'=>0));
	}
 }


//uploadFilesFromServer By VIR
   function uploadFilesFromServer()
  {
	$jobId = $_POST['JobID'];
	echo '123'.$jobId;
	 
	
 	$myJob = new Job($jobId);
	$jobnumber= $myJob->job_number;
	$pdfdata=$_POST['pdfdata'];
	 
	$selectedVals = explode(',',$pdfdata);	
	
	$flag=0;
	 
	for ($x = 0; $x < sizeof($selectedVals); $x++) 
	{
	 
		$file =  'ServerDirectory/'.$jobnumber.'/'. $selectedVals[$x];
		 
		$fileName=$selectedVals[$x];
        $ext = explode('.', $fileName);
        $ext = end($ext);
		$newFilename = md5(mt_rand() . time()) . ".".$ext;
		$newfile = UPLOADS_PATH .'/'. $newFilename;
		$fileSql = mysqli_query("select title from uploads where title= '$fileName' and job_id='$jobId'");
		if(mysqli_num_rows($fileSql))
        {
        }
		else{
			if (!copy($file, $newfile)) {
				//echo "failed to copy $file...\n";
			}else{
				//echo "copied $file into $newfile\n";
			}

			$itemId=$_POST['jobid'];

			//set query
			$sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
				VALUES ('$jobId', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$newFilename', '$fileName', now())";
			$result = DBUtil::query($sql);
		
			if($result)
			{
				$insertId = DBUtil::getInsertId();
			}
			$flag++;
		}
	}
	  return json_encode(array('status'=> 1,'message'=>"File uploaded successfully!"));
  }
  
?>
