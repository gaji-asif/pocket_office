<?php

/**
 * @author cmitchell
 */
class UIModel extends AssureModel {
    
    public static function getNavList() {
        $sql = "SELECT n.navigation_id, n.title, n.source, n.icon
                FROM navigation n, nav_access na
                WHERE 
                    na.account_id = '{$_SESSION['ao_accountid']}'
                    AND n.navigation_id = na.navigation_id
                    AND na.level = '{$_SESSION['ao_level']}'
                ORDER BY n.order_num ASC";
        return DBUtil::queryToArray($sql);
    }
    
}