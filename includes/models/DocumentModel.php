<?php

/**
 * @author cmitchell
 */
class DocumentModel extends AssureModel {
    
    /**
     * 
     * @return array
     */
    public static function getAllDocumentGroups() {
        return DBUtil::getAll('document_groups', 'label');
    }
    
    /**
     * 
     * @param int $offset
     * @param int $pageSize
     * @return array
     */
    public static function getList($offset = 0, $pageSize = NULL) {
        $offset = $offset >= 0 ? $offset : 0;
        $extraSql = '';
        $searchBys = '';
        $sort = RequestUtil::get('sort');
        $sort = !empty($sort) ? $sort : 'ORDER BY d.document ASC';
        $searchStr = RequestUtil::get('search');
        $searchTerms = explode(' ', trim($searchStr));
        $documentGroupId = RequestUtil::get('document_group_id');

        if($pageSize) {
            $limitStr = "LIMIT $offset, $pageSize";
        }

        if(ModuleUtil::checkOwnership('view_documents')) {
            $extraSql .= "AND (d.user_id = '{$_SESSION['ao_userid']}')";
        }

        if(!empty($searchStr)) {
            $searchBys = array();
            foreach($searchTerms as $term) {
                $term = trim($term);
                $searchBys[] = "AND (
                                    d.document LIKE '%$term%'
                                    OR d.description LIKE '%$term%' 
                                )";
            }
            $searchBys = implode(' ', $searchBys);
        }

        if($documentGroupId) {
            $extraSql .= "AND dgl.document_group_id = '$documentGroupId'";
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS d.document_id, d.document, d.filetype, d.timestamp, concat(u.lname, ', ', u.fname) as owner, d.user_id, dg.label
                FROM users u, documents d
                    LEFT JOIN document_group_link dgl ON (dgl.document_id = d.document_id)
                    LEFT JOIN document_groups dg ON (dgl.document_group_id = dg.document_group_id)
                WHERE d.account_id = {$_SESSION['ao_accountid']}
                    AND d.user_id = u.user_id
                    $extraSql
                    $searchBys
                GROUP BY d.document_id
                $sort
                $limitStr";
        return DBUtil::queryToArray($sql);
    }
    
}