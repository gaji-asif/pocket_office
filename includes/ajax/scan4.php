
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">



<head>

    <title>Dynamic Web TWAIN Online Demo</title>

    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

    <meta http-equiv="Content-Language" content="en-us"/>

    <meta name="description"

        content="Dynamic Web TWAIN is a TWAIN scanning SDK specifically optimized for web applications. 

        You can control any TWAIN compatible device drivers - scanner, digital camera or capture card - 

        in a web page to acquire images, edit and then upload to web servers using the TWAIN control." />

    <meta name="keyword" content="Dynamsoft, TWAIN, Scanners, SDK, Scanning"/>

   <style type="text/css">
body
{
    padding: 0px; margin: 0px; background-color:#3a3a3a; font-family: "verdana", "sans-serif"; font-size: 11px;
}

li.fontstyle
{
    font-size: 10px; color: #222222; line-height: 24px;
}

input.bigbutton 
{
    width:120px;height:37px; font-family:"Arial Black"; color:#FE8E14; font-size:14pt; font-style:italic; 
}

ul 
{
    list-style:none; padding-left:0px; margin:0px; 
}

ul li
{
    margin-bottom:6px;
}

div.divinput 
{
    font-size: 11px; color: #222222; padding:10px; line-height: 14px; font-family: "verdana" , "sans-serif"; 
    margin:5px; margin-bottom:10px; background-color:#f0f0f0; text-align:left;
}

div.menudiv
{
    float:left; 
    height:25px;
    padding-top:10px;
}

div#DWTcontainer
{
    margin: 0 auto;
}
.divcontrol
{
    width:580px; height:600px;
}

.divcontrolthumbnail
{
    width:90px; height:560px;
}
div#dwtcontrolContainer  
{
    margin:0px; margin-left:22px; float:left; padding:0px; padding-top:10px; width:600px; height:800px;
}

div.dwtcontrolThumbnail
{
    padding:5px; padding-top:10px; text-align:center; border-collapse:collapse;border:3px solid #cE5E04; position: absolute; height: 580px; 
	z-index: 1; background-color: #f0f0f0; width:100px;
}

div#ScanWrapper 
{
    margin:0px; float:left; padding:0px; padding-top:5px; width:320px; height:800px;
}

div#Crop {
	padding:5px; padding-top:10px; text-align:center; border-collapse:collapse;border:3px solid #cE5E04; position: absolute; height: 80px; 
	z-index: 1; background-color: #f0f0f0; width:250px
}
div#ImgSizeEditor 
{
    padding:5px; padding-top:10px; text-align:center; border-collapse:collapse;border:3px solid #cE5E04; position: absolute; height: 110px; 
	z-index: 1; background-color: #f0f0f0; width:300px
}

div#MoreEditMethods
{
    padding:5px; padding-top:10px; border-collapse:collapse;border:3px solid #cE5E04; position: absolute; height: 175px; 
	z-index: 1; background-color: #f0f0f0; width:250px;  text-align:left;
}

div#divRotateConfig
{
    padding:5px; padding-top:10px; border-collapse:collapse;border:3px solid #cE5E04; position: absolute; height: 100px; 
	z-index: 2; background-color: #f0f0f0; width:200px;
}

div#divSetImageLayout
{
    padding:5px; padding-top:10px; border-collapse:collapse;border:3px solid #cE5E04; position: absolute; height: 75px; 
	z-index: 2; background-color: #f0f0f0; width:350px;
}

