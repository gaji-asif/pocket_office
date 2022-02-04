/****** GLOBALS ******/
var GLOBALS = {};
GLOBALS.journals_being_processed = [];
GLOBALS.uploads_being_processed = [];
GLOBALS.subscribers_being_processed = [];
GLOBALS.sheets_being_processed = [];
GLOBALS.watches_being_processed = [];

//widget offset
var window_height_offset = 110;



//global ajax settings
$.ajaxSetup({
  timeout: 0
});

/****** DOCUMENT READY ******/
$(document).ready(function(){
    //bindings
    Bindings.setAllBindings();
    Bindings.setupUI();
    Jobs.setBindings();
    Tooltips.setBindings();
    
    //dropzone
    if(Modernizr.draganddrop) {
        Dropzone.init();
    }

	//input masking
	$('.masked-phone').inputmask('(999) 999-9999',{placeholder: ' '});
});

function resizeAppIframes() {
    var height = $(window).height() - 29;
    var width = $(window).width() - $('#sidebar').outerWidth();
    
    $('.app-iframe').height(height).width(width);
}

/****** WINDOW LOAD ******/
$(window).load(resizeAppIframes);

/****** WINDOW RESIZE ******/
//$(window).resize(_.debounce(resizeAppIframes, 500));
$(window).resize(resizeAppIframes);

function initAllWidgets() {
	//get all widget containers
	var widget_containers = $('div.widget-container');

	//empty array to store intervals
	GLOBALS.widget_intervals = [];

	widget_containers.each(function(index, widget) {
		//wrap widget in jQuery object
		widget = $(widget);

		//get widget data
		var destination = widget.attr('id');
		var machine_hook = destination.replace('-', '_');

		//make initial ajax request
		Request.make(GLOBALS.ajax_dir + '/' + machine_hook + '.php', destination, true, true, function(){
			//set interval
			GLOBALS.widget_intervals[machine_hook] = setInterval(
				function(){
					Request.make(GLOBALS.ajax_dir + '/' + machine_hook + '.php', destination, false, true);
				}, GLOBALS.ao_refresh
			);
		});
	});
}

function resizeAllWidgets() {
	//get all widget containers and number of containers
	var widget_containers = $('div.widget-container');
	var count = widget_containers.length;

    if(count % 2 !== 0) {
        widget_containers.last().parent().closest('div').css('width', '100%');
    }

	//get number of rows
	var rows = Math.ceil(count/2);

	//calculate height
	var widget_height = ($(window).height() - window_height_offset) / rows;

	//set height
	widget_containers.height(widget_height);
    
    //fade dashboard in
    $('body.dashboard').fadeIn();
}

function killAllWidgetIntervals() {
	$.each(GLOBALS.widget_intervals, function(index, interval) {
		clearInterval(interval);
	});
}

function filterList(url, element) {
	clearElement('notes');

    var values = {};
    $('.list-filter-input').each(function(){
        values[$(this).attr('id')] = $(this).val();
    });

    Request.make(url + '?' + $.param(values), element, true, true);
    
    	 /////////////////////// http://xactbid.pocketofficepro.com/
	document.getElementById("urlResult").innerHTML = 'http://xactbid.pocketofficepro.com/schedule.php?schedule='+url + '?' + $.param(values);
	document.getElementById("urlResultText").value = 'http://xactbid.pocketofficepro.com/schedule.php?schedule='+url + '?' + $.param(values);
	urlSet();
	//////////////////////
}

function resetFilterListInputs() {
    $('.list-filter-input').each(function(){
		if(!$(this).hasClass('ignore-filter-reset'))
		{
			$(this).val('');
		}
    });
}

function switchJobTab(tab) {
    $('.job-tab-content').hide();
    $('.job-tab-link').removeClass('active');
    $('.job-tab-content.' + tab).show();
    $('.job-tab-link.' + tab).addClass('active');
}

function closeAllQuickSettings(job_id) {
    $('#btm_spacer').hide();

    //reload job row if needed
    if(job_id !== null)
    {
        Request.make('includes/ajax/get_jobrow.php?id=' + job_id, 'jobrow' + job_id, false, true);
    }
    $('.quick-settings-container').hide().empty();
}

function showQuickSettings(job_id) {
    $('#btm_spacer').show();

    closeAllQuickSettings();
    var quick_settings_continer_id = 'quick-settings-container-job' + job_id;
    Request.make('includes/ajax/get_quicksettings.php?id=' + job_id, quick_settings_continer_id, false, true);

	//show container
	$('#' + quick_settings_continer_id).show();

	//scroll to row with 40px offset
	$.scrollTo('#' + quick_settings_continer_id, {
		duration: 500,
		offset: {top: -40}
	});

}

