<?php

/**
 * @author cmitchell
 */
class FormUtil extends AssureUtil {
    
    public static $noParens = array(
        'now()',
        'null',
        'curdate()'
    );
    
    public static function getTimePicklist($name = 'time', $selectedVal = NULL) {
        $picklistStr = "<select name=\"$name\">";
        $selectedVal = $selectedVal ?: date('H') . ':' . (date('i') < 30 ? '00' : '30') .':00';
        
        for($h = 1; $h <= 24; $h++) {
            for($m = 0; $m <= 30; $m += 30) {
                $ampm = $h > 11 ? 'pm' : 'am';
                $hour = $h > 12 ? $h-12 : $h;
                $minute = $m === 0 ? '00' : $m;
                $val = DateUtil::formatMySQLTime("$h:$m:00");
                
                $picklistStr .= "<option value=\"$val\"" . ($selectedVal == $val ? ' selected' : '') . ">$hour:$minute $ampm</option>";
            }
        }
        
        return $picklistStr . '</select>';
    }
    
    public static function update($table = NULL, $id = NULL, $field = NULL) {
        $table = $table ?: RequestUtil::get('table');
        $id = $id ?: RequestUtil::get('id');
        $tableFields = MapUtil::mapTo(DBUtil::getTableFields($table), 'Field');
       //echo "<pre>";print_r($tableFields);die;
        //get the id field
        $idField = reset($tableFields);
        $field = $field ?: $idField['Field'];
        
        //echo "<pre>";print_r(self::createUpdateSql($table, $id, $field));die;
        $results = DBUtil::query(self::createUpdateSql($table, $id, $field));
        //var_dump($results); die();

        if(!$results) { return $results; }
       
        //force re-cache
        RequestUtil::set('ignore_cache', 1);
        return DBUtil::getRecord($table, $id, $field);
    }
    
    /**
     * 
     * @param string $table
     * @param id $id
     * @return mixed
     */
    public static function createUpdateSql($table = NULL, $id = NULL, $field = NULL) {
        $table = $table ?: RequestUtil::get('table');
        $id = $id ?: RequestUtil::get('id');
        $tableFields = MapUtil::mapTo(DBUtil::getTableFields($table), 'Field');
        
        //get the id field
        $idField = reset($tableFields);
        $field = $field ?: $idField['Field'];
        
        //iterate through all request variables and build set statements
        
        
        
        $sets = array();
        foreach($_POST as $name => $value) {
            if(array_key_exists($name, $tableFields)) {
                //format if phone
                $value = strpos($name, 'phone') !== FALSE || strpos($name, 'fax') !== FALSE ? StrUtil::formatPhoneToSave($value) : $value;
                
                //set to Default if empty and fieeld will set default value
                if(empty($value) && $tableFields[$name]['Field'] =='generalins') {
                    $value = 'NOW()';
                }
                if(empty($value) && $tableFields[$name]['Field'] =='workerins') {
                    $value = '0000-00-00 00:00:00';
                }
                //set to null if empty and filed accepts null
                if(empty($value) && $tableFields[$name]['Null'] === 'YES') {
                    $value = 'NULL';
                }                              
                //save set statement
                if($name == 'adjuster_phone')
                {
                    $value .=':'.$_POST['adjuster_ext'];
                    unset($_POST['adjuster_ext']);
                }
                if($name != 'adjuster_ext')
                {
                    $sets[] = in_array(strtolower($value), self::$noParens) ? "$table.$name = $value" : "$table.$name = '$value'";
                }
            }
        }
        //nothing to update
        if(!count($sets)) {
            return NULL;
        }
        
        
        //finalize and return query
        $setSql = 'SET ' . implode(', ', $sets);
        $accountSql = MapUtil::get($tableFields, 'account_id') ? "AND $table.account_id = '{$_SESSION['ao_accountid']}'" : '';
        $sql="UPDATE $table $setSql WHERE $table.$field = '$id' $accountSql LIMIT 1";
       
       // return $sql;

        return "UPDATE $table $setSql WHERE $table.$field = '$id'  $accountSql LIMIT 1";
        
    }
    
}