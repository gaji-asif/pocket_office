var WebTWAIN;
var ua;
var em = "";
var seed;
var objEmessage;
var re;
var strre;
var ileft, itop, iright, ibottom;
var timeout;
window.onload = Pageonload;
//====================Page Onload  Start==================//

function Pageonload() {
    var strObjectFF = "";
    strObjectFF = " <embed style='display: inline' id='mainDynamicWebTWAINnotIE' type='Application/DynamicWebTwain-Plugin'";
    strObjectFF += " OnPostTransfer='DynamicWebTwain_OnPostTransfer' OnPostAllTransfers='DynamicWebTwain_OnPostAllTransfers'";
    strObjectFF += " OnMouseClick='DynamicWebTwain_OnMouseClick'  OnPostLoad='DynamicWebTwain_OnPostLoadfunction'";
    strObjectFF += " OnImageAreaSelected = 'DynamicWebTwain_OnImageAreaSelected'";
    strObjectFF += " OnImageAreaDeSelected = 'DynamicWebTwain_OnImageAreaDeselected'";
    strObjectFF += " OnMouseDoubleClick = 'DynamicWebTwain_OnMouseDoubleClick'";
    strObjectFF += " OnMouseRightClick = 'DynamicWebTwain_OnMouseRightClick'";
    strObjectFF += " OnTopImageInTheViewChanged = 'DynamicWebTwain_OnTopImageInTheViewChanged'";
    strObjectFF += " class='divcontrol' pluginspage='DynamicWebTWAIN/DynamicWebTwain.xpi'></embed>";
    var strObject = "";
    strObject = "   <param name='_cx' value='3784' />";
    strObject += "	<param name='_cy' value='4128' />";
    strObject += "	<param name='JpgQuality' value='80' />";
    strObject += "	<param name='Manufacturer' value='DynamSoft Corporation' />";
    strObject += "	<param name='ProductFamily' value='Dynamic Web TWAIN' />";
    strObject += "	<param name='ProductName' value='Dynamic Web TWAIN' />";
    strObject += "	<param name='VersionInfo' value='Dynamic Web TWAIN 6.3.1' />";
    strObject += "	<param name='TransferMode' value='0' />";
    strObject += "	<param name='BorderStyle' value='0' />";
    strObject += "	<param name='FTPUserName' value='' />";
    strObject += "	<param name='FTPPassword' value='' />";
    strObject += "	<param name='FTPPort' value='21' />";
    strObject += "	<param name='HTTPUserName' value='' />";
    strObject += "	<param name='HTTPPassword' value='' />";
    strObject += "	<param name='HTTPPort' value='80' />";
    strObject += "	<param name='ProxyServer' value='' />";
    strObject += "	<param name='IfDisableSourceAfterAcquire' value='0' />";
    strObject += "	<param name='IfShowUI' value='-1' />";
    strObject += "	<param name='IfModalUI' value='-1' />";
    strObject += "	<param name='IfTiffMultiPage' value='0' />";
    strObject += "	<param name='IfThrowException' value='0' />";
    strObject += "	<param name='MaxImagesInBuffer' value='1' />";
    strObject += "	<param name='TIFFCompressionType' value='0' />";
    strObject += "	<param name='IfFitWindow' value='-1' />";
    strObject += "	<param name='IfSSL' value='0' />";
    strObject += "	</object>";
    if(ExplorerType() == "IE" && navigator.userAgent.indexOf("Win64") != -1 && navigator.userAgent.indexOf("x64") != -1) {
        strObject = "<object id='mainDynamicWebTwainIE' codebase='DynamicWebTWAIN/DynamicWebTWAINx64.cab#version=6,3,1' class='divcontrol' " + "classid='clsid:FFC6F181-A5CF-4ec4-A441-093D7134FBF2' viewastext> " + strObject;
        var objDivx64 = document.getElementById("maindivIEx64");
        objDivx64.style.display = "inline";
        objDivx64.innerHTML = strObject;
        var obj = document.getElementById("maindivPlugin");
        obj.style.display = "none";
        WebTWAIN = document.getElementById("mainDynamicWebTwainIE");
        document.getElementById("64bitsamplesource").style.display = "inline";
        document.getElementById("32bitsamplesource").style.display = "none";
    }
    else if(ExplorerType() == "IE" && (navigator.userAgent.indexOf("Win64") == -1 || navigator.userAgent.indexOf("x64") == -1)) {
        strObject = "<object id='mainDynamicWebTwainIE' codebase='DynamicWebTWAIN/DynamicWebTWAIN.cab#version=6,3,1' class='divcontrol' " + "classid='clsid:FFC6F181-A5CF-4ec4-A441-093D7134FBF2' viewastext> " + strObject;
        var objDivx86 = document.getElementById("maindivIEx86");
        objDivx86.style.display = "inline";
        objDivx86.innerHTML = strObject;
        var obj = document.getElementById("maindivPlugin");
        obj.style.display = "none";
        WebTWAIN = document.getElementById("mainDynamicWebTwainIE");
    }
    else {
        var objDivFF = document.getElementById("mainControlInstalled");
        objDivFF.innerHTML = strObjectFF;
        var obj = document.getElementById("maindivIE");
        obj.style.display = "none";
        var obj = document.getElementById("maindivPlugin");
        obj.style.display = "inline";
        WebTWAIN = document.getElementById("mainDynamicWebTWAINnotIE");
    }
    seed = setInterval(ControlDetect, 500);
}

