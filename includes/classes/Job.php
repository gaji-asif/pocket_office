<?php

class Job extends AssureClass {

    public $job_id;
    public $job_number;
    public $customer_id;
    public $stage_num;
    public $stages;
    public $salesman_id;
    public $salesman_fname;
    public $salesman_lname;
    public $salesman_phone;
    public $user_fname;
    public $user_lname;
    public $insurance;
    public $insurance_id;
    public $claim;
    public $insurance_approval;
    public $date_of_loss;
    public $adjuster_name;
    public $adjuster_email;
    public $adjuster_phone;
    public $timestamp;
    public $sold;
    public $job_type;
    public $job_type_note;
    public $po_number;
    public $jurisdiction_id;
    public $jurisdiction;
    public $jurisdiction_url;
    public $midroof;
    public $midroofLadder;
    public $permit_length;
    public $permit;
    public $permit_expire;
    public $user_id;
	public $materials_total = 0;
    public $dob;
    public $stage_age;
    public $duration;
    public $invoice_id;
    public $orgin_id;
    public $origin;
    public $repairs = array();
    public $referral_id;
    public $referral_fname;
    public $referral_lname;
    public $referral_paid;
	public $uploads_array;
    public $num_uploads;
	public $generated_uploads_array;
    public $num_generated_uploads;
	public $journals_array;
    public $num_journals;
    public $pif_date;
    public $hash;
    public $profit_sheet_id;
    public $status_hold_id;
    public $status_id;
	public $status_hold_timestamp;
    public $status_hold;
    public $status_hold_color;
    public $status_hold_expires;
    public $job_type_id;
    public $canvasser_id=0;
	public $meta_data = array();
    private $account_id;
    
    protected function construct($id, $dieIfNotFound = TRUE) {
       
        RequestUtil::set('ignore_cache', 1);                
        $record = DBUtil::getRecord('jobs', $id);        
        
        if (!$record) {
            if($dieIfNotFound) {
                die('Job not found');
            } else {
                return FALSE;
            }
        }
        $this->record = $record;
        
        //assign data
        list($this->job_id, $this->job_number, $this->customer_id, $this->account_id, $this->stage_num, $this->stage_age, $this->user_id, $this->salesman_id, $this->referral_id, $this->referral_paid, $this->insurance_id, $this->claim, $this->insurance_approval,$this->date_of_loss,$this->adjuster_name,$this->adjuster_email,$this->adjuster_phone, $this->sold, $this->job_type_id, $this->job_type_note,$this->po_number, $this->origin_id, $this->jurisdiction_id, $this->pif_date, $this->timestamp, $this->hash) = array_values($record);
        $this->build($record);

        //date of birth
        $this->dob = DateUtil::formatDate($this->timestamp);
        
        //get a whole bunch of related data
        $this->setUserNames();
        $this->setReferralNames();
        $this->setStageNames();
        $this->setInsurance();
        $this->setSalesman();
        $this->setJobType();
        $this->setJurisdiction();
        $this->setPermit();
        $this->setProfitSheet();
        $this->setInvoice();
        $this->setMidRoof();
        $this->getDuration();
        $this->setOrigin();
        $this->setUploads();
        $this->setGeneratedUploads();
        $this->setJournals();
        $this->getStatusHold();
		$this->getMetaData();
		$this->getCanvasser();
        
        return TRUE;
    }

