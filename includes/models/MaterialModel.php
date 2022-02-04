<?php

/**
 * @author cmitchell
 */
class MaterialModel extends AssureModel {
    
    public static function getAllSuppliers() {
        $sql = "SELECT sl.supplier_id, s.supplier
                FROM suppliers_link sl, suppliers s
                WHERE sl.account_id = '{$_SESSION['ao_accountid']}'
                    AND sl.supplier_id = s.supplier_id
                ORDER BY s.supplier asc";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getAllCategories() {
        return DBUtil::getAll('categories', 'category');
    }
    
    public static function getAllBrands() {
        return DBUtil::getAll('brands', 'brand');
    }
    
    public static function getCategoryById($categoryId) {
        return DBUtil::getRecord('categories', $categoryId);
    }
    
    public static function getMaterialsInCategory($categoryId) {
        $sql = "SELECT m.*, u.unit
                FROM materials m, units u
                WHERE m.unit_id = u.unit_id 
                    AND m.category_id = '$categoryId'
                ORDER BY m.material ASC";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getMaterialsInCategoryAndInBrand($categoryId, $brandId) {
        $sql = "SELECT m.*, u.unit
                FROM materials m
                JOIN units u ON m.unit_id = u.unit_id
                WHERE m.category_id = '$categoryId'
                    AND m.brand_id = '$brandId'
                ORDER BY m.material ASC";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getMaterialColors($materialId) {
        return DBUtil::getRecords('colors', $materialId, 'material_id');
    }
    
}