function ExplorerType() {
    ua = (navigator.userAgent.toLowerCase());
    if(ua.indexOf("msie") != -1) {
        return "IE";
    }
    else {
        return "notIE";
    }
}

function pause() {
    clearInterval(seed);
}

function ControlDetect() {
    if(WebTWAIN.ErrorCode == 0) {
        pause();
        var i;
        document.getElementById("source").options.length = 0;
        WebTWAIN.OpenSourceManager();

		lastScanner = getCookie('lastScanner');

        for (i = 0; i < WebTWAIN.SourceCount; i++) {
			selectedItem = false;
			if(lastScanner != null && i == lastScanner)
			{
				selectedItem = true;
			}
            document.getElementById("source").options.add(new Option(WebTWAIN.SourceNameItems(i), i, selectedItem, selectedItem));
        }
        WebTWAIN.MaxImagesInBuffer = 4096;
        WebTWAIN.MouseShape = false;
        document.getElementById("Resolution").options.length = 0;
        document.getElementById("Resolution").options.add(new Option("100", 100));
        document.getElementById("Resolution").options.add(new Option("150", 150));
        document.getElementById("Resolution").options.add(new Option("200", 200));
        document.getElementById("Resolution").options.add(new Option("300", 300));

        document.getElementById("InterpolationMethod").options.length = 0;
        document.getElementById("InterpolationMethod").options.add(new Option("NearestNeighbor", 1));
        document.getElementById("InterpolationMethod").options.add(new Option("Bilinear", 2));
        document.getElementById("InterpolationMethod").options.add(new Option("Bicubic", 3));

        document.getElementById("txt_fileNameforSave").value = "WebTWAINImage";
        document.getElementById("txt_fileName").value = "WebTWAINImage";

        document.getElementById("ADF").checked = true;
        document.getElementById("MultiPageTIFF_save").disabled = true;
        document.getElementById("MultiPagePDF_save").disabled = true;
        document.getElementById("MultiPageTIFF").disabled = true;
        document.getElementById("MultiPagePDF").disabled = true;

        document.getElementById("PreviewMode").options.length = 0;
        document.getElementById("PreviewMode").options.add(new Option("1X1", 0));
        document.getElementById("PreviewMode").options.add(new Option("2X2", 1));
        document.getElementById("PreviewMode").options.add(new Option("3X3", 2));
        document.getElementById("PreviewMode").options.add(new Option("4X4", 3));
        document.getElementById("PreviewMode").options.add(new Option("5X5", 4));
        document.getElementById("PreviewMode").selectedIndex = 0;

        document.getElementById("pNoScanner").style.display = "block";
        document.getElementById("pNoScanner").style.textAlign = "center";

        objEmessage = document.getElementById("emessage");

        re = /^\d+$/;
        strre = /^\w+$/;

        ileft = 0;
        itop = 0;
        iright = 0;
        ibottom = 0;

        for (var i = 0; i < document.links.length; i++) {
            if(document.links[i].className == "ShowtblLoadImage") {
                document.links[i].onclick = ShowtblLoadImage_onclick;
            }
            if(document.links[i].className == "ClosetblLoadImage") {
                document.links[i].onclick = ClosetblLoadImage_onclick;
            }
        }
        var allinputs = document.getElementsByTagName("input");
        var j = 0;
        for (var i = 0; i < allinputs.length; i++) {
            if(allinputs[i].type == "text") {
                allinputs[i].onkeyup = function () {
                    if(event.keyCode != 37 && event.keyCode != 39) value = value.replace(/\D/g, '');
                }
            }
        }
        objEmessage.ondblclick = function () {
            em = "";
            this.innerHTML = "";
        }
        if(ExplorerType() == "IE") {
            WebTWAIN.attachEvent('OnPostTransfer', DynamicWebTwain_OnPostTransfer);
            WebTWAIN.attachEvent('OnPostAllTransfers', DynamicWebTwain_OnPostAllTransfers);
            WebTWAIN.attachEvent('OnMouseClick', DynamicWebTwain_OnMouseClick);
            WebTWAIN.attachEvent('OnPostLoad', DynamicWebTwain_OnPostLoadfunction);
            WebTWAIN.attachEvent('OnImageAreaSelected', DynamicWebTwain_OnImageAreaSelected);
            WebTWAIN.attachEvent('OnMouseDoubleClick', DynamicWebTwain_OnMouseDoubleClick);
            WebTWAIN.attachEvent('OnMouseRightClick', DynamicWebTwain_OnMouseRightClick);
            WebTWAIN.attachEvent('OnTopImageInTheViewChanged', DynamicWebTwain_OnTopImageInTheViewChanged);
            WebTWAIN.attachEvent('OnImageAreaDeSelected', DynamicWebTwain_OnImageAreaDeselected);
        }
        //WebTWAIN.LogLevel = 1; // added in 6.3.1 (2011-09-02)
    }
    else {
        if(ua.match(/chrome\/([\d.]+)/) || ua.match(/opera.([\d.]+)/) || ua.match(/version\/([\d.]+).*safari/)) {
            document.getElementById("mainControlNotInstalled").style.display = "inline";
            document.getElementById("mainControlInstalled").style.display = "none";
        }
    }
    timeout = setTimeout(function(){},10);
}
function ShowtblLoadImage_onclick() {
    switch (document.getElementById("tblLoadImage").style.visibility) {
        case "hidden": document.getElementById("tblLoadImage").style.visibility = "visible";
            break;
        case "visible": document.getElementById("tblLoadImage").style.visibility = "hidden";
            break;
        default: break;
    }
    document.getElementById("tblLoadImage").style.top = ds_gettop(document.getElementById("pNoScanner")) + document.getElementById("pNoScanner").offsetHeight + "px";
    document.getElementById("tblLoadImage").style.left = ds_getleft(document.getElementById("pNoScanner")) + 0 + "px";
    return false;
}
function ClosetblLoadImage_onclick() {
    document.getElementById("tblLoadImage").style.visibility = "hidden";
    return false;
}
//====================Page Onload End====================//

