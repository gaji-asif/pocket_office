<?php
if(!empty($repairs) && is_array($repairs)) {
?>
<h3>Repairs</h3>
<?php
    foreach($repairs as $repair) {
?>
    <div>
        <span style="color: red; font-weight: bold;"><?=MapUtil::get($repair, 'fail_type')?></span>
        (<a href="<?=ROOT_DIR?>/?p=jobs&id=<?=MapUtil::get($repair, 'job_id')?>"><?=MapUtil::get($repair, 'job_number')?></a>)
    </div>
    <div>
        <?=MapUtil::get($repair, 'customer_fname')?> <?=MapUtil::get($repair, 'customer_lname')?>
        <?=MapUtil::get($repair, 'address')?>,
        <?=MapUtil::get($repair, 'city')?>
    </div>
<?php
        if(MapUtil::get($repair, 'salesman_lname')) {
?>
    <div>
        Salesman:
        <?=MapUtil::get($repair, 'salesman_lname')?>, <?=MapUtil::get($repair, 'salesman_fname')?>
    </div>
<?php
        }
        if(MapUtil::get($repair, 'contractor_lname')) {
?>
    <div>
        Contractor:
        <?=MapUtil::get($repair, 'dba') ? stripslashes(MapUtil::get($task, 'dba')) : MapUtil::get($repair, 'contractor_lname') . ', ' . MapUtil::get($repair, 'contractor_fname')?>
    </div>
<?php
        }
?>
<br />
<?php
    }
}

if(!empty($tasks) && is_array($tasks)) {
?>
<h3>Tasks</h3>
<?php
    foreach($tasks as $task) {
?>
    <div>
        <span style="font-weight: bold;"><?=MapUtil::get($task, 'task')?></span>
        (<a href="<?=ROOT_DIR?>/?p=jobs&id=<?=MapUtil::get($task, 'job_id')?>"><?=MapUtil::get($task, 'job_number')?></a>)
    </div>
    <div>
        <?=MapUtil::get($task, 'customer_fname')?> <?=MapUtil::get($task, 'customer_lname')?> - 
        <?=MapUtil::get($task, 'address')?>,
        <?=MapUtil::get($task, 'city')?>
    </div>
<?php
        if(MapUtil::get($task, 'salesman_lname')) {
?>
    <div>
        Salesman:
        <?=MapUtil::get($task, 'salesman_lname')?>, <?=MapUtil::get($task, 'salesman_fname')?>
    </div>
<?php
        }
        if(MapUtil::get($task, 'contractor_lname')) {
?>
    <div>
        Contractor:
        <?=MapUtil::get($task, 'dba') ? stripslashes(MapUtil::get($task, 'dba')) : MapUtil::get($task, 'contractor_lname') . ', ' . MapUtil::get($task, 'contractor_fname')?>
    </div>
<?php
        }
?>
<br />
<?php
    }
}

if(!empty($events) && is_array($events)) {
?>
<h3>Events</h3>
<?php
    foreach($events as $event) {
?>
    <div>
        <?=MapUtil::get($event, 'all_day') ? 'All day' : DateUtil::formatTime(MapUtil::get($event, 'date'))?>
    </div>
    <div>
        <?=MapUtil::get($event, 'title')?>
    </div><br />
<?php
    }
}

if(!empty($appointments) && is_array($appointments)) {
?>
<h3>Appointments</h3>
<?php
    foreach($appointments as $appointment) {
?>
    <div>
        <?=DateUtil::formatTime(MapUtil::get($appointment, 'datetime'))?>
        (<a href="<?=ROOT_DIR?>/?p=jobs&id=<?=MapUtil::get($appointment, 'job_id')?>"><?=MapUtil::get($appointment, 'job_number')?></a>)
    </div>
    <div>
        <?=MapUtil::get($appointment, 'customer_fname')?> <?=MapUtil::get($appointment, 'customer_lname')?> - 
        <?=MapUtil::get($appointment, 'address')?>,
        <?=MapUtil::get($appointment, 'city')?>
    </div>
<?php
        if(MapUtil::get($appointment, 'salesman_lname')) {
?>
    <div>
        Salesman:
        <?=MapUtil::get($appointment, 'salesman_lname')?>, <?=MapUtil::get($appointment, 'salesman_fname')?>
    </div>
<?php
        }
?>
<br />
<?php
    }
}

if(!empty($deliveries) && is_array($deliveries)) {
?>
<h3>Material Deliveries</h3>
<?php
    foreach($deliveries as $delivery) {
?>
    <div>
        <?=MapUtil::get($delivery, 'label')?>
        (<a href="<?=ROOT_DIR?>/?p=jobs&id=<?=MapUtil::get($delivery, 'job_id')?>"><?=MapUtil::get($delivery, 'job_number')?></a>)
    </div>
    <div>
        <?=MapUtil::get($delivery, 'customer_fname')?> <?=MapUtil::get($delivery, 'customer_lname')?> - 
        <?=MapUtil::get($delivery, 'address')?>,
        <?=MapUtil::get($delivery, 'city')?>
    </div>
<?php
        if(MapUtil::get($delivery, 'salesman_lname')) {
?>
    <div>
        Salesman:
        <?=MapUtil::get($delivery, 'salesman_lname')?>, <?=MapUtil::get($delivery, 'salesman_fname')?>
    </div>
<?php
        }
?>
<br />
<?php
    }
}

if(!empty($expiringHolds) && is_array($expiringHolds)) {
?>
<h3>Expiring Status Holds</h3>
<?php
    foreach($expiringHolds as $expiringHold) {
?>
    <div>
        <?=MapUtil::get($expiringHold, 'status')?>
        (<a href="<?=ROOT_DIR?>/?p=jobs&id=<?=MapUtil::get($expiringHold, 'job_id')?>"><?=MapUtil::get($expiringHold, 'job_number')?></a>)
    </div>
    <div>
        <?=MapUtil::get($expiringHold, 'customer_fname')?> <?=MapUtil::get($expiringHold, 'customer_lname')?> - 
        <?=MapUtil::get($expiringHold, 'address')?>,
        <?=MapUtil::get($expiringHold, 'city')?>
    </div>
<?php
        if(MapUtil::get($expiringHold, 'salesman_lname')) {
?>
    <div>
        Salesman:
        <?=MapUtil::get($expiringHold, 'salesman_lname')?>, <?=MapUtil::get($expiringHold, 'salesman_fname')?>
    </div>
<?php
        }
?>
<br />
<?php
    }
}
?>