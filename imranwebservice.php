<?php
    
    include 'includes/common_lib.php';
	include 'includes/PHPMailer-master/PHPMailerAutoload.php'; 
    $response = '';
    $account = $_POST['MethodName'];
   
    $response = $account();
    echo $response;

    
    
    require_once("dbcontroller.php");
    $db_handle = new DBController();
    
    function connect()
    {
     $link = DBUtil::connect('workflow365test');
    }
    //Get customer list imran
    function GetCustomerList()
    {       
          connect();
          global $UId;
          
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
    
    function ForgotPassword()
    {
       if(isset($_POST['account']) && isset($_POST['email'])) 
        {
            
            $account = $_POST['account'];
            $email = $_POST['email'];
            $results = AuthModel::processForgotPassword($email, $account);
           
           if($results) 
           {
	            $viewData = DBUtil::fetchAssociativeArray($results);
               
	            //build body
	            $viewData['account_url'] = ACCOUNT_URL;
	            $body = ViewUtil::loadView('mail/password-recovery', $viewData);

              //get new mail object
	            $mail = new PHPMailer;

	            //add from, to, subject, body
	            $mail->setFrom(ALERTS_EMAIL, APP_NAME . " Alerts");
	            $mail->addAddress($email, "$fname $lname");
	            $mail->Subject = APP_NAME . ': Password Recovery';
	            $body = str_replace('http://workflow365.co', 'http://workflow365.co/HtmlPages/circles/index.html', $body);
              $mail->msgHTML($body);
              
              
              if($mail->send()) 
              {		            
                 return json_encode(array('message' => 'Password has been sent to your email.', 'status' => 1));
	            }
              else 
              {		            
                 return json_encode(array('message' => 'Email failed to send.', 'status' => 0));
	            }
            } 
            else 
            {
	            LogUtil::getInstance()->logNotice("Failed password recovery - Invalid credentials: $account, $email");
	           
              return json_encode(array('message' => 'Failed password recovery - Invalid credentials.', 'status' => 0));
            }

             
         }
            
    }
    
    
    function BindCall1(){
     global $UId;
    return json_encode(array("status" =>$UId->UserName, "message" => $UId->Password,"Try" => $UId->Try));
    }
    
?>