//====================Frequently Used Functions=======================//

function CheckIfImagesInBuffer() {
    if(WebTWAIN.HowManyImagesInBuffer == 0) {
        em = em + "There is no image in buffer.<br />";
        objEmessage.innerHTML = em;
        return false;
    }
    else {
        return true;
    }
}

function CheckErrorString() {
    if(WebTWAIN.ErrorCode == 0) {
        em = em + "<span style='color:#cE5E04'><b>" + WebTWAIN.ErrorString + "</b></span><br />";
        objEmessage.innerHTML = em;
        objEmessage.scrollTop = objEmessage.scrollHeight;
        return true;
    }
    else {
        if(WebTWAIN.ErrorCode == -2003) {
            var ErrorMessageWin = window.open("", "ErrorMessage", "height=500,width=750,top=0,left=0,toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no, status=no");
            ErrorMessageWin.document.writeln(WebTWAIN.HTTPPostResponseString);
        }
        em = em + "<span style='color:#cE5E04'><b>" + WebTWAIN.ErrorString + "</b></span><br />";
        objEmessage.innerHTML = em;
        objEmessage.scrollTop = objEmessage.scrollHeight;
        return false;
    }
}

function ds_getleft(el) {
    var tmp = el.offsetLeft;
    el = el.offsetParent
    while (el) {
        tmp += el.offsetLeft;
        el = el.offsetParent;
    }
    return tmp;
}
function ds_gettop(el) {
    var tmp = el.offsetTop;
    el = el.offsetParent
    while (el) {
        tmp += el.offsetTop;
        el = el.offsetParent;
    }
    return tmp;
}

