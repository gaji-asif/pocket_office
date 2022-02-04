<?php



/**

 * @author cmitchell

 */

class StageModel extends AssureModel {



    public static function getAllStages($groupByStageNum = FALSE) {

        

        $stageNameSql = $groupByStageNum ? "GROUP_CONCAT(stage SEPARATOR ', ')" : 'stage';

        $groupBy = $groupByStageNum ? 'GROUP BY stage' : '';

        $sql = "SELECT stage_id, stage_num, $stageNameSql AS stage

                FROM stages

                WHERE account_id='{$_SESSION['ao_accountid']}'

                $groupBy

                ORDER BY order_num ASC";

        return DBUtil::queryToArray($sql, 'order_num');

    }

    

    public static function getStages() {

        return DBUtil::getAll('stages');

    }

    

    public static function getRequirements($stageId = NULL, $accountId = NULL) {

        $accountId = $accountId ?: $_SESSION['ao_accountid'];



        $extraSql = $stageId ? "AND srl.stage_id = '$stageId'" : '';

        $sql = "SELECT sr.*

                FROM stage_reqs_link srl

                JOIN stage_reqs sr ON sr.stage_req_id = srl.stage_req_id

                WHERE srl.account_id = '$accountId'

                $extraSql";

        return DBUtil::queryToArray($sql);

    }

    

    public static function getCSVStagesByStageNum($stageNum) {

        $stages = DBUtil::getRecords('stages', $stageNum, 'stage_num');

        return implode(', ', MapUtil::pluck($stages, 'stage'));

    }

    

    public static function getStageNameById($stageId) {

        $stage = DBUtil::getRecord('stages', $stageId);

        return MapUtil::get($stage, 'stage');

    }

    

    public static function getStageIdByName($stageName) {

        return DBUtil::getRecord('stages', $stageName, 'stage');

    }

    

	 public static function getLastStageNum() {

        $sql = "SELECT stage_num FROM `stages` WHERE LOWER(stage) like '%closed%' and account_id = '{$_SESSION['ao_accountid']}'";

        return DBUtil::queryToScalar($sql);

    }



    public static function getFinalStageNum() {

        $sql = "SELECT stage_num

                FROM stages

                WHERE account_id = '{$_SESSION['ao_accountid']}'

                ORDER BY order_num DESC

                LIMIT 1";

        return DBUtil::queryToScalar($sql);

    }

    

    public static function getStageClass($stageNum) {

        $stage = DBUtil::getRecord('stages', $stageNum, 'stage_num');

        return MapUtil::get($stage, 'class');

    }

     public static function getJobStages($job_id){



        $sql = "select * from job_stages where job_id = ".$job_id." order by job_stage_num asc";

        return DBUtil::queryToArray($sql);

    }

}