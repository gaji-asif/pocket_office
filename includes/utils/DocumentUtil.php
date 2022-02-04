<?php
/**
 * @author cmitchell
 */
class DocumentUtil extends AssureUtil {
    
    /**
     * 
     * @param int $documentId
     * @return array
     */
    public static function getDocumentGroup($documentId) {
        $sql = "SELECT dg.*
                FROM document_groups dg
                JOIN document_group_link dgl ON dgl.document_group_id = dg.document_group_id
                WHERE dgl.document_id = '$documentId'
                LIMIT 1";
        return DBUtil::fetchAssociativeArray(DBUtil::query($sql));
    }
    
}