function UpdatePageInfo(){
    document.getElementById("TotalImage").value = WebTWAIN.HowManyImagesInBuffer;
    document.getElementById("CurrentImage").value = WebTWAIN.CurrentImageIndexInBuffer + 1;
}

//====================Preview Group Start====================//
function btnFirstImage_onclick() {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    WebTWAIN.CurrentImageIndexInBuffer = 0;
    UpdatePageInfo();
}
function btnPreImage_onclick() {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    else if(WebTWAIN.CurrentImageIndexInBuffer == 0) {
        return;
    }
    WebTWAIN.CurrentImageIndexInBuffer = WebTWAIN.CurrentImageIndexInBuffer - 1;
    UpdatePageInfo();
}
function btnNextImage_onclick() {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    else if(WebTWAIN.CurrentImageIndexInBuffer == WebTWAIN.HowManyImagesInBuffer - 1) {
        return;
    }
    WebTWAIN.CurrentImageIndexInBuffer = WebTWAIN.CurrentImageIndexInBuffer + 1;
    UpdatePageInfo();
}
function btnLastImage_onclick() {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    WebTWAIN.CurrentImageIndexInBuffer = WebTWAIN.HowManyImagesInBuffer - 1;
    UpdatePageInfo();
}

function btnRemoveCurrentImage_onclick() {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    WebTWAIN.RemoveAllSelectedImages();
    if(WebTWAIN.HowManyImagesInBuffer == 0) {
        document.getElementById("TotalImage").value = WebTWAIN.HowManyImagesInBuffer;
        document.getElementById("CurrentImage").value = "";
        return;
    }
    else {
        UpdatePageInfo();
    }
}
function btnRemoveAllImages_onclick() {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    WebTWAIN.RemoveAllImages();
    document.getElementById("TotalImage").value = "0";
    document.getElementById("CurrentImage").value = "";
}
function slPreviewMode() {
    WebTWAIN.SetViewMode(parseInt(document.getElementById("PreviewMode").selectedIndex + 1), parseInt(document.getElementById("PreviewMode").selectedIndex + 1));
    if(document.getElementById("PreviewMode").selectedIndex != 0) {
        WebTWAIN.MouseShape = true;
    }
    else {
        WebTWAIN.MouseShape = false;
    }
}
//====================Preview Group End====================//

//====================Get Image Group Start=====================//

