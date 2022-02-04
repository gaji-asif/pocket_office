<?php
/**
 * @author cmitchell
 */
class ModuleUtil extends AssureUtil {
    
    /**
     * 
     * @param string $actionHook
     * @return mixed
     */
    public static function getJobActionByHook($actionHook) {
        $actionHook = mysqli_real_escape_string(DBUtil::Dbcont(),$actionHook);
        
        $sql = "SELECT * 
                FROM job_actions
                WHERE hook = '$actionHook'
                LIMIT 1";
        $results = DBUtil::query($sql);
        
        return !$results || !mysqli_num_rows($results) ? FALSE : mysqli_fetch_assoc($results);
    }
    
    public static function checkJobModuleAccess($hooks, $job, $showModalAlert = FALSE, $hideCloseLink = FALSE) {
        if(!$job || !($job instanceof job)) {
            return FALSE;
        }
        $hooks = is_array($hooks) ? $hooks : array($hooks);
        
        foreach($hooks as $hook) {
            $hasAccess = ((ModuleUtil::checkAccess($hook) && !moduleOwnership($hook)) || (moduleOwnership($hook) && (JobUtil::isSubscriber($job->job_id) || $job->salesman_id == $_SESSION['ao_userid'] || $job->user_id == $_SESSION['ao_userid'] || $job->canvasser_id == $_SESSION['ao_userid'])));
            if(!$hasAccess) { break; }
        }
        
        if($showModalAlert && !$hasAccess) {
            self::showInsufficientRightsAlert($hook, $hideCloseLink);
            die();
        }
        
        return $hasAccess;
    }
    
    public static function checkAccess($hooks, $showModalAlert = FALSE, $hideCloseLink = FALSE) {
        $hasAccess = FALSE;
        $hooks = is_array($hooks) ? $hooks : array($hooks);
        //echo "<pre>";print_r($_SESSION['ao_module_access']);
        if (is_array($_SESSION['ao_module_access'])) {
            foreach($hooks as $hook) {
                $hasAccess = in_array($hook, $_SESSION['ao_module_access']);
                if(!$hasAccess) { break; }
            }
        }
        
        if($showModalAlert && !$hasAccess) {
            self::showInsufficientRightsAlert($hook, $hideCloseLink);
            die();
        }
        
        return $hasAccess;
    }
    
    public static function checkOwnership($hook, $userId = NULL, $accountId = NULL) {
        $accountId = $accountId ?: $_SESSION['ao_accountid'];
        $userId = $userId ?: $_SESSION['ao_userid'];
        $level = UserUtil::getUserLevelByUserId($userId);
        
        $sql = "SELECT *
                FROM modules m
                LEFT JOIN exceptions e ON e.module_id = m.module_id 
                    AND e.user_id = '$userId'
                LEFT JOIN module_access ma ON ma.module_id = m.module_id 
                    AND ma.account_id = '$accountId'
                    AND ma.level = '$level'
                WHERE m.hook = '$hook'
                    AND (
                        (ma.ownership = 1 AND e.ownership IS NULL)
                        OR (e.ownership = 1 AND e.onoff = 1)
                    )
                LIMIT 1";
        return DBUtil::hasRows(DBUtil::query($sql));
    }
    
    public static function canAccessObject($hook, $object, $showModalAlert = FALSE, $hideCloseLink = FALSE) {
        $shouldCheckOwnership = self::checkOwnership($hook);
        
        //can pass either object or array
        if(!method_exists($object, 'hasOwnership')) {
            $userId = MapUtil::get($object, 'user_id');
            $hasOwnership  = $userId ? $userId == $_SESSION['ao_userid'] : TRUE;
        } else {
            $hasOwnership = call_user_func(array($object, 'hasOwnership'));
        }
        
        if($shouldCheckOwnership && !$hasOwnership && $showModalAlert) {
            self::showInsufficientRightsAlert($hook, $hideCloseLink);
            die();
        }
        
        return $shouldCheckOwnership ? $hasOwnership : TRUE;
    }
    
    public static function hasAccess($hook, $object) {
        $className = get_class($object);
        switch ($className) {
            case 'customer':
                return self::hasCustomerAccess($hook, $object);
                break;
            case 'job':
                return self::hasJobAccess($hook, $object);
                break;
            case 'repair':
                return self::hasRepairAccess($hook, $object);
                break;
            case 'task':
                return self::hasTaskAccess($hook, $object);
                break;
            case 'user':
                return self::hasUserAccess($hook, $object);
                break;
            default:
                return false;
        };
    }
    
    public static function checkIsFounder($showModalAlert = FALSE) {
        $isFouder = ($_SESSION['ao_founder']);
        
        if(!$isFouder && $showModalAlert) {
            self::showInsufficientRightsAlert();
            die();
        }
        
        return $isFouder;
    }
    
    public static function getModuleByHook($hook = NULL) {
        $sql = "SELECT *
                FROM modules
                WHERE hook = '$hook'
                LIMIT 1";
        return DBUtil::fetchAssociativeArray(DBUtil::query($sql));
    }
    
    public static function showInsufficientRightsAlert($hook = NULL, $hideCloseLink = FALSE) {
        echo ViewUtil::loadView('insufficient-rights-modal', array('module' => self::getModuleByHook($hook), 'hideCloseLink' => $hideCloseLink));
    }
    
    public static function fetchModuleAccess() {
        $sql = "SELECT m.hook, ma.module_access_id, e.onoff
                FROM modules m
                LEFT JOIN exceptions e ON e.module_id = m.module_id AND e.user_id = '{$_SESSION['ao_userid']}'
                LEFT JOIN module_access ma ON ma.module_id = m.module_id AND ma.account_id = '{$_SESSION['ao_accountid']}' AND ma.level = '{$_SESSION['ao_level']}'";
        $modules = DBUtil::queryToArray($sql);

        $_SESSION['ao_module_access'] = array();
        foreach($modules as $module) {
            if ((($_SESSION['ao_founder'] == '1' && $module['hook'] != 'events_view_all') || $module['module_access_id'] != '' || $module['onoff'] == '1') && $module['onoff'] != '0') {
                $_SESSION['ao_module_access'][] = $module['hook'];
            }
        }
    }
    
    public static function getAll() {
        return DBUtil::getAll('modules');
    }
    
    public static function moduleIsInUse($moduleId)  {
        $jobActions = self::getJobActionsByModule($moduleId);
        $moduleAccess = self::getSystemModuleAccess($moduleId);

        return (!empty($jobActions) || !empty($moduleAccess));
    }

    public static function getJobActionsByModule($moduleId) {
        $sql = "SELECT *
                FROM job_actions ja, modules m
                WHERE ja.hook = m.hook AND m.module_id = '$moduleId'
                ORDER BY action ASC";
        return DBUtil::queryToArray($sql);
    }

    public static function getSystemModuleAccess($moduleId) {
        $sql = "SELECT * FROM module_access WHERE module_id = '$moduleId'";
        return DBUtil::queryToArray($sql);
    }

}