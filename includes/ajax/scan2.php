
<html>

<head>

    <title>Dynamic Web TWAIN Online Demo</title>

    <meta content="False" name="vs_snapToGrid"/>

    <meta http-equiv="Content-Language" content="en-us"/>

    <link href="styles/style.css" type="text/css" rel="stylesheet" />

</head>



<body nof="(MB=(AlphaSoftware, 175, 99, 0, 0), L=(RegistrationLayout, 731, 1760))">

<center>

<form id="frmScan" action="scan3.php">
  <table height="705" cellspacing="0" cellpadding="0" width="984" bgcolor="#ffffff"

border="0" nof="ly" align="center">

<tr>

<td valign="top" align="center" width="60%">

<object classid="clsid:5220cb21-c88d-11cf-b347-00aa00a28331" viewastext style="display:none;" width="100%">

<param name="LPKPath" value="DynamicWebTWAIN/DynamicWebTwain.lpk" />


</object>

<table width="90%">

<tr>

<td align="center">

<div id="divPlugin">

<table style="display: none" width="437" height="511" id="IsControlInstalled">

<tr>

<td style="text-align: center; vertical-align: middle;">

    <a href="http://<?=$_SERVER['SERVER_NAME']?>/plugins/DynamicWebTWAINPlugIn.exe"><strong>Download and install the Plug-in Here</strong></a><br />

    After the installation, please restart your browser.</td>
</tr>
</table>

<embed style="display: block" id="DynamicWebTWAIN" type="Application/DynamicWebTwain-Plugin"

    OnPostTransfer="DynamicWebTwain1_OnPostTransfer" OnPostAllTransfers="DynamicWebTwain1_OnPostAllTransfers"

    OnMouseClick="DynamicWebTwain1_OnMouseClick"  OnPostLoad="OnPostLoadfunction"

    height="528" width="100%" pluginspage="http://<?=$_SERVER['SERVER_NAME']?>/plugins/DynamicWebTWAINPlugIn.exe"></embed>
</div>

<div id="divIE">

<div id="divIEx86" ></div>

<div id="divIEx64" ></div>
</div></td>
</tr>

<tr>

<td align="center">

<p>

<input id="btnFirstImage" onClick="return btnFirstImage_onclick()" type="button"

value=" |< ">&nbsp;&nbsp;

<input id="btnPreImage" onClick="return btnPreImage_onclick()" type="button" value=" < ">&nbsp;&nbsp;

<input type="text" name="CurrentImage" size="2" id="CurrentImage" readonly="readOnly" />/<input

type="text" name="TotalImage" size="2" id="TotalImage" readonly="readOnly" value="0" />&nbsp;&nbsp;

<input id="btnNextImage" onClick="return btnNextImage_onclick()" type="button" value=" > ">&nbsp;&nbsp;

<input id="btnLastImage" onClick="return btnLastImage_onclick()" type="button" value=" >| ">

Preview Mode

<select size="1" name="PreviewMode" id="PreviewMode" onChange="slPreviewMode();">
</select><br />

<input id="btnRemoveCurrentImage" onClick="return btnRemoveCurrentImage_onclick()"

type="button" value="Remove Current Image"><input id="btnRemoveAllImages" onClick="return btnRemoveAllImages_onclick()"

type="button" value="Remove All Images"><br />
</p></td>
</tr>
</table>

</td>

<td valign="top" width="40%">

<table width="90%" bgcolor="#f0f0f0">

<tr>

<td width="9" height="30">

</td>

<td height="30">

<img alt="&gt;" src="Images/arrow.gif" width="9" height="12" />

</td>

<td height="30">

<b>Custom Scan</b>

</td>

</tr>

<tr>

<td valign="middle" width="3%">

</td>

<td valign="middle" width="3%">

</td>

<td valign="middle" width="94%">

Select Source

<select size="1" name="source" id="source">

</select>

</td>

</tr>

<tr>

<td>

</td>

<td>

</td>

<td>

If Show UI

<input type="checkbox" value="ON" name="ShowUI" id="ShowUI" />&nbsp;

ADF

<input type="checkbox" value="ON" name="ADF" id="ADF" />&nbsp;

Duplex

<input type="checkbox" value="ON" name="Duplex" id="Duplex" />

</td>

</tr>

<tr>

<td>

</td>

<td>

</td>

<td>

If Discard Blank Page

<input type="checkbox" value="ON" id="DiscardBlank" name="DiscardBlank">&nbsp;

</td>

</tr>

<tr>

<td>

</td>

<td>

</td>

<td>

Pixel Type

<groupbox><input type="radio"  value="V15" name="PixelType">BW <input type="radio" value="V13" CHECKED name="PixelType">Gray <input type="radio" value="V14" name="PixelType">RGB</groupbox>

</td>

</tr>

<tr>

<td>

</td>

<td>

</td>

<td>

Resolution&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<select size="1" name="Resolution" id="Resolution">

</select>

