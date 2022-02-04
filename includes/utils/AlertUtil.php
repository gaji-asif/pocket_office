<?php
/**
 * @author cmitchell
 */
class AlertUtil extends AssureUtil {
    
    public static function generate($alerts, $type = 'error', $margins = TRUE) {
        if(empty($alerts)) { return ''; }
        
        return '<div class="alert alert-'. $type . ($margins ? ' margins' : '') . '">' . implode('<br />', $alerts) . '</div>';
    }
    
}