function saveQuickSettings(job_id, button) {
    //set button text
    $(button).val('Saving...');

    pif = document.getElementById(job_id+'pif');
    approved = document.getElementById(job_id+'approved');
    refpaid = document.getElementById(job_id+'refpaid');
    confirmed = document.getElementById(job_id+'confirmed');
    stage = document.getElementById(job_id+'stage');

    if(pif!=null)
        pif_value = pif.checked;
    else pif_value = "null";
    if(approved!=null)
        approved_value = approved.checked
    else approved_value = "null";
    if(refpaid!=null)
        refpaid_value = refpaid.checked;
    else refpaid_value = "null";
    if(confirmed!=null)
        confirmed_value = confirmed.checked;
    else confirmed_value = "null";
    if(stage!=null)
        stage_value = stage.value;
    else stage_value = "null";

    Request.make("includes/ajax/save_quicksettings.php?id="+job_id+"&pif="+pif_value+"&approved="+approved_value+"&refpaid="+refpaid_value+"&confirmed="+confirmed_value+"&stage="+stage_value,"","","", function(){
        closeAllQuickSettings(job_id);
    });
}

function killAllOverlays() {
    $('#mymessages, #mynotifications').remove();
}

function myVal(element) {
    return document.getElementById(element).value;
}

function getTaskSpecificUsersEdit(task_list, user_list, on_change, cur_user_id) {
    task_id = document.getElementById(task_list).value;
    url = 'get_taskspecificusers.php?task='+task_id+'&onchange='+on_change+'&userid='+cur_user_id;
    Request.make(url, user_list, false, true);

    getContractorSchedule();
}

function getTaskSpecificUsers(task_list, user_list, on_change, cur_user_id) {
    task_id = document.getElementById(task_list).value;
    url = 'get_taskspecificusers.php?task='+task_id+'&onchange='+on_change+'&userid='+cur_user_id;
    Request.make(url, user_list, false, true);
}

function deleteRecipient() {
    var currentRecipientsInput = $('input[name="recipients"]'),
        currentRecipients = currentRecipientsInput.val(),
        recipientId = $(this).data('user-id');
    currentRecipients = currentRecipients.length ? currentRecipients.split(',') : [];
    
    //remove from array and set value
    currentRecipients = _.without(currentRecipients, String(recipientId));
    currentRecipientsInput.val(currentRecipients.join());
    
    //update view
    printRecipientsList(currentRecipients);
}

function addRecipient() {
    var recipientId = $('#recipients').val(),
        currentRecipientsInput = $('input[name="recipients"]'),
        currentRecipients = currentRecipientsInput.val();
    currentRecipients = currentRecipients.length ? currentRecipients.split(',') : [];
    
    //reset select list
    $('#recipients').val('');
    
    //has it been added?
    if(_.indexOf(currentRecipients, recipientId) !== -1 || !recipientId.length) {
        return;
    }
    
    //set value
    currentRecipients.push(recipientId);
    currentRecipientsInput.val(currentRecipients.join());
    
    //update view
    printRecipientsList(currentRecipients);
}

function printRecipientsList(recipients) {
    var recipientsList = $('#recipients-list');
    recipientsList.empty();
    
    _.each(recipients, function(recipientId) {
        var name = $('#recipients option[value="' + recipientId + '"]').text();
        recipientsList.append('<div><i class="icon-remove cursor-pointer" rel="remove-journal-recipient" data-user-id="'+ recipientId +'"></i>&nbsp;' + name + '</>');
    });
}

function setReferralPaid(referralEl, selector) {
    var paidEl = $(selector);
    referralEl = $(referralEl);

    if(referralEl.val().length) {
        paidEl.removeAttr('disabled');
    } else {
        paidEl.attr('checked', false);
        paidEl.attr('disabled', true);
    }
}

function setInsuranceApproval(approval,claim_obj)
{
    approval_obj = document.getElementById(approval);

    if(claim_obj.value!='')
        approval_obj.checked=true;
    else approval_obj.checked=false;
}

function jobStatsTicker()
{
    Request.make('includes/ajax/job_statsticker.php', 'notes', false, true);
}

function clearElement(id)
{
	var element = $('#' + id);
    if(element.length != 0)
	{
		element.empty();
	}
}

