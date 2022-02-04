<?php
/**
 * @author cmitchell
 */
class CurrencyUtil extends AssureUtil {
    
    /**
     * 
     * @param int $amount
     * @return float
     */
    public static function formatUSD($amount) {
        return sprintf("%01.2f", $amount);
    }
    
}