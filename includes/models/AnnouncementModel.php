<?php

/**
 * @author cmitchell
 */
class AnnouncementModel extends AssureModel {
    
    /**
     * 
     * @param int $announcementId
     * @return boolean
     */
    public static function isRead($announcementId) {
        $sql = "SELECT *
                FROM read_announcements
                WHERE announcement_id = '$announcementId'
                    AND user_id = '{$_SESSION['ao_userid']}'
                    AND account_id = '{$_SESSION['ao_accountid']}'
                LIMIT 1";
        return DBUtil::hasRows(DBUtil::query($sql));
    }
    
    /**
     * 
     * @param int $announcementId
     * @return mixed
     */
    public static function markRead($announcementId) {
        $sql = "INSERT INTO read_announcements (announcement_id, user_id, account_id, timestamp)
                VALUES ('$announcementId', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', NOW())";
        return DBUtil::query($sql);
    }
    /**
     * 
     * @param int $announcementId
     * @return array
     */
    public static function getAccessHistory($announcementId) {
        $concatSql = UIUtil::getFirstLast() ? "CONCAT(u.fname, ' ', u.lname)" : "CONCAT(u.lname, ', ', u.fname)";
        $sql = "SELECT ra.timestamp, $concatSql AS display_name, ra.user_id
                FROM read_announcements ra
                JOIN users u ON u.user_id = ra.user_id
                WHERE ra.announcement_id = '$announcementId'
                ORDER BY ra.timestamp DESC";
        return DBUtil::queryToArray($sql);
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
        $limitStr = '';
        $sort = RequestUtil::get('sort', 'ORDER BY a.timestamp desc');
        $searchStr = RequestUtil::get('search');
        $searchTerms = explode(' ', trim($searchStr));

        if($pageSize) {
            $limitStr = "LIMIT $offset, $pageSize";
        }

        if(!empty($searchStr)) {
            $searchBys = array();
            foreach($searchTerms as $term) {
                $term = trim($term);
                $searchBys[] = "AND (
                                    a.subject LIKE '%$term%'
                                    OR a.body LIKE '%$term%' 
                                )";
            }
            $searchBys = implode(' ', $searchBys);
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS a.announcement_id
                FROM announcements a
                WHERE a.account_id = '{$_SESSION['ao_accountid']}'
                    $extraSql
                    $searchBys
                GROUP BY a.announcement_id
                $limitStr";
        return DBUtil::queryToArray($sql);
    }
    
}