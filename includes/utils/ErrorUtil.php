<?php

/**
 * @author cmitchell
 */
class ErrorUtil extends AssureUtil{
    
    /**
     * 
     * @return boolean
     */
    public static function fatalShutdownHandler() {
        return;
        $error = error_get_last();
        
        if($error) {
            //we ony want errors and warnings...
            $type = MapUtil::get($error, 'type');
            if(!in_array($type, array(2, 4))) {
                return FALSE;
            }
            
            $body = ViewUtil::loadView('mail/error', array('error' => $error));
            $subject = 'Error';
            return self::send($subject, $body);
        }
    }
    
    /**
     * 
     * @param string $subject
     * @param string $body
     * @return boolean
     */
    public static function send($subject, $body) {
        if(defined('DISABLE_EMAIL')) { return; }
        //get new mail object
        $mail = new PHPMailer();

        //add from, to, subject, body
        $mail->SetFrom(ALERTS_EMAIL, APP_NAME . " Alerts");
        $mail->AddAddress("cbm3384@gmail.com", "Christopher Mitchell");
        $mail->Subject = APP_NAME . " Alerts: $subject";
        $mail->MsgHTML($body);

        //send
        return $mail->Send();
    }
    
}