</td>

</tr>

<tr>

<td valign="top" colspan="3" height="35">

<p align="center">

<input id="btnScan" type="button" style="font-size: 10; width: 104; height: 37; font-family: Arial Black;

color: #FE8E14; font-size: 14pt; font-style: italic" value="Scan" onClick="btnScan_onclick();"/>

</p>

</td>

</tr>

</table>

<table>

<tr style="height:10px; width:100%">

<td><br /></td>

</tr>

</table>

<table width="90%" bgcolor="#f0f0f0">

<tr>

<td width="9" height="30">
</td>

<td height="30">

<img src="Images/arrow.gif" width="9" height="12" />

</td>

<td height="30">

<b>Load Image</b>

</td>

</tr>

<tr>

<td width="3%" height="30">

</td>

<td width="3%" height="30">

</td>

<td width="94%" height="30">

If you don't have any scanner connnected, you can load images from your local disk.<br/>

</td>

</tr>

<tr>

<td valign="top" colspan="3" height="40">

<p align="center">

<input id="btnLoad" type="button" style=" width: 140; height: 37; font-family: Arial Black;

color: #FE8E14; font-size: 12pt; font-style: italic" value="Load Image" onClick="return btnLoad_onclick()"></p>

</td>

</tr>

</table>



<table>

<tr style="height:10px; width:100%">

<td><br /></td>

</tr>

</table>

<table width="90%" bgcolor="#f0f0f0">

<tr>

<td height="25">

</td>

<td height="25">

<img alt = "&gt;" src="Images/arrow.gif" width="9" height="12" />

</td>

<td height="25">

<b>Edit Image</b>

</td>

</tr>

<tr style="height:0px;">

<td>

</td>

<td>

</td>

<td>

<div style="border: 1px solid #000000; position: absolute; height: 120px; z-index: 1;

right: 280px; top: 560px; background-color: #f0f0f0; width: 275px; visibility: hidden"

id="ImgSizeEditor">

<table border="0" style="border-collapse: collapse" width="100%" id="table1">

<tr>

    <td width="94">

    </td>

    <td>

    </td>

</tr>

<tr>

    <td width="94">

        New Height:

    </td>

    <td>

        <input type="text" name="img_height" id="img_height" size="10" onKeyUp="if(event.keyCode !=37 &amp;&amp; event.keyCode!=39) value=value.replace(/\D/g,'');"

            onpaste="clipboardData.setData("text",clipboardData.getData("text").replace(/\D/g,''))">pixel

    </td>

</tr>

<tr>

    <td width="94">

        New Width:

    </td>

    <td>

        <input type="text" name="img_width" id="img_width" size="10" onKeyUp="if(event.keyCode !=37 &amp;&amp; event.keyCode!=39) value=value.replace(/\D/g,'');"

            onpaste="clipboardData.setData("text",clipboardData.getData("text").replace(/\D/g,''))">pixel

    </td>

</tr>

<tr>

    <td width="94">

        Interpolation method

    </td>

    <td>

        <select size="1" name="InterpolationMethod" id="InterpolationMethod">

        </select>

    </td>

</tr>

<tr>

    <td width="94">

        <input type="button" value="     OK     " name="btn_OK" onClick="return btnOK_onclick()"

            style="float: right">

    </td>

    <td>

        <input type="button" value="  Cancel  " name="btn_Cancel" onClick="return btnCancel_onclick()">

    </td>

</tr>

<tr>

    <td width="94">

    </td>

    <td>

    </td>

</tr>

</table>

</div>

<div style="border: 1px solid #000000; position: absolute; height: 125px; z-index: 1;

right: 280px; top: 570px; background-color: #f0f0f0; width: 275px; visibility: hidden"

id="Crop">



<table width="100%">

<tr>

    <td width="50%">

    </td>

    <td width="50%">

    </td>

</tr>

<tr>

    <td width="50%" height="26">

        left:&nbsp;

        <input type="text" name="img_left" id="img_left" size="10" onKeyUp="if(event.keyCode !=37 &amp;&amp; event.keyCode!=39) value=value.replace(/\D/g,'');"

            onpaste="clipboardData.setData("text",clipboardData.getData("text").replace(/\D/g,''))">

    </td>

    <td width="50%" height="26">

        top:&nbsp;&nbsp;&nbsp;&nbsp;

        <input type="text" name="img_top" id="img_top" size="10" onKeyUp="if(event.keyCode !=37 &amp;&amp; event.keyCode!=39) value=value.replace(/\D/g,'');"

            onpaste="clipboardData.setData("text",clipboardData.getData("text").replace(/\D/g,''))">

    </td>

</tr>

