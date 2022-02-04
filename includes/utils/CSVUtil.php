<?php
/**
 * @author cmitchell
 */
class CSVUtil extends AssureUtil {
    
    public static function generate($rows, $fileName) {
        $fileName = $fileName ?: md5(time());
        header("Content-Disposition: attachment; filename=$fileName.csv");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Transfer-Encoding: binary");
        
        $handle = fopen("php://output", "w");
        foreach ($rows as $row) {
            fputcsv($handle, array_map('strval', $row));
        }
        fclose($handle);
        die();
    }
    
    public static function generateAndSave($rows, $fileName = NULL) {
        $fileName = $fileName ?: md5(time());
        $tmpFileName = $fileName;
        
        $count = 1;
        while(file_exists($tmpFileName) && ++$count) {
            $tmpFileName = "$fileName ($count)";
        }
        
        $handle = fopen("$tmpFileName.csv", "w");
        foreach ($rows as $row) {
            fputcsv($handle, array_map('strval', $row));
        }
        fclose($handle);
        return "$tmpFileName.csv";
    }

}