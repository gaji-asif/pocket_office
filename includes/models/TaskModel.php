<?php

/**
 * @author cmitchell
 */
class TaskModel extends AssureModel {
    
    public static function getAllTaskTypes() {
        return DBUtil::getAll('task_type', 'task');
    }
    
}