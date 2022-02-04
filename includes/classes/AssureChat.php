<?php

class AssureChat {
    private $chatData = null;
    private $myId;
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function __construct($fromId) {
        $this->myId = $fromId;
    }
    
    /**
    * Description
    * 
    * @param
    * @return
    */
    public function getAllChats($refresh = FALSE) {
        if (isset($this->chatData) && !$refresh) {
            return $this->chatData;
        }
        
        $sql = "SELECT 
                    IF(c.to_user_id = '{$this->myId}',
                        c.from_user_id,
                        c.to_user_id) as user_id,
                    IF(c.to_user_id = '{$this->myId}',
                        u2.fname,
                        u1.fname) as fname,
                    IF(c.to_user_id = '{$this->myId}',
                        u2.lname,
                        u1.lname) as lname
                FROM chats c
                JOIN users u1 ON u1.user_id = c.to_user_id
                JOIN users u2 ON u2.user_id = c.from_user_id
                WHERE 
                    c.to_user_id = '{$this->myId}'
                    OR c.from_user_id = '{$this->myId}'
                GROUP BY user_id";
        $this->chatData = $this->getMessages(DBUtil::queryToArray($sql));
        return $this->chatData;
    }
    
    /**
    * Description
    * 
    * @param
    * @return
    */
    public function getMessages($data) {
        //messages for single chat only
        if (!is_array($data)) {
            return $this->getChatMessages($data);
        }

        foreach($data as $key => $chat) {
            $userId = mysqli_real_escape_string(DBUtil::Dbcont(),$chat['user_id']);
            $data[$key]['messages'] = $this->getChatMessages($userId);
        }

        return $data;
    }
    
    /**
    * Description
    * 
    * @param
    * @return
    */
    private function getChatMessages($userId) {
        $userId = mysqli_real_escape_string(DBUtil::Dbcont(),$userId);
        $sql = "SELECT
                    u.user_id,
                    u.fname,
                    u.lname,
                    c.text,
                    c.is_read,
                    c.timestamp
                FROM chats c, users u
                WHERE 
                    (
                        (c.from_user_id = '$userId' AND c.to_user_id = '{$this->myId}')
                        OR
                        (c.to_user_id = '$userId' AND c.from_user_id = '{$this->myId}')
                    )
                    AND u.user_id = c.from_user_id
                ORDER BY timestamp ASC";
        return DBUtil::queryToArray($sql);
    }
    
    /**
    * Description
    * 
    * @param
    * @return
    */
    public function postMessage($userId, $text) {
        if (empty($text) || !UserModel::get($userId)) {
            return false;
        }

        $userId = mysqli_real_escape_string(DBUtil::Dbcont(),$userId);
        $text = mysqli_real_escape_string(DBUtil::Dbcont(),$text);
        $sql = "INSERT INTO chats
                    (to_user_id, from_user_id, text, timestamp)
                VALUES
                    ('$userId', '{$this->myId}', '$text', NOW())";
        $results = DBUtil::query($sql);
        
        if (!$results) {
            return false;
        }
        
        return $this->getChatMessages($userId);
    }

    /**
    * Description
    * 
    * @param
    * @return
    */
    public function markChatRead($theirUserId) {
        $theirUserId = mysqli_real_escape_string(DBUtil::Dbcont(),$theirUserId);
        $sql = "UPDATE chats
                SET is_read = 1
                WHERE 
                    to_user_id = '{$this->myId}'
                    AND from_user_id = '$theirUserId'";
        $results = DBUtil::query($sql);
        
        if (!$results || mysqli_affected_rows() === 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function getSumNewMessages() {
        $filters = '';
        if($userId) {
            $userId = mysqli_real_escape_string(DBUtil::Dbcont(),$userId);
            $filters = "AND from_user_id = '$userId'";
        }
        
        $sql = "SELECT count(*)
                FROM chats
                WHERE is_read = 0
                    AND to_user_id = '{$this->myId}'
                    $filters";
        $results = DBUtil::query($sql);
        
        if (!$results) {
            return 0;
        }
        
        $row = mysqli_fetch_array($results);
        return $row[0];
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function getSumNewMessagesGroupedByUser() {
        $sql = "SELECT
                    u.user_id,
                    u.fname,
                    u.lname,
                    COUNT(*) AS new
                FROM chats c
                INNER JOIN users u ON c.from_user_id = u.user_id
                WHERE c.to_user_id = '{$this->myId}'
                    AND c.is_read = 0
                GROUP BY u.user_id";
        $results = DBUtil::query($sql);
        return DBUtil::queryToArray($sql, 'user_id');
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public static function getUsersOnline() {
        $sql = "SELECT
                    u.user_id,
                    u.fname,
                    u.lname
                FROM users u, logged_in li
                WHERE
                    li.last_activity > NOW() - INTERVAL 1 MINUTE
                    AND u.user_id = li.user_id
                    AND li.account_id = '{$_SESSION['ao_accountid']}'
                    AND u.user_id != '{$_SESSION['ao_userid']}'
                GROUP BY user_id";
        return DBUtil::queryToArray($sql, 'user_id');
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public static function loadChatApp() {
        echo ViewUtil::loadView('chat-wrapper');
    }
}
