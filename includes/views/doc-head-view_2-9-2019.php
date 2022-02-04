<?php

$css = array(

    '/workflow/css/style.css',

    '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css',

    '/workflow/css/vendor/multi-select.css',

    '/workflow/css/vendor/responsive-gs-12col.css',

    '/workflow/css/vendor/pikaday.css',

    '/workflow/css/vendor/jquery.atwho.css',
    
     '/workflow/css/vendor/jquery.fancybox.min.css'

);

if(@$scan_css) {

    array_push($css, '/workflow/includes/ajax/scan/scan_styles.css');

}



$js = array(

    '/workflow/includes/js/libs/extend-prototype.js',

    '/workflow/includes/js/vendor/modernizr.js',

    '//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',

    '/workflow/includes/js/vendor/lodash.min.js',

    '/workflow/includes/js/vendor/jquery.easing.1.3.js',

    '/workflow/includes/js/libs/extend-jquery.js',

    '/workflow/includes/js/libs/request.js',

    '/workflow/includes/js/app.js',

    '/workflow/includes/js/libs/data.js',

    '/workflow/includes/js/libs/dropzone.js',

    '/workflow/includes/js/libs/ui.js',

    '/workflow/includes/js/libs/bindings.js',

    '/workflow/includes/js/libs/chat.js',

    // '/workflow/includes/js/libs/functions.js',

    '/workflow/includes/js/libs/jobs.js',

    '/workflow/includes/js/libs/journals.js',

    '/workflow/includes/js/libs/tooltips.js',

    '/workflow/includes/js/libs/search.js',

    '/workflow/includes/js/libs/jquery.tss-multiselect-search.js',

    '/workflow/includes/js/libs/jquery.tss-select-search.js',

    '/workflow/includes/js/vendor/calendarDateInput.js',

    '/workflow/includes/js/vendor/jscolor.js',

    '/workflow/includes/js/vendor/calendar_us.js',

    '/workflow/includes/js/vendor/jquery.lightbox_me.js',

    '/workflow/includes/js/vendor/noty/jquery.noty.js',

    '/workflow/includes/js/vendor/noty/layouts/top.js',

    '/workflow/includes/js/vendor/noty/layouts/topRight.js',

    '/workflow/includes/js/vendor/noty/themes/default.js',

    '/workflow/includes/js/vendor/jhere.min.js',

    '/workflow/includes/js/vendor/jquery.scrollTo.min.js',

//    '/includes/js/vendor/jquery.maskedinput.min.js',

    '/workflow/includes/js/vendor/jquery.inputmask.bundle.js',

    '/workflow/includes/js/vendor/jquery.ba-throttle-debounce.min.js',

    '/workflow/includes/js/vendor/jquery.multi-select.js',

    '/workflow/includes/js/vendor/moment.min.js',

    '/workflow/includes/js/vendor/handlebars.js',

    '/workflow/includes/js/vendor/handlebars.extend.js',

    '/workflow/includes/js/vendor/handlebars.helpers.js',

    '/workflow/includes/js/vendor/jquery.backstretch.min.js',

    '/workflow/includes/js/vendor/jquery.growl.js',

    '/workflow/includes/js/vendor/uri.js',

    '/workflow/includes/js/vendor/pikaday.js',

    '/workflow/includes/js/vendor/pikaday.jquery.js',

    '/workflow/includes/js/vendor/inflection.js',

    '/workflow/includes/js/vendor/jquery.caret.min.js',

    '/workflow/includes/js/vendor/jquery.atwho.js',

    '/workflow/includes/js/vendor/jquery.fastLiveFilter.js',
    '/workflow/includes/js/vendor/tinymce/js/tinymce/tinymce.min.js',

//    '/includes/js/vendor/bootstrap.min.js',

//    '/includes/js/vendor/chosen.jquery.min.js',

//    '/includes/js/vendor/stupidtable.js',

//    '/includes/js/vendor/jquery.hotkeys.js',

'/workflow/includes/js/vendor/jquery.fancybox.min.js',



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

        </script>

	</head>

	<body class="<?=@$body_class?>">

		<!--<div id="full-screen-loading-indicator"><div></div></div>-->