<div class="padded">
    <div class="alert alert-danger">
        <strong>Hey!</strong>
        <br /><br />
        You do not have permission to access this.
<?php
if(isset($module) && count($module)) {
?>
        Please ask your system admin for information regarding the module <i>"<?=$module['title']?>"</i>.
<?php
}
if(!$hideCloseLink) {
?>
        <br /><br />
        <a href="" rel="close-me">Click here to close</a>.
<?php
}
?>
    </div>
</div>