function confirmOrder(sheet_id, job_id)
{
    Request.make('includes/ajax/get_job.php?sheet_id='+sheet_id+'&id='+job_id+'&action=confirm', 'jobscontainer', true, true);
}

function unconfirmOrder(sheet_id, job_id)
{
    Request.make('includes/ajax/get_job.php?sheet_id='+sheet_id+'&id='+job_id+'&action=unconfirm', 'jobscontainer', true, true);
}

function setNav(id)
{
}

function setScheduleFromProx(d, m, y)
{
    if(d!=''&&m!=''&&y!='')
    {
        d_input = document.getElementById('startdate_Day_ID');
        m_input = document.getElementById('startdate_Month_ID');
        y_input = document.getElementById('startdate_Year_ID');
        date_input = document.getElementById('startdate');


        y_input.value = y;
        m_input.value = m;
        k.changeMonth(m_input);
        d_input.value = d;

        m++;

        date_input.value = y+"-"+m+"-"+d;

        var m = '' + m;
        if(m.length==1)
            m = '0'+m;
        var d = '' + d;
        if(d.length==1)
            d = '0'+d;
        getContractorScheduleRaw(y+'-'+m+'-'+d);
    }
}

function getContractorSchedule() {
    var date = $('[name="startdate"]').length ? $('[name="startdate"]').val() : $('[name="start_date"]').val(),
        contractorId = $('[name="contractor"]').val();

    Request.make('prox_scheduler.php?id=' + contractorId + '&d=' + date, 'proximityschedule', false, true);
}

function getContractorScheduleRaw(date)
{
    contractor_id = document.getElementById('contractor').value;

    Request.make('prox_scheduler.php?id='+contractor_id+'&d='+date, 'proximityschedule', false, true);
}

function applyParentOverlay(src) {
    applyOverlay(src, true);
}

function applyOverlay(src, toParent) {
    //delete overlay, if exists
    deleteOverlay();

    if(String(src).substr(0, 16) === 'scan_jobfile.php') {
        window.open(GLOBALS.ajax_dir + '/' + src, 'scan_window', 'resizable=1');
        return false;
    }

    var overlay = $('<div id="myoverlay"><iframe src="includes/ajax/' + src + '" frameborder="0" height="100%" width="100%"></div></div>');
    if(toParent) {
        parent.$(overlay).lightbox_me({
            destroyOnClose: true,
            centered: true,
            overlaySpeed: 0,
            lightboxSpeed: 250,
            overlayCSS: {background: 'black', opacity: .6}
        });
    } else {
        $(overlay).lightbox_me({
            destroyOnClose: true,
            centered: true,
            overlaySpeed: 0,
            lightboxSpeed: 250,
            overlayCSS: {background: 'black', opacity: .6}
        });
    }
    
    $('html, body').animate({scrollTop: 0});

    //remove scroll bars from body to avoid double scroll bars
    //$('body').css('overflow', 'hidden');
}

function deleteOverlay() {
    //$('#myoverlay').remove();
    //$('.lb_overlay').remove();
    $('#myoverlay').trigger('close');
    //$('body').css('overflow', 'auto');
}

function viewNote(note_id, type, id) {
    Request.make('includes/ajax/get_notes.php?action=view&noteid='+note_id+'&type='+type+'&id='+id, 'notes', false, true);
}

function markUnread(message_id)
{
    Request.make('includes/ajax/get_messagelist.php?action=unread&urid='+message_id, 'messagecontainer', true, true);
}

function deleteNote(note_id, type, id)
{
    if(confirm("Are you sure?"))
    {
        Request.make('includes/ajax/get_notes.php?action=del&noteid='+note_id+'&type='+type+'&id='+id, 'notes', false, true);
    }
}

function deleteMessage(message_id)
{
    if(confirm("Are you sure?"))
    {
        Request.make('includes/ajax/get_messagelist.php?action=del&delid='+message_id, 'messagecontainer', true, true);
    }
}

function recoverMessage(message_id)
{
    Request.make('includes/ajax/get_messagelist.php?action=recover&recoverid='+message_id, 'messagecontainer', true, true);
}

function markTaskPaid(task_id, job_id)
{
    if(confirm("Are you sure you want to mark paid?"))
        Request.make('includes/ajax/get_job.php?id='+job_id+'&action=marktaskpaid&taskid='+task_id, 'jobscontainer', true, true);
}

function markTaskUnpaid(task_id, job_id)
{
    if(confirm("Are you sure you want to mark unpaid?"))
        Request.make('includes/ajax/get_job.php?id='+job_id+'&action=marktaskunpaid&taskid='+task_id, 'jobscontainer', true, true);
}

