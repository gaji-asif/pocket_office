<?php
include 'includes/common_lib.php';
$redirectTo = ROOT_DIR . '?' . http_build_query($_GET);

if(UserModel::loggedIn()) {
    //header('Location: dashboard.php');
    
    parentRedirect(ROOT_DIR);
    parentRedirect($redirectTo);
	die();
}


// echo $redirectTo;die;
$errorStr = '';

if(isset($_POST['username']) && isset($_POST['password'])) 
{
    
	if(AuthModel::processLogin()) {
        if($_SESSION['is_active'] == 1 && $_SESSION['account_is_active'] == 1 && $_SESSION['is_deleted'] == 0) {
            //preload module access
            ModuleUtil::fetchModuleAccess();

            //preload nav access
            UserModel::fetchNavAccess();

			//set widget access
			UserModel::fetchWidgetAccess();

			//set system user access
			AuthModel::setSystemUserAccess();
            UserModel::logAccess($_SESSION['ao_userid']);
            
            
            //redirect to app
            parentRedirect($redirectTo);
        }
        else {
            $errorStr = 'Account Not Active';
        }
    }
    else {
		LogUtil::getInstance()->logNotice("Failed login - Invalid credentials: $account, $username, $password");
        $errorStr = 'Incorrect Login';
    }
}

//kill the session
logout();

echo ViewUtil::loadView('doc-head', array('body_class' => 'login'));

$view_data = array(
	'error_str' => $errorStr
);
echo ViewUtil::loadView('login');
?>
<script>
$.backstretch(
<?php
$backgrounds = array();
for($i = 1; $i < 10; $i++) {
    $backgrounds[] = "\"img/bg/$i.jpg\"";
}
?>
    [<?=implode(',', $backgrounds)?>], {duration: 5000, fade: 1500});
</script>
</body>
</html>