<tr>

    <td>

        right:<input type="text" name="img_right" id="img_right" size="10" onKeyUp="if(event.keyCode !=37 &amp;&amp; event.keyCode!=39) value=value.replace(/\D/g,'');"

            onpaste="clipboardData.setData("text",clipboardData.getData("text").replace(/\D/g,''))">

    </td>

    <td>

        bottom:<input type="text" name="img_bottom" id="img_bottom" size="10" onKeyUp="if(event.keyCode !=37 &amp;&amp; event.keyCode!=39) value=value.replace(/\D/g,'');"

            onpaste="clipboardData.setData("text",clipboardData.getData("text").replace(/\D/g,''))">

    </td>

</tr>

<tr>

    <td>

    </td>

    <td>

        <input type="button" value="    OK    " name="btn_OK1" onClick="return btnCropOK_onclick()"><input

            type="button" value="Cancel" name="btn_Cancel1" onClick="return btnCropCancel_onclick()">

    </td>

</tr>

<tr>

    <td>

    </td>

    <td>

    </td>

</tr>

</table>

</div>

</td>

</tr>

<tr>

<td>

</td>

<td>

</td>

<td>

<input id="btnShowImageEditor" onClick="return btnShowImageEditor_onclick()" type="button"

value="Show Image Editor"/>

</td>

</tr>

<tr>

<td>

</td>

<td>

</td>

<td>

<input id="btnRotateRight" onClick="return btnRotateRight_onclick()" type="button"

value="Rotate Right">

<input id="btnRotateLeft" onClick="return btnRotateLeft_onclick()" type="button"

value="Rotate Left">

</td>

</tr>

<tr>

<td>

</td>

<td>

</td>

<td>

<input id="btnMirror" onClick="return btnMirror_onclick()" type="button" value="Mirror">

<input id="btnFlip" onClick="return btnFlip_onclick()" type="button" value=" Flip ">

<input id="btnCrop" type="button" value="Crop" onClick="return btnCrop_onclick()">

</td>

</tr>

<tr>

<td>

</td>

<td>

</td>

<td>

<input id="btnChangeImageSize" type="button" value="Change Image Size" onClick="return btnChangeImageSize_onclick()">

</td>

</tr>

</table>

<table>

<tr style="height:10px; width:100%">

<td><br /></td>

</tr>

</table>

<table width="90%" bgcolor="#f0f0f0">

<tr>

<td width="3%" height="30">

</td>

<td width="3%" height="30">

<img alt = "&gt;" src="Images/arrow.gif" width="9" height="12" />

</td>

<td width="24%" height="30">

<b>Save Image</b>

</td>

<td width="70%" height="30">

</td>

</tr>

<tr>

<td>

</td>

<td>

</td>

<td colspan="2">

<label>

<input type="radio" value="bmp" name="imgType_save" onClick="rdsave_onclick();"/></label>BMP

<label>

<input type="radio" value="jpg" name="imgType_save" checked="checked" onClick="rdsave_onclick();"/></label>JPEG

<label>

<input type="radio" value="tif" name="imgType_save" onClick="rdTIFFsave_onclick();"/></label>TIFF

<label>

<input type="radio" value="png" name="imgType_save" onClick="rdsave_onclick();"/></label>PNG

<label>

<input type="radio" value="pdf" name="imgType_save" onClick="rdPDFsave_onclick();"/></label>PDF

</td>

</tr>

<tr>

<td>

</td>

<td>

</td>

<td colspan="2">

<input type="checkbox" value="ON" name="MultiPageTIFF_save" id="MultiPageTIFF_save" />Multi-Page

TIFF

<input type="checkbox" value="ON" name="MultiPagePDF_save" id="MultiPagePDF_save" />Multi-Page

PDF

</td>

</tr>

<tr>

<td>

</td>

<td>

</td>

<td>

File Name

</td>

<td>

<input type="text" size="20" name="txt_fileNameforSave" id="txt_fileNameforSave"/>

</td>

</tr>

<tr>

<td>

</td>

<td>

</td>

<td>

Path

</td>

<td>

<input type="text" size="20" name="txt_filePathforSave" id="txt_filePathforSave">

</td>

</tr>

<tr>

<td valign="top" colspan="4" height="36">

<p align="center">

<input id="btnSave" type="button" value="Save Image" onClick="return btnSave_onclick()"/></p>

</td>

</tr>

</table>

<table>

<tr style="height:10px; width:100%">

<td><br /></td>

</tr>

</table>

<tr>

<td>

<br />

</td>

</tr>

</td>

</tr>

</table>

<table cellspacing="0" cellpadding="0" width="984" bgcolor="#ffffff" border="0" nof="ly">

<tr valign="top" align="left">

</tr>

</table>

<table cellspacing="0" cellpadding="0" width="984" bgcolor="#ffffff" border="0" nof="ly">

<tr valign="top" align="left">

</tr>

</table>

<table cellspacing="0" cellpadding="0" width="984" bgcolor="#ffffff" border="0" nof="ly">

<tr valign="top" align="left">

</tr>

</table>

<table cellspacing="0" cellpadding="0" width="984" bgcolor="#ffffff" border="0" nof="ly">

<tr valign="top" align="left">