/*------------------Scan Image--------------------------*/
function btnScan_onclick() {
    WebTWAIN.SelectSourceByIndex(document.getElementById("source").selectedIndex);
    WebTWAIN.CloseSource();
    WebTWAIN.OpenSource();
    WebTWAIN.IfShowUI = document.getElementById("ShowUI").checked;

    var i;
    for(i=0;i<3;i++)
    {
        if(document.getElementsByName("PixelType").item(i).checked==true)
        WebTWAIN.PixelType = i;
    }
    WebTWAIN.Resolution = document.getElementById("Resolution").value;
    WebTWAIN.IfFeederEnabled = document.getElementById("ADF").checked ;
    WebTWAIN.IfDuplexEnabled = document.getElementById("Duplex").checked ;
    em = em +"Pixel Type: " + WebTWAIN.PixelType + "<br />Resolution: " + WebTWAIN.Resolution + "<br />" ;
    objEmessage.innerHTML = em;
    objEmessage.scrollTop = objEmessage.scrollHeight;
    WebTWAIN.IfDisableSourceAfterAcquire = true;
    WebTWAIN.AcquireImage();
}

/*-----------------Load Image---------------------*/
function btnLoad_onclick() {
    WebTWAIN.IfShowFileDialog = true;
    WebTWAIN.LoadImageEx("", 5);
    clearTimeout(timeout);
    timeout = setTimeout(function () {
        document.getElementById("tblLoadImage").style.visibility = "hidden";
    }, 1000);
}

//====================Get Image Group End=====================//

//====================Edit Image Group Start=====================//

function btnShowImageEditor_onclick() {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    WebTWAIN.ShowImageEditor();
}

function btnRotateRight_onclick() {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    WebTWAIN.RotateRight(WebTWAIN.CurrentImageIndexInBuffer);
}
function btnRotateLeft_onclick() {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    WebTWAIN.RotateLeft(WebTWAIN.CurrentImageIndexInBuffer);
}

function btnMirror_onclick() {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    WebTWAIN.Mirror(WebTWAIN.CurrentImageIndexInBuffer);
}
function btnFlip_onclick() {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    WebTWAIN.Flip(WebTWAIN.CurrentImageIndexInBuffer);
}

/*----------------------Crop Method---------------------*/
function btnCrop_onclick() {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    if(ileft != 0 || itop != 0 || iright != 0 || ibottom != 0) {
        WebTWAIN.Crop(
            WebTWAIN.CurrentImageIndexInBuffer,
            ileft, itop, iright, ibottom
        );
        ileft = 0;
        itop = 0;
        iright = 0;
        ibottom = 0;
        return;
    }
    switch (document.getElementById("Crop").style.visibility) {
        case "visible": document.getElementById("Crop").style.visibility = "hidden"; break;
        case "hidden": document.getElementById("Crop").style.visibility = "visible"; break;
        default: break;
    }
    document.getElementById("Crop").style.top = ds_gettop(document.getElementById("btnCrop")) + document.getElementById("btnCrop").offsetHeight + "px";
    document.getElementById("Crop").style.left = ds_getleft(document.getElementById("btnCrop")) - 80 + "px";
}

function btnCropCancel_onclick() {
    document.getElementById("Crop").style.visibility = "hidden";
}
function btnCropOK_onclick() {
    document.getElementById("img_left").className = "";
    document.getElementById("img_top").className = "";
    document.getElementById("img_right").className = "";
    document.getElementById("img_bottom").className = "";
    if(!re.test(document.getElementById("img_left").value)) {
        document.getElementById("img_left").className += " invalid";
        document.getElementById("img_left").focus();
        em = em +"Please input a valid <b>left</b> value.<br />" ;
        objEmessage.innerHTML = em;
        objEmessage.scrollTop = objEmessage.scrollHeight;
        return;
    }
    if(!re.test(document.getElementById("img_top").value)) {
        document.getElementById("img_top").className += " invalid";
        document.getElementById("img_top").focus();
        em = em +"Please input a valid <b>top</top> value.<br />" ;
        objEmessage.innerHTML = em;
        objEmessage.scrollTop = objEmessage.scrollHeight;
        return;
    }
    if(!re.test(document.getElementById("img_right").value)) {
        document.getElementById("img_right").className += " invalid";
        document.getElementById("img_right").focus();
        em = em +"Please input a valid <b>right</b> value.<br />" ;
        objEmessage.innerHTML = em;
        objEmessage.scrollTop = objEmessage.scrollHeight;
        return;
    }
    if(!re.test(document.getElementById("img_bottom").value)) {
        document.getElementById("img_bottom").className += " invalid";
        document.getElementById("img_bottom").focus();
        em = em +"Please input a valid <b>bottom</b> value.<br />" ;
        objEmessage.innerHTML = em;
        objEmessage.scrollTop = objEmessage.scrollHeight;
        return;
    }
    WebTWAIN.Crop(
        WebTWAIN.CurrentImageIndexInBuffer,
        document.getElementById("img_left").value,
        document.getElementById("img_top").value,
        document.getElementById("img_right").value,
        document.getElementById("img_bottom").value
    );
}

