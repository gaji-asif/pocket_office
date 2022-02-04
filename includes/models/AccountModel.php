<?php

/**
 * @author cmitchell
 */
class AccountModel extends AssureModel {
    
    public static function getAllOffices() {
        return DBUtil::getAll('offices', 'title');
    }
    
    public static function getAllMetaData() {
        return DBUtil::getAll('account_meta', 'meta_key', NULL, 'meta_key');
    }
    
    public static function getMetaValue($key, $defaultValue = NULL) {
        $sql = "SELECT meta_value
                FROM account_meta
                WHERE account_id = '{$_SESSION['ao_accountid']}'
                    AND meta_key = '$key'
                LIMIT 1";
        return DBUtil::queryToScalar($sql, $defaultValue);
    }
    
    public static function deleteMetaValue($key) {
        $sql = "DELETE
                FROM account_meta
                WHERE account_id = '{$_SESSION['ao_accountid']}'
                    AND meta_key = '$key'
                LIMIT 1";
        return DBUtil::query($sql);
    }
    
    public static function setMetaValue($key, $value) {
        if(empty($key) || !AccountModel::deleteMetaValue($key)) {
            return FALSE;
        }
        
        $sql = "INSERT INTO account_meta (account_id, meta_key, meta_value)
                VALUES('{$_SESSION['ao_accountid']}', '$key', '$value')";
        return DBUtil::query($sql);
    }
    
    public static function getLogoImageTag() {
        if(file_exists(ROOT_PATH.'/logos/' . $_SESSION['ao_logo'])) {
            return '<img height=125 width=280 src="' . ROOT_DIR.'/logos/' . $_SESSION['ao_logo'] . '">';
        }
    }
    
    public static function getAll() {
        return DBUtil::getAll('accounts');
    }
    
    public static function getById($accountId) {
        return DBUtil::getRecord('accounts', $accountId);
    }
    
}