</tr>

</table>

<table cellspacing="0" cellpadding="0" width="984" bgcolor="#ffffff" border="0" nof="ly">

<tr valign="top" align="left">

<td width="984" height="16">

<table height="1" cellspacing="0" cellpadding="0" width="772" border="0" nof="ly">

<tr valign="top" align="left">

<td width="984" colspan="3" height="4">

<table class="" cellspacing="0" cellpadding="0" width="984" align="center" border="0">

<tbody>

<tr>

<td width="984" bgcolor="#303030" height="4">

</td>

</tr>

<tr>

<td width="984" bgcolor="#ff8e13" height="6">

</td>

</tr>

</tbody>

</table>

</td>

</tr>

</table>

</td>
</tr>

</table>

</form>

</center>



<script>

var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");

document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js'%3E%3C/script%3E"));

</script>



<script>

try {

var pageTracker = _gat._getTracker("UA-1203134-1");

pageTracker._trackPageview();

} catch (err) { }</script>



<script>

var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");

document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js'%3E%3C/script%3E"));

</script>



<script>

try {

var pageTracker = _gat._getTracker("UA-1203134-2");

pageTracker._trackPageview();

} catch (err) { }</script>



</body>



<script language="javascript" id="clientEventHandlersJS">



window.onload = Pageonload;



function ExplorerType() {

var ua = (navigator.userAgent.toLowerCase());       

if(ua.indexOf("msie") != -1) return "IE";     

}

//====================IE or Firefox end======================//

	

var WebTWAIN;



var strObject = "";

strObject = "<param name='_cx' value='3784' />";

strObject +="	<param name='_cy' value='4128' />";

strObject +="	<param name='JpgQuality' value='80' />";

strObject +="	<param name='Manufacturer' value='DynamSoft Corporation' />";

strObject +="	<param name='ProductFamily' value='Dynamic Web TWAIN' />";

strObject +="	<param name='ProductName' value='Dynamic Web TWAIN' />";

strObject +="	<param name='VersionInfo' value='Dynamic Web TWAIN 6.3.1' />";

strObject +="	<param name='TransferMode' value='0' />";

strObject +="	<param name='BorderStyle' value='0' />";

strObject +="	<param name='FTPUserName' value='' />";

strObject +="	<param name='FTPPassword' value='' />";

strObject +="	<param name='FTPPort' value='21' />";

strObject +="	<param name='HTTPUserName' value='' />";

strObject +="	<param name='HTTPPassword' value='' />";

strObject +="	<param name='HTTPPort' value='80' />";

strObject +="	<param name='ProxyServer' value='' />";

strObject +="	<param name='IfDisableSourceAfterAcquire' value='0' />";

strObject +="	<param name='IfShowUI' value='-1' />";

strObject +="	<param name='IfModalUI' value='-1' />";

strObject +="	<param name='IfTiffMultiPage' value='0' />";

strObject +="	<param name='IfThrowException' value='0' />";

strObject +="	<param name='MaxImagesInBuffer' value='1' />";

strObject +="	<param name='TIFFCompressionType' value='0' />";

strObject +="	<param name='IfFitWindow' value='-1' />";

strObject +="	<param name='IfSSL' value='0' />";

strObject +="	</object>";

if(ExplorerType() == "IE" && navigator.userAgent.indexOf("Win64") != -1 && navigator.userAgent.indexOf("x64") != -1) {

strObject = "<object id='DynamicWebTwain1' codebase='DynamicWebTWAIN/DynamicWebTWAINx64.cab#version=6,3,1' height='528' width=\"100%\"  " + "classid='clsid:FFC6F181-A5CF-4ec4-A441-093D7134FBF2' viewastext> " + strObject;

		

var objDivx86 =document.getElementById("divIEx64");

objDivx86.innerHTML=strObject;		

var obj = document.getElementById("divPlugin");

obj.style.display = "none";

WebTWAIN = document.getElementById("DynamicWebTwain1"); 

}



else if(ExplorerType() == "IE" && (navigator.userAgent.indexOf("Win64") == -1 || navigator.userAgent.indexOf("x64") == -1)){ 

strObject = "<object id='DynamicWebTwain1' codebase='DynamicWebTWAIN/DynamicWebTWAIN.cab#version=6,3,1' height='528' width=\"100%\" "+ "classid='clsid:FFC6F181-A5CF-4ec4-A441-093D7134FBF2' viewastext> " + strObject;

		

var objDivx64 =document.getElementById("divIEx86");

objDivx64.innerHTML=strObject;    	 

var obj = document.getElementById("divPlugin");

obj.style.display = "none";

WebTWAIN = document.getElementById("DynamicWebTwain1"); 

}



else {

var obj = document.getElementById("divIE");

obj.style.display = "none";

var obj = document.getElementById("divPlugin");

obj.style.display = "";



WebTWAIN = document.embeds[0];



}



var em = "";

//====================Page Onload  Start==================//