div#tblLoadImage
{
    padding:5px; padding-top:10px; text-align:left; border-collapse:collapse; border:3px solid #cE5E04; position: absolute; height: 225px; 
	z-index: 1; background-color: #fefefe; width:300px
}
a:link {
	color: #222222; line-height: 18px; text-decoration: underline
}
a:visited {
	color: #222222; line-height: 18px; text-decoration: underline
}
a:active {
	color: #666666; line-height: 18px; text-decoration: underline
}
a:hover {
	color: #ff3300; line-height: 18px; text-decoration: underline
}
a.menu:link {
	color: #222222; line-height: 18px; text-decoration: none
}
a.menu:visited {
	color: #222222; line-height: 18px; text-decoration: none
}
a.menu:active {
	color: #222222; line-height: 18px; text-decoration: none
}
a.menu:hover {
	color: #222222; line-height: 18px; text-decoration: none
}
a.white:link {
	color: #d9d9d9; line-height: 18px; text-decoration: underline
}
a.white:visited {
	color: #d9d9d9; line-height: 18px; text-decoration: underline
}
a.white:active {
	color: #d9d9d9; line-height: 18px; text-decoration: underline
}
a.white:hover {
	color: #d9d9d9; line-height: 18px; text-decoration: none
}
a.gray:link {
	color: #222222; line-height: 18px; text-decoration: none
}
a.gray:visited {
	color: #222222; line-height: 18px; text-decoration: none
}
a.gray:active {
	color: #222222; line-height: 18px; text-decoration: none
}
a.gray:hover {
	color: #222222; line-height: 18px; text-decoration: underline
}
a.grayunder:link {
	color: #454545; line-height: 18px; text-decoration: underline
}
a.grayunder:visited {
	color: #454545; line-height: 18px; text-decoration: underline
}
a.grayunder:active {
	color: #454545; line-height: 18px; text-decoration: underline
}
a.grayunder:hover {
	color: #454545; line-height: 18px; text-decoration: none
}
.tableborder {
	border-right: #cdcdcd 1px solid; border-top: #cdcdcd 1px solid; border-left: #cdcdcd 1px solid; border-bottom: #cdcdcd 1px solid
}
.tableborderbottom {
	border-bottom: #cdcdcd 1px solid
}
.fontgray12B {
	font-weight: bold; color: #555555
}
.fontyellow12B {
	font-weight: bold; color: #3a3a3a
}
.titlepagetd {
	vertical-align: middle; height: 30px
}
.subtitletd {
	vertical-align: bottom; height: 30px
}
.titlepage {
	font-weight: bold; font-size: 14px; color: #fe8e14
}
.subtitle {
	font-weight: 600; font-size: 11px; vertical-align: bottom; color: #fe8e14; FONT-FAMILY: Verdana; height: 20px; TEXT-ALIGN: left
}
.menuout {
	padding-bottom: 5px; cursor: hand; color: #ffffff; background-color: #fe8e14
}
.menuover {
	cursor: hand; color: #ffffff; background-color: #5f6062
}
.menu_top_over {
	padding-left: 30px; font-size: 11px; background: url(../images/menutop1.jpg) no-repeat 50% bottom; width: 151px; cursor: hand; color: #353535; padding-top: 9px; FONT-FAMILY: "verdana"; height: 48px
}
.menu_top_out {
	padding-left: 30px; font-size: 11px; background: url(../images/menutop.jpg) no-repeat 50% bottom; width: 151px; cursor: hand; color: #353535; padding-top: 9px; FONT-FAMILY: "verdana"; height: 48px
}
.menu_over {
	padding-left: 30px; font-size: 11px; background: url(../images/menuover.jpg) no-repeat 50% bottom; width: 151px; cursor: hand; color: #353535; FONT-FAMILY: "verdana"; height: 33px
}
.menu_out {
	padding-left: 30px; font-size: 11px; background: url(../images/menuout.jpg) no-repeat 50% bottom; width: 151px; cursor: hand; color: #353535; FONT-FAMILY: "verdana"; height: 33px
}
.menu_blank {
	background: url(../images/menublank.jpg) no-repeat 50% bottom; width: 151px; cursor: default; color: #353535; height: 33px
}
.body_Narrow_width {
	width: 964px;
}
.body_Broad_width {
	width: 984px;
}
input{
    font:normal 11px verdana;
}

input.invalid {
	background-color: #FF9;
	border: 2px red inset;
}

a.menucolor:link
{
    font-weight: bold; font-family: Arial; font-size: 12px; margin-right: 5;
    color: #FFFFFF;
    text-decoration: none;
}
a.menucolor:visited
{
    font-weight: bold; font-family: Arial; font-size: 12px; margin-right: 5;
    color: #FFFFFF;
    text-decoration: none;
}
a.menucolor:hover
{
    font-weight: bold; font-family: Arial; font-size: 12px; margin-right: 5;
    color: #FE8E14;
    text-decoration: none;
}
a.menucolor:active
{
    font-weight: bold; font-family: Arial; font-size: 12px; margin-right: 5;
    color: #FFFFFF;
    text-decoration: none;
}

