<?php 
include 'includes/common_lib.php';
$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));
pageSecure($this_page->source);
if(@$_GET['action'] == 'toggle_inactive_users') 
{
    if($_SESSION['ao_show_inactive_users_in_jobs_list'] === true) 
    {
        $_SESSION['ao_show_inactive_users_in_jobs_list'] = false;
    }
    else {
        $_SESSION['ao_show_inactive_users_in_jobs_list'] = true;
    }
}
echo ViewUtil::loadView('doc-head');
echo $this_page->getHeader(TRUE);
?>

<?php
 $tokenPath = ROOT_PATH. '/mail-react-app/api/auth-token/' . $_SESSION['ao_userid'] . '-token.json';
if (!file_exists($tokenPath)) 
{
?>
    <meta name="google-signin-client_id" content="101848984544-8nvh0fejqj7oell8rtin9s0aq2inp41a.apps.googleusercontent.com">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <a href="javascript:void(0);" onclick="login()" style="margin-top: 20px; position: absolute">Please Authorize</a>
    
<?php
} 
else 
{
?>

    <?php
    if(ModuleUtil::checkAccess('view_jobs')) 
    {
        echo ViewUtil::loadView('email-filters');
    ?>
        <table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
            <?pho // echo ViewUtil::loadView('email-header'); ?>
            <tr>
                <td id="emailscontainer"></td>
            </tr>
            <tr>
                <td colspan=2>
                    <div id="btm_spacer" style="display:none;">
                        <table border=0>
                            <tr height=200>
                            <td>&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>



        <script type="text/javascript">
        $(document).ready(function()
        {
            <?php
            if(!empty($_GET['id'])) 
            {
            ?>
        		Request.make('<?=AJAX_DIR?>/get_notes.php?type=jobs&id=<?php echo $_GET['id']; ?>','notes', false, true);

                <?php
                if(!empty($_GET['tab'])) 
                {
                ?>
        		      Request.make('<?=AJAX_DIR?>/get_email.php?id=<?php echo $_GET['id']; ?>&tab=<?=$_GET['tab']?>', 'emailscontainer', true, true);
                <?php
                } 
                else 
                {
                ?>
        		Request.make('<?=AJAX_DIR?>/get_email.php?id=<?php echo $_GET['id']; ?>', 'emailscontainer', true, true);
                <?php
                }
            } 
            else 
            {
            ?>
        	Request.make('<?=AJAX_DIR?>/get_emaillist.php', 'emailscontainer', true, true);
            <?php
            }
            ?>
        });
        </script>
    <?php
    } 
    else 
    {
        echo ModuleUtil::showInsufficientRightsAlert('view_jobs', TRUE);
    }

} 
?>


<script>
  function login() {
    popupCenter('https://xactbid.pocketofficepro.com/mail-react-app/api/index.php', 'Authorize', 400, 400)
  }

  function popupCenter(url, title, w, h) {
    // Fixes dual-screen position                             Most browsers      Firefox
    var dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
    var dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var systemZoom = width / window.screen.availWidth;
    var left = (width - w) / 2 / systemZoom + dualScreenLeft
    var top = (height - h) / 2 / systemZoom + dualScreenTop
    var newWindow = window.open(url, title,
      `
      scrollbars=yes,
      width=${w / systemZoom}, 
      height=${h / systemZoom}, 
      top=${top}, 
      left=${left}
      `
    )

    if (window.focus) newWindow.focus();
  }
</script>
  </body>

</html>