function CheckIfImagesInBuffer() {

if(WebTWAIN.HowManyImagesInBuffer == 0)

{

em = em + "There is no image in buffer.\n";

document.getElementById("emessage").value = em;

return;

}

}



function CheckErrorString() {

    if(WebTWAIN.ErrorString == "HTTP process error"){

        var ErrorMessageWin = window.open ('','ErrorMessage','height=500,width=750,top=0,left=0,toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no, status=no');

        ErrorMessageWin.document.writeln(objDynamicWebTWAIN.HTTPPostResponseString);

    }

    em = em + WebTWAIN.ErrorString + "\n";

    document.getElementById("emessage").value = em;

    return;

}

var count;

var seed;

function ControlDetect() {

//count -= 0.5;   

//alert(WebTWAIN.ErrorCode);

if(WebTWAIN.ErrorCode == 0){	 

pause();

if(ua.match(/chrome\/([\d.]+)/)||ua.match(/opera.([\d.]+)/)||ua.match(/version\/([\d.]+).*safari/)){

document.getElementById("IsControlInstalled").style.display = "none";

document.getElementById("DynamicWebTWAIN").style.display="block";

}	



WebTWAIN.MaxImagesInBuffer = 4096;

WebTWAIN.MouseShape = true;

var i;



document.getElementById("source").options.length=0;



for(i=0;i< WebTWAIN.SourceCount;i++)

{

document.getElementById("source").options.add(new Option(WebTWAIN.SourceNameItems(i),i));

}



document.getElementById("Resolution").options.length=0;

document.getElementById("Resolution").options.add(new Option("100",100));

document.getElementById("Resolution").options.add(new Option("150",150));

document.getElementById("Resolution").options.add(new Option("200",200));

document.getElementById("Resolution").options.add(new Option("300",300));

  

document.getElementById("InterpolationMethod").options.length = 0;

document.getElementById("InterpolationMethod").options.add(new Option("NearestNeighbor",1));

document.getElementById("InterpolationMethod").options.add(new Option("Bilinear",2));

document.getElementById("InterpolationMethod").options.add(new Option("Bicubic",3));

  

document.getElementById("txt_fileNameforSave").value = "WebTWAINImage";

document.getElementById("txt_filePathforSave").value ="C:\\";

document.getElementById ("txt_fileName").value = "WebTWAINImage";

  

document.getElementById("ADF").checked = true;

document.getElementById("MultiPageTIFF_save").disabled = true;

document.getElementById("MultiPagePDF_save").disabled = true;

document.getElementById("MultiPageTIFF").disabled = true;

document.getElementById("MultiPagePDF").disabled = true;

  

document.getElementById("PreviewMode").options.length = 0;

document.getElementById("PreviewMode").options.add(new Option("1X1",0));

document.getElementById("PreviewMode").options.add(new Option("2X2",1));

document.getElementById("PreviewMode").options.add(new Option("3X3",2));

document.getElementById("PreviewMode").selectedIndex = 1;

  

WebTWAIN.SetViewMode(2,2);

}

else

{

if(ua.match(/chrome\/([\d.]+)/)||ua.match(/opera.([\d.]+)/)||ua.match(/version\/([\d.]+).*safari/)){

document.getElementById("IsControlInstalled").style.display = "block";

document.getElementById("DynamicWebTWAIN").style.display="none";

}

}

}



var ua = (navigator.userAgent.toLowerCase()); 

  

function Pageonload() {

// count = 10;

seed = setInterval(ControlDetect, 500);

//setTimeout("ControlDetect()", 2000);   

}

//function timeUp()

//{   

//	clearInterval(seed);

//	window.location.href="online_demo_scan3.aspx";

//}



function pause()

{

    clearInterval(seed);

}



//====================Page Onload End====================//



//====================Preview Group Start====================//

function btnFirstImage_onclick() {

CheckIfImagesInBuffer();

WebTWAIN.CurrentImageIndexInBuffer = 0;

document.getElementById("TotalImage").value = WebTWAIN.HowManyImagesInBuffer;

document.getElementById("CurrentImage").value = WebTWAIN.CurrentImageIndexInBuffer+1;

}

function btnPreImage_onclick() {

CheckIfImagesInBuffer();

if(WebTWAIN.CurrentImageIndexInBuffer == 0)

return;

WebTWAIN.CurrentImageIndexInBuffer = WebTWAIN.CurrentImageIndexInBuffer - 1;

document.getElementById("TotalImage").value = WebTWAIN.HowManyImagesInBuffer;

document.getElementById("CurrentImage").value = WebTWAIN.CurrentImageIndexInBuffer+1;

}

function btnNextImage_onclick() {

CheckIfImagesInBuffer();

if(WebTWAIN.CurrentImageIndexInBuffer == WebTWAIN.HowManyImagesInBuffer - 1)

return;

WebTWAIN.CurrentImageIndexInBuffer = WebTWAIN.CurrentImageIndexInBuffer + 1;

document.getElementById("TotalImage").value = WebTWAIN.HowManyImagesInBuffer;

document.getElementById("CurrentImage").value = WebTWAIN.CurrentImageIndexInBuffer+1;

}

