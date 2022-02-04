<?php
/**
 * @author cmitchell
 */
class TaskUtil extends AssureUtil {
    
    /**
     * 
     * @param int $taskId
     * @return array
     */
    public static function getAutoCreateTasks($taskTypeId) {
        $sql = "SELECT tt.*
                FROM task_type tt, auto_create_tasks act
                WHERE tt.task_type_id = act.child_task_type_id
                    AND act.task_type_id = '$taskTypeId'
                    AND act.active = 1
                ORDER BY tt.task ASC";
        return DBUtil::queryToArray($sql);
    }
    
    public static function updateAutoCreateTasks($taskTypeId, $currentAutoCreateTasks, $newAutoCreateTasks = NULL) {
        $newAutoCreateTasks = $newAutoCreateTasks ?: RequestUtil::get('auto_create_tasks', array());
        $currentAutoCreateTaskTypeIds = MapUtil::pluck($currentAutoCreateTasks, 'task_type_id');
        $newAutoCreateTaskTypeIds = MapUtil::pluck($newAutoCreateTasks, 'task_type_id');
        
        //only add new
        foreach($newAutoCreateTasks as $newAutoCreateTask) {
            $newAutoCreateTaskTypeId = MapUtil::get($newAutoCreateTask, 'task_type_id');
            if(in_array($newAutoCreateTaskTypeId, $currentAutoCreateTaskTypeIds)) {
                continue;
            }
            
            $sql = "INSERT INTO auto_create_tasks (account_id, user_id, task_type_id, child_task_type_id)
                    VALUES ('{$_SESSION['ao_accountid']}', '{$_SESSION['ao_userid']}', '$taskTypeId', '$newAutoCreateTaskTypeId')";
            DBUtil::query($sql);
        }
        
        //remove old
        foreach($currentAutoCreateTaskTypeIds as $currentAutoCreateTaskTypeId) {
            if(in_array($currentAutoCreateTaskTypeId, $newAutoCreateTaskTypeIds)) {
                continue;
            }
            
            $sql = "UPDATE auto_create_tasks
                    SET active = 0
                    WHERE task_type_id = '$taskTypeId'
                        AND child_task_type_id = '$currentAutoCreateTaskTypeId'
                    LIMIT 1";
            DBUtil::query($sql);
        }
    }
}