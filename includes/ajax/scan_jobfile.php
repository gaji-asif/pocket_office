<?php
set_time_limit (0);
include '../common_lib.php';
echo ViewUtil::loadView('doc-head', array('scan_css' => TRUE));
ModuleUtil::checkAccess('upload_job_file', TRUE, TRUE);
$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('upload_job_file', $myJob, TRUE, TRUE);

/*$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, MCRYPT_SECRET_KEY, session_id(), MCRYPT_MODE_ECB);
$crypted_session_id = base64_encode($crypttext);*/

$rand_session_code = mt_rand(0, 999999999);

$sql =      ("INSERT INTO upload_sessions
             (rand_session_code, session_id, user_id, account_id) VALUES
			 ('$rand_session_code', '".mysqli_real_escape_string(DBUtil::Dbcont(),session_id())."', '".intval($_SESSION['ao_userid'])."', '".intval($_SESSION['ao_accountid'])."')");

DBUtil::query($sql);
$rand_session_id = DBUtil::getInsertId();

$crypted_session_id = $rand_session_code.'|'.$rand_session_id;


?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                Scan Files - Job #<?=$myJob->job_number?>
              </td>
              <td align="right">
              <span onmouseover='this.style.cursor="pointer";' onclick='window.close();'>X</span>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="infocontainernopadding">
            <form enctype='multipart/form-data' action='?id=<?=$myJob->getMyId()?>' method="post">

          <div id="container" class="body_Broad_width" style="margin:0 auto;">
  <div id="DWTcontainer" class="body_Broad_width" style="background-color:#ffffff; height:800px; border:0;">

<div id="dwtcontrolContainer">
<div id="dwtcontrol">
<div style="display: inline;" id="maindivPlugin">

<div style="display: none;" id="mainControlNotInstalled">
<table id="maintblcontrolnotinstalled" class="divcontrol">
    <tbody><tr>
    <td style="text-align: center; vertical-align: middle;">
        <a href="/plugins/DynamicWebTWAINPlugIn.exe"><strong>Download and install the Plug-in Here</strong></a><br>
        After the installation, please restart your browser.
    </td>
    </tr>
</tbody></table>
</div>

<div id="mainControlInstalled"> <embed style="display: inline;" id="mainDynamicWebTWAINnotIE" type="Application/DynamicWebTwain-Plugin" onposttransfer="DynamicWebTwain_OnPostTransfer" onpostalltransfers="DynamicWebTwain_OnPostAllTransfers" onmouseclick="DynamicWebTwain_OnMouseClick" onpostload="DynamicWebTwain_OnPostLoadfunction" onimageareaselected="DynamicWebTwain_OnImageAreaSelected" onimageareadeselected="DynamicWebTwain_OnImageAreaDeselected" onmousedoubleclick="DynamicWebTwain_OnMouseDoubleClick" onmouserightclick="DynamicWebTwain_OnMouseRightClick" ontopimageintheviewchanged="DynamicWebTwain_OnTopImageInTheViewChanged" class="divcontrol" pluginspage="DynamicWebTWAIN/DynamicWebTwain.xpi"></div>

</div>

<div style="display: none;" id="maindivIE">
<object classid="clsid:5220cb21-c88d-11cf-b347-00aa00a28331" style="display: none;">
    <param name="LPKPath" value="DynamicWebTWAIN/DynamicWebTwain.lpk">
</object>
<div id="maindivIEx86">
</div>
<div id="maindivIEx64">
</div>
</div>
</div>

<div id="extraInfo" style="font-size: 11px; color: #222222; font-family: verdana sans-serif; background-color:#f0f0f0; text-align:left; width:580px;"></div>

<div class="divinput" style="text-align:center; width:580px; background-color:#FFFFFF;">
    <input id="btnFirstImage" onClick="return btnFirstImage_onclick()" value=" |&lt; " type="button">&nbsp;
    <input id="btnPreImage" onClick="return btnPreImage_onclick()" value=" &lt; " type="button">&nbsp;&nbsp;
    <input size="2" id="CurrentImage" readonly="readonly" type="text">/
    <input size="2" id="TotalImage" readonly="readonly" value="0" type="text">&nbsp;&nbsp;
    <input id="btnNextImage" onClick="return btnNextImage_onclick()" value=" &gt; " type="button">&nbsp;
    <input id="btnLastImage" onClick="return btnLastImage_onclick()" value=" &gt;| " type="button">
    Preview Mode
    <select size="1" id="PreviewMode" onChange="slPreviewMode();">

    <option selected="selected" value="0">1X1</option><option value="1">2X2</option><option value="2">3X3</option><option value="3">4X4</option><option value="4">5X5</option></select><br>
    <input id="btnRemoveCurrentImage" onClick="return btnRemoveCurrentImage_onclick()" value="Remove Selected Images" type="button">
    <input id="btnRemoveAllImages" onClick="return btnRemoveAllImages_onclick()" value="Remove All Images" type="button"><br>
    <div id="divMsg" style="text-align:left;">
        Message:<br>
        <div id="emessage" style="width:550px;height:80px; overflow:scroll; background-color:#ffffff; border:1px #303030; border-style:solid; text-align:left;">
        </div>
    </div>
</div>

</div>

<div id="ScanWrapper">

<div id="divScanner" class="divinput">
<ul>
    <li><img alt="arrow" src="scan/arrow.gif" style="width: 9px; height: 12px;">
        <b>Custom Scan</b></li>
    <li style="padding-left:15px;">
        <label for="source">Select Source<select size="1" id="source">

        <option selected="selected" value="0">Samsung SCX-4x21 Series</option><option value="1">Samsung Universal Scan Driver</option><option value="2">WIA-Samsung Universal Scan Driver</option><option value="3">WIA-Samsung SCX-4x21 Series</option></select></label></li>
    <li style="padding-left:12px;">
        <label for="ShowUI"><input id="ShowUI" type="checkbox">If Show UI&nbsp;</label>
        <label for="ADF"><input checked="checked" id="ADF" type="checkbox">ADF&nbsp;</label>
        <label for="Duplex"><input id="Duplex" type="checkbox">Duplex</label></li>
    <li style="padding-left:12px;">
        <label for="DiscardBlank"><input id="DiscardBlank" type="checkbox">If Discard Blank Images</label></li>
    <li style="padding-left:15px;">Pixel Type
        <label for="BW"><input id="BW" name="PixelType" type="radio">B&amp;W </label>
        <label for="Gray"><input checked="checked" id="Gray" name="PixelType" type="radio">Gray</label>
        <label for="RGB"><input id="RGB" name="PixelType" type="radio">Color</label></li>
    <li style="padding-left:15px;">
        <label for="Resolution">Resolution:<select size="1" id="Resolution"><option selected="selected" value="100">100</option><option value="150">150</option><option value="200">200</option><option value="300">300</option></select></label></li>
    <li style="text-align:center;">
        <input id="btnScan" class="bigbutton" value="Scan" onClick="btnScan_onclick();" type="button"></li>
    <li style="display: block; text-align: center;" id="pNoScanner">
        <a href="javascript:%20void(0)" class="ShowtblLoadImage"><b>What if I don't have a scanner connected?</b>
    </a></li>
</ul>
</div>

<div id="tblLoadImage" style="visibility:hidden;">
<ul>
    <li><b>You can:</b><a href="javascript:%20void(0)" style="text-decoration: none; padding-left: 225px;" class="ClosetblLoadImage">X</a></li>
</ul>
<div style="background-color:#f0f0f0; padding:10px;">
<ul>
    <li><img alt="arrow" src="scan/arrow.gif" height="12" width="9"><b>Install a Virtual Scanner:</b></li>
    <li style="text-align:center;"><a id="32bitsamplesource" href="http://www.dynamsoft.com/demo/DWT5/Sources/twainds.win32.installer.2.1.3.msi">32-bit Sample Source</a>
        <a id="64bitsamplesource" style="display: none;" href="http://www.dynamsoft.com/demo/DWT5/Sources/twainds.win64.installer.2.1.3.msi">64-bit Sample Source</a></li>
</ul>
</div>

<ul>
    <li><b>Or you can:</b></li>
</ul>
<div style="background-color:#f0f0f0; padding:10px;">
<ul>
    <li><img alt="arrow" src="scan/arrow.gif" height="12" width="9"><b>Load a Local Image:</b></li>
    <li style="text-align:center"><input id="btnLoad" class="bigbutton" style="width: 180px;" value="Load Image" onClick="return btnLoad_onclick()" type="button"></li>
    <li>Please <b>NOTE</b> that not all images are supported. For more info, please click
        <a target="_blank" href="http://kb.dynamsoft.com/questions/612/How+to+load+images+not+created+by+Dynamic+Web+TWAIN%3F">here</a>.</li>
</ul>
</div>
</div>

<div id="divUpload" class="divinput">
<ul>
    <li><img alt="arrow" src="scan/arrow.gif" height="12" width="9"><b>Upload Image</b></li>
    <li style="padding-left:9px;">
        <label for="fileTitle">Title: <input value="" size="20" id="fileTitle" type="text"></label></li>

    <li style="padding-left:9px;">
        <label for="txt_fileName">File Name: <input value="WebTWAINImage" size="20" id="txt_fileName" type="text"></label></li>
    <li style="padding-left:9px;">
        <!--<label for="imgTypebmp2">
            <input type="radio" value="bmp" name="ImageType" id="imgTypebmp2" onclick ="rd_onclick();"/>BMP</label>-->
	    <label for="imgTypejpeg2">
		    <input value="jpg" name="ImageType" id="imgTypejpeg2" checked="checked" onClick="rd_onclick();" type="radio">JPEG</label>
	    <label for="imgTypetiff2">
		    <input value="tif" name="ImageType" id="imgTypetiff2" onClick="rdTIFF_onclick();" type="radio">TIFF</label>
	    <label for="imgTypepng2">
		    <input value="png" name="ImageType" id="imgTypepng2" onClick="rd_onclick();" type="radio">PNG</label>
	    <label for="imgTypepdf2">
		    <input value="pdf" name="ImageType" id="imgTypepdf2" onClick="rdPDF_onclick();" type="radio">PDF</label></li>
    <li style="padding-left:9px;">
        <label for="MultiPageTIFF"><input disabled="disabled" id="MultiPageTIFF" type="checkbox">Multi-Page TIFF</label>
        <label for="MultiPagePDF"><input disabled="disabled" id="MultiPagePDF" type="checkbox">Multi-Page PDF </label></li>
    <li style="text-align: center">
        <input id="btnUpload" value="Upload Image" onClick="return btnUpload_onclick(<?=$myJob->job_id?>, '<?=$crypted_session_id?>', '<?=$_SESSION['database_name']?>')" type="button"></li>
</ul>
</div>

<div id="divEdit" class="divinput">
<ul>
    <li><img alt="arrow" src="scan/arrow.gif" height="12" width="9"><b>Edit Image</b></li>
    <li style="padding-left:9px;">
        <input value="Show Image Editor" id="btnEditor" onClick="return btnShowImageEditor_onclick()" type="button"></li>
    <li style="padding-left:9px;">
        <input value="Rotate Right" id="btnRotateR" onClick="return btnRotateRight_onclick()" type="button">
        <input value="Rotate Left" id="btnRotateL" onClick="return btnRotateLeft_onclick()" type="button"></li>
    <li style="padding-left:9px;">
        <input value="Mirror" id="btnMirror" onClick="return btnMirror_onclick()" type="button">
        <input value="Flip" id="btnFlip" onClick="return btnFlip_onclick()" type="button">
        <input value="Crop" id="btnCrop" onClick="btnCrop_onclick();" type="button"></li>
    <li style="padding-left:9px; height:20px;">
        <input value="Change Image Size" id="btnChangeImageSize" onClick="return btnChangeImageSize_onclick();" style="float: left;" type="button"></li>
</ul>
<div id="ImgSizeEditor" style="visibility:hidden; text-align:left;">
<ul>
    <li><label for="img_height"><b>New Height :</b>
        <input id="img_height" style="width: 50%;" size="10" type="text">pixel</label></li>
    <li><label for="img_width"><b>New Width :</b>&nbsp;
        <input id="img_width" style="width: 50%;" size="10" type="text">pixel</label></li>
    <li>Interpolation method:
        <select size="1" id="InterpolationMethod"><option selected="selected" value="1">NearestNeighbor</option><option value="2">Bilinear</option><option value="3">Bicubic</option></select></li>
    <li style="text-align:center;">
        <input value="   OK   " id="btnChangeImageSizeOK" onClick="return btnChangeImageSizeOK_onclick();" type="button">
        <input value=" Cancel " id="btnCancelChange" onClick="return btnCancelChange_onclick();" type="button"></li>
</ul>
</div>
<div id="Crop" style="visibility:hidden ;">
<div style="width:50%; height:100%; float:left; text-align:left;">
<ul>
    <li><label for="img_left"><b>left: </b>
        <input id="img_left" style="width: 50%;" size="4" type="text"></label></li>
    <li><label for="img_top"><b>top: </b>
        <input id="img_top" style="width: 50%;" size="4" type="text"></label></li>
    <li style="text-align:center;">
        <input value="  OK  " id="btnCropOK" onClick="return btnCropOK_onclick()" type="button"></li>
</ul>
</div>
<div style="width:50%; height:100%; float:left; text-align:right;">
<ul>
    <li><label for="img_right"><b>right : </b>
        <input id="img_right" style="width: 50%;" size="4" type="text"></label></li>
    <li><label for="img_bottom"><b>bottom:</b>
        <input id="img_bottom" style="width: 50%;" size="4" type="text"></label></li>
    <li style=" text-align:center;">
        <input value="Cancel" id="cancelcrop" onClick="return btnCropCancel_onclick()" type="button"></li>
</ul>
</div>
</div>
</div>

<div id="divSave" class="divinput">
<ul>
    <li><img alt="arrow" src="scan/arrow.gif" height="12" width="9"><b>Save Image</b></li>
    <li style="padding-left:15px;">
        <label for="txt_fileNameforSave">File Name:<input value="WebTWAINImage" size="20" id="txt_fileNameforSave" type="text"></label></li>
    <li style="padding-left:12px;">
        <label for="imgTypebmp">
            <input value="bmp" name="imgType_save" id="imgTypebmp" onClick="rdsave_onclick();" type="radio">BMP</label>
	    <label for="imgTypejpeg">
		    <input value="jpg" name="imgType_save" id="imgTypejpeg" onClick="rdsave_onclick();" type="radio">JPEG</label>
	    <label for="imgTypetiff">
		    <input value="tif" name="imgType_save" id="imgTypetiff" onClick="rdTIFFsave_onclick();" type="radio">TIFF</label>
	    <label for="imgTypepng">
		    <input value="png" name="imgType_save" id="imgTypepng" onClick="rdsave_onclick();" type="radio">PNG</label>
	    <label for="imgTypepdf">
		    <input checked="checked" value="pdf" name="imgType_save" id="imgTypepdf" onClick="rdPDFsave_onclick();" type="radio">PDF</label></li>
    <li style="padding-left:12px;">
        <label for="MultiPageTIFF_save"><input disabled="disabled" id="MultiPageTIFF_save" type="checkbox">Multi-Page TIFF</label>
        <label for="MultiPagePDF_save"><input id="MultiPagePDF_save" type="checkbox">Multi-Page PDF </label></li>
    <li style="text-align: center">
        <input id="btnSave" value="Save Image" onClick="return btnSave_onclick()" type="button"></li>
</ul>
</div>



</div>

</div>



</div>


                </form>
        </td>
      </tr>
    </table>
    <script language="javascript" src="<?=FileUtil::version('/includes/ajax/scan/scan.js')?>"></script>
  </body>
</html>