/*----------------Change Image Size--------------------*/
function btnChangeImageSize_onclick(){
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    switch (document.getElementById("ImgSizeEditor").style.visibility) {
        case "visible": document.getElementById("ImgSizeEditor").style.visibility = "hidden"; break;
        case "hidden": document.getElementById("ImgSizeEditor").style.visibility = "visible"; break;
        default: break;
    }
    document.getElementById("ImgSizeEditor").style.top = ds_gettop(document.getElementById("btnChangeImageSize")) + document.getElementById("btnChangeImageSize").offsetHeight + "px";
    document.getElementById("ImgSizeEditor").style.left = ds_getleft(document.getElementById("btnChangeImageSize")) - 30 + "px";
}
function btnCancelChange_onclick() {
    document.getElementById("ImgSizeEditor").style.visibility="hidden";
}

function btnChangeImageSizeOK_onclick() {
    document.getElementById("img_height").className = "";
    document.getElementById("img_width").className = "";
    if(!re.test(document.getElementById("img_height").value)) {
        document.getElementById("img_height").className += " invalid";
        document.getElementById("img_height").focus();
        em = em +"Please input a valid <b>height</b>.<br />" ;
        objEmessage.innerHTML = em;
        objEmessage.scrollTop = objEmessage.scrollHeight;
        return;
    }
    if(!re.test(document.getElementById("img_width").value)) {
        document.getElementById("img_width").className += " invalid";
        document.getElementById("img_width").focus();
        em = em +"Please input a valid <b>width</b>.<br />" ;
        objEmessage.innerHTML = em;
        objEmessage.scrollTop = objEmessage.scrollHeight;
        return;
    }
    WebTWAIN.ChangeImageSize(
        WebTWAIN.CurrentImageIndexInBuffer,
        document.getElementById("img_width").value,
        document.getElementById("img_height").value,
        document.getElementById("InterpolationMethod").selectedIndex + 1
    );
}

//====================Edit Image Group End==================//