a.fontcolor:link
{
    color: #000000;
    text-decoration: none;
    line-height:14px;
}
a.fontcolor:visited
{
    color: #000000;
    text-decoration: none;
    line-height:14px;
}
a.fontcolor:hover
{
    color: #000000;
    text-decoration: none;
    line-height:14px;
}
a.fontcolor:active
{
    color: #000000;
    text-decoration: none;
    line-height:14px;
}   
   </style>

</head>



<body>



<div id="container" class="body_Broad_width" style="margin:0 auto;">



<div id="headcontainer" class="body_Broad_width" style="background-color:#3a3a3a; border:0;">

<br />



<div class="body_Broad_width" style="background-image:url(Images/adtopbackground.gif); height:120px; ">



<div style="float:left; border:0px; padding-top:35px; width:198px; margin-left:45px;">

    <a href="http://www.dynamsoft.com/Products/WebTWAIN_Overview.aspx">

    <img alt = "DynamicWebTWAIN logo" border="0" src="Images/DynamicWebTWAIN_logo.gif"/></a>

</div>

<div style="float:left; border:0px; padding-top:35px; width:242px; margin-left:-10px;">

<a><img alt = "Online Demo" border="0" src="Images/Online-Demo.gif"/></a>

</div>

<div style="float:left; border:0px; padding-top:20px; padding-left:50px; width:404px;">

<ul>

<li class="fontstyle">Supports 32-bit/64-bit TWAIN drivers (ActiveX Edition)</li>

<li class="fontstyle">Supports IE (32-bit/64-bit), Firefox, Chrome, Safari,Opera...</li>

<li class="fontstyle">Supports JavaScript, ASP.NET, VB.NET, ASP, PHP, JSP...</li>

</ul>

</div>



</div>



<div class="body_Broad_width" style="height:60px; background-image:url(Images/adtop1background.gif);">

<br />



<div class="menudiv" style="width:280px;" >

    <a class="menucolor" style="color: #FE8E14; margin-left: 35px;"><b>Online Demo </b></a>

</div>



<div class="menudiv" style="width:358px; text-align:right;" >

    <a class="menucolor" href="http://www.dynamsoft.com/Downloads/WebTWAIN_Download.aspx">Free Trial Download</a>

</div>



<div class="menudiv" style="width:200px; text-align:center;" >

    <a class="menucolor" href="http://www.dynamsoft.com/download/Support/DWT/DWTDemoCode.zip">Demo Code Download</a>

</div>



<div class="menudiv" style="width:100px; text-align:center;" >

    <a class="menucolor" href="online_demo_faq.aspx">FAQ</a>

</div>



</div>



<div class="body_Broad_width" style="background-image:url(Images/menu_back.gif); background-repeat:repeat; height:20px; background-color:#ffffff;">



</div>



</div>



<div id="DWTcontainer" class="body_Broad_width" style="background-color:#ffffff; height:800px; border:0;">



<div id="dwtcontrolContainer">

<div id="dwtcontrol">

<div id="maindivPlugin" >



<div style="display: none;" id="mainControlNotInstalled">

<table id="maintblcontrolnotinstalled" class="divcontrol">

    <tr>

    <td style="text-align: center; vertical-align: middle;">

        <a href="http://<?=$_SERVER['SERVER_NAME']?>/plugins/DynamicWebTWAINPlugIn.exe"><strong>Download and install the Plug-in Here</strong></a><br />

        After the installation, please restart your browser.

    </td>

    </tr>

</table>

</div>



<div id="mainControlInstalled">



</div>



</div>



<div id="maindivIE">

<object classid="clsid:5220cb21-c88d-11cf-b347-00aa00a28331" style="display:none;">

    <param name="LPKPath" value="DynamicWebTWAIN/DynamicWebTwain.lpk" />

</object>

<div id="maindivIEx86" >                                    

</div>

<div id="maindivIEx64" >                                  

</div>

</div>

</div>



<div id="extraInfo" style="font-size: 11px; color: #222222; font-family: verdana sans-serif; background-color:#f0f0f0; text-align:left; width:580px;" ></div>



