<?php
/**
 * @author cmitchell
 */
class PermissionUtil extends AssureUtil {
    
    public static function hasJobModuleAccess($hook, $job = NULL) {
        if(!$job instanceof job) {
            return false;
        }
        return ((ModuleUtil::checkAccess($hook) && !moduleOwnership($hook)) || (moduleOwnership($hook) && (JobUtil::isSubscriber($job->job_id) || $job->salesman_id == $_SESSION['ao_userid'] || $job->user_id == $_SESSION['ao_userid'])));
    }
    
}