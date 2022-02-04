<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head', array('body_class' => 'dashboard'));

//$office = UserUtil::getOffice();
//WeatherUtil::fetchForcast(MapUtil::get($office, 'city'), MapUtil::get($office, 'state'), $days);

if(viewWidget('widget_today')) {
?>
        <div style="float: left; padding: 1px; width: 50%">
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='smcontainertitle'>
            <tr>
              <td><i class="icon-calendar"></i>&nbsp;Today</td>
            </tr>
          </table>
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='infocontainernopadding'>
            <tr>
              <td>
				  <div id="widget-today" class="widget-container"></div>
              <!--<iframe id='widget_today' width='100%' height=95 frameborder=0></iframe>-->
              </td>
            </tr>
          </table>
        </div>
<?php
}
if(1 == 2 && viewWidget('widget_bookmarks')) {
?>
        <div style="float: left; padding: 1px; width: 50%">
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='smcontainertitle'>
            <tr>
              <td><i class="icon-bookmark-empty"></i>&nbsp;Bookmarks</td>
            </tr>
          </table>
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='infocontainernopadding'>
            <tr>
              <td>
				  <div id="widget-bookmarks" class="widget-container"></div>
              <!--<iframe id='widget_bookmarks' width='100%' height=95 frameborder=0></iframe>-->
              </td>
            </tr>
          </table>
        </div>
<?php
}
if(viewWidget('widget_announcements')) {
?>
        <div style="float: left; padding: 1px; width: 50%">
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='smcontainertitle'>
            <tr>
              <td><i class="icon-bullhorn"></i>&nbsp;Recent Announcements</td>
            </tr>
          </table>
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='infocontainernopadding'>
            <tr>
              <td>
				  <div id="widget-announcements" class="widget-container"></div>
              <!--<iframe id='widget_announcements' width='100%' height=95 frameborder=0></iframe>-->
              </td>
            </tr>
          </table>
        </div>
<?php
}
if(viewWidget('widget_documents')) {
?>
        <div style="float: left; padding: 1px; width: 50%">
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='smcontainertitle'>
            <tr>
              <td><i class="icon-file-text-alt"></i>&nbsp;Recent Documents</td>
            </tr>
          </table>
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='infocontainernopadding'>
            <tr>
              <td>
				  <div id="widget-documents" class="widget-container"></div>
              <!--<iframe id='widget_documents' width='100%' height=95 frameborder=0></iframe>-->
              </td>
            </tr>
          </table>
        </div>
<?php
}
if(viewWidget('widget_inbox')) {
?>
        <div style="float: left; padding: 1px; width: 50%">
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='smcontainertitle'>
            <tr>
              <td><i class="icon-envelope"></i>&nbsp;Inbox</td>
            </tr>
          </table>
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='infocontainernopadding'>
            <tr>
              <td>
				  <div id="widget-inbox" class="widget-container"></div>
              <!--<iframe id='widget_inbox' width='100%' height=95 frameborder=0></iframe>-->
              </td>
            </tr>
          </table>
        </div>
<?php
}
if(1 == 2 && viewWidget('widget_journals')) {
?>
        <div style="float: left; padding: 1px; width: 50%">
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='smcontainertitle'>
            <tr>
              <td width=20><img src='<?=IMAGES_DIR?>/icons/clipboard_16.png'></td>
              <td>Recent Journals</td>
            </tr>
          </table>
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='infocontainernopadding'>
            <tr>
              <td>
				  <div id="widget-journals" class="widget-container"></div>
              <!--<iframe id='widget_journals' width='100%' height=95 frameborder=0></iframe>-->
              </td>
            </tr>
          </table>
        </div>
<?php
} if(viewWidget('widget_urgent')) {
?>
        <div style="float: left; padding: 1px; width: 50%">
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='smcontainertitle'>
            <tr>
              <td><i class="icon-briefcase"></i>&nbsp;Urgent Jobs</td>
            </tr>
          </table>
          <table width='100%' border=0 cellpadding=0 cellspacing=0 class='infocontainernopadding'>
            <tr>
              <td>
				  <div id="widget-urgent" class="widget-container"></div>
              <!--<iframe id='widget_urgent' width='100%' height=95 frameborder=0></iframe>-->
              </td>
            </tr>
          </table>
        </div>
<?php
}
?>
    <script type="text/javascript">
		$(document).ready(function(){
			initAllWidgets();
		});

		$(window).load(function(){
			$(this).resize(resizeAllWidgets);
			resizeAllWidgets();
		});

		$(window).on('beforeunload', killAllWidgetIntervals);
    </script>
  </body>
</html>