    public function getDuration() {
        $sql = "select duration from stages where stage_num='" . $this->stage_num . "' and account_id='" . $_SESSION['ao_accountid'] . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->duration) = mysqli_fetch_row($res);
    }

    public function setMidRoof() {
        $sql = "SELECT midroof_timing, ladder
                FROM jurisdiction
                WHERE jurisdiction_id = '$this->jurisdiction_id'
                LIMIT 1";
        $results = DBUtil::query($sql);
        list($this->midroof, $this->midroofLadder) = mysqli_fetch_row($results);
    }

    public function setOrigin() {
        $sql = "select origin from origins where origin_id='" . $this->origin_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->origin) = mysqli_fetch_row($res);
    }

    public function setPermit() {
        $sql = "select number, timestamp from permits where job_id='" . $this->job_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->permit, $timestamp) = mysqli_fetch_row($res);

        if($this->permit != '')
            $this->permit_expire = date("m-d-Y", strtotime($timestamp . " +" . $this->permit_length . " day"));
    }

    public function setJurisdiction() {
        $sql = "select location, permit_days, permit_url from jurisdiction where jurisdiction_id='" . $this->jurisdiction_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->jurisdiction, $this->permit_length, $this->jurisdiction_url) = mysqli_fetch_row($res);
    }

    public function setUserNames() {
        $sql = "select fname, lname from users where user_id='" . $this->user_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->user_fname, $this->user_lname) = mysqli_fetch_row($res);
    }

    public function setReferralNames() {
        $sql = "select fname, lname from users where user_id='" . $this->referral_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->referral_fname, $this->referral_lname) = mysqli_fetch_row($res);
    }

    public function setStageNames() {
        $sql = "select stage_id, stage, description from stages where stage_num='" . $this->stage_num . "' and account_id={$_SESSION['ao_accountid']}";
        $res = DBUtil::query($sql);

        $i = 0;
        while (list($stage_id, $temp_stage, $stage_desc) = mysqli_fetch_row($res)) {
            $this->stages[$i][0] = $temp_stage;
            $this->stages[$i][1] = $stage_desc;
            $this->stages[$i][2] = $stage_id;
            $i++;
        }
    }

    public function setInsurance() {
        $sql = "select insurance from insurance where insurance_id='" . $this->insurance_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->insurance) = mysqli_fetch_row($res);
    }

    public function setSalesman() {
        $sql = "select fname, lname, phone from users where user_id='" . $this->salesman_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->salesman_fname, $this->salesman_lname, $this->salesman_phone) = mysqli_fetch_row($res);
    }

    public function setJobType() {
        $sql = "select job_type from job_type where job_type_id='" . $this->job_type_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->job_type) = mysqli_fetch_row($res);
    }

    public function setProfitSheet() {
        $sql = "select profit_sheet_id from profit_sheets where job_id='" . $this->job_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->profit_sheet_id) = mysqli_fetch_row($res);
    }

    public function setInvoice() {
        $sql = "select invoice_id from invoices where job_id='" . $this->job_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->invoice_id) = mysqli_fetch_row($res);
    }

    public function getCSVStages() {
        $stages_str = '';
        for ($i = 0; $i < count($this->stages); $i++) {
            $stage_name = $this->stages[$i][0];
            $desc = $this->stages[$i][1];
            $stage_id = $this->stages[$i][2];

            $tmp_str = <<<HTML
                <span>$stage_name</span>
                <div id="stage$stage_id" style="display:none; position:absolute; font-size: 11px; border: 2px solid #CCCCCC; background-color: #F5F5F5; padding: 5px; width: 250px;">
                    $desc
                </div>
HTML;
            $stages_str .= $tmp_str;
            $stages_str .=", ";
        }
        return substr($stages_str, 0, -2);
    }

    public function getCSVJournalStages($stage_num) {
        $sql = "select stage from stages where stage_num='" . $stage_num . "' and account_id='" . $_SESSION['ao_accountid'] . "'";
        $res = DBUtil::query($sql);

        $i = 0;

        $stages_str = '';
        while (list($stage) = mysqli_fetch_row($res)) {
            $stages_str.=$stage;
            if($i < mysqli_num_rows($res) - 1)
                $stages_str.=", ";
        }

        return $stages_str;
    }

    public function getJournalTask($journal_id) {
        $sql = "select task_type.task" .
            " from task_type, tasks, journals" .
            " where journals.journal_id='" . $journal_id . "' and journals.task_id=tasks.task_id and tasks.task_type=task_type.task_type_id" .
            " limit 1";
        $res = DBUtil::query($sql);

        list($task) = mysqli_fetch_row($res);

        return $task;
    }

    public function getLastAppointment() {
        $sql = "select title, text from appointments where job_id = '" . $this->job_id . "' order by appointment_id desc limit 1";
        $res = DBUtil::query($sql);

        if(mysqli_num_rows($res) != 0) {
            list($appointment_title, $appointment_text) = mysqli_fetch_row($res);
            return $appointment_title . ': ' . $appointment_text;
        }
        return null;
    }

    public function getLastJournalMessage() {
        $sql = "select text from journals where job_id = '" . $this->job_id . "' order by journal_id desc limit 1";
        $res = DBUtil::query($sql);

        if(mysqli_num_rows($res) != 0) {
            list($journal_message) = mysqli_fetch_row($res);
            return $journal_message;
        }
        return null;
    }

    public function getAgeDays() {
        $diff = time() - strtotime($this->timestamp);
        $days = round($diff / (60 * 60 * 24));
        return $days;
    }

    public function getStageAge() {
        $diff = time() - strtotime($this->stage_age);
        $days = floor($diff / (60 * 60 * 24));

        return $days;
    }

    public function getNextStages() {
        $sql = "select order_num from stages where stage_num='$this->stage_num' and account_id='" . $_SESSION['ao_accountid'] . "'";
        $res = DBUtil::query($sql);
        $order_num = 0;
        while ($stage = mysqli_fetch_row($res)) {
            $order_num = $stage[0];
        }
        
        $sql = "select stage from stages where order_num='" . ($order_num+1) . "' and account_id='" . $_SESSION['ao_accountid'] . "'";
        $res = DBUtil::query($sql);

        $i = 0;

        $stages_str = '';
        while (list($stage) = mysqli_fetch_row($res)) {
            $stages_str.=$stage;

            if($i < mysqli_num_rows($res) - 1)
                $stages_str.=", ";
            $i++;
        }

        return $stages_str . $alt_stages_str;
    }
    
    public function fetchStageRequirements($stageNumber = NULL, $keyByLabel = FALSE, $getAll = FALSE) {
        $extraSql = '';
        if(!$getAll) {
            $stageNumber = $stageNumber ?: $this->stage_num + 1;
            $extraSql .= "AND s.stage_num = '$stageNumber'";
        }
        

        $sql = "SELECT sr.query, sr.label, sr.hook, sr.script, sr.special_instructions, sr.description
				FROM stage_reqs sr, stages s, stage_reqs_link srl
				WHERE s.stage_id = srl.stage_id
                    AND srl.stage_req_id = sr.stage_req_id 
                    AND s.account_id = '{$_SESSION['ao_accountid']}'
                    AND srl.account_id = '{$_SESSION['ao_accountid']}'
                    $extraSql
				GROUP BY sr.stage_req_id";
        return DBUtil::queryToArray($sql, $keyByLabel ? 'label' : NULL);
    }
    
    public function fetchUnfinishedRequirements($stageNumber = NULL) {
        if($this->unfinishedRequirements) {
            return $this->unfinishedRequirements;
        }
        
        $stageRequirements = $this->fetchStageRequirements($stageNumber);
        
        $this->unfinishedRequirements = array();
        foreach($stageRequirements as $stageRequirement) {
            if(!empty($stageRequirement['script']) && !$this->checkRequirement($stageRequirement['query'], $stageRequirement['special_instructions'])) {
                $this->unfinishedRequirements[] = $stageRequirement;
            }
        }
        
        return $this->unfinishedRequirements;
    }
    
    /**
     * 
     * @param int $stageNumber
     * @return array
     */
    public function fetchUnscheduledTasks($stageNumber = NULL) {
        if(!AccountModel::getMetaValue('require_task_stage')) {
            return array();
        }
        if($this->unscheduledTasks) {
            return $this->unscheduledTasks;
        }
        
        $stageNumber = $stageNumber ?: $this->stage_num + 1;

        $sql = "SELECT t.task_id, tt.task, tt.color
                FROM stages s, tasks t, task_type tt
                WHERE s.stage_num = '$stageNumber'
                    AND s.account_id = '{$_SESSION['ao_accountid']}'
                    AND t.stage_id = s.stage_id
                    AND tt.task_type_id = t.task_type
                    AND t.start_date IS NULL
                    AND t.account_id = '{$_SESSION['ao_accountid']}'
                    AND t.job_id = '{$this->job_id}'
                ORDER BY tt.task ASC";
        
        $this->unscheduledTasks = DBUtil::queryToArray($sql);
        return $this->unscheduledTasks;
    }
    
    
    public function fetchStageDocuments($stageNumber = NULL) {
        if($this->stageDocuments) {
            return $this->stageDocuments;
        }
        
        $stageNumber = $stageNumber ?: $this->stage_num;
        
        $ownership = moduleOwnership('view_documents') ? "AND user_id = '{$_SESSION['ao_userid']}'" : '';
        $sql = "SELECT document_id, document, filename, filetype
                FROM documents
                WHERE account_id = '{$_SESSION['ao_accountid']}'
                    AND stage_num = '$stageNumber'
                    $ownership
                ORDER BY document ASC";
        
        $this->stageDocuments = DBUtil::queryToArray($sql);
        return $this->stageDocuments;
    }

	public function fetchMaterialSheets() {
        if($this->materialSheets) {
            return $this->materialSheets;
        }
        
		$sql = "SELECT *
                FROM sheets
                WHERE job_id = '{$this->job_id}'";
        
        $this->materialSheets = DBUtil::queryToArray($sql);
        return $this->materialSheets;
	}

	public function fetchCharges($invoice_id='') {
        if($this->charges) {
            return $this->charges;
        }
        
		$sql = "SELECT c.*
                FROM charges c
                JOIN invoices i ON i.invoice_id = c.invoice_id
                WHERE i.job_id = '{$this->job_id}'";
            if(!empty($invoice_id))
            {
                 $sql .= " AND i.invoice_id='{$invoice_id}'";
            }
        $this->charges = DBUtil::queryToArray($sql);
        return $this->charges;
	}

	public function fetchCredits($invoice_id='') {
        if($this->credits) {
            return $this->credits;
        }
        
		$sql = "SELECT c.*
                FROM credits c
                JOIN invoices i ON i.invoice_id = c.invoice_id
                WHERE i.job_id = '{$this->job_id}'";
            if(!empty($invoice_id))
            {
                 $sql .= " AND i.invoice_id='{$invoice_id}'";
            }
        
        $this->credits = DBUtil::queryToArray($sql);
        return $this->credits;
	}

	public function fetchRepairs() {
        if($this->repairs) {
            return $this->repairs;
        }
        
        $sql = "SELECT r.repair_id, ft.fail_type, r.startdate, r.completed
                FROM repairs r, fail_types ft 
                WHERE r.job_id = '{$this->job_id}'
                    AND r.fail_type = ft.fail_type_id
                ORDER BY r.timestamp DESC";
        
        $this->repairs = DBUtil::queryToArray($sql);
        return $this->repairs;
	}

	public function fetchAppointments() {
        if($this->appointments) {
            return $this->appointments;
        }
        
        $sql = "SELECT appointment_id, title, datetime
                FROM appointments
                WHERE job_id = '{$this->job_id}'
                ORDER BY timestamp DESC";
        
        $this->appointments = DBUtil::queryToArray($sql);
        return $this->appointments;
	}

	public function fetchTasks() {
        if($this->tasks) {
            return $this->tasks;
        }
        $sql = "SELECT t.task_id, tt.task, t.start_date, t.duration, t.completed, tt.color, t.paid
                FROM tasks t, task_type tt
                WHERE t.task_type = tt.task_type_id
                    AND t.job_id = '{$this->job_id}'";
        
        $this->tasks = DBUtil::queryToArray($sql);
        return $this->tasks;
	}

	public function fetchCustomer() {
        if($this->customer) {
            return $this->customer;
        }
        
        $this->customer = new Customer($this->customer_id);
        return $this->customer;
	}
    
    public function getJobItemsList() {
        $output = '';
        
        //material sheets
        $viewData = array('myJob' => $this);
        $output .= ViewUtil::loadView('material-sheets', $viewData);
        
        //repairs
        $output .= ViewUtil::loadView('repairs', $viewData);
        
        //appointments
        $output .= ViewUtil::loadView('appointments', $viewData);
        
        //tasks
        $output .= ViewUtil::loadView('tasks', $viewData);

//        //warranty
//        $output .= ViewUtil::loadView('material-sheets', $viewData);
        
        return $output;
    }
    
    public function getNextStageReqs() {
        $output = '';
        
        //requirements
        $viewData = array('myJob' => $this, 'requirements' => $this->fetchStageRequirements());
        $output .= ViewUtil::loadView('requirements', $viewData);
        
        //unscheduled tasks
        $viewData = array('unscheduledTasks' => $this->fetchUnscheduledTasks());
        $output .= ViewUtil::loadView('unscheduled-tasks', $viewData);
        
        //stage documents
        $viewData = array('stageDocuments' => $this->fetchStageDocuments());
        $output .= ViewUtil::loadView('stage-documents', $viewData);

        if(stageAdvanceAccess($this->stage_num) && $this->stage_num != StageModel::getFinalStageNum() && $this->requirementsMet() && ModuleUtil::checkJobModuleAccess('next_job_stage', $this)) {
            $output .= "<tr><td colspan=\"2\">
                                    <a href=\"\" rel=\"next-stage\" data-job-id=\"{$this->job_id}\" class=\"btn btn-block btn-success btn-next-stage\">
                                        Go to Next Stage&nbsp;<i class=\"icon-circle-arrow-right\"></i>
                                    </a>
                                </td></tr>";
        }
        
        return $output;
    }

    public function checkRequirement($query, $special_instructions) {
        if($special_instructions == 'multiple') {
			return $this->checkRequirementMultiple($query);
		}

        eval("\$query = \"$query\";");
        $query = str_replace('::jobId', $this->job_id, $query);
        
        return DBUtil::hasRows(DBUtil::query($query));
    }

	public function checkRequirementMultiple($query) {
		eval("\$query = \"$query\";");
        $query = str_replace('::jobId', $this->job_id, $query);
        
        return !DBUtil::hasRows(DBUtil::query($query));
	}

	public function hasOpenRepairs() {
        $repairs = $this->fetchRepairs();
        foreach($repairs as $repair) {
            if(empty($repair['completed'])) {
                return true;
            }
        }
        
        return false;
	}

	public function setGeneratedUploads() {
		$sql = "SELECT * FROM uploads WHERE job_id='$this->job_id'
				AND title IN('Schedule Roofing Job','Schedule Window Job','Schedule Repair Job','Schedule Gutter Job')";
		$this->generated_uploads_array = DBUtil::queryToArray($sql, 'upload_id');
		$this->num_generated_uploads = count($this->generated_uploads_array);
	}

	public function setUploads() {
		$sql = "SELECT up.*, u.fname, u.lname
				FROM uploads up
				LEFT JOIN users u ON u.user_id = up.user_id
				WHERE job_id='$this->job_id'
                    AND active = 1
				ORDER BY up.timestamp DESC";
		$this->uploads_array = DBUtil::queryToArray($sql, 'upload_id');
		$this->num_uploads = count($this->uploads_array);
	}
    
    public function hasUpload($keyword) {
        $uploads = array_filter($this->uploads_array, function($upload) use ($keyword) {
            return stripos(MapUtil::get($upload, 'title'), $keyword) !== FALSE;
        });
        
        return count($uploads) ? TRUE : FALSE;
    }

	public function setJournals() {
		$sql = "SELECT j.*, u.fname, u.lname
				FROM journals j
				LEFT JOIN users u ON u.user_id = j.user_id
				WHERE job_id='$this->job_id'
				ORDER BY j.timestamp DESC";
		$this->journals_array = DBUtil::queryToArray($sql, 'journal_id');
		$this->num_journals = count($this->journals_array);
	}

    public function getInvoiceBalance($invoice_id='') {
        $charges = $this->getMaterialsCost() + $this->getInvoiceChargesTotal($invoice_id);
        $credits = $this->getInvoiceCreditsTotal($invoice_id);
        return CurrencyUtil::formatUSD($charges - $credits);
    }
    
    public function getInvoiceChargesTotal($invoice_id='') {
        $sql = "SELECT SUM(ch.amount) as charges
                FROM invoices i
                JOIN charges ch ON ch.invoice_id = i.invoice_id
                WHERE i.job_id = '{$this->getMyId()}'";
            if(!empty($invoice_id))
            {
                 $sql .= " AND i.invoice_id='{$invoice_id}'";
            }
        return DBUtil::fetchScalar(DBUtil::query($sql)) ?: 0;
    }
    
    public function getInvoiceCreditsTotal($invoice_id='') {
        $sql = "SELECT SUM(cr.amount) as credits
                FROM invoices i
                JOIN credits cr ON cr.invoice_id = i.invoice_id
                WHERE i.job_id = '{$this->getMyId()}'";
            if(!empty($invoice_id))
            {
                 $sql .= " AND i.invoice_id='{$invoice_id}'";
            }
        return DBUtil::fetchScalar(DBUtil::query($sql)) ?: 0;
        
    }
    
    public function getMaterialsCost() {
        $materialSheets = $this->fetchMaterialSheets();
        if(!count($materialSheets)) { return 0;}
        
        $sheetIds = MapUtil::pluck($materialSheets, 'sheet_id');
        $sheetIds = implode(',', $sheetIds);
        
        $sql = "SELECT sum(si.quantity * m.price)
                FROM sheet_items si
                JOIN materials m ON m.material_id = si.material_id
                WHERE si.sheet_id IN ($sheetIds)";
        
        return DBUtil::fetchScalar(DBUtil::query($sql)) ?: 0;
    }

	public function setMaterialsTotal()
	{
		$sql = "select sum(materials.price*sheet_items.quantity) from sheets, materials, sheet_items where sheets.job_id='" . $this->job_id . "' and sheet_items.sheet_id=sheets.sheet_id and materials.material_id=sheet_items.material_id";
		$res = DBUtil::query($sql);
		list($this->materials_total) = mysqli_fetch_row($res);
	}

    public function getAccountID() {
        return $this->account_id;
    }

    public function getStatusHold() {
        $sql = "SELECT s.status_id, s.status, sh.timestamp, s.color, sh.expires
                FROM status s, status_holds sh
                WHERE sh.job_id = '{$this->job_id}'
                    AND sh.status_id = s.status_id 
                LIMIT 1";
        $res = DBUtil::query($sql);
        if(mysqli_num_rows($res) == 1) {
            list($this->status_hold_id, $this->status_hold, $this->status_hold_timestamp, $this->status_hold_color, $this->status_hold_expires) = mysqli_fetch_row($res);
        }
    }

    public function getLastAction() {
        $sql = "select action from history where job_id='" . $this->job_id . "' order by timestamp desc limit 1";
        $res = DBUtil::query($sql) or die(mysqli_error);
        list($last_action) = mysqli_fetch_row($res);

        return $last_action;
    }

    public function getSubscribers()
    {
        $sql = "SELECT s.subscriber_id, s.user_id, u.fname, u.lname
				FROM subscribers s, users u
				WHERE s.job_id = '{$this->job_id}'
                    AND s.user_id = u.user_id
				ORDER BY u.lname ASC";
        return DBUtil::queryToArray($sql, 'user_id');
    }

	public function getExtendedWarranty() {
		return JobModel::getMetaValue('job-' . $this->job_id, 'extended_warranty');
	}

	public function getMetaData()
	{
		$this->meta_data = JobModel::getAllMeta($this->job_id);
	}

	public function getCanvasser() {
        $canvasserId = MapUtil::get(DBUtil::getRecord('canvassers', $this->getMyId(), 'job_id'), 'user_id');
        $this->canvasser_id = $canvasserId;
        $this->set('canvasser_id', $canvasserId);
    }
    
    /**
     * 
     * @return boolean
     */
    public function requirementsMet() {
        $total = count($this->fetchUnfinishedRequirements()) + count($this->fetchUnscheduledTasks());
        return $total === 0 ? TRUE : FALSE;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasReachedFinalStage() {
        return $this->stage_num === StageModel::getFinalStageNum();
    }
    
    /**
     * @return string
     */
    public function getStageClass() {
        return StageModel::getStageClass($this->stage_num);
    }
    
    /**
     * 
     * @return string
     */
    public function getCSVJobTasks() {
        return MapUtil::join($this->fetchTasks(), 'task');
    }
    
    /**
     * 
     * @return string
     */
    public function getCSVRepairs() {
        return MapUtil::join($this->fetchRepairs(), 'fail_type');
    }
    
    /**
     * 
     * @return strings
     */
    public function getLatestJournalForTooltip() {
        if(!count($this->journals_array)) { return; }
        
        reset($this->journals_array);
        $journal = current($this->journals_array);
        
        return "({$journal['lname']}, {$journal['fname']}) {$journal['text']}";
    }
    
    /**
     * 
     */
    public function storeSnapshot() {
        $tableData = DBUtil::getTableFields('jobs');
        
        $fields = array();
        $values = array();
        foreach($tableData as $field) {
            $fieldName = MapUtil::get($field, 'Field');
            $nullable = MapUtil::get($field, 'Null') === 'YES';
            $fields[] = $fieldName;
            $value = MapUtil::get($this->record, $fieldName);
            if(empty($value) && $nullable) {
                $values[] = 'NULL';
            } else {
                $values[] = "'$value'";
            }
        }
        
        //add extra stuff...
        $fields[] = 'canvasser';
        if(empty($this->canvasser_id))
            $this->canvasser_id=0;

        $values[] = "{$this->canvasser_id}";
        // echo "<pre>";print_r($fields);
        // echo "<pre>";print_r($values);die;
        //build sql
        $sql = "INSERT INTO job_audits (" . implode(',', $fields) . ", audit_user_id, audit_timestamp)
                VALUES (" . implode(",", $values) . ", '{$_SESSION['ao_userid']}', NOW())";
        //echo $sql;die;
        DBUtil::query($sql);
    }
    
    /**
     * 
     * @param boolean $render
     * @return array|string
     */
    public function getHistory($render = TRUE) {
        $method = $render ? 'render' : 'generate';
        $model = new JobAuditModel($this->getMyId());
        return $model->$method();
    }
    
    /**
     * 
     * @param string $searchBy
     * @return boolean
     */
    public function hasCredit($searchBy) {
        $sql = "SELECT c.*
                FROM credits c
                JOIN invoices i ON i.invoice_id = c.invoice_id
                WHERE c.note LIKE '%$searchBy%'
                    AND i.job_id = '{$this->getMyId()}'
                LIMIT 1";
        return DBUtil::hasRows(DBUtil::query($sql));
    }
    
    /**
     * 
     */
    public function getTooltip() {
        $customer = $this->fetchCustomer();
        $salesman = !empty($this->salesman_lname) ? "{$this->salesman_lname}, {$this->salesman_fname}" : '';
        $info = array(
            'Job Number' => $this->job_number,
            'Customer' => $customer->getDisplayName(),
            'Address' => $customer->getfullAddress(),
            'Jurisdiction' => $this->jurisdiction . (!empty($this->midroof) ? " - Midroof {$this->midroof}" : ''),
            'Salesman' => $salesman,
            'Balance' => '$' . $this->getInvoiceBalance(),
            'Stage' => JobUtil::getCSVStages($this->job_id),
            'Days @ Stage' => $this->getStageAge(),
            'Repairs' => $this->getCSVRepairs(),
            'Tasks' => $this->getCSVJobTasks(),
            'Type' => $this->job_type,
            'Age' => $this->getAgeDays(),
            'Last Journal' => UIUtil::cleanOutput($this->getLatestJournalForTooltip(), FALSE)
        );
            
        echo ViewUtil::loadView('tooltip', array('info' => $info));
    }
    
    /**
     * 
     * @param int $userId
     * @return boolean
     */
    public function shouldBeWatching($userId = NULL) {
        $userId = $userId ?: $_SESSION['ao_userid'];
        
        if($userId == $this->user_id || $userId == $this->salesman_id) {
            return TRUE;
        }
        
        //check tasks
        $tasks = DBUtil::getRecords('tasks', $this->job_id, 'job_id');
        $taskUserIds = array_merge(MapUtil::pluck($tasks, 'user_id'), MapUtil::pluck($tasks, 'contractor'));
        if(in_array($userId, $taskUserIds)) { return TRUE; }
        
        //check repairs
        $repairs = DBUtil::getRecords('repairs', $this->job_id, 'job_id');
        $repairUserIds = array_merge(MapUtil::pluck($repairs, 'user_id'), MapUtil::pluck($repairs, 'contractor'));
        if(in_array($userId, $repairUserIds)) { return TRUE; }
        
        //check appointments
        $materialSheets = DBUtil::getRecords('sheets', $this->job_id, 'job_id');
        if(in_array($userId, MapUtil::pluck($materialSheets, 'user_id'))) { return TRUE; }
        
        return FALSE;
    }
}

?>