function btnLastImage_onclick() {

CheckIfImagesInBuffer();

WebTWAIN.CurrentImageIndexInBuffer = WebTWAIN.HowManyImagesInBuffer - 1;

document.getElementById("TotalImage").value = WebTWAIN.HowManyImagesInBuffer;

document.getElementById("CurrentImage").value = WebTWAIN.CurrentImageIndexInBuffer+1;

}

function btnRemoveCurrentImage_onclick() {

CheckIfImagesInBuffer();

WebTWAIN.RemoveImage(WebTWAIN.CurrentImageIndexInBuffer);

if(WebTWAIN.HowManyImagesInBuffer == 0){

document.getElementById("TotalImage").value = WebTWAIN.HowManyImagesInBuffer;

document.getElementById("CurrentImage").value = "";

return;

}

else{

document.getElementById("TotalImage").value = WebTWAIN.HowManyImagesInBuffer;

document.getElementById("CurrentImage").value = WebTWAIN.CurrentImageIndexInBuffer+1;

}

}

function btnRemoveAllImages_onclick() {

CheckIfImagesInBuffer();

WebTWAIN.RemoveAllImages();

document.getElementById("TotalImage").value = "0";

document.getElementById("CurrentImage").value = "";

}

//====================Preview Group End====================//



//====================Edit Image Group Start=====================//

function btnShowImageEditor_onclick() {

CheckIfImagesInBuffer();

WebTWAIN.ShowImageEditor();

}

function btnRotateRight_onclick() {

CheckIfImagesInBuffer();

WebTWAIN.RotateRight(WebTWAIN.CurrentImageIndexInBuffer);

}

function btnRotateLeft_onclick() {

CheckIfImagesInBuffer();

WebTWAIN.RotateLeft(WebTWAIN.CurrentImageIndexInBuffer);

}

function btnMirror_onclick() {

CheckIfImagesInBuffer();

WebTWAIN.Mirror(WebTWAIN.CurrentImageIndexInBuffer);

}

function btnFlip_onclick() {

CheckIfImagesInBuffer();

WebTWAIN.Flip(WebTWAIN.CurrentImageIndexInBuffer);

}

/*----------------------Crop Method---------------------*/

function btnCrop_onclick() {

if(WebTWAIN.HowManyImagesInBuffer == 0)

{

em = em + "There is no image in buffer.\n"

document.getElementById("emessage").value = em;

return;

}

document.getElementById("Crop").style.visibility="visible";

}



function btnCropCancel_onclick(){

document.getElementById("Crop").style.visibility="hidden";

}

function btnCropOK_onclick(){

if(document.getElementById("img_left").value == ""){

em = em + "Please input left value.\n"

document.getElementById("emessage").value = em;

return;

}

if(document.getElementById("img_top").value == ""){

em = em + "Please input top value.\n"

document.getElementById("emessage").value = em;

return;    

}

if(document.getElementById("img_right").value == ""){

em = em + "Please input right value.\n"

document.getElementById("emessage").value = em;

return;   

}

if(document.getElementById("img_bottom").value == ""){

em = em + "Please input bottom value.\n"

document.getElementById("emessage").value = em;

return;    

}

WebTWAIN.Crop(

WebTWAIN.CurrentImageIndexInBuffer,

document.getElementById("img_left").value,

document.getElementById("img_top").value,

document.getElementById("img_right").value,

document.getElementById("img_bottom").value);



document.getElementById("Crop").style.visibility="hidden";

}

/*-----------------------------------------------------*/

//====================Edit Image Group End==================//





function btnScan_onclick() {

    var i;    

    WebTWAIN.SelectSourceByIndex(document.getElementById("source").selectedIndex);

    WebTWAIN.CloseSource();	 

    WebTWAIN.OpenSource();

	  

    WebTWAIN.IfShowUI = document.getElementById("ShowUI").checked;

    for(i=0;i<3;i++)

    {

    if(document.getElementsByName("PixelType").item(i).checked==true)

    WebTWAIN.PixelType = i;

    }  

    WebTWAIN.Resolution = document.getElementById("Resolution").value;



    WebTWAIN.IfFeederEnabled = document.getElementById("ADF").checked ;

    WebTWAIN.IfDuplexEnabled = document.getElementById("Duplex").checked ;

    em = em + "Pixel Type: " + WebTWAIN.PixelType + "\nResolution: " + WebTWAIN.Resolution + "\n";

    document.getElementById("emessage").value = em;

    WebTWAIN.IfDisableSourceAfterAcquire = true;

    WebTWAIN.AcquireImage();

}

/*-----------------Load Image---------------------*/