/*-----------------Save Image Group---------------------*/
function btnSave_onclick(){
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    var i,strimgType_save;
    for(i=0;i<5;i++){
        if(document.getElementsByName("imgType_save").item(i).checked == true){
            strimgType_save  = document.getElementsByName("imgType_save").item(i).value;
            break;
        }
    }
    WebTWAIN.IfShowFileDialog = true;
    document.getElementById("txt_fileNameforSave").className = "";
    if(!strre.test(document.getElementById("txt_fileNameforSave").value)) {
        document.getElementById("txt_fileNameforSave").className += " invalid";
        document.getElementById("txt_fileNameforSave").focus();
        em = em +"Please input a valid <b>file name</b>.<br />" ;
        objEmessage.innerHTML = em;
        objEmessage.scrollTop = objEmessage.scrollHeight;
        return;
    }
    var strFilePath = "C:\\" + document.getElementById("txt_fileNameforSave").value + "." + strimgType_save;
    if(strimgType_save == "tif" && document.getElementById("MultiPageTIFF_save").checked) {
        if((WebTWAIN.SelectedImagesCount == 1) || (WebTWAIN.SelectedImagesCount == WebTWAIN.HowManyImagesInBuffer)) {
            WebTWAIN.SaveAllAsMultiPageTIFF(strFilePath);
        }
        else {
            WebTWAIN.SaveSelectedImagesAsMultiPageTIFF(strFilePath);
        }
    }
    else if(strimgType_save == "pdf" && document.getElementById("MultiPagePDF_save").checked) {
        if((WebTWAIN.SelectedImagesCount == 1) || (WebTWAIN.SelectedImagesCount == WebTWAIN.HowManyImagesInBuffer)) {
            WebTWAIN.SaveAllAsPDF(strFilePath);
        }
        else {
            WebTWAIN.SaveSelectedImagesAsMultiPagePDF(strFilePath);
        }
    }
    else {
        switch (i) {
            case 0: WebTWAIN.SaveAsBMP(strFilePath, WebTWAIN.CurrentImageIndexInBuffer); break;
            case 1: WebTWAIN.SaveAsJPEG(strFilePath, WebTWAIN.CurrentImageIndexInBuffer); break;
            case 2: WebTWAIN.SaveAsTIFF(strFilePath, WebTWAIN.CurrentImageIndexInBuffer); break;
            case 3: WebTWAIN.SaveAsPNG(strFilePath, WebTWAIN.CurrentImageIndexInBuffer); break;
            case 4: WebTWAIN.SaveAsPDF(strFilePath, WebTWAIN.CurrentImageIndexInBuffer); break;
        }
    }
	if(CheckErrorString()) {
	    return;
	}
	else {
	    alert(WebTWAIN.ErrorString);
	}
}
/*-------------------------------------------------------*/

