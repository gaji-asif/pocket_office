<?php

function addNewAccount($data_array)
{
	//generate new hash
	$account_hash = md5($data_array['account-name'] . microtime());

	//build and execute account query
	$sql = "INSERT INTO accounts (account_name, primary_contact, email, phone, address, city, state, zip, reg_date, job_unit, hash, license_limit)
			VALUES ('{$data_array['account-name']}', '{$data_array['primary-contact']}', '{$data_array['email']}', '{$data_array['phone']}',
					'{$data_array['address']}', '{$data_array['city']}', '{$data_array['state']}', '{$data_array['zip']}', CURDATE(),
					'Squares', '$account_hash', '{$data_array['license-limit']}')";
	$insert_results = DBUtil::query($sql);

	//if failed, return false
	if($insert_results === false)
	{
		return false;
	}

	//get insert id
	$new_account_id = DBUtil::getInsertId();

	//copy email templates and sms templates
	if(!copyEmailTemplatesToNewAccount($new_account_id))
	{
		destroyAccountData($new_account_id);
		return false;
	}
	if(!copySMSTemplatesToNewAccount($new_account_id))
	{
		destroyAccountData($new_account_id);
		return false;
	}

	//copy insurance providers
	if(!copyInsuranceProvidersToNewAccount($new_account_id))
	{
		destroyAccountData($new_account_id);
		return false;
	}

	//copy job types
	if(!copyJobTypesToNewAccount($new_account_id))
	{
		destroyAccountData($new_account_id);
		return false;
	}

	//copy origins
	if(!copyOriginsToNewAccount($new_account_id))
	{
		destroyAccountData($new_account_id);
		return false;
	}

	//add primary user
	if(!addNewPrimaryUser($new_account_id, $data_array))
	{
		destroyAccountData($new_account_id);
		return false;
	}

	//copy stages and stage requirements
	if(!copyStagesAndStageRequirementsToNewAccount($new_account_id))
	{
		destroyAccountData($new_account_id);
		return false;
	}

	//add module access
	if(!addModuleAccessToNewAccount($new_account_id))
	{
		destroyAccountData($new_account_id);
		return false;
	}

	//add navigation access
	if(!addNavigationAccessToNewAccount($new_account_id))
	{
		destroyAccountData($new_account_id);
		return false;
	}

	//send notifiaction email
	if(!sendNewAccountNotification($data_array))
	{
		destroyAccountData($new_account_id);
		return false;
	}

	return true;
}

function copyEmailTemplatesToNewAccount($account_id)
{
	//get all default email templates
	$sql = "SELECT * from email_templates_default";
	$results = DBUtil::query($sql);

	//if failed, return false
	if($results === false)
	{
		return false;
	}

	//iterate
	while($email_template = mysqli_fetch_array($results))
	{
		//add slashes
		$email_template['text'] = addslashes($email_template['text']);

		//build and execute query
		$sql = "INSERT INTO email_templates (account_id, hook, subject, text, is_active)
				VALUES ('$account_id', '{$email_template['hook']}', '{$email_template['subject']}', '{$email_template['text']}', '{$email_template['is_active']}')";
		$insert_results = DBUtil::query($sql);

		//if failed, return false
		if($insert_results === false)
		{
			return false;
		}
	}

	return true;
}

function copySMSTemplatesToNewAccount($account_id)
{
	//get all default sms templates
	$sql = "SELECT * from sms_templates_default";
	$results = DBUtil::query($sql);

	//if failed, return false
	if($results === false)
	{
		return false;
	}

	//iterate
	while($sms_template = mysqli_fetch_array($results))
	{
		//build and execute query
		$sql = "INSERT INTO sms_templates (account_id, hook, subject, text, is_active)
				VALUES ('$account_id', '{$sms_template['hook']}', '{$sms_template['subject']}', '{$sms_template['text']}', '{$sms_template['is_active']}')";
		$insert_results = DBUtil::query($sql);

		//if failed, return false
		if($insert_results === false)
		{
			return false;
		}
	}

	return true;
}

function copyInsuranceProvidersToNewAccount($account_id)
{
	//get all default insurance providers
	$sql = "SELECT * from insurance_default";
	$results = DBUtil::query($sql);

	//if failed, return false
	if($results === false)
	{
		return false;
	}

	//iterate
	while($insurance = mysqli_fetch_array($results))
	{
		//build and execute query
		$sql = "INSERT INTO insurance (account_id, insurance)
				VALUES ('$account_id', '{$insurance['insurance']}')";
		$insert_results = DBUtil::query($sql);

		//if failed, return false
		if($insert_results === false)
		{
			return false;
		}
	}

	return true;
}

function copyJobTypesToNewAccount($account_id)
{
	//get all default job types
	$sql = "SELECT * from job_type_default";
	$results = DBUtil::query($sql);

	//if failed, return false
	if($results === false)
	{
		return false;
	}

	//iterate
	while($job_type = mysqli_fetch_array($results))
	{
		//build and execute query
		$sql = "INSERT INTO job_type (account_id, job_type)
				VALUES ('$account_id', '{$job_type['job_type']}')";
		$insert_results = DBUtil::query($sql);

		//if failed, return false
		if($insert_results === false)
		{
			return false;
		}
	}

	return true;
}

