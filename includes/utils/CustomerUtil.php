<?php
/**
 * @author cmitchell
 */
class CustomerUtil extends AssureUtil {
    
    private static $customerCache = array();
    
    /**
     * 
     * @param int $customerId
     * @param boolean $asLink
     * @return string
     */
    public static function getDisplayName($customerId, $asLink = TRUE) {
        if(isset(self::$customerCache[$customerId])) {
            $customer = self::$customerCache[$customerId];
        } else {
            $customer = DBUtil::getRecord('customers', $customerId);
            self::$customerCache[$customerId] = $customer;
        }
        
        if(!count($customer)) { return ''; }
        
        $displayName = MapUtil::get($customer, 'nickname') ?: MapUtil::get($customer, 'lname') . ', ' . MapUtil::get($customer, 'fname');
        
        if(!RequestUtil::get('csv') && !defined('CRON_REQUEST') && $asLink) {
            return "<a href=\"/customers.php?id=$customerId\" tooltip>$displayName</a>";
        }
        
        return $displayName;
    }
    
}