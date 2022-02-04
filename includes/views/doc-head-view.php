<?php

$css = array(

    ROOT_DIR.'/css/style.css',

    '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css',

    ROOT_DIR.'/css/vendor/multi-select.css',

    ROOT_DIR.'/css/vendor/responsive-gs-12col.css',

    ROOT_DIR.'/css/vendor/pikaday.css',

    ROOT_DIR.'/css/vendor/jquery.atwho.css',
    
    ROOT_DIR.'/css/vendor/jquery.fancybox.min.css'

);

if(@$scan_css) {

    array_push($css, '/xactbid/includes/ajax/scan/scan_styles.css');

}



$js = array(

    ROOT_DIR.'/includes/js/libs/extend-prototype.js',

    ROOT_DIR.'/includes/js/vendor/modernizr.js',

    '//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',

    ROOT_DIR.'/includes/js/vendor/lodash.min.js',

    ROOT_DIR.'/includes/js/vendor/jquery.easing.1.3.js',

    ROOT_DIR.'/includes/js/libs/extend-jquery.js',

    ROOT_DIR.'/includes/js/libs/request.js',

    ROOT_DIR.'/includes/js/app.js',

    ROOT_DIR.'/includes/js/libs/data.js',

    ROOT_DIR.'/includes/js/libs/dropzone.js',

    ROOT_DIR.'/includes/js/libs/ui.js',

    ROOT_DIR.'/includes/js/libs/bindings.js',

    ROOT_DIR.'/includes/js/libs/chat.js',

    // ROOT_DIR.'/includes/js/libs/functions.js',

    ROOT_DIR.'/includes/js/libs/jobs.js',

    ROOT_DIR.'/includes/js/libs/journals.js',

    ROOT_DIR.'/includes/js/libs/tooltips.js',

    ROOT_DIR.'/includes/js/libs/search.js',

    ROOT_DIR.'/includes/js/libs/jquery.tss-multiselect-search.js',

    ROOT_DIR.'/includes/js/libs/jquery.tss-select-search.js',

    ROOT_DIR.'/includes/js/vendor/calendarDateInput.js',

    ROOT_DIR.'/includes/js/vendor/jscolor.js',

    ROOT_DIR.'/includes/js/vendor/calendar_us.js',

    ROOT_DIR.'/includes/js/vendor/jquery.lightbox_me.js',

    ROOT_DIR.'/includes/js/vendor/noty/jquery.noty.js',

    ROOT_DIR.'/includes/js/vendor/noty/layouts/top.js',

    ROOT_DIR.'/includes/js/vendor/noty/layouts/topRight.js',

    ROOT_DIR.'/includes/js/vendor/noty/themes/default.js',

    ROOT_DIR.'/includes/js/vendor/jhere.min.js',

    ROOT_DIR.'/includes/js/vendor/jquery.scrollTo.min.js',

//    ROOT_DIR.'/includes/js/vendor/jquery.maskedinput.min.js',

    ROOT_DIR.'/includes/js/vendor/jquery.inputmask.bundle.js',

    ROOT_DIR.'/includes/js/vendor/jquery.ba-throttle-debounce.min.js',

    ROOT_DIR.'/includes/js/vendor/jquery.multi-select.js',

    ROOT_DIR.'/includes/js/vendor/moment.min.js',

    ROOT_DIR.'/includes/js/vendor/handlebars.js',

    ROOT_DIR.'/includes/js/vendor/handlebars.extend.js',

    ROOT_DIR.'/includes/js/vendor/handlebars.helpers.js',

    ROOT_DIR.'/includes/js/vendor/jquery.backstretch.min.js',

    ROOT_DIR.'/includes/js/vendor/jquery.growl.js',

    ROOT_DIR.'/includes/js/vendor/uri.js',

    ROOT_DIR.'/includes/js/vendor/pikaday.js',

    ROOT_DIR.'/includes/js/vendor/pikaday.jquery.js',

    ROOT_DIR.'/includes/js/vendor/inflection.js',

    ROOT_DIR.'/includes/js/vendor/jquery.caret.min.js',

    ROOT_DIR.'/includes/js/vendor/jquery.atwho.js',

    ROOT_DIR.'/includes/js/vendor/jquery.fastLiveFilter.js',
    ROOT_DIR.'/includes/js/vendor/tinymce/js/tinymce/tinymce.min.js',

//    '/includes/js/vendor/bootstrap.min.js',

//    '/includes/js/vendor/chosen.jquery.min.js',

//    '/includes/js/vendor/stupidtable.js',

//    '/includes/js/vendor/jquery.hotkeys.js',

ROOT_DIR.'/includes/js/vendor/jquery.fancybox.min.js',



);

?>

<html>

	<head>

		<title><?=APP_NAME?></title>

        <meta name="robots" content="noindex,nofollow" />

        <link rel="icon" type="image/png" href="images/favicon.png">
		

<?php

//load css

foreach($css as $file) {

    $path = StrUtil::startsWith($file, '//') ? $file : FileUtil::version($file);

?>

        <link rel="stylesheet" href="<?=$path?>" />

<?php

}
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<?php


//load js

foreach($js as $file) {

    $path = StrUtil::startsWith($file, '//') ? $file : FileUtil::version($file);

?>

        <script src="<?=$path?>"></script>

<?php

}

?>

        

        <script>

            //set system globals

            GLOBALS.server_name = '<?=$_SERVER['SERVER_NAME']?>';

            GLOBALS.ajax_dir = '<?=AJAX_DIR?>';

            GLOBALS.ao_refresh = '<?=($_SESSION['ao_refresh'] * 1000)?>';

            GLOBALS.ao_userid = '<?=$_SESSION['ao_userid']?>';
            
            GLOBALS.loading_no_counter = 0;
        </script>

	</head>

	<body class="<?=@$body_class?>">

		<!--<div id="full-screen-loading-indicator"><div></div></div>-->