/*-----------------Upload Image Group---------------------*/
function btnUpload_onclick(job_id, session_info, database_name){
    if(!CheckIfImagesInBuffer()) {
        return;
    }
    var i, strHTTPServer, strActionPage, strImageType;
    document.getElementById("txt_fileName").className = "";
    if(!strre.test(document.getElementById("txt_fileName").value)) {
        document.getElementById("txt_fileName").className += " invalid";
        document.getElementById("txt_fileName").focus();
        em = em +"please input <b>file name</b>.<br />" ;
        objEmessage.innerHTML = em;
        objEmessage.scrollTop = objEmessage.scrollHeight;
        return;
    }

	//save the last selected scanner
	setCookie('lastScanner', document.getElementById("source").value, 3650);

//    strHTTPServer = "assure.1derfulroofing.com";
    strHTTPServer = GLOBALS.server_name;
    WebTWAIN.HTTPPort = 80;
    var CurrentPathName = unescape(location.pathname);	// get current PathName in plain ASCII
    var CurrentPath = CurrentPathName.substring(0, CurrentPathName.lastIndexOf("/") + 1);
    var strActionPage = CurrentPath + "scan_jobfile2.php?dbname="+database_name+"&id="+job_id+'&session_info='+session_info+'&title='+encodeURIComponent(document.getElementById('fileTitle').value); //the ActionPage's file path
	var redirectURLifOK = CurrentPath + "scan_jobfile3.php?id="+job_id+'&dbname='+database_name;
    for(i=0;i<4;i++){
        if(document.getElementsByName("ImageType").item(i).checked == true){
            strImageType  = i + 1;
            break;
        }
    }

    var uploadfilename = document.getElementById("txt_fileName").value + "." + document.getElementsByName("ImageType").item(i).value;
    if(strImageType == 2 && document.getElementById("MultiPageTIFF").checked) {
        if((WebTWAIN.SelectedImagesCount == 1) || (WebTWAIN.SelectedImagesCount == WebTWAIN.HowManyImagesInBuffer)) {
            WebTWAIN.HTTPUploadAllThroughPostAsMultiPageTIFF(
                strHTTPServer,
                strActionPage,
                uploadfilename
            );
        }
        else {
            WebTWAIN.HTTPUploadThroughPostAsMultiPageTIFF(
                strHTTPServer,
                strActionPage,
                uploadfilename
            );
        }
    }
    else if(strImageType == 4 && document.getElementById("MultiPagePDF").checked) {
        if((WebTWAIN.SelectedImagesCount == 1) || (WebTWAIN.SelectedImagesCount == WebTWAIN.HowManyImagesInBuffer)) {
            WebTWAIN.HTTPUploadAllThroughPostAsPDF(
                strHTTPServer,
                strActionPage,
                uploadfilename
            );
        }
        else {
            WebTWAIN.HTTPUploadThroughPostAsMultiPagePDF(
                strHTTPServer,
                strActionPage,
                uploadfilename
            );
        }
    }
    else {
        WebTWAIN.HTTPUploadThroughPostEx(
            strHTTPServer,
            WebTWAIN.CurrentImageIndexInBuffer,
            strActionPage,
            uploadfilename,
            strImageType
        );
    }
    em = em +"<b>Upload: </b>" ;
    if(CheckErrorString()) {
        window.location = redirectURLifOK;
    }
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

function DynamicWebTwain_OnPostTransfer() {
    if(document.getElementById("DiscardBlank").checked == true) {
        var NewlyScannedImage = WebTWAIN.CurrentImageIndexInBuffer;
        if(WebTWAIN.IsBlankImage(NewlyScannedImage)) {
            WebTWAIN.RemoveImage(NewlyScannedImage);
        }
        em = em +"<b>Blank Discard (On PostTransfer): </b>" ;
        if(CheckErrorString()) {
            UpdatePageInfo();
            return;
        }
    }
    UpdatePageInfo();
}

function DynamicWebTwain_OnPostLoadfunction(path, name, type) {
    if(document.getElementById("DiscardBlank").checked == true) {
        var NewlyScannedImage = WebTWAIN.CurrentImageIndexInBuffer;
        if(WebTWAIN.IsBlankImage(NewlyScannedImage)) {
            WebTWAIN.RemoveImage(NewlyScannedImage);
        }
        em = em +"<b>Blank Discard (On PostLoad): </b>" ;
        if(CheckErrorString()) {
            UpdatePageInfo();
            return;
        }
    }
    UpdatePageInfo();
}

function DynamicWebTwain_OnPostAllTransfers() {
    WebTWAIN.CloseSource();
}

var imageindex;
var imageindex2;
function DynamicWebTwain_OnMouseClick(index) {
    imageindex = index;
    document.getElementById("CurrentImage").value = index + 1;
}

function DynamicWebTwain_OnMouseRightClick(index2) {
    if(!CheckIfImagesInBuffer()) {
        return;
    }
}

function DynamicWebTwain_OnImageAreaSelected(index, left, top, right, bottom) {
    ileft = left;
    itop = top;
    iright = right;
    ibottom = bottom;
}

function DynamicWebTwain_OnImageAreaDeselected(index) {
    ileft = 0;
    itop = 0;
    iright = 0;
    ibottom = 0;
}

function DynamicWebTwain_OnMouseDoubleClick() {
    var StrextraInfo;
    StrextraInfo = "Image Width: " + WebTWAIN.GetImageWidth(WebTWAIN.CurrentImageIndexInBuffer) +
        " Image Height: " + WebTWAIN.GetImageHeight(WebTWAIN.CurrentImageIndexInBuffer) +
        " Image Bit Depth: " + WebTWAIN.GetImageBitDepth(WebTWAIN.CurrentImageIndexInBuffer);
    document.getElementById("extraInfo").innerHTML = StrextraInfo;
    clearTimeout(timeout);
    timeout = setTimeout(function () {
        document.getElementById("extraInfo").innerHTML = "";
    }, 10000);
}

function DynamicWebTwain_OnTopImageInTheViewChanged(index) {
    document.getElementById("CurrentImage").value = index + 1;
}

//from w3schools
function setCookie(c_name,value,exdays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}

function getCookie(c_name)
{
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++)
	{
	  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
	  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
	  x=x.replace(/^\s+|\s+$/g,"");
	  if(x==c_name)
		{
		return unescape(y);
		}
	  }
}