function btnLoad_onclick(){

    WebTWAIN.IfShowFileDialog = true;

    WebTWAIN.LoadImageEx("D:\\WebTWAINImage",5);

    CheckErrorString();

} 

/*----------------Change Image Size--------------------*/

function btnChangeImageSize_onclick(){

if(WebTWAIN.HowManyImagesInBuffer == 0)

{

em = em + "There is no image in buffer.\n"

document.getElementById("emessage").value = em;

return;

}

document.getElementById("ImgSizeEditor").style.visibility="visible";

}

function btnCancel_onclick() {

document.getElementById("ImgSizeEditor").style.visibility="hidden";

}



function btnOK_onclick(){

if(document.getElementById("img_height").value == ""){

em = em + "Please input the height.\n";

document.getElementById("emessage").value = em;

return;    

}

if(document.getElementById("img_width").value == ""){

em = em + "Please input the width.\n";

document.getElementById("emessage").value = em;

return;   	  

}





WebTWAIN.ChangeImageSize(

WebTWAIN.CurrentImageIndexInBuffer,

document.getElementById("img_width").value,

document.getElementById("img_height").value,

document.getElementById("InterpolationMethod").selectedIndex + 1);

document.getElementById ("ImgSizeEditor").style.visibility = "hidden";

}



/*-----------------Save Image Group---------------------*/

function btnSave_onclick(){

if(WebTWAIN.HowManyImagesInBuffer == 0)

{

em = em + "There is no image in buffer.\n"

document.getElementById("emessage").value = em;

return;

}

var i,strimgType_save;

for(i=0;i<5;i++){

if(document.getElementsByName("imgType_save").item(i).checked == true){

strimgType_save  = document.getElementsByName("imgType_save").item(i).value;

break;

}

}

	

	

if(document.getElementById("txt_filePathforSave").value.charAt(document.getElementById("txt_filePathforSave").value.length - 1) != "\\")

document.getElementById("txt_filePathforSave").value = document.getElementById("txt_filePathforSave").value + "\\";

		

var strFilePath = document.getElementById("txt_filePathforSave").value+document.getElementById("txt_fileNameforSave").value+"."+strimgType_save;

if(strimgType_save == "tif" && document.getElementById("MultiPageTIFF_save").checked){

WebTWAIN.SaveAllAsMultiPageTIFF(strFilePath);}

else if(strimgType_save == "pdf" && document.getElementById("MultiPagePDF_save").checked){

WebTWAIN.SaveAllAsPDF(strFilePath);}

else{

switch(i){

case 0:WebTWAIN.SaveAsBMP(strFilePath , WebTWAIN.CurrentImageIndexInBuffer);break;

case 1:WebTWAIN.SaveAsJPEG(strFilePath ,WebTWAIN.CurrentImageIndexInBuffer);break;

case 2:WebTWAIN.SaveAsTIFF(strFilePath ,WebTWAIN.CurrentImageIndexInBuffer);break;

case 3:WebTWAIN.SaveAsPNG(strFilePath , WebTWAIN.CurrentImageIndexInBuffer);break;

case 4:WebTWAIN.SaveAsPDF(strFilePath , WebTWAIN.CurrentImageIndexInBuffer);break;

}

}

alert(WebTWAIN.ErrorString);

CheckErrorString();

}

/*-------------------------------------------------------*/



/*-----------------Upload Image Group---------------------*/

function btnUpload_onclick(){

if(WebTWAIN.HowManyImagesInBuffer == 0)

{

em = em + "There is no image in buffer.\n";

document.getElementById("emessage").value = em;

return;

}

var i,strHTTPServer,strActionPage,strImageType;

if(document.getElementById("txt_fileName").value == ""){

em = em + "please input file name.\n";

document.getElementById("emessage").value = em;

return;

}

strHTTPServer = "www.dynamsoft.com";

// strHTTPServer = "192.168.1.177";

var CurrentPathName = unescape(location.pathname);	// get current PathName in plain ASCII	

var CurrentPath = CurrentPathName.substring(0, CurrentPathName.lastIndexOf("/") + 1);			

var strActionPage = CurrentPath + "SaveToDB.aspx"; //the ActionPage's file path

		

for(i=0;i<5;i++){

if(document.getElementsByName("ImageType").item(i).checked == true){

strImageType  = i;

break;

}

}

if(strImageType == 2 && document.getElementById("MultiPageTIFF").checked){

WebTWAIN.HTTPUploadAllThroughPostAsMultiPageTIFF(

strHTTPServer, 

strActionPage,

document.getElementById("txt_fileName").value + document.getElementsByName("ImageType").item(i).value);

if(WebTWAIN.ErrorCode == 0)

document.getElementById("frmScan").submit();

}

else if(strImageType == 4 && document.getElementById("MultiPagePDF").checked){

WebTWAIN.HTTPUploadAllThroughPostAsPDF(

strHTTPServer, 

strActionPage,

document.getElementById("txt_fileName").value + document.getElementsByName("ImageType").item(i).value);

if(WebTWAIN.ErrorCode == 0)

document.getElementById("frmScan").submit();

}

else{

WebTWAIN.HTTPUploadThroughPostEx(

strHTTPServer, 

WebTWAIN.CurrentImageIndexInBuffer,

strActionPage,

document.getElementById("txt_fileName").value + document.getElementsByName("ImageType").item(i).value,

strImageType);

if(WebTWAIN.ErrorCode == 0)

document.getElementById("frmScan").submit();

}

CheckErrorString();			

}