function copyOriginsToNewAccount($account_id)
{
	//get all default origins
	$sql = "SELECT * from origins_default";
	$results = DBUtil::query($sql);

	//if failed, return false
	if($results === false)
	{
		return false;
	}

	//iterate
	while($origin = mysqli_fetch_array($results))
	{
		//build and execute query
		$sql = "INSERT INTO origins (account_id, origin)
				VALUES ('$account_id', '{$origin['origin']}')";
		$insert_results = DBUtil::query($sql);

		//if failed, return false
		if($insert_results === false)
		{
			return false;
		}
	}

	return true;
}

function addNewPrimaryUser($account_id, &$data_array)
{
	//generate new password
	$password = UserUtil::generatePassword();
	$data_array['password'] = $password;

	//get first and last name
	$names = explode(' ', $data_array['primary-contact']);

	//build and execute query for users table
    $sql = "INSERT INTO users (username, fname, lname, password, dba, email, phone, level, reg_date, account_id, founder)
			VALUES ('{$data_array['primary-username']}', '{$names[0]}', '{$names[1]}', '$password', '{$data_array['account-name']}', '{$data_array['email']}', '{$data_array['phone']}',
					1, CURDATE(), '$account_id', 1)";
    $insert_results = DBUtil::query($sql);

	//if failed, return false
	if($insert_results === false)
	{
		return false;
	}

	//get new User id
    $user_id = DBUtil::getInsertId();

    //build and execute query for settings table
    $sql = "INSERT INTO settings (user_id) VALUES ('$user_id')";
    $insert_results = DBUtil::query($sql);

	//if failed, return false
	if($insert_results === false) {
		return false;
	}

	//log access
    UserModel::logAccess($user_id);

	return true;
}

function copyStagesAndStageRequirementsToNewAccount($account_id)
{
	//get all default job types
	$sql = "SELECT * from stages_default";
	$results = DBUtil::query($sql);

	//if failed, return false
	if($results === false)
	{
		return false;
	}

	//iterate
	while($stage = mysqli_fetch_array($results))
	{
		//get requirements for stage
		$sql = "SELECT * FROM stage_reqs_link_default
				WHERE stage_id = '{$stage['stage_id']}'";
		$stage_reqs_results = DBUtil::query($sql);

		//if failed, return false
		if($stage_reqs_results === false)
		{
			return false;
		}

		//add stage
		$sql = "INSERT INTO stages (stage_num, account_id, stage, description, duration)
				VALUES ('{$stage['stage_num']}', '$account_id', '{$stage['stage']}', '{$stage['description']}', '{$stage['duration']}')";
		$insert_results = DBUtil::query($sql);

		//if failed, return false
		if($insert_results === false)
		{
			return false;
		}

		//get insert id
		$new_stage_id = DBUtil::getInsertId();

		//insert stage requirements
		while($stage_requirement = mysqli_fetch_array($stage_reqs_results))
		{
			//build and execute query
			$sql = "INSERT INTO stage_reqs_link (stage_id, account_id, stage_req_id)
					VALUES ('$new_stage_id', '$account_id', '{$stage_requirement['stage_req_id']}')";
			$insert_results = DBUtil::query($sql);

			//if failed, return false
			if($insert_results === false)
			{
				return false;
			}
		}

		//add stage access
		$sql = "INSERT INTO stage_access (stage_id, level_id, account_id)
				VALUES ('$new_stage_id', 1, '$account_id')";
		$insert_results = DBUtil::query($sql);

		//if failed, return false
		if($insert_results === false)
		{
			return false;
		}
	}
	return true;
}

function addModuleAccessToNewAccount($account_id)
{
	//get all modules
	$sql = "SELECT * FROM modules";
	$results = DBUtil::query($sql);

	//if failed, return false
	if($results === false)
	{
		return false;
	}

	//iterate
	while($module = mysqli_fetch_array($results))
	{
		//build and execute query
		$sql = "INSERT INTO module_access (module_id, account_id, level)
				VALUES ('{$module['module_id']}', '$account_id', 1)";
		$insert_results = DBUtil::query($sql);

		//if failed, return false
		if($insert_results === false)
		{
			return false;
		}
	}
	return true;
}

function addNavigationAccessToNewAccount($account_id)
{
	//get all navigation
	$sql = "SELECT * FROM navigation";
	$results = DBUtil::query($sql);

	//if failed, return false
	if($results === false)
	{
		return false;
	}

	//iterate
	while($navigation = mysqli_fetch_array($results))
	{
		//build and execute query
		$sql = "INSERT INTO nav_access (navigation_id, account_id, level)
				VALUES ('{$navigation['navigation_id']}', '$account_id', 1)";
		$insert_results = DBUtil::query($sql);

		//if failed, return false
		if($insert_results === false)
		{
			return false;
		}
	}
	return true;
}

