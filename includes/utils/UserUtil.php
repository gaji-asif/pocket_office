<?php
/**
 * @author cmitchell
 */
class UserUtil extends AssureUtil {
    
    private static $userCache = array();
    
    /**
     * 
     * @param int, array $userId
     * @param boolean $asLink
     * @param boolean $firstLast
     * @param boolean $useDba
     * @return string
     */
    public static function getDisplayName($userId = NULL, $asLink = TRUE, $firstLast = NULL, $useDba = FALSE) {
        $firstLast = is_null($firstLast) ? UIUtil::getFirstLast() : $firstLast;
        if(!is_array($userId)) {
//            if(isset(self::$userCache[$userId])) {
//                $user = self::$userCache[$userId];
//            } else {
                $user = DBUtil::getRecord('users', $userId);
//                self::$userCache[$userId] = $user;
//            }
        } else {
            $user = $userId;
            $userId = MapUtil::get($user, 'user_id');
        }
        
        
        if(!count($user)) { return ''; }
        
        $dba = MapUtil::get($user, 'dba');
        $displayName = $dba && $useDba ? $dba : ($firstLast ? MapUtil::get($user, 'fname') . ' ' . MapUtil::get($user, 'lname')
                        : MapUtil::get($user, 'lname') . ', ' . MapUtil::get($user, 'fname'));
        
        if(!RequestUtil::get('csv') && !defined('CRON_REQUEST') && $asLink) {
            return "<a href=\"/workflow/users.php?id=$userId\" tooltip>$displayName</a>";
        }
        
        return $displayName;
    }
    
    /**
     * 
     * @param int $userId
     * @param boolean $asLink
     * @param boolean $firstLast
     * @return string
     */
    public static function getDbaOrDisplayName($userId = NULL, $asLink = TRUE, $firstLast = NULL) {
        return self::getDisplayName($userId, $asLink, $firstLast, TRUE);
    }
    
    /**
     * 
     * @param int, array $userId
     * @return string
     */
    public static function getUsernameLink($userId = NULL) {
        if(!is_array($userId)) {
//            if(isset(self::$userCache[$userId])) {
//                $user = self::$userCache[$userId];
//            } else {
//                $user = DBUtil::getRecord('users', $userId);
                self::$userCache[$userId] = $user;
//            }
        } else {
            $user = $userId;
            $userId = MapUtil::get($user, 'user_id');
        }
        
        
        if(!count($user)) { return ''; }
        
        $username = MapUtil::get($user, 'username');
        return "<a href=\"/users.php?id=$userId\" tooltip>@$username</a>";
    }
    
    /**
     * 
     * @param int $userId
     * @return int
     */
    public static function getUserLevelByUserId($userId) {
        $user = DBUtil::getRecord('users', $userId);
        return MapUtil::get($user, 'level');
    }
    
    /**
     * 
     * @return array
     */
    public static function getBookmarks() {
        $sql = "SELECT b.job_id, j.job_number
                FROM bookmarks b, jobs j
                WHERE b.user_id = '{$_SESSION['ao_userid']}'
                    AND b.job_id = j.job_id
                ORDER BY b.timestamp desc";
        return DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     * @param int $userId
     * @return mixed
     */
    public static function getOffice($userId = NULL) {
        $userId = $userId ?: $_SESSION['ao_userid'];
        
        $user = DBUtil::getRecord('users', $userId);
        if(!$user) { return FALSE; }

        $officeId = MapUtil::get($user, 'office_id');
        return $officeId ? DBUtil::getRecord('offices', $officeId) : DBUtil::getRecord('accounts', MapUtil::get($user, 'account_id'));
    }
    
    public static function generatePassword($length = 10) {
        $vowels = 'aeuyAEUY';
        $consonants = 'bdghjmnpqrstvz@#$%BDGHJLMNPQRSTVWXZ23456789@#$%';

        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }
        return $password;
    }
    
    public static function sortUsersByDBA($usersArray) {
        usort($usersArray, function($a, $b) {
            $sortKey = (!empty($a['dba']) && !empty($a['dba']))  ? 'dba' : 'lname';
            $aStr = strtolower($a[$sortKey]);
            $bStr = strtolower($b[$sortKey]);

            if ($aStr == $bStr) {
                return 0;
            }

            return ($aStr > $bStr) ? +1 : -1;
        });

        return $usersArray;
    }
	public static function getinsurance($userid)
	{
		$sql = "SELECT pdfname, datecreated
                FROM insurancepdfupload
                WHERE user_id = '$userid'
                ORDER BY datecreated desc";
        
        return DBUtil::queryToArray($sql);
	}

    

}