/*------------------radio response----------------------------*/

function rdTIFFsave_onclick(){

document.getElementById("MultiPageTIFF_save").disabled = false;

    

document.getElementById("MultiPageTIFF_save").checked = false;

document.getElementById("MultiPagePDF_save").checked = false;

document.getElementById("MultiPagePDF_save").disabled = true;

}

function rdPDFsave_onclick(){

document.getElementById("MultiPagePDF_save").disabled = false;



document.getElementById("MultiPageTIFF_save").checked = false;

document.getElementById("MultiPagePDF_save").checked = false;

document.getElementById("MultiPageTIFF_save").disabled = true;

}

function rdsave_onclick(){

document.getElementById("MultiPageTIFF_save").checked = false;

document.getElementById("MultiPagePDF_save").checked = false;

    

document.getElementById("MultiPageTIFF_save").disabled = true;

document.getElementById("MultiPagePDF_save").disabled = true;

}

function rdTIFF_onclick(){

document.getElementById("MultiPageTIFF").disabled = false;

    

document.getElementById("MultiPageTIFF").checked = false;

document.getElementById("MultiPagePDF").checked = false;

document.getElementById("MultiPagePDF").disabled = true;

}

function rdPDF_onclick(){

document.getElementById("MultiPagePDF").disabled = false;



document.getElementById("MultiPageTIFF").checked = false;

document.getElementById("MultiPagePDF").checked = false;

document.getElementById("MultiPageTIFF").disabled = true;

}

function rd_onclick(){

document.getElementById("MultiPageTIFF").checked = false;

document.getElementById("MultiPagePDF").checked = false;

    

document.getElementById("MultiPageTIFF").disabled = true;

document.getElementById("MultiPagePDF").disabled = true;

}

/*------------------select menu response----------------------------*/



function slPreviewMode(){

    WebTWAIN.SetViewMode(parseInt(document.all.PreviewMode.selectedIndex + 1),parseInt(document.all.PreviewMode.selectedIndex+1));

}

function DynamicWebTwain1_OnPostTransfer(){ 

    if(document.getElementById("DiscardBlank").checked == true) {

    var NewlyScannedImage = WebTWAIN.CurrentImageIndexInBuffer;

    if(WebTWAIN.IsBlankImage(NewlyScannedImage))

    WebTWAIN.RemoveImage(NewlyScannedImage);

    }

    document.getElementById("TotalImage").value = WebTWAIN.HowManyImagesInBuffer;

    document.getElementById("CurrentImage").value = WebTWAIN.CurrentImageIndexInBuffer+1;	

}




function OnPostLoadfunction(path, name, type) {

    if(document.getElementById("DiscardBlank").checked == true) {

        var NewlyScannedImage = WebTWAIN.CurrentImageIndexInBuffer;

        if(WebTWAIN.IsBlankImage(NewlyScannedImage))

            WebTWAIN.RemoveImage(NewlyScannedImage);

    }

    document.getElementById("TotalImage").value = WebTWAIN.HowManyImagesInBuffer;

    document.getElementById("CurrentImage").value = WebTWAIN.CurrentImageIndexInBuffer + 1;

}



function DynamicWebTwain1_OnPostAllTransfers() {

    WebTWAIN.CloseSource();

}



function DynamicWebTwain1_OnMouseClick(index) {

    WebTWAIN.CurrentImageIndexInBuffer = index;

    document.getElementById("CurrentImage").value = index+1;		

}



//function btnfaq_onclick(){

//   window.showModalDialog("online_demo_faq.aspx", "", "");

//}



function btnfaq_onclick(){

    window.open('online_demo_faq.aspx', '_self', 'width=1000,height=700, scrollbars=yes', true);

}

//-->

</script>



<script language="javascript" for="DynamicWebTwain1" event="OnPostTransfer">

<!--

DynamicWebTwain1_OnPostTransfer();

//-->

</script>



<script language="javascript" for="DynamicWebTwain1" event="OnPostAllTransfers">

<!--

DynamicWebTwain1_OnPostAllTransfers();

//-->

</script>



<script language="javascript" for="DynamicWebTwain1" event="OnMouseClick(index)">

<!--

DynamicWebTwain1_OnMouseClick(index);

//-->

</script>

<script language="javascript" for="DynamicWebTwain1" event="OnPostLoad(path, name, type)">

<!--

 OnPostLoadfunction(path, name, type);

//-->

</script>

</html>