function destroyAccountData($account_id)
{
	//tables to delete account info from
	$tables_array = array(
		'accounts',
		'email_templates',
		'sms_templates',
		'insurance',
		'job_type',
		'origins',
		'stage_reqs_link',
		'stages',
		'users',
		'module_access',
		'nav_access'
	);

	//iterate
	foreach($tables_array as $table)
	{
		//build and execute
		$sql = "DELETE FROM $table WHERE account_id = '$account_id'";
		DBUtil::query($sql);
	}
}

function sendNewAccountNotification($data_array)
{
	//get mail body from template
	$view_data = array(
		'name' => $data_array['account-name'],
		'username' => $data_array['primary-username'],
		'password' => $data_array['password'],
		'email' => $data_array['email']
	);
	$body = ViewUtil::loadView('mail/new-account', $view_data);

	//get new mail object
	$mail = new PHPMailer();

	//add from, to, subject, body
	$mail->SetFrom(ALERTS_EMAIL, APP_NAME);
	$mail->AddAddress($data_array['email'], $view_data['primary-contact']);
	$mail->AddBCC("cbm3384@gmail.com");
	$mail->Subject = APP_NAME . " - New Account";
	$mail->MsgHTML($body);

	//send
	if(!$mail->Send())
	{
		return false;
	}
	return true;
}

function switchToUser($user_id)
{
	//get database name and resource link
	
	$database_name = $_SESSION['database_name'];
	$database_link = $_SESSION['database_link'];

	$user_id = mysqli_real_escape_string(DBUtil::Dbcont(),$user_id);
	$sql = "SELECT users.user_id AS ao_userid, users.username AS ao_username, users.fname AS ao_fname, users.lname AS ao_lname, users.dba AS ao_dba,
            DATE_FORMAT(access.timestamp, '%c/%e %k:%i') AS ao_lastvisit, users.level AS ao_level, users.is_active, users.is_deleted,
            accounts.account_name AS ao_accountname, users.account_id AS ao_accountid, users.founder AS ao_founder, settings.num_results AS ao_numresults,
            settings.browsing_results AS ao_browsingresults, settings.refresh AS ao_refresh, settings.widget_today AS ao_widget_today,
            settings.widget_announcements AS ao_widget_announcements, settings.widget_documents AS ao_widget_documents,
            settings.widget_bookmarks AS ao_widget_bookmarks, settings.widget_urgent AS ao_widget_urgent, settings.widget_inbox AS ao_widget_inbox,
            settings.widget_journals AS ao_widget_journals, accounts.logo AS ao_logo, accounts.job_unit AS ao_jobunit, accounts.is_active AS account_is_active,
            users.office_id AS ao_officeid
            FROM users, accounts, access, settings
            WHERE users.user_id = '$user_id'  AND users.user_id=settings.user_id AND access.user_id = users.user_id AND accounts.account_id = users.account_id
            ORDER BY access.access_id DESC LIMIT 1";
	$results = DBUtil::query($sql);

	if($results == false || mysqli_num_rows($results) == 0)
	{
		return false;
	}

	//set all information for desired user
	$_SESSION = mysqli_fetch_assoc($results);

	//set system user session variable
	$_SESSION['ao_system_user'] = true;

	//set database name and resource link
	$_SESSION['database_name'] = $database_name;
	$_SESSION['database_link'] = $database_link;

	//preload module access
	ModuleUtil::fetchModuleAccess();

	//preload nav access
	UserModel::fetchNavAccess();

	//set widget information
	if(!ModuleUtil::checkAccess('view_schedule'))
	{
		$_SESSION['ao_widget_today'] = '0';
	}
	if(!ModuleUtil::checkAccess('view_announcements'))
	{
		$_SESSION['ao_widget_announcements'] = '0';
	}
	if(!ModuleUtil::checkAccess('view_documents'))
	{
		$_SESSION['ao_widget_documents'] = '0';
	}
	if(!ModuleUtil::checkAccess('view_jobs'))
	{
		$_SESSION['ao_widget_urgent'] = '0';
		$_SESSION['ao_widget_bookmarks'] = '0';
		$_SESSION['ao_widget_journals'] = '0';
	}

	return true;
}

function modifyAccount($data_array)
{
	//build and execute query
	$sql = "UPDATE accounts
			SET account_name = '{$data_array['account-name']}', primary_contact = '{$data_array['primary-contact']}',
			email = '{$data_array['email']}', phone = '{$data_array['phone']}', address = '{$data_array['address']}',
			city = '{$data_array['city']}', state = '{$data_array['state']}', zip = '{$data_array['zip']}',
			license_limit = '{$data_array['license-limit']}'
			WHERE account_id = '{$data_array['account-id']}' LIMIT 1";
	$insert_results = DBUtil::query($sql);

	//if failed, return false
	if($insert_results === false)
	{
		return false;
	}

	return true;
}