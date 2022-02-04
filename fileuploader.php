<?php
 include 'includes/common_lib.php';

  $flag=$_POST['flag'];

   

  // document module file upload

   if($flag=='1'){

      if(ModuleUtil::checkAccess('upload_document')){

        $pieces = explode('.',$_FILES["file"]["name"]);

   

       

        $title = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['title']);

        $desc = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['description']);

        $stage = intval($_POST['stage']);

        $group = intval($_POST['document_group']);

        $file_ext = strtoupper($pieces[sizeof($pieces)-1]);

        switch($file_ext)

        {

          case 'ZIP':

          case 'RAR':

            $type = "archive";

            break;

          case 'JPG':

          case 'PNG':

          case 'GIF':

          case 'BMP':

            $type = "image";

            break;

          case 'PDF':

            $type = "pdf";

            break;

          case "PPTX":

          case "PPT":

            $type = "powerpoint";

            break;

          case "DOCX":

          case "DOC":

            $type = "word";

            break;

          case "XLSX":

          case "XLS":

            $type = "excel";

            break;

          default:

            $type = "unknown";

        }



        $new_filename = mt_rand().mktime().".".$pieces[sizeof($pieces)-1];

     

        $new_path =   'docs/' . $new_filename;

   

         //echo $_FILES["file"]["tmp_name"];



          if(move_uploaded_file($_FILES["file"]["tmp_name"], $new_path))

          {

            $sql = "insert into documents values(0, '".$_SESSION['ao_accountid']."', '".$title."', '".$desc."', '".$new_filename."', '".$type."', '".$_SESSION['ao_userid']."', '".$stage."',  now())";

            DBUtil::query($sql);



            if(!empty($group))

            {

                $last_insert_id = DBUtil::getInsertId();

                $sql = "insert into document_group_link (document_id, document_group_id)

                        values($last_insert_id, $group)";

                DBUtil::query($sql);

            }
            if (compressImage($new_path,$new_path,5)) {
              //echo "File uploaded successfully"; 
              echo "Document saved successfully"; 
            }

          }

          else

          {

            echo "Upload failed"; 

          }

     

        }

     }

    if($flag=='2')

    {

      $fileName=$_POST['filename'];

      $itemId=$_POST['jobid'];

      

      //get extension

      $ext = explode('.', $fileName);

      $ext = end($ext);



      //new filename

      $newFilename = md5(mt_rand() . time()) . ".$ext";

      $path =  'uploads/'.$newFilename;

     

      //set query

      $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)

                  VALUES ('$itemId', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$newFilename', '$fileName', now())";

          

      if(move_uploaded_file($_FILES["file"]["tmp_name"], $path)) {

        if(DBUtil::query($sql)) {

            $insertId = DBUtil::getInsertId();

            setUploadMeta($insertId, 'job_file', 'upload_mechanism', 'dropzone');
            if (compressImage($path,$path,5)) {
              //echo "File uploaded successfully"; 
              //echo "Document saved successfully"; 
              echo "File uploaded successfully"; 
            }

        }

        

      }

      else {

        echo "Upload failed"; 

      }

        

    }

    if($flag=='3'){

       $curDate = DateUtil::formatMySQLDate();

      $date_regex = '/^\d{4}\-\d{2}\-\d{2}$/';

      $pieces = $_FILES["file"]["type"];

      $pdffileinfo = $insuranceform['iform'];

      //echo '<pre>'; print_r($pdffileinfo); 

      $fname = $_FILES["file"]["name"];  

      $ftype =  $_FILES["file"]["type"];

      $fsize =$_FILES["file"]["size"];    

      $ferror =$_FILES["file"]["error"];  

      $ftmp = $_FILES["file"]["tmp_name"];    

      $user_id = $_POST['userid'];

      $new_path =  'insuranceform/'.$fname ;

      $ext = explode('.', $fname);

      $ext = "." .end($ext);

       

      if($fname == '' || $fsize <=0 || $ext != '.pdf')

      {

        echo "Please Upload PDF File";

       

      }

      else if($fsize > 10000000)

      {

       echo "File Size limit should not exceed 10MB.";

      }

      else if(!count($errors))

      {

        if(move_uploaded_file($ftmp, $new_path))

        {

          $sql = "insert into insurancepdfupload(pdfname, user_id, datecreated) values('$fname', $user_id, '$curDate')";

          DBUtil::query($sql);

          echo "File uploaded successfully"; 

        }

      }

    }

    if($flag=='4')

    {

      $fileName=$_POST['filename'];

       

      //create new file destination

	    $new_file_name = md5(time()) . '-' . $fileName;

	    $new_file_destination = LOGOS_PATH . '/' . $new_file_name;

      $path =  'logos/'. $new_file_name;         

       

      //save

	    if(move_uploaded_file($_FILES["file"]["tmp_name"], $path)) {

	   

	    	//update database

	    	$sql = "update accounts set logo = '$new_file_name' where account_id = '{$_SESSION['ao_accountid']}' limit 1";

	    	$result = DBUtil::query($sql);

        if($result)

        {

	    	  //unlink old logo

	    	  @unlink(LOGOS_PATH . '/' . $_SESSION['ao_logo']);



	    	  //set logo session variable

	    	  $_SESSION['ao_logo'] = $new_file_name;

          

          echo "Logo successfully modified";

        }

        else

	      {

	    	  echo "Error while modifying logo";

        }

	    }

	    else

	    {

	    	echo "Error while modifying logo";

      }

    }

    if($flag=='5')

    {

      $filepath=$_POST['filepath'];

      $fileName=$_POST['filename'];

      $itemId=$_POST['jobid'];

     

      //get extension

      $ext = explode('.', $fileName);

      $ext = end($ext);

      $ext=".jpg";

      //new filename

      $newFilename = md5(mt_rand() . time()) . ".jpg";

      $path =  'uploads/'.$newFilename;

     

      //set query

      $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)

                  VALUES ('$itemId', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$newFilename', '$fileName', now())";

          

      

      if(copy($filepath ,$path)){

        if(DBUtil::query($sql)) {

            $insertId = DBUtil::getInsertId();

            setUploadMeta($insertId, 'job_file', 'upload_mechanism', 'dropzone');

            //echo "File uploaded successfully"; 
            if (compressImage($path,$path,5)) {
                echo "File uploaded successfully"; 
            }

        }

      }

      else

      {

        echo "Error uploading file";

      }

    }

     if($flag=='6')

    {

      $filetype=$_POST['filetype'];

      $fileName=$_POST['filename'];

      $ext='';

      

      if($filetype == 'jpeg'){

        //$fileName=$_POST['filename'].".jpg";

        $fileName=$_POST['filename'];

        $ext='jpg';

      }

      if($filetype == 'pdf'){

        $fileName=$_POST['filename'];

        $ext = explode('.', $fileName);

        $ext = end($ext);

      }

      $itemId=$_POST['jobid'];

       

      //new filename

      $newFilename = md5(mt_rand() . time()) . ".".$ext;

      $path =  './uploads/'.$newFilename;

     

      //set query

      $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)

                  VALUES ('$itemId', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$newFilename', '$fileName', now())";

          

      if(move_uploaded_file($_FILES["file"]["tmp_name"], $path)) {

        if(DBUtil::query($sql)) {

            $insertId = DBUtil::getInsertId();

            setUploadMeta($insertId, 'job_file', 'upload_mechanism', 'dropzone');
           
            if (compressImage($path,$path,10)) {
              echo "File uploaded successfully"; 
            }

        }

        

      }

      else {

        echo "Upload failed"; 

      }

    }

    

    if($flag=='7')

    {

    

        //$pieces = explode('.',$_FILES["file"]["name"]);

        $filetype=$_POST['filetype'];

        $fileName=$_POST['filename'];

        $ext='';

        $accountID = intval($_POST['AccountID']);

        $title = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['Title']);

        $pcontact = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['PContact']);

        $firstname = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['FirstName']);

        $lastname = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['LastName']);

        $username = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['UserName']);

        $password = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['Password']);

        $email = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['Email']);

        $phone = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['Phone']);

        $address = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['Address']);

        $city = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['City']);

        $state = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['State']);

        $zip = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['Zip']);

        $jobUnit = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['JobUnit']);

        $licenseLimit = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['licenseLimit']);

        $account_hash = md5($title . microtime());

          

        //  $ext='.jpg';

     

        //  $new_filename = md5(mt_rand() . time()) . ".".$ext;

	      //$new_filename = mt_rand().mktime().".".$pieces[sizeof($pieces)-1];

	     

        $new_path =  'logos/'. $fileName;         

       

       

       

        if(move_uploaded_file($_FILES["file"]["tmp_name"], $new_path))

        {

          if($accountID > 0)

          {

              $getSql = "select logo from accounts where account_id= '$accountID' LIMIT 1";

              $sqlRes = DBUtil::query($getSql);

          

              if(mysqli_num_rows($sqlRes) != 0) {

                  list($filename) = mysqli_fetch_row($sqlRes);

                  unlink(LOGOS_PATH . '/' . $filename);

              }

              

              $sql = "update accounts set account_name='$title', primary_contact='$pcontact', email='$email', phone='$phone', address='$address', city='$city', state='$state', zip='$zip', job_unit='$jobUnit', license_limit = '$licenseLimit', logo = '$fileName' where account_id= '$accountID'"; 

                      $result = DBUtil::query($sql);

                      if($result == true)

                      {

                            echo "Account details updated successfully"; 

                      }

                      else

                      {

                            echo "There are some error, can't save Account"; 

                      }

          }

          else

          {

              $sql = "INSERT INTO accounts (account_name, primary_contact, email, phone, address, city, state, zip, job_unit, license_limit, logo, reg_date, hash) VALUES ('$title', '$pcontact', '$email', '$phone', '$address', '$city', '$state', '$zip', '$jobUnit', '$licenseLimit', '$fileName', CURDATE(), '$account_hash')";

                      $result = DBUtil::query($sql);

                      if($result == true)

                      {

                            $newAcctID = DBUtil::getInsertId();

					                  //$password = UserUtil::generatePassword();

					                  $userSql = "INSERT INTO users (username, fname, lname, password, dba, email, phone, sms_carrier, level, reg_date, account_id, founder, notes, office_id)  VALUES ('$username', '$firstname', '$lastname', '$password', '', '$email', '$phone', '0', '1', now(), '$newAcctID', '1', '', '0')";

				

					                  $UserResult = DBUtil::query($userSql);

					                  if($UserResult == true)

					                  {		

					                  	  $newUserID = DBUtil::getInsertId();

					                  	  $sql = "INSERT INTO settings VALUES (0, '$newUserID', 15, 5, 180, 400, 1, 1, 1, 1, 1, 1, 1)";

					                  	  DBUtil::query($sql);

                                

                                $navSql = "select navigation_id from navigation";

                                $navArray= DBUtil::queryToArray($navSql);

                                foreach($navArray as $navValue){

			                              $navId= $navValue['navigation_id'];

                                    $insertSql = "INSERT INTO nav_access (navaccess_id, navigation_id, account_id, level) VALUES (NULL, '$navId', '$newAcctID', '1');";

                                    DBUtil::query($insertSql);

                                }

                                

					                      UserModel::logAccess($newUserID);

					                  	  NotifyUtil::emailFromTemplate('new_user', $newUserID);

					                  }

                          

                            echo "Account details inserted successfully"; 

                      }

                      else

                      {

                            echo "There are some error, can't add Account"; 

                      }

          }

        }

        else

        {

          echo "Upload failed"; 

        }

    }

    

    if($flag=='8')

    {

         $fileName=$_POST['filename'];

         $accountId = $_POST['accountId'];

      //create new file destination

	    $new_file_name = md5(time()) . '-' . $fileName;

	    $new_file_destination = LOGOS_PATH . '/' . $new_file_name;

      $path =  'logos/'. $new_file_name;         

       

      //save

	    if(move_uploaded_file($_FILES["file"]["tmp_name"], $path)) {

	   

	    	//update database

	    	$sql = "update accounts set logo = '$new_file_name' where account_id = '$accountId' limit 1";

	    	$result = DBUtil::query($sql);

        if($result)

        {

	    	  

          

          echo "Logo successfully modified";

        }

        else

	      {

	    	  echo "Error while modifying logo";

        }

	    }

	    else

	    {

	    	echo "Error while modifying logo";

      }

    }

function compressImage($source_url, $destination_url, $quality) {
    $info = getimagesize($source_url);
    $extensions=array('image/jpg','image/jpe','image/jpeg','image/jfif','image/png','image/bmp','image/dib','image/gif');
    if(in_array($info['mime'], $extensions)){
        //save file
        if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg') $image = imagecreatefromjpeg($source_url);
        elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
        elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);
        imagejpeg($image, $destination_url, $quality);
        //return destination file
    }
  return true;
}

?>