<div class="divinput" style="text-align:center; width:580px; background-color:#FFFFFF;">

    <input id="btnFirstImage" onclick="return btnFirstImage_onclick()" type="button" value=" |&lt; "/>&nbsp;

    <input id="btnPreImage" onclick="return btnPreImage_onclick()" type="button" value=" &lt; "/>&nbsp;&nbsp;

    <input type="text" size="2" id="CurrentImage" readonly="readonly"/>/

    <input type="text" size="2" id="TotalImage" readonly="readonly" value="0"/>&nbsp;&nbsp;

    <input id="btnNextImage" onclick="return btnNextImage_onclick()" type="button" value=" &gt; "/>&nbsp;

    <input id="btnLastImage" onclick="return btnLastImage_onclick()" type="button" value=" &gt;| "/>

    Preview Mode

    <select size="1" id="PreviewMode" onchange ="slPreviewMode();">

        <option value="0">1X1</option>

    </select><br />

    <input id="btnRemoveCurrentImage" onclick="return btnRemoveCurrentImage_onclick()" type="button" value="Remove Selected Images"/>

    <input id="btnRemoveAllImages" onclick="return btnRemoveAllImages_onclick()" type="button" value="Remove All Images"/><br />

    <div id="divMsg" style="text-align:left;">

        Message:<br />

        <div id="emessage" style="width:550px;height:80px; overflow:scroll; background-color:#ffffff; border:1px #303030; border-style:solid; text-align:left;" >

        </div>

    </div>

</div>



</div>



<div id="ScanWrapper">



<div id="divScanner" class="divinput">

<ul>

    <li><img alt="arrow" src="Images/arrow.gif" style="width:9px; height:12px;"/>

        <b>Custom Scan</b></li>

    <li style="padding-left:15px;">

        <label for="source">Select Source<select size="1" id="source">

            <option value = ""></option>    

        </select></label></li>

    <li style="padding-left:12px;">

        <label for = "ShowUI"><input type="checkbox" id="ShowUI" />If Show UI&nbsp;</label>

        <label for = "ADF"><input type="checkbox" id="ADF" />ADF&nbsp;</label>

        <label for = "Duplex"><input type="checkbox" id="Duplex" />Duplex</label></li>

    <li style="padding-left:12px;">

        <label for = "DiscardBlank"><input type="checkbox" id="DiscardBlank"/>If Discard Blank Images</label></li>

    <li style="padding-left:15px;">Pixel Type

        <label for="BW"><input type="radio" id="BW" name="PixelType"/>B&amp;W </label>

        <label for="Gray"><input type="radio" checked="checked" id="Gray" name="PixelType"/>Gray</label>

        <label for="RGB"><input type="radio" id="RGB" name="PixelType"/>Color</label></li>

    <li style="padding-left:15px;">

        <label for="Resolution">Resolution:<select size="1" id="Resolution"><option value = ""></option></select></label></li>

    <li style="text-align:center;">

        <input id="btnScan" class="bigbutton" type="button" value="Scan" onclick ="btnScan_onclick();"/></li>

    <li style="display:none;" id="pNoScanner">

        <a href="javascript: void(0)" class="ShowtblLoadImage"><b>What if I don't have a scanner connected?</b>

    </a></li>

</ul>

</div>



<div id="tblLoadImage" style="visibility:hidden;">

<ul>

    <li><b>You can:</b><a href="javascript: void(0)" style="text-decoration:none; padding-left:225px" class="ClosetblLoadImage">X</a></li>

</ul>

<div style="background-color:#f0f0f0; padding:10px;">

<ul>

    <li><img alt="arrow" src="Images/arrow.gif" width="9" height="12"/><b>Install a Virtual Scanner:</b></li>

    <li style="text-align:center;"><a id="32bitsamplesource" href="http://www.dynamsoft.com/demo/DWT5/Sources/twainds.win32.installer.2.1.3.msi">32-bit Sample Source</a>

        <a id="64bitsamplesource" style="display:none;" href="http://www.dynamsoft.com/demo/DWT5/Sources/twainds.win64.installer.2.1.3.msi">64-bit Sample Source</a></li>

</ul>

</div>



<ul>

    <li><b>Or you can:</b></li>

</ul>

<div style="background-color:#f0f0f0; padding:10px;">

<ul>

    <li><img alt="arrow" src="Images/arrow.gif" width="9" height="12"/><b>Load a Local Image:</b></li>

    <li style="text-align:center"><input id="btnLoad" class="bigbutton" type="button" style="width:180px;" value="Load Image" onclick="return btnLoad_onclick()" /></li>

    <li>Please <b>NOTE</b> that not all images are supported. For more info, please click 

        <a target="_blank" href="http://kb.dynamsoft.com/questions/612/How+to+load+images+not+created+by+Dynamic+Web+TWAIN%3F">here</a>.</li>