function showHide(div)
{
    $('#' + div).toggle();
}

function showHideFrame(div, frame)
{
    eval("myDiv = parent."+frame+".document.getElementById(div)");

    if(myDiv.style.display=='none')
        myDiv.style.display='';
    else myDiv.style.display='none';

    return;
}

function hoverRow(row)
{
    $(row).addClass('rowhover');
}
function hoverRowOut(row)
{
    $(row).removeClass('rowhover');
}

function bookmarkJob(job_id)
{
    Request.make("includes/ajax/toggle_bookmark.php?id="+job_id);
}

function removeSubscriber(job_id, user_id)
{
    if(confirm("Are you sure?"))
    {
        script = 'includes/ajax/delete_subscriber.php?job='+job_id+'&user='+user_id;
        Request.make(script, '', '', '');
        setTimeout("Request.make('includes/ajax/get_job.php?id="+job_id+"', 'jobscontainer', true, true)", 1000);
    }
}

function removeJournalAndRestyleListDashboard(journal_id) {
	$('#journal-' + journal_id).remove();
	if(parent.$) {
		parent.$('#journal-' + journal_id).remove();
		window.location.reload();
	}
}

function removeuserJournal(user_id) {
	$('#user-id-' + user_id).remove();
	if(parent.$) {
		parent.$('#user-id-' + user_id).remove();
		//window.location.reload();
	}
}

function addMaterial(sheet, job_id)
{
    item = document.getElementById("item").value;
    color = document.getElementById("color").value;
    qty = document.getElementById("qty").value;

    document.getElementById("colorblock").style.display="none";

    Request.make("get_sheet.php?sheet_id="+sheet+"&job_id="+job_id+"&i="+item+"&c="+color+"&q="+qty, "materiallistcontainer", "", "yes");
}

function getMaterialDropDown(cat, sheet, jobid)
{
    if(cat!='')
    {
        document.getElementById("materialblock").style.display="";
        Request.make("get_materialdropdown.php?jobid="+jobid+"&sheet="+sheet+"&cat="+cat, "materials", "", "yes");
    }
}

function getColorsDropDown(item, sheet, jobid)
{
    if(item!='')
    {
        document.getElementById("colorblock").style.display="";
        Request.make("get_colordropdown.php?jobid="+jobid+"&sheet="+sheet+"&item="+item, "colors", "", "yes");
    }
}

function getReportColorDropdown(dropdown, element)
{
    $.getJSON('includes/ajax/get_reportcolordropdown.php?item='+$(dropdown).val(), function(json_data){
        $(element).empty();
        $.each(json_data, function(){
            $(element).append(
                $('<option></option>').val(this.color_id).html(this.color)
                );
        });
    });
}

function changeQty(sheet_id, job_id, qty, item)
{
    if(qty == 'del')
        url = "get_sheet.php?sheet_id="+sheet_id+"&job_id="+job_id+"&item="+item+"&action=del";

    else
        url = "get_sheet.php?sheet_id="+sheet_id+"&job_id="+job_id+"&item="+item+"&qty="+qty;

    Request.make(url, "materiallistcontainer", "", "yes");
}

function launchAction(url)
{
    if(url!='')
    {
        newWindow("includes/ajax/"+url, 550, 600);
    }
}

function changeStage(job_id, stage_num)
{
    Request.make('includes/ajax/next_stage.php?id='+job_id+'&action=select&stage='+stage_num, 'jobscontainer', true);
    setTimeout("Request.make('includes/ajax/get_job.php?id="+job_id+"', 'jobscontainer', true, true)", 1000);
}

function nextStage(job_id)
{
    Request.make('includes/ajax/next_stage.php?id='+job_id, 'jobscontainer', true);
    setTimeout("Request.make('includes/ajax/get_job.php?id="+job_id+"', 'jobscontainer', true, true)", 1000);
}

function hover(obj)
{
    obj.style.cursor='pointer';
}

//(url, targetElementId, loadingIndicator, loadResponse, deleteOverlay, callback) {


