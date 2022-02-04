<?php

/**
 * @author cmitchell
 */
class FileUtil extends AssureUtil {
    
    private static $incPaths = array('classes', 'utils', 'models');
    
    public static function init() {
        spl_autoload_register('FileUtil::autoloader');
    }
    
    public static function autoloader($className) {
        foreach (self::$incPaths as $incDir) {
            $file = INCLUDES_PATH . "/$incDir/$className.php";
            if (file_exists($file)) {
                include $file;
                return;
            }
        }
    }
    
    public static function version($file) {
        $path = ROOT_PATH . $file;
        if (!file_exists($path)) {
            return $file;
        }

        $mtime = filemtime($path);
        return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
    }
    
}