</ul>

</div>

</div>



<div id ="divEdit" class="divinput">

<ul>

    <li><img alt="arrow" src="Images/arrow.gif" width="9" height="12"/><b>Edit Image</b></li>

    <li style="padding-left:9px;">

        <input type="button" value="Show Image Editor" id="btnEditor" onclick="return btnShowImageEditor_onclick()"/></li>

    <li style="padding-left:9px;">

        <input type="button" value="Rotate Right" id="btnRotateR" onclick="return btnRotateRight_onclick()"/>

        <input type="button" value="Rotate Left" id="btnRotateL" onclick="return btnRotateLeft_onclick()"/></li>

    <li style="padding-left:9px;">

        <input type="button" value="Mirror" id="btnMirror" onclick="return btnMirror_onclick()"/>

        <input type="button" value="Flip" id="btnFlip" onclick="return btnFlip_onclick()"/>

        <input type="button" value="Crop" id="btnCrop" onclick="btnCrop_onclick();"/></li>

    <li style="padding-left:9px; height:20px;">

        <input type="button" value="Change Image Size" id="btnChangeImageSize" onclick="return btnChangeImageSize_onclick();" style="float:left" /></li>

</ul>

<div id="ImgSizeEditor" style="visibility:hidden; text-align:left;">	

<ul>

    <li><label for="img_height"><b>New Height :</b>

        <input type="text" id="img_height" style="width:50%;" size="10"/>pixel</label></li>

    <li><label for="img_width"><b>New Width :</b>&nbsp;

        <input type="text" id="img_width" style="width:50%;" size="10"/>pixel</label></li>

    <li>Interpolation method:

        <select size="1" id="InterpolationMethod"><option value = ""></option></select></li>

    <li style="text-align:center;">

        <input type="button" value="   OK   " id="btnChangeImageSizeOK" onclick ="return btnChangeImageSizeOK_onclick();"/>

        <input type="button" value=" Cancel " id="btnCancelChange" onclick ="return btnCancelChange_onclick();"/></li>

</ul>

</div>

<div id="Crop" style="visibility:hidden ;">	

<div style="width:50%; height:100%; float:left; text-align:left;">

<ul>

    <li><label for="img_left"><b>left: </b>

        <input type="text" id="img_left" style="width:50%;" size="4"/></label></li>

    <li><label for="img_top"><b>top: </b>

        <input type="text" id="img_top" style="width:50%;" size="4"/></label></li>

    <li style="text-align:center;">

        <input type="button" value="  OK  " id="btnCropOK" onclick ="return btnCropOK_onclick()"/></li>

</ul>

</div>

<div style="width:50%; height:100%; float:left; text-align:right;">

<ul>

    <li><label for="img_right"><b>right : </b>

        <input type="text" id="img_right" style="width:50%;" size="4"/></label></li>

    <li><label for="img_bottom"><b>bottom:</b>

        <input type="text" id="img_bottom" style="width:50%;" size="4"/></label></li>

    <li style=" text-align:center;">

        <input type="button" value="Cancel" id="cancelcrop" onclick ="return btnCropCancel_onclick()"/></li>

</ul>

</div>

</div>

</div>



<div id="divSave" class="divinput">

