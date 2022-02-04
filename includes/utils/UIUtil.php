<?php
/**
 * @author cmitchell
 */
class UIUtil extends AssureUtil {
    
    /**
     * 
     * @param job $jobObject
     * @param string $actionHook
     * @param string $tooltip
     * @return string
     */
    public static function inlineJobActionLink($jobObject, $actionHook, $tooltip = 'Edit') {
        $jobAction = ModuleUtil::getJobActionByHook($actionHook);
        
        if(!$jobAction || !ModuleUtil::checkJobModuleAccess($actionHook, $jobObject)) {
            return '';
        }
        
        $viewData = array(
            'myJob' => $jobObject,
            'jobAction' => $jobAction,
            'tooltip' => $tooltip
        );
        return ViewUtil::loadView('inline-job-action-link', $viewData);
    }
    
    /**
     * 
     * @param string $hexColor
     * @return string
     */
    public static function getContrast($hexColor){
        return (hexdec($hexColor) > (0xffffff / 2)) ? 'black '.hexdec($hexColor) : 'white '.hexdec($hexColor);
        
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        
        return ($yiq >= 100) ? 'black' : 'white';
    }
    
    /**
     * 
     * @param string $error
     */
    public static function showModalError($error) {
        echo ViewUtil::loadView('error-modal', array('error' => $error));
        die();
    }
    
    /**
     * 
     * @param string $error
     */
    public static function showListError($error) {
        echo ViewUtil::loadView('error-list', array('error' => $error));
        die();
    }
    
    /**
     * 
     * @param int $phone
     * @return string
     */
    public static function formatPhone($phone) {
        return preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '($1) $2-$3', $phone);
    }
    
    /**
     * 
     * @return string
     */
    public static function getNameOrderOptions() {
        $firstLast = self::getFirstLast();
        return '
            <option value="first_last" ' . ($firstLast ? 'selected' : '') . '>First Last</option>
            <option value="last_first" ' . ($firstLast ? '' : 'selected') . '>Last, First</option>
        ';
    }
    
    /**
     * 
     * @return boolean
     */
    public static function getFirstLast() {
        return SettingsUtil::get('name_order') == 'first_last';
    }
    
    /**
     * 
     * @param string $first
     * @param string $last
     * @return string
     */
    public static function getDisplayName($first, $last, $dba = NULL) {
        return $dba ?: (self::getFirstLast() ? "$first $last" : "$last, $first");
    }
    
    /**
     * 
     * @param User, int $user
     * @return mixed
     */
    public static function getUserLink($user) {
        if(is_a($user, 'User')) {
            $displayName = $user->getDisplayName();
            $userId = $user->getMyId();
        } else {
            $user = DBUtil::getRecord('users', $user);
            if($user) {
                $displayName = self::getDisplayName(MapUtil::get($user, 'fname'), MapUtil::get($user, 'lname'), MapUtil::get($user, 'dba'));
                $userId = MapUtil::get($user, 'user_id');
            }
        }
        
        return $displayName ? "<a href=\"/users.php?id=$userId\" data-type=\"user\" data-id=\"$userId\" tooltip>$displayName</a>" : NULL;
    }
    
    /**
     * 
     * @param string $content
     * @return string
     */
    public static function cleanOutput($content, $forTextarea = TRUE) {
        
        if($forTextarea) {
            $content = str_replace(array("\\\r\\\n", "\\\r", "\\\n"), "\r", $content);
            $content = str_replace(array("\\r\\n", "\\r", "\\n"), "\r", $content);
            $content = str_replace(array("\r\n", "\r", "\n"), "\r", $content);
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        } else {
            $content = str_replace(array("\\\r\\\n", "\\\r", "\\\n"), "<br />", $content);
            $content = str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $content);
            $content = str_replace(array("\r\n", "\r", "\n"), "<br />", $content);
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        }
        
        $content = str_replace(array("\\'"), "'", $content);
        $content = str_replace(array("\\\""), "\"", $content);

        return $content;
    }
    
    /**
     * 
     * @param string $message
     */
    public static function showAlert($message) {
        echo ViewUtil::loadView('js/alert', array('message' => $message));
    }
    
    /**
     * 
     * @param string, int $selectedValue
     * @return string
     */
    public static function getWeekdayOptions($selectedValue = NULL) {
        $options = '<option value=""></option>';
        for ($i = 0; $i < 7; $i++) {
            $time = strtotime("+$i days", strtotime('2010-03-28'));
            $dayNum = date('N', $time);
            $dayName = date('l', $time);
            $selected = $selectedValue == $dayNum ? 'selected' : '';
            $options .= "<option value=\"$dayNum\" $selected>$dayName</option>";
        }
        
        return $options;
    }
    
    /**
     * 
     * @param type $selectedValue
     * @return type
     */
    public static function getDayIntervalOptions($selectedValue = NULL) {
        $options = '<option value=""></option>';
        for ($i = 1; $i < 31; $i++) {
            $selected = $selectedValue == $i ? 'selected' : '';
            $label = $i == 1 ? 'Every day' : "Every $i days";
            $options .= "<option value=\"$i\" $selected>$label</option>";
        }
        
        return $options;
    }
    
    /**
     * 
     * @param type $selectedValue
     * @return type
     */
    public static function getWeekIntervalOptions($selectedValue = NULL) {
        $options = '<option value=""></option>';
        for ($i = 1; $i < 8; $i++) {
            $selected = $selectedValue == $i ? 'selected' : '';
            $label = $i == 1 ? 'Every week' : "Every $i weeks";
            $options .= "<option value=\"$i\" $selected>$label</option>";
        }
        
        return $options;
    }
}