function getXMLHttp()
{
    var xmlHttp

    try {
        //Firefox, Opera 8.0+, Safari
        xmlHttp = new XMLHttpRequest();
    }
    catch(e) {
        //Internet Explorer
        try {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch(e)
        {
            try {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(e) {
                alert("Your browser does not support AJAX!")
                return false;
            }
        }
    }
    return xmlHttp;
}

function HandleResponse(response, div)
{
    $('#'+div).html(response);
}

function newWindow(url, width, height)
{
    window.open(url,'mywindow','width='+width+',height='+height+',left=50,top=50,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=yes,resizable=yes');
}

function updateClock (mydiv)
{
    var currentTime = new Date ();

    var currentHours = currentTime.getHours ();
    var currentMinutes = currentTime.getMinutes ();
    var currentSeconds = currentTime.getSeconds ();

    currentMinutes = (currentMinutes<10 ? "0" : "") + currentMinutes;
    currentSeconds = (currentSeconds<10 ? "0" : "") + currentSeconds;

    var timeOfDay = (currentHours<12) ? "AM" : "PM";

    currentHours = (currentHours>12) ? currentHours - 12 : currentHours;

    currentHours = (currentHours==0) ? 12 : currentHours;

    var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;

    document.getElementById(mydiv).innerHTML = "yes";
    document.getElementById(mydiv).innerHTML = currentTimeString;
}

function createNotification(text, type) {
    type = type == 'success' ? 'notice' : type;
    
    $.growl[type]({message: text});
    
//	noty({
//		text: text,
//		layout: 'topRight',
//		type: type,
//		timeout: 3000
//	});
}

function setMeta(el) {
	var el = $(this),
        key = el.data('key'),
        type = el.data('type'),
        showAlert = el.data('alert'),
        value = el.is(':checkbox') ? (el.is(':checked') ? 1 : 0) : (el.val() || '');

	$.getJSON(GLOBALS.ajax_dir + '/set_' + type + '_meta_data.php?key=' + key + '&value=' + value, function(data){
		if(showAlert) {
			if(data.error) {
				createNotification(data.error, 'error');
			} else if(data.success) {
				createNotification(data.success, 'success');
			}
		}
	}).fail(function(){
		createNotification('Operation failed', 'error');
	});
}

function getContrastTextClass(hexcolor)
{
    if(parseInt(hexcolor, 16) > 0xffffff/2)
	{
		//dark text
	}
	else
	{
		//light text
	}
}

function printPage(url)
{
	child = window.open(url, '', 'height=600, width=800');
	child.focus();
	child.print();
	setTimeout(function(){
		child.close();
	}, 500);
}

function removeJournalAndRestyleList(journal_id) {
	$('#journal-' + journal_id).remove();
	if(parent.$) {
		parent.$('#journal-' + journal_id).remove();
	}
}

function removeJobUpload(upload_id)
{
	$('#upload-' + upload_id).remove();
	if(parent.$)
	{
		parent.$('#upload-' + upload_id).remove();
	}
	var upload_containers = $('#dropzone .upload-container');
}

function removeJobSubscriber(subscriber_id)
{
	$('#subscriber-' + subscriber_id).remove();
	if(parent.$)
	{
		parent.$('#subscriber-' + subscriber_id).remove();
	}
}

function removeJobMaterialSheet(sheet_id)
{
	$('#sheet-' + sheet_id).remove();
	if(parent.$)
	{
		parent.$('#sheet-' + sheet_id).remove();
	}
}

function stopWatching(conversation_id, type)
{
	$('#watch-' + type + '-' + conversation_id).remove();
	if(parent.$)
	{
		parent.$('#watch-' + type + '-' + conversation_id).remove();
	}
}

function setFrameLocation(url) {
    $('#app-iframe').attr('src', url);
}

function loadStatusBar() {
    loadBrowsingHistory();
    loadNewJobs();
}

function loadBrowsingHistory() {
	$('#browsing-history-items').load(addHeightToUrl(GLOBALS.ajax_dir + '/get_browsing_history_items.php'));
}

function loadNewJobs() {
    $('#new-jobs').load(addHeightToUrl(GLOBALS.ajax_dir + '/get_new_jobs.php'));
}

function loadBookmarks() {
    $('#bookmarks').load(addHeightToUrl(GLOBALS.ajax_dir + '/get_bookmarks.php'));
}

function deleteBrowsingHistoryItem(element)
{
	element.parent().remove();
}

function addHeightToUrl(url) {
    var concat = url.indexOf('?') !== -1 ? '&' : '?';
    
    return url + concat + 'window_height=' + $(window).height();
}

function clearInfoContainer(selector, timeout) {
    timeout = timeout || 3000;
    
    setTimeout(function() {
        $(selector).fadeOut(function() {
            $(this).empty().show();
        });
    }, timeout);
} 

function getQueryStringParam(url, param) {
    var sURLVariables = url.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == param) {
            return sParameterName[1];
        }
    }
}