<ul>

    <li><img alt="arrow" src="Images/arrow.gif" width="9" height="12"/><b>Save Image</b></li>

    <li style="padding-left:15px;">

        <label for="txt_fileNameforSave">File Name:<input type="text" size="20" id="txt_fileNameforSave"/></label></li>

    <li style="padding-left:12px;">

        <label for="imgTypebmp">

            <input type="radio" value="bmp" name="imgType_save" id="imgTypebmp" onclick ="rdsave_onclick();"/>BMP</label>

	    <label for="imgTypejpeg">

		    <input type="radio" value="jpg" name="imgType_save" id="imgTypejpeg" checked="checked" onclick ="rdsave_onclick();"/>JPEG</label>

	    <label for="imgTypetiff">

		    <input type="radio" value="tif" name="imgType_save" id="imgTypetiff" onclick ="rdTIFFsave_onclick();"/>TIFF</label>

	    <label for="imgTypepng">

		    <input type="radio" value="png" name="imgType_save" id="imgTypepng" onclick ="rdsave_onclick();"/>PNG</label>

	    <label for="imgTypepdf">

		    <input type="radio" value="pdf" name="imgType_save" id="imgTypepdf" onclick ="rdPDFsave_onclick();"/>PDF</label></li>

    <li style="padding-left:12px;">

        <label for="MultiPageTIFF_save"><input type="checkbox" id="MultiPageTIFF_save"/>Multi-Page TIFF</label>

        <label for="MultiPagePDF_save"><input type="checkbox" id="MultiPagePDF_save"/>Multi-Page PDF </label></li>

    <li style="text-align: center">

        <input id="btnSave" type="button" value="Save Image" onclick ="return btnSave_onclick()"/></li>

</ul>

</div>



<div id="divUpload" class="divinput">

<ul>

    <li><img alt="arrow" src="Images/arrow.gif" width="9" height="12"/><b>Upload Image</b></li>

    <li style="padding-left:9px;">

        <label for="txt_fileName">File Name: <input type="text" size="20" id="txt_fileName"/></label></li>

    <li style="padding-left:9px;">

        <!--<label for="imgTypebmp2">

            <input type="radio" value="bmp" name="ImageType" id="imgTypebmp2" onclick ="rd_onclick();"/>BMP</label>-->

	    <label for="imgTypejpeg2">

		    <input type="radio" value="jpg" name="ImageType" id="imgTypejpeg2" checked="checked" onclick ="rd_onclick();"/>JPEG</label>

	    <label for="imgTypetiff2">

		    <input type="radio" value="tif" name="ImageType" id="imgTypetiff2" onclick ="rdTIFF_onclick();"/>TIFF</label>

	    <label for="imgTypepng2">

		    <input type="radio" value="png" name="ImageType" id="imgTypepng2" onclick ="rd_onclick();"/>PNG</label>

	    <label for="imgTypepdf2">

		    <input type="radio" value="pdf" name="ImageType" id="imgTypepdf2" onclick ="rdPDF_onclick();"/>PDF</label></li>

    <li style="padding-left:9px;">

        <label for="MultiPageTIFF"><input type="checkbox" id="MultiPageTIFF"/>Multi-Page TIFF</label>

        <label for="MultiPagePDF"><input type="checkbox" id="MultiPagePDF"/>Multi-Page PDF </label></li>

    <li style="text-align: center">

        <input id="btnUpload" type="button" value="Upload Image" onclick ="return btnUpload_onclick()"/></li>

</ul>

</div>



</div>



</div>



<div id="tailcontainer" class="body_Broad_width" style="background-color:#ffffff; border:0;">



<div class="body_Broad_width" style="height:4px; background-color:#303030"></div>

<div class="body_Broad_width" style="height:6px; background-color:#ff8e13"></div>



<div style="width:10px; height:85px;float:left; border:0px; padding:0px;">

    <img alt = "&gt;" src="Images/bottomleft.gif"/></div>

<div class="body_Narrow_width" style="height:85px; border:0px; padding:0px; float:left; text-align:center; background-image: url(Images/bottommid.gif); background-repeat:repeat;">

<br />

    <a class="fontcolor" href="http://www.dynamsoft.com/Products/WebTWAIN_Overview.aspx"

        style="cursor:text">TWAIN SDK</a> Powered By Dynamsoft

</div>

<div style="width:10px; height:85px;float:left; border:0px; padding:0px;">

<img alt = "&gt;" src="Images/bottomright.gif"/></div>



</div>



</div>



<script language="javascript" src="Scripts/online_demo_scan.js"></script>



<script>

<!--

    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");

    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js'%3E%3C/script%3E"));

    //-->

</script>

<script>

<!--

    try {

        var pageTracker = _gat._getTracker("UA-1203134-1");

        pageTracker._trackPageview();

    } catch (err) { }

    //-->

</script>

<script>

<!--

    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");

    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js'%3E%3C/script%3E"));

    //-->

</script>

<script>

<!--

    try {

        var pageTracker = _gat._getTracker("UA-1203134-2");

        pageTracker._trackPageview();

    } catch (err) { }

    //-->

</script>



</body>

</html>

