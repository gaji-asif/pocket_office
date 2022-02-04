// Initialize your app
var myApp = new Framework7({
    animateNavBackIcon: true,
    // Enable templates auto precompilation
    precompileTemplates: true,
    // Enabled pages rendering using Template7
    swipeBackPage: true,
    pushState: true,
    init: true
});


// Export selectors engine
var $$ = Dom7;

// Add main View
var mainView = myApp.addView('.view-main', {
    // Enable dynamic Navbar
    dynamicNavbar: true,
});


//Global Varibales
var loginuserid = 0;
var loginusername = "";
var loginfname = "";
var loginlname = "";
var loginlevelid = 0;
var loginaccountid = 0;
var ao_module_access;
var ao_nav_access;
var ao_system_user = false;
var ao_founder = false;
var ao_password = "";
var accountMetaData;


var pictureSource; // picture source
var destinationType; // sets the format of returned value
var featherEditor;


//document.addEventListener("deviceready", onDeviceReady, false);
document.addEventListener("backbutton", onBackKeyDown, false);
////////// PhoneGap is ready to be used!
//////////
function onBackKeyDown() {
    try {
        return false;
        //mainView.router.back({ force: true });
        //  navigator.app.exitApp()
        //  mainView.router.back();
    } catch (e) {
        //alert(e);
        navigator.notification.alert(
            e, alertDismissed, "Unsuccessful", "Done"
        );
    }
}


// PhoneGap is ready to be used!
document.addEventListener("deviceready", onDeviceReady, false);
function onDeviceReady() {
    
    pictureSource = navigator.camera.PictureSourceType;
    destinationType = navigator.camera.DestinationType;
    
}
function CheckUserLogin() {
    var $ = jQuery.noConflict();

    if (window.localStorage.getItem("sessLoginId") != undefined && window.localStorage.getItem("sessLoginId") != null) {

        loginuserid = window.localStorage.getItem("sessLoginId");

        //  SetLoginUserName(loginuserid);
        //  GetUserListCount(loginuserid)
        $("#alogo").attr("href", "myaccount.html");
        var logoutTag = "<a href='javascript:;' onclick='logoutClick();' style='margin-left:3px;color:yellow' title='Logout'>Logout</a>"
        var followingCount = "<br/><a id='afollowing' href='#' title='' style='color:white'></a>"
        $(".navbar-header").append(logoutTag);
        $(".navbar-header").append(followingCount);
        if (window.localStorage.getItem("sessUserName") != undefined) {
            loginusername = window.localStorage.getItem("sessUserName");
            $('#headerUserName').text('Welcome,  ' + loginusername)
        }
        if (window.localStorage.getItem("sessFname") != null) {
            loginfname = window.localStorage.getItem("sessFname");
        }
        if (window.localStorage.getItem("sessLname") != null) {
            loginlname = window.localStorage.getItem("sessLname");
        }
        if (window.localStorage.getItem("sessLevel") != null) {
            loginlevelid = window.localStorage.getItem("sessLevel");
        }
        if (window.localStorage.getItem("sessAccountId") != null) {
            loginaccountid = window.localStorage.getItem("sessAccountId");
        }
        if (window.localStorage.getItem("ao_module_access") != null) {
            ao_module_access = window.localStorage.getItem("ao_module_access");
        }

        if (window.localStorage.getItem("ao_nav_access") != null) {
            ao_nav_access = window.localStorage.getItem("ao_nav_access");
        }
        if (window.localStorage.getItem("ao_founder") != null) {
            ao_founder = window.localStorage.getItem("ao_founder");
        }
        if (window.localStorage.getItem("ao_password") != undefined) {
            ao_password = window.localStorage.getItem("ao_password");
        }
        return true;
    } else {
        logout();
        return true;
    }
}

function logout() {
    sessionStorage.clear();
    window.localStorage.clear();
    location.href = "index.html";
}

function Checkaccess(hook) {
    var $ = jQuery.noConflict();
    var obj = JSON.parse(ao_module_access);

    var res = false;
    for (var i = 0; i < obj.length; i++) {
        if (obj[i] == hook) {
            res = true;
        }

    }
    return res;
}
myApp.onPageInit('dashboard', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    if (Checkaccess('view_schedule')) {
        GetTodayList('MethodName=GetRepairList');
    }
    if (Checkaccess('view_jobs')) {
        GetUrgentJob('MethodName=GetUrgentJobList');
    }
    if (Checkaccess('view_documents')) {
        GetRecentDocument('MethodName=GetDocumentList');
    }
    GetInbox('MethodName=GetInboxList');

    function GetTodayList(data) {
        var $ = jQuery.noConflict();

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                //Repair Array

                if (data.status == '1') {
                    $('#divtodaylist').css('display', 'block');
                    if (data.repairsArray.length > 0) {
                        $('#lstRepairs').empty();
                        var items = [];
                        items.push('<table  class="table"><tr><th>Fail Type</th><th>Job Number</th><th>DBA</th></tr>');
                        for (var i = 0; i < data.repairsArray.length; i++) {
                            var clsname = "linkToRepairPage";
                            var uname = '';
                            if (data.repairsArray[i].completed != null && data.repairsArray[i].completed != undefined && data.repairsArray[i].completed != "")
                                clsname += " line-through";
                            if (data.repairsArray[i].contractor != null && data.repairsArray[i].contractor != undefined && data.repairsArray[i].contractor != '')
                                if (data.repairsArray[i].dba != null && data.repairsArray[i].dba != undefined && data.repairsArray[i].dba != '')
                                    uname = data.repairsArray[i].dba;
                                else {
                                    if (data.firstLast == "")
                                        uname = data.repairsArray[i].contractor_lname + ', ' + data.repairsArray[i].contractor_fname;
                                    else
                                        uname = data.repairsArray[i].contractor_fname + ', ' + data.repairsArray[i].contractor_lname
                                }
                            items.push('<tr><td style="cursor: pointer;" class="' + clsname + '" id="linkToRepairPage_' + data.repairsArray[i].repair_id + "_" + data.repairsArray[i].job_id + '">' + data.repairsArray[i].fail_type + '</td><td style="cursor: pointer;" class=linkToTodayJobDetail id="linkToTodayJobDetail_' + data.repairsArray[i].job_id + '">' + data.repairsArray[i].job_number + '</td><td style="cursor: pointer;" class=linkToTodayUserDetail id="linkToTodayUserDetail_' + data.repairsArray[i].contractor + '">' + uname + '</td></tr>');


                            //items.push('<tr><td>' + data.repairsArray[i].fail_type + '</td></tr><tr><td>' + data.repairsArray[i].job_number + '</td></tr><tr><td>' + data.repairsArray[i].contractor_fname + ' ' + data.repairsArray[i].contractor_lname + '</td></tr>');

                        }
                        items.push('</table>');
                        $('#lstRepairs').append(items.join(''));

                        $('.linkToRepairPage').on("click", function() {
                            var splits_id = this.id.split('_');
                            var repairID = splits_id[1];
                            var repairJobID = splits_id[2];
                            mainView.loadPage("get_repair.html?srcPage=dashboard.html&id=" + repairID + "&JId=" + repairJobID);
                            return false;
                        });

                        $('.linkToTodayJobDetail').on("click", function() {
                            var splits_id = this.id.split('_');
                            var JobId = splits_id[1];
                            mainView.loadPage("jobdetails.html?JId=" + JobId);
                            return false;
                        });

                        $('.linkToTodayUserDetail').on("click", function() {
                            var splits_id = this.id.split('_');
                            var UserID = splits_id[1];
                            mainView.loadPage("get_user.html?UserID=" + UserID);
                            return false;
                        });
                    } else {
                        $('#lstRepairs').attr("display", "none");
                        $('#divRepairList').attr("style", "display:none");

                    }

                    //Task Array
                    if (data.tasksArray.length > 0) {
                        $('#lstTask').empty();
                        var items = [];
                        items.push('<table  class="table"><tr><th>Task</th><th>Job No</th></tr>');
                        for (var i = 0; i < data.tasksArray.length; i++) {
                            if (data.tasksArray[i].completed != "") {
                                items.push('<tr><td style="cursor: pointer;" class="linkToTaskDetail " id="linkToTaskDetail_' + data.tasksArray[i].task_id + "_" + data.tasksArray[i].job_id + '">' + data.tasksArray[i].task + '</td><td style="cursor: pointer;" class=linkToTaskJobsPage id="linkToTaskJobsPage_' + data.tasksArray[i].job_id + '">' + data.tasksArray[i].job_number + '</td></tr>');
                            } else {
                                items.push('<tr><td style="cursor: pointer;" class="linkToTaskDetail" id="linkToTaskDetail_' + data.tasksArray[i].task_id + "_" + data.tasksArray[i].job_id + '">' + data.tasksArray[i].task + '</td><td style="cursor: pointer;" class=linkToTaskJobsPage id="linkToTaskJobsPage_' + data.tasksArray[i].job_id + '">' + data.tasksArray[i].job_number + '</td></tr>');
                            }
                            //items.push('<tr><td>' + data.tasksArray[i].task + '</td></tr><tr><td>' + data.tasksArray[i].job_number + '</td></tr><tr><td>' + data.tasksArray[i].contractor_fname + ' ' + data.tasksArray[i].contractor_lname + '</td></tr>');

                        }
                        items.push('</table>');
                        $('#lstTask').append(items.join(''));

                        $('.linkToTaskDetail').on("click", function() {
                            var splits_id = this.id.split('_');
                            var TaskID = splits_id[1];
                            var TaskJobID = splits_id[2];
                            mainView.loadPage("get_task.html?Id=" + TaskID + "&JId=" + TaskJobID + "&srcPage=dashboard.html");
                            return false;
                        });

                        $('.linkToTaskJobsPage').on("click", function() {
                            var splits_id = this.id.split('_');
                            var TaskJobID = splits_id[1];
                            mainView.loadPage("jobtabs.html?JId=" + TaskJobID);
                            return false;
                        });
                    } else {
                        $('#lstTask').attr("display", "none");
                        $('#divTaskList').attr("style", "display:none");
                    }

                    //Event Array
                    if (data.eventsArray.length > 0) {
                        $('#lstEvent').empty();
                        var items = [];
                        items.push('<table class="table"><tr><th>Time</th><th>Title</th></tr>');
                        for (var i = 0; i < data.eventsArray.length; i++) {
                            //items.push('<tr><td>' + data.eventsArray[i].date + '</td></tr><tr><td>' + data.eventsArray[i].title + '</td></tr>');
                            //if (data.eventsArray[i].completed != "") {
                            //    items.push('<tr style="cursor: pointer;" class="linkToEventDetails line-through" id="linkToEventDetails_' + data.eventsArray[i].event_id + '"><td>' + data.eventsArray[i].time + '</td><td>' + data.eventsArray[i].title + '</td></tr>');
                            //}
                            //else {
                            items.push('<tr style="cursor: pointer;" class="linkToEventDetails" id="linkToEventDetails_' + data.eventsArray[i].event_id + '"><td>' + data.eventsArray[i].time + '</td><td>' + data.eventsArray[i].title + '</td></tr>');
                            //}
                        }
                        items.push('</table>');
                        $('#lstEvent').append(items.join(''));

                        $('.linkToEventDetails').on("click", function() {
                            var splits_id = this.id.split('_');
                            var EventID = splits_id[1];
                            mainView.loadPage("add_event.html?srcPage=dashboard.html&date=&id=" + EventID);
                            return false;
                        });
                    } else {
                        $('#lstEvent').attr("display", "none");
                        $('#divEventList').attr("style", "display:none");
                    }

                    //Appoinments Array
                    if (data.appointmentsArray.length > 0) {
                        $('#lstAppt').empty();
                        var items = [];
                        items.push('<table  class="table"><tr><th>Time</th><th>Title</th><th>Job Number</th></tr>');
                        for (var i = 0; i < data.appointmentsArray.length; i++) {
                            //items.push('<tr><td>' + data.appointmentsArray[i].datetime + '</td></tr><tr><td>' + data.appointmentsArray[i].title + '</td></tr><tr><td>' + data.appointmentsArray[i].job_number + '</td></tr>');
                            if (data.appointmentsArray[i].completed != "") {
                                items.push('<tr><td>' + data.appointmentsArray[i].time + '</td><td style="cursor: pointer;" class="linkToApptDetail line-through" id="linkToApptDetail_' + data.appointmentsArray[i].appointment_id + "_" + data.appointmentsArray[i].job_id + '">' + data.appointmentsArray[i].title + '</td><td style="cursor: pointer;" class=linkToApptAssoJob id="linkToApptAssoJob_' + data.appointmentsArray[i].job_id + '">' + data.appointmentsArray[i].job_number + '</td></tr>');
                            } else { items.push('<tr><td>' + data.appointmentsArray[i].time + '</td><td style="cursor: pointer;" class="linkToApptDetail" id="linkToApptDetail_' + data.appointmentsArray[i].appointment_id + "_" + data.appointmentsArray[i].job_id + '">' + data.appointmentsArray[i].title + '</td><td style="cursor: pointer;" class=linkToApptAssoJob id="linkToApptAssoJob_' + data.appointmentsArray[i].job_id + '">' + data.appointmentsArray[i].job_number + '</td></tr>'); }
                        }
                        items.push('</table>');
                        $('#lstAppt').append(items.join(''));

                        $('.linkToApptDetail').on("click", function() {
                            var splits_id = this.id.split('_');
                            var ApptID = splits_id[1];
                            var ApptJobID = splits_id[2];
                            mainView.loadPage("get_appointment.html?srcPage=dashboard.html&id=" + ApptID + "&JId=" + ApptJobID);
                            return false;
                        });

                        $('.linkToApptAssoJob').on("click", function() {
                            var splits_id = this.id.split('_');
                            var JobId = splits_id[1];
                            mainView.loadPage("jobdetails.html?JId=" + JobId);
                            return false;
                        });
                    } else {
                        $('#lstAppt').attr("display", "none");
                        $('#divApptList').attr("style", "display:none");
                    }

                    //Deliveries Array
                    if (data.deliveriesArray.length > 0) {
                        $('#lstDeli').empty();
                        var items = [];
                        items.push('<table  class="table"><tr><th>Date</th><th>Label</th><th>Last Name</th></tr>');
                        for (var i = 0; i < data.deliveriesArray.length; i++) {

                            if (data.deliveriesArray[i].confirmed != null && data.deliveriesArray[i].confirmed != "undefined")
                                items.push('<tr style="cursor: pointer;" class=linkToMaterialDetails id="linkToMaterialDetails_' + data.deliveriesArray[i].job_id + '"><td>' + 'Delivery confirmed on ' + $.datepicker.formatDate('M dd, yy', new Date(data.deliveriesArray[i].confirmed)) + '</td><td>' + data.deliveriesArray[i].label + '</td><td>' + data.deliveriesArray[i].customer_lname + '</td></tr>');

                            else
                                items.push('<tr style="cursor: pointer;" class=linkToMaterialDetails id="linkToMaterialDetails_' + data.deliveriesArray[i].job_id + '"><td>' + '' + '</td><td>' + data.deliveriesArray[i].label + '</td><td>' + data.deliveriesArray[i].salesman_fname + ' ' + data.deliveriesArray[i].salesman_lname + '</td></tr>');

                        }
                        items.push('</table>');
                        $('#lstDeli').append(items.join(''));
                        $('.linkToMaterialDetails').on("click", function() {
                            var splits_id = this.id.split('_');
                            var job_id = splits_id[1];

                            mainView.loadPage("jobdetails.html?JId=" + job_id);
                            return false;
                        });
                    } else {
                        $('#lstDeli').attr("display", "none");
                        $('#divDeliList').attr("style", "display:none");
                    }


                    if (data.repairsArray.length < 1 && data.tasksArray.length < 1 && data.eventsArray.length < 1 && data.appointmentsArray.length < 1 && data.deliveriesArray.length < 1) {
                        $("#divTodayAccordionPane").css("display", "none");
                        $('#lblTodayMsg').html("No Tasks, Repairs, Deliveries or Events Today");
                        $("#lblTodayMsg").css("display", "block");

                    }
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                GetTodayList('MethodName=GetRepairList');
                // navigator.notification.alert(
                //     errorMessage, alertDismissed, "An error occured", "Done"
                // );
            }
        })
    }

    function GetRecentDocument(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'post',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $('#lstRctDoc').empty();
                var items = [];
                if (data.status == '1') {
                    $("#divDashboardRecentDoc").css("display", "block");

                    if (data.Post != null) {
                        items.push('<table class="table"><tr><th>Title</th><th>Date</th></tr>');
                        for (var i = 0; i < data.Post.length; i++) {

                            //$('#lstrepairs').append('<li>' + data[i].post.user_id + '</li><li>' + data[i].post.account_id + '</li><li>' + data[i].post.notes + '</li><li><br>');
                            items.push('<tr style="cursor: pointer;" class=linkToRecentDocument id="linkToRecentDocument_' + data.Post[i].document_id + '"><td>' + data.Post[i].document + '</td><td>' + data.Post[i].ForamtDate + '</td></tr>');
                        }
                        items.push('</table>');
                    } else {
                        $('#lblRecDocMsg').html("No Documents Found");
                        $("#lblRecDocMsg").css("display", "block");
                    }
                    $('#lstRctDoc').append(items.join(''));
                    $('.linkToRecentDocument').on("click", function() {
                        var splits_id = this.id.split('_');
                        var DocID = splits_id[1];
                        mainView.loadPage("edit_document.html?DocumentID=" + DocID);
                        return false;
                    });
                } else if (data.status == '-1') {
                    $("#divDashboardRecentDoc").css("display", "block");
                    $('#lblRecDocMsg').html("No Documents Found");
                    $("#lblRecDocMsg").css("display", "block");
                    $("#divRctDoc").css("display", "none");

                } else {
                    $("#divDashboardRecentDoc").css("display", "none");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
               GetRecentDocument('MethodName=GetDocumentList');
               // navigator.notification.alert(
               //      errorMessage, alertDismissed, "An error occured", "Done"
               //  );
            }
        })
    }

    function GetInbox(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $('#lstInbox').empty();
                //Inbox
                var items = [];
                if (data.status == '1') {
                    $("#divDashboardInboxList").css("display", "block");
                    if (data.Post != null) {
                        items.push('<table   class="table"><tr><th>Job Number</th><th>Subject</th><th>From</th><th>Timestamp</th></tr>');
                        for (var i = 0; i < data.Post.length; i++) {
                            if (data.Post[i].job_id != "") {
                                items.push('<tr style="cursor: pointer;" class=linkToUserJobDetail id="linkToUserJobDetail_' + data.Post[i].job_id + '" title=Journal><td>' + data.Post[i].job_number + '</td><td>' + data.Post[i].subject + '</td><td>' + data.Post[i].lname + ', ' + data.Post[i].fname.charAt(0) + '</td><td>' + data.Post[i].formatDt + '</td></tr>');
                            } else {
                                items.push('<tr style="cursor: pointer;" class=linkToUserMsgDetail id="linkToUserMsgDetail_' + data.Post[i].message_id + '"><td>' + data.Post[i].job_number + '</td><td>' + data.Post[i].subject + '</td><td>' + data.Post[i].lname + ', ' + data.Post[i].fname.charAt(0) + '</td><td>' + data.Post[i].formatDt + '</td></tr>');
                            }
                        }
                        items.push('</table>');
                    } else {
                        $('#lblInboxMsg').html("No Messages Found");
                        $("#lblInboxMsg").css("display", "block");
                    }
                    $('#lstInbox').append(items.join(''));

                    $('.linkToUserJobDetail').on("click", function() {
                        var splits_id = this.id.split('_');
                        var job_id = splits_id[1];
                        mainView.loadPage("jobtabs.html?JId=" + job_id);

                        return false;
                    });

                    $('.linkToUserMsgDetail').on("click", function() {
                        var splits_id = this.id.split('_');
                        var MsgID = splits_id[1];
                        mainView.loadPage("messaging.html?MessageID=" + MsgID);
                        return false;
                    });
                } else if (data.status == '-1') {
                    $("#divDashboardInboxList").css("display", "block");
                    $('#lblInboxMsg').html("No Messages Found");
                    $("#lblInboxMsg").css("display", "block");
                    $("#divInbox").css("display", "none");

                } else {
                    $("#divDashboardInboxList").css("display", "none");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                GetInbox('MethodName=GetInboxList');
            }
        })
    }

    function GetUrgentJob(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $('#lsUrgentJob').empty();
                var dasclass = '';
                //Inbox
                var items = [];

                if (data.status == '1') {
                    $("#divDashboardUrgentJob").css("display", "block");

                    if (data != null) {
                        items.push('<table  class="table"><tr><th>Job #</th><th>Insured</th><th>Stage</th><th>DAS / Limit</th></tr>');
                        for (var i = 0; i < data.Post.length; i++) {
                            dasclass = "class = 'das_Important'";
                            if (data.Post[i].days_past < 5) { dasclass = "class = 'das_Warning'"; }
                            //$('#lstRepairs').append('<li>' + data[i].post.user_id + '</li><li>' + data[i].post.account_id + '</li><li>' + data[i].post.notes + '</li><li><br>');
                            items.push('<tr style="cursor: pointer;" class=linkToJobDetail id="linkToJobDetail_' + data.Post[i].job_id + '"><td>' + data.Post[i].job_number + '</td><td>' + data.Post[i].lname + ', ' + data.Post[i].fname.charAt(0) + '</td><td>' + '#' + data.Post[i].stage_num + ': ' + data.Post[i].stage + '</td><td><label ' + dasclass + '>' + data.Post[i].das + " / " + data.Post[i].duration + '</label></td></tr>');
                        }
                        items.push('</table>');

                    } else {
                        $('#lblUrgentJobMsg').html("No Urgent Jobs Found");
                        $("#lblUrgentJobMsg").css("display", "block");
                    }
                    $('#lsUrgentJob').append(items.join(''));

                    $('.linkToJobDetail').on("click", function() {
                        var splits_id = this.id.split('_');
                        var JobID = splits_id[1];
                        mainView.loadPage("jobtabs.html?JId=" + JobID);
                        return false;
                    });
                } else if (data.status == '-1') {
                    $("#divDashboardUrgentJob").css("display", "block");
                    $('#lblUrgentJobMsg').html("No Urgent Jobs Found");
                    $("#lblUrgentJobMsg").css("display", "block");
                    $("#divUrgentJob").css("display", "none");

                } else {
                    $("#divDashboardUrgentJob").css("display", "none");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                GetUrgentJob('MethodName=GetUrgentJobList');
            }
        })
    }

});
myApp.onPageInit('suppliers', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    if (Checkaccess('view_customers')) {
        GetSupplierList('MethodName=GetSupplierList');
        $('#divSupplierLst').css("display", "block");
        $('#lblMsgForSupplierMainLabel').css("display", "none");
    } else {
        $('#lblMsgForSupplierMainLabel').html("Insufficient Rights");
        $('#lblMsgForSupplierMainLabel').css("display", "block");
        $('#divSupplierLst').css("display", "none");
    }

    if (Checkaccess('modify_suppliers')) {
        $('#aAddSplr').css("display", "block");
    } else {
        $('#aAddSplr').css("display", "none");
    }

    function GetSupplierList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $('#lstSupl').empty();
                var items = [];

                if (data.result.length > 0) {
                    items.push('<table style="width:100%;" class="table"><tr><th style="font-weight:bold;">Supplier</th><th style="font-weight:bold;">Contact</th><th style="font-weight:bold;">Phone</th><th style="font-weight:bold;">Fax</th><th style="font-weight:bold;">Email</th></tr>');
                    for (var i = 0; i < data.result.length; i++) {
                        items.push('<tr style="cursor: pointer;" id=aSupId_' + data.result[i].supplier_id + ' class="aSuplTitle" ><td><a id="aSupplier" href="AddSupplier.html?SupplierId=' + data.result[i].supplier_id + '"  style="font-weight:bold;">' + data.result[i].supplier + '</a></td><td> <a id="aSupplier" href="AddSupplier.html?SupplierId=' + data.result[i].supplier_id + '">' + data.result[i].contact + '</a></td><td><a id="aSupplier" href="AddSupplier.html?SupplierId=' + data.result[i].supplier_id + '">' + data.result[i].formatPhn + '</a></td><td><a id="aSupplier" href="AddSupplier.html?SupplierId=' + data.result[i].supplier_id + '">' + data.result[i].formatFax + '</a></td><td><a id="aSupplier" href="AddSupplier.html?SupplierId=' + data.result[i].supplier_id + '">' + data.result[i].email + '</a></td></tr>');
                    }
                    items.push('</table>');
                } else {
                    items.push('<table style="width:100%;" class="table"><tr><th style="font-weight:bold;">Supplier</th><th style="font-weight:bold;">Contact</th><th style="font-weight:bold;">Phone</th><th style="font-weight:bold;">Fax</th><th style="font-weight:bold;">Email</th></tr>');
                    items.push('<tr><td colspan="5" class="acenter">No Suppliers Found</td></tr>');
                    items.push('</table>');
                }
                $('#lstSupl').append(items.join(''));

            },
            error: function(jqxhr, textStatus, errorMessage) {
                GetSupplierList('MethodName=GetSupplierList');
            }
        })
    }

    $(".aAddSplr").on("click", function() {
        mainView.loadPage("AddSupplier.html");
        return false;
    });
});

myApp.onPageInit('addsupplier', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();

    $('.masked-phone').inputmask('(999) 999-9999', { placeholder: ' ' });
    var supplierId = page.query.SupplierId;
    if (supplierId != null && supplierId != "" && supplierId != "undefined") {
        BindSupplierDetail('MethodName=BindSupplierDetailByID&SupplierID=' + page.query.SupplierId);
    }

    if (Checkaccess('modify_suppliers')) {
        $('#divEditSupplier').css("display", "block");
    }
    if (supplierId != null && supplierId != "" && supplierId != "undefined") {
        if (Checkaccess('modify_suppliers')) {
            $("#divAddSupplier").css("display", "none");
            $("#divEditSupplier").css("display", "block");
            $("#divHeadForSupplierMethod").html("");
            $("#divHeadForSupplierMethod").html("Edit Supplier");
        }
    } else {
        if (Checkaccess('modify_suppliers')) {
            $("#divAddSupplier").css("display", "block");
            $("#divEditSupplier").css("display", "none");
            $("#divHeadForSupplierMethod").html("");
            $("#divHeadForSupplierMethod").html("Add Supplier");
        }
    }
    $("#btnSaveSupplier").on("click", function() {
        var retval = false;
        var ans = check_itemsvalidate('#divAddSupplier input');
        if (ans) {
            SaveSupplierDetail('MethodName=SaveSupplierDetail&Supplier=' + $("#txtSupplier").val() + '&Contact=' + $("#txtContactNo").val() + '&Phone=' + $("#txtPhone").val() + '&Fax=' + $("#txtFax").val() + '&Email=' + $("#txtEmail").val());
        } else {
            return false;
        }

    });

    function SaveSupplierDetail(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);
                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    mainView.loadPage("suppliers.html");
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    $("#divAddSupplier").css("display", "block");
                    $("#divEditSupplier").css("display", "none");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                SaveSupplierDetail(data);
            }
        })
    }

    $("#btnCancel").on("click", function() {
        jQuery(".formError").remove();
        mainView.loadPage("suppliers.html");
    });
    function BindSupplierDetail(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];
                if (data != null) {
                    for (var i = 0; i < data.length; i++) {
                        items.push($("#txtEditSupplier").val(data[i].Post.supplier) + $("#txtEditContact").val(data[i].Post.contact) + $("#txtEditPhone").val(data[i].Post.phone) + $("#txtEditFax").val(data[i].Post.fax) + $("#txtEditEmail").val(data[i].Post.email));
                    }
                    return false;
                } else {
                    navigator.notification.alert(
                        "There are some error in fetching supplier detail, please try again later.", alertDismissed, "An error occured", "Done"
                    );
                    return false;
                }

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                BindSupplierDetail(data)
                return false;
            }
        });
    }

    function UpdateSupplierDetail(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    mainView.loadPage("suppliers.html");
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    $("#divAddSupplier").css("display", "none");
                    $("#divEditSupplier").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                //navigator.notification.alert(     "There was an error. Try again please!", alertDismissed,"An error occured","Done"              );
                navigator.notification.alert(
                    "There was an error. Try again please!", alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function DeleteSupplierById(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    mainView.loadPage("suppliers.html");

                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblEditSupplier').html(data.message);
                    //$("#lblEditSupplier").css("display", "block");
                    $("#divAddSupplier").css("display", "none");
                    $("#divEditSupplier").css("display", "block");
                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    $("#btnUpdateSupplier").on("click", function() {

        var retval = false;
        var ans = check_itemsvalidate('#divEditSupplier input');

        if (ans) {
            var SupplierID = page.query.SupplierId;
            if (SupplierID != null && SupplierID != "" && SupplierID != "undefined") {
                //UpdateSupplierDetail({ MethodName: 'UpdateSupplierDetail', SupplierID: SupplierID, Supplier: $("#txtSupplier").val(), Contact: $("#txtContact").val(), Phone: $("#txtPhone").val(), Fax: $("#txtFax").val(), Email: $("#txtEmail").val() });
                UpdateSupplierDetail('MethodName=UpdateSupplierDetail&SupplierID=' + SupplierID + '&Supplier=' + $("#txtEditSupplier").val() + '&Contact=' + $("#txtEditContact").val() + '&Phone=' + $("#txtEditPhone").val() + '&Fax=' + $("#txtEditFax").val() + '&Email=' + $("#txtEditEmail").val());
            } else {
                return false;
            }
        } else {
            return false;
        }
    });

    $("#btndelete").on("click", function() {
        if (confirm('Are you sure?')) {

            var SupplierID = page.query.SupplierId;
            if (SupplierID != null && SupplierID != "" && SupplierID != "undefined") {

                //DeleteSupplierById({ MethodName: 'DeleteSupplierById', SupplierID: SupplierID, AccountID: loginaccountid });
                //DeleteSupplierById('MethodName=DeleteSupplierById&SupplierID='+ SupplierID +'&AccountID='+ loginaccountid );
                DeleteSupplierById('MethodName=DeleteSupplierById&SupplierID=' + SupplierID);
            } else {
                return false;
            }

        } else {
            return false;
        }
    });

    $("#btnEditCancel").on("click", function() {
        jQuery(".formError").remove();
        mainView.loadPage("suppliers.html");
    });

});

myApp.onPageInit('materials', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    GetCatListforMaterial('MethodName=GetCatListforMaterial');

    if (!Checkaccess('view_materials')) {
        $('#lblMsgForMaterialMainDiv').html("Insufficient Rights");
        $('#lblMsgForMaterialMainDiv').css("display", "block");
        $('#divMainDivOfMaterials').css("display", "none");
    } else {
        $('#lblMsgForMaterialMainDiv').html("");
        $('#lblMsgForMaterialMainDiv').css("display", "none");
        $('#divMainDivOfMaterials').css("display", "block");
    }

    function GetCatListforMaterial(data) {
        try {


            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: 'POST',
                data: data,
                cache: false,
                success: function(data, textStatus, jqxhr) {

                    var data = $.parseJSON(data);
                    $('#lstMatCat').empty();
                    var items = [];
                    if (data.length > 0) {
                        //items.push('<table style="width:100%;" class="Table"><tr><td style="width:7%;" class="gridHeader">Title</td><td style="width:12%;" class="gridHeader">Date</td></tr>');
                        items.push('<table class="table"><tr><th>Material Category</th></tr>');
                        for (var i = 0; i < data.length; i++) {
                            //$('#lstrepairs').append('<li>' + data[i].post.user_id + '</li><li>' + data[i].post.account_id + '</li><li>' + data[i].post.notes + '</li><li><br>');
                            items.push('<tr><td><a href="javascript:;"  id=CatId_' + data[i].Post.category_id + ' class="aCatTitle">' + data[i].Post.category + '</a></td></tr>');
                        }
                        items.push('</table>');

                    } else {
                        items.push('<table class="table"><tr><th>Material Category</th></tr>');
                        items.push('<tr><td class="acenter">No Categories Found</td></tr>');
                        items.push('</table>');
                        //$('#lblMatCat').html("No Categories Found");
                        //$("#lblMatCat").css("display", "block");
                    }
                    $('#lstMatCat').append(items.join(''));
                    $(".aCatTitle").on("click", function() {

                        var splits_id = this.id.split('_');
                        var MaterialCatID = splits_id[1];

                        GetMatDetailForCategory('MethodName=GetMatDetailForCategory&CategoryID=' + MaterialCatID);
                        return false;
                    });
                },
                error: function(jqxhr, textStatus, errorMessage) {
                    GetCatListforMaterial('MethodName=GetCatListforMaterial');
                }
            })
        } catch (e) {

        }
    }

    function GetMatDetailForCategory(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $("#divMatCat").css("display", "none");
                $("#divMatDetail").css("display", "block");
                $('#lstMatDetail').empty();
                var items = [];
                items.push('<table class="table"><th>Materials</th>');
                if (data.categoryArray.length > 0) {
                    for (var i = 0; i < data.categoryArray.length; i++) {
                        items.push('<tr><td>' + data.categoryArray[i].category + '</td></tr>');
                    }
                    items.push('</table>');
                } else {
                    items.push('<tr style="padding-bottom:10px;"><td><b>No materials found</b></td></tr>');
                }
                $('#lstMatDetail').append(items.join(''));

                $('#lstBrandDetail').empty();
                var items3 = [];
                items3.push('<table class="table">')
                var BrandId = 0;

                if (data.matDetailSql.length > 0) {

                    for (var i = 0; i < data.matDetailSql.length; i++) {

                        if (data.matDetailSql[i].brand_id != "0") {
                            if (BrandId == data.matDetailSql[i].brand_id) {
                                items3.push('<tr style="padding-bottom:10px;"><td  style="width:30%">' + data.matDetailSql[i].material + '</td><td>' + data.matDetailSql[i].info + '<br /><b>Unit: </b>' + data.matDetailSql[i].unit + '<br /><b>Price: $</b>' + data.matDetailSql[i].price + '</td></tr>');
                            } else {
                                BrandId = data.matDetailSql[i].brand_id;
                                var brandCNT = "";
                                if (data.brandcount.length > 0) {
                                    for (var k = 0; k < data.brandcount.length; k++) {
                                        if (data.brandcount[k].brand_id == "-1") {
                                            brandCNT = " (" + data.brandcount[k].count + ")";
                                        } else if (data.brandcount[k].brand_id == BrandId) {
                                            brandCNT = " (" + data.brandcount[k].count + ")";
                                        }
                                    }
                                }
                                if (data.matDetailSql[i].brand_id == "-1") {
                                    items3.push('<tr style="padding-bottom:10px;"><td colspan="2" style="width:30%; font-weight:bold;">Varies ' + brandCNT + '</td></tr><tr><td>' + data.matDetailSql[i].material + '</td><td>' + data.matDetailSql[i].info + '<br /><b>Unit: </b>' + data.matDetailSql[i].unit + '<br /><b>Price: $</b>' + data.matDetailSql[i].price + '</td></tr>');
                                    brandCNT = "";
                                } else {
                                    items3.push('<tr><td colspan="2" style="font-weight:bold;">' + data.matDetailSql[i].brand + ' ' + brandCNT + '</td></tr><tr style="padding-bottom:10px;"><td style="width:30%">' + data.matDetailSql[i].material + '</td><td>' + data.matDetailSql[i].info + '<br /><b>Unit: </b>' + data.matDetailSql[i].unit + '<br /><b>Price: $</b>' + data.matDetailSql[i].price + '</td></tr>');
                                    brandCNT = "";
                                }

                            }
                        }
                    }

                } else {
                    items3.push('<tr style="padding-bottom:10px;"><td><b>No materials found</b></td></tr>');


                }
                items3.push('</table>');
                $('#lstBrandDetail').append(items3.join(''));
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function GetCategoryDetail(Id) {

    }
    $('#aBack').on("click", function() {
        $("#divMatCat").css("display", "block");
        $("#divMatDetail").css("display", "none");
        //  location.href = "dashboard.html";
    });

});
myApp.onPageInit('home', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    AjaxCall('MethodName=BindMenu');
});
myApp.onPageInit('jobs', function(page) {

    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    $body = $("body");
    $body.addClass("loading");
    CheckUserLogin();
    $("#txtjob_Search").val("");
    var FunctionOne = function() {

        var $ = jQuery.noConflict();
        // create a deferred object
        var r = $.Deferred();
        BindAllJobFilters();
        // do whatever you want (e.g. ajax/animations other asyc tasks)

        setTimeout(function() {
            // and call `resolve` on the deferred object, once you're done
            r.resolve();
        }, 2500);

        // return the deferred object
        return r;
    };

    // define FunctionTwo as needed
    var FunctionTwo = function() {

        GetFilterJoblist();
    };

    if (!CheckmoduleOwnership('view_jobs') && Checkaccess('view_jobs')) {
        // BindAllJobFilters();
        FunctionOne().done(FunctionTwo);
    }

    $('#txtjob_Search').keyup(function() {
        GetFilterJoblist();
    });
    $('#chkhideholdjob').on("click", function() {
        GetFilterJoblist();
    });
    $('#drd_job_sort').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });
    $('#drd_ser_salesman').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });
    $('#drd_ser_stages').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });
    $('#drd_ser_type').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });
    $('#drd_ser_task').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });
    $('#drd_ser_warranty').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });

    $('#drd_ser_provider').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });
    $('#drd_ser_canvasser').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });
    $('#drd_ser_referral').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });

    $('#drd_ser_creator').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });
    $('#drd_ser_age').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });
    $('#drd_ser_juridiction').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });
    $('#drd_ser_permit').change(function() {
        GetFilterJoblist();
        myApp.closeModal('#divjobfilter');
    });


    $('#lnkresetjobs').on("click", function myfunction() {
        var $ = jQuery.noConflict();

        $("#drd_job_sort").val('j.timestamp desc');
        $("#drd_ser_salesman").val(' ');
        $("#drd_ser_stages").val(' ');
        $("#drd_ser_type").val(' ');
        $('#drd_ser_task').val(' ');
        $('#drd_ser_warranty').val(' ');
        $('#drd_ser_provider').val(' ');
        $('#drd_ser_canvasser').val(' ');
        $('#drd_ser_referral').val(' ');
        $('#drd_ser_creator').val(' ');
        $('#drd_ser_age').val(' ');
        $('#drd_ser_juridiction').val(' ');
        $('#drd_ser_permit').val(' ');

        GetFilterJoblist();

    });


    // call FunctionOne and use the `done` method
    // with `FunctionTwo` as it's parameter


    function BindAllJobFilters() {
        var $ = jQuery.noConflict();
        var data = 'MethodName=BindAllJobFilters';
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);

                $("#drd_job_sort").val('j.timestamp desc');
                $("#drd_ser_salesman").empty();
                $("#drd_ser_salesman").append(Result.salesmen);
                //$("#drd_ser_salesman").append("<option value=' ' selected  >Salesman</option>");
                //$("#drd_ser_salesman").append("<option value='and j.salesman is null' >No Salesmen Assigned</option>");


                //$.each(Result.salesmen, function (key, value) {
                //    $("#drd_ser_salesman").append("<option value='AND j.salesman= " + value.select_label + "'>" + value.select_label + "</option>");

                //});


                $("#drd_ser_stages").empty();
                $("#drd_ser_stages").append("<option value=' ' selected>Stage/Status</option>");
                // $("#drd_ser_stages").append("<option value='and r.repair_id is not null'>Has Repair(s)</option>");
                $("#drd_ser_stages").append("<optgroup label='Status Hold'></optgroup>");
                $("#drd_ser_stages").append("<option value='AND (sh.status_id IS NOT NULL AND (sh.expires IS NULL || sh.expires >= CURDATE()))'>All</option>");
                $.each(Result.statuses, function(key, value) {
                    $("#drd_ser_stages").append("<option value='AND sh.status_id= " + value.status_id + "'>" + value.status + "</option>");

                });
                $("#drd_ser_stages").append("<optgroup label='Stage'>");
                $.each(Result.stages, function(key, value) {
                    $("#drd_ser_stages").append($("<option></option>").val('AND j.stage_num= ' + value.stage_num).html(value.stage));
                });

                $("#drd_ser_type").empty();
                $("#drd_ser_type").append("<option value=' ' selected>Type</option>");
                $.each(Result.jobTypesArray, function(key, value) {
                    $("#drd_ser_type").append("<option value='and j.job_type=" + value.job_type_id + "'>" + value.job_type + "</option>");
                });


                $("#drd_ser_task").empty();
                $("#drd_ser_task").append("<option value=' ' selected>Task</option>");
                $.each(Result.taskTypes, function(key, value) {
                    $("#drd_ser_task").append("<option value='and t.task_type=" + value.task_type_id + "'>" + value.task + "</option>");
                });

                $("#drd_ser_warranty").empty();
                $("#drd_ser_warranty").append("<option value=' ' selected>Warranty</option>");
                $.each(Result.warranties, function(key, value) {
                    $("#drd_ser_warranty").append("<option value='and jm.meta_value =" + value.warranty_id + "'>" + value.label + "</option>");
                });


                $("#drd_ser_provider").empty();
                $("#drd_ser_provider").append("<option value=' ' selected>Provider</option>");
                $.each(Result.providers, function(key, value) {
                    $("#drd_ser_provider").append("<option value= 'AND j.insurance_id =" + value.insurance_id + "'>" + value.insurance + "</option>");
                });

                $("#drd_ser_canvasser").empty();
                $("#drd_ser_canvasser").append(Result.canvasser);
                //$("#drd_ser_canvasser").append("<option value=' ' selected>Canvasser</option>");
                //$("#drd_ser_canvasser").append("<option value='and cv.canvasser_id is null'>No Canvasser Assigned</option>");

                //$.each(Result.canvasser, function (key, value) {
                //    $("#drd_ser_canvasser").append("<option value= 'AND cv.user_id=" + value.user_id + "'>" + value.select_label + "</option>");
                //});

                $("#drd_ser_referral").empty();
                $("#drd_ser_referral").append(Result.referral);
                //$("#drd_ser_referral").append("<option value=' ' selected>Referral</option>");
                //$("#drd_ser_referral").append("<option value='and j.referral is null'>No Referral Assigned</option>");

                //$.each(Result.referral, function (key, value) {
                //    $("#drd_ser_referral").append("<option value='AND j.referral=" + value.user_id + "'>" + value.select_label + "</option>");
                //});


                $("#drd_ser_creator").empty();
                $("#drd_ser_creator").append(Result.creator);

                $("#drd_ser_juridiction").empty();
                $("#drd_ser_juridiction").append("<option value=' ' selected>Jurisdiction</option>");
                $.each(Result.jurisdictions, function(key, value) {
                    $("#drd_ser_juridiction").append("<option value='AND j.jurisdiction=" + value.jurisdiction_id + "'>" + value.location + "</option>");
                });

            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindAllJobFilters();
                // navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    function GetFilterJoblist() {

        var $ = jQuery.noConflict();

        var Hidehold = '';
        if ($('#chkhideholdjob').is(":checked")) {
            Hidehold = '1';
        }
        var data = 'MethodName=GetFilterJoblist&salesman=' + $("#drd_ser_salesman").val() + '&referral=' + $("#drd_ser_referral").val();
        data += '&canvasser=' + $("#drd_ser_canvasser").val() + '&stage=' + $("#drd_ser_stages").val() + '&creator=' + $("#drd_ser_creator").val();
        data += '&type=' + $("#drd_ser_type").val() + '&sort=' + $("#drd_job_sort").val() + '&age=' + $("#drd_ser_age").val();
        data += '&jurisdiction=' + $("#drd_ser_juridiction").val() + '&task_type=' + $("#drd_ser_task").val() + '&permit_expires=' + $("#drd_ser_permit").val();
        data += '&warranty=' + $("#drd_ser_warranty").val() + '&insurance_provider=' + $("#drd_ser_provider").val() + '&search=' + $("#txtjob_Search").val();
        data += '&limit=' + $("#hdnoffset").val() + '&hidehold=' + Hidehold;

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                if (data && data !== 'Job not found') {

                    Result = $.parseJSON(data);

                    $('#tbljoblist').empty();
                    $('#divjobpaging').empty();
                    var strrow = '<tr><th>Job Number</th><th>Insured</th><th>Address</th><th>Customer</th><th>Stage</th><th>Type</th><th>Age</th><th>DAS</th></tr>';
                    $('#tbljoblist').append(strrow);
                    var repairStr = "";
                    var todaydate = new Date();



                    var currentOffset = Result.currentOffset;
                    if (Result.joblist.length > 0) {
                        console.log(Result.joblist);
                        currentOffset = parseInt(currentOffset) + parseInt(Result.joblist.length);
                        for (var i = 0; i < Result.joblist.length; i++) {

                            var job = Result.joblist[i];
                            repairStr = "";
                            var holdClass = "";

                            var holdClass = "";
                            if (job.IsHold != "") {
                                //holdClass = "Class=" + job.IsHold;
                                holdClass = "Class=greytext_blk";
                            }
                            if (job.class == "null" || job.class == "hold") {
                                holdClass = "Class=greytext_blk";
                            }

                            var expirationDate = todaydate;
                            if (job.status_hold_expires != undefined && job.status_hold_expires != null && job.status_hold_expires != "")
                                expirationDate = new Date(job.status_hold_expires);

                            var status_hold = '';


                            var duration = job.duration;

                            var Dasstr = "";
                            var diff = job.Stageage - job.duration;

                            var color = "black";

                            if (duration != '9999' && diff > -3) {
                                if (diff < 0)
                                    color = "yellow";
                                else if (diff < 6)
                                    color = "orange";
                                else
                                    color = "red";
                            }


                            if (duration == '9999' || duration == null || $.isNumeric(duration) == false) {


                                duration = "No Limit";
                            }

                            if (job.Stageage == null) {
                                job.Stageage = "736254";
                                color = "red";
                            }

                            if (job.Repairs != "") {
                                repairStr = ", <span style='color: red; font-weight: bold;'>REPAIR</span>";
                            }

                            var stagestr = '<b>#' + job.stage_num + ':</b> ' + job.stagename + repairStr;

                            if (job.status_id != null && expirationDate.getDate() >= todaydate.getDate()) {

                                stagestr = " <b>HOLD:</b> " + job.status;

                                if (job.status_hold_expires != undefined && job.status_hold_expires != null && job.status_hold_expires != '00/00/00' && job.status_hold_expires != "") {

                                    stagestr = stagestr + " (exp. " + job.status_hold_expires + ")";
                                }
                            }

                            if (job.sname == null)
                                job.sname = '';

                            if (job.job_type == null)
                                job.job_type = '';

                            strrow = '<tr ' + holdClass + ' onclick=Bindjobdata(' + job.job_id + ');><td><b>' + job.job_number + '</b></td><td>' + job.custName + '</td><td>' + job.address + '</td><td>' + job.sname + '</td><td>' + stagestr + '</td><td>' + job.job_type + '</td><td>' + job.Agedays + '</td><td><span style=color:' + color + '><b>' + job.Stageage + '</b>/' + duration + '</span></td></tr>';
                            $('#tbljoblist').append(strrow);

                        }

                        var items = [];

                        if (Result.totalJobs > 10) {
                            items.push('<center><a id="btnprev" class="button_small"  style="visibility:hidden;cursor:pointer;"> </a>&nbsp;&nbsp;');
                            items.push('<label>Showing: ' + (parseInt(Result.currentOffset) + 1) + " - " + currentOffset + " of " + parseInt(Result.totalJobs) + '</label>');
                            items.push('&nbsp;&nbsp;<a id="btnnext" style="cursor:pointer;" class="button_small" > </a></center>');

                            $('#divjobpaging').append(items.join(''));

                            $('#btnnext').on("click", function() {
                                getnextjobs();
                            });
                            $('#btnprev').on("click", function() {
                                getprevjobs();
                            });
                            if (parseInt(Result.totalJobs) == parseInt(currentOffset)) {
                                document.getElementById('btnnext').style.visibility = 'hidden';
                                document.getElementById('btnprev').style.visibility = 'visible';
                            } else if (parseInt(Result.totalJobs) < parseInt(currentOffset)) {
                                document.getElementById('btnnext').style.visibility = 'visible';
                                document.getElementById('btnprev').style.visibility = 'Hidden';
                            } else {
                                if (parseInt(currentOffset) == 10) {
                                    document.getElementById('btnprev').style.visibility = 'Hidden';
                                } else {
                                    document.getElementById('btnnext').style.visibility = 'visible';
                                    document.getElementById('btnprev').style.visibility = 'visible';
                                }
                            }
                        }

                    } else {
                        var strrow = '<tr><td colspan="8"><center><b>No Jobs Found</b> </center></tr>';
                        $('#tbljoblist').append(strrow);
                    }
                    $body.removeClass("loading");
                }else{
                    GetFilterJoblist();
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                // navigator.notification.alert(
                //     errorMessage, alertDismissed, "An error occured", "Done"
                // );
                GetFilterJoblist();
            }
        });
    }

    function getnextjobs() {
        var offset = 0;
        var x = document.getElementById("hdnoffset").value;
        offset = parseInt(x) + 10;
        document.getElementById("hdnoffset").value = offset;
        GetFilterJoblist();
    }

    function getprevjobs() {
        var offset = 0;
        var x = document.getElementById("hdnoffset").value;
        offset = parseInt(x) - 10;
        document.getElementById("hdnoffset").value = offset;
        GetFilterJoblist();
    }

});



function Bindjobdata(JId) {

    mainView.router.loadPage("jobtabs.html?JId=" + JId, { ignoreCache: true });
    //mainView.router.load({
    //    url: "jobtabs.html?JId=" + JId

    //})
}

function CheckmoduleOwnership(hook) {
    var $ = jQuery.noConflict();
    var data = 'MethodName=CheckmoduleOwnership&Hook=' + hook;
    $.ajax({
        url: "https://xactbid.pocketofficepro.com/workflowservice.php",
        type: "POST",
        data: data,
        cache: false,
        success: function(data, textStatus, jqxhr) {
            data = $.parseJSON(data);
            return data.value;
        },
        error: function(jqxhr, textStatus, errorMessage) {
            navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
        }
    })
}


myApp.onPageInit('reports', function(page) {

    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    BindReportFilters();

    function BindReportFilters() {

        var data = 'MethodName=BindReportFilters';
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                data = $.parseJSON(data);
                $('#lstfiltertab').empty();
                $('#lstfiltertab').append(data.strfilter);
                $('#divfilterfields').empty();
                $('#divfilterfields').append(data.filterfields);

                $('.lnkfilter').on("click", function() {
                    $(".divfilters").hide();
                    var divId = $(this).attr("data-tab");
                    $("#divfilter" + divId).show();
                });
                $('.lnksavereports').on("click", function() {
                    var data = 'MethodName=Generatereport';
                    var str = $.parseJSON($(this).data('query').replace(/\//g, '').replace('&nbsp;', ' '));
                    $.each(str, function(key, value) {
                        data = data + '&' + key + '=' + value;
                    });

                    GenerateReport(data);
                });

            },
            error: function(jqxhr, textStatus, errorMessage) {
                 BindReportFilters();
            }
        })
    }

    function GenerateReport(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                data = $.parseJSON(data);
                $('#divreports').empty();
                $('#divreports').append(data.strreport);
            },
            error: function(jqxhr, textStatus, errorMessage) {
                GenerateReport(data);
            }
        })
    }

});
myApp.onPageInit('settings', function(page) {

    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    BindSettingsDetails();

    function UpdateSettings(data, flag) {

        $.when($.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);

                if (Result.status == '0') {
                    //alert(Result.errors[0]);
                } else {
                    if (Result.info != null && Result.info != undefined && Result.info.length > 0) {
                        //alert(Result.info[0]);
                        myApp.closeModal('#divchangepassword');
                    }
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindSettingsDetails();
            }
        }).then(BindSettingsDetails()));
    }

    function BindSettingsDetails() {
        var data = 'MethodName=Getthesettingsdetails';
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                data = $.parseJSON(data);

                $('#tblbasicinfosetting').empty();
                $('#tblbasicinfosetting').append(data.strbasicinfo);
                $("#lnkeditsetingdba").on("click", function myfunction() {
                    $("#lblsettingdba").css("display", "none");

                    $("#txtsetingdba").css("display", "block");
                    $("#lnkeditsetingdba").css("display", "none");
                    $("#txtsetingdba").focus();
                });

                $("#txtsetingdba").focusout(function myfunction() {
                    UpdateSettings('MethodName=UpdateMainAppSettings&flag=1&dba=' + $("#txtsetingdba").val().trim() + '&email=' + $('#txtsetingemail').val().trim() + '&phone=' + $('#txtsetingphone').val().trim() + '&sms=' + $('#drdsetingsms').val());
                });
                $("#txtsetingemail").focusout(function myfunction() {
                    UpdateSettings('MethodName=UpdateMainAppSettings&flag=1&dba=' + $("#txtsetingdba").val().trim() + '&email=' + $('#txtsetingemail').val().trim() + '&phone=' + $('#txtsetingphone').val().trim() + '&sms=' + $('#drdsetingsms').val());
                });
                $("#txtsetingphone").focusout(function myfunction() {
                    UpdateSettings('MethodName=UpdateMainAppSettings&flag=1&dba=' + $("#txtsetingdba").val().trim() + '&email=' + $('#txtsetingemail').val().trim() + '&phone=' + $('#txtsetingphone').val().trim() + '&sms=' + $('#drdsetingsms').val());
                });
                $("#drdsetingsms").change(function myfunction() {
                    UpdateSettings('MethodName=UpdateMainAppSettings&flag=1&dba=' + $("#txtsetingdba").val().trim() + '&email=' + $('#txtsetingemail').val().trim() + '&phone=' + $('#txtsetingphone').val().trim() + '&sms=' + $('#drdsetingsms').val());
                });

                $("#lnkeditsetingemail").on("click", function myfunction() {
                    $("#lblsettingemail").css("display", "none");
                    $("#txtsetingemail").css("display", "block");
                    $("#lnkeditsetingemail").css("display", "none");
                    $("#txtsetingemail").focus();
                });
                $("#lnkeditsetingphone").on("click", function myfunction() {
                    $("#lblsettingphone").css("display", "none");
                    $("#txtsetingphone").css("display", "block");
                    $("#lnkeditsetingphone").css("display", "none");
                    $("#txtsetingphone").focus();
                });

                $("#lnkeditsetingsms").on("click", function myfunction() {
                    $("#lblsettingsms").css("display", "none");
                    $("#drdsetingsms").css("display", "block");
                    $("#lnkeditsetingsms").css("display", "none");
                    $("#drdsetingsms").focus();
                });
                $('.masked-phone').inputmask('(999) 999-9999', { placeholder: ' ' });



                $('#tblinterfacesetting').empty();
                $('#tblinterfacesetting').append(data.strInterface);
                $('#tblstagesetting').empty();
                $('#tblstagesetting').append(data.strstagesetting);
                $('#tbljobsubscriptionsetting').empty();
                $('#tbljobsubscriptionsetting').append(data.strjobsetting);
                $('#tblconversationsetting').empty();
                $('#tblconversationsetting').append(data.strconversationsetting);

                $('.lnkchangewidgetstatus').on("click", function() {
                    var data = $(this).attr('data-query');

                    UpdateSettings('MethodName=UpdateMainAppSettings&flag=2&widget=' + data);
                    return false;
                });
                $("#drdchangenameord").change(function myfunction() {
                    navigator.notification.alert(
                        "Name Order saved", alertDismissed, "Successful", "Done"
                    );
                    UpdateSettings('MethodName=UpdateMainAppSettings&flag=3&key=name_order&value=' + $("#drdchangenameord").val().trim());
                });
                if (data.dailysetting != '') {
                    $('#chksetnotification').attr('checked', 'checked');
                }
                $("#chksetnotification").on("click", function myfunction() {

                    if ($(this).prop('checked')) {
                        navigator.notification.alert(
                            "Daily Schedule saved", alertDismissed, "Successful", "Done"
                        );
                        UpdateSettings('MethodName=UpdateMainAppSettings&flag=3&key=daily_schedule&value=1');
                    } else {
                        // alert("Daily Schedule saved");
                        navigator.notification.alert(
                            "Daily Schedule saved", alertDismissed, "Successful", "Done"
                        );
                        UpdateSettings('MethodName=UpdateMainAppSettings&flag=3&key=daily_schedule&value=0');
                    }
                });

                $(".lnkchangenotification").on("click", function myfunction() {
                    var data = $(this).attr('data-query');
                    if ($(this).prop('checked'))
                        UpdateSettings('MethodName=UpdateMainAppSettings&flag=4&status=1&' + data);
                    else
                        UpdateSettings('MethodName=UpdateMainAppSettings&flag=4&status=0&' + data);
                });
                $(".lnkredictjoblink").on("click", function() {
                    var Jid = $(this).attr('data-Jid');
                    mainView.loadPage("jobtabs.html?JId=" + Jid);
                });
                $(".lnkdeleteconvesation").on("click", function() {
                    if (confirm('Are you sure?')) {
                        var conversation_id = $(this).attr('data-conversation-id');
                        UpdateSettings('MethodName=UpdateMainAppSettings&flag=5&conversationid=' + conversation_id + '&type=job');
                    }
                    return false;
                });
                $("#lnkchangepassword").on("click", function() {
                    $('#txtcurpassword').val("");
                    $('#txtnewpassword').val("");
                    $('#txtconfirmpassword').val("");
                    myApp.popup('#divchangepassword');
                    $('#btnchangepassword').on("click", function() {
                        UpdateSettings('MethodName=UpdateMainAppSettings&flag=6&current_password=' + $('#txtcurpassword').val().trim() + '&password=' + $('#txtnewpassword').val().trim() + '&password_confirm=' + $('#txtconfirmpassword').val().trim(), 1);
                        return false;
                    });
                    return false;
                });




            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindSettingsDetails();
            }
        });
        return false;
    }


});
myApp.onPageInit('job-map', function(page) {
 var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    var location = page.query.location;

    if (location != null && location != "" && location != "undefined") {
        BindJobMapDetails('MethodName=BindJobMapDetails&address=' + location + '&radius=5');
    }

    myfunction();

    function myfunction() {

        var mapOptions = {
            center: new google.maps.LatLng(39.8282, -98.5795),
            zoom: 10,
            draggable: true,
            disableDoubleClickZoom: true,
            disableDefaultUI: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP,

            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
            },
        };
        var infoWindow = new google.maps.InfoWindow();
        document.getElementById("jobs-map").innerHTML = "";
        var map = new google.maps.Map(document.getElementById("jobs-map"), mapOptions);
    }

    function BindJobMapDetails(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                data = $.parseJSON(data);

                $strSearchLoc = new google.maps.LatLng(39.8282, -98.5795);

                if (data.flag == '0') {

                    var zoom = 8;
                    var radius = data.jobs.zoom;
                    if (radius > 40) {
                        zoom = 9;
                    } else if (radius > 30) {
                        zoom++;
                    } else if (radius > 20) {
                        zoom += 2;
                    } else if (radius > 10) {
                        zoom += 2;
                    } else {
                        zoom += 3;
                    }

                    $strSearchLoc = new google.maps.LatLng(data.jobs.lat, data.jobs.long)
                    var mapOptions = {
                        //center: new google.maps.LatLng(data.jobs[0].lat, data.jobs[0].long),
                        center: $strSearchLoc,
                        zoom: zoom,
                        draggable: true,
                        disableDoubleClickZoom: true,
                        disableDefaultUI: true,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,

                        mapTypeControlOptions: {
                            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                        },
                    };

                    var infoWindow = new google.maps.InfoWindow();
                    document.getElementById("jobs-map").innerHTML = "";
                    var map = new google.maps.Map(document.getElementById("jobs-map"), mapOptions);
                } else if (data.jobs.length > 0) {
                    $strSearchLoc = new google.maps.LatLng(data.jobs[0].lat, data.jobs[0].long)
                    var mapOptions = {
                        //center: new google.maps.LatLng(data.jobs[0].lat, data.jobs[0].long),
                        center: $strSearchLoc,
                        zoom: 10,
                        draggable: true,
                        disableDoubleClickZoom: true,
                        disableDefaultUI: true,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,

                        mapTypeControlOptions: {
                            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                        },
                    };

                    var infoWindow = new google.maps.InfoWindow();
                    document.getElementById("jobs-map").innerHTML = "";
                    var map = new google.maps.Map(document.getElementById("jobs-map"), mapOptions);
                    if (data.jobs.length > 0) {
                        for (var i = 0; i < data.jobs.length; i++) {

                            var data1 = data.jobs[i];
                            //var markercontent = data1.bubblecontent.replace('https://xactbid.pocketofficepro.com/jobs.php?id', 'jobtabs.html?JId');
                            //markercontent = markercontent.replace('https://xactbid.pocketofficepro.com/users.php?id', 'get_user.html?UserID');
                            //markercontent = markercontent.replace('https://xactbid.pocketofficepro.com/customers.php?id', 'edit_customer.html?CustomerId');

                            var myLatlng = new google.maps.LatLng(data1.lat, data1.long);

                            var marker = new google.maps.Marker({
                                position: myLatlng,
                                map: map,
                                title: data1.customer_id
                            });
                            (function(marker, data1) {
                                google.maps.event.addListener(marker, "click", function(e) {
                                    infoWindow.setContent(data1.bubblecontent);
                                    infoWindow.open(map, marker);
                                });
                            })(marker, data1);

                        }
                    }
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindJobMapDetails(data);
            }
        });
        return false;
    }
    $('#btnsearchjobmap').on("click", function() {
        var ans = check_itemsvalidate('#divjobmapfilters input');
        if (ans) {
            BindJobMapDetails('MethodName=BindJobMapDetails&address=' + $('#txtmapaddress').val().trim() + '&radius=' + $('#drdradious').val());
        } else {
            return false;
        }
        return false;
    });
});
myApp.onPageInit('customers', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    $('#sort').change(function() {
        ViewSortCategory(this);
    });
    $('#txt_Search').change(function() {
        searchcustomer();
    });

    function searchcustomer() {
        document.getElementById("hdnoffset").value = "0";
        var searchtext = $("#txt_Search").val();
        var searchtype = $("#sort").val();
        AjaxCall('MethodName=GetCustomerList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=' + document.getElementById("hdnoffset").value + '');


    }

    var searchtype = $("#sort").val();
    var searchtext = $("#txt_Search").val();
    //AjaxCall({ MethodName: 'GetCustomerList', SearchType: searchtype, SearchText: searchtext, limit: 10, offset: document.getElementById("hdnoffset").value });
    AjaxCall('MethodName=GetCustomerList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=' + document.getElementById("hdnoffset").value + '');

    function AjaxCall(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                data = $.parseJSON(data);
                var currentOffset = parseInt(data.currentofset);
                if (data.custlist.length > 0) {
                    currentOffset = parseInt(currentOffset) + parseInt(data.custlist.length);
                    $('#tblCustomer').empty();
                    $('#tblCustomer').attr("style", "display:block");
                    var items = [];
                    items.push('<table class="table"><tr><th>Name</th><th>Nick Name</th><th>Timestamp</th><th>Added By</th></tr>');
                    // items.push('<table class="table"><tr><th>Name</th><th>Nick Name</th><th>Timestamp</th></tr>');
                    for (var i = 0; i < data.custlist.length; i++) {
                        items.push('<tr><td><a id="aCustDetail" href="edit_customer.html?CustomerId=' + data.custlist[i].customer_id + '">' + data.custlist[i].fname + ' ' + data.custlist[i].lname + '</a></td><td>' + data.custlist[i].nickname + '</td><td> ' + data.custlist[i].timestamp + '</td><td>&nbsp;</td></tr>');
                    }
                    items.push('</table>');
                    document.getElementById("hdntotalrecord").value = data.totalrecord;
                    if (data.totalrecord > 10) {
                        items.push('<a id="btnprev" class="button_small"  style="visibility:hidden;cursor:pointer;"> </a>&nbsp;&nbsp;');
                        items.push('<label>Showing: ' + (parseInt(data.currentofset) + 1) + " - " + currentOffset + " of " + parseInt(data.totalrecord) + '</label>');
                        items.push('&nbsp;&nbsp;<a id="btnnext" style="cursor:pointer;" class="button_small" > </a>');
                    }
                    $('#tblCustomer').append(items.join(''));
                    $('#btnnext').on("click", function() {
                        callnext10();
                    });
                    $('#btnprev').on("click", function() {
                        callPrev10();
                    });
                    if (data.totalrecord > 10) {
                        if (parseInt(data.totalrecord) == parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'hidden';
                            document.getElementById('btnprev').style.visibility = 'visible';
                        } else if (parseInt(data.totalrecord) < parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'visible';
                            document.getElementById('btnprev').style.visibility = 'Hidden';
                        } else {
                            if (parseInt(data.currentofset) == 0) {
                                document.getElementById('btnprev').style.visibility = 'Hidden';
                            } else {
                                document.getElementById('btnnext').style.visibility = 'visible';
                                document.getElementById('btnprev').style.visibility = 'visible';
                            }
                        }
                    }
                } else {

                    $('#tblCustomer').empty();

                    var items = [];
                    items.push('<table style="width:100%;" class="gridTable"><tr><td style="width:7%;" class="gridHeader">Name</td><td style="width:12%;" class="gridHeader">Nick Name</td><td style="width:12%;" class="gridHeader">Timestamp</td><td style="width:12%;" class="gridHeader">Added By</td></tr>');
                    items.push('<tr><td colspan="4"><label>no records found for the Insured</label></td></tr>');
                    items.push('</table>');
                    $('#tblCustomer').append(items.join(''));
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                AjaxCall(data);
            }
        })
    }

    function ViewSortCategory(obj) {
        var searchtype = $("#sort").val();
        var searchtext = $("#txt_Search").val();
        //AjaxCall({ MethodName: 'GetCustomerList', SearchType: searchtype, SearchText: searchtext, limit: 10, offset: 0 });
        AjaxCall('MethodName=GetCustomerList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=0');
    }

    function callnext10() {
        var searchtype = $("#sort").val();
        var searchtext = $("#txt_Search").val();
        var offset = 0;
        var x = document.getElementById("hdnoffset").value;
        offset = parseInt(x) + 10;
        document.getElementById("hdnoffset").value = offset;
        //AjaxCall({ MethodName: 'GetCustomerList', SearchType: searchtype, SearchText: searchtext, limit: 10, offset: offset });
        AjaxCall('MethodName=GetCustomerList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=' + offset + '');
    }

    function callPrev10() {
        var searchtype = $("#sort").val();
        var searchtext = $("#txt_Search").val();
        var offset = 0;
        var x = document.getElementById("hdnoffset").value;
        offset = parseInt(x) - 10;
        document.getElementById("hdnoffset").value = offset;
        //AjaxCall({ MethodName: 'GetCustomerList', SearchType: searchtype, SearchText: searchtext, limit: 10, offset: offset });
        AjaxCall('MethodName=GetCustomerList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=' + offset + '');
    }

});

myApp.onPageInit('stages', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();

    //$(document).ready(function () {
    var flag = page.query.flag;
    if (flag == '2') {
        $("#divStageHeaderText").html("");
        $("#divStageHeaderText").html("System - Stages");
        $("#aLinkBackToSystemStages").css("display", "block");
    } else {
        $("#divStageHeaderText").html("");
        $("#divStageHeaderText").html("Stages");
        $("#aLinkBackToSystemStages").css("display", "none");
    }

    //BindStages('MethodName=GetStages&AccountID=' + loginaccountid);
    BindStages('MethodName=GetStages');

    //});
    $('#divchangeorder').on("click", function() {

        $('#divmainstages').css('display', 'none');
        $('#divstagechangeordersection').css('display', 'block');
        return false;
    });
    $('#divbktostage').on("click", function() {
        $('#divmainstages').css('display', 'block');
        $('#divstagechangeordersection').css('display', 'none');
        return false;
    });
    $('#btnupdatestage').on("click", function() {
        var $ = jQuery.noConflict();
        $body = $("body");
        var JobId = page.query.JId;
        var selected = new Array();

        $('#job_stage_table > li').each(function() {
            if ($(this).attr('id') != undefined)
                selected.push($(this).attr('id'));

        });

        $body.addClass("loading");
        var data = 'MethodName=UpdateStageOrder&stages=' + selected;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);
                navigator.notification.alert(
                    Result.status, alertDismissed, "Successful", "Done"
                );

                BindStages('MethodName=GetStages');

            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindStages('MethodName=GetStages');
            }
        });
        $body.removeClass("loading");

    });

    function BindStages(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                $("#aAddStage").css("display", "block");
                //$("#divStageHeaderText").html("");
                //$("#divStageHeaderText").html("Stages");
                $('#divmainstages').css('display', 'block');
                $('#divstagechangeordersection').css('display', 'none');
                var data = $.parseJSON(data);
                $('#lstStage').empty();
                var items = [];

                if (data.stageArray.length > 0) {
                    items.push('<table style="width:100%;" class="table"><tr><td style="font-weight:bold;">Number</td><td style="font-weight:bold;">Stage</td><td style="font-weight:bold;">Requirements</td><td>&nbsp;</td></tr>');
                    for (var i = 0; i < data.stageArray.length; i++) {
                        var stageid = data.stageArray[i].stage_id;
                        var strReqContent = "";
                        for (var j = 0; j < data.reqArray.length; j++) {
                            var matchStageId = data.reqArray[j].stage_id;

                            if (stageid == matchStageId) {
                                strReqContent = strReqContent + data.reqArray[j].label + " (" + data.reqArray[j].stage_req_id + ") <br/> ";
                            }
                        }
                        //items.push('<tr><td>' + data.stageArray[i].stage_num + '</td><td><input data-original="' + data.stageArray[i].stage + '" type="text" disabled="disabled" value="' + data.stageArray[i].stage + '" id="stage-name_' + data.stageArray[i].stage_id + '" /></td><td>' + strReqContent + '</td><td><a class="aEditStage" href="javascript:;" id="aEditStage_' + data.stageArray[i].stage_id + '">Edit</a><a class="aSaveEdit" href="javascript:;" id="aSaveEdit_' + data.stageArray[i].stage_id + '" style="display:none;">GO</a><a class="aResetEdit" href="javascript:;" id="aResetEdit_' + data.stageArray[i].stage_id + '" style="display:none;">Reset</a><input type="text" style="display:none;" value="' + data.stageArray[i].stage + '" id="reset-text_' + data.stageArray[i].stage_id + '" /><a class="aStageDelete" href="javascript:;" id="aStageDelete_' + data.stageArray[i].stage_id + '">&nbsp;&nbsp;Delete</a></td></tr>');
                        //items.push('<tr><td>' + data.stageArray[i].stage_num + '</td><td><label id="lblStageName_' + data.stageArray[i].stage_id + '" style="display:block;" >' + data.stageArray[i].stage + '</label><input data-original="' + data.stageArray[i].stage + '" type="text"  maxlength="40" style="display:none;" value="' + data.stageArray[i].stage + '" id="stage-name_' + data.stageArray[i].stage_id + '" class="chkstageVal form_input form-control validation validate[required[Stage name cannot be empty]]"/></td><td>' + strReqContent + '</td><td class="acenter"><a class="aEditStage" href="javascript:;" id="aEditStage_' + data.stageArray[i].stage_id + '"><i class="icon-pencil"></i></a><a class="aSaveEdit" href="javascript:;" id="aSaveEdit_' + data.stageArray[i].stage_id + '" style="display:none;"><i class="fa fa-chevron-circle-right"></i></a><a class="aResetEdit" href="javascript:;" id="aResetEdit_' + data.stageArray[i].stage_id + '" style="display:none;"><i class="fa fa-refresh"></i></a><input type="text" style="display:none;" value="' + data.stageArray[i].stage + '" id="reset-text_' + data.stageArray[i].stage_id + '" /><a class="aStageDelete" href="javascript:;" id="aStageDelete_' + data.stageArray[i].stage_id + '"><i class="icon-remove"></i></a></td></tr>');
                        items.push('<tr><td>' + data.stageArray[i].stage_num + '</td><td><label id="lblStageName_' + data.stageArray[i].stage_id + '" style="display:block;" >' + data.stageArray[i].stage + '</label><input data-original="' + data.stageArray[i].stage + '" type="text"  maxlength="40" style="display:none;" value="' + data.stageArray[i].stage + '" id="stage-name_' + data.stageArray[i].stage_id + '" class="chkstageVal form_input form-control validation validate[required[Stage name cannot be empty]]"/></td><td>' + strReqContent + '</td><td class="acenter"><a class="aEditStage" href="javascript:;" id="aEditStage_' + data.stageArray[i].stage_id + '"><i class="icon-pencil"></i></a><a class="aStageDelete" href="javascript:;" id="aStageDelete_' + data.stageArray[i].stage_id + '"><i class="icon-remove"></i></a><a class="aSaveEdit" href="javascript:;" id="aSaveEdit_' + data.stageArray[i].stage_id + '" style="display:none;"><i class="fa fa-chevron-circle-right"></i></a><a class="aResetEdit" href="javascript:;" id="aResetEdit_' + data.stageArray[i].stage_id + '" style="display:none;"><i class="fa fa-refresh"></i></a><input type="text" style="display:none;" value="' + data.stageArray[i].stage + '" id="reset-text_' + data.stageArray[i].stage_id + '" /></td></tr>');
                    }
                    items.push('</table>');

                } else {
                    items.push('<table style="width:100%;" class="table"><tr><td style="font-weight:bold;">Number</td><td style="font-weight:bold;">Stage</td><td style="font-weight:bold;">Requirements</td><td>&nbsp;</td></tr>');
                    items.push('<tr><td colspan="4" class="acenter">No Stage Found</td></tr>');
                    items.push('</table>');
                    //$('#lblStage').html("No Stage Found");
                    //$("#lblStage").css("display", "block");
                }
                $('#lstStage').append(items.join(''));
                $('#job_stage_table').empty();
                $('#job_stage_table').append(data.tblstage);
                $("#job_stage_table").sortable();

                $(".aEditStage").on("click", function() {

                    var splits_id = this.id.split('_');
                    var editStageID = splits_id[1];

                    $("#stage-name_" + editStageID).prop('disabled', false);
                    $("#aEditStage_" + editStageID).css("display", "none");
                    $("#aStageDelete_" + editStageID).css("display", "none");
                    $("#aSaveEdit_" + editStageID).css("display", "inline-block");
                    $("#aResetEdit_" + editStageID).css("display", "inline-block");

                    $("#lblStageName_" + editStageID).css("display", "none");
                    $("#stage-name_" + editStageID).css("display", "block");

                    return false;
                });

                $(".aSaveEdit").on("click", function() {
                    var retval = false;
                    var ans = check_itemsvalidate('.chkstageVal');
                    if (ans) {
                        var splits_id = this.id.split('_');
                        var saveStageID = splits_id[1];

                        $("#stage-name_" + saveStageID).attr('disabled', true);
                        $("#aEditStage_" + saveStageID).css("display", "inline-block");
                        $("#aStageDelete_" + saveStageID).css("display", "inline-block");
                        $("#aSaveEdit_" + saveStageID).css("display", "none");
                        $("#aResetEdit_" + saveStageID).css("display", "none");

                        UpdateStage('MethodName=UpdateStageDetail&StageID=' + saveStageID + '&stage=' + $("#stage-name_" + saveStageID).val());
                        BindStages('MethodName=GetStages');

                        $("#lblStageName_" + saveStageID).css("display", "block");
                        $("#stage-name_" + saveStageID).css("display", "none");

                        return false;
                    } else {
                        return false;
                    }
                });

                $(".aResetEdit").on("click", function() {

                    var splits_id = this.id.split('_');
                    var rstEditStageID = splits_id[1];
                    jQuery(".formError").remove();
                    var txtRestVal = $("#reset-text_" + rstEditStageID).val();
                    var editStageID = "stage-name_" + rstEditStageID;

                    $("#stage-name_" + rstEditStageID).val($("#reset-text_" + rstEditStageID).val())
                    $("#stage-name_" + rstEditStageID).attr('disabled', true);
                    $("#aEditStage_" + rstEditStageID).css("display", "block");
                    $("#aStageDelete_" + rstEditStageID).css("display", "block");
                    $("#aSaveEdit_" + rstEditStageID).css("display", "none");
                    $("#aResetEdit_" + rstEditStageID).css("display", "none");
                    BindStages('MethodName=GetStages');

                    $("#lblStageName_" + rstEditStageID).css("display", "block");
                    $("#stage-name_" + rstEditStageID).css("display", "none");

                    return false;
                });

                $(".aStageDelete").on("click", function() {
                    if (confirm('Are you sure?')) {

                        var splits_id = this.id.split('_');
                        var deleteStageID = splits_id[1];

                        $("#stage-name_" + deleteStageID).attr('disabled', true);
                        $("#aEditStage_" + deleteStageID).css("display", "block");
                        $("#aStageDelete_" + deleteStageID).css("display", "block");
                        $("#aSaveEdit_" + deleteStageID).css("display", "none");
                        $("#aResetEdit_" + deleteStageID).css("display", "none");

                        DeleteStage('MethodName=DeleteStageById&StageID=' + deleteStageID);
                        BindStages('MethodName=GetStages');

                        //$("#lblStageName_" + rstEditStageID).css("display", "block");
                        //$("#stage-name_" + rstEditStageID).css("display", "none");

                        return false;
                    } else {
                        return false;
                    }
                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindStages('MethodName=GetStages');
            }
        })
    }

    function AddStage(data) {
        var flag = page.query.flag;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    //alert(obj.message);
                    $("#divStage").css("display", "block");
                    $("#divAddStage").css("display", "none");
                    BindStages('MethodName=GetStages');
                } else {
                    //alert(obj.message);
                    //$('#lblAddStage').html(obj.message);
                    //$("#lblAddStage").css("display", "block");
                    $("#divAddStage").css("display", "block");
                    $("#divStage").css("display", "none");
                    return false;
                }
                if (flag == '2') {
                    $("#aLinkBackToSystemStages").css("display", "block");
                } else {
                    $("#aLinkBackToSystemStages").css("display", "none");
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    "There was an error while Add Supplier. Try again please!", alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function UpdateStage(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    $("#divStage").css("display", "block");
                    $("#divAddStage").css("display", "none");
                    BindStages('MethodName=GetStages');
                } else {
                    //alert(data.message);
                    //$('#lblStage').html(data.message);
                    //$("#lblStage").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    $("#btnSaveStage").on("click", function() {


        var retval = false;
        var ans = check_itemsvalidate('#divAddStage input');

        if (ans) {
            //SaveSupplierDetail({ MethodName: 'SaveSupplierDetail', AccountID: loginaccountid, Supplier: $("#txtSupplier").val(), Contact: $("#txtContactNo").val(), Phone: $("#txtPhone").val(), Fax: $("#txtFax").val(), Email: $("#txtEmail").val() });
            //AddStage('MethodName=SaveStage&AccountID=' + loginaccountid + '&stage=' + $("#txtStageName").val() + '&description=' + $("#txtDescription").val() + '&duration=' + $("#txtDuration").val());
            AddStage('MethodName=SaveStage&stage=' + $("#txtStageName").val() + '&description=' + $("#txtDescription").val() + '&duration=' + $("#txtDuration").val());
        } else {
            return false;
        }

    });

    $("#btnCancel").on("click", function() {
        jQuery(".formError").remove();
        $("#divStage").css("display", "block");
        $("#divAddStage").css("display", "none");
        $("#aAddStage").css("display", "block");
        $("#divStageHeaderText").html("");
        $("#divStageHeaderText").html("Stages");
        jQuery(".formError").remove();
        if (flag == '2') {
            $("#aLinkBackToSystemStages").css("display", "block");
        } else {
            $("#aLinkBackToSystemStages").css("display", "none");
        }
        $("#divchangeorder").css("display", "block");
        return false;
    });

    $("#aAddStage").on("click", function() {
        $("#divStage").css("display", "none");
        if (ao_founder == 1) {
            $("#divAddStage").css("display", "block");
            $('#txtStageName').val("");
            $('#txtDescription').val("");
            $('#txtDuration').val("");
            $("#lblStageMsgMainLbl").css("display", "none");
            $("#aLinkBackToSystemStages").css("display", "none");
        } else {
            $("#aLinkBackToSystemStages").css("display", "block");
            $("#divAddStage").css("display", "none");
            $("#lblStageMsgMainLbl").html("Insufficient Rights");
            $("#lblStageMsgMainLbl").css("display", "block");
        }
        $("#divchangeorder").css("display", "none");
        $("#aAddStage").css("display", "none");
        $("#divStageHeaderText").html("");
        $("#divStageHeaderText").html("Add Stage");
        return false;
    });

    function DeleteStage(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    //alert(data.message);
                    BindStages('MethodName=GetStages');
                    //mainView.loadPage("stages.html");
                } else {
                    //alert(data.message);
                    //$('#lblStage').html(data.message);
                    //$("#lblStage").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

});

myApp.onPageInit('documents', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var searchtype = $("#sort").val();
    var searchtext = $("#txt_Search").val();
    BindDocumentList('MethodName=GetListOfDocuments&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=0');

    if (Checkaccess('upload_document')) {
        $("#divEditGroup_AddDoc").css("display", "block");
    }
    if (!Checkaccess('view_documents')) {
        $("#sort").css("display", "none");
        $("#divDocumentList").css("display", "none");
        $("#lblMsgForDocumentListMainLabel").html("Insufficient Rights");
        $("#lblMsgForDocumentListMainLabel").css("display", "block");
    } else {
        $("#sort").css("display", "inline-block");
        $("#divDocumentList").css("display", "block");
        $("#lblMsgForDocumentListMainLabel").html("");
        $("#lblMsgForDocumentListMainLabel").css("display", "none");
    }

    $('#sort').change(function() {
        ViewSortCategory(this);
    });
    $('#txt_Search').keyup(function() {
        searchdocument();
    });
    $('#btnClearFilter').on("click", function() {
        $("#txt_Search").val('');
        $("#sort").val('');
        BindDocumentList('MethodName=GetListOfDocuments&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=0');
    });

    function searchdocument() {
        document.getElementById("hdnoffset").value = "0";
        var searchtext = $("#txt_Search").val();
        var searchtype = $("#sort").val();
        BindDocumentList('MethodName=GetListOfDocuments&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=' + document.getElementById("hdnoffset").value);
    }

    $("#aEditGroup").on("click", function() {
        mainView.loadPage("documentGroup.html");
        return false;
    });
    $("#aAddDocument").on("click", function() {
        $("#aAddDocument").attr("href", "#");
        mainView.loadPage("edit_document.html?DocumentID=0");
        return false;
    });

    function BindDocumentList(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                data = $.parseJSON(data);

                if (data.groupList.length > 0) {
                    var search_group = $("#sort").val();
                    var sort_dropdown = '<option value="">Group</option>';
                    for (var i = 0; i < data.groupList.length; i++) {
                        sort_dropdown += '<option value="' + data.groupList[i].label + '" ';
                        if (search_group == data.groupList[i].label) {
                            sort_dropdown += 'selected="selected" ';
                        }
                        sort_dropdown += '>' + data.groupList[i].label + '</option>';
                    }
                }

                var currentOffset = parseInt(data.currentofset);
                if (data.doclist.length > 0) {
                    currentOffset = parseInt(currentOffset) + parseInt(data.doclist.length);
                    $('#divDocumentList').empty();
                    $('#divDocumentList').attr("style", "display:block");
                    var items = [];
                    items.push('<table class="table"><tr><th>Title</th><th>Group</th><th>Timestamp</th><th>Added By</th></tr>');

                    for (var i = 0; i < data.doclist.length; i++) {
                        items.push('<tr><td><img alt="img" src="images/icons/' + data.doclist[i].filetype + '.png" />&nbsp;&nbsp;<a id="aDocTitle" href="edit_document.html?DocumentID=' + data.doclist[i].document_id + '">' + data.doclist[i].document + '</a></td><td>' + data.doclist[i].label + '</td><td> ' + data.doclist[i].timestamp + '</td><td>' + data.doclist[i].owner + '</td></tr>');
                    }
                    items.push('</table>');
                    document.getElementById("hdntotalrecord").value = data.totalrecord;

                    if (data.totalrecord > 10) {

                        items.push('<a id="btnprev" class="button_small"  style="visibility:hidden;cursor:pointer;"> </a>&nbsp;&nbsp;');
                        items.push('<label>Showing: ' + (parseInt(data.currentofset) + 1) + " - " + currentOffset + " of " + parseInt(data.totalrecord) + '</label>');
                        items.push('&nbsp;&nbsp;<a id="btnnext" style="cursor:pointer;" class="button_small" > </a>');
                    }
                    $('#divDocumentList').append(items.join(''));
                    $('#btnnext').on("click", function() {
                        callnext10();
                    });
                    $('#btnprev').on("click", function() {
                        callPrev10();
                    });
                    if (data.totalrecord > 10) {
                        if (parseInt(data.totalrecord) == parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'hidden';
                            document.getElementById('btnprev').style.visibility = 'visible';
                        } else if (parseInt(data.totalrecord) < parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'visible';
                            document.getElementById('btnprev').style.visibility = 'Hidden';
                        } else {
                            if (parseInt(data.currentofset) == 0) {
                                document.getElementById('btnprev').style.visibility = 'Hidden';
                            } else {
                                document.getElementById('btnnext').style.visibility = 'visible';
                                document.getElementById('btnprev').style.visibility = 'visible';
                            }
                        }
                    }
                } else {

                    $('#divDocumentList').empty();

                    var items = [];
                    items.push('<table style="width:100%;" class="table"><tr><th style="width:7%;" class="gridHeader">Title</th><th style="width:12%;" class="gridHeader">Group</th><th style="width:12%;" class="gridHeader">Timestamp</th><th style="width:12%;" class="gridHeader">Added By</th></tr>');
                    items.push('<tr><td colspan="4" class="acenter"><label>No Documents Found</label></td></tr>');
                    items.push('</table>');
                    $('#divDocumentList').append(items.join(''));
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindDocumentList(data);
            }
        })
    }

    function ViewSortCategory(obj) {
        var searchtype = $("#sort").val();
        var searchtext = $("#txt_Search").val();

        BindDocumentList('MethodName=GetListOfDocuments&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=0');
    }

    function callnext10() {
        var searchtype = $("#sort").val();
        var searchtext = $("#txt_Search").val();
        var offset = 0;
        var x = document.getElementById("hdnoffset").value;
        offset = parseInt(x) + 10;
        document.getElementById("hdnoffset").value = offset;
        //BindDocumentList('MethodName=GetListOfDocuments&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=' + document.getElementById("hdnoffset").value);
        BindDocumentList('MethodName=GetListOfDocuments&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=' + offset + '');
    }

    function callPrev10() {
        var searchtype = $("#sort").val();
        var searchtext = $("#txt_Search").val();
        var offset = 0;
        var x = document.getElementById("hdnoffset").value;
        offset = parseInt(x) - 10;
        document.getElementById("hdnoffset").value = offset;
        //BindDocumentList('MethodName=GetListOfDocuments&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=' + document.getElementById("hdnoffset").value);
        BindDocumentList('MethodName=GetListOfDocuments&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=' + offset + '');
    }

});

myApp.onPageInit('edit-document', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var DocumentID = page.query.DocumentID;

    if (DocumentID != null && DocumentID != "" && DocumentID != "undefined") {
        if (DocumentID == '0') {

            $('#divDocument').css("display", "none");
            $('#divEditDocument').css("display", "block");
            $('#divuploadfile').css("display", "block");
            $("#divEditDocumentHeader").html("Add Document");
            $("#divEditDoc").css("display", "block");
            BindDocumentDetailsForEdit('MethodName=GetDetailsForDocuments&DocumentID=0');
        } else {
            if (Checkaccess('view_documents')) {
                BindDocumentDetails('MethodName=GetDetailsForDocuments&DocumentID=' + DocumentID);
            }

            if (CheckmoduleOwnership('view_documents')) {
                $('#divEditDocument').css("display", "block");
                $('#lblMainLabelForEditDocument').css("");
                $('#lblMainLabelForEditDocument').css("display", "none");
            } else {
                $('#divEditDocument').css("display", "none");
                $('#lblMainLabelForEditDocument').css("Insufficient Rights");
                $('#lblMainLabelForEditDocument').css("display", "block");
            }
            if (Checkaccess('modify_documents') && (!CheckmoduleOwnership('modify_documents') || CheckmoduleOwnership('modify_documents'))) {
                $('#aEditDocument').css("display", "block");
            } else {
                $('#aEditDocument').css("display", "none");
            }
            if (Checkaccess('delete_documents') && (!CheckmoduleOwnership('delete_documents') || CheckmoduleOwnership('delete_documents'))) {
                $('#aDeleteDocument').css("display", "block");
            } else {
                $('#aDeleteDocument').css("display", "none");
            }

        }
    } else {
        mainView.loadPage("documents.html");
    }

    function BindDocumentDetails(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var DocumentID = page.query.DocumentID;
                $("#divDocument").css("display", "block");
                $("#divEditDocument").css("display", "none");
                $("#divEditDocumentHeader").html("");
                $("#divEditDocumentHeader").html("Documents");
                $('#divDocDetail').empty();
                var items = [];

                if (data.docDetailArray.length > 0) {

                    items.push('<div class="table-responsive"><table class="table"><tr><th>Title</th><th>Group</th><th>Timestamp</th><th>Added By</th></tr>');
                    for (var i = 0; i < data.docDetailArray.length; i++) {
                        items.push('<tr><td>' + data.docDetailArray[i].document + '</td><td>' + data.docDetailArray[i].label + '</td><td> ' + data.docDetailArray[i].timestamp + '</td><td>' + data.docDetailArray[i].owner + '</td></tr>');
                    }
                    items.push('<tr><td colspan="4" style="text-align: right;"><a id="aEditDocument" href="javascript:;" style="text-align: right; font-weight: bold;"><i class="icon-pencil"></i></a>&nbsp;&nbsp;<a id="aDeleteDocument" href="javascript:;" style="text-align: right; font-weight: bold;"><i class="icon-remove"></i></a></td></tr></table></div>');

                    items.push('<div style="padding-top:20px;"><table class="table"><tr><th colspan="2">Document Info</th></tr>');
                    for (var i = 0; i < data.docDetailArray.length; i++) {

                        items.push('<tr><td class="left_listit">Description:</td><td>' + data.docDetailArray[i].description + '</td></tr>');
                        items.push('<tr><td class="left_listit">File Type:</td><td>' + data.docDetailArray[i].filetype + '</td></tr>');
                        items.push('<tr><td class="left_listit">Stage:</td><td>' + data.docDetailArray[i].stage + '</td></tr>');
                        items.push('<tr"><td class="left_listit">File Size:</td><td>' + data.file_size + '</td></tr>');
                        items.push('<tr><td class="left_listit">View/Download:</td><td><a id="aFileView" title="' + data.docDetailArray[i].filename + '/' + data.docDetailArray[i].filetype + '">' + data.docDetailArray[i].filename + '</a></td></tr>');
                    }
                    items.push('</table></div>');
                } else {
                    navigator.notification.alert(
                        "Document details not found!", alertDismissed, "Unsuccessful", "Done"
                    );
                }
                $('#divDocDetail').append(items.join(''));

                $("#aEditDocument").on("click", function() {
                    var aDocumentID = page.query.DocumentID;
                    $("#divDocument").css("display", "none");
                    $("#divEditDocument").css("display", "block");
                    $("#divEditDoc").css("display", "block");
                    $("#divuploadfile").css("display", "none");
                    $("#divEditDocumentHeader").html("");
                    $("#divEditDocumentHeader").html("Edit Document");
                    BindDocumentDetailsForEdit('MethodName=GetDetailsForDocuments&DocumentID=' + aDocumentID);
                    return false;
                });

                $("#aDeleteDocument").on("click", function() {
                    if (confirm('Are you sure?')) {
                        var aDocumentID = page.query.DocumentID;
                        $("#divDocument").css("display", "block");
                        $("#divEditDocument").css("display", "none");
                        $("#divEditDocumentHeader").html("");
                        $("#divEditDocumentHeader").html("Documents");
                        DeleteDocument('MethodName=DeleteDocumentDetails&DocumentID=' + aDocumentID);
                        mainView.loadPage("documents.html");
                        //BindDocumentDetails('MethodName=GetDetailsForDocuments&DocumentID=' + aDocumentID);
                        return false;
                    } else { return false; }
                });

                $("#aFileView").on("click", function() {
                    var file_id = aFileView.title.split('/');
                    var FileName = file_id[0];
                    var FileType = file_id[1];
                    $("#aFileView").attr("href", "#");
                    //if (FileType != 'image' && FileType != 'pdf') {
                    if (FileType != 'image') {
                        if (data.DOCUMENTS_PATH != "")
                            var FullPath = data.DOCUMENTS_PATH + '/' + FileName;
                        //window.open('http://docs.google.com/viewer?url=' + data.DOCUMENTS_PATH + '/' + FileName, '_blank');
                        window.open(FullPath, '_system');
                    } else {
                        if (data.DOCUMENTS_PATH != "")
                            window.open(data.DOCUMENTS_PATH + '/' + FileName, '_blank');
                    }
                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindDocumentDetails(data);
            }
        })
    }

    $('#aBackDocument').on("click", function() {
        mainView.loadPage("documents.html");
        return false;
    });

    $('#btnCancel').on("click", function() {
        jQuery(".formError").remove();
        var DocumentID = page.query.DocumentID;
        if (DocumentID != '0') {
            $("#divEditDocument").css("display", "none");
            $("#divDocument").css("display", "block");
            $("#divEditDocumentHeader").html("");
            $("#divEditDocumentHeader").html("Documents");
        } else {
            mainView.loadPage("documents.html");
        }
        return false;
    });

    function BindDocumentDetailsForEdit(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var DocumentID = page.query.DocumentID;
                for (var i = 0; i < data.documentGroups.length; i++) {
                    $("#ddlGroup").append($("<option value='" + data.documentGroups[i].document_group_id + "'>" + data.documentGroups[i].label + "</option>"));
                }

                var obj = JSON.stringify(data.stages);
                $.each(data.stages, function(key, value) {
                    $("#ddlStage").append($("<option value='" + value.stage_num + "'>" + value.stage + "</option>"));
                });
                if (DocumentID != '0') {
                    var items = [];
                    if (data.docDetailArray.length > 0) {
                        for (var i = 0; i < data.docDetailArray.length; i++) {
                            items.push($("#txtTitle").val(data.docDetailArray[i].document) + $('#ddlGroup').val(data.docDetailArray[i].document_group_id).attr("selected", "selected") + $('#ddlStage').val(data.docDetailArray[i].stage_num).attr("selected", "selected") + $("#txtDescription").val(data.docDetailArray[i].description));
                        }
                        return false;
                    } else {
                        navigator.notification.alert(
                            "There are some error, please try again later.", alertDismissed, "An error occured", "Done"
                        );
                        return false;
                    }
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                navigator.notification.alert(
                    "Error", alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        });
    }

    $("#btnUpdateDocument").on("click", function() {

        var retval = false;
        var ans = check_itemsvalidate('#divEditDoc input');


        if (ans) {
            var DocumentID = page.query.DocumentID;

            if (DocumentID != null && DocumentID != "" && DocumentID != "undefined") {
                if (DocumentID != '0') {

                    UpdateDocumentDetail('MethodName=UpdateDocumentDetail&DocumentID=' + DocumentID + '&title=' + $("#txtTitle").val() + '&group=' + $("#ddlGroup").val() + '&stage=' + $("#ddlStage").val() + '&description=' + $("#txtDescription").val());
                    BindDocumentDetails('MethodName=GetDetailsForDocuments&DocumentID=' + DocumentID);
                    return false;
                } else {

                    if ($('#fludoc').val() != "") {
                        // alert($('#fludoc').prop('files').length);
                        var file_data = $('#fludoc').prop('files')[0];
                        var form_data = new FormData();

                        form_data.append('flag', '1');
                        form_data.append('title', $('#txtTitle').val().trim());
                        form_data.append('description', $('#txtDescription').val().trim());
                        form_data.append('stage', $('#ddlStage').val());
                        form_data.append('document_group', $('#ddlGroup').val().trim());
                        form_data.append('file', file_data);
                        $.ajax({
                            url: 'https://xactbid.pocketofficepro.com/fileuploader.php', // point to server-side PHP script
                            dataType: 'text', // what to expect back from the PHP script, if anything
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function(php_script_response) {
                                mainView.loadPage("documents.html");
                                // display response from the PHP script, if any
                            },
                            error: function(data) {
                                // alert('err' + data);
                                var myerr = 'err' + data;
                                navigator.notification.alert(
                                    myerr, alertDismissed, "An error occured", "Done"
                                );
                            },
                        });

                    } else {
                        navigator.notification.alert(
                            "No files selected", alertDismissed, "Unsuccessful", "Done"
                        );
                    }
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }

    });

    function UpdateDocumentDetail(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    //alert(data.message);
                    $("#divEditDocument").css("display", "none");
                    $("#divDocument").css("display", "block");
                } else {
                    //alert(data.message);
                    //$('#lblEditDocument').html(data.message);
                    //$("#lblEditDocument").css("display", "block");

                }
                return false;

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
                return false;
            },
        });
        return false;
    }

    function DeleteDocument(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    $("#divEditDocument").css("display", "none");
                    $("#divDocument").css("display", "block");
                } else {
                    //alert(data.message);
                    $('#lblEditDocument').html(data.message);
                    $("#lblEditDocument").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

});

myApp.onPageInit('edit-customer', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var CustomerID = page.query.CustomerId;

    if (CustomerID != null && CustomerID != "" && CustomerID != "undefined") {
        BindCustomerDetails('MethodName=GetCustomerDetails&CustomerID=' + CustomerID);
    } else {
        mainView.loadPage("customers.html");
    }

    function BindCustomerDetails(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $("#divCustomer").css("display", "block");
                $("#divEditCustomer").css("display", "none");

                $('#divCustDetail').empty();
                var items = [];

                if (data.customerArray.length > 0) {

                    items.push('<div class="table-responsive"><table class="table"><tr><th>Name</th><th>Nick Name</th><th>Timestamp</th><th>Added By</th></tr>');
                    for (var i = 0; i < data.customerArray.length; i++) {
                        items.push('<tr><td>' + data.customerArray[i].fname + ' ' + data.customerArray[i].lname + '</td><td>' + data.customerArray[i].nickname + '</td><td> ' + data.customerArray[i].ForamtDate + '</td><td>' + data.customerArray[i].uFname + ' ' + data.customerArray[i].uLname + '</td></tr>');
                    }

                    if (Checkaccess('edit_customer'))
                        items.push('<tr><td colspan="4" style="text-align: right;"><a id="aEditCustomer" class="aEditCompanyProfile" href="javascript:;"><i class="icon-pencil"></i></a></td></tr></table></div>');

                    items.push('<div style="padding-top:20px;"><table class="table"><tr><th colspan="2">Insured Profile:</th></tr>');
                    for (var i = 0; i < data.customerArray.length; i++) {
                        items.push('<tr><td class="left_listit">Address:</td><td><a class="aLinkToCustMap" data-lat=' + data.customerArray[i].lat + ' data-long=' + data.customerArray[i].long + '>' + data.customerArray[i].address + '<br>' + data.customerArray[i].city + ', ' + data.customerArray[i].state + ' ' + data.customerArray[i].zip + '</a></td></tr>');
                        items.push('<tr><td class="left_listit">Cross Street:</td><td>' + data.customerArray[i].cross_street + '</td></tr>');
                        items.push('<tr><td class="left_listit">Latitude:</td><td>' + data.customerArray[i].lat + '</td></tr>');
                        items.push('<tr><td class="left_listit">Longitude:</td><td>' + data.customerArray[i].long + '</td></tr>');
                        var phno = data.customerArray[i].formatPhn;
                        if (data.customerArray[i].formatPhn2 != null && data.customerArray[i].formatPhn2 != '') {
                            phno += ',  ' + data.customerArray[i].formatPhn2;
                        }
                        items.push('<tr><td class="left_listit">Phone:</td><td>' + phno + '</td></tr>');
                        items.push('<tr><td class="left_listit">Email:</td><td>' + data.customerArray[i].email + '</td></tr>');
                        //items.push('<tr><td class="left_listit">Jobs:</td><td><a class="aLinkToCustJob" data-id=' + data.jobArray[i].job_id + '>' + data.jobArray[i].job_number + '</a></td></tr>');
                        items.push('<tr><td class="left_listit">Jobs:</td><td><table><tr><td style="border: none;">');
                        if (data.jobArray.length > 0) {
                            for (var i = 0; i < data.jobArray.length; i++) {
                                //items.push('<tr><td class="left_listit">Jobs:</td><td><a class="aLinkToCustJob" data-id=' + data.jobArray[i].job_id + '>' + data.jobArray[i].job_number + '</a></td></tr>');
                                items.push('<a class="aLinkToCustJob" data-id=' + data.jobArray[i].job_id + '>' + data.jobArray[i].job_number + '</a><br />');
                            }
                        }
                        items.push('</td></tr></table></td></tr>');
                    }
                    items.push('</table></div>');
                } else {
                    navigator.notification.alert(
                        "Customer details not found!", alertDismissed, "Unsuccessful", "Done"
                    );
                }
                $('#divCustDetail').append(items.join(''));

                $("#aEditCustomer").on("click", function() {
                    var aCustomerId = page.query.CustomerId;
                    $("#divCustomer").css("display", "none");
                    $("#divEditCustomer").css("display", "block");
                    BindCustomerDetailsForEdit('MethodName=GetCustomerDetails&CustomerID=' + aCustomerId);
                    return false;
                });
                $(".aLinkToCustJob").on("click", function() {
                    var jObId = $(this).attr('data-id');
                    mainView.loadPage("jobtabs.html?JId=" + jObId);
                    return false;
                });
                $(".aLinkToCustMap").on("click", function() {
                    var lat = $(this).attr('data-lat');
                    var long = $(this).attr('data-long');
                    var location = lat + " " + long;
                    mainView.loadPage("maps.html?location=" + location);
                    return false;
                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindCustomerDetails(data);
            }
        })
    }

    $('#aBackCustomer').on("click", function() {
        mainView.loadPage("customers.html");
    });

    $('#btnCancel').on("click", function() {
        jQuery(".formError").remove();
        $("#divEditCustomer").css("display", "none");
        $("#divCustomer").css("display", "block");
    });

    function BindCustomerDetailsForEdit(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                var items = [];
                if (data.customerArray.length > 0) {

                    var obj = JSON.stringify(data.stateArray);
                    $.each(data.stateArray, function(key, value) {
                        $("#ddlState").append($("<option value='" + key + "'>" + key + "</option>"));
                    });

                    for (var i = 0; i < data.customerArray.length; i++) {
                        items.push($("#txtFName").val(data.customerArray[i].fname) + $("#txtLName").val(data.customerArray[i].lname) + $("#txtNickName").val(data.customerArray[i].nickname) + $("#txtAddress").val(data.customerArray[i].address) + $("#txtCity").val(data.customerArray[i].city) + $('#ddlState').val(data.customerArray[i].state).attr("selected", "selected") + $("#txtZip").val(data.customerArray[i].zip) + $("#txtCrossStreet").val(data.customerArray[i].cross_street) + $("#txtPhone").val(data.customerArray[i].phone) + $("#txtPhone2").val(data.customerArray[i].phone2) + $("#txtEmail").val(data.customerArray[i].email));
                    }
                    return false;
                } else {
                    navigator.notification.alert(
                        "There are some error in fetching Insured detail, please try again later.", alertDismissed, "An error occured", "Done"
                    );
                    return false;
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                navigator.notification.alert(
                    "Error", alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        });
    }

    $("#btnUpdateCustomer").on("click", function() {

        var retval = false;
        var ans = check_itemsvalidate('#divEditCust input');


        if (ans) {
            var CustomerID = page.query.CustomerId;
            if (CustomerID != null && CustomerID != "" && CustomerID != "undefined") {
                UpdateCustomerDetail('MethodName=UpdateCustomerDetail&CustomerID=' + CustomerID + '&fname=' + $("#txtFName").val() + '&lname=' + $("#txtLName").val() + '&nickname=' + $("#txtNickName").val() + '&address=' + $("#txtAddress").val() + '&city=' + $("#txtCity").val() + '&state=' + $("#ddlState").val() + '&zip=' + $("#txtZip").val() + '&cross_street=' + $("#txtCrossStreet").val() + '&phone=' + $("#txtPhone").val() + '&phone2=' + $("#txtPhone2").val() + '&email=' + $("#txtEmail").val());
                BindCustomerDetails('MethodName=GetCustomerDetails&CustomerID=' + CustomerID);
            } else {
                return false;
            }
        }

    });

    function UpdateCustomerDetail(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    $("#divEditCustomer").css("display", "none");
                    $("#divCustomer").css("display", "block");
                } else {
                    //alert(data.message);
                    $('#lblEditCustomer').html(data.message);
                    $("#lblEditCustomer").css("display", "block");
                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

});

function ChangeJobPaidstatus(jobid, str) {
    var $ = jQuery.noConflict();
    var data = 'MethodName=updatejobpaymentstatus&jobid=' + jobid + '&action=' + str;
    $.ajax({
        url: "https://xactbid.pocketofficepro.com/workflowservice.php",
        type: "POST",
        data: data,
        cache: false,
        success: function(data, textStatus, jqxhr) {
            BindJobPrgressbar(jobid);
        },
        error: function(jqxhr, textStatus, errorMessage) {
            navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
        }
    });
    return false;
}

function updateBookmark(jobid) {
    var $ = jQuery.noConflict();
    var data = 'MethodName=updatebookmark&jobid=' + jobid;
    $.ajax({
        url: "https://xactbid.pocketofficepro.com/workflowservice.php",
        type: "POST",
        data: data,
        cache: false,
        success: function(data, textStatus, jqxhr) {
            BindJobPrgressbar(jobid);
        },
        error: function(jqxhr, textStatus, errorMessage) {
            navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
        }
    });
    return false;
}

function BindJobPrgressbar(JobId) {

    var $ = jQuery.noConflict();
    $body = $("body");
    $body.addClass("loading");
    var data = 'MethodName=Bindtabpagedetials&JobId=' + JobId;
    $.ajax({
        url: "https://xactbid.pocketofficepro.com/workflowservice.php",
        type: "POST",
        data: data,
        cache: false,
        success: function(data, textStatus, jqxhr) {
            if (data && data !== 'Job not found') {
                Result = $.parseJSON(data);
                job = Result.myJob[0];
                $("#lstsubmenu").empty();
                $("#lstsubmenu").append(Result.jobmenus);
                $(".lnkjobsubmenu").on("click", function() {
                    mainView.loadPage($(this).attr('data-pageurl'));

                });
                $('.headjobno').html(Result.customername);
                $('#divjobheadprogess').empty();
                if (Result.stagedrd != '') {
                    $('#divjobheadprogess').append(Result.stagedrd);
                    $('#lblstgedrd').css("display", "block");
                    $('#btnJumoToStage').css("display", "inline-block");
                }

                $('.headjobstatus').html(Result.iconStr);
                $('.percentage').html(Result.percentage + '%');
                $('.percentage').animate({ width: Result.percentage + '%' }, (50 * Result.percentage), 'easeInOutQuart');
                var bookmatrktext = "Bookmark";
                if (Result.bookmarkLinkText != "") {
                    bookmatrktext = Result.bookmarkLinkText;
                }
                $('#sbookmarkLinkText').text(Result.bookmarkLinkText);
                $("#aUpdatebookmark").on("click", function() {

                    var Jobid = JobId;
                    updateBookmark(Jobid);
                });

                $("#lnkpaidjob").on("click", function() {
                    var result = confirm("Are you sure you want to mark paid?");

                    if (result == true) {
                        var Jobid = JobId;
                        ChangeJobPaidstatus(Jobid, "paid");
                        return true;
                    } else {
                        return false;
                    }
                });
                $("#lnkunpaidjob").on("click", function() {
                    var result = confirm("Are you sure you want to mark unpaid?");

                    if (result == true) {
                        var Jobid = JobId;
                        ChangeJobPaidstatus(Jobid, "unpaid");
                        return true;
                    } else {
                        return false;
                    }
                });

                setTimeout(function() { $body.removeClass("loading"); }, 500)
            }else{
                BindJobPrgressbar(JobId);
            }
        },
        error: function(jqxhr, textStatus, errorMessage) {
            BindJobPrgressbar(JobId);
        }

    });

}


myApp.onPageInit('job-tabs', function(page) {
    var $ = jQuery.noConflict();

    jQuery(".formError").remove();
    CheckUserLogin();
    var JobId = page.query.JId;
    $("#hdnJobid").val(JobId);
    BindJobPrgressbar(JobId);
    $('#lnkjobtabback').on("click", function() {
        mainView.loadPage("jobs.html");
    });
    $('#alinkAddAppt').on("click", function() {
        var JobId = page.query.JId;
        mainView.loadPage("get_appointment.html?JId=" + JobId);
        return false;
    });
    $('#alinkAddMatSheet').on("click", function() {
        var JobId = page.query.JId;
        mainView.loadPage("job_materials.html?JId=" + JobId);
        return false;
    });
    $('#alinkAddRepair').on("click", function() {
        var JobId = page.query.JId;
        mainView.loadPage("get_repair.html?JId=" + JobId);
        return false;
    });

    $("#btnJumoToStage").on("click", function() {
        $stage_num = $('#myjobstages').val();
        JumpToStageUpdateStageIdByJob('MethodName=UpdateJobStageID&JobId=' + JobId + '&StageNum=' + $stage_num);
        return false;
    });


    function JumpToStageUpdateStageIdByJob(data) {
        // $body = $("body");
        // $body.addClass("loading");
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == 1) {
                    $body.removeClass("loading");
                    // mainView.loadPage("jobtabs.html?JId=" + JobId);
                    BindJobPrgressbar(JobId);
                } else {
                    $body.removeClass("loading");
                    alert(data.message);
                    return false;
                }


            },
            error: function(jqxhr, textStatus, errorMessage) {
                JumpToStageUpdateStageIdByJob(data);
            }
        })
    }

});

myApp.onPageInit('job-details', function(page) {
    var $ = jQuery.noConflict();

    jQuery(".formError").remove();
    CheckUserLogin();

    var JobId = page.query.JId;
    $("#hdnJobid").val(JobId);
    BindJobPrgressbar(JobId);

    BindJobDetailSection(JobId);

    function DateCallJS() {
        var $ = jQuery.noConflict();
        $('.datestamp').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: "1925:2999",
            onClose: function() {
                if (this.value != '') {
                    $.validationEngine.loadValidation('.datestamp');
                }
            }
        });
    }

    function BindJobDetailSection(JobId) {
        var $ = jQuery.noConflict();
        $body = $("body");
        $body.addClass("loading");
        var data = 'MethodName=Bindjobdetailssection&JobId=' + JobId;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);
                job = Result.myJob[0];

                var stage_name = '';
                if (job.stage_num != '0' && job.stages != null)
                //stage_name = job.stage_num;
                    stage_name = job.stages[0][0];

                $("#tbljobstagedetails").empty();
                $("#tbljobstagedetails").append('<tr><td class="left_listit">Current Stage:</td><td>' + stage_name + '</td></tr>');
                $("#tbljobstagedetails").append('<tr><td class="left_listit">Days @ Stage:</td><td>' + Result.stage_age + '</td></tr>');
                var duration = '';

                if (job.duration != '' && job.duration != null) {
                    duration = job.duration == "9999" ? "No Limit" : job.duration;
                }



                $("#tbljobstagedetails").append('<tr><td class="left_listit">Suggested Duration:</td><td>' + duration + '</td></tr>');
                $("#tbljobstagedetails").append('<tr><td class="left_listit">Next Stage:</td><td>' + Result.nextstage + '</td></tr>');
                if (Result.nextstageReq != undefined)
                    $("#tbljobstagedetails").append(Result.nextstageReq);

                $('.btn-next-stage').on("click", function() {
                    var jobId = $(this).attr("data-job-id");
                    if (confirm('Are you sure you want to move to next stage?')) {
                        UpdateJobToNextStage('MethodName=UpdateJobToNextStage&JobId=' + JobId);
                    } else { return false; }
                });

                $('.clsjobstagedoc').on("click", function() {
                    window.open(this);
                });
                $("#tbljobcustomerinfo").empty();

                //Result.map_url = Result.map_url.replace(new RegExp(' ', 'g'), '&nbsp;');
                $("#hdnmapurl").val(Result.map_url);

                $("#tbljobcustomerinfo").append('<tr><td class="listitemnoborder" style="text-align:center;" width=50><a href="javascript:;"   id="lnkmapcustomer" ><img src="images/icons/map_32.png" border=0></a></td><td><a id="lnkcustomer" href="edit_customer.html?CustomerId=' + job.customer_id + '" class="boldlink" tooltip>' + Result.customername + '</a>' + Result.caneditcustomer + '<br/>' + Result.custaddress + '</td></tr>');
                $("#lnkeditjobcust").on("click", function myfunction() {
                    $("#lnkcustomer").css("display", "none");
                    $("#drdjobcustomers").css("display", "block");
                    $("#lnkeditjobcust").css("display", "none");
                });
                $("#drdjobcustomers").change(function myfunction() {
                    UpdateInlineJob('1');

                });
                //   console.log(hasOwnProperty(job.meta_data.insurance_policy));

                if (Result.custphoneno != undefined && Result.custphoneno != '' && Result.custphoneno != null) {
                    $("#tbljobcustomerinfo").append('<tr><td  class="listitemnoborder left_listit" style="text-align:center;" width=50>Phone:</td><td>' + Result.custphoneno + '</td></tr>');
                    $('.lnkdialphone').on("click", function() {
                        var phone = $(this).attr('data-phone');
                        window.open("tel:'" + phone + "'", '_system')
                    });
                } else
                    $("#tbljobcustomerinfo").append('<tr><td  class="listitemnoborder left_listit" style="text-align:center;" width=50>Phone:</td><td >&nbsp;</td></tr>');
                //if (Result.custphoneno2 != undefined && Result.custphoneno2 != '' && Result.custphoneno2 != null) {
                //    $("#tbljobcustomerinfo").append('<tr><td  class="listitemnoborder left_listit" style="text-align:center;" width=50>Phone:</td><td ><a href="tel:' + Result.phonestring2 + '">' + Result.custphoneno2 + '</a></td></tr>');
                //}

                if (Result.custemail != null && Result.custemail != '') {
                    $("#tbljobcustomerinfo").append('<tr><td  class="left_listit">Email:</b></td><td class="listrownoborder"><a href="javascript:;" id=lnksendemail data-email="' + Result.custemail + '" >' + Result.custemail + '</a></td></tr>');
                    $('#lnksendemail').on("click", function() {
                        var email = $(this).attr('data-email');
                        window.open("mailto:" + email, '_system');
                    });
                } else
                    $("#tbljobcustomerinfo").append('<tr><td  class="listitemnoborder left_listit" style="text-align:center;" width=50>Email:</td><td></td></tr>');

                $("#lnkmapcustomer").on("click", function() {

                    window.open($("#hdnmapurl").val(), '_system');
                    return false;
                });

                $("#tbljobdetailssection").empty();

                $("#tbljobdetailssection").append('<tr><td  class="left_listit">Id Number:</td><td><span id=lbljobno>' + job.job_number + '</span>' + Result.lnkeditjobno + '</td></tr>');

                $("#lnkeditjobno").on("click", function myfunction() {
                    $("#lbljobno").css("display", "none");
                    $("#txtjobno").css("display", "block");
                    $("#lnkeditjobno").css("display", "none");
                });
                $("#txtjobno").focusout(function myfunction() {
                    var ans = check_itemsvalidate('#txtjobno');
                    if (ans) {
                        UpdateInlineJob('2');
                    } else {
                        $("#txtjobno").val(job.job_number);
                        return false;
                    }

                });

                $("#tbljobdetailssection").append('<tr><td  class="left_listit">Creator:</td><td><a href="get_user.html?UserId=' + job.user_id + '">' + job.user_fname + ' ' + job.user_lname + '</a></td></tr>');

                origin = '';
                if (job.origin != '' && job.origin != null)
                    origin = job.origin;
                $("#tbljobdetailssection").append('<tr><td  class="left_listit">Origin:</td><td><span id=lbljoborigin>' + origin + '</span>' + Result.lnkeditjoborigin + '</td></tr>');

                $("#lnkeditjoborigin").on("click", function myfunction() {
                    $("#lbljoborigin").css("display", "none");
                    $("#drdjoborigin").css("display", "block");
                    $("#lnkeditjoborigin").css("display", "none");
                });
                $("#drdjoborigin").change(function myfunction() {
                    UpdateInlineJob('3');

                });

                if (job.referral_id != null) {
                    $("#tbljobdetailssection").append('<tr><td  class="left_listit">Referral:</td><td><a href="get_user.html?UserId=' + job.referral_id + '">' + job.referral_fname + ' ' + job.referral_lname + '</a>' + Result.lnkeditjobreferal + '</td></tr>');

                    $('#lnkeditjobreferal').on("click", function() {
                        mainView.loadPage("job_operation.html?JId=" + JobId + '&flag=3');
                    });
                }
                if (job.canvasser_id != null) {
                    Result.canvasername = Result.canvasername.replace('/users.php?id', 'get_user.html?UserID');

                    $("#tbljobdetailssection").append('<tr><td  class="left_listit">Canvasser:</td><td><span id="lblcanvasername">' + Result.canvasername + '</span>' + Result.lnkeditjobcanvaser + '</td></tr>');
                }
                $("#lnkeditjobcanvaser").on("click", function myfunction() {
                    $("#lblcanvasername").css("display", "none");
                    $("#drdjobcanvaser").css("display", "block");
                    $("#lnkeditjobcanvaser").css("display", "none");
                });
                $("#drdjobcanvaser").change(function myfunction() {
                    UpdateInlineJob('4');
                });

                if (job.salesman_id != null) {
                    $("#tbljobdetailssection").append('<tr><td  class="left_listit">Customer:</td><td ><a href="get_user.html?UserID=' + job.salesman_id + '">' + job.salesman_fname + ' ' + job.salesman_lname + "</a> " + Result.lnkeditjobsalesman + '</td></tr>');
                }

                $("#tbljobdetailssection").append('<tr><td  class="left_listit">Created:</td><td><span id="lblcreateddate">' + job.dob + '</span>' + Result.lnkeditjobdate + '</td></tr>');
                DateCallJS();
                $("#lnkeditjobdate").on("click", function myfunction() {
                    $("#lblcreateddate").css("display", "none");
                    $("#txtjobcreatedate").css("display", "block");
                    $("#lnkeditjobdate").css("display", "none");
                });
                $('#lnkeditjobsalesman').on("click", function() {
                    mainView.loadPage("assign_jobsalesman.html?JId=" + JobId);
                });
                $("#txtjobcreatedate").change(function myfunction() {
                    var ans = check_itemsvalidate('#txtjobcreatedate');
                    if (ans) {
                        UpdateInlineJob('5');
                    } else {
                        return false;
                    }

                });


                $("#tbljobdetailssection").append('<tr><td  class="left_listit">Age:</td><td >' + Result.age_days + '</td></tr>');

                if (job.jurisdiction != null) {
                    $("#tbljobdetailssection").append('<tr><td  class="left_listit">Jurisdiction:</td><td><span id="lbljobjuridiction">' + job.jurisdiction + '</span>' + Result.lnkeditjobjuridiction + '</td></tr>');

                    $("#lnkeditjobjuridiction").on("click", function myfunction() {
                        $("#lbljobjuridiction").css("display", "none");
                        $("#drdjobjuridiction").css("display", "block");
                        $("#lnkeditjobjuridiction").css("display", "none");
                    });

                    $("#drdjobjuridiction").change(function myfunction() {
                        UpdateInlineJob('7');

                    });
                }

                if (job.permit != null) {

                    $("#tbljobdetailssection").append('<tr><td  class="left_listit">Permit #:</td><td ><span id="lbljobpermit">' + job.permit + '</span>' + Result.lnkeditjobpermit + '</td></tr>');

                    $("#lnkeditjobpermit").on("click", function myfunction() {
                        if (job.jurisdiction != null && job.jurisdiction != '0') {
                            $("#lbljobpermit").css("display", "none");
                            $("#txtjobpermit").css("display", "block");
                            $("#lnkeditjobpermit").css("display", "none");
                        } else {
                            navigator.notification.alert(
                                "Please assign a jurisdiction first", alertDismissed, "Unsuccessful", "Done"
                            );
                        }
                    });
                    $("#txtjobpermit").change(function myfunction() {
                        var ans = check_itemsvalidate('#txtjobpermit');
                        if (ans) {
                            UpdateInlineJob('8');
                        } else {
                            return false;
                        }

                    });
                    $("#tbljobdetailssection").append('<tr><td  class="left_listit">Expires:</td><td >' + job.permit_expire + '</td></tr>');

                    if (job.midroofLadder != null || job.midroof != null) {
                        midroofStr = job.midroof;
                        midroofStr += job.midroofLadder != '' ? midroofStr != '' ? ' - ' + job.midroofLadder + " story min." : '' : '';
                        $("#tbljobdetailssection").append('<tr><td  class="left_listit">Midroof:</td><td >' + midroofStr + '</td></tr>');
                    }
                }
                var jobtype = '';
                if (job.job_type != null && job.job_type != '')
                    jobtype = job.job_type;
                $("#tbljobdetailssection").append('<tr><td  class="left_listit">Job Type:</td><td>' + jobtype + Result.lnkeditjobtype + '</td></tr>');
                $('#lnkeditjobtype').on("click", function() {
                    mainView.loadPage("job_operation.html?JId=" + JobId + '&flag=5');
                });

                if (job.job_type_note != null && job.job_type_note != '') {
                    $("#tbljobdetailssection").append('<tr><td class="left_listit">Job Type Note:</td><td>' + job.job_type_note + '</td></tr>');
                }
                if (job.insurance != null && job.insurance != '') {
                    $("#tbljobdetailssection").append('<tr><td class="left_listit">Provider:</td><td >' + job.insurance + Result.lnkeditjobinsurance + '</td></tr>');
                }

                if (job.meta_data.insurance_policy != null && job.meta_data.insurance_policy != undefined && job.meta_data.insurance_policy.meta_value != '') {
                    $("#tbljobdetailssection").append('<tr><td class="left_listit">Policy:</td><td >' + job.meta_data.insurance_policy.meta_value + Result.lnkeditjobinsurance + '</td></tr>');
                }

                if (job.claim != null && job.claim != '') {
                    $("#tbljobdetailssection").append('<tr><td class="left_listit">Claim:</td><td >' + job.claim + Result.lnkeditjobinsurance + '</td></tr>');
                }

                $('.lnkeditjobinsurance').on("click", function() {
                    mainView.loadPage("job_operation.html?JId=" + JobId + '&flag=1');
                });
                $("#lstjoblistitems").empty();
                if (Result.materialSheets != undefined && Result.materialSheets != null) {

                    for (var i = 0; i < Result.materialSheets.length; i++) {
                        var material = Result.materialSheets[i];

                        var iconClass = material.confirmed != null ? ' green' : ' light-gray';
                        var dt = '';
                        if (material.delivery_date != null)
                            dt = $.datepicker.formatDate('M dd, yy', new Date(material.delivery_date));

                        var strMatDelivery = material.delivery_date != null ? '<span class="smallnote"> - Delivery Date ' + dt + '</span>' : '';

                        $("#lstjoblistitems").append('<li><i  class="icon-paper-clip" ></i>&nbsp;<i data-sheetid="' + material.sheet_id + '" class="lnksetmaterialconfirm icon-ok action' + iconClass + '"></i>&nbsp;<a href=job_materials.html?sheet_id=' + material.sheet_id + '&job_id=' + material.job_id + ' title="View material sheet" style="text-decoration:underline;">' + material.label + '</a>' + strMatDelivery + '</li>');
                    }
                }
                $('.lnksetmaterialconfirm').on("click", function() {
                    var data = 'MethodName=UpdateInlineJob&JobId=' + JobId + '&flag=10&sheetid=' + $(this).attr('data-sheetid');
                    $.ajax({
                        url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                        type: "POST",
                        data: data,
                        cache: false,
                        success: function(data, textStatus, jqxhr) {
                            BindJobDetailSection(JobId);
                        },
                        error: function(jqxhr, textStatus, errorMessage) {
                            navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
                        }
                    });
                });


                $("#lstjoblistitems").append(Result.repairs);
                $("#lstjoblistitems").append(Result.appointments);

                $("#lstjoblistitems").append(Result.tasks);
                $("#lstjoblistitems").append(Result.warranties);
                $('.lnkchangetaskpaidstatus').on("click", function() {
                    var data = 'MethodName=UpdateInlineJob&JobId=' + JobId + '&flag=9&taskid=' + $(this).attr('data-taskid');
                    $.ajax({
                        url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                        type: "POST",
                        data: data,
                        cache: false,
                        success: function(data, textStatus, jqxhr) {
                            BindJobDetailSection(JobId);
                        },
                        error: function(jqxhr, textStatus, errorMessage) {
                            navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
                        }
                    });
                });

                setTimeout(function() { $body.removeClass("loading"); }, 2500)

            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindJobDetailSection(JobId);
            }

        });


    }

    function UpdateJobToNextStage(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/VIRworkflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    BindJobDetailSection(JobId);
                    //mainView.loadPage("jobdetails.html?JId=" + JobId);
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                }
                return false;
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        })

    }

    function UpdateInlineJob(flag) {

        var $ = jQuery.noConflict();
        var JobId = page.query.JId;
        var data = '';
        if (flag != '') {
            if (flag == '1') {
                data = 'MethodName=UpdateInlineJob&JobId=' + JobId + "&flag=" + flag + "&customer_id=" + $("#drdjobcustomers").val();
            } else if (flag == '2') {
                data = 'MethodName=UpdateInlineJob&JobId=' + JobId + "&flag=" + flag + "&job_no=" + $("#txtjobno").val();
            } else if (flag == '3') {
                data = 'MethodName=UpdateInlineJob&JobId=' + JobId + "&flag=" + flag + "&origin_id=" + $("#drdjoborigin").val();
            } else if (flag == '4') {
                data = 'MethodName=UpdateInlineJob&JobId=' + JobId + "&flag=" + flag + "&user_id=" + $("#drdjobcanvaser").val();
            } else if (flag == '5') {
                data = 'MethodName=UpdateInlineJob&JobId=' + JobId + "&flag=" + flag + "&Jobdate=" + $("#txtjobcreatedate").val();
            } else if (flag == '7') {
                data = 'MethodName=UpdateInlineJob&JobId=' + JobId + "&flag=" + flag + "&jurisdictionid=" + $("#drdjobjuridiction").val();
            } else if (flag == '8') {
                data = 'MethodName=UpdateInlineJob&JobId=' + JobId + "&flag=" + flag + "&permit_number=" + $("#txtjobpermit").val();
            }

            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: "POST",
                data: data,
                cache: false,
                success: function(data, textStatus, jqxhr) {
                    BindJobDetailSection(JobId);
                },
                error: function(jqxhr, textStatus, errorMessage) {
                    UpdateInlineJob(flag)
                }
            });
        }

    }
    $('#lnkjobdetailback').on("click", function() {
        var JobId = page.query.JId;
        mainView.loadPage("jobtabs.html?JId=" + JobId);
    });
    $('.accordion-item1').click(function() {
        $(this).toggleClass("accordion-item-expanded");
    });
});
myApp.onPageInit('job-journals', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var JobId = page.query.JId;
    BindJobJournalDetails(JobId);

    function BindJobJournalDetails(JobId) {
        var $ = jQuery.noConflict();
        $('#txtjournal').val('');

        var data = 'MethodName=BindJobJournalDetails&JobId=' + JobId;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                if (data != 'Job not found') {

                    Result = $.parseJSON(data);

                    $("#tbljobjournals").empty();
                    $("#tbljobjournals").append(Result.lstjournals);

                    $("#divjournaluserlist").empty();
                    $("#divjournaluserlist").append(Result.showuserlist);

                    $("#divjournaluserdrop").empty();
                    $("#divjournaluserdrop").append(Result.drduser);
                    $(".optuser").on("click", function() {
                        if ((this).value != "" && (this).value != "0") {
                            var id = (this).value;

                            if (GetElementInsideContainer("divjournaluserlist", id) == "" || GetElementInsideContainer("divjournaluserlist", id) == "0") {
                                $("#divjournaluserlist").append("<div class='rmuser' id='" + (this).value + "'>" + (this).text + "<i data-user-id='" + (this).value + "' rel='delete-journal-user' class='icon-remove aDeletejournals' style='cursor: pointer;'></i></div> ");
                                $(".aDeletejournals").on("click", function() {
                                    var Jid = ($(this).attr('data-user-id'));
                                    $("#" + Jid).remove();
                                    return false;
                                });
                            }

                        }
                        return false;
                    });

                    $('.lnkdeletejournals').on("click", function() {
                        if (confirm('Are you sure?')) {
                            var JournalId = $(this).attr('data-journal-id');
                            DeleteJournalsRecords(JournalId);
                            return false;
                        } else { return false; }
                    });
                    $('.lnkremovejournaluser').on("click", function() {
                        var Userid = $(this).attr('data-user-id');

                        DeleteJournalsUserRecords(Userid);
                        jQuery(this).parent().remove();
                    });
                }else{
                    BindJobJournalDetails(JobId);
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindJobJournalDetails(JobId);
            }

        });
    }

    function GetElementInsideContainer(containerID, childID) {
        var elm = "";
        var elms = document.getElementById(containerID).getElementsByTagName("*");
        for (var i = 0; i < elms.length; i++) {
            if (elms[i].id === childID) {

                elm = elms[i];
                break;
            }
        }
        return elm;
    }
    $('#divaddjobjournal').on("click", function() {
        InsertJobJournals();
    });


    function InsertJobJournals() {
        if ($("#txtjournal").val().trim() != '') {
            $body = $("body");
            $body.addClass("loading");
            var data = 'MethodName=InsertJobJournals&JobId=' + JobId + '&journal=' + $("#txtjournal").val() + '&recipients=' + $("#drdjournaluser").val();
            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: "POST",
                data: data,
                cache: false,
                success: function(data, textStatus, jqxhr) {
                    $body.removeClass("loading");
                    Result = $.parseJSON(data);
                    navigator.notification.alert(
                        "Journals Posted Successfully", alertDismissed,"Success","Done"             
                    );
                    BindJobJournalDetails(JobId);
                },
                error: function(jqxhr, textStatus, errorMessage) {
                    $body.removeClass("loading");
                    InsertJobJournals();
                }

            });
        }
    }

    function DeleteJournalsRecords(JournalId) {
        $body = $("body");
        $body.addClass("loading");
        var JobId = page.query.JId;
        var data = 'MethodName=DeleteJournalsRecords&JobId=' + JobId + '&journalid=' + JournalId;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);
                BindJobJournalDetails(JobId);
                $body.removeClass("loading");
            },
            error: function(jqxhr, textStatus, errorMessage) {
                $body.removeClass("loading");
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }

        });
    }

    function DeleteJournalsUserRecords(userid) {
        var JobId = page.query.JId;

        var data = 'MethodName=DeleteJournalsUserRecords&JobId=' + JobId + '&userid=' + userid;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);

                //BindJobJournalDetails(JobId);
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }

        });
    }
    $('#lnkjobjournalsback').on("click", function() {
        var JobId = page.query.JId;
        mainView.loadPage("jobtabs.html?JId=" + JobId);
    });
});
myApp.onPageInit('job-subscribers', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var JobId = page.query.JId;
    BindJobScriberDetails();

    function Showhidesubscribersection(flag) {
        if (flag == '1') {
            $("#divlstjobsubscriber").css('display', 'none');
            $("#divaddsubscriber").css('display', 'block');
        } else {
            $("#divlstjobsubscriber").css('display', 'block');
            $("#divaddsubscriber").css('display', 'none');
        }
    }

    $('#lnkhideaddsubscriber').on("click", function() {
        Showhidesubscribersection('0');
        return false;
    });
    $('.btnsavenewsubscriber').on("click", function() {

        if ($('#drdaddjobsubscriber').val() != '') {
            AddNewJobSubscriber();
        }
    });

    function BindJobScriberDetails() {
        var JobId = page.query.JId;
        var $ = jQuery.noConflict();
        var data = 'MethodName=BindJobScriberDetails&JobId=' + JobId;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);
                Showhidesubscribersection('0');


                $("#tbljobscribers").empty();
                $("#tbljobscribers").append(Result.lstscribers);


                $('#lnkaddnewsubscriber').on("click", function() {
                    Showhidesubscribersection('1');
                });
                $('.lnkremovesubscriber').on("click", function() {
                    var subId = $(this).attr('data-subscriber-id');
                    DeleteJobSubscribersRecords(subId);
                });


                $("#drdaddjobsubscriber").empty();
                $("#drdaddjobsubscriber").append(Result.drdsubscribers);

                $("#lstaddjobsubscriber").empty();
                $("#lstaddjobsubscriber").append(Result.addscriberslst);


            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindJobScriberDetails();
            }
        });
    }

    function AddNewJobSubscriber() {
        var JobId = page.query.JId;
        var $ = jQuery.noConflict();
        var data = 'MethodName=UpdateInlineJob&JobId=' + JobId + '&flag=6&subscriberid=' + $("#drdaddjobsubscriber").val();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);
                BindJobScriberDetails();
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        });
    }

    function DeleteJobSubscribersRecords(SubId) {
        var JobId = page.query.JId;
        var $ = jQuery.noConflict();
        var data = 'MethodName=DeleteJobSubscribersRecords&JobId=' + JobId + '&subscriberid=' + SubId;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);
                BindJobScriberDetails();
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        });
    }

    $('#lnkjobsubscribersback').on("click", function() {
        var JobId = page.query.JId;
        mainView.loadPage("jobtabs.html?JId=" + JobId);
    });
});
myApp.onPageInit('job-stages', function(page) {
    var $ = jQuery.noConflict();
    $body = $("body");
    jQuery(".formError").remove();

    CheckUserLogin();
    var JobId = page.query.JId;

    //code for drad & drop
    //function touchHandler(event) {
    //    var touch = event.changedTouches[0];

    //    var simulatedEvent = document.createEvent("MouseEvent");
    //    simulatedEvent.initMouseEvent({
    //        touchstart: "mousedown",
    //        touchmove: "mousemove",
    //        touchend: "mouseup"
    //    }[event.type], true, true, window, 1,
    //    touch.screenX, touch.screenY,
    //    touch.clientX, touch.clientY, false,
    //    false, false, false, 0, null);

    //    touch.target.dispatchEvent(simulatedEvent);
    //    event.preventDefault();
    //}

    //function init() {

    //    document.addEventListener("touchstart", touchHandler, true);
    //    document.addEventListener("touchmove", touchHandler, true);
    //    document.addEventListener("touchend", touchHandler, true);
    //    document.addEventListener("touchcancel", touchHandler, true);
    //}

    //init();

    //code for drad & drop end

    BindJobstageSection('1');

    $('#btsavestage').on("click", function() {

        var JobId = page.query.JId;
        var selected = new Array();
        $(".check_stages input:checkbox[name=stages]:checked").each(function() {
            selected.push($(this).val());
        });
        var data = 'MethodName=UpdateJobstages&flag=1&JobId=' + JobId + '&stages=' + selected;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);
                navigator.notification.alert(
                    Result.status, alertDismissed, "Successful", "Done"
                );
                BindJobstageSection('1');

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        });

    });
    $('#btnupdatestage').on("click", function() {

        var JobId = page.query.JId;
        var selected = new Array();

        $('#job_stage_table > li').each(function() {
            if ($(this).attr('id') != undefined)
                selected.push($(this).attr('id'));


        });
        $body.addClass("loading");
        var data = 'MethodName=UpdateJobstages&flag=2&JobId=' + JobId + '&stages=' + selected;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);
                $body.removeClass("loading");
                BindJobstageSection('2');

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        });

    });
    $('#divchangeorder').on("click", function() {
        $('#divjobstagesavesection').css('display', 'none');
        $('#divstagechangeordersection').css('display', 'block');
        return false;
    });
    $('#lnkshowsavestage').on("click", function() {

        $('#divjobstagesavesection').css('display', 'block');
        $('#divstagechangeordersection').css('display', 'none');
        return false;

    });

    function BindJobstageSection(showdiv) {
        var JobId = page.query.JId;
        var $ = jQuery.noConflict();
        var data = 'MethodName=BindJobstageSection&JobId=' + JobId;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);

                $('#lstjobstagesection').empty();
                $('#lstjobstagesection').append(Result.lststage);

                $('#job_stage_table').empty();
                $('#job_stage_table').append(Result.tblstage);

                $("#job_stage_table").sortable();
                if (showdiv == '1') {
                    $('#divjobstagesavesection').css('display', 'block');
                    $('#divstagechangeordersection').css('display', 'none');
                } else {
                    $('#divjobstagesavesection').css('display', 'none');
                    $('#divstagechangeordersection').css('display', 'block');
                }

                if (Result.existingstages == '0')
                    $('#divchangeorder').css('display', 'none');


            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindJobstageSection(showdiv);
            }
        });
    }
    $('#lnkjobstageback').on("click", function() {
        var JobId = page.query.JId;
        mainView.loadPage("jobtabs.html?JId=" + JobId);
    });
});
myApp.onPageInit('job-uploads', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var JobId = page.query.JId;
    var selectedimgids = [];
    //var pictureSource;   // picture source
    //var destinationType; // sets the format of returned value

    // Wait for PhoneGap to connect with the device
    //

    $('#lnkcapturephoto').css('display', 'inline-block');
    $('#lnkcapturegallery').css('display', 'inline-block');
    $('#lnklintjobuploadback').on("click", function() {

        var JobId = page.query.JId;
        mainView.loadPage("jobtabs.html?JId=" + JobId);
        return false;
    });
    $('#lnkjobuploadback').on("click", function() {
        var JobId = page.query.JId;

        $("#divfiles").css("display", "block");
        $("#divEditUploadFileTitle").css("display", "none");
        $("#lnklintjobuploadback").css("display", "inline-block");
        return false;
    });
    $('#btnUploadfromserver').on("click", function() {

        mainView.loadPage("uploadfromserver.html?JId=" + JobId);
        return false;
    });

    BindUploadFilesForSelectedJob('MethodName=GetUploadFilesForSelectedJob&JobID=' + JobId);

    function BindUploadFilesForSelectedJob(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                selectedimgids = [];
                $("#divfiles").css("display", "block");
                $('#btngeneratepdf').css('display', 'none');
                $('#divjobUploadedphotos').empty();
                $('#divjobUploaddocs').empty();
                var photoitems = [];
                var pdfitems = [];

                if (data.isVisible != "true") {
                    $('#btnUploadfromserver').css("display", "none");
                }

                if (data.uploads_array.length > 0) {
                    $("#divEditUploadFileTitle").css("display", "none");
                    $("#lnklintjobuploadback").css("display", "inline-block");
                    var filePath = "https://xactbid.pocketofficepro.com/uploads/";

                    var fileImgDflt = "https://xactbid.pocketofficepro.com/images/icons/pdf_lg.png";

                    photoitems.push("<table>");
                    pdfitems.push("<table>");
                    for (var i = 0; i < data.uploads_array.length; i++) {

                        var fileFullPath = filePath + data.uploads_array[i].filename;
                        var fileImg = "";
                        if (data.uploads_array[i].IMGuploadType == 'image') {
                            $('#btngeneratepdf').css('display', 'inline-block');
                            fileImg = filePath + data.uploads_array[i].file_name;
                            photoitems.push("<tr class='pdf_form1'><td><div class='upldimg'><label class='checkbox metro-checkbox' for='chkjobupld_" + i + "' data-displayname='" + data.uploads_array[i].title.replace(/ /g, "&nbsp;") + "' data-uploadname='" + fileFullPath + "'><input class='jobuplodchk'  name='chkjobupld' data-displayname='" + data.uploads_array[i].title.replace(/ /g, "&nbsp;") + "'  data-uploadname='" + fileFullPath + "' type='checkbox' id='chkjobupld_" + i + "'/><span class='check'></span></label><img id='jobimgupld_" + i + "' title=" + fileFullPath + " class='viewJobUploadFile' src=" + fileFullPath + " /></div>" + data.uploads_array[i].title + "<br /><a id=aUploadByUser_" + data.uploads_array[i].user_id + " class='UploadByUser' href=get_user.html?UserID=" + data.uploads_array[i].user_id + "> " + data.uploads_array[i].FullName + "</a><br />" + data.uploads_array[i].timestamp + "</div><br/>" + data.uploads_array[i].file_size + "kb<div class='uploadFunction'><a name='" + data.uploads_array[i].type + "_" + data.uploads_array[i].upload_id + "' title='" + data.uploads_array[i].title.replace(/ /g, "&nbsp;") + "' class='EditJobUploadFiletitle' id=aEditJobUploadFiletitle_" + data.uploads_array[i].upload_id + "><i class='icon-pencil'></i></a>&nbsp;&nbsp;<a title='" + data.uploads_array[i].title.replace(" ", "&nbsp;") + "' class='DeleteJobUploadFile' id=aDeleteJobUploadFile_" + data.uploads_array[i].upload_id + "><i class='icon-remove'></i></a></div></td></tr>");

                        } else {
                            fileImg = fileImgDflt;
                            pdfitems.push("<tr class='pdf_form1'><td><div class='upldimg'><img id='jobimgupld_" + i + "' title=" + fileFullPath + " class='viewJobUploadFile' src=" + fileImg + " /></div>" + data.uploads_array[i].title + "<br /><a id=aUploadByUser_" + data.uploads_array[i].user_id + " class='UploadByUser' href=get_user.html?UserID=" + data.uploads_array[i].user_id + "> " + data.uploads_array[i].FullName + "</a><br />" + data.uploads_array[i].timestamp + "<br/>" + data.uploads_array[i].file_size + "kb<div class='uploadFunction'><a name='" + data.uploads_array[i].type + "_" + data.uploads_array[i].upload_id + "' title='" + data.uploads_array[i].title + "' class='EditJobUploadFiletitle' id=aEditJobUploadFiletitle_" + data.uploads_array[i].upload_id + "><i class='icon-pencil'></i></a>&nbsp;&nbsp;<a title='" + data.uploads_array[i].title + "' class='DeleteJobUploadFile' id=aDeleteJobUploadFile_" + data.uploads_array[i].upload_id + "><i class='icon-remove'></i></a></div></td></tr>");

                        }

                        //var fsize = GetFileSize(fileFullPath);
                        //alert(fsize);
                        //if (data.uploads_array[i].type != null || data.uploads_array[i].type != '') {


                        //items.push("<tr class='pdf_form1'><td><a href=" + fileFullPath + "><img title=" + fileFullPath + " class='viewJobUploadFile' src=" + fileImg + " /></a><br /><a id=aviewUploadFile_" + data.uploads_array[i].upload_id + " class='viewUploadFile' title=" + fileFullPath + " href=" + fileFullPath + "> " + data.uploads_array[i].title + "</a><br /><a id=aUploadByUser_" + data.uploads_array[i].user_id + " class='UploadByUser' href=get_user.html?UserID=" + data.uploads_array[i].user_id + "> " + data.uploads_array[i].FullName + "</a><br />" + data.uploads_array[i].timestamp + "</br></br><div class='uploadFunction'><a name='" + data.uploads_array[i].type + "_" + data.uploads_array[i].upload_id + "' title='" + data.uploads_array[i].title + "' class='EditJobUploadFiletitle' id=aEditJobUploadFiletitle_" + data.uploads_array[i].upload_id + "><i class='icon-pencil'></i></a>&nbsp;&nbsp;<a title='" + data.uploads_array[i].title + "' class='DeleteJobUploadFile' id=aDeleteJobUploadFile_" + data.uploads_array[i].upload_id + "><i class='icon-remove'></i></a></div></td></tr>");


                        //}
                        //else {
                        //    items.push("<tr  class='pdf_form1'><td><a href=" + fileFullPath + "><img title=" + fileFullPath + " class='viewJobUploadFile' src=" + fileImg + " /></a><br /><a id=aviewUploadFile_" + data.uploads_array[i].upload_id + " class='viewUploadFile' title=" + fileFullPath + " href=" + fileFullPath + "> " + data.uploads_array[i].title + "</a><br /><a id=aUploadByUser_" + data.uploads_array[i].user_id + " class='UploadByUser' href=get_user.html?UserID=" + data.uploads_array[i].user_id + "> " + data.uploads_array[i].FullName + "</a><br />" + data.uploads_array[i].timestamp + "</br></br><div class='uploadFunction'><a title='" + data.uploads_array[i].title + "' class='EditJobUploadFiletitle' id=aEditJobUploadFiletitle_" + data.uploads_array[i].upload_id + "><i class='icon-pencil'></i></a>&nbsp;&nbsp;<a title='" + data.uploads_array[i].title + "' class='DeleteJobUploadFile' id=aDeleteJobUploadFile_" + data.uploads_array[i].upload_id + "><i class='icon-remove'></i></a></div></td></tr>");
                        //}
                    }
                    photoitems.push("</table>");
                    pdfitems.push("</table>");
                    $('#divjobUploadedphotos').append(photoitems.join(''));
                    $('#divjobUploaddocs').append(pdfitems.join(''));
                    $(".viewJobUploadFile").on("click", function() {
                        var FileName = this.title;
                        var FileType = FileName.substring(FileName.lastIndexOf(".") + 1);
                        if (FileType == 'pdf') {
                            //window.open('http://docs.google.com/viewer?url=' + FileName, '_blank');
                            window.open(FileName, '_system');
                            return false;
                        } else {
                            //window.open(FileName, '_system');
                            var FileName = this.title;
                            launchEditor(this.id, FileName);
                            return false;
                        }

                    });
                    //$(".jobuplodchk").on("click",function () {

                    //    try {

                    //        //var chkid = $(this).attr('for');
                    //        //var ischecked = $('#' + chkid).prop('checked');


                    //         var ischecked = $(this).prop('checked');
                    //        var upldimages = $(this).attr('data-uploadname');
                    //        if (ischecked) {
                    //            selectedimgids.push(upldimages);
                    //        } else {
                    //            selectedimgids = jQuery.grep(selectedimgids, function (value) {
                    //                return value != upldimages;
                    //            });
                    //        }
                    //        alert(selectedimgids);

                    //    } catch (e) {
                    //        alert(e);
                    //    }

                    //});


                    $(".EditJobUploadFiletitle").on("click", function() {

                        var title = this.title;
                        var splits_id = this.id.split('_');
                        var uploadId = splits_id[1];
                        var scheduleJobInfo = this.name.split('_');
                        var scheduleType = scheduleJobInfo[0];
                        var sJobUploadId = scheduleJobInfo[1];

                        if (scheduleType == 'roofing' || scheduleType == 'window' || scheduleType == 'repair' || scheduleType == 'gutter') {
                            mainView.loadPage("schedule_jobs.html?flag=edit&scheduleType=" + scheduleType + "&UploadId=" + sJobUploadId + "&JId=" + JobId);
                        } else {
                            $("#lblJUId").val(uploadId);
                            $("#txtUFileTitle").val(title);
                            $("#divEditUploadFileTitle").css("display", "block");
                            $("#lnklintjobuploadback").css("display", "none");
                            $("#divfiles").css("display", "none");

                        }
                        return false;
                    });

                    $(".DeleteJobUploadFile").on("click", function() {
                        if (confirm('Are you sure?')) {
                            var splits_id = this.id.split('_');
                            var uploadId = splits_id[1];
                            BindDeleteJobUploadFile('MethodName=deleteJobUploadFile&uploadId=' + uploadId);
                            return false;
                        } else { return false; }
                    });

                    $("#btnSubmitEditTitle").on("click", function() {
                        var ans = check_itemsvalidate('#divEditUploadFileTitle input');
                        if (ans) {
                            var UploadID = $("#lblJUId").val();
                            var Title = $("#txtUFileTitle").val();
                            BindupdateJobUploadFileTitle('MethodName=updateJobUploadFileTitle&JobID=' + JobId + '&UploadID=' + UploadID + '&Title=' + Title);
                        } else { return false; }
                    });
                } else {
                    $("#divfiles").css("display", "block");
                    $('#divjobUploadedphotos').empty();
                    $('#divjobUploaddocs').empty();
                    photoitems.push("<table>");
                    pdfitems.push("<table>");
                    photoitems.push("<tr class='acenter'><td>No files Form uploded</td></tr>");
                    pdfitems.push("<tr class='acenter'><td>No files Form uploded</td></tr>");
                    photoitems.push("</table>");
                    pdfitems.push("</table>");
                    $('#divjobUploadedphotos').append(photoitems.join(''));
                    $('#divjobUploaddocs').append(pdfitems.join(''));
                    //alert(data.message);
                    //$('#lblJobUploadList').html("No files Form uploded");
                    //$("#lblJobUploadList").css("display", "block");
                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }
    $('#btngeneratepdf').on("click", function() {

        var selected = new Array();
        var selectedname = new Array();
        $(".metro-checkbox input:checkbox[name=chkjobupld]:checked").each(function() {
            upldimages = $(this).attr('data-uploadname');

            upldimagesname = $(this).attr('data-displayname');

            selected.push(upldimages);
            selectedname.push(upldimagesname);
        });

        if (selected.length > 0) {

            var JobId = page.query.JId;
            var data = 'MethodName=Genertepdffromimages&pdfdata=' + selected + '&jobid=' + JobId + '&selectedname=' + selectedname;
            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: 'POST',
                data: data,
                cache: false,
                success: function(data, textStatus, jqxhr) {
                    var data = $.parseJSON(data);

                    if (data.status == "1") {
                        var FullPath = "https://xactbid.pocketofficepro.com/uploads/" + data.filename;

                        //window.open('http://docs.google.com/viewer?url=https://xactbid.pocketofficepro.com/uploads/' + data.filename, '_blank');
                        window.open(FullPath, '_system');
                        //alert('file saved successfully');
                        BindUploadFilesForSelectedJob('MethodName=GetUploadFilesForSelectedJob&JobID=' + JobId);
                    } else {
                        return false;
                    }
                },
                error: function(jqxhr, textStatus, errorMessage) {
                    navigator.notification.alert(
                        errorMessage, alertDismissed, "An error occured", "Done"
                    );
                }
            });
        } else {
            navigator.notification.alert(
                "Select images to generate pdf", alertDismissed, "Unsuccessful", "Done"
            );
        }
    });

    function BindupdateJobUploadFileTitle(data) {
        var JobId = page.query.JId;
        var $ = jQuery.noConflict();
        $.when($.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(data.message, alertDismissed, "Success", "Done");
                    //mainView.loadPage("jobuploads.html?JId=" + JobId);
                    //BindUploadFilesForSelectedJob('MethodName=GetUploadFilesForSelectedJob&JobID=' + JobId);
                    $("#divEditUploadFileTitle").css("display", "none");
                    $("#lnklintjobuploadback").css("display", "inline-block");
                } else {
                    navigator.notification.alert(data.message, alertDismissed, "Success", "Done");
                    //$('#lblJobUploadList').html(data.message);
                    //$("#lblJobUploadList").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })).then(BindUploadFilesForSelectedJob('MethodName=GetUploadFilesForSelectedJob&JobID=' + JobId));
        return false;
    }

    function BindDeleteJobUploadFile(data) {
        var JobId = page.query.JId;

        var $ = jQuery.noConflict();
        $.when($.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    BindUploadFilesForSelectedJob('MethodName=GetUploadFilesForSelectedJob&JobID=' + JobId)
                        //alert(data.message);
                        //mainView.loadPage("jobdetails.html?JId=" + data.job_id);
                    $("#divEditUploadFileTitle").css("display", "none");
                    $("#lnklintjobuploadback").css("display", "inline-block");
                } else {
                    //alert(data.message);
                    $('#lblJobUploadList').html(data.message);
                    $("#lblJobUploadList").css("display", "block");

                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })).then();
        return false;
    }

    function GetFileSize(filepath) {
        var $ = jQuery.noConflict();
        var res = false;
        var fs = 0;
        var request;
        request = $.ajax({
            type: "HEAD",
            url: filepath,
            success: function() {
                //alert("Size is " + request.getResponseHeader("Content-Length"));
                var fileSize = request.getResponseHeader("Content-Length");
                //alert(fileSize/1000);
                return res = fileSize;

            }
        });
        //alert(res);

    }

    //function GetFileSize(filepath)
    //{
    //    var request;
    //    request = $.ajax({
    //        type: "HEAD",
    //        url: filepath,
    //        success: function () {
    //            //alert("Size is " + request.getResponseHeader("Content-Length"));
    //            var fileSize = request.getResponseHeader("Content-Length");
    //            return filesize;
    //        }
    //    });
    //}
    function SaveUploadFileRecord(imageURI, flag) {
        var $ = jQuery.noConflict();
        var JobId = page.query.JId;
        try {
            if (flag == '1') {
                
                var options = new FileUploadOptions();
                options.fileKey = "file";
                options.fileName = imageURI.substr(imageURI.lastIndexOf('/') + 1);
                if (options.fileName.indexOf(".pdf") > -1) {
                    options.mimeType = "application/pdf";
                } else {
                    options.mimeType = "image/jpeg";
                }

                var params = new Object();
                params.flag = "2";
                params.jobid = JobId;
                params.filename = imageURI.substr(imageURI.lastIndexOf('/') + 1);
                options.params = params;
                options.chunkedMode = false;

                var $ = jQuery.noConflict();
                $body = $("body");
                $body.addClass("loading");

                var transfer = new FileTransfer();
                transfer.upload(
                    imageURI,
                    encodeURI('https://xactbid.pocketofficepro.com/fileuploader.php'),
                    function(response) {
                        //alert("File uploaded successfully");
                        navigator.notification.alert("File uploaded successfully", alertDismissed, "Success", "Done");
                        BindUploadFilesForSelectedJob('MethodName=GetUploadFilesForSelectedJob&JobID=' + JobId);
                        $body.removeClass("loading");

                    },
                    function(error) {
                        navigator.notification.alert(
                            "ERROR FILEUPLOAD", alertDismissed, "An error occured", "Done"
                        );
                        $body.removeClass("loading");

                    },
                    options
                );

            }

            if (flag == '2') {

                var form_data = new FormData();
                form_data.append('flag', '5');
                form_data.append('jobid', JobId);
                form_data.append('filepath', imageURI);
                var filename = imageURI.substr(imageURI.lastIndexOf('/') + 1);
                form_data.append('filename', filename);

                $.when($.ajax({
                    url: 'https://xactbid.pocketofficepro.com/fileuploader.php', // point to server-side PHP script
                    dataType: 'text', // what to expect back from the PHP script, if anything
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function(message) {
                        navigator.notification.alert(
                            message, alertDismissed, "Successful", "Done"
                        );

                    },
                    error: function(data) {
                        // alert('err' + data);
                        var myerr = 'err' + data;
                        navigator.notification.alert(
                            myerr, alertDismissed, "An error occured", "Done"
                        );
                    },
                }).then(function() {
                    BindUploadFilesForSelectedJob('MethodName=GetUploadFilesForSelectedJob&JobID=' + JobId);
                    featherEditor.close();
                }));
            }
            if (flag == '3') {

                var sFileExtension = imageURI.split('.')[imageURI.split('.').length - 1].toLowerCase();

                var options = new FileUploadOptions();
                var params = new Object();
                options.fileKey = "file";
                options.fileName = imageURI.substr(imageURI.lastIndexOf('/') + 1);
                if (options.fileName.indexOf(".pdf") > -1 || options.fileName.indexOf(".docs") > -1) {
                    options.mimeType = "application/pdf";
                    params.filetype = "pdf";
                } else {
                    options.mimeType = "image/jpeg";
                    params.filetype = "jpeg";
                }


                params.flag = "6";
                params.jobid = JobId;
                params.filename = imageURI.substr(imageURI.lastIndexOf('/') + 1);
                options.params = params;

                var ft = new FileTransfer();

                ft.upload(imageURI, "https://xactbid.pocketofficepro.com/fileuploader.php",
                    function(result) {
                        BindUploadFilesForSelectedJob('MethodName=GetUploadFilesForSelectedJob&JobID=' + JobId);
                    },
                    function(error) {
                        // alert('Error uploading file ' + path + ': ' + error.code);
                        var myerror = 'Error uploading file ' + path + ': ' + error.code;
                        navigator.notification.alert(
                            myerror, alertDismissed, "An error occured", "Done"
                        );
                    }, options);
            }
        } catch (e) {
            navigator.notification.alert(
                e, alertDismissed, "Unsuccessful", "Done"
            );
        }

    }
    $('#lnkshowphotodiv').on("click", function() {
        $('#divjobUploadedphotos').css('display', 'block');
        $('#divjobUploaddocs').css('display', 'none');
        $(this).addClass('active');
        $('#lnkshowdocdiv').removeClass('active');
        $('#btngeneratepdf').css('display', 'inline-block');
        $('#lnkcapturephoto').css('display', 'inline-block');
        $('#lnkcapturegallery').css('display', 'inline-block');
    });
    $('#lnkshowdocdiv').on("click", function() {
        $('#divjobUploadedphotos').css('display', 'none');
        $('#divjobUploaddocs').css('display', 'block');
        $(this).addClass('active');
        $('#lnkshowphotodiv').removeClass('active');
        $('#btngeneratepdf').css('display', 'none');
        $('#lnkcapturephoto').css('display', 'none');
        $('#lnkcapturegallery').css('display', 'none');
    });

    $('#lnkcapturephoto').on("click", function() {
        capturePhoto();
    });
    //$('#lnkcapturegallery').on("click",function () {
    //    // getPhoto(pictureSource.PHOTOLIBRARY);
    //    var JobId = page.query.JId;
    //    $('#fluJobUpload').click();
    //    //jobFileUpload();
    //    return false;
    //});
    $("#lnkcapturegallery").on("touchend", function() { $('#fluJobUpload').click() });

    $('#fluJobUpload').change(function() {
        if ($('#fluJobUpload').val() != "") {
            var JobId = page.query.JId;
            var fileType = "";
            var files = $('#fluJobUpload')[0].files;
            var form_data = new FormData();
            var file_data = $('#fluJobUpload').prop('files')[0];
            if (files[0].name.indexOf(".pdf") > -1 || files[0].name.indexOf(".docs") > -1) {
                fileType = "pdf";
            } else {
                fileType = "jpeg";
            }

            form_data.append('flag', '6');
            form_data.append('filename', files[0].name);
            form_data.append('file', file_data);
            form_data.append('jobid', JobId)
            form_data.append('filetype', fileType)

            if (files[0].type == 'image/png' || files[0].type == 'image/jpeg' || files[0].type == 'image/jpg' || files[0].type == 'application/pdf' || files[0].type == 'application/docs') {

                $.when($.ajax({
                    url: 'https://xactbid.pocketofficepro.com/fileuploader.php', // point to server-side PHP script
                    dataType: 'text', // what to expect back from the PHP script, if anything
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function(php_script_response) {
                        // alert(php_script_response);
                        navigator.notification.alert(
                            php_script_response, alertDismissed, "Successful", "Done"
                        );
                        //$('#fluJobUpload').val("");
                    },
                    error: function(data) {
                        // alert('err' + data);
                        var myerr = 'err' + data;
                        navigator.notification.alert(
                            myerr, alertDismissed, "An error occured", "Done"
                        );

                    },
                }).then(function() {
                    BindUploadFilesForSelectedJob('MethodName=GetUploadFilesForSelectedJob&JobID=' + JobId);
                }));
            }else{
               navigator.notification.alert(
                    fileType+" File type not allowed", alertDismissed, "Unsuccessful", "Done"
                ); 
            }
        } else {
            navigator.notification.alert(
                "No files selected", alertDismissed, "Unsuccessful", "Done"
            );
        }
        return false;
    });

    function onPhotoDataSuccess(imageURI) {
        try {

            SaveUploadFileRecord(imageURI, '1');

        } catch (e) {
            navigator.notification.alert(
                e, alertDismissed, "Unsuccessful", "Done"
            );
        }
    }


    // Called when a photo is successfully retrieved
    //
    function onPhotoURISuccess(imageURI) {
        // Uncomment to view the image file URI
        // console.log(imageURI);

        // Get image handle
        //
        //var largeImage = document.getElementById('largeImage');

        SaveUploadFileRecord(imageURI, '3');

        //// Unhide image elements
        ////
        //largeImage.style.display = 'block';

        //// Show the captured photo
        //// The inline CSS rules are used to resize the image
        ////
        //largeImage.src = imageURI;
    }

    // A button will call this function
    //
    function capturePhoto() {

        if (device.platform === "iOS") {
            /*for ios*/
            navigator.camera.getPicture(onPhotoDataSuccess, onFail, { 
                quality: 50,
                destinationType: destinationType.NATIVE_URI,
                encodingType: Camera.EncodingType.JPEG,
            });
        } else if (device.platform == 'Android') {
            navigator.camera.getPicture(onPhotoDataSuccess, onFail, {
                quality: 50,
                encodingType: Camera.EncodingType.JPEG,
                correctOrientation: true
            });
        }
    }

    // A button will call this function
    //
    function capturePhotoEdit() {
        // Take picture using device camera, allow edit, and retrieve image as base64-encoded string
        navigator.camera.getPicture(onPhotoDataSuccess, onFail, { quality: 20, allowEdit: true });
    }

    // A button will call this function
    //
    // function getPhoto(source) {

    //     // Retrieve image file location from specified source
    //     navigator.camera.getPicture(onPhotoURISuccess, onFail, {
    //         quality: 50,
    //         destinationType: destinationType.FILE_URI,
    //         sourceType: source
    //     });
    // }

    // Called if something bad happens.
    //
    function onFail(message) {
        // alert('Failed because: ' + message);
        var mymsg = 'Failed because: ' + message;
        navigator.notification.alert(
            mymsg, alertDismissed, "Unsuccessful", "Done"
        );


    }

    try {

        var featherEditor = new Aviary.Feather({
            apiKey: '3e5188e231034b4496f4f1062bc9a4c3',
            theme: 'minimum', // Check out our new 'light' and 'dark' themes!
            tools: 'effects,orientation,lighting,color,sharpness,focus,draw,text,meme', //add all to get all tools
            appendTo: '',
            onSave: function(imageID, newURL) {
                SaveUploadFileRecord(newURL, '2');
                var img = document.getElementById(imageID);
                img.src = newURL;
            },
            onError: function(errorObj) {
                navigator.notification.alert(
                    errorObj.message, alertDismissed, "An error occured", "Done"
                );
            }
        });
    } catch (e) {

        navigator.notification.alert(
            e, alertDismissed, "Unsuccessful", "Done"
        );
    }

    function launchEditor(id, src) {

        featherEditor.launch({
            image: id,
            url: src
        });
        return false;
    }
});
myApp.onPageInit('get_task', function(page) {

    var $ = jQuery.noConflict();
    var srcPage = page.query.srcPage;
    jQuery(".formError").remove();
    CheckUserLogin();
    bindTaskTimeList('MethodName=getEventTimeList');

    function bindTaskTimeList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data) {
                var data = $.parseJSON(data);
                $("#ddlTasktStartTime").empty();
                $("#ddlTaskEndTime").empty();
                if (data.timeList.length > 0) {
                    $("#ddlTasktStartTime").append(data.timeList);
                    $("#ddlTaskEndTime").append(data.timeList);

                    return false;
                } else {
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function DateCallJS() {

        var $ = jQuery.noConflict();
        $('.datestamp').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: "1925:2999",
            onClose: function() {
                if (this.value != '') {
                    $.validationEngine.loadValidation('.datestamp');
                }
            }
        });
    }

    DateCallJS();
    var JobId = page.query.JId;

    function SetGettaskpage() {

        var JobId = page.query.JId;
        if (page.query.Id != undefined && page.query.Id != null) {
            $('#divheadjobtasks').empty();
            $('#divheadjobtasks').append('View Task Detail');
            $('#divViewTaskDetail').css('display', 'block');
            $('#divAddtask').css('display', 'none');
            Bindjobgettaskdetails('MethodName=Bindjobgettaskdetails&JobId=' + JobId + '&TaskId=' + page.query.Id)
        } else {
            $('#divheadjobtasks').empty();
            $('#divheadjobtasks').append('Add Job Task');
            $('#divAddtask').css('display', 'block');
            $('#divViewTaskDetail').css('display', 'none');
            Bindjobgettaskdetails('MethodName=Bindjobgettaskdetails&JobId=' + JobId + '&TaskId=0')
        }
    }
    SetGettaskpage();


    $('#btnSavejobTask').on("click", function() {
        var JobId = page.query.JId;
        var data = '';

        var StartDate = $('#txtTaskStartDate').val();
        var EndDate = $('#txtTaskEndDate').val();
        var StartTime = $('#ddlTasktStartTime select[name=time]').val();
        var EndTime = $('#ddlTaskEndTime select[name=time]').val();

        if (check_itemsvalidate('#divAddtask input')) {
            if (StartDate > EndDate) {
                navigator.notification.alert(
                    "End Date must be greater than Start Date", alertDismissed, "Unsuccessful", "Done"
                );
                return false;
            } else if (StartDate == EndDate) {
                if (StartTime >= EndTime) {
                    navigator.notification.alert(
                        "End Time must be greater than Start Time", alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                } else {
                    if (page.query.Id != undefined && page.query.Id != null) {
                        data = 'MethodName=InsertUpdateNewJobtask&JobId=' + JobId + '&TaskId=' + page.query.Id + '&task_type=' + $('#ddltasktype').val() + '&contractor=' + $('#ddlcontractor').val() + '&stage=' + $('#ddltaskstage').val() + '&notes=' + $('#txttaskNotes').val().trim() + '&duration=' + $('#drdduration').val() + '&startDt=' + StartDate + '&endDt=' + EndDate + '&startTime=' + StartTime + '&endTime=' + EndTime;
                        if ($("#chktaskschedule").prop('checked')) {
                            data = data + '&schedule=1';
                            data = data + '&startdate=' + $('#txttaskdate').val();
                        }
                        if ($("#chktaskcomplete").prop('checked')) {
                            data = data + '&completed=1';
                        }

                    } else {
                        data = 'MethodName=InsertUpdateNewJobtask&JobId=' + JobId + '&TaskId=0&task_type=' + $('#ddltasktype').val() + '&contractor=' + $('#ddlcontractor').val() + '&stage=' + $('#ddltaskstage').val() + '&notes=' + $('#txttaskNotes').val().trim() + '&duration=' + $('#drdduration').val() + '&startDt=' + StartDate + '&endDt=' + EndDate + '&startTime=' + StartTime + '&endTime=' + EndTime;
                    }
                    InsertUpdateNewJobtask(data);
                }
            } else {
                if (page.query.Id != undefined && page.query.Id != null) {
                    data = 'MethodName=InsertUpdateNewJobtask&JobId=' + JobId + '&TaskId=' + page.query.Id + '&task_type=' + $('#ddltasktype').val() + '&contractor=' + $('#ddlcontractor').val() + '&stage=' + $('#ddltaskstage').val() + '&notes=' + $('#txttaskNotes').val().trim() + '&duration=' + $('#drdduration').val() + '&startDt=' + StartDate + '&endDt=' + EndDate + '&startTime=' + StartTime + '&endTime=' + EndTime;
                    if ($("#chktaskschedule").prop('checked')) {
                        data = data + '&schedule=1';

                    }
                    if ($("#chktaskcomplete").prop('checked')) {
                        data = data + '&completed=1';
                    }

                } else {
                    data = 'MethodName=InsertUpdateNewJobtask&JobId=' + JobId + '&TaskId=0&task_type=' + $('#ddltasktype').val() + '&contractor=' + $('#ddlcontractor').val() + '&stage=' + $('#ddltaskstage').val() + '&notes=' + $('#txttaskNotes').val().trim() + '&duration=' + $('#drdduration').val() + '&startDt=' + StartDate + '&endDt=' + EndDate + '&startTime=' + StartTime + '&endTime=' + EndTime;
                }
                InsertUpdateNewJobtask(data);
            }

        }

    });
    $('#btnCanceltask').click(function() {
        jQuery(".formError").remove();
        var JobId = page.query.JId;
        if (page.query.Id != undefined && page.query.Id != null) {
            $('#divheadjobtasks').empty();
            $('#divheadjobtasks').append('View Task Detail');
            $('#divViewTaskDetail').css('display', 'block');
            $('#divAddtask').css('display', 'none');
            $('#divedittask').css('display', 'none');
        } else {
            mainView.loadPage("jobdetails.html?JId=" + JobId);
        }
    });
    $('#lnktaskviewback').on("click", function() {
        if (srcPage != null && srcPage != "" && srcPage != "undefined") {

            mainView.loadPage(srcPage);
        } else {
            mainView.loadPage("jobdetails.html?JId=" + JobId);
        }

    });



    function Editjobtask() {
        DateCallJS();
        $('#divheadjobtasks').empty();
        $('#divheadjobtasks').append('Edit Task');
        $('#divAddtask').css('display', 'block');
        $('#divedittask').css('display', 'block');
        $('#divViewTaskDetail').css('display', 'none');


    }

    function Bindjobgettaskdetails(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $('#ddltasktype').empty();
                $('#ddltasktype').append(data.drdtasktype);

                $('#ddltaskstage').empty();
                $('#ddltaskstage').append(data.drdtaskstage);

                $('#ddlcontractor').empty();
                $('#ddlcontractor').append(data.drdtaskcontract);

                $('#drdduration').empty();
                $('#drdduration').append(data.drdduration);

                if (page.query.Id != undefined && page.query.Id != null) {
                    $('#tbltaskdetails').empty();
                    $('#tbltaskdetails').append(data.taskdetails);
                    $('#btndeletetask').on("click", function() {
                        if (confirm('Are you sure?'))
                            DeleteJobTask();

                    });

                    $('#btnedittask').on("click", function() {
                        var JobId = page.query.JId;
                        Editjobtask();
                    });
                    $('#chktaskschedule').on("click", function() {
                        if (this.checked)
                            $('#divtaskschedule').css('display', 'block');
                        else
                            $('#divtaskschedule').css('display', 'none');

                    });


                    if (data.myTask.start_date != undefined && data.myTask.start_date != null && data.myTask.start_date != "0000-00-00") {
                        $('#chktaskschedule').attr('checked', 'checked');
                        $('#txtTaskStartDate').val(data.myTask.start_date);
                        $('#divtaskschedule').css('display', 'block');
                    } else {
                        $('#divtaskschedule').css('display', 'none');
                        $('.datestamp').datepicker("setDate", new Date());
                    }
                    if (data.myTask.paid != undefined && data.myTask.paid != null) {
                        $('#chktaskcomplete').attr('checked', 'checked');
                        $('#lbledittaskcompletedate').text(data.myTask.paid);
                    }
                    if (data.myTask.notes != undefined && data.myTask.notes != null) {
                        $('#txttaskNotes').val(data.myTask.notes);
                    }
                    if (data.myTask.start_date != undefined && data.myTask.start_date != null && data.myTask.start_date != "0000-00-00") {
                        $("#txtTaskStartDate").val(data.myTask.start_date);
                    }
                    if (data.myTask.end_date != undefined && data.myTask.end_date != null && data.myTask.end_date != "0000-00-00") {
                        $("#txtTaskEndDate").val(data.myTask.end_date);
                    }
                    $('#ddlTasktStartTime select[name=time]').val(data.myTask.start_time);
                    $('#ddlTaskEndTime select[name=time]').val(data.myTask.end_time);
                }




            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }

        });
        return false;
    }

    function InsertUpdateNewJobtask(data) {
        var JobId = page.query.JId;
        $.when($.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                navigator.notification.alert(
                    data.message, alertDismissed, "Successful", "Done"
                );
                mainView.loadPage("jobdetails.html?JId=" + JobId);
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }

        }).then(SetGettaskpage()));
        return false;
    }

    function DeleteJobTask() {
        var JobId = page.query.JId;
        var data = 'MethodName=DeleteJobTask&JobId=' + JobId + '&TaskId=' + page.query.Id;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                mainView.loadPage("jobdetails.html?JId=" + JobId);
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }

        });
        return false;

    }
});
myApp.onPageInit('job-operations', function(page) {

    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var JobId = page.query.JId;
    var Jobflag = page.query.flag;
    BindJobOperationPageData();

    function DateCallJS() {

        var $ = jQuery.noConflict();
        $('.datestamp').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: "1925:2999",
            onClose: function() {
                if (this.value != '') {
                    $.validationEngine.loadValidation('.datestamp');
                }
            }
        });
    }

    function BindJobOperationPageData() {

        var JobId = page.query.JId;
        var data = 'MethodName=BindJobOperationPageData&JobId=' + JobId + '&flag=' + page.query.flag;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data) {
                var data = $.parseJSON(data);
                if (page.query.flag == '1') {

                    $('#divjobop1').css('display', 'block');
                    $('#divheadjobopetation').empty();
                    $('#divheadjobopetation').append('Modify Job Insurance');
                    $('#ddljobprovider').empty();
                    $('#ddljobprovider').append(data.drdprovider);
                    $('#txtjobpolicy').val(data.txtpolicy);
                    if ($('#txtjobinsuranceclaim').val() != null && $('#txtjobinsuranceclaim').val() != null)
                        $('#txtjobinsuranceclaim').val(data.myJob.claim);
                    if (data.myJob.insurance_approval != null && data.myJob.insurance_approval != '' && data.myJob.insurance_approval != undefined) {
                        $('#lblapprovetext').append(data.lblapprovetext);
                        $("#chkjobclaimapproved").prop('checked', 'checked');
                    }
                } else if (page.query.flag == '2') {

                    $('#divjobop2').css('display', 'block');
                    $('#divheadjobopetation').empty();
                    $('#divheadjobopetation').append('Approve Job Estimate');
                    $('#divjobop2').empty();
                    $('#divjobop2').append(data.jobpagestr);
                } else if (page.query.flag == '3') {

                    $('#divjobop3').css('display', 'block');
                    $('#divheadjobopetation').empty();
                    $('#divheadjobopetation').append('Assign Referral');
                    $('#divjobop3').empty();
                    $('#divjobop3').append(data.jobpagestr);
                    $('#drdjoboprereferal').change(function() {
                        if ($(this).val() != '')
                            $("#chkjobrefpaid").removeAttr("disabled");
                        else {
                            $("#chkjobrefpaid").attr("disabled", true);
                            $("#chkjobrefpaid").removeAttr("checked");
                        }
                    });

                } else if (page.query.flag == '4') {

                    $('#divjobop4').css('display', 'block');
                    $('#divheadjobopetation').empty();
                    $('#divheadjobopetation').append('Assign Status Hold');
                    $('#divjobop4').empty();
                    $('#divjobop4').append(data.jobpagestr);
                    DateCallJS();
                    $('#chkjobexpires').on("click", function() {
                        if ($(this).prop('checked'))
                            $('#divjobexpdate').css('display', 'block');
                        else
                            $('#divjobexpdate').css('display', 'none');
                    });
                    if (data.myJob.status_hold_id != null && data.myJob.status_hold_id != undefined && data.myJob.status_hold_id != '')
                        $('#btnremovejobexpire').css('display', 'inline-block');
                    else
                        $('#btnremovejobexpire').css('display', 'none');

                } else if (page.query.flag == '5') {

                    $('#divjobop5').css('display', 'block');
                    $('#divheadjobopetation').empty();
                    $('#divheadjobopetation').append('Modify Job Type');
                    $('#divjobop5').empty();
                    $('#divjobop5').append(data.jobpagestr);
                } else if (page.query.flag == '6') {

                    $('#divjobop6').css('display', 'block');
                    $('#divheadjobopetation').empty();
                    $('#divheadjobopetation').append('Assign Warranty');
                    $('#divjobop6').empty();
                    $('#divjobop6').append(data.jobpagestr);
                } else if (page.query.flag == '7') {

                    $('#divjobop7').css('display', 'block');
                    $('#divheadjobopetation').empty();
                    $('#divheadjobopetation').append('Assign Jurisdiction');
                    $('#divjobop7').empty();
                    $('#divjobop7').append(data.jobpagestr);
                } else if (page.query.flag == '8') {


                    if (data.status == '0') {
                        navigator.notification.alert(
                            "Please assign a jurisdiction first", alertDismissed, "Unsuccessful", "Done"
                        );
                        $('#btnCanceljoboperation').click();
                        //mainView.loadPage("job_operation.html?flag=7&JId=" + JobId);
                    } else {
                        $('#divjobop8').css('display', 'block');
                        $('#divheadjobopetation').empty();
                        $('#divheadjobopetation').append('Assign Permit');
                        $('#divjobop8').empty();
                        $('#divjobop8').append(data.jobpagestr);
                    }

                } else if (page.query.flag == '9') {

                    $('#divjobop8').css('display', 'block');
                    $('#divheadjobopetation').empty();
                    $('#divheadjobopetation').append('Assign Canvasser');
                    $('#divjobop8').empty();
                    $('#divjobop8').append(data.jobpagestr);
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindJobOperationPageData();
            }

        });
        return false;
    }

    $('#btnSavejoboperation').on("click", function() {

        var JobId = page.query.JId;
        var data = '';
        if (page.query.flag == '1') {
            data = 'MethodName=UpdateInlineJob&JobId=' + JobId + '&flag=11&insurance_id=' + $('#ddljobprovider').val() + '&claim=' + $('#txtjobinsuranceclaim').val().trim() + '&policy=' + $('#txtjobpolicy').val().trim();
            if ($("#chkjobclaimapproved").prop('checked'))
                data = data + '&approve=1';
        }
        if (page.query.flag == '2') {
            data = 'MethodName=UpdateInlineJob&JobId=' + JobId + '&flag=12&comments=' + $('#txtestimatcmt').val().trim();
            if ($("#chkjobestimatapproved").prop('checked'))
                data = data + '&approved=1';
        }
        if (page.query.flag == '3') {
            data = 'MethodName=UpdateInlineJob&JobId=' + JobId + '&flag=13&referral=' + $('#drdjoboprereferal').val();
            if ($("#chkjobrefpaid").prop('checked'))
                data = data + '&paid=1';
        }
        if (page.query.flag == '4') {

            data = 'MethodName=UpdateInlineJob&JobId=' + JobId + '&flag=14&status_id=' + $('#drdjobholdtype').val();
            if ($("#chkjobexpires").prop('checked')) {
                if (check_itemsvalidate('#divjobexpdate input')) {
                    data = data + '&expires=1';
                    data = data + '&date=' + $('#txtjobexpires').val();
                }
            }
        }
        if (page.query.flag == '5') {

            data = 'MethodName=UpdateInlineJob&JobId=' + JobId + '&flag=16&jobtypeid=' + $('#drdjobtype').val();
            data = data + '&jobtypenote=' + $('#txtjobtypenote').val().trim();
        }
        if (page.query.flag == '6') {

            data = 'MethodName=UpdateInlineJob&JobId=' + JobId + '&flag=17&warranty=' + $('#drdjobwarranty').val();
            if ($("#chkjobwarrantyprocess").prop('checked'))
                data = data + '&processed=' + $("#chkjobwarrantyprocess").prop('value');

        }
        if (page.query.flag == '7') {
            data = 'MethodName=UpdateInlineJob&JobId=' + JobId + "&flag=" + page.query.flag + "&jurisdictionid=" + $("#drdjobjuridiction2").val();
        }

        if (page.query.flag == '8') {
            var ans = check_itemsvalidate('#txtjobpermit2');
            if (ans) {
                data = 'MethodName=UpdateInlineJob&JobId=' + JobId + "&flag=" + page.query.flag + "&permit_number=" + $("#txtjobpermit2").val();
            } else {
                return false;
            }
        }
        if (page.query.flag == '9') {
            data = 'MethodName=UpdateInlineJob&JobId=' + JobId + "&flag=4&user_id=" + $("#drdjobcanvaser2").val();

        }


        $.when($.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data) {

            },
            error: function(jqxhr, textStatus, errorMessage) {

            }

        })).then(function() {
            if (page.query.flag == '1')
                navigator.notification.alert(
                    "Job insurance information modified", alertDismissed, "Successful", "Done"
                );
            mainView.loadPage("jobdetails.html?JId=" + JobId);
            if (page.query.flag == '2')
                navigator.notification.alert(
                    "Job estimate information modified", alertDismissed, "Successful", "Done"
                );
            if (page.query.flag == '3')
                navigator.notification.alert(
                    "Job referral information modified", alertDismissed, "Successful", "Done"
                );
            if (page.query.flag == '4')
                navigator.notification.alert(
                    "Job status hold information modified", alertDismissed, "Successful", "Done"
                );
            if (page.query.flag == '5')
                navigator.notification.alert(
                    "Job type information modified", alertDismissed, "Successful", "Done"
                );
            if (page.query.flag == '6')
                navigator.notification.alert(
                    "Job Warranty information modified", alertDismissed, "Successful", "Done"
                );

        });

    });
    $('#btnremovejobexpire').on("click", function() {
        var JobId = page.query.JId;
        if (confirm('Are you sure?')) {
            var data = 'MethodName=UpdateInlineJob&JobId=' + JobId + '&flag=15';
            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: "POST",
                data: data,
                cache: false,
                success: function(data) {
                    mainView.loadPage("jobdetails.html?JId=" + JobId);
                },
                error: function(jqxhr, textStatus, errorMessage) {

                }

            })

        }

    });


    $('#btnCanceljoboperation').on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + JobId);
    });


});

myApp.onPageInit('job-history', function(page) {

    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var JobId = page.query.JId;
    Bindjobhistorylist();
    //BindCustomersJobForMap('MethodName=GetCustomersJobForMap&JobID=' + JobId);
    $('#btnbackjobhistory').on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + JobId);
    });

    //function BindCustomersJobForMap(data) {
    //    var $ = jQuery.noConflict();

    //    $.ajax({
    //        url: "https://xactbid.pocketofficepro.com/workflowservice.php",
    //        type: 'POST',
    //        data: data,
    //        cache: false,
    //        success: function (data, textStatus, jqxhr) {
    //            var data = $.parseJSON(data);
    //            var map = $('#dvMap');
    //            document.getElementById("dvMap").innerHTML = "";
    //            if (data.status == "1") {
    //                if (data.mapContent.length > 0) {
    //                  //  for (var i = 0; i < data.mapContent.length; i++) {

    //                        var data1 = data.mapContent[i];

    //                        var contentHtml = "";
    //                        contentHtml = "<div><ul><li>Job #: <a title=Go to job target=main href=https://xactbid.pocketofficepro.com/jobs.php?id=" + data1.job_id + ">" + data1.job_number + "</a></li>";
    //                        contentHtml = contentHtml + "<li>Salesman: <a title=View User target=mainhref=https://xactbid.pocketofficepro.com/users.php?id=" + data1.user_id + ">" + data1.fname + " " + data1.lname + "</a></li>";
    //                        contentHtml = contentHtml + "<div><ul><li>Customer: <a title=Go to job target=mainhref=https://xactbid.pocketofficepro.com/customers.php?id=" + data1.customer_id + ">" + data1.fname + " " + data1.lname + "</a></li>";
    //                        contentHtml = contentHtml + "<br />" + data1.address + ", " + data1.city + ", " + data1.state + " " + data1.zip + "";
    //                        contentHtml = contentHtml + "<li>DOB: " + data1.timestamp + "</li><li>" + data1.distance + " miles</li></ul></div>";
    //                        data1.cross_street = contentHtml;
    //                        map.jHERE('marker',[33.8329373, 117.9649283], {
    //                            icon: '../../icons/map/nearby-marker.png',
    //                            anchor: { x: 12, y: 12 },
    //                            click: function (event) {
    //                                map.jHERE('bubble', [33.8329373, 117.9649283], {
    //                                    content: '123'
    //                                });
    //                            }
    //                        });
    //                   // }
    //                    return false;
    //                }
    //            }
    //            else {
    //                alert(data.message);
    //                return false;
    //            }
    //        },
    //        error: function (jqxhr, textStatus, errorMessage) {
    //            alert(errorMessage);
    //        }
    //    })
    //    return false;
    //}
    function Bindjobhistorylist() {

        var JobId = page.query.JId;
        var data = 'MethodName=Bindjobhistorylist&JobId=' + JobId;
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data) {
                var data = $.parseJSON(data);
                $('#tbljobhistory').empty();
                $('#tbljobhistory').append(data.tblhistory);
                $('.lnkuserdetails').on("click", function() {
                    var userid = $(this).attr('data-userid');
                    mainView.loadPage("get_user.html?UserID=" + userid);
                });

            },
            error: function(jqxhr, textStatus, errorMessage) {
                Bindjobhistorylist();
            }
        });
    }
});


function GetJobSection(PageName) {
    var $ = jQuery.noConflict();

    if (PageName == 'Details') {

        mainView.loadPage("jobdetails.html?JId=" + $("#hdnJobid").val());

    } else if (PageName == 'Journals') {
        mainView.loadPage("jobjournals.html?JId=" + $("#hdnJobid").val());
    } else if (PageName == 'Subscribers') {
        mainView.loadPage("jobsubscribers.html?JId=" + $("#hdnJobid").val());
    } else if (PageName == 'Uploads') {
        mainView.loadPage("jobuploads.html?JId=" + $("#hdnJobid").val());
    } else if (PageName == 'Stages') {
        mainView.loadPage("jobstages.html?JId=" + $("#hdnJobid").val());
    }else if (PageName == 'Contacts') {
        mainView.loadPage("jobcontacts.html?JId=" + $("#hdnJobid").val());
    }else if (PageName == 'Invoice') {
        mainView.loadPage("job_invoice.html?JId=" + $("#hdnJobid").val());
    }
}

myApp.onPageInit('get-user', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var UserID = page.query.UserID;
    if (UserID != null && UserID != "" && UserID != "undefined") {
        if (Checkaccess('view_users')) {
            BindUserDetails('MethodName=GetDetailsForUser&UserID=' + UserID);
        }
    } else {
        mainView.loadPage("users.html");
    }

    function BindUserDetails(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $("#divUserDetail").css("display", "block");
                $('#divUserDetail').empty();
                var items = [];
                if (data.userArray.length > 0) {
                    items.push('<div><table class="table"><tr><th>Name</th><th>DBA</th><th>DOB</th></tr>');
                    for (var i = 0; i < data.userArray.length; i++) {
                        if (data.userArray[i].is_active == "1") {
                            if (data.userArray[i].is_deleted == "1") {
                                items.push('<tr><td style="text-decoration: line-through;">' + data.userArray[i].lname + ', ' + data.userArray[i].fname + '</td><td>' + data.userArray[i].dba + '</td><td> ' + data.userArray[i].reg_date + '</td></tr>');
                            } else {
                                items.push('<tr><td>' + data.userArray[i].lname + ', ' + data.userArray[i].fname + '</td><td>' + data.userArray[i].dba + '</td><td> ' + data.userArray[i].reg_date + '</td></tr>');
                            }
                        } else {
                            items.push('<tr><td>' + data.userArray[i].lname + ', ' + data.userArray[i].fname + '&nbsp - &nbsp; <span style="color:red; font-weight:bold;">INACTIVE</span></td><td>' + data.userArray[i].dba + '</td><td> ' + data.userArray[i].reg_date + '</td></tr>');
                        }
                        if (data.userArray[i].is_deleted != "1" && Checkaccess('edit_users')) {
                            items.push('<tr><td colspan="4" style="text-align: right;"><a id="alinkEditUser" href="edit_user.html?UserID=' + data.userArray[i].user_id + '" style="text-align: right; font-weight: bold;">Edit User&nbsp;|&nbsp;</a>&nbsp;<a class="alinkSendCredential" id="alinkSendCredential_' + data.userArray[i].user_id + '" href="javascript:;"  style="text-align: right; font-weight: bold;">Send User Credentials</a>&nbsp;|&nbsp;&nbsp;<a id="alinkAccessHistory" href="get_useraccess.html?UserID=' + data.userArray[i].user_id + '" style="text-align: right; font-weight: bold;">Access History</a>&nbsp;|&nbsp;&nbsp;<a id="alinkUserActivity" href="get_useractivity.html?UserID=' + data.userArray[i].user_id + '" style="text-align: right; font-weight: bold;">Activity</a>&nbsp;|&nbsp;&nbsp;<a id="alinkBrowseHistory" href="get_user_browsing_history.html?UserID=' + data.userArray[i].user_id + '" style="text-align: right; font-weight: bold;">Recent Browsing History</a></td></tr>');
                            items.push('<tr><td colspan="4"><label id="lblSendUserCredentials" style="float:right; font-weight:bold; color:red; display:none;"></td></tr></table></div>');
                        }

                    }

                    //items.push('<div style="padding-top:20px;"><table class="table"><tr><th colspan="2">User Profile</th></tr>');

                    for (var i = 0; i < data.userArray.length; i++) {
                        if (data.userArray[i].is_deleted != "1") {
                            items.push('<div style="padding-top:20px;"><table class="table"><tr><th colspan="2">User Profile</th></tr>');
                            items.push('<tr><td class="left_listit">Username:</td><td>' + data.userArray[i].username + '</td></tr>');
                            items.push('<tr><td class="left_listit">Access Level:</td><td>' + data.userArray[i].level + '</td></tr>');
                            if (data.userArray[i].title != null) {
                                items.push('<tr><td class="left_listit">Office:</td><td>' + data.userArray[i].title + '</td></tr>');
                            } else {
                                items.push('<tr><td class="left_listit">Office:</td><td>&nbsp;</td></tr>');
                            }
                            items.push('<tr><td class="left_listit">DOB:</td><td>' + data.userArray[i].reg_date + '</td></tr>');
                            items.push('<tr><td class="left_listit">DBA:</td><td>' + data.userArray[i].dba + '</td></tr>');
                            items.push('<tr><td class="left_listit">Email:</td><td>' + data.userArray[i].email + '</td></tr>');
                            items.push('<tr><td class="left_listit">Phone:</td><td>' + data.userArray[i].formatPhn + '</td></tr>');
                            if (data.taskArray.length > 0) {
                                var strTaskType = '';
                                for (var i = 0; i < data.taskArray.length; i++) {
                                    strTaskType = data.taskArray[i].task + ', ';
                                    //items.push('<tr><td>Tasks Assigned:</td><td>' + data.taskArray[i].task + '</td></tr>');
                                }
                                strTaskType += strTaskType;
                                items.push('<tr><td class="left_listit">Tasks Assigned:</td><td>' + rtrim(strTaskType, ',') + '</td></tr>');
                            } else { items.push('<tr><td class="left_listit">Tasks Assigned:</td><td>None Assigned</td></tr>'); }
                            items.push('<tr><td class="left_listit">Notes:</td><td>' + data.userArray[i].notes + '</td></tr>');

                            if (data.accessArray.length > 0) {
                                items.push("<tr><td class='left_listit'>Message:</td><td><input style='margin-top:0px !important;' type=button value=Compose class='btnUserCompose greybtn_comn'  id=btnUserCompose onclick=ShowComposeDive(" + data.userArray[i].user_id + ",'" + data.userArray[i].fname + "','" + data.userArray[i].lname + "');></a></td></tr>");
                                //items.push("<tr><td>Message:</td><td><input type=button value=Compose class=btnUserCompose id=btnUserCompose onclick=ShowComposeDive(" + data.userArray[i].user_id + ");></a></td></tr>");
                                $('#lblComposeUserName').text(data.userArray[i].fname + ' ' + data.userArray[i].lname);
                            }
                            items.push('</table></div>');
                        } else {
                            items.push('<div style="padding-top:20px;"><table class=""><tr><td colspan="2">THIS USER HAS BEEN DELETED<td></tr>');
                            items.push('<tr><td colspan="2"><a class="aLinkRestoreUser" id="' + data.userArray[i].user_id + '"  data-Id =' + data.userArray[i].user_id + ' style="font-weight:bold;">Restore User</a><td></tr>');
                            items.push('</table></div>');
                        }
                    }
                    //items.push('</table></div>');

                } else {
                    navigator.notification.alert(
                        "User details not found!", alertDismissed, "Unsuccessful", "Done"
                    );
                }
                $('#divUserDetail').append(items.join(''));

                $(".alinkSendCredential").on("click", function() {
                    var splits_id = this.id.split('_');
                    var sendCredentialUserId = splits_id[1];
                    ResponseSendUserCredential('MethodName=SendUserCredentials&UserID=' + sendCredentialUserId);
                    return false;
                });

                $(".aLinkRestoreUser").on("click", function() {
                    if (confirm("Are you sure?")) {
                        var userID = $(this).attr('data-Id');

                        restoreUser('MethodName=RestoreUser&UserID=' + userID);
                        return false;
                    } else {
                        return false;
                    }
                });

                //$("#aEditDocument").on("click",function () {
                //    var aDocumentID = page.query.DocumentID;
                //    $("#divDocument").css("display", "none");
                //    $("#divEditDocument").css("display", "block");
                //    BindDocumentDetailsForEdit('MethodName=GetDetailsForDocuments&DocumentID=' + aDocumentID);
                //    return false;
                //});

                return false;
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindUserDetails(data);
            }
        })
    }

    function restoreUser(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);
                if (data.status == "1") {
                    //alert(data.message);
                    BindUserDetails('MethodName=GetDetailsForUser&UserID=' + UserID);
                    return false;
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#aBackUser').on("click", function() {
        mainView.loadPage("users.html");
    });

    function SendUserComposeMail(data) {

        var $ = jQuery.noConflict();

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    $('#divUserCompose').css("display", "none");
                    return false;
                } else {
                    //alert(data.message);
                    $("#lblSendMailMsg").css('display', 'block');
                    $("#lblSendMailMsg").text(data.message);
                    $('#divUserCompose').css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }


    function ResponseSendUserCredential(data) {

        var $ = jQuery.noConflict();
        $body = $("body");
        $body.addClass("loading");
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#lblSendUserCredentials").html(data.message);
                    $('#lblSendUserCredentials').css("display", "none");
                    $body.removeClass("loading");
                    return false;
                } else {
                    $("#lblSendUserCredentials").html(data.message);
                    $("#lblSendUserCredentials").css('display', 'block');
                    return false;
                }
                $body.removeClass("loading");
                return false;

            },
            error: function(jqxhr, textStatus, errorMessage) {
                $body.removeClass("loading");
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

});

function SendUserCredentials(id) {
    ResponseSendUserCredential('MethodName=SendUserCredentials&UserID=' + id);
    return false;
}


myApp.onPageInit('documentGroup', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    //new multiple_file_uploader
    //({
    //    form_id: "fileUpload",
    //    autoSubmit: true,
    //    server_url: "uploader.php" // PHP file for uploading the browsed files
    //});


    GetDocumentGroupList('MethodName=GetDocumentGroupListForEdit');

    $("#btnAddDocGroup").on("click", function() {

        var ans = check_itemsvalidate('#divAddGroup input');
        if (ans) {

            AddGroup('MethodName=AddDocumentGroup&title=' + $("#txtAddGroupTitle").val());
            $("#txtAddGroupTitle").val("");
            return false;
        } else {
            return false;
        }
        return false;
    });

    function AddGroup(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divmodifyDocGroup").css("display", "block");

                    GetDocumentGroupList('MethodName=GetDocumentGroupListForEdit');
                    //$('#lblModifyDoc').html(obj.message);
                    //$("#lblModifyDoc").css("display", "block");
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyDoc').html(obj.message);
                    //$("#lblModifyDoc").css("display", "block");
                    $("#divmodifyDocGroup").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    function GetDocumentGroupList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $('#divEditDocGroup').empty();
                var items = [];
                if (data.documentGroups.length > 0) {
                    items.push("<div><table class='table'><tr><td colspan='3'><b>Current Groups</b></td></tr>");
                    for (var i = 0; i < data.documentGroups.length; i++) {

                        items.push('<tr><td class="acenter"><a class="aDeleteGroup" href="javascript:;" id="aDeleteGroup_' + data.documentGroups[i].document_group_id + '"><i class="icon-remove"></i></a></td><td><input class="form_input form-control validation validate[required[Title cannot be empty]]" type="text" value="' + data.documentGroups[i].label + '" id="docGroup_' + data.documentGroups[i].document_group_id + '" /></td><td class="acenter"><a class="aEditGroup" href="javascript:;" id="aEditGroup_' + data.documentGroups[i].document_group_id + '"><i class="icon-pencil"></i></a></td></tr>');
                    }
                    items.push('</table>');
                } else {

                    //$('#lblModifyDoc').html(data.message);
                    //$("#lblModifyDoc").css("display", "block");
                    $("#divmodifyDocGroup").css("display", "block");

                    return false;
                }

                $('#divEditDocGroup').append(items.join(''));
                $(".aEditGroup").on("click", function() {

                    var splits_id = this.id.split('_');
                    var editDocGroupID = splits_id[1];

                    var ans = check_itemsvalidate('#divEditDocGroup input');
                    if (ans) {
                        EditDocumentGroup('MethodName=EditDocumentGroupByID&DocumentGroup=' + $("#docGroup_" + editDocGroupID).val() + '&DocumentGroupID=' + editDocGroupID);
                        return false;
                    } else {
                        return false;
                    }
                });
                $(".aDeleteGroup").on("click", function() {
                    if (confirm('Are you sure?')) {
                        var splits_id = this.id.split('_');
                        var deleteDocGroupID = splits_id[1];
                        DeleteDocumentGroup('MethodName=DeleteDocumentGroupByID&DocumentGroupID=' + deleteDocGroupID);
                        return false;
                    } else { return false; }
                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                GetDocumentGroupList(data);
            }
        })
    }

    function EditDocumentGroup(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divmodifyDocGroup").css("display", "block");

                    GetDocumentGroupList('MethodName=GetDocumentGroupListForEdit');
                    //$('#lblModifyDoc').html(data.message);
                    //$("#lblModifyDoc").css("display", "block");
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyDoc').html(data.message);
                    //$("#lblModifyDoc").css("display", "block");
                    $("#divmodifyDocGroup").css("display", "block");

                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function DeleteDocumentGroup(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divmodifyDocGroup").css("display", "block");

                    GetDocumentGroupList('MethodName=GetDocumentGroupListForEdit');
                    //$('#lblModifyDoc').html(data.message);
                    //$("#lblModifyDoc").css("display", "block");
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyDoc').html(data.message);
                    //$("#lblModifyDoc").css("display", "block");
                    $("#divmodifyDocGroup").css("display", "block");

                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnAddCancel").on("click", function() {
        jQuery(".formError").remove();
        mainView.loadPage("documents.html");
    });
    $("#lnkdocGroupback").on("click", function() {
        mainView.loadPage("documents.html");
    });

    $("#btnUploadGroup").on("click", function() {
        var ans = check_itemsvalidate('#divUploadGroup input');

        if (ans) {
            UploadDocumentForGroup('MethodName=UploadDocumentForGroup&Docfile=' + $("#txtUploadGroupTitle").val() + '&Title=' + $("#txtUploadGroupTitle").val() + '&Group=' + $("#ddlUploadGroup").val() + '&Stage=' + $("#ddlUploadStage").val() + '&Description=' + $("#txtDescription").val());
            return false;
        } else { return false; }
        return false;
    });

    function UploadDocumentForGroup(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    mainView.loadPage("documents.html");
                } else {
                    //alert(data.message);
                    $('#lblUploadDocGroup').html(data.message);
                    $("#lblUploadDocGroup").css("display", "block");
                    $("#divmodifyDocGroup").css("display", "none");
                    $
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }



});

myApp.onPageInit('messaging', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var searchtext = $("#txt_Search").val();
    var searchtype = $("#btnInbox").val();

    var MessageID = page.query.MessageID

    if (MessageID != null && MessageID != "" && MessageID != "undefined") {
        $('#divMessageDetail').css("display", "block");
        BindMessageDetails('MethodName=GetAllMessageDetails&MessageID=' + MessageID + '&MessageType=inbox');
    } else {
        //BindMessagingList('MethodName=GetMessagingList&SearchText=' + searchtext + '&limit=10&offset=0');
        BindMessagingList('MethodName=GetMessagingList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=100&offset=0');
        BindUserGroupAndUser('MethodName=GetlistofUsersAndUserGroupForComposeMsg');
    }


    function BindMessagingList(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                data = $.parseJSON(data);
                $('#divBindAllMessages').empty();
                $('#divBindAllMessages').attr("style", "display:block");
                $('#divMainHeaderForMessaging').html("");
                $('#divMainHeaderForMessaging').html("Messaging");
                var currentOffset = parseInt(data.currentofset);
                if (data.messageList.length > 0) {
                    data.totalrecord = data.messageList.length;
                    currentOffset = parseInt(currentOffset) + parseInt(data.messageList.length);

                    var items = [];
                    items.push('<table class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>From</th></tr>');

                    for (var i = 0; i < data.messageList.length; i++) {

                        if (data.messageList[i].sentTime == null) {
                            items.push('<tr><td colspan="2"><img src="images/icons/bubble_16.png" />&nbsp;&nbsp;<a class="aMsgListing" id=aMsgListing_' + data.messageList[i].message_id + ' href="javascript:;">' + data.messageList[i].subject + '</a></td><td>' + data.messageList[i].timestamp + '</td><td>' + data.messageList[i].lname + ', ' + data.messageList[i].fname + '</td></tr>');
                        } else {
                            items.push('<tr><td colspan="2"><img src="images/icons/bubble_16_grey.png" />&nbsp;<a class="aMsgListing" id=aMsgListing_' + data.messageList[i].message_id + ' href="javascript:;">' + data.messageList[i].subject + '</a></td><td>' + data.messageList[i].timestamp + '</td><td>' + data.messageList[i].lname + ', ' + data.messageList[i].fname + '</td></tr>');
                        }
                    }
                    items.push('</table>');
                    document.getElementById("hdntotalrecord").value = data.totalrecord;

                    if (data.totalrecord > 100) {
                        items.push('<div class="next_privcenter"><a id="btnprev" class="button_small"  style="visibility:hidden;cursor:pointer;"> </a>&nbsp;&nbsp;');
                        items.push('<label>Showing: ' + (parseInt(data.currentofset) + 1) + " - " + currentOffset + " of " + parseInt(data.totalrecord) + '</label>');
                        items.push('&nbsp;&nbsp;<a id="btnnext" style="cursor:pointer;" class="button_small" > </a></div>');
                    } else {
                        items.push('<div class="next_privcenter"><label>Showing: ' + (parseInt(data.currentofset) + 1) + " - " + currentOffset + " of " + parseInt(data.totalrecord) + '</label></div>');
                    }
                    $('#divBindAllMessages').append(items.join(''));

                    if ($("#txt_Search").val() != '') {
                        $('#lblMessage').html('Searching ' + $('#txt_Search').val() + ' - ' + data.messageList.length + ' result(s) found');
                        $('#lblMessage').css("display", "block");
                        $('#divBack').css("display", "block");
                        $('#divDetailBack').css("display", "none");
                    } else {
                        $('#lblMessage').html('');
                        $('#lblMessage').css("display", "none");
                        $('#divBack').css("display", "none");
                        $('#divDetailBack').css("display", "none");
                    }

                    $('#btnnext').on("click", function() {
                        callnext10();
                    });
                    $('#btnprev').on("click", function() {
                        callPrev10();
                    });

                    $('.aMsgListing').on("click", function() {
                        var splits_id = this.id.split('_');
                        var InboxMsgId = splits_id[1];
                        $('#divMsgCompose').css("display", "none");
                        $('#divMessagingList').css("display", "none");
                        $('#divMessageDetail').css("display", "block");
                        BindMessageDetails('MethodName=GetAllMessageDetails&MessageID=' + InboxMsgId + '&MessageType=inbox');
                        return false;
                    });

                    if (data.totalrecord > 100) {
                        if (parseInt(data.totalrecord) == parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'hidden';
                            document.getElementById('btnprev').style.visibility = 'visible';
                        } else if (parseInt(data.totalrecord) < parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'visible';
                            document.getElementById('btnprev').style.visibility = 'Hidden';
                        } else {
                            if (parseInt(data.currentofset) == 0) {
                                document.getElementById('btnprev').style.visibility = 'Hidden';
                            } else {
                                document.getElementById('btnnext').style.visibility = 'visible';
                                document.getElementById('btnprev').style.visibility = 'visible';
                            }
                        }
                    }
                } else {
                    var items = [];
                    if ($("#txt_Search").val() != '') {
                        $('#lblMessage').html('');
                        //$('#lblMessage').html('Searching ' + $('#txt_Search').val() + ' - ' + data.messageList.length + ' result(s) found');
                        //$('#lblMessage').css("display", "block");
                        items.push('<table style="width:100%;" class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>From</th></tr>');
                        items.push('<tr><td colspan="4"><label>' + 'Searching ' + $('#txt_Search').val() + ' - ' + data.messageList.length + ' result(s) found' + '</label></td></tr>');
                        items.push('</table>');
                        $('#divBindAllMessages').append(items.join(''));
                        $('#divBack').css("display", "block");
                        $('#divDetailBack').css("display", "none");
                    } else {
                        $('#lblMessage').html('');
                        $('#lblMessage').css("display", "none");
                        $('#divBack').css("display", "none");
                        $('#divBindAllMessages').empty();
                        $('#divDetailBack').css("display", "none");

                        items.push('<table style="width:100%;" class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>From</th></tr>');
                        items.push('<tr><td colspan="4"><label>No Messages Found</label></td></tr>');
                        items.push('</table>');
                        $('#divBindAllMessages').append(items.join(''));

                    }
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindMessagingList(data);
            }
        })
    }

    function BindSentMessagingList(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                data = $.parseJSON(data);
                $('#divBindAllMessages').empty();
                $('#divBindAllMessages').attr("style", "display:block");
                var currentOffset = parseInt(data.currentofset);
                if (data.messageList.length > 0) {
                    data.totalrecord = data.messageList.length;
                    currentOffset = parseInt(currentOffset) + parseInt(data.messageList.length);

                    var items = [];
                    items.push('<table class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>To</th></tr>');

                    for (var i = 0; i < data.messageList.length; i++) {

                        if (data.messageList[i].sentTime == null) {

                            if (data.messageList[i].delete == 1)
                                items.push('<tr><td colspan="2"><img src="images/icons/right_16.png" />&nbsp;&nbsp;<a class="aSentMsgListing" id=aSentMsgListing_' + data.messageList[i].message_id + ' href="javascript:;"><s><b>' + data.messageList[i].subject + '</s></b> - <span style="font-size:11px;">Unread</span></a></td><td>' + data.messageList[i].timestamp + '</td><td>' + data.messageList[i].lname + ', ' + data.messageList[i].fname + '</td></tr>');
                            else
                                items.push('<tr><td colspan="2"><img src="images/icons/right_16.png" />&nbsp;&nbsp;<a class="aSentMsgListing" id=aSentMsgListing_' + data.messageList[i].message_id + ' href="javascript:;"><b>' + data.messageList[i].subject + '</b> - <span style="font-size:11px;">Unread</span></a></td><td>' + data.messageList[i].timestamp + '</td><td>' + data.messageList[i].lname + ', ' + data.messageList[i].fname + '</td></tr>');
                        } else {
                            if (data.messageList[i].delete == 1)
                                items.push('<tr><td colspan="2"><img src="images/icons/right_16.png" />&nbsp;<a class="aSentMsgListing" id=aSentMsgListing_' + data.messageList[i].message_id + ' href="javascript:;"><s><b>' + data.messageList[i].subject + '</s></b> - <span style="font-size:11px;">Read on ' + data.messageList[i].sentTime + '</span></a></td><td>' + data.messageList[i].timestamp + '</td><td>' + data.messageList[i].lname + ', ' + data.messageList[i].fname + '</td></tr>');
                            else
                                items.push('<tr><td colspan="2"><img src="images/icons/right_16.png" />&nbsp;<a class="aSentMsgListing" id=aSentMsgListing_' + data.messageList[i].message_id + ' href="javascript:;"><b>' + data.messageList[i].subject + '</b> - <span style="font-size:11px;">Read on ' + data.messageList[i].sentTime + '</span></a></td><td>' + data.messageList[i].timestamp + '</td><td>' + data.messageList[i].lname + ', ' + data.messageList[i].fname + '</td></tr>');
                        }
                    }
                    items.push('</table>');
                    document.getElementById("hdntotalrecord").value = data.totalrecord;
                    if (data.totalrecord > 100) {
                        items.push('<a id="btnprev" class="button_small"  style="visibility:hidden;cursor:pointer;"> </a>&nbsp;&nbsp;');
                        items.push('<label>Showing: ' + (parseInt(data.currentofset) + 1) + " - " + currentOffset + " of " + parseInt(data.totalrecord) + '</label>');
                        items.push('&nbsp;&nbsp;<a id="btnnext" style="cursor:pointer;" class="button_small" > </a>');
                    } else {
                        items.push('<label>Showing: ' + (parseInt(data.currentofset) + 1) + " - " + currentOffset + " of " + parseInt(data.totalrecord) + '</label>');
                    }
                    $('#divBindAllMessages').append(items.join(''));

                    if ($("#txt_Search").val() != '') {
                        $('#lblMessage').html('Searching ' + $('#txt_Search').val() + ' - ' + data.messageList.length + ' result(s) found');
                        $('#lblMessage').css("display", "block");
                        $('#divBack').css("display", "block");
                        $('#divDetailBack').css("display", "none");
                    } else {
                        $('#lblMessage').html('');
                        $('#lblMessage').css("display", "none");
                        $('#divBack').css("display", "none");
                        $('#divDetailBack').css("display", "none");
                    }

                    $('#btnnext').on("click", function() {
                        callnext10();
                    });
                    $('#btnprev').on("click", function() {
                        callPrev10();
                    });

                    $('.aSentMsgListing').on("click", function() {
                        var splits_id = this.id.split('_');
                        var SentMsgId = splits_id[1];

                        $('#divMsgCompose').css("display", "none");
                        $('#divMessagingList').css("display", "none");
                        $('#divMessageDetail').css("display", "block");
                        BindSentMessageDetails('MethodName=GetAllMessageDetails&MessageID=' + SentMsgId + '&MessageType=sent');
                        return false;
                    });

                    if (data.totalrecord > 100) {
                        if (parseInt(data.totalrecord) == parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'hidden';
                            document.getElementById('btnprev').style.visibility = 'visible';
                        } else if (parseInt(data.totalrecord) < parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'visible';
                            document.getElementById('btnprev').style.visibility = 'Hidden';
                        } else {
                            if (parseInt(data.currentofset) == 0) {
                                document.getElementById('btnprev').style.visibility = 'Hidden';
                            } else {
                                document.getElementById('btnnext').style.visibility = 'visible';
                                document.getElementById('btnprev').style.visibility = 'visible';
                            }
                        }
                    }
                } else {
                    var items = [];
                    if ($("#txt_Search").val() != '') {
                        $('#lblMessage').html('');
                        //$('#lblMessage').html('Searching ' + $('#txt_Search').val() + ' - ' + data.messageList.length + ' result(s) found');
                        //$('#lblMessage').css("display", "block");
                        items.push('<table style="width:100%;" class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>To</th></tr>');
                        items.push('<tr><td colspan="4"><label>' + 'Searching ' + $('#txt_Search').val() + ' - ' + data.messageList.length + ' result(s) found' + '</label></td></tr>');
                        items.push('</table>');
                        $('#divBindAllMessages').append(items.join(''));
                        $('#divBack').css("display", "block");
                        $('#divDetailBack').css("display", "none");
                    } else {
                        $('#lblMessage').html('');
                        $('#lblMessage').css("display", "none");
                        $('#divBack').css("display", "none");
                        $('#divBindAllMessages').empty();
                        $('#divDetailBack').css("display", "none");

                        items.push('<table style="width:100%;" class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>To</th></tr>');
                        items.push('<tr><td colspan="4"><label>No Sent Messages Found</label></td></tr>');
                        items.push('</table>');
                        $('#divBindAllMessages').append(items.join(''));
                    }
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindSentMessagingList(data);
            }
        })
    }

    function BindTrashMessagingList(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                data = $.parseJSON(data);
                $('#divBindAllMessages').empty();
                $('#divBindAllMessages').attr("style", "display:block");

                var currentOffset = parseInt(data.currentofset);
                if (data.messageList.length > 0) {
                    data.totalrecord = data.messageList.length;
                    currentOffset = parseInt(currentOffset) + parseInt(data.messageList.length);
                    var items = [];
                    items.push('<table class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>To</th></tr>');

                    for (var i = 0; i < data.messageList.length; i++) {

                        if (data.messageList[i].sentTime == null) {
                            items.push('<tr><td colspan="2"><img src="images/icons/bubble_16.png" />&nbsp;<a class="aTrashMsgListing" id=aTrashMsgListing_' + data.messageList[i].message_id + ' href="javascript:;">' + data.messageList[i].subject + '</a></td><td>' + data.messageList[i].timestamp + '</td><td>' + data.messageList[i].lname + ', ' + data.messageList[i].fname + '</td></tr>');
                        } else {
                            items.push('<tr><td colspan="2"><img src="images/icons/bubble_16_grey.png" />&nbsp;<a class="aTrashMsgListing" id=aTrashMsgListing_' + data.messageList[i].message_id + ' href="javascript:;">' + data.messageList[i].subject + '</a></td><td>' + data.messageList[i].timestamp + '</td><td>' + data.messageList[i].lname + ', ' + data.messageList[i].fname + '</td></tr>');
                        }
                    }
                    items.push('</table>');
                    document.getElementById("hdntotalrecord").value = data.totalrecord;

                    if (data.totalrecord > 100) {
                        items.push('<a id="btnprev" class="button_small"  style="visibility:hidden;cursor:pointer;"> </a>&nbsp;&nbsp;');
                        items.push('<label>Showing: ' + (parseInt(data.currentofset) + 1) + " - " + currentOffset + " of " + parseInt(data.totalrecord) + '</label>');
                        items.push('&nbsp;&nbsp;<a id="btnnext" style="cursor:pointer;" class="button_small" > </a>');
                    } else {
                        items.push('<label>Showing: ' + (parseInt(data.currentofset) + 1) + " - " + currentOffset + " of " + parseInt(data.totalrecord) + '</label>');
                    }
                    $('#divBindAllMessages').append(items.join(''));

                    if ($("#txt_Search").val() != '') {
                        $('#lblMessage').html('Searching ' + $('#txt_Search').val() + ' - ' + data.messageList.length + ' result(s) found');
                        $('#lblMessage').css("display", "block");
                        $('#divBack').css("display", "block");
                        $('#divDetailBack').css("display", "none");
                    } else {
                        $('#lblMessage').html('');
                        $('#lblMessage').css("display", "none");
                        $('#divBack').css("display", "none");
                        $('#divDetailBack').css("display", "none");
                    }

                    $('#btnnext').on("click", function() {
                        callnext10();
                    });
                    $('#btnprev').on("click", function() {
                        callPrev10();
                    });

                    $('.aTrashMsgListing').on("click", function() {
                        var splits_id = this.id.split('_');
                        var TrashMsgId = splits_id[1];
                        $('#divMsgCompose').css("display", "none");
                        $('#divMessagingList').css("display", "none");
                        $('#divMessageDetail').css("display", "block");
                        BindTrashMessageDetails('MethodName=GetAllMessageDetails&MessageID=' + TrashMsgId + '&MessageType=trash');
                        return false;
                    });

                    if (data.totalrecord > 100) {
                        if (parseInt(data.totalrecord) == parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'hidden';
                            document.getElementById('btnprev').style.visibility = 'visible';
                        } else if (parseInt(data.totalrecord) < parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'visible';
                            document.getElementById('btnprev').style.visibility = 'Hidden';
                        } else {
                            if (parseInt(data.currentofset) == 0) {
                                document.getElementById('btnprev').style.visibility = 'Hidden';
                            } else {
                                document.getElementById('btnnext').style.visibility = 'visible';
                                document.getElementById('btnprev').style.visibility = 'visible';
                            }
                        }
                    }
                } else {
                    var items = [];
                    if ($("#txt_Search").val() != '') {
                        $('#lblMessage').html('');
                        //$('#lblMessage').html('Searching ' + $('#txt_Search').val() + ' - ' + data.messageList.length + ' result(s) found');
                        //$('#lblMessage').css("display", "block");
                        items.push('<table style="width:100%;" class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>To</th></tr>');
                        items.push('<tr><td colspan="4"><label>' + 'Searching ' + $('#txt_Search').val() + ' - ' + data.messageList.length + ' result(s) found' + '</label></td></tr>');
                        items.push('</table>');
                        $('#divBindAllMessages').append(items.join(''))
                        $('#divBack').css("display", "block");
                        $('#divDetailBack').css("display", "none");
                    } else {
                        $('#lblMessage').html('');
                        $('#lblMessage').css("display", "none");
                        $('#divBack').css("display", "none");
                        $('#divDetailBack').css("display", "none");
                        $('#divBindAllMessages').empty();

                        items.push('<table style="width:100%;" class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>To</th></tr>');
                        items.push('<tr><td colspan="4"><label>Trash Empty</label></td></tr>');
                        items.push('</table>');
                        $('#divBindAllMessages').append(items.join(''));

                    }
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindTrashMessagingList(data);
            }
        })
    }

    $("#btnInbox").on("click", function() {

        var searchtext = $("#txt_Search").val();
        var searchtype = $("#btnInbox").val();
        $("#txt_Search").val('');
        //BindMessagingList('MethodName=GetMessagingList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=100&offset=0');
        BindMessagingList('MethodName=GetMessagingList&SearchType=' + searchtype + '&SearchText=&limit=100&offset=0');
        return false;
    });
    $("#btnSent").on("click", function() {

        var searchtext = $("#txt_Search").val();
        var searchtype = $("#btnSent").val();
        $("#txt_Search").val('');
        //BindSentMessagingList('MethodName=GetMessagingList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=100&offset=0');
        BindSentMessagingList('MethodName=GetMessagingList&SearchType=' + searchtype + '&SearchText=&limit=100&offset=0');
        return false;
    });
    $("#btnTrash").on("click", function() {

        var searchtext = $("#txt_Search").val();
        var searchtype = $("#btnTrash").val();
        $("#txt_Search").val('');
        //BindTrashMessagingList('MethodName=GetMessagingList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=100&offset=0');
        BindTrashMessagingList('MethodName=GetMessagingList&SearchType=' + searchtype + '&SearchText=&limit=100&offset=0');
        return false;
    });
    $("#btnCompose").on("click", function() {
        $('#divMsgCompose').css("display", "block");
        $('#divMessagingList').css("display", "none");
        $('#divMessageDetail').css("display", "none");
        $('#divMainHeaderForMessaging').html("");
        $('#divMainHeaderForMessaging').html("Compose Message");
        return false;
    });
    $("#btnSrchInbox").on("click", function() {

        var searchtext = $("#txt_Search").val();
        var searchtype = $("#btnInbox").val();
        BindMessagingList('MethodName=GetMessagingList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=100&offset=0');
        return false;
    });
    $("#btnSrchSent").on("click", function() {

        var searchtext = $("#txt_Search").val();
        var searchtype = $("#btnSent").val();
        BindSentMessagingList('MethodName=GetMessagingList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=100&offset=0');
        return false;
    });
    $("#btnSrchTrash").on("click", function() {

        var searchtext = $("#txt_Search").val();
        var searchtype = $("#btnTrash").val();
        BindTrashMessagingList('MethodName=GetMessagingList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=100&offset=0');
        return false;
    });
    $("#divBack").on("click", function() {
        $('#divMsgCompose').css("display", "none");
        $('#divMessagingList').css("display", "block");
        $('#divMessageDetail').css("display", "none");
        BindMessagingList('MethodName=GetMessagingList&SearchType=Inbox&SearchText=&limit=100&offset=0');
        $('#divMainHeaderForMessaging').html("");
        $('#divMainHeaderForMessaging').html("Messaging");
        $('#txt_Search').val('');
    });
    $("#divDetailBack").on("click", function() {
        $('#divMsgCompose').css("display", "none");
        $('#divMessagingList').css("display", "block");
        $('#divMessageDetail').css("display", "none");
        $('#divDetailBack').css("display", "none");
        $('#lblMsgDetail').css("display", "none");
        BindMessagingList('MethodName=GetMessagingList&SearchType=Inbox&SearchText=&limit=100&offset=0');
    });

    function BindUserGroupAndUser(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);

                var items = [];

                //var obj = JSON.stringify(data.users_array);

                $("#ddlUser").append(data.users_array);
                //if (data.users.length > 0) {
                //    for (var i = 0; i < data.users.length; i++) {
                //        $("#ddlUser").append($("<option value='" + data.users[i].user_id + "'>" + data.users[i].select_label + "</option>"));
                //    }

                //}
                if (data.groups.length > 0) {
                    for (var i = 0; i < data.groups.length; i++) {
                        $("#ddlGroup").append($("<option value='" + data.groups[i].usergroup_id + "'>" + data.groups[i].label + "</option>"));
                    }

                }
                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert("Error");
                navigator.notification.alert(
                    "Error", alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        });
    }
    $("#btnMsgSend").on("click", function() {
        var retval = false;
        var ans = check_itemsvalidate('#divMsgCompose input');
        if (ans) {
            SendMailToUser('MethodName=SendMail&User=' + $('#ddlUser').val() + '&UserGroup=' + $('#ddlGroup').val() + '&Subject=' + $('#txtSubject').val() + '&Message=' + $('#txtMessage').val());
            return false;
        } else { return false; }
        return false;
    });
    $("#btnMsgCancel").click(function() {
        jQuery(".formError").remove();
        $("#ddlUser").val('');
        $("#ddlGroup").val('');
        $("#txtSubject").val('');
        $("#txtMessage").val('');

        $('#divMsgCompose').css("display", "none");
        $('#divMessagingList').css("display", "block");
        $('#divMessageDetail').css("display", "none");
        $('#divMainHeaderForMessaging').html("");
        $('#divMainHeaderForMessaging').html("Messaging");
        return false;
    });

    function SendMailToUser(data) {

        var $ = jQuery.noConflict();

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    //alert(data.sendStatus);
                    $('#divMsgCompose').css("display", "none");
                    $('#divMessagingList').css("display", "block");
                    $('#divMessageDetail').css("display", "none");
                    return false;
                } else {
                    //alert(data.message);
                    //alert(data.sendStatus);
                    $("#lblSendMailMsg").css('display', 'block');
                    $("#lblSendMailMsg").text(data.message);
                    $('#divMsgCompose').css("display", "block");
                    $('#divMessagingList').css("display", "none");
                    $('#divMessageDetail').css("display", "none");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        });
    }

    function BindMessageDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $('#divMessageDetail').empty();
                var items = [];

                if (data.InboxArray.length > 0) {
                    items.push('<div><table class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>From</th></tr>');
                    for (var i = 0; i < data.InboxArray.length; i++) {
                        items.push('<tr><td colspan="2"><img src="images/icons/bubble_16_grey.png">&nbsp;' + data.InboxArray[i].subject + '</td><td>' + data.InboxArray[i].timestamp + '</td><td> ' + data.InboxArray[i].lname + ', ' + data.InboxArray[i].fname + '</td></tr>');
                        items.push('<tr><td colspan="4" style="text-align: right;"><input class="btnMarkUnread greybtn_comn" type="button" name="markunread" id="btnMarkUnread_' + data.InboxArray[i].message_id + '" value="Mark Unread" />&nbsp;&nbsp;<input type="button" class="btnMsgDelete redbtn_comn bulebtn_comn" name="btnMsgDelete" id="btnMsgDelete_' + data.InboxArray[i].message_id + '" value="Delete" /></td></tr></table></div>');
                    }

                    items.push('<div style="padding-top:20px;"><table class="table"><tr><th colspan="2">Message</th></tr>');
                    for (var i = 0; i < data.InboxArray.length; i++) {

                        items.push('<tr><td class="left_listit">Subject:</td><td>' + data.InboxArray[i].subject + '</td></tr>');
                        items.push('<tr><td class="left_listit">From:</td><td><a  class="aLinkToUserDetails" id=' + data.InboxArray[i].user_id + '>' + data.InboxArray[i].lname + ', ' + data.InboxArray[i].fname + '</a></td></tr>');
                        items.push('<tr><td class="left_listit">Sent:</td><td>' + data.InboxArray[i].timestamp + '</td></tr>');
                        items.push('<tr><td class="left_listit">Message:</td><td>' + data.InboxArray[i].body + '</td></tr>');
                    }
                    items.push('</table></div>');

                    items.push('<div style="padding-top:20px;"><table class="table"><tr><th colspan="2">Send Reply </th></tr>');
                    for (var i = 0; i < data.InboxArray.length; i++) {

                        items.push('<tr><td class="left_listit">Subject:</td><td><label id="lblRplySubject">RE: ' + data.InboxArray[i].subject + '</label></td></tr>');
                        items.push('<tr><td class="left_listit">To:</td><td><label id="lblRplyUser">' + data.InboxArray[i].lname + ', ' + data.InboxArray[i].fname + '</label><label id="lblRplyUserId" style="visibility: hidden;">' + data.InboxArray[i].user_id + '</label></td></tr>');
                        items.push('<tr><td class="left_listit">Message:</td><td><textarea name="txtreply_body" id="txtreply_body" style="width:100%; font-size:14px;" rows=5 >ORIGINAL MESSAGE: "' + data.InboxArray[i].body.replace("\r\n", "<br>") + '"</textarea></td></tr>');
                    }
                    items.push('<tr><td colspan="4" style="text-align: right;"><input type="button" class="btnMsgReply greybtn_comn" name="btnMsgReply" id="btnMsgReply" value="Reply" /></td></tr></table></div>');
                } else {
                    $('#lblMsgDetail').html(data.message);
                    $('#lblMsgDetail').css("display", "block");
                    return false;
                }
                $('#divMessageDetail').append(items.join(''));

                $(".aLinkToUserDetails").on("click", function() {
                    var splits_id = this.id;
                    mainView.loadPage("get_user.html?UserID=" + splits_id);
                    return false;
                });

                $(".btnMarkUnread").on("click", function() {
                    var splits_id = this.id.split('_');
                    var UnreadMsgId = splits_id[1];

                    $('#divMsgCompose').css("display", "none");
                    $('#divMessagingList').css("display", "block");
                    $('#divMessageDetail').css("display", "none");
                    UpdateMessageStatusAsUnread('MethodName=UpdateMsgStatusAsUnread&UnreadMsgId=' + UnreadMsgId);
                    BindMessagingList('MethodName=GetMessagingList&SearchType=Inbox&SearchText=&limit=100&offset=0');
                    return false;
                });

                $(".btnMsgDelete").on("click", function() {
                    if (confirm('Are you sure?')) {
                        var splits_id = this.id.split('_');
                        var DeleteMsgId = splits_id[1];

                        $('#divMsgCompose').css("display", "none");
                        $('#divMessagingList').css("display", "block");
                        $('#divMessageDetail').css("display", "none");
                        UpdateMessageStatusAsDelete('MethodName=UpdateMsgStatusAsDelete&DeleteMsgId=' + DeleteMsgId);
                        BindMessagingList('MethodName=GetMessagingList&SearchType=Inbox&SearchText=&limit=100&offset=0');
                        return false;
                    } else { return false; }
                });

                $("#btnMsgReply").on("click", function() {

                    SendReplyToUser('MethodName=ReplyMail&User=' + $('#lblRplyUserId').text() + '&UserGroup=&Subject=' + $('#lblRplySubject').text() + '&Message=' + $('textarea#txtreply_body').val());
                    return false;
                });
                $('#divDetailBack').css("display", "block");
                return false;
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindMessageDetails(data);
            }
        })
    }

    function SendReplyToUser(data) {

        var $ = jQuery.noConflict();

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    $('#divMsgCompose').css("display", "none");
                    $('#divMessagingList').css("display", "block");
                    $('#divMessageDetail').css("display", "none");
                    return false;
                } else {
                    //alert(data.message);
                    $("#lblMsgDetail").css('display', 'block');
                    $("#lblMsgDetail").text(data.message);
                    $('#divMsgCompose').css("display", "none");
                    $('#divMessagingList').css("display", "none");
                    $('#divMessageDetail').css("display", "block");
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                SendReplyToUser(data);
            }
        })
    }

    function UpdateMessageStatusAsUnread(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    $('#divMsgCompose').css("display", "none");
                    $('#divMessagingList').css("display", "block");
                    $('#divMessageDetail').css("display", "none");
                } else {
                    //alert(data.message);
                    $("#lblMsgDetail").css('display', 'block');
                    $("#lblMsgDetail").text(data.message);
                    $('#divMsgCompose').css("display", "none");
                    $('#divMessagingList').css("display", "block");
                    $('#divMessageDetail').css("display", "none");
                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    function UpdateMessageStatusAsDelete(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    $('#divMsgCompose').css("display", "none");
                    $('#divMessagingList').css("display", "block");
                    $('#divMessageDetail').css("display", "none");
                } else {
                    //alert(data.message);
                    $("#lblMsgDetail").css('display', 'block');
                    $("#lblMsgDetail").text(data.message);
                    $('#divMsgCompose').css("display", "none");
                    $('#divMessagingList').css("display", "block");
                    $('#divMessageDetail').css("display", "none");
                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    function BindSentMessageDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var strDel = "";
                $('#divMessageDetail').empty();
                var items = [];
                if (data.SentArray.length > 0) {
                    items.push('<div><table class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>To</th></tr>');
                    for (var i = 0; i < data.SentArray.length; i++) {
                        if (data.SentArray[i].delete == "1") {

                            strDel = 'style="text-decoration: line-through;"';
                        }
                        if (data.SentArray[i].SentTime == null) {
                            items.push('<tr><td colspan="2"><img src="images/icons/right_16.png">&nbsp;<b "' + strDel + '">' + data.SentArray[i].subject + '</b> - <span style="font-size:11px;">Unread<span></td><td>' + data.SentArray[i].timestamp + '</td><td> ' + data.SentArray[i].lname + ', ' + data.SentArray[i].fname + '</td></tr>');
                        } else {
                            items.push('<tr><td colspan="2"><img src="images/icons/right_16.png">&nbsp;<b "' + strDel + '">' + data.SentArray[i].subject + '</b> - <span style="font-size:11px;">Read on ' + data.SentArray[i].SentTime + '</span></td><td>' + data.SentArray[i].timestamp + '</td><td> ' + data.SentArray[i].lname + ', ' + data.SentArray[i].fname + '</td></tr>');
                        }
                        items.push('</table></div>');
                    }

                    items.push('<div style="padding-top:20px;"><table class="table"><tr><th colspan="2">Message</th></tr>');
                    for (var i = 0; i < data.SentArray.length; i++) {

                        items.push('<tr><td class="left_listit">Subject:</td><td>' + data.SentArray[i].subject + '</td></tr>');
                        items.push('<tr><td class="left_listit">To:</td><td><a class="aLinkToUserDetails" id=' + data.SentArray[i].SentUserID + '>' + data.SentArray[i].lname + ', ' + data.SentArray[i].fname + '</a></td></tr>');
                        items.push('<tr><td class="left_listit">Sent:</td><td>' + data.SentArray[i].timestamp + '</td></tr>');
                        if (data.SentArray[i].SentTime != null && data.SentArray[i].SentTime != "") {
                            items.push('<tr><td class="left_listit">Read:</td><td>' + data.SentArray[i].SentTime + '</td></tr>');
                        } else {
                            items.push('<tr><td class="left_listit">Read:</td><td>&nbsp;</td></tr>');
                        }
                        items.push('<tr><td class="left_listit">Message:</td><td>' + data.SentArray[i].body + '</td></tr>');
                    }
                    items.push('</table></div>');
                } else {
                    $('#lblMsgDetail').html(data.message);
                    $('#lblMsgDetail').css("display", "block");
                    return false;
                }
                $('#divMessageDetail').append(items.join(''));

                $(".aLinkToUserDetails").on("click", function() {
                    var splits_id = this.id;
                    mainView.loadPage("get_user.html?UserID=" + splits_id);
                    return false;
                });

                $('#divDetailBack').css("display", "block");
                return false;
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function BindTrashMessageDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $('#divMessageDetail').empty();
                var items = [];
                if (data.TrashArray.length > 0) {
                    items.push('<div><table class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>To</th></tr>');
                    for (var i = 0; i < data.TrashArray.length; i++) {
                        items.push('<tr><td colspan="2"><img src="images/icons/bubble_16_grey.png">&nbsp;' + data.TrashArray[i].subject + '</td><td>' + data.TrashArray[i].timestamp + '</td><td> ' + data.TrashArray[i].lname + ', ' + data.TrashArray[i].fname + '</td></tr>');
                        items.push('<tr><td colspan="4" style="text-align: right;"><input class="btnRecover greybtn_comn" type="button" name="recover" id="btnRecover_' + data.TrashArray[i].message_id + '" value="Recover" /></td></tr></table></div>');
                    }

                    items.push('<div style="padding-top:20px;"><table class="table"><tr><th colspan="2">Message</th></tr>');
                    for (var i = 0; i < data.TrashArray.length; i++) {

                        items.push('<tr><td class="left_listit">Subject:</td><td>' + data.TrashArray[i].subject + '</td></tr>');
                        items.push('<tr><td class="left_listit">From:</td><td><a  class="aTraskLinkToUserDetails" id=' + data.TrashArray[i].user_id + '>' + data.TrashArray[i].lname + ', ' + data.TrashArray[i].fname + '</a></td></tr>');
                        items.push('<tr><td class="left_listit">Sent:</td><td>' + data.TrashArray[i].timestamp + '</td></tr>');
                        items.push('<tr><td class="left_listit">Message:</td><td>' + data.TrashArray[i].body + '</td></tr>');
                    }
                    items.push('</table></div>');
                } else {
                    $('#lblMsgDetail').html(data.message);
                    $('#lblMsgDetail').css("display", "block");
                    return false;
                }
                $('#divMessageDetail').append(items.join(''));

                $(".btnRecover").on("click", function() {
                    var splits_id = this.id.split('_');
                    var RecoverMsgId = splits_id[1];

                    $('#divMsgCompose').css("display", "none");
                    $('#divMessagingList').css("display", "block");
                    $('#divMessageDetail').css("display", "none");
                    RecoverDeletedMessages('MethodName=RecoverDeletedMessages&RecoverMsgId=' + RecoverMsgId);
                    BindMessagingList('MethodName=GetMessagingList&SearchType=Inbox&SearchText=&limit=100&offset=0');
                    return false;
                });

                $(".aTraskLinkToUserDetails").on("click", function() {
                    var splits_id = this.id;
                    mainView.loadPage("get_user.html?UserID=" + splits_id);
                    return false;
                });

                $('#divDetailBack').css("display", "block");
                return false;
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function RecoverDeletedMessages(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    $('#divMsgCompose').css("display", "none");
                    $('#divMessagingList').css("display", "block");
                    $('#divMessageDetail').css("display", "none");
                } else {
                    //alert(data.message);
                    $("#lblMsgDetail").css('display', 'block');
                    $("#lblMsgDetail").text(data.message);
                    $('#divMsgCompose').css("display", "none");
                    $('#divMessagingList').css("display", "block");
                    $('#divMessageDetail').css("display", "none");
                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

});

myApp.onPageInit('users', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    if (!Checkaccess('add_user')) {
        $("#divAddNewUser").css("display", "none");
    }
    if (!Checkaccess('view_users')) {
        $("#divUserSearch_Filter").css("display", "none");
        $("#divUserList").css("display", "none");
    }

    var searchtext = $("#txt_UserSearch").val();
    var searchtype = $("#userSorting").val();
    if (Checkaccess('view_users')) {
        BindUserList('MethodName=GetAllUserList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=0');
    }

    function BindUserList(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                data = $.parseJSON(data);
                $('#divBindUserList').empty();
                $('#divBindUserList').attr("style", "display:block");
                var currentOffset = parseInt(data.currentofset);


                if (data.userlist.length > 0) {
                    currentOffset = parseInt(currentOffset) + parseInt(data.userlist.length);

                    var items = [];
                    items.push('<table class="table"><tr><th colspan="2">Name</th><th>DBA</th><th>DOB</th></tr>');

                    for (var i = 0; i < data.userlist.length; i++) {
                        var uname = '';
                        if (data.firstLast == '1')
                            uname = data.userlist[i].fname + ', ' + data.userlist[i].lname;
                        else
                            uname = data.userlist[i].lname + ', ' + data.userlist[i].fname;
                        if (data.userlist[i].is_active == "1") {
                            if (data.userlist[i].is_deleted == "1") {
                                items.push('<tr class="trUserDetail" id="trUserDetail_' + data.userlist[i].user_id + '"><td colspan="2"><a class="aUserDetail" id=aUserDetail_' + data.userlist[i].user_id + ' href="javascript:;" style="text-decoration: line-through;">' + uname + '</a></td><td>' + data.userlist[i].dba + '</td><td>' + data.userlist[i].reg_date + '</td></tr>');
                            } else {
                                items.push('<tr class="trUserDetail" id="trUserDetail_' + data.userlist[i].user_id + '"><td colspan="2"><a class="aUserDetail" id=aUserDetail_' + data.userlist[i].user_id + ' href="javascript:;">' + uname + '</a></td><td>' + data.userlist[i].dba + '</td><td>' + data.userlist[i].reg_date + '</td></tr>');
                            }
                        } else {
                            items.push('<tr class="trUserDetail" id="trUserDetail_' + data.userlist[i].user_id + '"><td colspan="2"><a class="aUserDetail" id=aUserDetail_' + data.userlist[i].user_id + ' href="javascript:;">' + uname + '&nbsp - &nbsp; <span style="color:red; font-weight:bold;">INACTIVE</span></a></td><td>' + data.userlist[i].dba + '</td><td>' + data.userlist[i].reg_date + '</td></tr>');
                        }
                    }
                    items.push('</table>');
                    document.getElementById("hdntotalrecord").value = data.totalrecord;
                    $('#divBindUserList').append(items.join(''));
                    var items2 = [];

                    if (data.totalrecord > 10) {
                        $('#divUserPaging').empty();
                        $('#divUserPaging').css("display", "block");
                        items2.push('<div><a id="btnprev" class="button_small"  style="visibility:hidden;cursor:pointer;"> </a>&nbsp;&nbsp;');
                        items2.push('<label>Showing: ' + (parseInt(data.currentofset) + 1) + " - " + currentOffset + " of " + parseInt(data.totalrecord) + '</label>');
                        items2.push('&nbsp;&nbsp;<a id="btnnext" style="cursor:pointer;" class="button_small" > </a></div>');
                        $('#divUserPaging').append(items2.join(''));
                        $('#btnnext').on("click", function() {
                            callUserNext10();
                        });
                        $('#btnprev').on("click", function() {
                            callUserPrev10();
                        });

                        if (parseInt(data.totalrecord) == parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'hidden';
                            document.getElementById('btnprev').style.visibility = 'visible';
                        } else if (parseInt(data.totalrecord) < parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'visible';
                            document.getElementById('btnprev').style.visibility = 'Hidden';
                        } else {
                            if (parseInt(data.currentofset) == 0) {
                                document.getElementById('btnprev').style.visibility = 'Hidden';
                            } else {
                                document.getElementById('btnnext').style.visibility = 'visible';
                                document.getElementById('btnprev').style.visibility = 'visible';
                            }
                        }

                    } else { $('#divUserPaging').empty(); }

                    if ($("#txt_UserSearch").val() != '') {
                        $('#lblMsgDetail').html('Searching ' + $('#txt_UserSearch').val() + ' - ' + data.totalrecord + ' result(s) found');
                        $('#divDetailBack').css("display", "block");
                    } else {
                        $('#lblMsgDetail').html('');
                        $('#divDetailBack').css("display", "none");
                    }

                    $('.trUserDetail').on("click", function(e) {

                        var splits_id = this.id.split('_');
                        var UserDetailID = splits_id[1];
                        //       mainView.loadPage("get_user.html?UserID=" + UserDetailID);

                        mainView.loadPage({ url: "get_user.html?UserID=" + UserDetailID, force: true });

                        //    e.stopPropagation(); mainView.loadPage({ url: "get_user.html?UserID=" + UserDetailID, forceUrl: true });
                        return false;
                    });
                } else {
                    $('#lblMsgDetail').html('');
                    if ($("#txt_UserSearch").val() != '') {
                        $('#lblMsgDetail').html('Searching ' + $('#txt_UserSearch').val() + ' - ' + data.totalrecord + ' result(s) found');
                        $('#divDetailBack').css("display", "block");
                    } else {
                        $('#lblMsgDetail').css("display", "none");
                        $('#divDetailBack').css("display", "block");
                    }
                    $('#divDetailBack').css("display", "block");
                    $('#divUserPaging').css("display", "none");
                    $('#divBindUserList').empty();
                    var items = [];
                    items.push('<table style="width:100%;" class="table"><tr><th colspan="2">Subject</th><th>Sent</th><th>To</th></tr>');
                    items.push('<tr><td colspan="4" class="acenter"><label>No Users Found</label></td></tr>');
                    items.push('</table>');
                    $('#divBindUserList').append(items.join(''));
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindUserList(data);
            }
        });
    }

    $("#aAddUser").on("click", function() {
        $("#aAddUser").attr("href", "#");
        mainView.loadPage("add_user.html");
        return false;
    });

    $('#userSorting').change(function() {
        ViewSortUserCategory(this);
    });
    $('#txt_UserSearch').keyup(function() {
        searchUser();
    });

    function searchUser() {
        document.getElementById("hdnoffset").value = "0";
        var searchtext = $("#txt_UserSearch").val();
        var searchtype = $("#userSorting").val();
        BindUserList('MethodName=GetAllUserList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=0' + document.getElementById("hdnoffset").value);
    }

    function ViewSortUserCategory(obj) {
        var searchtext = $("#txt_UserSearch").val();
        var searchtype = $("#userSorting").val();
        BindUserList('MethodName=GetAllUserList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=0');
    }

    function callUserNext10() {
        var searchtext = $("#txt_UserSearch").val();
        var searchtype = $("#userSorting").val();
        var offset = 0;
        var x = document.getElementById("hdnoffset").value;
        offset = parseInt(x) + 10;
        document.getElementById("hdnoffset").value = offset;
        BindUserList('MethodName=GetAllUserList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=' + offset + '');
    }

    function callUserPrev10() {
        var searchtext = $("#txt_UserSearch").val();
        var searchtype = $("#userSorting").val();
        var offset = 0;
        var x = document.getElementById("hdnoffset").value;
        offset = parseInt(x) - 10;
        document.getElementById("hdnoffset").value = offset;
        BindUserList('MethodName=GetAllUserList&SearchType=' + searchtype + '&SearchText=' + searchtext + '&limit=10&offset=' + offset + '');
    }

    $('#divDetailBack').on("click", function() {
        $("#txt_UserSearch").val('');
        $("#userSorting").val('');
        $('#divDetailBack').css("display", "none");
        BindUserList('MethodName=GetAllUserList&SearchType=&SearchText=&limit=10&offset=0');
    });

});

function ShowComposeDive(Id, fname, lname) {
    var $ = jQuery.noConflict();
    $('#lblComposeUserName').text(fname + ' ' + lname);
    $('#divUserCompose').css("display", "block");
    return false;
}

function SendUserComposeMail(data) {

    var $ = jQuery.noConflict();

    $.ajax({
        url: "https://xactbid.pocketofficepro.com/workflowservice.php",
        type: "POST",
        data: data,
        cache: false,
        success: function(data, textStatus, jqxhr) {
            var data = $.parseJSON(data);

            if (data.status == "1") {
                //alert(data.message);
                $('#divUserCompose').css("display", "none");
                return false;
            } else {
                //alert(data.message);
                $("#lblSendMailMsg").css('display', 'block');
                $("#lblSendMailMsg").text(data.message);
                $('#divUserCompose').css("display", "block");
                return false;
            }
        },
        error: function(jqxhr, textStatus, errorMessage) {
            navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
        }
    })
}

function SendUserComposeMsg() {
    var $ = jQuery.noConflict();
    var retval = false;
    var ans = check_itemsvalidate('#divUserCompose input');
    if (ans) {
        SendUserComposeMail('MethodName=SendUserComposeEMail&User=' + $('#lblComposeUserName').val() + '&Subject=' + $('#txtUserSubject').val() + '&Message=' + $('#txtUserMessage').val());
        return false;
    } else { return false; }
    return false;
}

function closeUserComposediv() {
    var $ = jQuery.noConflict();
    $('#divUserCompose').css("display", "none");
    $('#txtUserSubject').val('');
    $('#txtUserMessage').val('');
    jQuery(".formError").remove();
    return false;
}

myApp.onPageInit('get_userbrowsing_history', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var UserID = page.query.UserID;
    if (UserID != null && UserID != "" && UserID != "undefined") {
        if (Checkaccess('view_user_history')) {
            BindUserBrowsingHistory('MethodName=GetUsersBrowsingHistory&UserID=' + UserID);
        }
    }

    function BindUserBrowsingHistory(data) {
        $ = jQuery.noConflict();
        try {
            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: 'POST',
                data: data,
                cache: false,
                success: function(data, textStatus, jqxhr) {
                    var data = $.parseJSON(data);
                    $('#lstUserBrowsingHistory').empty();
                    var items = [];
                    var Link = '';
                    dataClass = '';

                    if (data.browsingHistory.length > 0) {
                        // customers.php => customers.php, documents.php => edit_document.html, messaging.php => messaging.html,  users.php => get_user.html
                        items.push('<table class="table"><th>Item</th>');
                        for (var i = 0; i < data.browsingHistory.length; i++) {
                            if (data.browsingHistory[i].script.indexOf("customers.php") != -1) {
                                Link = "edit_customer.html?CustomerId=" + data.browsingHistory[i].item_id + "";
                                //Link = "edit_customer.html&CustomerId=" + data.browsingHistory[i].item_id + "";
                                dataClass = 'icon-book';
                            } else if (data.browsingHistory[i].script.indexOf("documents.php") != -1) {
                                Link = "edit_document.html?DocumentID=" + data.browsingHistory[i].item_id + "";
                                //Link = "edit_document.html&DocumentID=" + data.browsingHistory[i].item_id + "";
                                dataClass = 'icon-file';
                            } else if (data.browsingHistory[i].script.indexOf("messaging.php") != -1) {
                                Link = "messaging.html";
                                //Link = "messaging.html";
                                dataClass = 'icon-envelope';
                            } else if (data.browsingHistory[i].script.indexOf("users.php") != -1) {
                                Link = "get_user.html?UserID=" + data.browsingHistory[i].item_id + "";
                                //Link = "get_user.html&UserID=" + data.browsingHistory[i].item_id + "";
                                dataClass = 'icon-file';
                            } else if (data.browsingHistory[i].script.indexOf("jobs.php") != -1) {
                                Link = "jobtabs.html?JId=" + data.browsingHistory[i].item_id + "";
                                //Link = "jobtabs.html?JId==" + data.browsingHistory[i].item_id + "";
                                dataClass = 'icon-briefcase';
                            } else {
                                Link = data.browsingHistory[i].script + "?id=" + data.browsingHistory[i].item_id;
                                //Link =  data.browsingHistory[i].script + "?id=" + data.browsingHistory[i].item_id;
                                dataClass = 'icon-book';
                            }

                            items.push("<tr><td><i class='" + dataClass + "'></i>&nbsp;&nbsp;<a class='aUserBrowseLinks' href='" + Link + "' id=aUserBrowseLinks>" + data.browsingHistory[i].title + "</a></td></tr>");
                            //items.push("<tr><td><img alt='' src='' />&nbsp;&nbsp;<a class='aUserBrowseLinks' id=aUserBrowseLinks  title=" + data.browsingHistory[i].script + "?id=" + data.browsingHistory[i].item_id + ">" + data.browsingHistory[i].title + "</a></td></tr>");
                        }
                        items.push("</table>");
                    } else {
                        items.push('<table class="table"><th>Item</th>');
                        items.push("<tr><td>No History Found</td></tr>");
                        items.push("</table>");
                        //$('#lblBrowsingHistory').html("No History Found");
                        //$("#lblBrowsingHistory").css("display", "block");
                    }
                    $('#lstUserBrowsingHistory').append(items.join(''));
                },
                error: function(jqxhr, textStatus, errorMessage) {
                    BindUserBrowsingHistory(data);
                }
            })
        } catch (e) {
            BindUserBrowsingHistory(data);
        }
    }

    $("#aBackLinkUserBrowseHistory").on("click", function() {
        mainView.loadPage("get_user.html?UserID=" + UserID);
        return false;
    });

});

myApp.onPageInit('get-useracess', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var UserID = page.query.UserID;
    if (UserID != null && UserID != "" && UserID != "undefined") {
        if (Checkaccess('view_user_history')) {
            BindUserAccessHistory('MethodName=GetUsersAccessHistory&UserID=' + UserID);
        }
    }

    function BindUserAccessHistory(data) {
        $ = jQuery.noConflict();
        try {
            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: 'POST',
                data: data,
                cache: false,
                success: function(data, textStatus, jqxhr) {
                    var data = $.parseJSON(data);

                    $('#lstUserAccessHistory').empty();
                    var items = [];
                    var Link = '';
                    if (data.accessHistory.length > 0) {
                        items.push('<table class="table"><th>Timestamp</th><th>IP Address</th>');
                        for (var i = 0; i < data.accessHistory.length; i++) {
                            items.push("<tr><td>" + data.accessHistory[i]['timestamp'] + "</td><td>" + data.accessHistory[i]['ip_address'] + "</td></tr>");
                        }
                        //items.push("<tr><td colspan='2'><a class='greybtn_comn' href='get_user.html?UserID=" + data.currUser + "' >&laquo;&nbsp;Back</a></td></tr>");
                        items.push('</table>');
                    } else {
                        items.push('<table class="table"><th>Timestamp</th><th>IP Address</th>');
                        //if (data.currUser.length > 0) {
                        items.push("<tr><td colspan='2'>No History Found</td></tr>");
                        //}
                        items.push('</table>');
                        //$('#lblAccessHistory').html(data.message);
                        //$("#lblAccessHistory").css("display", "block");
                    }
                    $('#lstUserAccessHistory').append(items.join(''));
                },
                error: function(jqxhr, textStatus, errorMessage) {
                    BindUserAccessHistory(data);
                }
            })
        } catch (e) {

        }
    }

    $("#aBackLinkUserAccess").on("click", function() {
        mainView.loadPage("get_user.html?UserID=" + UserID);
        return false;
    });

});

myApp.onPageInit('get-useractivity', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var UserID = page.query.UserID;
    if (UserID != null && UserID != "" && UserID != "undefined") {
        if (Checkaccess('view_user_history')) {
            BindUserActivityHistory('MethodName=GetUsersActivityHistory&UserID=' + UserID);
        }
    }

    function BindUserActivityHistory(data) {
        $ = jQuery.noConflict();
        try {
            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: 'POST',
                data: data,
                cache: false,
                success: function(data, textStatus, jqxhr) {
                    var data = $.parseJSON(data);
                    $('#lstUserActivityHistory').empty();
                    var items = [];
                    var Link = '';
                    if (data.activityHistory.length > 0) {
                        items.push('<table class="table"><th>Action</th><th>Timestamp</th><th>Job ID</th>');
                        for (var i = 0; i < data.activityHistory.length; i++) {
                            items.push("<tr><td>" + data.activityHistory[i]['action'] + "</td><td>" + data.activityHistory[i]['timestamp'] + "</td><td><a class='aUserBrowseLinks' href=https://xactbid.pocketofficepro.com/HtmlPages/circles/jobtabs.html?JId=='" + data.activityHistory[i].job_id + "'>" + data.activityHistory[i].job_number + "</a></td></tr>");
                        }
                        //items.push("<tr><td colspan='3'>&nbsp;</td></tr><tr><td colspan='3'><a style='margin-top:12%;' class='greybtn_comn' href='get_user.html?UserID=" + data.currUser + "' >&laquo;&nbsp;Back</a></td></tr>");
                        items.push('</table>');
                    } else {
                        items.push('<table class="table"><th>Action</th><th>Timestamp</th><th>Job ID</th>');
                        //if (data.currUser.length > 0) {
                        items.push("<tr><td colspan='3'>No History Found</td></tr>");
                        //}
                        items.push('</table>');
                        //$('#lblActivityHistory').html("No History Found");
                        //$("#lblActivityHistory").css("display", "block");
                    }
                    $('#lstUserActivityHistory').append(items.join(''));
                },
                error: function(jqxhr, textStatus, errorMessage) {
                    BindUserActivityHistory(data);
                }
            })
        } catch (e) {

        }
    }

    $("#aBackLinkUserActivity").on("click", function() {
        mainView.loadPage("get_user.html?UserID=" + UserID);
        return false;
    });
});

myApp.onPageInit('schedule_jobs', function(page) {

    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    var flag = page.query.flag;
    var JobID = page.query.JId;
    var UploadId = page.query.UploadId;
    var scheduleJobType = page.query.scheduleType;

    if (Checkaccess('view_jobs')) {
        $('#divMainScheduleJob').css("display", "block");
    } else { $('#divMainScheduleJob').css("display", "none"); }

    if (flag == 'edit') {
        if (scheduleJobType == 'gutter') {
            BindScheduleJobDetails('MethodName=GetScheduleJobDetailsForEDIT&JobID=' + JobID + '&flag=1' + '&UploadId=' + UploadId);
            BindScheduleJobDetailForEdit('MethodName=BindScheduleJobDetailForEdit&JobID=' + JobID + '&UploadId=' + UploadId + '&scheduleType=' + scheduleJobType);
            $('#divGutterJobDetail').css("display", "block");
            $('#divScheduleJobHeader').html("");
            $('#divScheduleJobHeader').html("Schedule Gutter Job");
            $('#divScheJobDetailHead').html("");
            $('#divScheJobDetailHead').html("Schedule Gutter Job");
        } else if (scheduleJobType == 'repair') {
            BindScheduleJobDetails('MethodName=GetScheduleJobDetailsForEDIT&JobID=' + JobID + '&flag=2' + '&UploadId=' + UploadId);
            BindScheduleJobDetailForEdit('MethodName=BindScheduleJobDetailForEdit&JobID=' + JobID + '&UploadId=' + UploadId + '&scheduleType=' + scheduleJobType);
            $("#divRepairJobDetail").css("display", "block");
            $('#divScheduleJobHeader').html("");
            $('#divScheduleJobHeader').html("Schedule Repair Job");
            $('#divScheJobDetailHead').html("");
            $('#divScheJobDetailHead').html("Schedule Repair Job");
        } else if (scheduleJobType == 'roofing') {
            BindScheduleJobDetails('MethodName=GetScheduleJobDetailsForEDIT&JobID=' + JobID + '&flag=3' + '&UploadId=' + UploadId);
            BindScheduleJobDetailForEdit('MethodName=BindScheduleJobDetailForEdit&JobID=' + JobID + '&UploadId=' + UploadId + '&scheduleType=' + scheduleJobType);
            $("#divRoofingJobDetail").css("display", "block");
            $('#divScheduleJobHeader').html("");
            $('#divScheduleJobHeader').html("Schedule Roofing Job");
            $('#divScheJobDetailHead').html("");
            $('#divScheJobDetailHead').html("Schedule Roofing Job");
        } else if (scheduleJobType == 'window') {
            BindScheduleJobDetails('MethodName=GetScheduleJobDetailsForEDIT&JobID=' + JobID + '&flag=4' + '&UploadId=' + UploadId);
            BindScheduleJobDetailForEdit('MethodName=BindScheduleJobDetailForEdit&JobID=' + JobID + '&UploadId=' + UploadId + '&scheduleType=' + scheduleJobType);
            $("#divWindowJobDetail").css("display", "block");
            $('#divScheduleJobHeader').html("");
            $('#divScheduleJobHeader').html("Schedule Window Job");
            $('#divScheJobDetailHead').html("");
            $('#divScheJobDetailHead').html("Schedule Window Job");
            $("#divWindow1").css("display", "block");
            $("#divWindow2").css("display", "block");
            $("#divWindow3").css("display", "block");
            $("#divWindow4").css("display", "block");
        }
    } else {
        if (flag == '1') {
            BindScheduleJobDetails('MethodName=GetScheduleJobDetails&JobID=' + JobID + '&flag=1');
            $('#divGutterJobDetail').css("display", "block");
            $('#divScheduleJobHeader').html("");
            $('#divScheduleJobHeader').html("Schedule Gutter Job");
            $('#divScheJobDetailHead').html("");
            $('#divScheJobDetailHead').html("Schedule Gutter Job");
        } else if (flag == '2') {
            BindScheduleJobDetails('MethodName=GetScheduleJobDetails&JobID=' + JobID + '&flag=2');
            $("#divRepairJobDetail").css("display", "block");
            $('#divScheduleJobHeader').html("");
            $('#divScheduleJobHeader').html("Schedule Repair Job");
            $('#divScheJobDetailHead').html("");
            $('#divScheJobDetailHead').html("Schedule Repair Job");
        } else if (flag == '3') {
            BindScheduleJobDetails('MethodName=GetScheduleJobDetails&JobID=' + JobID + '&flag=3');
            $("#divRoofingJobDetail").css("display", "block");
            $('#divScheduleJobHeader').html("");
            $('#divScheduleJobHeader').html("Schedule Roofing Job");
            $('#divScheJobDetailHead').html("");
            $('#divScheJobDetailHead').html("Schedule Roofing Job");
        } else if (flag == '4') {
            BindScheduleJobDetails('MethodName=GetScheduleJobDetails&JobID=' + JobID + '&flag=4');
            $("#divWindowJobDetail").css("display", "block");
            $('#divScheduleJobHeader').html("");
            $('#divScheduleJobHeader').html("Schedule Window Job");
            $('#divScheJobDetailHead').html("");
            $('#divScheJobDetailHead').html("Schedule Window Job");
            $("#divWindow1").css("display", "block");
            $("#divWindow2").css("display", "block");
            $("#divWindow3").css("display", "block");
            $("#divWindow4").css("display", "block");
        }
    }

    function BindScheduleJobDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);
                var items = [];
                if (data != null) {

                    $("#txtSTitle").val(data.UploadTitle.toString());

                    if (scheduleJobType == 'window' && flag == 'edit') {
                        if (data.sideOfHouse != null) {
                            $('#ddlWinHouseSide').val(data.sideOfHouse).attr("selected", "selected");
                        }
                        $("#lblWinMarked").val(data.marked);
                        $("#lblWinStoryNo").val(data.windowStory);
                        $("#lblNoOfWindow").val(data.numOfWindow);
                    }

                    for (var i = 0; i < data.myCustomer.length; i++) {
                        $("#lblSCustomer").text(data.myCustomer[i].FullName);
                        $("#lblSAddress").text(data.myCustomer[i].address);
                        $("#lblSCity").text(data.myCustomer[i].city);
                        $("#lblState").text(data.myCustomer[i].state);
                        $("#lblSZip").text(data.myCustomer[i].zip);
                        $("#lblPhone").text(data.myCustomer[i].phone);
                        $("#lblStartDt").text(data.myCustomer[i].startDate);
                    }

                    $("#lblSalesman").text(data.myJob.salesman_fname + ' ' + data.myJob.salesman_lname);

                    $("#lblSalesPhone").text(data.myJob.salesman_phone);
                    $("#lblSJob").text(data.myJob.job_number);
                    return false;
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                BindScheduleJobDetails(data);
                return false;
            }

        });
        return false;
    }

    $("#btnCancelGutterJob").on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + JobID);
        return false;
    });
    $("#btnCancleScheduleRepairJob").on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + JobID);
        return false;
    });
    $("#btnCancelRoofingJob").on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + JobID);
        return false;
    });
    $("#btnCancelWindowJob").on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + JobID);
        return false;
    });


    $("#btnSaveGutterJob").on("click", function() {
        GetInsertedScheduleGutterJobs('MethodName=InsertScheduleGutterJob&JobID=' + JobID + '&upload_title=' + $("#txtSTitle").val() + '&customer=' + $("#lblSCustomer").text() + '&salesman=' + $("#lblSalesman").text() + '&job=' + $("#lblSJob").text() + '&address=' + $("#lblSAddress").text() + '&phone=' + $("#lblPhone").text() + '&startdate=' + $("#lblStartDt").text() + '&city=' + $("#lblSCity").text() + '&state=' + $("#lblState").text() + '&zip=' + $("#lblSZip").text() +
            '&phone2=' + $("#lblSalesPhone").text() + '&gutter_l_f=' + $("#txtGtrLinealFootage").val() + '&downspout_l_f=' + $("#txtDownspoutLF").val() + '&gutter_color=' + $("#txtGutterColor").val() + '&gutter_size=' + $("#ddlGutterSize").val() + '&gutter_material=' + $("#ddlGutterMaterial").val() + '&downspout_size=' + $("#ddlDownspoutSize").val() + '&cover_type=' + $("#txtGtrCoverType").val() + '&cover_lineal_footage=' + $("#txtGtrCoverLF").val() +
            '&pitch=' + $("#txtPitch").val() + '&electrical_outlet_location=' + $("#txtElecOutletLoc").val() + '&stories=' + $("#ddlStories").val() + '&agreed_upon_price=' + $("#txtAgreedPrice").val() + '&tear_off_notes=' + $("#txtTearOffNotes").val() + '&job_details=' + $("#txtSpecificDetails").val() + '&UploadId=' + UploadId);
    });

    $("#btnSaveScheduleRepairJob").on("click", function() {
        GetInsertedScheduleGutterJobs('MethodName=InsertScheduleRepairJob&JobID=' + JobID + '&upload_title=' + $("#txtSTitle").val() + '&customer=' + $("#lblSCustomer").text() + '&salesman=' + $("#lblSalesman").text() + '&job=' + $("#lblSJob").text() + '&address=' + $("#lblSAddress").text() + '&phone=' + $("#lblPhone").text() + '&startdate=' + $("#lblStartDt").text() + '&city=' + $("#lblSCity").text() + '&state=' + $("#lblState").text() + '&zip=' + $("#lblSZip").text() + '&phone2=' + $("#lblSalesPhone").text() +
            '&house=' + $("#txtHouseRJ").val() + '&garage=' + $("#txtGarageRJ").val() + '&shed=' + $("#txtShedRJ").val() + '&patio=' + $("#txtPatioRJ").val() + '&gutters=' + $("#txtGuttersRJ").val() + '&color=' + $("#txtColorRJ").val() + '&total_l_f=' + $("#txtTotalLFRJ").val() + '&downspout=' + $("#txtDownspoutRJ").val() +
            '&repair_details=' + $("#txtRepairDetailsRJ").val() + '&upon_price=' + $("#txtSCAgreedPriceRJ").val() + '&UploadId=' + UploadId);
    });

    $("#btnSaveRoofingJob").on("click", function() {
        GetInsertedScheduleGutterJobs('MethodName=InsertScheduleRoofingJob&JobID=' + JobID + '&upload_title=' + $("#txtSTitle").val() + '&customer=' + $("#lblSCustomer").text() + '&salesman=' + $("#lblSalesman").text() + '&job=' + $("#lblSJob").text() + '&address=' + $("#lblSAddress").text() + '&phone=' + $("#lblPhone").text() + '&startdate=' + $("#lblStartDt").text() + '&city=' + $("#lblSCity").text() + '&state=' + $("#lblState").text() + '&zip=' + $("#lblSZip").text() +
            '&phone2=' + $("#lblSalesPhone").text() +
            '&permit=' + $("#ddlPermitLst").val() + '&need_a_production_manager=' + $("#ddlProdMngr").val() + '&stories=' + $("#ddlStoriesNum").val() +
            '&existing_roof=' + $("#txtExistingRoof").val() + '&house=' + $("#ddlHouseLayers").val() + '&house_squares=' + $("#txtHouseSquares").val() +
            '&garage=' + $("#ddlGarageLayers").val() + '&garage_squares=' + $("#txtGarageSquares").val() + '&shed=' + $("#ddlShedLayers").val() +
            '&shed_squares=' + $("#txtShedSquares").val() + '&patio=' + $("#ddlPatioLayers").val() + '&patio_squares=' + $("#txtPatioSquares").val() +
            '&new_roof=' + $("#txtNewRoof").val() + '&job_new_roof_color=' + $("#txtRoofColor").val() + '&squares=' + $("#txtSquares").val() +
            '&pitch=' + $("#txtRoofPitch").val() + '&roofings_tear_off=' + $("#ddlRoofTearOff").val() + '&roofings_color=' + $("#txtRoofingColor").val() +
            '&drip_edge=' + $("#ddlDripEdgeInstl").val() + '&agreed_upon_price=' + $("#txtRoofUponPrice").val() + '&tear_off_notes=' + $("#txtRoofTearOffNotes").val() +
            '&job_details=' + $("#txtRoofSpecificDetails").val() + '&UploadId=' + UploadId);
    });

    $("#btnSaveWindowJob").on("click", function() {
        GetInsertedScheduleGutterJobs('MethodName=InsertScheduleWindowJob&JobID=' + JobID + '&upload_title=' + $("#txtSTitle").val() + '&customer=' + $("#lblSCustomer").text() + '&salesman=' + $("#lblSalesman").text() + '&job=' + $("#lblSJob").text() + '&address=' + $("#lblSAddress").text() + '&phone=' + $("#lblPhone").text() + '&startdate=' + $("#lblStartDt").text() + '&city=' + $("#lblSCity").text() + '&state=' + $("#lblState").text() + '&zip=' + $("#lblSZip").text() + '&phone2=' + $("#lblSalesPhone").text() +
            '&no_window=' + $("#lblNoOfWindow").val() + '&marked=' + $("#lblWinMarked").val() + '&window_story=' + $("#lblWinStoryNo").val() + '&window_side=' + $("#ddlWinHouseSide").val() + '&window_type=' + $("#ddlWindowType").val() + '&window_color=' + $("#txtWindowColor").val() + '&window_dimension_x=' + $("#txtWinDimensions_x").val() + '&window_dimension_y=' + $("#txtWinDimensions_y").val() +
            '&window_screen=' + $("#txtWindowScreen").val() + '&glazing_bead=' + $("#txtGlazingBead").val() +
            '&agreed_upon_price=' + $("#txtWindowUponPrice").val() + '&des_damage=' + $("#txtWindowDamage").val() + '&specific_detail=' + $("#txtWinSpecificDetails").val() + '&UploadId=' + UploadId);
    });

    function GetInsertedScheduleGutterJobs(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);
                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    mainView.loadPage("jobuploads.html?JId=" + JobID);
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblScheduleJobDetails').html(obj.message);
                    //$("#lblScheduleJobDetails").css("display", "block");

                    $("#divGutterJobDetail").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function BindScheduleJobDetailForEdit(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);
                var items = [];
                if (data != null) {
                    if (data.scheduleType == 'roofing') {
                        if (data.viewData.schedule_roofing_job_permit.meta_value != null && data.viewData.schedule_roofing_job_permit.meta_value != '') {
                            $("#ddlPermitLst").val(data.viewData.schedule_roofing_job_permit.meta_value).attr("selected", "selected");
                        }
                        if (data.viewData.schedule_roofing_job_stories.meta_value != null && data.viewData.schedule_roofing_job_stories.meta_value != '') {
                            $("#ddlStoriesNum").val(data.viewData.schedule_roofing_job_stories.meta_value).attr("selected", "selected");
                        }
                        if (data.viewData.schedule_roofing_job_house.meta_value != null && data.viewData.schedule_roofing_job_house.meta_value != '') {
                            $("#ddlHouseLayers").val(data.viewData.schedule_roofing_job_house.meta_value).attr("selected", "selected");
                        }
                        $("#txtHouseSquares").val(data.viewData.schedule_roofing_job_house_squares.meta_value);
                        if (data.viewData.schedule_roofing_job_shed.meta_value != null && data.viewData.schedule_roofing_job_shed.meta_value != '') {
                            $("#ddlShedLayers").val(data.viewData.schedule_roofing_job_shed.meta_value).attr("selected", "selected");
                        }
                        $("#txtShedSquares").val(data.viewData.schedule_roofing_job_shed_squares.meta_value);
                        $("#txtNewRoof").val(data.viewData.schedule_roofing_job_new_roof.meta_value);
                        $("#txtSquares").val(data.viewData.schedule_roofing_job_squares.meta_value);
                        if (data.viewData.schedule_roofing_job_roofings_tear_off.meta_value != null && data.viewData.schedule_roofing_job_roofings_tear_off.meta_value != '') {
                            $("#ddlRoofTearOff").val(data.viewData.schedule_roofing_job_roofings_tear_off.meta_value).attr("selected", "selected");
                        }
                        if (data.viewData.schedule_roofing_job_drip_edge.meta_value != null && data.viewData.schedule_roofing_job_drip_edge.meta_value != '') {
                            $("#ddlDripEdgeInstl").val(data.viewData.schedule_roofing_job_drip_edge.meta_value).attr("selected", "selected");
                        }
                        if (data.viewData.schedule_roofing_job_need_a_production_manager.meta_value != null && data.viewData.schedule_roofing_job_need_a_production_manager.meta_value != '') {
                            $("#ddlProdMngr").val(data.viewData.schedule_roofing_job_need_a_production_manager.meta_value).attr("selected", "selected");
                        }
                        $("#txtExistingRoof").val(data.viewData.schedule_roofing_job_existing_roof.meta_value);
                        if (data.viewData.schedule_roofing_job_garage.meta_value != null && data.viewData.schedule_roofing_job_garage.meta_value != '') {
                            $("#ddlGarageLayers").val(data.viewData.schedule_roofing_job_garage.meta_value);
                        }
                        $("#txtGarageSquares").val(data.viewData.schedule_roofing_job_garage_squares.meta_value);
                        if (data.viewData.schedule_roofing_job_patio.meta_value != null && data.viewData.schedule_roofing_job_patio.meta_value != '') {
                            $("#ddlPatioLayers").val(data.viewData.schedule_roofing_job_patio.meta_value).attr("selected", "selected");
                        }

                        $("#txtPatioSquares").val(data.viewData.schedule_roofing_job_patio_squares.meta_value);
                        $("#txtRoofColor").val(data.viewData.schedule_roofing_job_job_new_roof_color.meta_value);
                        $("#txtRoofPitch").val(data.viewData.schedule_roofing_job_pitch.meta_value);
                        $("#txtRoofingColor").val(data.viewData.schedule_roofing_job_roofings_color.meta_value);
                        $("#txtRoofUponPrice").val(data.viewData.schedule_roofing_job_agreed_upon_price.meta_value);
                        $("#txtRoofTearOffNotes").val(data.viewData.schedule_roofing_job_tear_off_notes.meta_value);
                        $("#txtRoofSpecificDetails").val(data.viewData.schedule_roofing_job_job_details.meta_value);
                    } else if (data.scheduleType == 'gutter') {
                        $("#txtGtrLinealFootage").val(data.viewData.schedule_gutter_job_gutter_l_f.meta_value);
                        $("#txtGutterColor").val(data.viewData.schedule_gutter_job_gutter_color.meta_value);
                        if (data.viewData.schedule_gutter_job_gutter_material.meta_value != null && data.viewData.schedule_gutter_job_gutter_material.meta_value != '') {
                            $("#ddlGutterMaterial").val(data.viewData.schedule_gutter_job_gutter_material.meta_value).attr("selected", "selected");;
                        }
                        $("#txtGtrCoverType").val(data.viewData.schedule_gutter_job_cover_type.meta_value);
                        $("#txtPitch").val(data.viewData.schedule_gutter_job_pitch.meta_value);
                        if (data.viewData.schedule_gutter_job_stories.meta_value != null && data.viewData.schedule_gutter_job_stories.meta_value != '') {
                            $("#ddlStories").val(data.viewData.schedule_gutter_job_stories.meta_value).attr("selected", "selected");;
                        }
                        $("#txtDownspoutLF").val(data.viewData.schedule_gutter_job_downspout_l_f.meta_value);
                        if (data.viewData.schedule_gutter_job_gutter_size.meta_value != null && data.viewData.schedule_gutter_job_gutter_size.meta_value != '') {
                            $("#ddlGutterSize").val(data.viewData.schedule_gutter_job_gutter_size.meta_value).attr("selected", "selected");;
                        }
                        if (data.viewData.schedule_gutter_job_downspout_size.meta_value != null && data.viewData.schedule_gutter_job_downspout_size.meta_value != '') {
                            $("#ddlDownspoutSize").val(data.viewData.schedule_gutter_job_downspout_size.meta_value).attr("selected", "selected");;
                        }
                        $("#txtGtrCoverLF").val(data.viewData.schedule_gutter_job_cover_lineal_footage.meta_value);
                        $("#txtElecOutletLoc").val(data.viewData.schedule_gutter_job_electrical_outlet_location.meta_value);
                        $("#txtAgreedPrice").val(data.viewData.schedule_gutter_job_agreed_upon_price.meta_value);
                        $("#txtTearOffNotes").val(data.viewData.schedule_gutter_job_tear_off_notes.meta_value);
                        $("#txtSpecificDetails").val(data.viewData.schedule_gutter_job_job_details.meta_value);
                    } else if (data.scheduleType == 'repair') {
                        $("#txtHouseRJ").val(data.viewData.schedule_repair_job_house.meta_value);
                        $("#txtShedRJ").val(data.viewData.schedule_repair_job_shed.meta_value);
                        $("#txtGuttersRJ").val(data.viewData.schedule_repair_job_gutters.meta_value);
                        $("#txtTotalLFRJ").val(data.viewData.schedule_repair_job_total_l_f.meta_value);
                        $("#txtRepairDetailsRJ").val(data.viewData.schedule_repair_job_repair_details.meta_value);
                        $("#txtGarageRJ").val(data.viewData.schedule_repair_job_garage.meta_value);
                        $("#txtPatioRJ").val(data.viewData.schedule_repair_job_patio.meta_value);
                        $("#txtColorRJ").val(data.viewData.schedule_repair_job_color.meta_value);
                        $("#txtDownspoutRJ").val(data.viewData.schedule_repair_job_downspout.meta_value);
                        $("#txtSCAgreedPriceRJ").val(data.viewData.schedule_repair_job_upon_price.meta_value);
                    } else if (data.scheduleType == 'window') {

                        if (data.viewData.schedule_window_job_window_type.meta_value != null && data.viewData.schedule_window_job_window_type.meta_value != '') {
                            $("#ddlWindowType").val(data.viewData.schedule_window_job_window_type.meta_value).attr("selected", "selected");;
                        }
                        $("#txtWinDimensions_x").val(data.viewData.schedule_window_job_window_dimension_x.meta_value);
                        $("#txtWinDimensions_y").val(data.viewData.schedule_window_job_window_dimension_y.meta_value);
                        $("#txtGlazingBead").val(data.viewData.schedule_window_job_glazing_bead.meta_value);
                        $("#txtWindowColor").val(data.viewData.schedule_window_job_window_color.meta_value);
                        $("#txtWindowScreen").val(data.viewData.schedule_window_job_window_screen.meta_value);
                        $("#txtWindowUponPrice").val(data.viewData.schedule_window_job_agreed_upon_price.meta_value);
                        $("#txtWindowDamage").val(data.viewData.schedule_window_job_des_damage.meta_value);
                        $("#txtWinSpecificDetails").val(data.viewData.schedule_window_job_specific_detail.meta_value);
                    }

                    return false;
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                navigator.notification.alert(
                    errorThrown, alertDismissed, "An error occured", "Done"
                );
                return false;
            }

        })

    }
});

myApp.onPageInit('addnewJob', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    $('.masked-phone').inputmask('(999) 999-9999', { placeholder: ' ' });
    BindAllDDListForAddNewJob('MethodName=GetAllDDListForAddNewJob');
    var flag;
    if (Checkaccess('add_job')) {
        $('#divMainAddNewJob').css("display", "block");
    } else { $('#divMainAddNewJob').css("display", "none"); }

    if (Checkaccess('assign_job_salesman')) {
        $('#divReferral').css("display", "block");
        $('#divSalesman').css("display", "block");
    } else {
        $('#divReferral').css("display", "none");
        $('#divSalesman').css("display", "none");
    }


    function BindAllDDListForAddNewJob(data) {
        //console.log(data);
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                //console.log(data);
                var data = $.parseJSON(data);
                var obj = JSON.stringify(data.stateArray);

                $.each(data.stateArray, function(key, value) {
                    $("#ddlCustState").append($("<option value='" + key + "'>" + key + "</option>"));
                });

                $("#ddlCustomer").empty();
                $("#ddlCustomer").append("<option value=''>Add New Insured</option>");
                for (var i = 0; i < data.customers.length; i++) {
                    $("#ddlCustomer").append($("<option value='" + data.customers[i].customer_id + "'>" + data.customers[i].select_label + "</option>"));
                }

                $("#ddlJobOrigin").empty();
                for (var i = 0; i < data.origins.length; i++) {
                    $("#ddlJobOrigin").append($("<option value='" + data.origins[i].origin_id + "'>" + data.origins[i].origin + "</option>"));
                }

                $("#ddlReferral").empty();
                $("#ddlReferral").append("<option value=''>No Referral</option>");
                if (data.accountMetaValue == null) {
                    for (var i = 0; i < data.salesmen.length; i++) {
                        $("#ddlReferral").append($("<option value='" + data.salesmen[i].user_id + "'>" + data.salesmen[i].select_label + "</option>"));
                    }
                } else {
                    for (var i = 0; i < data.salesmenByLevel.length; i++) {
                        $("#ddlReferral").append($("<option value='" + data.salesmenByLevel[i].user_id + "'>" + data.salesmenByLevel[i].select_label + "</option>"));
                    }
                }

                $("#ddlJobType").empty();
                for (var i = 0; i < data.jobType.length; i++) {
                    $("#ddlJobType").append($("<option value='" + data.jobType[i].job_type_id + "'>" + data.jobType[i].job_type + "</option>"));
                }

                $("#ddlSalesman").empty();
                $("#ddlSalesman").append("<option value=''>Choose Later</option>");
                if (data.accountMetaValue == null) {
                    for (var i = 0; i < data.salesmen.length; i++) {
                        $("#ddlSalesman").append($("<option value='" + data.salesmen[i].user_id + "'>" + data.salesmen[i].select_label + "</option>"));
                    }
                } else {
                    for (var i = 0; i < data.salesmenByLevel.length; i++) {
                        $("#ddlSalesman").append($("<option value='" + data.salesmenByLevel[i].user_id + "'>" + data.salesmenByLevel[i].select_label + "</option>"));
                    }
                }

                $("#ddlProvider").empty();
                $("#ddlProvider").append("<option value=''>Choose Later</option>");
                for (var i = 0; i < data.providers.length; i++) {
                    $("#ddlProvider").append($("<option value='" + data.providers[i].insurance_id + "'>" + data.providers[i].insurance + "</option>"));
                }

                $("#ddlJurisdiction").empty();
                $("#ddlJurisdiction").append("<option value=''>Choose Later</option>");
                for (var i = 0; i < data.jurisdictions.length; i++) {
                    $("#ddlJurisdiction").append($("<option value='" + data.jurisdictions[i].jurisdiction_id + "'>" + data.jurisdictions[i].location + "</option>"));
                }
                return false;
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindAllDDListForAddNewJob(data);
                return false;
            }
        })
        return false;
    }

    $('#ddlReferral').change(function() {
        var custId = $('#ddlReferral').val();

        if (custId != '') {
            GetUserNotesByUSerID('MethodName=GetUserNotesByUSerID&CId=' + custId);
        } else {
            $("#divReferralNotes").css("display", "none");
            $("#spnRefTexts").html("");
        }
    });

    $('#ddlSalesman').change(function() {
        var custId = $('#ddlSalesman').val();

        if (custId > 0) {
            GetSalesmanJobByID('MethodName=GetSalesmanJobByID&CId=' + custId);
        } else {
            //$("#divSalesmanJobs").css("display", "none");
            $("#lblSalesJob").html("");
        }

    });

    $("#btnCancelJob").on("click", function() {
        jQuery(".formError").remove();
        mainView.loadPage("jobs.html");
        return false;
    });

    $('#ddlCustomer').change(function() {

        var CustId = $('#ddlCustomer').val();
        if (CustId == '') {
            $('#divAddNewCustomer').css("display", "block");
            return false;
        } else {
            $('#divAddNewCustomer').css("display", "none");
        }
    });

    $("#btnSaveJob").on("click", function() {

        var CustId = $('#ddlCustomer').val();

        if (CustId != '') {
            flag = 0;
        } else { flag = 1; }

        if (flag == 0) {
            if (CustId != '') {
                SaveNewJob('MethodName=AddNewJobItem&CustomerID=' + $('#ddlCustomer').val() + '&OriginId=' + $('#ddlJobOrigin').val() + '&Referral=' + $('#ddlReferral').val() + '&JobType=' + $('#ddlJobType').val() + '&JobTypeNote=' + $('#txtJobTypeNote').val() + '&salesman=' + $('#ddlSalesman').val() + '&Provider=' + $('#ddlProvider').val() + '&Jurisdiction=' + $('#ddlJurisdiction').val());
                return false;
            } else {
                $('#divAddNewCustomer').css("display", "block");
                return false;
            }
        }
        if (flag == 1) {
            var retval = false;
            var ans = check_itemsvalidate('#divNewCustomer input');

            if (ans) {
                SaveNewJob('MethodName=AddNewJobItemWithCustomer&Fname=' + $('#txtCustFName').val() + '&Lname=' + $('#txtCustLName').val() + '&nickName=' + $('#txtCustNickName').val() + '&address=' + $('#txtCustAddress').val() + '&city=' + $('#txtCustCity').val() + '&state=' + $('#ddlCustState').val() + '&zip=' + $('#txtCustZip').val() + '&street=' + $('#txtCustCrossStreet').val() + '&Phone=' + $('#txtCustPhone').val() + '&SecPhone=' + $('#txtCustSecPhone').val() + '&Email=' + $('#txtCustEmail').val() + '&CustomerID=' + $('#ddlCustomer').val() + '&OriginId=' + $('#ddlJobOrigin').val() + '&Referral=' + $('#ddlReferral').val() + '&JobType=' + $('#ddlJobType').val() + '&JobTypeNote=' + $('#txtJobTypeNote').val() + '&salesman=' + $('#ddlSalesman').val() + '&Provider=' + $('#ddlProvider').val() + '&Jurisdiction=' + $('#ddlJurisdiction').val());
                return false;
            } else {
                $('#lblAddNewCustomer').html("please enter all required fields");
                $('#lblAddNewCustomer').css("display", "block");
                return false;
            }
        }
        return false;
    });

    function SaveNewJob(data) {
        var $ = jQuery.noConflict();
        $('#btnSaveJob').prop('disabled', true);
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    mainView.loadPage("jobdetails.html?JId=" + data.JobId);
                    return false;
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblAddNewJob').html(data.message);
                    //$("#lblAddNewJob").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    function GetUserNotesByUSerID(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.userArray.length > 0) {
                    if (data.userArray[0].notes != '' && data.userArray[0].notes != null) {
                        $("#divReferralNotes").css("display", "block");
                        $("#spnRefTexts").html(data.userArray[0].notes);
                    } else {
                        $("#divReferralNotes").css("display", "none");
                        $("#spnRefTexts").html("");

                    }

                } else {
                    $("#divReferralNotes").css("display", "none");
                    $("#spnRefTexts").html("");

                }
                return false;
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    function GetSalesmanJobByID(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $("#divSalesmanJobs").css("display", "block");
                $("#lblSalesJob").html(data.jobsTotal + " YTD job(s)");

                return false;
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }
});

myApp.onPageInit('assign_jobsalesman', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    var jobId = page.query.JId;
    BindJobSalesmanList('MethodName=GetJobSalesmanList&JobID=' + jobId);
    BindCustomersJobForMap('MethodName=GetCustomersJobForMap&JobID=' + jobId);

    $("#btnSaveJobSalesman").on("click", function() {

        ReBindUpdatedJobDetails('MethodName=UpdateJobSalesmanInfo&JobId=' + jobId + '&SalesmanID=' + $('select[name=salesman]').val());
    });

    function BindJobSalesmanList(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    $("#salesjobstotal").html("");
                    if (data.jobsTotal > 0) {
                        $("#salesjobstotal").html("<b>" + data.jobsTotal + "</b> YTD job(s)");
                    } else {
                        $("#salesjobstotal").html("<b>0</b> YTD job(s)");
                    }
                    if (data.strJobSalesmanList != null) {
                        $('#lstJobSalesman').empty();
                        $("#lstJobSalesman").append(data.strJobSalesmanList);
                        return false;
                    }
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindJobSalesmanList(data);
            }
        })
        return false;
    }

    function ReBindUpdatedJobDetails(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    BindJobSalesmanList('MethodName=GetJobSalesmanList&JobID=' + jobId);
                    return false;
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    function BindCustomersJobForMap(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    if (data.flag == '0') {

                        var mapOptions = {
                            center: new google.maps.LatLng(data.mapContent.lat, data.mapContent.long),
                            zoom: 10,
                            draggable: true,
                            disableDoubleClickZoom: true,
                            disableDefaultUI: true,
                            mapTypeId: google.maps.MapTypeId.ROADMAP,

                            mapTypeControlOptions: {
                                style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                            },
                        };

                        var infoWindow = new google.maps.InfoWindow();
                        document.getElementById("dvMap").innerHTML = "";
                        var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
                    } else if (data.mapContent.length > 0) {
                        var mapOptions = {
                            center: new google.maps.LatLng(data.mapContent[0].lat, data.mapContent[0].long),
                            zoom: 10,
                            draggable: true,
                            disableDoubleClickZoom: true,
                            disableDefaultUI: true,
                            mapTypeId: google.maps.MapTypeId.ROADMAP,

                            mapTypeControlOptions: {
                                style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                            },
                        };

                        var infoWindow = new google.maps.InfoWindow();
                        document.getElementById("dvMap").innerHTML = "";
                        var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);

                        for (var i = 0; i < data.mapContent.length; i++) {

                            var data1 = data.mapContent[i];

                            var myLatlng = new google.maps.LatLng(data1.lat, data1.long);

                            var marker = new google.maps.Marker({
                                position: myLatlng,
                                map: map,
                                title: data1.customer_id
                            });

                            var contentHtml = "";
                            contentHtml = "<div><ul><li>Job #: <a class='alinkToJob2' title=Go to job target=main href=jobtabs.html?JId=" + data1.job_id + ">" + data1.job_number + "</a></li>";
                            contentHtml = contentHtml + "<li>Customer: <a  class='alinkToSalesman' title=View User target=main href=get_user.html?UserID=" + data1.user_id + ">" + data1.FullName + "</a></li>";
                            contentHtml = contentHtml + "<div><ul><li>Insured: <a  class='alinkToCustomer' title=Go to job target=main href=edit_customer.html?CustomerId=" + data1.customer_id + ">" + data1.fname + " " + data1.lname + "</a></li>";
                            contentHtml = contentHtml + "<br />" + data1.address + ", " + data1.city + ", " + data1.state + " " + data1.zip + "";
                            contentHtml = contentHtml + "<li>DOB: " + data1.timestamp + "</li><li>" + data1.distance + " miles</li></ul></div>";
                            data1.cross_street = contentHtml;
                            (function(marker, data1) {
                                google.maps.event.addListener(marker, "click", function(e) {
                                    infoWindow.setContent(data1.cross_street);
                                    infoWindow.open(map, marker);
                                });
                            })(marker, data1);



                        }
                        return false;
                    }
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    $('#btnAssignJobSalesman').on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + jobId);
        return false;
    });
});

myApp.onPageInit('edit-user', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    DateCallJS();
    var UserID = page.query.UserID;

    if (UserID != null && UserID != "" && UserID != "undefined") {
        if (Checkaccess('edit_users')) {
            BindUserDetailsForEdit('MethodName=GetDetailOfUserForEdit&UserID=' + UserID);
            BindUserUploadInsPdfForms('MethodName=UserInsurancepdfUpload&UserID=' + UserID);
            BindEditUserPermissions('MethodName=GetUsersEditPermissions&UserID=' + UserID);
            BindStagesForAdvanceAccess('MethodName=GetAllStagesForEditUser&UserID=' + UserID);
            BindAllStageNotification('MethodName=BindStageNotificationDetails&UserID=' + UserID);
            $("#divMainEditUser").css("display", "block");
        } else {
            $("#lblMainLabelForEditUser").html("Insufficient Rights");
            $("#lblMainLabelForEditUser").css("display", "block");
            $("#divMainEditUser").css("display", "none");
        }
        if (!Checkaccess('edit_user_passwords')) {
            $("#divEditPassword").css("display", "none");
        }
    }

    function BindUserDetailsForEdit(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);

                $("#ddlSmsCarrier").empty();
                $("#ddlSmsCarrier").append("<option value=''>NO SMS</option>");
                for (var i = 0; i < data.carriersArray.length; i++) {
                    $("#ddlSmsCarrier").append($("<option value='" + data.carriersArray[i].sms_id + "'>" + data.carriersArray[i].carrier + "</option>"));
                }
                $("#ddlAccessLevel").empty();
                for (var i = 0; i < data.userLevelsArray.length; i++) {
                    $("#ddlAccessLevel").append($("<option value='" + data.userLevelsArray[i].level_id + "'>" + data.userLevelsArray[i].level + "</option>"));
                }
                $("#ddlOffice").empty();
                $("#ddlOffice").append("<option value='0'>Default</option>");
                for (var i = 0; i < data.officesArray.length; i++) {
                    $("#ddlOffice").append($("<option value='" + data.officesArray[i].office_id + "'>" + data.officesArray[i].title + "</option>"));
                }
                var items = [];
                if (data.userArray.length > 0) {
                    for (var i = 0; i < data.userArray.length; i++) {
                        $("#txtFirstName").val(data.userArray[i].fname);
                        $("#txtLastName").val(data.userArray[i].lname);
                        $("#txtDBA").val(data.userArray[i].dba);
                        $('#divEditUserHeader').html("Edit " + data.userArray[i].dba);
                        $("#txtEmail").val(data.userArray[i].email);
                        $("#txtPhone").val(data.userArray[i].phone);
                        if (data.userArray[i].journal.toString() == "1") {
                            $('#chkForJournal').attr('checked', true);
                        }
                        if (data.userArray[i].founder.toString() == "1") {
                            $('#chkFounder').attr('checked', true);
                        }
                        if (data.userArray[i].is_active.toString() == "1") {
                            $('#chkActive').attr('checked', true);
                        }
                        if (data.userArray[i].generalinsbox.toString() == "1") {
                            $('#chkLibInsExpNoNeed').attr('checked', true);
                        }

                        $("#txtLibInsExpOn").val(data.userArray[i].generalins);
                        if (data.userArray[i].workerinsbox.toString() == "true") {
                            $('#chkCompInsExpNoNeed').attr('checked', true);
                        }

                        $("#txtCompInsExpOn").val(data.userArray[i].workerins);
                        $("#txtNotes").val(data.userArray[i].notes);

                        var SmsId = data.userArray[i].sms_carrier;
                        var LevelId = data.userArray[i].level;
                        var OfficeId = data.userArray[i].office_id;

                        $('#ddlSmsCarrier').val(SmsId).attr("selected", "selected");
                        $('#ddlAccessLevel').val(LevelId).attr("selected", "selected");

                        $('#ddlOffice').val(OfficeId).attr("selected", "selected");
                        return false;
                    }
                    return false;
                } else {
                    navigator.notification.alert(
                        "User details not found!", alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
                return false;
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    $("#btnEditUser").on("click", function() {

        var retval = false;
        var ans = check_itemsvalidate('#divSubEditDoc input');

        if (ans) {
            var chkeditJournal = "0";
            var chkeditFounder = "0";
            var chkeditActive = "0";
            var chkeditLibInsNotNeed = "0";
            var chkeditCompInsNotNeed = "0";

            var UserID = page.query.UserID;
            if (UserID != null && UserID != "" && UserID != "undefined") {

                if ($('#chkForJournal').is(":checked")) { chkeditJournal = 1; }

                if ($('#chkFounder').is(":checked")) { chkeditFounder = 1; }

                if ($('#chkActive').is(":checked")) { chkeditActive = 1; }

                if ($('#chkLibInsExpNoNeed').is(":checked")) { chkeditLibInsNotNeed = 1; }

                if ($('#chkCompInsExpNoNeed').is(":checked")) { chkeditCompInsNotNeed = 1; }

                UpdateEditUserDetails('MethodName=UpdateEditUserDetails&UserID=' + UserID + '&fname=' + $("#txtFirstName").val() + '&lname=' + $("#txtLastName").val() + '&Dba=' + $("#txtDBA").val() + '&email=' + $("#txtEmail").val() + '&phone=' + $("#txtPhone").val() + '&smsCarrier=' + $("#ddlSmsCarrier").val() + '&accessLevel=' + $("#ddlAccessLevel").val() + '&office=' + $("#ddlOffice").val() + '&journal=' + chkeditJournal + '&founder=' + chkeditFounder + '&Active=' + chkeditActive + '&libInsExp=' + chkeditLibInsNotNeed + '&txtLibInsExpOn=' + $("#txtLibInsExpOn").val() + '&comInsExp=' + chkeditCompInsNotNeed + '&txtComInsExpOn=' + $("#txtCompInsExpOn").val() + '&txtNotes=' + $("#txtNotes").val());

            } else {
                return false;
            }
        }
        return false;
    });

    function UpdateEditUserDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    //$('#lblEditUser').html(data.message);
                    //$("#lblEditUser").css("display", "block");
                    mainView.loadPage("get_user.html?UserID=" + UserID);
                    //BindUserDetailsForEdit('MethodName=GetDetailOfUserForEdit&UserID=' + UserID);
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblEditUser').html(data.message);
                    //$("#lblEditUser").css("display", "block");
                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    $('#btnSubmitEditPwd').on("click", function() {
        var retval = false;
        var ans = check_itemsvalidate('#divEditPassword input');
        if (ans) {
            UpdatePasswordForUser('MethodName=UpdateUserPassword&UserID=' + UserID + '&Password=' + $("#txtNewPwd").val());
        } else {
            return false;
        }
        return false;
    });

    function UpdatePasswordForUser(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    //$('#lblEditPassword').html(data.message);
                    //$("#lblEditPassword").css("display", "block");
                    $("#txtNewPwd").val('');
                    $("#txtConfNewPwd").val('');
                    mainView.loadPage("get_user.html?UserID=" + UserID);
                    //BindUserDetailsForEdit('MethodName=GetDetailOfUserForEdit&UserID=' + UserID);
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblEditPassword').html(data.message);
                    //$("#lblEditPassword").css("display", "block");
                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    function BindUserUploadInsPdfForms(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $('#divuserUploadPdfForms').empty();
                $('#lblInsuranceForm').html("");
                var items = [];
                if (data.userPdfArray.length > 0) {
                    //alert(data.message);
                    var path = "https://xactbid.pocketofficepro.com/images/icons/pdf_lg.png";
                    var filePath = "https://xactbid.pocketofficepro.com/insuranceform/";
                    items.push("<table>");
                    for (var i = 0; i < data.userPdfArray.length; i++) {
                        var fileFullPath = filePath + data.userPdfArray[i].pdfname;
                        items.push("<tr class='pdf_form'><td><a href=" + fileFullPath + "><img title=" + fileFullPath + " class='viewInsuranceForm' src=" + path + " /></a><br /><a id=aviewInsuranceForm_" + data.userPdfArray[i].pdf_id + " class='viewInsuranceForm' title=" + fileFullPath + " href=" + fileFullPath + "> " + data.userPdfArray[i].pdfname + "</a><br />" + data.userPdfArray[i].datecreated + "</td></tr>");
                    }
                    items.push("</table>");
                    $('#divuserUploadPdfForms').append(items.join(''));
                    $(".viewInsuranceForm").on("click", function() {
                        var FileName = this.title;
                        window.open(FileName, '_system');
                        return false;

                    });
                } else {
                    //alert(data.message);
                    $('#lblInsuranceForm').html("No Insurance Form uploded");
                    $("#lblInsuranceForm").css("display", "block");
                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    $('#btnDeleteUser').on("click", function() {
        if (confirm('Are you sure?')) {
            DeleteUserByUserId('MethodName=DeleteUserById&UserID=' + UserID);
            return false;
        } else { return false; }
    });

    function DeleteUserByUserId(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    mainView.loadPage("users.html");
                    //BindUserDetailsForEdit('MethodName=GetDetailOfUserForEdit&UserID=' + UserID);
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblDeleteUser').html(data.message);
                    //$("#lblDeleteUser").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    function BindEditUserPermissions(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $('#divBindEditUserPerm').empty();
                var items = [];
                if (data.userPermArray.length > 0) {
                    //alert(data.message);
                    var imgPath = "https://xactbid.pocketofficepro.com/images/icons/delete.png";
                    items.push("<table><tr><td colspan='4'><b>Modules:</b><span class='smallnote'><br />A = Access<br />O = Ownership required<br /><font color='red'>Exception to Group<br /></font></span></td></tr><tr><td colspan='4'>&nbsp;</td></tr><tr style='float: left; width:100%'><td style='float: left; margin-left: 6px; width: 7%;'><b>A</b></td><td style='width: 5%; float: left; text-align: left;'><b>O</b></td><td colspan='2'>&nbsp;</td></tr>");

                    for (var i = 0; i < data.userPermArray.length; i++) {
                        //var RoundVal = Math.round(data.userPermArray.length / 2);

                        if (data.userPermArray[i].exception_id == '' || data.userPermArray[i].exception_id == null) {

                            var accesChecked = '';
                            var accessAction = "add_ex_on";
                            items.push("<tr style='width:45%; float:left; margin-left: 5px;  font-size: 13px;'>");
                            if (data.userPermArray[i].module_access_id != '' && data.userPermArray[i].module_access_id != null) {
                                accesChecked = "checked";
                                accessAction = "add_ex_off";
                            }
                            items.push("<td><input type='checkbox' class='chkAccessPerm' value=" + accessAction + " checked=" + accesChecked + " name='chkAccessPerm_" + data.userPermArray[i].module_id + "' id='chkAccessPerm_" + data.userPermArray[i].module_id + "' ></td>");

                            var ownerDisabled = 'disabled=disabled';
                            var checked_owner = '';
                            var ownerAction = "add_ex_ownon";
                            var IsChecked = "";
                            if (data.userPermArray[i].access_ownership == 1) {
                                checked_owner = "checked";
                                ownerAction = "add_ex_ownoff";
                                IsChecked = "checked:checked";
                            }

                            if (data.userPermArray[i].ownership_enabled == 1) {
                                //items.push("<td><input type='checkbox' class='chkOwnerShip' enabled='enabled'   value=" + ownerAction + "  IsChecked name='chkOwnerShip_" + data.userPermArray[i].module_id + "' id='chkOwnerShip_" + data.userPermArray[i].module_id + "' ></td>");
                                items.push("<td><input type='checkbox' class='chkOwnerShip' enabled='enabled'   value=" + ownerAction + "  IsChecked   name='chkOwnerShip_" + data.userPermArray[i].module_id + "' id='chkOwnerShip_" + data.userPermArray[i].module_id + "' ></td>");
                            } else {
                                items.push("<td><input type='checkbox' class='chkOwnerShip' disabled='disabled'   value=" + ownerAction + "  IsChecked   name='chkOwnerShip_" + data.userPermArray[i].module_id + "' id='chkOwnerShip_" + data.userPermArray[i].module_id + "' ></td>");
                            }
                            //items.push("<td><input type='checkbox' class='chkOwnerShip'  value=" + ownerAction + "  checked=" + checked_owner + "  name='chkOwnerShip_" + data.userPermArray[i].module_id + "' id='chkOwnerShip_" + data.userPermArray[i].module_id + "' ></td>");
                            items.push("<td colspan=2>" + data.userPermArray[i].title + "</td></tr>");
                        } else {

                            items.push("<tr style='width:45%; float:left;'>");
                            var checked_access = '';
                            var action_access = "edit_ex_on";
                            if (data.userPermArray[i].exception_onoff != 1) {
                                checked_access = "checked:checked";
                                action_access = "edit_ex_on";
                            }
                            items.push("<td><input type='checkbox' disabled='disabled' class='chkEditAccessPerm' value=" + action_access + "  checked_access   name='chkEditAccessPerm_" + data.userPermArray[i].module_id + "' id='chkEditAccessPerm_" + data.userPermArray[i].module_id + "' ></td>");

                            var editChecked_owner = '';

                            if (data.userPermArray[i].exception_ownership == 1) { editChecked_owner = "checked:checked"; }

                            items.push("<td><input type='checkbox' class='chkEditOwnerShip' disabled='disabled'  editChecked_owner   name='chkEditOwnerShip_" + data.userPermArray[i].module_id + "' id='chkEditOwnerShip_" + data.userPermArray[i].module_id + "' ></td>");
                            items.push("<td><a href='javascript:;' id='aLinkDelRights_" + data.userPermArray[i].module_id + "'><img id='imgDelRights_" + data.userPermArray[i].module_id + "' class='imgDelUserPerm' alt='del' src=" + imgPath + " border='0'style='margin:0px 10px 0px 0px !important;'></a></td><td>" + data.userPermArray[i].title + "</td></tr>");
                        }

                    }
                    items.push("</table>");
                    $('#divBindEditUserPerm').append(items.join(''));

                    $(".chkAccessPerm").on("click", function() {
                        var accessActionName = this.value;
                        var splits_id = this.id.split('_');
                        var moduleId = splits_id[1];
                        if ((moduleId != null || moduleId != "" || moduleId != "undefined") && accessActionName != "") {
                            BindUserPermAfterActionPerform('MethodName=UpdateUserPermissionByAction&ModuleId=' + moduleId + '&Action=' + accessActionName + '&UserID=' + UserID);
                        }
                        return false;
                    });
                    $(".chkOwnerShip").on("click", function() {
                        var ownerActionName = this.value;
                        var splits_id = this.id.split('_');
                        var moduleId = splits_id[1];
                        if ((moduleId != null || moduleId != "" || moduleId != "undefined") && ownerActionName != "") {
                            BindUserPermAfterActionPerform('MethodName=UpdateUserPermissionByAction&ModuleId=' + moduleId + '&Action=' + ownerActionName + '&UserID=' + UserID);
                        }
                        return false;
                    });
                    $(".imgDelUserPerm").on("click", function() {
                        var delActionName = "del_ex";
                        var splits_id = this.id.split('_');
                        var moduleId = splits_id[1];
                        if ((moduleId != null || moduleId != "" || moduleId != "undefined") && delActionName != "") {
                            BindUserPermAfterActionPerform('MethodName=UpdateUserPermissionByAction&ModuleId=' + moduleId + '&Action=' + delActionName + '&UserID=' + UserID);
                        }
                        return false;
                    });
                } else {
                    //alert(data.message);
                    $('#lblUserEditPerm').html("No record found for User Permission");
                    $("#lblUserEditPerm").css("display", "block");
                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    function BindUserPermAfterActionPerform(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    BindEditUserPermissions('MethodName=GetUsersEditPermissions&UserID=' + UserID);
                } else {
                    //alert(data.message);
                    $('#lblUserEditPerm').html(data.message);
                    $("#lblUserEditPerm").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    function BindStagesForAdvanceAccess(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);
                $('#divStageAdvanceAccess').empty();
                var items = [];
                items.push("<table>");
                var arrcheckk = [];

                for (var i = 0; i < data.stageArray.length; i++) {
                    var uclass = "";
                    var IsChecked = "";
                    var exception = false;
                    var StageID = data.stageArray[i].stage_id;
                    arrcheckk[i] = "";
                    if (data.stage_accessArray.length > 0) {
                        for (var j = 0; j < data.stage_accessArray.length; j++) {

                            var stageAccessId = data.stage_accessArray[j].stage_id;
                            if (stageAccessId == StageID) {
                                arrcheckk[i] = ("checked='checked'");
                                if (data.user_stage_accessArray.length > 0) {
                                    for (var k = 0; k < data.user_stage_accessArray.length; k++) {
                                        if (data.user_stage_accessArray[k].stage_id == StageID) {
                                            if (data.user_stage_accessArray[k].has_access == 0) {
                                                arrcheckk[i] = "";
                                                exception = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (data.user_stage_accessArray.length > 0) {
                        for (var k = 0; k < data.user_stage_accessArray.length; k++) {
                            if (data.user_stage_accessArray[k].stage_id == StageID) {
                                if (data.user_stage_accessArray[k].has_access == 1) {
                                    arrcheckk[i] = ("checked='checked'");
                                    exception = true;
                                }
                            }
                        }
                    }

                    items.push("<tr><td><input type='checkbox' class='chkStageAdvncAccess' " + arrcheckk[i].toString() + "  id='chkStageAdvncAccess_" + data.stageArray[i].stage_id + "' ></td><td><b>" + data.stageArray[i].stage_num + "</b>.&nbsp;</td><td>" + data.stageArray[i].stage + "</td></tr>");
                }
                items.push("</table>");
                $('#divStageAdvanceAccess').append(items.join(''));
                $(".chkStageAdvncAccess").on("click", function() {
                    var splits_id = this.id.split('_');
                    var stageId = splits_id[1];
                    if (stageId != null || stageId != "" || stageId != "undefined") {
                        BindStageAdvcAccessStatus('MethodName=UpdateUserStageAdvAccessStatus&stageId=' + stageId + '&UserID=' + UserID);
                    }
                    return false;
                });

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })

    }

    function BindStageAdvcAccessStatus(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    BindStagesForAdvanceAccess('MethodName=GetAllStagesForEditUser&UserID=' + UserID);
                } else {
                    //alert(data.message);
                    $('#lblStageAdvAccess').html(data.message);
                    $("#lblStageAdvAccess").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    function BindAllStageNotification(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);
                $('#divStageNofification').empty();
                var items = [];
                var mydata = data.stageNotifyArray;
                navigator.notification.alert(
                    mydata, alertDismissed, "Successful", "Done"
                );
                if (data.stageNotifyArray.length > 0) {
                    items.push("<table><tr><td><b>E</b></td><td><b>T</b></td><td>&nbsp;</td><td>&nbsp;</td></tr>");
                    var checked_Email = [];
                    var checked_Sms = [];

                    for (var i = 0; i < data.stageNotifyArray.length; i++) {
                        checked_Email[i] = "";
                        checked_Sms[i] = "";
                        var StageNum = data.stageNotifyArray[i].stage_num;
                        for (var j = 0; j < data.emailNotifyArray.length; j++) {
                            if (data.emailNotifyArray[j].stage_num == StageNum) {
                                checked_Email[i] = "checked='checked'";
                            }
                        }
                        for (var k = 0; k < data.smsNotifyArray.length; k++) {
                            if (data.smsNotifyArray[k].stage_num == StageNum) {
                                checked_Sms[i] = "checked='checked'";
                            }
                        }
                        items.push("<tr><td><input type='checkbox' value='emailAction' class='chkStageEmailNotify' " + checked_Email[i].toString() + " id='chkStageEmailNotify_" + data.stageNotifyArray[i].stage_num + "' ></td><td><input type='checkbox' value='smsAction' class='chkStagesSmslNotify' " + checked_Sms[i].toString() + "  id='chkStagesSmslNotify_" + data.stageNotifyArray[i].stage_num + "' ></td><td><b>" + data.stageNotifyArray[i].stage_num + "</b>.&nbsp;</td><td>" + data.stageNotifyArray[i].stage + "</td></tr>");
                    }

                } else {
                    $("#lblStageNotify").html("No Stages");
                    $("#lblStageNotify").css("display", "block");
                }

                items.push("</table>");
                $('#divStageNofification').append(items.join(''));
                $(".chkStageEmailNotify").on("click", function() {
                    var splits_id = this.id.split('_');
                    var stageNum = splits_id[1];
                    var action = "emailAction";

                    if (stageNum != null || stageNum != "" || stageNum != "undefined") {
                        BindStageNotiAfterUpdate('MethodName=UpdateUserStageNotificationStatus&action=' + action + '&stageNum=' + stageNum + '&UserID=' + UserID);
                    }
                    return false;
                });
                $(".chkStagesSmslNotify").on("click", function() {
                    var splits_id = this.id.split('_');
                    var stageNum = splits_id[1];
                    var action = "smsAction";

                    if (stageNum != null || stageNum != "" || stageNum != "undefined") {
                        BindStageNotiAfterUpdate('MethodName=UpdateUserStageNotificationStatus&action=' + action + '&stageNum=' + stageNum + '&UserID=' + UserID);
                    }
                    return false;
                });

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })

    }

    function BindStageNotiAfterUpdate(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    BindAllStageNotification('MethodName=BindStageNotificationDetails&UserID=' + UserID);
                } else {
                    //alert(data.message);
                    $('#lblStageNotify').html(data.message);
                    $("#lblStageNotify").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }
    $("#btnuploadinsuranceform").on("click", function() {

        var UserID = page.query.UserID;
        if ($('#fuinsurancefrm').val() != "") {
            // alert($('#fludoc').prop('files').length);
            var file_data = $('#fuinsurancefrm').prop('files')[0];
            var form_data = new FormData();

            form_data.append('flag', '3');
            form_data.append('userid', UserID);

            form_data.append('file', file_data);
            $.when($.ajax({
                url: 'https://xactbid.pocketofficepro.com/fileuploader.php', // point to server-side PHP script
                dataType: 'text', // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function(message) {
                    navigator.notification.alert(
                        message, alertDismissed, "Successful", "Done"
                    );
                    $('#fuinsurancefrm').val("");
                },
                error: function(data) {
                    //alert('err' + data);
                    var myerr = 'err' + data;
                    navigator.notification.alert(
                        myerr, alertDismissed, "An error occured", "Done"
                    );
                },
            }).then(function() {
                BindUserUploadInsPdfForms('MethodName=UserInsurancepdfUpload&UserID=' + UserID);
            }));



            $("#divMainEditUser").css("display", "block");

        } else {
            navigator.notification.alert(
                "No files selected", alertDismissed, "Unsuccessful", "Done"
            );
        }
        return false;

    });

    $("#btnEditUserBack").on("click", function() {
        mainView.loadPage("get_user.html?UserID=" + UserID);
        return false;
    });

    function DateCallJS() {
        var $ = jQuery.noConflict();
        $('.datestamp').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: "1925:2999",
            onClose: function() {
                if (this.value != '') {
                    $.validationEngine.loadValidation('.datestamp');
                }
            }
        });
    }
});

myApp.onPageInit('get_Appoinment', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    var apptJobId = page.query.JId;
    var apptId = page.query.id;
    var srcPage = page.query.srcPage;
    var date = new Date();

    $('#txtApptdate').val(formatDate(date));
    if (srcPage != null && srcPage != "" && srcPage != "undefined") {
        $("#alinkViewJob").show();
        $("#alinkDelAppt").hide();
    } else {
        $("#alinkDelAppt").show();
        $("#alinkViewJob").hide();
    }

    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }
    DateCallJS();

    if (apptJobId != null && apptJobId != "" && apptJobId != "undefined") {
        if (apptId != null && apptId != "" && apptId != "undefined") {
            BindViewAppoinmentDetail('MethodName=ViewAppoinmentDetail&ApptID=' + apptId);
            $("#divAddApppointment").css("display", "none");
            $("#divViewApptDetail").css("display", "block");
            $("#divHeadForGetAppointment").html("");
            $("#divHeadForGetAppointment").html("View Appointment Detail");
        } else {
            bindAppointmentTimeList('MethodName=getAppointmentTimeList');
            $("#divAddApppointment").css("display", "block");
            $("#divViewApptDetail").css("display", "none");
            $("#divHeadForGetAppointment").html("");
            $("#divHeadForGetAppointment").html("Add Appointment");
        }
    }

    function BindViewAppoinmentDetail(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];
                if (data.apptArray.length > 0) {
                    for (var i = 0; i < data.apptArray.length; i++) {
                        items.push($("#lblApptTitle").text(data.apptArray[i].title) + $("#lblApptCreator").text(data.apptArray[i].Creator) + $("#lblApptDate").text(data.apptArray[i].datetime) + $("#lblApptTime").text(data.apptArray[i].jobTime) + $("#lblAppJobNum").text(data.apptArray[i].job_number) + $("#lblApptSalesman").text(data.apptArray[i].salesman) + $("#lblApptDesc").text(data.apptArray[i].text) + $("#lblApptCreated").text(data.apptArray[i].Fulltimestamp));
                    }
                    return false;
                } else {
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    $(".aStageDelete").on("click", function() {
        if (confirm('Are you sure?')) {
            DeleteAppointmentByAptID('MethodName=RemoveAppointmentByID&ApptId=' + apptId);
            return false;
        } else {
            return false;
        }
    });

    function DeleteAppointmentByAptID(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    if (srcPage != null && srcPage != "" && srcPage != "undefined") {

                        mainView.loadPage(srcPage);
                    } else {
                        mainView.loadPage("jobdetails.html?JId=" + apptJobId);
                    }

                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblviewAppDetail').html(data.message);
                    //$("#lblviewAppDetail").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    function DateCallJS() {
        var $ = jQuery.noConflict();
        $('.datestamp').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: "1925:2999",
            onClose: function() {
                if (this.value != '') {
                    $.validationEngine.loadValidation('.datestamp');
                }
            }
        });
    }

    $("#btnSaveAppointment").on("click", function() {
        var retval = false;
        var ans = check_itemsvalidate('#divAddApppointment input');
        if (ans) {
            BindAddAppointDetail('MethodName=AddAppointmentDetails&JobID=' + apptJobId + '&date=' + $("#txtApptdate").val() + '&time=' + $('select[name=time]').val() + '&title=' + $("#txtApptTitle").val() + '&description=' + $("#txtApptDesc").val());
        } else { return false; }
    });

    function BindAddAppointDetail(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    //$('#lblAddApppointment').html(data.message);
                    //$("#lblAddApppointment").css("display", "block");
                    mainView.loadPage("jobdetails.html?JId=" + apptJobId);
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblAddApppointment').html(data.message);
                    //$("#lblAddApppointment").css("display", "block");
                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    function bindAppointmentTimeList(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data) {
                var data = $.parseJSON(data);
                $("#ddlTimeList").empty();
                if (data.timeList.length > 0) {
                    $("#ddlTimeList").append(data.timeList);
                    return false;
                } else {
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {

                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    $("#btnCancelAppt").on("click", function() {
        jQuery(".formError").remove();
        mainView.loadPage("jobdetails.html?JId=" + apptJobId);
        return false;
    });


    $("#btnViewApptBack").on("click", function() {

        if (srcPage != null && srcPage != "" && srcPage != "undefined") {

            mainView.loadPage(srcPage);
        } else {
            mainView.loadPage("jobdetails.html?JId=" + apptJobId);
        }
        return false;
    });
    $("#alinkViewJob").on("click", function() {

        mainView.loadPage("jobdetails.html?JId=" + apptJobId);

        return false;
    });
});

myApp.onPageInit('get_jobmessages', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    var jobId = page.query.JId;
    BindJobMessageHistory('MethodName=GetJobMessageHistory&jobId=' + jobId);

    function BindJobMessageHistory(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.strJobMsgHistory != null) {
                    $('#divBindJobMsgHistory').empty();
                    $("#divBindJobMsgHistory").append(data.strJobMsgHistory);
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    $('#btnbackviewjobmsghistory').on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + jobId);
        return false;
    });

    $('.viewJobMsgHistory').on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + jobId);
        return false;
    });

});

myApp.onPageInit('get_repair', function(page) {
    var $ = jQuery.noConflict();
    var srcPage = page.query.srcPage;
    jQuery(".formError").remove();
    //if (!checkJobModuleAccess('add_repair'))
    //{ $("#divMainAddRepair").css("display", "none"); }
    //else
    //{ $("#divMainAddRepair").css("display", "block"); }

    //if (!checkJobModuleAccess('view_repair'))
    //{ $("#divMainEditRepair").css("display", "none"); }
    //else
    //{ $("#divMainEditRepair").css("display", "block"); }

    //if (!Checkaccess('edit_repair'))
    //{ $("#btnEditRepairDetail").css("display", "none"); }
    //else
    //{ $("#btnEditRepairDetail").css("display", "block"); }

    //if (!Checkaccess('delete_repair'))
    //{ $("#btnDelRepairDetail").css("display", "none"); }
    //else
    //{ $("#btnDelRepairDetail").css("display", "block"); }

    //if (!checkJobModuleAccess('edit_repair'))
    //{ $("#divMainEditRepair").css("display", "none"); }
    //else
    //{ $("#divMainEditRepair").css("display", "block"); }

    DateCallJS();

    function DateCallJS() {
        var $ = jQuery.noConflict();
        $('.datestamp').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: "1925:2999",
            onClose: function() {
                if (this.value != '') {
                    $.validationEngine.loadValidation('.datestamp');
                }
            }
        });
    }
    var repairJobId = page.query.JId;
    if (repairJobId != null && repairJobId != "" && repairJobId != "undefined") {
        BindAddReapirDddLists('MethodName=GetAddJobRepairDDList');
        $("#divMainAddRepair").css("display", "block");
        $("#divMainEditRepair").css("display", "none");
        $("#divGetRepairMainHeader").html("");
        $("#divGetRepairMainHeader").html("Add Job Repair");
    } else {
        $("#divMainAddRepair").css("display", "none");
        mainView.loadPage("jobs.html");
    }

    var repairEditJobId = page.query.id;
    if (repairEditJobId != null && repairEditJobId != "" && repairEditJobId != "undefined") {
        BindRepairJobDetailForView('MethodName=GetRepairJobDetailForView&RepairEditJobID=' + repairEditJobId);
        $("#divMainAddRepair").css("display", "none");
        $("#divMainEditRepair").css("display", "block");
        $("#divViewRepairDetail").css("display", "block");
        $("#divEditRepairDetail").css("display", "none");
        $("#divGetRepairMainHeader").html("");
        $("#divGetRepairMainHeader").html("View Repair Detail");
    } else {
        $("#divMainEditRepair").css("display", "none");
        mainView.loadPage("jobs.html");
    }

    function BindAddReapirDddLists(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);
                $("#ddlFallType").empty();
                for (var i = 0; i < data.failTypes.length; i++) {
                    $("#ddlFallType").append($("<option value='" + data.failTypes[i].fail_type_id + "'>" + data.failTypes[i].fail_type + "</option>"));
                }
                $("#ddlPriority").empty();
                for (var i = 0; i < data.priorities.length; i++) {
                    $("#ddlPriority").append($("<option value='" + data.priorities[i].priority_id + "'>" + data.priorities[i].priority + "</option>"));
                }
                $("#ddlContractor").empty();
                $("#ddlContractor").append("<option value=''></option>");
                for (var i = 0; i < data.contractors.length; i++) {
                    var CTitle = '';
                    if (data.contractors[i].dba != null && data.contractors[i].dba != "") {
                        CTitle = data.contractors[i].dba + "&nbsp;(" + data.contractors[i].lname + ")";
                    } else { CTitle = data.contractors[i].select_label; + "&nbsp;(" + data.contractors[i].lname + ")"; }
                    $("#ddlContractor").append($("<option value='" + data.contractors[i].user_id + "'>" + CTitle + "</option>"));
                }
                return false;
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    $("#btnAddRepairJob").on("click", function() {

        var retval = false;
        var ans = check_itemsvalidate('#divMainAddRepair input');
        if (ans) {
            BindAddRepairJob('MethodName=SavedAddRepairJob&JobID=' + repairJobId + '&failType=' + $("#ddlFallType").val() + '&priority=' + $("#ddlPriority").val() + '&contractor=' + $("#ddlContractor").val() + '&notes=' + $("#txtARNotes").val());
        } else { return false; }
    });

    function BindAddRepairJob(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    mainView.loadPage("jobdetails.html?JId=" + repairJobId);
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblAddRepair').html(data.message);
                    //$("#lblAddRepair").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    $("#btnCancelAddRepairJob").on("click", function() {
        jQuery(".formError").remove();
        mainView.loadPage("jobdetails.html?JId=" + repairJobId);
        return false;
    });

    function BindRepairJobDetailForView(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];
                if (data.repairJobArray.length > 0) {
                    for (var i = 0; i < data.repairJobArray.length; i++) {

                        items.push($("#lblViewFailType").text(data.repairJobArray[i].fail_type) + $("#lblViewPriority").text(data.repairJobArray[i].priority) + $("#lblViewCreator").text(data.repairJobArray[i].Creator) + $("#lblViewDOB").text(data.repairJobArray[i].timestamp) + $("#lblViewContractor").text(data.repairJobArray[i].contractor) + $("#lblViewDBA").text(data.repairJobArray[i].dba) + $("#lblViewNotes").text(data.repairJobArray[i].notes));
                        if (data.repairJobArray[i].startdate != null && data.repairJobArray[i].startdate != "") {
                            items.push($("#lblViewStartDt").text(data.repairJobArray[i].startdate));
                        } else {
                            items.push($("#lblViewStartDt").html("<span style='color: red;'>Not Scheduled</span>"));
                        }
                    }
                    if (data.myRepair.completed != '' && data.myRepair.completed != null) {

                        $('#trJobRepairCompleted').css("display", "block");
                        $('#lblCompletedTime').html("<b>Completed</b><br />in <b>" + data.myRepair.total_length + "</b> day(s) <br /> on " + data.myRepair.completed + "");
                    } else { $('#trJobRepairCompleted').css("display", "none"); }

                } else {
                    $("#divViewRepairDetail").css("display", "none");
                    $("#divEditRepairDetail").css("display", "none");
                    $("#lblMainEditRepair").html("");
                    $("#lblMainEditRepair").html("Detail not found!");
                    $("#lblMainEditRepair").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    $("#btnCancelRepairDetail").on("click", function() {
        jQuery(".formError").remove();
        if (srcPage != null && srcPage != "" && srcPage != "undefined") {
            mainView.loadPage(srcPage);

        } else {
            mainView.loadPage("jobdetails.html?JId=" + repairJobId);
        }

        return false;
    });

    $("#btnDelRepairDetail").on("click", function() {
        if (confirm('Are you sure?')) {
            RemoveCurrentRepairJobID('MethodName=DeleteRepairJobByRepairId&RepairDelJobID=' + repairEditJobId);
            //mainView.loadPage("jobdetails.html?JId=" +);
            return false;
        } else {
            return false;
        }
    });

    function RemoveCurrentRepairJobID(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    mainView.loadPage("jobdetails.html?JId=" + data.job_id);
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblMainEditRepair').html(data.message);
                    //$("#lblMainEditRepair").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    $("#btnCancelRepairJob").on("click", function() {
        //var splits_id = this.id.split('_');
        //var RepairJobId = splits_id[1];
        jQuery(".formError").remove();
        mainView.loadPage("jobdetails.html?JId=" + repairJobId);
        return false;
    });

    $("#btnEditRepairDetail").on("click", function() {

        $("#divMainAddRepair").css("display", "none");
        $("#divMainEditRepair").css("display", "block");
        $("#divViewRepairDetail").css("display", "none");
        $("#divEditRepairDetail").css("display", "block");
        $("#divGetRepairMainHeader").html("");
        $("#divGetRepairMainHeader").html("Edit Repair");
        BindEDITReapirDddLists('MethodName=GetAddJobRepairDDList');
        BindRepairJobDetailForEDIT('MethodName=GetRepairJobDetailForView&RepairEditJobID=' + repairEditJobId);
        return false;
    });
    $("#btnEditJobRepair").on("click", function() {

        var retval = false;
        var ans = check_itemsvalidate('#divEditRepairDetail input');

        if (ans) {

            if ($('#chkForSchedule').is(":checked") && $("#txtJobStartDt").val().trim() == '') {
                navigator.notification.alert(
                    "Enter schedule date", alertDismissed, "Successful", "Done"
                );
            } else {
                var chkSchedule = 0;
                var chkCompleted = 0;

                if ($('#chkForSchedule').is(":checked")) {
                    chkSchedule = 1;
                }
                if ($('#chkForCompleted').is(":checked")) {
                    chkCompleted = 1;
                }

                SaveRepairJobDetailForEDIT('MethodName=SaveRepairJobEditDetail&RepairEditJobID=' + repairEditJobId + '&fail_type=' + $("#ddlEditFallType").val() + '&priority=' + $("#ddlEditPriority").val() + '&contractor=' + $("#ddlEditContractor").val() + '&startDate=' + $("#txtJobStartDt").val() + '&completed=' + chkCompleted + '&notes=' + $("#txtRJEditNotes").val() + '&isComplete=' + chkSchedule);
            }
            return false;
        } else { return false; }
    });

    function BindEDITReapirDddLists(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);
                $("#ddlEditFallType").empty();
                for (var i = 0; i < data.failTypes.length; i++) {
                    $("#ddlEditFallType").append($("<option value='" + data.failTypes[i].fail_type_id + "'>" + data.failTypes[i].fail_type + "</option>"));
                }
                $("#ddlEditPriority").empty();
                for (var i = 0; i < data.priorities.length; i++) {
                    $("#ddlEditPriority").append($("<option value='" + data.priorities[i].priority_id + "'>" + data.priorities[i].priority + "</option>"));
                }
                $("#ddlEditContractor").empty();
                for (var i = 0; i < data.contractors.length; i++) {
                    var CTitle = '';
                    if (data.contractors[i].dba != null && data.contractors[i].dba != "") {
                        CTitle = data.contractors[i].dba + "&nbsp;(" + data.contractors[i].lname + ")";
                    } else { CTitle = data.contractors[i].select_label; + "&nbsp;(" + data.contractors[i].lname + ")"; }
                    $("#ddlEditContractor").append($("<option value='" + data.contractors[i].user_id + "'>" + CTitle + "</option>"));
                }
                return false;
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    function BindRepairJobDetailForEDIT(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];
                if (data.repairJobArray.length > 0) {
                    for (var i = 0; i < data.repairJobArray.length; i++) {

                        items.push($("#ddlEditFallType").val(data.repairJobArray[i].failTypeVal) + $("#ddlEditPriority").val(data.repairJobArray[i].propVal) + $("#ddlEditContractor").val(data.repairJobArray[i].contVal) + $("#txtRJEditNotes").text(data.repairJobArray[i].notes));

                        if (data.repairJobArray[i].startdate != null && data.repairJobArray[i].startdate != "") {
                            items.push($('#chkForSchedule').attr('checked', true));
                            $('#divStartDate').css("display", "block");
                            $('#txtJobStartDt').text(data.repairJobArray[i].startdate);
                            $('.datestamp').datepicker("setDate", new Date(data.repairJobArray[i].startdate));
                        }
                        if (data.repairJobArray[i].completed != null && data.repairJobArray[i].completed != "") {
                            items.push($('#chkForCompleted').attr('checked', true));
                        }
                    }

                } else {
                    $("#divViewRepairDetail").css("display", "none");
                    $("#divEditRepairDetail").css("display", "none");
                    $("#lblMainEditRepair").html("");
                    $("#lblMainEditRepair").html("Detail not found!");
                    $("#lblMainEditRepair").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    function SaveRepairJobDetailForEDIT(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    mainView.loadPage("jobdetails.html?JId=" + data.job_id);
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblMainEditRepair').html(data.message);
                    //$("#lblMainEditRepair").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    $("#chkForSchedule").change(function() {
        if (this.checked) {
            $('#divStartDate').css("display", "block");
        } else {
            $('#divStartDate').css("display", "none");
        }
    });

});

myApp.onPageInit('job_invoice', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    var jobId = page.query.JId;
    BindJobInvoiceForAll('MethodName=GetJobInvoiceForAll&JobID=' + jobId);

    $("#btnAddJobInv").on("click", function() {
        var retval = false;
        var ans = check_itemsvalidate('#divAddJobInvoice input');

        if (ans) {
            ReBindAllJobInvoices('MethodName=AddNewJobInvoice&JobId=' + jobId + '&InvDesc=' + $("#txtJobInvDesc").val() + '&InvAmt=' + $("#txtJobInvAmt").val() + '&InvType=' + $("#lstJobInvType").val());
        }
        return false;
    });

    function ReBindAllJobInvoices(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#txtJobInvDesc").val('');
                    $("#txtJobInvAmt").val('');
                    $("#lstJobInvType").val('charge')
                    BindJobInvoiceForAll('MethodName=GetJobInvoiceForAll&JobID=' + jobId);
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblAddJobInvoice').html(data.message);
                    //$("#lblAddJobInvoice").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        });
        return false;
    }

    $("#btnCancelJobInv").on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + jobId);
        return false;
    });

    $("#lnkjobInvoiceback").on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + jobId);
        return false;
    });

    function BindJobInvoiceForAll(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];
                var items2 = [];
                if (data.status == "1") {
                    if (data != null) {
                        $('#lstBindJobInvoice').empty();
                        items.push('<table  class="table"><tr><th>Description</th><th>Credit</th><th>Charge</th><th>Actions</th></tr>');
                        if (data.charges.length > 0); {
                            for (var i = 0; i < data.charges.length; i++) {
                                items.push('<tr><td>' + data.charges[i].note + '</td><td>&nbsp;</td><td style="color: red;">' + data.charges[i].amount + '</td><td><a class=aDelJobInvChargeItem id="aDelJobInvChargeItem_' + data.charges[i].charge_id + '"><i class="icon-remove"></i></a></td></tr>');
                            }
                        }
                        if (data.credits.length > 0) {
                            for (var i = 0; i < data.credits.length; i++) {
                                items.push('<tr><td>' + data.credits[i].note + '</td><td style="color: green;">(' + data.credits[i].amount + ')</td><td>&nbsp;</td><td><a class=aDelJobInvCreditItem id="aDelJobInvCreditItem_' + data.credits[i].credit_id + '"><i class="icon-remove"></i></a></td></tr>');
                            }
                        }
                        //if (data.totalCharges == "0")
                        //{ data.totalCharges = "0.00";}
                        items.push('<tr><td><b>Total:</b></td><td style="color: green;">(<b>' + data.totalCredits + '</b>)</td><td colspan=2 style="color: red;"><b>' + data.totalCharges + '</b></td></tr>');
                        items.push('<tr><td colspan=2><b>Balance:</b></td><td colspan=2><b>' + data.balance + '</b></td></tr>');
                        items.push('</table>');
                        $("#divOfficeList").empty();
                        if (data.resOffice.length > 0) {
                            items2.push('<select class="cos_select_box"><option value="">Default</option>');
                            var selected = '';
                            for (var i = 0; i < data.resOffice.length; i++) {
                                if (data.resOffice[i].office_id == data.resUser.office_id) {
                                    IsSelected = "selected";
                                }
                                items2.push("<option  value='" + data.resOffice[i].office_id + "'>" + data.resOffice[i].title + "</option>");
                            }
                            $("#divOfficeList").append(items2.join(''));
                        }
                    }
                    $('#lstBindJobInvoice').append(items.join(''));

                    $(".aDelJobInvChargeItem").on("click", function() {
                        if (confirm('Are you sure?')) {
                            var splits_id = this.id.split('_');
                            var InvTypeID = splits_id[1];
                            BindJobInvAfterDelete('MethodName=DeleteJobInvoiceByTypeID&JobID=' + jobId + '&InvType=charge&InvTypeID=' + InvTypeID);
                            return false;
                        } else { return false; }
                    });
                    $(".aDelJobInvCreditItem").on("click", function() {
                        var splits_id = this.id.split('_');
                        var InvTypeID = splits_id[1];
                        BindJobInvAfterDelete('MethodName=DeleteJobInvoiceByTypeID&JobID=' + jobId + '&InvType=credit&InvTypeID=' + InvTypeID);
                        return false;
                    });
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    $('#lblJobInvErrMsg').html(data.message);
                    $('#lblJobInvErrMsg').css("display", "block");
                    $("#divAddJobInvoice").css("display", "none");
                    $("#divListJobInvoice").css("display", "none");
                    $("#divOfficeListMain").css("display", "none");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindJobInvoiceForAll(data);
            }
        });
        return false;
    }

    function BindJobInvAfterDelete(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    BindJobInvoiceForAll('MethodName=GetJobInvoiceForAll&JobID=' + jobId);
                } else {
                    //alert(data.message);
                    BindJobInvoiceForAll('MethodName=GetJobInvoiceForAll&JobID=' + jobId);
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        });
        return false;
    }

});
myApp.onPageInit('jobcontacts', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    var jobId = page.query.JId;
    BindJobContactsForAll('MethodName=BindJobContactsForAll&JobID=' + jobId);
    $("#btnCancelJobInv").on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + jobId);
        return false;
    });

    $("#lnkjobInvoiceback").on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + jobId);
        return false;
    });

    function BindJobContactsForAll(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                if (data!='Job not found') {

                    var data = $.parseJSON(data);
                    var items = [];
                    if (data.status == "1") {
                        if (data != null) {
                            $('#lstBindJobInvoice').empty();
                            items.push('<table  class="table"><tr><th>#Id</th><th>Contact Header</th><th>Contact Note</th><th>Sender</th><th>Contact Time</th></tr>');
                            if (data.totalcontacts.length > 0); {
                                for (var i = 0; i < data.totalcontacts.length; i++) {
                                    
                                    var iin = (i+1);

                                    items.push( '<tr>'+
                                                    '<td>' + iin +  '</td>'+
                                                    '<td>' + data.totalcontacts[i].contact_header +  '</td>'+
                                                    '<td>' + data.totalcontacts[i].contact_note +  '</td>'+
                                                    '<td>' + data.totalcontacts[i].fname +' '+ data.totalcontacts[i].lname +  '</td>'+
                                                    '<td>' + data.totalcontacts[i].created_at +  '</td>'+
                                                '</tr>');
                                }
                            }
                        }
                        $('#lstBindJobInvoice').append(items.join(''));
                    } else {
                        navigator.notification.alert(
                            data.message, alertDismissed, "Unsuccessful", "Done"
                        );
                        $('#lblJobInvErrMsg').html(data.message);
                        $('#lblJobInvErrMsg').css("display", "block");
                        $("#divListJobInvoice").css("display", "none");
                        return false;
                    }
                }else{
                    BindJobContactsForAll('MethodName=BindJobContactsForAll&JobID=' + jobId);
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindJobContactsForAll(data);
            }
        });
        return false;
    }
});
myApp.onPageInit('job_materials', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    //if (!checkJobModuleAccess('job_material_sheet'))
    //{ $("#divMainAddJobMatSheet").css("display", "none"); }
    //else
    //{ $("#divMainAddJobMatSheet").css("display", "block"); }
    DateCallJS();

    function DateCallJS() {
        var $ = jQuery.noConflict();
        $('.datestamp').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: "1925:2999",
            onClose: function() {
                if (this.value != '') {
                    $.validationEngine.loadValidation('.datestamp');
                }
            }
        });
    }

    var jobMatSheetID = page.query.JId;
    if (jobMatSheetID != null && jobMatSheetID != "" && jobMatSheetID != "undefined") {
        BindDdlJobSuppliers('MethodName=GetSupplierForJobMatSheet');
        $("#divMainAddJobMatSheet").css("display", "block");
        $("#divMainEditJobMatSheet").css("display", "none");
        $("#divJobMatMainHeader").html("");
        $("#divJobMatMainHeader").html("Create Material Sheet");

    } else {
        $("#divMainAddJobMatSheet").css("display", "none");
        mainView.loadPage("jobs.html");
    }

    var editJobMatSheetId = page.query.sheet_id;
    var editMatSheetJobId = page.query.job_id;

    if (editJobMatSheetId != null && editJobMatSheetId != "" && editJobMatSheetId != "undefined") {
        BindEDITJobSuppliersDdl('MethodName=GetSupplierForJobMatSheet');
        BindJobMatSheetDetailForEDIT('MethodName=GetJobMatSheetDetailBySheetId&sheet_id=' + editJobMatSheetId + '&job_id=' + editMatSheetJobId);
        bindMaterialsListBySheetID('MethodName=getMaterialsListBySheetID&sheetId=' + editJobMatSheetId);
        GetCatListforMaterial('MethodName=GetCatListforJobMaterial');
        $("#divMainAddJobMatSheet").css("display", "none");
        $("#divMainEditJobMatSheet").css("display", "block");
        $("#divJobMatMainHeader").html("");
        $("#divJobMatMainHeader").html("Modify Material Sheet");
    } else {
        $("#divMainEditJobMatSheet").css("display", "none");
        mainView.loadPage("jobs.html");
    }

    function BindDdlJobSuppliers(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);
                $("#ddlJobMatSupplier").empty();
                $("#ddlJobMatSupplier").append("<option value='0'>None Chosen</option>");
                for (var i = 0; i < data.supplierList.length; i++) {
                    $("#ddlJobMatSupplier").append($("<option value='" + data.supplierList[i].supplier_id + "'>" + data.supplierList[i].supplier + "</option>"));
                }
                $("#lblmatjobsize").empty();
                $("#lblmatjobsize").text(data.job_unit);

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        });
        return false;
    }

    $("#btnAddJobMatSheet").on("click", function() {

        var retval = false;
        var JobID = 0;
        var ans = check_itemsvalidate('#divMainAddJobMatSheet input');
        if (ans) {
            if (editMatSheetJobId != null && editMatSheetJobId != "" && editMatSheetJobId != "undefined") {
                JobID = editMatSheetJobId;
            } else { JobID = jobMatSheetID; }

            BindJobMaterailSheet('MethodName=SavedJobMaterialSheet&JobID=' + JobID + '&supplier=' + $("#ddlJobMatSupplier").val() + '&label=' + $("#txtMatSheetLbl").val() + '&size=' + $("#txtMatSheetJobSize").val() + '&notes=' + $("#txtJMSNotes").val());
        } else { return false; }
    });

    function BindJobMaterailSheet(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    var ParentJobID = 0;
                    if (editMatSheetJobId != null && editMatSheetJobId != "" && editMatSheetJobId != "undefined") {
                        ParentJobID = editMatSheetJobId;
                    } else { ParentJobID = jobMatSheetID; }
                    mainView.loadPage("jobdetails.html?JId=" + ParentJobID);
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblAddJobMatSheet').html(data.message);
                    //$("#lblAddJobMatSheet").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        });
        return false;
    }
    $("#btnCancelJobMatSheet").on("click", function() {
        if (jobMatSheetID != null && jobMatSheetID != "" && jobMatSheetID != "undefined") {
            mainView.loadPage("jobdetails.html?JId=" + jobMatSheetID);
        } else {
            mainView.loadPage("jobdetails.html?JId=" + editMatSheetJobId);
        }
        return false;
    });

    //Below Code for Modify
    function BindEDITJobSuppliersDdl(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $("#ddlEditJobMatSupplier").empty();
                $("#ddlEditJobMatSupplier").append("<option value='0'>None Chosen</option>");
                for (var i = 0; i < data.supplierList.length; i++) {
                    $("#ddlEditJobMatSupplier").append($("<option value='" + data.supplierList[i].supplier_id + "'>" + data.supplierList[i].supplier + "</option>"));
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        });
        return false;
    }

    function BindJobMatSheetDetailForEDIT(data) {
        var $ = jQuery.noConflict();

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];
                if (data.mySheet != null) {
                    $('#txtEditMatSheetLbl').val(data.mySheet.label);
                    $('#lblEditMatSheetJobSize').html(data.mySheet.size + " sq");
                    if (data.mySheet.delivery_date == null || data.mySheet.delivery_date == '') {
                        $('#lblEditDeleDate').html("<span style='color: red;'>Not Scheduled</span>");
                        $('#txtEditDeleDate').val(data.mySheet.delivery_date);
                        $('.datestamp').datepicker("setDate", new Date());
                        $('#chkDoNotSchedule').attr('checked', true);
                    } else {
                        $('#lblEditDeleDate').html(data.mySheet.delivery_date);
                        $('#txtEditDeleDate').val(data.mySheet.delivery_date);
                        $('.datestamp').datepicker("setDate", new Date(data.mySheet.delivery_date));
                    }
                    if (data.mySheet.confirmed != null && data.mySheet.confirmed != '') {
                        $('#chkConfirmed').attr('checked', true);
                        $('#lbljobconfirmDt').html(data.mySheet.confirmed);
                    } else { $('#lbljobconfirmDt').html(""); }

                    $('#ddlEditJobMatSupplier').val(data.mySheet.supplier_id).attr("selected", "selected");

                    if (data.mySheet.supplier_id != 0) {
                        if (data.supplier.length > 0) {
                            for (var i = 0; i < data.supplier.length; i++) {
                                $("#trSupplierDetails").css("display", "block");
                                $("#divSupplierDetails").html("<div class='supplier_det'><label>Contact:</label>" + data.supplier[i].contact + "</div><div class='supplier_det'><label>Email:</label>" + data.supplier[i].email + "</div><div class='supplier_det'><label>Phone:</label>" + data.supplier[i].phone + "</div><div class='supplier_det'><label>Fax:</label>" + data.supplier[i].fax + "</div>");
                            }
                        }
                    }
                    $('#btnviewpdf').click(function() {
                        if (data.filename != '')
                            window.open(data.filename, "_system");
                        return false;
                    });
                    $('#txtEditJMSNotes').val(data.mySheet.notes);
                } else {

                    $("#divMainAddJobMatSheet").css("display", "none");
                    $("#divMainEditJobMatSheet").css("display", "none");
                    $("#divJobMatMainHeader").html("");
                    $("#lblMainEditJobMatSheet").html("Material sheet not found!");
                    $("#lblMainEditJobMatSheet").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        });
    }

    function bindMaterialsListBySheetID(data) {
        try {
            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: 'POST',
                data: data,
                cache: false,
                success: function(data, textStatus, jqxhr) {
                    var data = $.parseJSON(data);
                    $('#divbindMatDetails').empty();
                    var items = [];
                    var total = 0;
                    if (data.matArray.length > 0) {
                        items.push('<table class="table"><tr><th style="width: 28%;">Material</th><th style="width: 5%;">Brand</th><th style="width: 10%;">Color</th><th style="width: 10%;">Unit</th><th style="width: 10%;">Price</th><th style="width: 5%;">Qty</th><th style="width: 10%;">Total</th><th style="width: 22%;">&nbsp;</th></tr>');
                        for (var i = 0; i < data.matArray.length; i++) {
                            total = parseFloat(total) + parseFloat(data.matArray[i].total);
                            total = parseFloat(Math.round(total * 100) / 100).toFixed(2);

                            items.push('<tr><td>' + data.matArray[i].material + '</td><td>' + data.matArray[i].brand + '</td><td>' + data.matArray[i].color + '</td><td>' + data.matArray[i].unit + '</td><td>' + data.matArray[i].price + '</td><td>' + data.matArray[i].quantity + '</td><td>' + data.matArray[i].total + '</td><td class="mins_plus_icon"><a class="aMinusMatQty" href="javascript:;" id="aMinusMatQty_' + data.matArray[i].sheet_item_id + '"><i class="icon icon-minus"></i></a>&nbsp;&nbsp;<a class="aPlusMatQty" href="javascript:;" id="aPlusMatQty_' + data.matArray[i].sheet_item_id + '"><i class="icon icon-plus"></i></a>&nbsp;&nbsp;<a class="aRemoveSheetItem" href="javascript:;" id="aRemoveSheetItem_' + data.matArray[i].sheet_item_id + '"><i class="icon icon-remove"></i></a></td></tr>');
                        }
                        items.push('<tr><td class="job_cost" colspan="8"><b>Cost:</b> ' + total + '</td></tr>');
                        items.push('</table>');

                    } else {

                        $("#lblMatDetailMain").html("");
                        $("#lblMatDetailMain").html(data.massage);
                        $("#lblMatDetailMain").css("display", "block");
                    }
                    $('#divbindMatDetails').append(items.join(''));
                    $(".aMinusMatQty").on("click", function() {
                        var splits_id = this.id.split('_');
                        var MatSheetID = splits_id[1];
                        bindMatSheetItemDetails('MethodName=updateMatSheetItemDetail&sheetId=' + MatSheetID + '&jobId=' + editMatSheetJobId + '&action=minus');
                        return false;
                    });

                    $(".aPlusMatQty").on("click", function() {
                        var splits_id = this.id.split('_');
                        var MatSheetID = splits_id[1];
                        bindMatSheetItemDetails('MethodName=updateMatSheetItemDetail&sheetId=' + MatSheetID + '&jobId=' + editMatSheetJobId + '&action=plus');
                        return false;
                    });

                    $(".aRemoveSheetItem").on("click", function() {
                        if (confirm('Are you sure?')) {
                            var splits_id = this.id.split('_');
                            var MatSheetID = splits_id[1];
                            bindMatSheetItemDetails('MethodName=updateMatSheetItemDetail&sheetId=' + MatSheetID + '&jobId=' + editMatSheetJobId + '&action=del');
                            return false;
                        } else { return false; }
                    });
                },
                error: function(jqxhr, textStatus, errorMessage) {
                    navigator.notification.alert(
                        errorMessage, alertDismissed, "An error occured", "Done"
                    );
                }
            })
        } catch (e) {

        }
    }

    function bindMatSheetItemDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    //alert(data.message);
                    BindEDITJobSuppliersDdl('MethodName=GetSupplierForJobMatSheet');
                    BindJobMatSheetDetailForEDIT('MethodName=GetJobMatSheetDetailBySheetId&sheet_id=' + editJobMatSheetId + '&job_id=' + editMatSheetJobId);
                    bindMaterialsListBySheetID('MethodName=getMaterialsListBySheetID&sheetId=' + editJobMatSheetId);
                    $("#divMainAddJobMatSheet").css("display", "none");
                    $("#divMainEditJobMatSheet").css("display", "block");

                } else {
                    //alert(data.message);
                    $("#divMainAddJobMatSheet").css("display", "none");
                    $("#divMainEditJobMatSheet").css("display", "block");
                    $("#lblMatDetailMain").html("");
                    $("#lblMatDetailMain").html(data.massage);
                    $("#lblMatDetailMain").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }

    $("#btnSaveModifyJMS").on("click", function() {

        var retval = false;
        var ans = check_itemsvalidate('#divEditJobMatSheet input');

        if (ans) {

            var supplierVal = 0;
            if (supplierVal != '' || supplierVal != 'null') {
                supplierVal = $("#ddlEditJobMatSupplier").val();
            }

            var ddate = null;

            if ($('#chkDoNotSchedule').is(":checked")) {
                ddate = null;
            } else { ddate = $('#txtEditDeleDate').val(); }

            var isConfirmed = 0;
            if ($('#chkConfirmed').is(":checked")) {
                isConfirmed = 1;
            }

            BindModifiedMatSheetDetails('MethodName=ModifyJobMaterialSheet&job_id=' + editMatSheetJobId + '&sheet_id=' + editJobMatSheetId + '&supplier=' + supplierVal + '&label=' + $("#txtEditMatSheetLbl").val() + '&notes=' + $("#txtEditJMSNotes").val() + '&deliveryDate=' + ddate + '&confirm=' + isConfirmed);
        } else { return false; }
    });

    function BindModifiedMatSheetDetails(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);

                if (data.status == "1") {

                    navigator.notification.alert(
                        "Job material sheet saved successfully!", alertDismissed, "Successful", "Done"
                    );
                    BindEDITJobSuppliersDdl('MethodName=GetSupplierForJobMatSheet');
                    BindJobMatSheetDetailForEDIT('MethodName=GetJobMatSheetDetailBySheetId&sheet_id=' + editJobMatSheetId + '&job_id=' + editMatSheetJobId);
                    bindMaterialsListBySheetID('MethodName=getMaterialsListBySheetID&sheetId=' + editJobMatSheetId);
                    $("#divMainAddJobMatSheet").css("display", "none");
                    $("#divMainEditJobMatSheet").css("display", "block");

                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    $("#divMainAddJobMatSheet").css("display", "none");
                    $("#divMainEditJobMatSheet").css("display", "block");
                    $("#lblMatDetailMain").html("");
                    $("#lblMatDetailMain").html(data.massage);
                    $("#lblMatDetailMain").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnCancleModifyJMS").on("click", function() {
        mainView.loadPage("jobdetails.html?JId=" + editMatSheetJobId);
        return false;
    });

    $("#btnAllMatModifyJMS").on("click", function() {
        //$('#divEditJobMatSheet').css("display", "none");
        $('#divMainEditJobMatSheet').css("display", "none");
        $('#divBindAllMatSheet').css("display", "block");
        BindAllJobMatSheet('MethodName=getAllJobMatSheet&JobId=' + editMatSheetJobId);
        return false;
    });
    $("#lnkBackToModifyMatSheet").on("click", function() {
        BindEDITJobSuppliersDdl('MethodName=GetSupplierForJobMatSheet');
        BindJobMatSheetDetailForEDIT('MethodName=GetJobMatSheetDetailBySheetId&sheet_id=' + editJobMatSheetId + '&job_id=' + editMatSheetJobId);
        bindMaterialsListBySheetID('MethodName=getMaterialsListBySheetID&sheetId=' + editJobMatSheetId);
        $("#divMainAddJobMatSheet").css("display", "none");
        $("#divMainEditJobMatSheet").css("display", "block");
        $('#divEditJobMatSheet').css("display", "block");
        $('#divBindAllMatSheet').css("display", "none");
        return false;
    });

    function BindAllJobMatSheet(data) {
        var $ = jQuery.noConflict();

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $("#tblAllMatSheet").empty();
                if (data.hasAccess == true) {
                    if (data.canDelete == true) {
                        $("#tblAllMatSheet").append("<a id='alinkAddNewMatSheetItem' href='javascript:;'><i class='icon icon-plus'></i></a>");
                        for (var i = 0; i < data.matSheetArray.length; i++) {
                            $("#tblAllMatSheet").append("<div class='btn-group'><div class='btn btn-blue'><a style='color:#ffffff;' id='alinkMatSheetDetail_" + data.matSheetArray[i].sheet_id + "' class='alinkMatSheetDetail'>" + data.matSheetArray[i].label + "</a>&nbsp;&nbsp;&nbsp;<a style='color:#ffffff;' id='aRemovelinkMatSheet_" + data.matSheetArray[i].sheet_id + "' class='aRemovelinkMatSheet'><i class='icon-remove'></i></a></div></div>");
                        }
                    } else {
                        for (var i = 0; i < data.matSheetArray.length; i++) {
                            $("#tblAllMatSheet").append("<div class='btn-group'><div class='btn btn-blue'><a style='color:#ffffff;' id='alinkMatSheetDetail_" + data.matSheetArray[i].sheet_id + "' class='alinkMatSheetDetail'>" + data.matSheetArray[i].label + "</a>&nbsp;</div></div>");
                        }
                    }
                    $(".alinkMatSheetDetail").on("click", function() {
                        var splits_id = this.id.split('_');
                        var MatSheetID = splits_id[1];
                        BindEDITJobSuppliersDdl('MethodName=GetSupplierForJobMatSheet');
                        BindJobMatSheetDetailForEDIT('MethodName=GetJobMatSheetDetailBySheetId&sheet_id=' + MatSheetID + '&job_id=' + editMatSheetJobId);
                        bindMaterialsListBySheetID('MethodName=getMaterialsListBySheetID&sheetId=' + MatSheetID);
                        $("#divMainAddJobMatSheet").css("display", "none");
                        $("#divMainEditJobMatSheet").css("display", "block");
                        $('#divEditJobMatSheet').css("display", "block");
                        $('#divBindAllMatSheet').css("display", "none");
                        return false;
                    });
                    if (data.canDelete == true) {
                        $(".aRemovelinkMatSheet").on("click", function() {
                            if (confirm('Are you sure?')) {
                                var splits_id = this.id.split('_');
                                var MatSheetID = splits_id[1];
                                bindAfterPerformRemoveOptionMatSheetList('MethodName=RemoveMaterialsSheetBySheetID&sheetId=' + MatSheetID + '&JobId=' + editMatSheetJobId);
                                return false;
                            } else { return false; }
                        });
                    }
                    $("#alinkAddNewMatSheetItem").on("click", function() {
                        BindDdlJobSuppliers('MethodName=GetSupplierForJobMatSheet');
                        $("#divMainAddJobMatSheet").css("display", "block");
                        $("#divMainEditJobMatSheet").css("display", "none");
                        $("#divBindAllMatSheet").css("display", "none");
                        $("#divJobMatMainHeader").html("");
                        $("#divJobMatMainHeader").html("Create Material Sheet");
                        return false;
                    });
                } else {
                    $('#lblViewAllMatSheet').html("");
                    $('#lblViewAllMatSheet').html("Invalid permissions!");
                    $('#lblViewAllMatSheet').css("display", "block");
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    function bindAfterPerformRemoveOptionMatSheetList(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    BindAllJobMatSheet('MethodName=getAllJobMatSheet&JobId=' + editMatSheetJobId);
                    $("#divMainAddJobMatSheet").css("display", "none");
                    $("#divMainEditJobMatSheet").css("display", "none");
                    $("#divEditJobMatSheet").css("display", "block");
                    $("#divBindAllMatSheet").css("display", "block");
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    $("#divMainAddJobMatSheet").css("display", "none");
                    $("#divMainEditJobMatSheet").css("display", "block");
                    //$("#lblViewAllMatSheet").html("");
                    //$("#lblViewAllMatSheet").html(data.massage);
                    //$("#lblViewAllMatSheet").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function GetCatListforMaterial(data) {
        try {
            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: 'POST',
                data: data,
                cache: false,
                success: function(data, textStatus, jqxhr) {
                    var data = $.parseJSON(data);
                    $('#ddlMatCategory').empty();
                    if (data.matCategory.length > 0) {
                        $('#ddlMatCategory').append("<option value=''>Select Category</option>");
                        for (var i = 0; i < data.matCategory.length; i++) {
                            $('#ddlMatCategory').append("<option value='" + data.matCategory[i].category_id + "'>" + data.matCategory[i].category + "</option>");
                        }
                    }
                },
                error: function(jqxhr, textStatus, errorMessage) {
                    navigator.notification.alert(
                        errorMessage, alertDismissed, "An error occured", "Done"
                    );
                }
            })
        } catch (e) {
            navigator.notification.alert(
                e.message, alertDismissed, "Unsuccessful", "Done"
            );
        }
    }
    $("#ddlMatCategory").change(function myfunction() {
        $categoryId = $('#ddlMatCategory').val();
        if ($categoryId != '' && $categoryId != null) {
            BindCatMatDetailsByCatId('MethodName=GetMatDetailForCategory&CategoryID=' + $categoryId);
        } else {
            $('#ddlMatSheetItems').empty();
            $('#divMatSheetItems').css("display", "none");
            $('#ddlMatColors').empty();
            //$("#txtNoOfUnit").removeClass("form_input form-control validation validate[required[Enter Quantity]] validate[funcCall[GreaterValue0[Value should be Greater than 0]]] validate[funcCall[Onlydigitsex0[Enter numbers only]]]");
            $('#divMatColors').css("display", "none");

        }
        return false;
    });

    function BindCatMatDetailsByCatId(data) {
        try {
            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: 'POST',
                data: data,
                cache: false,
                success: function(data, textStatus, jqxhr) {
                    var data = $.parseJSON(data);

                    $('#ddlMatSheetItems').empty();
                    var BrandId = 0;
                    if (data.matDetailSql.length > 0) {
                        $('#divMatSheetItems').css("display", "block");
                        for (var i = 0; i < data.matDetailSql.length; i++) {
                            if (data.matDetailSql[i].brand_id != "0") {

                                if (BrandId == data.matDetailSql[i].brand_id) {
                                    $('#ddlMatSheetItems').append("<option value='" + data.matDetailSql[i].material_id + "'>" + data.matDetailSql[i].material + "</option>");
                                } else {
                                    BrandId = data.matDetailSql[i].brand_id;

                                    if (data.matDetailSql[i].brand_id == "-1") {
                                        $('#ddlMatSheetItems').append("<option value=''>**&nbsp;Varies&nbsp;**</option>");
                                        $('#ddlMatSheetItems').append("<option value='" + data.matDetailSql[i].material_id + "'>" + data.matDetailSql[i].material + "</option>");
                                    } else {
                                        $('#ddlMatSheetItems').append("<option value=''>**&nbsp;" + data.matDetailSql[i].brand + "&nbsp;**</option>");
                                        $('#ddlMatSheetItems').append("<option value='" + data.matDetailSql[i].material_id + "'>" + data.matDetailSql[i].material + "</option>");
                                    }

                                }
                            }
                        }
                    }
                },
                error: function(jqxhr, textStatus, errorMessage) {
                    navigator.notification.alert(
                        errorMessage, alertDismissed, "An error occured", "Done"
                    );
                }
            })
        } catch (e) {
            navigator.notification.alert(
                e.message, alertDismissed, "Unsuccessful", "Done"
            );
        }
    }

    $("#ddlMatSheetItems").change(function myfunction() {
        $materialId = $('#ddlMatSheetItems').val();
        if ($materialId != '' && $materialId != null) {
            BindColorListByMaterialId('MethodName=GetColorListByMaterialId&material_id=' + $materialId);
        } else {
            $('#ddlMatColors').empty();
            $("#txtNoOfUnit").removeClass("form_input form-control validation validate[required[Enter Quantity]] validate[funcCall[GreaterValue0[Value should be Greater than 0]]] validate[funcCall[Onlydigitsex0[Enter numbers only]]]");
            $('#divMatColors').css("display", "none");
        }
        return false;
    });

    function BindColorListByMaterialId(data) {
        try {
            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: 'POST',
                data: data,
                cache: false,
                success: function(data, textStatus, jqxhr) {
                    var data = $.parseJSON(data);

                    $('#ddlMatColors').empty();
                    $('#divMatColors').css("display", "block");
                    $('#ddlMatColors').append("<option value=''>select color</option>");

                    if (data.colorArray.length > 0) {
                        for (var i = 0; i < data.colorArray.length; i++) {
                            $('#ddlMatColors').append("<option value='" + data.colorArray[i].color_id + "'>" + data.colorArray[i].color + "</option>");
                        }
                    }
                    $("#txtNoOfUnit").addClass("form_input form-control validation validate[required[Enter Quantity]] validate[funcCall[GreaterValue0[Value should be Greater than 0]]]  validate[funcCall[Onlydigitsex0[Enter numbers only]]]");
                    if (data.matArray.length > 0) {
                        for (var i = 0; i < data.matArray.length; i++) {
                            $('#lblUnit').html("");
                            $('#lblUnit').html("<b>Unit:&nbsp;</b>" + data.matArray[i].unit);
                            $('#lblMatDesc').html("");
                            $('#lblMatDesc').html("<b>Description:&nbsp;</b>" + data.matArray[i].info);
                        }
                    }
                },
                error: function(jqxhr, textStatus, errorMessage) {
                    navigator.notification.alert(
                        errorMessage, alertDismissed, "An error occured", "Done"
                    );
                }
            })
        } catch (e) {
            navigator.notification.alert(
                e.message, alertDismissed, "Unsuccessful", "Done"
            );
        }
    }

    $("#btnAddJobMatItems").on("click", function() {

        var retval = false;
        var ans = check_itemsvalidate('#divMatColors input');

        if (ans) {
            var Qty = $('#txtNoOfUnit').val();
            if (Qty > 0) {
                var SheetID = editJobMatSheetId;
                var catMaterial = $('#ddlMatSheetItems').val();
                var MatColor = $('#ddlMatColors').val();
                AddNewMaterialSheetItem('MethodName=InsertNewMatSheetItem&SheetId=' + SheetID + '&MatId=' + catMaterial + '&ColorId=' + MatColor + '&qty=' + Qty);
            }

        } else { return false; }
    });

    function AddNewMaterialSheetItem(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    BindEDITJobSuppliersDdl('MethodName=GetSupplierForJobMatSheet');
                    BindJobMatSheetDetailForEDIT('MethodName=GetJobMatSheetDetailBySheetId&sheet_id=' + editJobMatSheetId + '&job_id=' + editMatSheetJobId);
                    bindMaterialsListBySheetID('MethodName=getMaterialsListBySheetID&sheetId=' + editJobMatSheetId);
                    $("#divMainAddJobMatSheet").css("display", "none");
                    $("#divMainEditJobMatSheet").css("display", "block");
                    $('#ddlMatCategory').val('');
                    $('#ddlMatSheetItems').empty();
                    $('#divMatSheetItems').css("display", "none");
                    $('#ddlMatColors').empty();
                    //$("#txtNoOfUnit").removeClass("form_input form-control validation validate[required[Enter Quantity]] validate[funcCall[GreaterValue0[Value should be Greater than 0]]] validate[funcCall[Onlydigitsex0[Enter numbers only]]]");
                    $('#divMatColors').css("display", "none");
                } else {
                    //alert(data.message);
                    $("#divMainAddJobMatSheet").css("display", "none");
                    $("#divMainEditJobMatSheet").css("display", "block");
                    $("#lblErrMsgAddMatSheet").html("");
                    $("#lblErrMsgAddMatSheet").html(data.massage);
                    $("#lblErrMsgAddMatSheet").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnPrintModifyJMS").on("click", function() {
        mainView.loadPage("print_form.html?job_id=" + editMatSheetJobId + "&sheet_id=" + editJobMatSheetId);
        //BindthePrintMatSheetForm('MethodName=GenerateMatSheetPrintForm&JobId=' + editMatSheetJobId + '&SheetId=' + editJobMatSheetId);
        return false;
    });

    $("#btnViewJobUploadFiles").on("click", function() {
        mainView.loadPage("jobuploads.html?JId=" + editMatSheetJobId);
        return false;
    });

});

myApp.onPageInit('print_form', function(page) {
    var $ = jQuery.noConflict();

    jQuery(".formError").remove();
    var editJobMatSheetId = page.query.sheet_id;
    var editMatSheetJobId = page.query.job_id;
    BindthePrintMatSheetForm('MethodName=GenerateMatSheetPrintForm&job_id=' + editMatSheetJobId + '&sheet_id=' + editJobMatSheetId);

    function BindthePrintMatSheetForm(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.materialPrintForm != null) {
                    var printForm = data.materialPrintForm;
                    $('#divPrintMaterialSheetInfo').empty();
                    $('#divPrintMaterialSheetInfo').append(data.materialPrintForm);
                    //window.open(printForm, '_system');
                    //window.print(printForm);
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }
    $("#alinkBackPrintForm").on("click", function() {
        mainView.loadPage("job_materials.html?sheet_id=" + editJobMatSheetId + "&job_id=" + editMatSheetJobId);
        return false;
    });
    $('#lnkprint').on("click", function() {
        return false;
    });
    //$("#alinkPrintThisForm").click(function () {
    //    var gadget = new cloudprint.Gadget();
    //    var url = page.url;
    //    gadget.setPrintDocument(url, "Print Form", window.location.href, url);
    //    gadget.openPrintDialog();
    //    return false;
    //});

    //$("#alinkPrintThisForm").click(function () {
    //    var page = '<h1>Hello Document</h1>';

    //    cordova.plugins.printer.print(page, 'print_form.html', function () {
    //        alert('printing finished or canceled')
    //    });
    //    return false;
    //});
});

myApp.onPageInit('system', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();

    GetSystemModuleDetails('MethodName=GetSystemModuleDetails');

    function GetSystemModuleDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];

                if (data.status == "1") {
                    $("#spnSystemLogo").empty();
                    $("#divMainHeaderForSystem").html("");
                    $("#divMainHeaderForSystem").html("System");
                    $("#divSystemDetails").css("display", "block");
                    $("#divEditCompanyProfile").css("display", "none");

                    if (data.userAcctDetail.length > 0) {
                        for (var i = 0; i < data.userAcctDetail.length; i++) {
                            if (data.userAcctDetail[i].logo != null && data.userAcctDetail[i].logo != '') {
                                $("#spnSystemLogo").append("<img height=125 width=280 id=imgSysCompanyLogo src='https://xactbid.pocketofficepro.com/logos/" + data.userAcctDetail[i].profileImg + "' />");
                                $("#spnSidePanelLogo").empty();
                                $("#spnSidePanelLogo").append("<img height=125 width=280 id=imgSysCompanyLogo src='https://xactbid.pocketofficepro.com/logos/" + data.userAcctDetail[i].profileImg + "' />");
                            } else {
                                $("#spnSystemLogo").append("<img height=125 width=280 id=imgSysCompanyLogo src='https://xactbid.pocketofficepro.com/images/icons/user_64.png' />");
                                $("#spnSidePanelLogo").append("<img height=125 width=280 id=imgSysCompanyLogo src='https://xactbid.pocketofficepro.com/images/icons/user_64.png' />");
                            }

                            items.push($("#lblSysCompanyTitle").text(data.userAcctDetail[i].account_name) + $("#lblSysAddress").text(data.userAcctDetail[i].address + " " + data.userAcctDetail[i].city + ", " + data.userAcctDetail[i].state + " " + data.userAcctDetail[i].zip) + $("#lblSysPhone").text(data.userAcctDetail[i].formatPhn) + $("#lblSysEmail").text(data.userAcctDetail[i].email) + $("#lblSysPrimaryContact").text(data.userAcctDetail[i].primary_contact) + $("#lblSysAcctDOB").text(data.userAcctDetail[i].reg_date) + $("#lblSysTotalUsers").text(data.usersCount) + $("#lblSysLicenseLimit").text(data.userAcctDetail[i].license_limit));
                        }
                    } else {
                        return false;
                    }
                    $('#divSysConfiguration').empty();
                    $('#divSysConfiguration').append(data.sysConfigList);
                    $('#divSysSecurityLevels').empty();
                    $('#divSysSecurityLevels').append(data.sysLevelList);
                } else {
                    $("#lblMsgForSysDetail").html("");
                    $("#lblMsgForSysDetail").html(data.message);
                    $("#lblMsgForSysDetail").css("display", "block");
                    $("#divSystemDetails").css("display", "none");
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#btnSysLogoUpload').on("click", function() {
        if ($('#fluSysLogo').val() != "") {

            var retval = false;
            var ans = check_itemsvalidate('#divSysCompanyProfile input');

            if (ans) {
                var files = $('#fluSysLogo')[0].files;
                var form_data = new FormData();
                var file_data = $('#fluSysLogo').prop('files')[0];
                form_data.append('flag', '4');
                form_data.append('filename', files[0].name);
                form_data.append('file', file_data);

                $.when($.ajax({
                    url: 'https://xactbid.pocketofficepro.com/fileuploader.php', // point to server-side PHP script
                    dataType: 'text', // what to expect back from the PHP script, if anything
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function(php_script_response) {
                        navigator.notification.alert(
                            "Logo successfully modified", alertDismissed, "Successful", "Done"
                        );

                        // display response from the PHP script, if any
                    },
                    error: function(data) {
                        //alert('err' + data);
                        var myerr = 'err' + data;
                        navigator.notification.alert(
                            myerr, alertDismissed, "An error occured", "Done"
                        );
                    },
                }).then(function() {
                    GetSystemModuleDetails('MethodName=GetSystemModuleDetails');
                }));

            }
        } else {
            navigator.notification.alert(
                "No files selected", alertDismissed, "Unsuccessful", "Done"
            );
        }
        return false;
    });

    $('#aEditCompanyProfile').on("click", function() {
        $("#divMainHeaderForSystem").html("");
        $("#divMainHeaderForSystem").html("Edit Company Profile");
        $("#divSystemDetails").css("display", "none");
        $("#divEditCompanyProfile").css("display", "block");
        GetSystemModuleDetailsForEDIT('MethodName=GetSystemModuleDetails');
        return false;
    });

    function GetSystemModuleDetailsForEDIT(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];

                if (data.status == "1") {

                    if (data.userAcctDetail.length > 0) {
                        $.each(data.stateArray, function(key, value) {
                            $("#ddlEditComState").append($("<option value='" + key + "'>" + key + "</option>"));
                        });
                        for (var i = 0; i < data.userAcctDetail.length; i++) {
                            items.push($("#txtEditComTitle").val(data.userAcctDetail[i].account_name) + $("#txtEditComPriContact").val(data.userAcctDetail[i].primary_contact) + $("#txtEditComEmail").val(data.userAcctDetail[i].email) + $("#txtEditComPhone").val(data.userAcctDetail[i].phone) + $("#txtEditComAddress").val(data.userAcctDetail[i].address) + $("#txtEditComCity").val(data.userAcctDetail[i].city) + $('#ddlEditComState').val(data.userAcctDetail[i].state).attr("selected", "selected") + $("#txtEditComZip").val(data.userAcctDetail[i].zip) + $("#txtEditComJobUnit").val(data.userAcctDetail[i].job_unit));
                        }
                    } else {
                        return false;
                    }
                } else {
                    $("#lblErrMsgEditCProfile").html("");
                    $("#lblErrMsgEditCProfile").html(data.message);
                    $("#lblErrMsgEditCProfile").css("display", "block");
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }
    $('#btnCancelCompanyProfile').on("click", function() {
        jQuery(".formError").remove();
        $("#divMainHeaderForSystem").html("");
        $("#divMainHeaderForSystem").html("System");
        $("#divSystemDetails").css("display", "block");
        $("#divEditCompanyProfile").css("display", "none");
        return false;
    });

    $('#btnUpdateCompanyProfile').on("click", function() {
        var retval = false;
        var ans = check_itemsvalidate('#divEditCompanyProfile input');

        if (ans) {
            UpdateCompanyProfileDetails('MethodName=UpdateCompanyProfileDetails&Title=' + $("#txtEditComTitle").val() + '&PContact=' + $("#txtEditComPriContact").val() + '&Email=' + $("#txtEditComEmail").val() + '&Phone=' + $("#txtEditComPhone").val() + '&Address=' + $("#txtEditComAddress").val() + '&City=' + $("#txtEditComCity").val() + '&State=' + $("#ddlEditComState").val() + '&Zip=' + $("#txtEditComZip").val() + '&JobUnit=' + $("#txtEditComJobUnit").val());
            return false;
        } else {
            return false;
        }
    });

    function UpdateCompanyProfileDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    GetSystemModuleDetails('MethodName=GetSystemModuleDetails');
                } else {
                    $("#lblErrMsgEditCProfile").html("");
                    $("#lblErrMsgEditCProfile").html(data.message);
                    $("#lblErrMsgEditCProfile").css("display", "block");
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }
});

myApp.onPageInit('app_configuration', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();

    BindSystemConfigDetails('MethodName=GetSystemConfigDetails');

    function BindSystemConfigDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];

                if (data.strAppConfigDetails != null) {
                    $('#divAppConfigBackDetails').empty();
                    $('#divAppConfigBackDetails').append(data.strAppConfigDetails);

                    if (data.accountMetaData != null) {
                        if (data.accountMetaData.user_session_timeout != undefined && data.accountMetaData.user_session_timeout != null && data.accountMetaData.user_session_timeout != "")
                            $('#ddlUserSessionExpMetaVal').val(data.accountMetaData.user_session_timeout.meta_value).attr("selected", "selected");
                    }

                    $('.drddefaultdmeta').on("click", function myfunction() {
                        var ddlDefaultMeta = this.id;

                        var splits_id = this.id.split('_');
                        var splitId1 = splits_id[0];
                        var splitId2 = splits_id[1];
                        var selectMeta = "#drdselectedmeta_" + splitId2;

                        var $options = $("#" + ddlDefaultMeta + " > option:selected");
                        $(selectMeta).append($options);

                        var optVal = "";
                        $(selectMeta).find('option').each(function() {
                            if (optVal != "") {
                                optVal = optVal + $(this).val() + ","
                            } else { optVal = $(this).val() + "," }
                        });
                        var selVal = optVal;
                        // alert(selVal);
                        selVal = selVal.toString().substring(0, selVal.toString().length - 1);
                        //alert(selVal);
                        //alert(splitId2);

                        if (selVal != "") {
                            if (splitId2 == "1") {
                                UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_job_salesman_user_dropdown&meta_value=' + selVal);
                            } else if (splitId2 == "2") {
                                UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_job_referral_user_dropdown&meta_value=' + selVal);
                            } else if (splitId2 == "3") {
                                UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_job_subscriber_user_dropdown&meta_value=' + selVal);
                            } else if (splitId2 == "4") {
                                UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_journal_recipient_user_dropdown&meta_value=' + selVal);
                            } else if (splitId2 == "5") {
                                UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_repair_contractor_user_dropdown&meta_value=' + selVal);
                            } else if (splitId2 == "6") {
                                UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_task_contractor_user_dropdown&meta_value=' + selVal);
                            } else if (splitId2 == "7") {
                                UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_job_canvasser_user_dropdown&meta_value=' + selVal);
                            } else if (splitId2 == "8") {
                                UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=job_salesman_filter_user_dropdown&meta_value=' + selVal);
                            }
                        }
                        //var $options = $("#drddefaultdmeta_1 > option:selected");
                        //$('#drdselectedmeta_1').append($options);

                        //var $options = $("#drddefaultdmeta > option:selected");
                        //$('#drdselectedmeta').append($options);
                    });
                    $('.drdselectedmeta').on("click", function myfunction() {

                        var ddlSelectMeta = this.id;

                        var splits_id = this.id.split('_');
                        var splitId1 = splits_id[0];
                        var splitId2 = splits_id[1];
                        var defaultMeta = "#drddefaultdmeta_" + splitId2;

                        var $options = $("#" + ddlSelectMeta + " > option:selected");

                        $(defaultMeta).append($options);

                        var optVal = "";

                        $("#" + ddlSelectMeta).find('option').each(function() {

                            if (optVal != "") {
                                optVal = optVal + $(this).val() + ","
                            } else { optVal = $(this).val() + "," }
                        });

                        var selVal = optVal;
                        selVal = selVal.toString().substring(0, selVal.toString().length - 1);
                        //alert(selVal);
                        //alert(splitId2);

                        if (splitId2 == "1") {
                            UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_job_salesman_user_dropdown&meta_value=' + selVal);
                        } else if (splitId2 == "2") {
                            UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_job_referral_user_dropdown&meta_value=' + selVal);
                        } else if (splitId2 == "3") {
                            UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_job_subscriber_user_dropdown&meta_value=' + selVal);
                        } else if (splitId2 == "4") {
                            UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_journal_recipient_user_dropdown&meta_value=' + selVal);
                        } else if (splitId2 == "5") {
                            UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_repair_contractor_user_dropdown&meta_value=' + selVal);
                        } else if (splitId2 == "6") {
                            UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_task_contractor_user_dropdown&meta_value=' + selVal);
                        } else if (splitId2 == "7") {
                            UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=assign_job_canvasser_user_dropdown&meta_value=' + selVal);
                        } else if (splitId2 == "8") {
                            UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=job_salesman_filter_user_dropdown&meta_value=' + selVal);
                        }

                        //var $options = $("#drdselectedmeta_1 > option:selected");
                        //$('#drddefaultdmeta_1').append($options);

                        //var $options = $("#drdselectedmeta > option:selected");
                        //$('#drddefaultdmeta').append($options);
                    });

                    $('.updateRqrTaskStageMetaVal').on("click", function myfunction() {

                        var checkStage = '0';
                        if ($('.updateRqrTaskStageMetaVal').is(":checked")) {
                            checkStage = '1';
                        }
                        UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=require_task_stage&meta_value=' + checkStage);
                        return false;
                    });

                    $('#ddlUserSessionExpMetaVal').change(function() {
                        //alert($('#ddlUserSessionExpMetaVal').val());
                        var sessionExpVal = $('#ddlUserSessionExpMetaVal').val();
                        UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=user_session_timeout&meta_value=' + sessionExpVal);
                        return false;
                    });

                    $('.updateAllowMentionMetaVal').on("click", function myfunction() {
                        var mentionVal = '0';
                        if ($('.updateAllowMentionMetaVal').is(":checked")) {
                            mentionVal = '1';
                        }
                        UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=allow_mentions&meta_value=' + mentionVal);
                        return false;
                    });

                    $('.updateInactiveUserMetaVal').on("click", function myfunction() {

                        var inactiveUser = '0';
                        if ($('.updateInactiveUserMetaVal').is(":checked")) {
                            inactiveUser = '1';
                        }
                        UpdateUserAccountMetaValue('MethodName=UpdateUserAccountMetaValue&meta_key=show_inactive_users_in_lists&meta_value=' + inactiveUser);
                        return false;
                    });
                } else {
                    $("#lblAppConfigMsg").html("");
                    $("#lblAppConfigMsg").html(data.message);
                    $("#lblAppConfigMsg").css("display", "block");
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#aAppConfigBack').on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });

    function UpdateUserAccountMetaValue(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    BindSystemConfigDetails('MethodName=GetSystemConfigDetails');
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }
});

myApp.onPageInit('job_types', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();

    BindJobTypeList('MethodName=GetJobTypeList');

    function BindJobTypeList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $('#divEditJobType').empty();
                var items = [];
                if (data.jobTypes.length > 0) {
                    items.push("<table class='table'><tr><th colspan='3'>Name</th><th class='acenter'>Actions</th></tr>");
                    for (var i = 0; i < data.jobTypes.length; i++) {
                        items.push('<tr><td colspan="3">' + data.jobTypes[i].job_type + '</td><td class="acenter"><a class="aDeleteJobType" href="javascript:;" id="aDeleteJobType_' + data.jobTypes[i].job_type_id + '"><i class="icon-remove"></i></a></td></tr>');
                    }
                    items.push('</table>');
                } else {
                    items.push("<table class='table'><tr><th colspan='3'>Name</th><th class='acenter'>Actions</th></tr>");
                    items.push('<tr><td class="acenter" colspan="4">No Job Type Found</td></tr>');
                    items.push('</table>');
                    $("#divmodifyJobTypes").css("display", "block");


                    //return false;
                }

                $('#divEditJobType').append(items.join(''));

                $(".aDeleteJobType").on("click", function() {
                    if (confirm('Are you sure?')) {
                        var splits_id = this.id.split('_');
                        var deleteJobTypeID = splits_id[1];
                        DeleteJobType('MethodName=DeleteJobTypeById&JobTypeID=' + deleteJobTypeID);
                        return false;
                    } else { return false; }
                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnAddJobType").on("click", function() {

        var ans = check_itemsvalidate('#divAddJobType input');
        if (ans) {

            AddJobType('MethodName=AddJobType&title=' + $("#txtAddJobTypeTitle").val());
            $("#txtAddJobTypeTitle").val("");
            return false;
        } else {
            return false;
        }
        return false;
    });

    $("#btnCancelJobType").on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });

    function AddJobType(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divmodifyJobTypes").css("display", "block");

                    BindJobTypeList('MethodName=GetJobTypeList');
                    //$('#lblModifyJobType').html(obj.message);
                    //$("#lblModifyJobType").css("display", "block");
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyJobType').html(obj.message);
                    //$("#lblModifyJobType").css("display", "block");
                    $("#divmodifyJobTypes").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    function DeleteJobType(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divmodifyJobTypes").css("display", "block");

                    BindJobTypeList('MethodName=GetJobTypeList');
                    //$('#lblModifyJobType').html(data.message);
                    //$("#lblModifyJobType").css("display", "block");
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyJobType').html(data.message);
                    //$("#lblModifyJobType").css("display", "block");
                    $("#divmodifyJobTypes").css("display", "block");

                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#aJobTypesBack').on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });
});

myApp.onPageInit('get_level', function(page) {
    var $ = jQuery.noConflict();
    $body = $("body");
    jQuery(".formError").remove();
    CheckUserLogin();

    var levelId = page.query.id;
    if (levelId != null && levelId != "" && levelId != "undefined") {
        BindSecurityLevelDetails('MethodName=GetSecurityLevelDetails&levelId=' + levelId);
    }

    function BindSecurityLevelDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $('#divGetLevelDetails').empty();
                var items = [];
                if (data.status == "1") {
                    if (data.strSecurityLevels != null) {
                        $('#divGetLevelDetails').append(data.strSecurityLevels);

                        $(".chkToggle").on("click", function() {
                            var splits_id = this.id.split('_');
                            var moduleId = splits_id[1];
                            var checked = false;
                            if ($(this).is(':checked')) {
                                checked = true;
                            }

                            if ((moduleId != null || moduleId != "" || moduleId != "undefined")) {
                                BindUpdatedSecurityLevelPermission('MethodName=UpdateSecurityLevelPermission&ModuleId=' + moduleId + '&Action=chkToggle&LevelID=' + levelId + '&checked=' + checked);
                            }
                            return false;
                        });
                        $(".chkownership").on("click", function() {
                            var splits_id = this.id.split('_');
                            var moduleId = splits_id[1];
                            var checked = false;
                            if (!$(this).is(':checked')) {
                                checked = false;
                            } else {
                                checked = true;
                            }

                            if ((moduleId != null || moduleId != "" || moduleId != "undefined")) {
                                BindUpdatedSecurityLevelPermission('MethodName=UpdateSecurityLevelPermission&ModuleId=' + moduleId + '&Action=chkownership&LevelID=' + levelId + '&checked=' + checked);
                            }
                            return false;
                        });
                        $("#aLinkChkAllModules").on("click", function() {

                            var moduleId = "0";
                            var checked = false;

                            if ((moduleId != null || moduleId != "" || moduleId != "undefined")) {

                                BindUpdatedSecurityLevelPermission('MethodName=UpdateSecurityLevelPermission&ModuleId=' + moduleId + '&Action=chkAllModules&LevelID=' + levelId + '&checked=' + checked);


                            }

                            return false;
                        });
                        $("#aLinkUnChkAllModules").on("click", function() {
                            var moduleId = "0";
                            var checked = false;

                            if ((moduleId != null || moduleId != "" || moduleId != "undefined")) {
                                BindUpdatedSecurityLevelPermission('MethodName=UpdateSecurityLevelPermission&ModuleId=' + moduleId + '&Action=UnChkAllModules&LevelID=' + levelId + '&checked=' + checked);
                            }
                            return false;
                        });
                        $(".chkstage").on("click", function() {
                            var splits_id = this.id.split('_');
                            var moduleId = splits_id[1];
                            var checked = false;
                            if (!$(this).is(':checked')) {
                                checked = false;
                            } else {
                                checked = true;
                            }

                            if ((moduleId != null || moduleId != "" || moduleId != "undefined")) {
                                BindUpdatedSecurityLevelPermission('MethodName=UpdateSecurityLevelPermission&ModuleId=' + moduleId + '&Action=chkstage&LevelID=' + levelId + '&checked=' + checked);
                            }
                            return false;
                        });
                        $("#aLinkChkAllStages").on("click", function() {
                            var moduleId = "0";
                            var checked = false;

                            if ((moduleId != null || moduleId != "" || moduleId != "undefined")) {
                                BindUpdatedSecurityLevelPermission('MethodName=UpdateSecurityLevelPermission&ModuleId=' + moduleId + '&Action=chkallstages&LevelID=' + levelId + '&checked=' + checked);
                            }
                            return false;
                        });
                        $("#aLinkUnChkAllStages").on("click", function() {
                            var moduleId = "0";
                            var checked = false;

                            if ((moduleId != null || moduleId != "" || moduleId != "undefined")) {
                                BindUpdatedSecurityLevelPermission('MethodName=UpdateSecurityLevelPermission&ModuleId=' + moduleId + '&Action=UnChkallstages&LevelID=' + levelId + '&checked=' + checked);
                            }
                            return false;
                        });

                        $(".chkNavigation").on("click", function() {
                            var splits_id = this.id.split('_');
                            var moduleId = splits_id[1];
                            var checked = false;
                            if (!$(this).is(':checked')) {
                                checked = false;
                            } else {
                                checked = true;
                            }

                            if ((moduleId != null || moduleId != "" || moduleId != "undefined")) {
                                BindUpdatedSecurityLevelPermission('MethodName=UpdateSecurityLevelPermission&ModuleId=' + moduleId + '&Action=chknavigation&LevelID=' + levelId + '&checked=' + checked);
                            }
                            return false;
                        });
                    } else {
                        $("#lblgetLevelMsg").html(data.message);
                        $("#lblgetLevelMsg").css("display", "block");
                        $("#divGetSecurityLevels").css("display", "block");

                        return false;
                    }
                } else {
                    $("#lblgetLevelMsg").html(data.message);
                    $("#lblgetLevelMsg").css("display", "block");
                    $("#divGetSecurityLevels").css("display", "block");

                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }


    $("#aLinkSecurityLevelBack").on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });


    function BindUpdatedSecurityLevelPermission(data) {
        $body.addClass("loading");

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);
                if (data.status == "1") {
                    //alert(data.message);
                    BindSecurityLevelDetails('MethodName=GetSecurityLevelDetails&levelId=' + levelId);

                } else {
                    //alert(data.message);
                    $('#lblgetLevelMsg').html(data.message);
                    $("#lblgetLevelMsg").css("display", "block");
                    return false;
                }
                $body.removeClass("loading");
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }


});

myApp.onPageInit('smstemplates', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();

    BindSMSTextTemplates('MethodName=GetSMSTextTemplates');

    function BindSMSTextTemplates(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $('#divSmsContent').empty();
                var items = [];
                if (data.status != 0) {
                    if (data.strSmsTemplates != null) {
                        $('#divSmsContent').append(data.strSmsTemplates);

                        $(".aLinkTop").on("click", function() {
                            BindSMSTextTemplates('MethodName=GetSMSTextTemplates');
                            $("#ddljump").focus();
                            //$("#aBackLinkSmsTemplate").focus();
                            return false;
                        });

                        $(".btnSaveSmsTemplate").on("click", function() {
                            var retval = false;
                            var splits_id = this.id.split('_');
                            var templateId = splits_id[1];
                            var validateDiv = "tempTr" + templateId + "  input";
                            var ans = check_itemsvalidate(validateDiv);

                            if (ans) {
                                var subject = $("#subject_" + templateId).val();
                                var body = $("#tempBody_" + templateId).val();

                                var active = 0;
                                if ($("#chkActive_" + templateId).prop('checked')) { active = 1; }

                                BindUpdatedSMSTextTemplates('MethodName=UpdateSMSTextTemplates&id=' + templateId + '&subject=' + subject + '&body=' + body + '&active=' + active);
                                return false;
                            } else { return false; }
                        });

                        $(".btnRevertToOriginal").on("click", function() {
                            var retval = false;
                            var splits_id = this.id.split('_');
                            var templateId = splits_id[1];

                            BindUpdatedSMSTextTemplates('MethodName=RevertSmsTextTemplateContent&id=' + templateId + '&action=revert');
                            return false;
                        });

                        $("#btnRevertAllTemplates").on("click", function() {
                            BindUpdatedSMSTextTemplates('MethodName=RevertALLSmsTextTemplateContent');
                            return false;
                        });

                        $('#ddljump').change(function() {
                            var selValue = $('#ddljump').val();
                            if (selValue != null && selValue != "" && selValue != "undefined") {
                                var splits_id = selValue.split('_');
                                var templId = splits_id[1];
                                var focusDiv = "#subject_" + templId;
                                $(focusDiv).focus();
                                return false;
                            } else { return false; }
                        });
                    } else {
                        $('#lblMsgForSMSTextTemplate').html(data.message);
                        $("#lblMsgForSMSTextTemplate").css("display", "block");
                    }
                } else {
                    $('#lblMsgForSMSTextTemplate').html(data.message);
                    $("#lblMsgForSMSTextTemplate").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#aBackLinkSmsTemplate").on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });

    function BindUpdatedSMSTextTemplates(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    BindSMSTextTemplates('MethodName=GetSMSTextTemplates');
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblMsgForSMSTextTemplate').html(obj.message);
                    //$("#lblMsgForSMSTextTemplate").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

});

myApp.onPageInit('usergroups', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();

    BindUsreGroupList('MethodName=GetUserGroupList');

    function BindUsreGroupList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $('#lblUserGroupMainHeader').html("");
                $('#lblUserGroupMainHeader').html("User Groups");
                $("#divmodifyUserGroups").css("display", "block");
                $("#divAssignUserToUserGroup").css("display", "none");

                $('#divUserGroupList').empty();
                var items = [];
                if (data.userGroupList.length > 0) {
                    items.push("<table class='table'><tr><th class='acenter'>Actions</th><th colspan='3'>Group Label</th></tr>");
                    for (var i = 0; i < data.userGroupList.length; i++) {
                        items.push('<tr><td class="acenter"><a class="aDeleteUserGroup" href="javascript:;" id="aDeleteUserGroup_' + data.userGroupList[i].usergroup_id + '"><i class="icon-remove"></i></a></td><td colspan="3"><a class="aEditUserGroupList" href="javascript:;" id="aEditUserGroupList_' + data.userGroupList[i].usergroup_id + '">' + data.userGroupList[i].label + ' (' + data.userGroupList[i].count + ')</a></td></tr>');
                    }
                    items.push('</table>');
                    $('#divUserGroupList').append(items.join(''));
                } else {
                    items.push("<table class='table'><tr><th class='acenter'>Actions</th><th colspan='3'>Group Label</th></tr>");
                    items.push('<tr><td class="acenter" colspan=4>No record found!</td></tr>');
                    items.push('</table>');
                    $('#divUserGroupList').append(items.join(''));
                    return false;
                }



                $(".aDeleteUserGroup").on("click", function() {
                    if (confirm('Are you sure?')) {
                        var splits_id = this.id.split('_');
                        var deleteUserGroupID = splits_id[1];
                        DeleteUserGroup('MethodName=DeleteUserGroupById&UserGroupID=' + deleteUserGroupID);
                        return false;
                    } else { return false; }
                });

                $(".aEditUserGroupList").on("click", function() {

                    var splits_id = this.id.split('_');
                    var editUserGroupID = splits_id[1];
                    EditUserGroup('MethodName=assignUserToUserGroupList&UserGroupID=' + editUserGroupID);
                    return false;

                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindUsreGroupList(data);
            }
        })
    }

    $("#btnAddUserGroup").on("click", function() {

        var ans = check_itemsvalidate('#divAddUserGroup input');
        if (ans) {

            AddUserGroup('MethodName=AddUserGroup&title=' + $("#txtAddUserGroupTitle").val());
            $("#txtAddUserGroupTitle").val("");
            return false;
        } else {
            return false;
        }
        return false;
    });

    $("#btnCancelUserGroup").on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });

    function AddUserGroup(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    //$("#divmodifyUserGroups").css("display", "block");
                    //$("#divAssignUserToUserGroup").css("display", "none");
                    BindUsreGroupList('MethodName=GetUserGroupList');
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    $("#divmodifyUserGroups").css("display", "block");
                    $("#divAssignUserToUserGroup").css("display", "none");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    function DeleteUserGroup(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "post",
            data: data,
            cache: false,
            success: function(data, textstatus, jqxhr) {

                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    //$("#divmodifyUserGroups").css("display", "block");
                    //$("#divAssignUserToUserGroup").css("display", "none");
                    BindUsreGroupList('MethodName=GetUserGroupList');
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    $("#divmodifyUserGroups").css("display", "block");
                    $("#divAssignUserToUserGroup").css("display", "none");
                    return false;
                }
            },
            error: function(jqxhr, textstatus, errormessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function EditUserGroup(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "post",
            data: data,
            cache: false,
            success: function(data, textstatus, jqxhr) {
                var obj = $.parseJSON(data);

                if (obj.groupTitle.length > 0) {
                    for (var i = 0; i < obj.groupTitle.length; i++) {
                        $("#lblUserGroupMainHeader").html("");
                        $("#lblUserGroupMainHeader").html("User Groups - " + obj.groupTitle[i].label);
                        $("#hdnUserGroupId").val(obj.groupTitle[i].usergroup_id);
                    }
                }

                if (obj.userGroupList.length > 0) {

                    $("#divAssignUserToUserGroup").css("display", "block");
                    $("#divmodifyUserGroups").css("display", "none");

                    $('#divmodifyUserGroupList').empty();
                    var items = [];
                    if (obj.userGroupList.length > 0) {
                        items.push("<table class='table'><tr><th>User Name </th></tr>");

                        for (var i = 0; i < obj.userGroupList.length; i++) {
                            if (obj.userGroupList[i].usergroups_link_id != null && obj.userGroupList[i].usergroups_link_id != "") {
                                items.push('<tr><td><span class="check_users"><input id="selected_user" checked type="checkbox" value=' + obj.userGroupList[i].user_id + ' name="selected_user" /></span><b>' + obj.userGroupList[i].FullName + '</b></td></tr>');
                            } else {
                                items.push('<tr><td><span class="check_users"><input id="selected_user" type="checkbox" value=' + obj.userGroupList[i].user_id + ' name="selected_user" /></span>' + obj.userGroupList[i].FullName + '</td></tr>');
                            }
                        }
                        items.push('<tr><td class="next_privcenter"><input id="btnSaveUserGroupList" type="submit" name="btnSaveUserGroupList" value="Save" class="form_submit bulebtn_comn btn_doc"/><input id="btnResetUserGroupList" type="button" name="btnResetUserGroupList" value="Reset" class="form_submit greybtn_comn btn_doc"/></td></tr></table>');
                        $('#divmodifyUserGroupList').append(items.join(''));
                    } else {
                        items.push("<table class='table'><tr><th>User Name </th></tr>");
                        items.push('<tr><td>No record found!</td></tr>');
                        items.push('</table>');
                        $('#divmodifyUserGroupList').append(items.join(''));
                        return false;
                    }
                    $('#btnResetUserGroupList').on("click", function() {
                        var groupID = $("#hdnUserGroupId").val();
                        $("#aBackLinkUserGroupList").focus();
                        EditUserGroup('MethodName=assignUserToUserGroupList&UserGroupID=' + groupID);

                        return false;
                    });

                    $('#btnSaveUserGroupList').on("click", function() {
                        var selected = new Array();
                        $(".check_users input:checkbox[name=selected_user]:checked").each(function() {
                            selected.push($(this).val());
                        });
                        //alert("Group Id =>" + $("#hdnUserGroupId").val());
                        //alert(selected);
                        var groupID = $("#hdnUserGroupId").val();
                        var data = 'MethodName=UpdateUserGroupsUserDetails&groupID=' + groupID + '&userList=' + selected;
                        $.ajax({
                            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                            type: "POST",
                            data: data,
                            cache: false,
                            success: function(data, textStatus, jqxhr) {
                                var data = $.parseJSON(data);

                                if (data.status == "1") {
                                    navigator.notification.alert(
                                        data.message, alertDismissed, "Successful", "Done"
                                    );
                                    EditUserGroup('MethodName=assignUserToUserGroupList&UserGroupID=' + groupID);
                                    return false;
                                } else {
                                    navigator.notification.alert(
                                        data.message, alertDismissed, "Unsuccessful", "Done"
                                    );
                                    return false;
                                }
                                return false;
                            },
                            error: function(jqxhr, textStatus, errorMessage) {
                                navigator.notification.alert(
                                    errorMessage, alertDismissed, "An error occured", "Done"
                                );
                            }
                        });
                        return false;
                    });
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    $("#lblUserGroupMainHeader").html("");
                    $("#lblUserGroupMainHeader").html("User Groups " + obj.userGroupList.label);
                    $("#divAssignUserToUserGroup").css("display", "block");
                    $("#divmodifyUserGroups").css("display", "none");
                    return false;
                }
            },
            error: function(jqxhr, textstatus, errormessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#aBackLinkUserGroupList").on("click", function() {
        BindUsreGroupList('MethodName=GetUserGroupList');
        $('#lblUserGroupMainHeader').html("");
        $('#lblUserGroupMainHeader').html("User Groups");
        $("#divmodifyUserGroups").css("display", "block");
        $("#divAssignUserToUserGroup").css("display", "none");
        return false;
    });

    $('#aUserGroupBack').on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });

});

myApp.onPageInit('emailtemplates', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();

    BindEmailTemplates('MethodName=GetEmailTemplates');

    function BindEmailTemplates(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $('#divEmailContent').empty();
                var items = [];

                if (data.status != 0) {
                    if (data.strEmailTemplates != null) {

                        $('#divEmailContent').append(data.strEmailTemplates);

                        $(".aLinkTop").on("click", function() {
                            BindEmailTemplates('MethodName=GetEmailTemplates');
                            $("#ddlEmailJump").focus();
                            return false;
                        });

                        $(".btnSaveEmailTemplate_").on("click", function() {
                            var retval = false;
                            var splits_id = this.id.split('_');
                            var templateId = splits_id[1];
                            var validateDiv = "tempTr" + templateId + "  input";
                            var ans = check_itemsvalidate(validateDiv);

                            if (ans) {
                                var subject = $("#subject_" + templateId).val();
                                var body = $("#tempBody_" + templateId).val();
                                body = body.replace("&", "_and_").replace("&", "_and_");

                                var active = 0;
                                if ($("#chkActive_" + templateId).prop('checked')) { active = 1; }

                                BindUpdatedEmailTemplates("MethodName=UpdateEmailTemplates&tempId=" + templateId + "&subject=" + subject + "&body=" + body + "&active=" + active);
                                return false;
                            } else { return false; }
                        });

                        $(".btnRevertToOriginal").on("click", function() {
                            var retval = false;
                            var splits_id = this.id.split('_');
                            var templateId = splits_id[1];

                            BindUpdatedEmailTemplates('MethodName=RevertEmailTemplateContent&id=' + templateId + '&action=revert');
                            return false;
                        });

                        $("#btnRevertAllTemplates").on("click", function() {
                            BindUpdatedEmailTemplates('MethodName=RevertALLEmailTemplateContent');
                            return false;
                        });

                        $('#ddlEmailJump').change(function() {
                            var selValue = $('#ddlEmailJump').val();
                            if (selValue != null && selValue != "" && selValue != "undefined") {
                                var splits_id = selValue.split('_');
                                var templId = splits_id[1];
                                var focusDiv = "#subject_" + templId;
                                $(focusDiv).focus();
                                return false;
                            } else { return false; }
                        });
                    } else {
                        $('#lblMsgForEmailTemplate').html(data.message);
                        $("#lblMsgForEmailTemplate").css("display", "block");
                    }
                } else {
                    $('#lblMsgForEmailTemplate').html(data.message);
                    $("#lblMsgForEmailTemplate").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#aBackLinkEmailTemplate").on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });

    function BindUpdatedEmailTemplates(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    BindEmailTemplates('MethodName=GetEmailTemplates');
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblMsgForEmailTemplate').html(obj.message);
                    //$("#lblMsgForEmailTemplate").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

});

myApp.onPageInit('jurisdiction', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();

    BindJurisdictionList('MethodName=GetJurisdictionList');

    function BindJurisdictionList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                $("#divJuryHeaderText").html("");
                $("#divJuryHeaderText").html("Jurisdictions");

                $("#divJurisdiction").css("display", "block");
                $("#aAddJurisdiction").css("display", "block");
                $("#divModifyJurisdiction").css("display", "none");

                var data = $.parseJSON(data);
                $('#lstJurisdiction').empty();
                var items = [];

                if (data.jurisdictions.length > 0) {
                    items.push('<table style="width:100%;" class="table"><tr><th colspan="2" style="font-weight:bold;">Name</th><th class="acenter" style="font-weight:bold;">Actions</th></tr>');
                    for (var i = 0; i < data.jurisdictions.length; i++) {
                        var jurisdictionID = data.jurisdictions[i].jurisdiction_id;
                        items.push('<tr><td colspan="2">' + data.jurisdictions[i].location + '</td><td class="acenter"><a class="aEditJurisdiction" href="javascript:;" id="aEditJurisdiction_' + data.jurisdictions[i].jurisdiction_id + '"><i class="icon-pencil"></i></a>&nbsp;&nbsp;<a class="aRemoveJurisdictions" href="javascript:;" id="aRemoveJurisdictions_' + data.jurisdictions[i].jurisdiction_id + '"><i class="icon-remove"></i></a></td></tr>');
                    }
                    items.push('</table>');
                } else {
                    items.push('<table style="width:100%;" class="table"><tr><th colspan="2" style="font-weight:bold;">Name</th><th class="acenter" style="font-weight:bold;">Actions</th></tr>');
                    items.push('<tr><td class="acenter" colspan="3" >No Record Found!</td></tr>');
                    items.push('</table>');
                    //$('#lblJurisdiction').html(data.message);
                    //$("#lblJurisdiction").css("display", "block");
                }
                $('#lstJurisdiction').append(items.join(''));

                $(".aEditJurisdiction").on("click", function() {

                    var splits_id = this.id.split('_');
                    var editJurID = splits_id[1];
                    $("#hdnJurisdictionID").val(editJurID);

                    BindJurisdictionDetailForEDIT('MethodName=GetJurisdictionDetailForEDIT&juryId=' + editJurID);
                    return false;
                });

                $(".aRemoveJurisdictions").on("click", function() {
                    if (confirm('Are you sure?')) {

                        var splits_id = this.id.split('_');
                        var deleteJuryID = splits_id[1];

                        DeleteJurisdiction('MethodName=deleteJurisdictionItem&id=' + deleteJuryID);
                        return false;
                    } else {
                        return false;
                    }
                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindJurisdictionList(data);
            }
        })
    }

    function BindJurisdictionDetailForEDIT(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $("#divModifyJurisdiction").css("display", "block");
                $("#divJurisdiction").css("display", "none");
                $("#aAddJurisdiction").css("display", "none");

                $("#ddlMinLadderReq").empty();
                $("#ddlMinLadderReq").append($("<option value='0'>None</option>"));
                for (var i = 1; i <= 5; i++) {
                    $("#ddlMinLadderReq").append($("<option value='" + i + "'>" + i + "</option>"));
                }

                if (data.jurisdictionDetails.length > 0) {
                    for (var i = 0; i < data.jurisdictionDetails.length; i++) {

                        $("#divJuryHeaderText").html("");
                        $("#divJuryHeaderText").html("Edit Jurisdictions - '" + data.jurisdictionDetails[i].location + "'");

                        $("#hdnJurisdictionID").val(data.jurisdictionDetails[i].jurisdiction_id);
                        $("#txtJuryTitle").val(data.jurisdictionDetails[i].location);
                        $("#txtMidInspection").val(data.jurisdictionDetails[i].midroof_timing);
                        $('#ddlMinLadderReq').val(data.jurisdictionDetails[i].ladder).attr("selected", "selected");
                        $("#txtJuryLength").val(data.jurisdictionDetails[i].permit_days);
                        $("#txtJuryURL").val(data.jurisdictionDetails[i].permit_url);
                        return false;
                    }
                } else {
                    navigator.notification.alert(
                        "There are some error, please try again later.", alertDismissed, "An error occured", "Done"
                    );
                    return false;
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                navigator.notification.alert(
                    errorThrown, alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        });
    }

    function DeleteJurisdiction(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    BindJurisdictionList('MethodName=GetJurisdictionList');
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnSaveJurisdiction").on("click", function() {

        var retval = false;
        var ans = check_itemsvalidate('#divAddJurisdiction input');
        var jurisdictionId = $("#hdnJurisdictionID").val();

        if (jurisdictionId != 0) {
            if (ans) {
                UpdateJurisdictionDetails('MethodName=UpdateJurisdictionDetails&id=' + jurisdictionId + '&title=' + $("#txtJuryTitle").val() + '&midroof_timing=' + $("#txtMidInspection").val() + '&length=' + $("#txtJuryLength").val() + '&ladder=' + $("#ddlMinLadderReq").val() + '&url=' + $("#txtJuryURL").val());
                return false;
            } else {
                return false;
            }
        } else {
            if (ans) {
                UpdateJurisdictionDetails('MethodName=AddJurisdictionItem&&title=' + $("#txtJuryTitle").val() + '&midroof_timing=' + $("#txtMidInspection").val() + '&length=' + $("#txtJuryLength").val() + '&ladder=' + $("#ddlMinLadderReq").val() + '&url=' + $("#txtJuryURL").val());
                return false;
            } else {
                return false;
            }
        }
    });

    function UpdateJurisdictionDetails(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#hdnJurisdictionID").val("0");
                    $("#txtJuryTitle").val("");
                    $("#txtMidInspection").val("");
                    $("#txtJuryLength").val("");
                    $("#txtJuryURL").val("");
                    BindJurisdictionList('MethodName=GetJurisdictionList');
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    $('#lblJuryMsgMainLbl').html(data.message);
                    $("#lblJuryMsgMainLbl").css("display", "block");
                    $("#divJurisdiction").css("display", "none");
                    $("#aAddJurisdiction").css("display", "none");
                    $("#divModifyJurisdiction").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
                return false;
            },
        });
        return false;
    }

    $("#aAddJurisdiction").on("click", function() {

        $("#hdnJurisdictionID").val("0");
        $("#txtJuryTitle").val("");
        $("#txtMidInspection").val("");
        $("#txtJuryLength").val("");
        $("#txtJuryURL").val("");

        $("#divJurisdiction").css("display", "none");
        $("#aAddJurisdiction").css("display", "none");

        $("#divModifyJurisdiction").css("display", "block");
        $("#divJuryHeaderText").html("");
        $("#divJuryHeaderText").html("New Jurisdiction");
        $("#ddlMinLadderReq").empty();
        $("#ddlMinLadderReq").append($("<option value='0'>None</option>"));
        for (var i = 1; i <= 5; i++) {
            $("#ddlMinLadderReq").append($("<option value='" + i + "'>" + i + "</option>"));
        }
        return false;
    });

    $("#btnCancelJurisdiction").on("click", function() {
        $("#divJurisdiction").css("display", "block");
        $("#aAddJurisdiction").css("display", "block");
        $("#divModifyJurisdiction").css("display", "none");
        $("#divJuryHeaderText").html("");
        $("#divJuryHeaderText").html("Jurisdiction");
        jQuery(".formError").remove();
    });

    $('#aJurisdictionBack').on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });
});

myApp.onPageInit('providers', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();

    BindInsProviderList('MethodName=GetInsProviderList');

    function BindInsProviderList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $('#divEditInsProvider').empty();
                var items = [];
                if (data.InsProviders.length > 0) {
                    items.push("<table class='table'><tr><th colspan='3'>Name</th><th class='acenter'>Actions</th></tr>");
                    for (var i = 0; i < data.InsProviders.length; i++) {
                        items.push('<tr><td colspan="3">' + data.InsProviders[i].insurance + '</td><td class="acenter"><a class="aDeleteInsProvider" href="javascript:;" id="aDeleteInsProvider_' + data.InsProviders[i].insurance_id + '"><i class="icon-remove"></i></a></td></tr>');
                    }
                    items.push('</table>');
                    $('#divEditInsProvider').append(items.join(''));
                } else {

                    items.push("<table class='table'><tr><th colspan='3'>Name</th><th class='acenter'>Actions</th></tr>");
                    items.push('<tr><td class="acenter" colspan="4">No Record Found!</td></tr>');
                    items.push('</table>');
                    //$('#lblModifyInsProvider').html(data.message);
                    //$("#lblModifyInsProvider").css("display", "block");
                    $("#divInsuranceProvider").css("display", "block");
                    $('#divEditInsProvider').append(items.join(''));
                    return false;
                }



                $(".aDeleteInsProvider").on("click", function() {
                    if (confirm('Are you sure?')) {
                        var splits_id = this.id.split('_');
                        var deleteInsProID = splits_id[1];
                        DeleteInsProvider('MethodName=DeleteInsProvider&ProviderID=' + deleteInsProID);
                        return false;
                    } else { return false; }
                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindInsProviderList(data);
            }
        })
    }

    $("#btnAddInsProvider").on("click", function() {

        var ans = check_itemsvalidate('#divAddInsProvider input');
        if (ans) {

            AddInsProvider('MethodName=AddInsProvider&title=' + $("#txtAddInsProviderTitle").val());
            $("#txtAddInsProviderTitle").val("");
            return false;
        } else {
            return false;
        }
        return false;
    });

    $("#btnCancelInsProvider").on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });

    function AddInsProvider(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divInsuranceProvider").css("display", "block");

                    BindInsProviderList('MethodName=GetInsProviderList');
                    //$('#lblModifyInsProvider').html(obj.message);
                    //$("#lblModifyInsProvider").css("display", "block");
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyInsProvider').html(obj.message);
                    //$("#lblModifyInsProvider").css("display", "block");
                    $("#divInsuranceProvider").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    function DeleteInsProvider(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divInsuranceProvider").css("display", "block");

                    BindInsProviderList('MethodName=GetInsProviderList');
                    //$('#lblModifyInsProvider').html(data.message);
                    //$("#lblModifyInsProvider").css("display", "block");
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyInsProvider').html(data.message);
                    //$("#lblModifyInsProvider").css("display", "block");
                    $("#divInsuranceProvider").css("display", "block");

                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#aInsProviderBack').on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });
});

myApp.onPageInit('statusholds', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    $('#txtAddStatusColor').val("#CCCCCC");
    BindStatusHoldList('MethodName=GetStatusHoldList');

    function BindStatusHoldList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $('#divEditStausHoldList').empty();
                var items = [];
                if (data.statusHolds.length > 0) {
                    items.push("<table class='table'><tr><th class='acenter'>Status Holds</th><th class='acenter'>Edit Color</th><th class='acenter' style='width:21%;'>Actions</th></tr>");
                    for (var i = 0; i < data.statusHolds.length; i++) {
                        items.push('<tr><td class="acenter"><a class="aDeleteStatusHold" href="javascript:;" id="aDeleteStatusHold_' + data.statusHolds[i].status_id + '"><i class="icon-remove"></i></a>&nbsp;&nbsp;<input class="form_input form-control validation validate[required[Title cannot be empty]]" type="text" value="' + data.statusHolds[i].status + '" id="txtStatusHold_' + data.statusHolds[i].status_id + '" style="color:' + data.statusHolds[i].color + '" /><input type="text" style="display:none;" value="' + data.statusHolds[i].status + '" id="reset_name_' + data.statusHolds[i].status_id + '" /></td><td class="status_col_icon acenter"><input class="form_input form-control validation validate[required[select color]]" type="color" value="' + data.statusHolds[i].color + '" id="txtStatusColor_' + data.statusHolds[i].status_id + '" /><input type="text" style="display:none;" value="' + data.statusHolds[i].color + '" id="reset_color_' + data.statusHolds[i].status_id + '" /></td><td class="acenter"><a class="aSaveStatusHold  aSaveEdit" href="javascript:;" id="aSaveStatusHold_' + data.statusHolds[i].status_id + '"><i class="fa fa-chevron-circle-right"></i></a><a class="aResetStatusEdit  aResetEdit" href="javascript:;" id="aResetStatusEdit_' + data.statusHolds[i].status_id + '"><i class="fa fa-refresh"></i></a></td></tr>');
                    }
                    items.push('</table>');
                } else {
                    items.push("<table class='table'><tr><th class='acenter'>Status Holds</th><th class='acenter'>Edit Color</th><th class='acenter' style='width:21%;'>Actions</th></tr>");
                    items.push('<tr><td colspan ="3" class="acenter"><b>No Status Holds Found</b></td></tr>');
                    items.push('</table>');
                }
                $('#divEditStausHoldList').append(items.join(''));
                $(".aSaveStatusHold").on("click", function() {

                    var splits_id = this.id.split('_');
                    var editStatusID = splits_id[1];

                    var ans = check_itemsvalidate('#divEditStausHoldList input');
                    if (ans) {
                        EditStatusHoldDetails('MethodName=EditStatusHoldByID&status=' + $("#txtStatusHold_" + editStatusID).val() + '&statusColor=' + $("#txtStatusColor_" + editStatusID).val() + '&statusId=' + editStatusID);
                        return false;
                    } else {
                        return false;
                    }
                });
                $(".aDeleteStatusHold").on("click", function() {
                    if (confirm('Are you sure?')) {
                        var splits_id = this.id.split('_');
                        var deleteStatusID = splits_id[1];
                        DeleteStatusHoldByID('MethodName=DeleteStatusHoldByID&statusID=' + deleteStatusID);
                        return false;
                    } else { return false; }
                });

                $(".aResetStatusEdit").on("click", function() {

                    var splits_id = this.id.split('_');
                    var rstStatusHoldID = splits_id[1];

                    var txtResetColor = $("#reset_color_" + rstStatusHoldID).val();
                    var txtResetName = $("#reset_name_" + rstStatusHoldID).val();


                    $("#txtStatusHold_" + rstStatusHoldID).val(txtResetName)
                    $("#txtStatusColor_" + rstStatusHoldID).val(txtResetColor)

                    return false;
                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnAddStatusHold").on("click", function() {

        var ans = check_itemsvalidate('#divAddHoldStatus input');
        if (ans) {
            AddStatusHold('MethodName=AddStatusHold&status=' + $("#txtAddStatusName").val() + '&statusColor=' + $("#txtAddStatusColor").val());
            $("#txtAddStatusName").val("");
            $("#txtAddStatusColor").val("#CCCCCC");
            return false;
        } else {
            return false;
        }
        return false;
    });

    function AddStatusHold(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    BindStatusHoldList('MethodName=GetStatusHoldList');
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    function EditStatusHoldDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    BindStatusHoldList('MethodName=GetStatusHoldList');
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function DeleteStatusHoldByID(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    BindStatusHoldList('MethodName=GetStatusHoldList');
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnCancelStatusHold").on("click", function() {
        mainView.loadPage("system.html");
    });

    $('#aJStatusHoldBack').on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });
});

myApp.onPageInit('joborigins', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();

    BindJobOriginList('MethodName=GetJobOriginList');

    function BindJobOriginList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $('#divEditJobOrigins').empty();
                var items = [];
                if (data.jobOrigins.length > 0) {
                    items.push("<div><table class='table'><tr><th colspan='3'>Name</th><th class='acenter'>Actions</th></tr>");
                    for (var i = 0; i < data.jobOrigins.length; i++) {
                        items.push('<tr><td colspan="3">' + data.jobOrigins[i].origin + '</td><td class="acenter"><a class="aDeleteJobOrigins" href="javascript:;" id="aDeleteJobOrigins_' + data.jobOrigins[i].origin_id + '"><i class="icon-remove"></i></a></td></tr>');
                    }
                    items.push('</table>');
                } else {
                    items.push("<div><table class='table'><tr><th colspan='3'>Name</th><th class='acenter'>Actions</th></tr>");
                    items.push('<tr><td class="acenter" colspan="4">No Record Found!</td></tr>');
                    items.push('</table>');
                    //$('#lblModifyInsProvider').html(data.message);
                    //$("#lblModifyInsProvider").css("display", "block");
                    $("#divJobOrigins").css("display", "block");
                }

                $('#divEditJobOrigins').append(items.join(''));

                $(".aDeleteJobOrigins").on("click", function() {
                    if (confirm('Are you sure?')) {
                        var splits_id = this.id.split('_');
                        var deleteJobOriginID = splits_id[1];
                        DeleteJobOrigin('MethodName=DeleteJobOrigin&JOriginID=' + deleteJobOriginID);
                        return false;
                    } else { return false; }
                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindJobOriginList(data);
            }
        })
    }

    $("#btnAddJobOrigins").on("click", function() {

        var ans = check_itemsvalidate('#divAddJobOrigins input');
        if (ans) {

            AddJobOrigin('MethodName=AddJobOrigin&title=' + $("#txtAddJobOriginsTitle").val());
            $("#txtAddJobOriginsTitle").val("");
            return false;
        } else {
            return false;
        }
        return false;
    });

    $("#btnCancelJobOrigins").on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });

    function AddJobOrigin(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divJobOrigins").css("display", "block");

                    BindJobOriginList('MethodName=GetJobOriginList');
                    //$('#lblModifyJobOrigins').html(obj.message);
                    //$("#lblModifyJobOrigins").css("display", "block");
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyJobOrigins').html(obj.message);
                    //$("#lblModifyJobOrigins").css("display", "block");
                    $("#divJobOrigins").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    function DeleteJobOrigin(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divJobOrigins").css("display", "block");

                    BindJobOriginList('MethodName=GetJobOriginList');
                    //$('#lblModifyJobOrigins').html(data.message);
                    //$("#lblModifyJobOrigins").css("display", "block");
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyJobOrigins').html(data.message);
                    //$("#lblModifyJobOrigins").css("display", "block");
                    $("#divJobOrigins").css("display", "block");

                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#aJobOriginsBack').on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });
});

myApp.onPageInit('modoffices', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    BindModOfficeList('MethodName=GetModOfficeList');
    $('.masked-phone').inputmask('(999) 999-9999', { placeholder: ' ' });

    function BindModOfficeList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                $("#divOfficeHeaderText").html("");
                $("#divOfficeHeaderText").html("Offices");

                $("#divModOffice").css("display", "block");
                $("#aAddOffice").css("display", "block");
                $("#divModifyModOffice").css("display", "none");

                var data = $.parseJSON(data);
                $('#lstModOffice').empty();
                var items = [];

                if (data.modOffice.length > 0) {
                    items.push('<table style="width:100%;" class="table"><tr><th colspan="2" style="font-weight:bold;">Name</th><th class="acenter" style="font-weight:bold;">Actions</th></tr>');
                    for (var i = 0; i < data.modOffice.length; i++) {
                        var modOfficeID = data.modOffice[i].office_id;
                        items.push('<tr><td colspan="2">' + data.modOffice[i].title + '</td><td class="acenter"><a class="aEditModOffice" href="javascript:;" id="aEditModOffice_' + data.modOffice[i].office_id + '"><i class="icon-pencil"></i></a>&nbsp;&nbsp;<a class="aRemoveModOffice" href="javascript:;" id="aRemoveModOffice_' + data.modOffice[i].office_id + '"><i class="icon-remove"></i></a></td></tr>');
                    }
                    items.push('</table>');
                } else {
                    items.push('<table style="width:100%;" class="table"><tr><th colspan="2" style="font-weight:bold;">Name</th><th class="acenter" style="font-weight:bold;">Actions</th></tr>');
                    items.push('<tr><td colspan="3" class="acenter">No Record Found!</td></tr>');
                    items.push('</table>');
                    //$('#lblModOffice').html(data.message);
                    //$("#lblModOffice").css("display", "block");
                }
                $('#lstModOffice').append(items.join(''));

                $(".aEditModOffice").on("click", function() {

                    var splits_id = this.id.split('_');
                    var editOfficeID = splits_id[1];
                    $("#hdnOfficeID").val(editOfficeID);
                    BindStateListforOffice('MethodName=GetStateListforOffice');
                    BindOfficeDetailForEDIT('MethodName=GetOfficeDetailForEDIT&officeID=' + editOfficeID);
                    return false;
                });

                $(".aRemoveModOffice").on("click", function() {
                    if (confirm('Are you sure?')) {

                        var splits_id = this.id.split('_');
                        var deleteOfficeID = splits_id[1];

                        DeleteModOffice('MethodName=DeleteModOffice&officeID=' + deleteOfficeID);
                        return false;
                    } else {
                        return false;
                    }
                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindModOfficeList(data);
            }
        })
    }

    function BindOfficeDetailForEDIT(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $("#divModifyModOffice").css("display", "block");
                $("#divModOffice").css("display", "none");
                $("#aAddOffice").css("display", "none");

                //BindStateListforOffice('MethodName=GetStateListforOffice');

                if (data.officeDetails.length > 0) {
                    for (var i = 0; i < data.officeDetails.length; i++) {

                        $("#divOfficeHeaderText").html("");
                        $("#divOfficeHeaderText").html("Edit Office - '" + data.officeDetails[i].title + "'");
                        $("#hdnOfficeID").val(data.officeDetails[i].office_id);
                        $("#txtOfficeTitle").val(data.officeDetails[i].title);
                        $("#txtOfficePhone").val(data.officeDetails[i].phone);
                        $("#txtOfficeFax").val(data.officeDetails[i].fax);
                        $("#txtOfficeAddress").val(data.officeDetails[i].address);
                        $('#txtOfficeCity').val(data.officeDetails[i].city);
                        $('#ddlOfficeState').val(data.officeDetails[i].state).attr("selected", "selected");
                        $("#txtOfficeZip").val(data.officeDetails[i].zip);

                        return false;
                    }
                } else {
                    navigator.notification.alert(
                        "There are some error, please try again later.", alertDismissed, "An error occured", "Done"
                    );
                    return false;
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {

                navigator.notification.alert(
                    errorThrown, alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        });
    }

    function DeleteModOffice(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    BindModOfficeList('MethodName=GetModOfficeList');
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnSaveOffice").on("click", function() {
        jQuery(".formError").remove();
        var retval = false;
        var ans = check_itemsvalidate('#divAddModOffice input');
        var officeID = $("#hdnOfficeID").val();

        if (officeID != 0) {
            if (ans) {
                UpdateModOfficeDetails('MethodName=UpdateModOfficeDetails&id=' + officeID + '&title=' + $("#txtOfficeTitle").val() + '&phone=' + $("#txtOfficePhone").val() + '&fax=' + $("#txtOfficeFax").val() + '&address=' + $("#txtOfficeAddress").val() + '&city=' + $("#txtOfficeCity").val() + '&state=' + $("#ddlOfficeState").val() + '&zip=' + $("#txtOfficeZip").val());
                return false;
            } else {
                return false;
            }
        } else {
            if (ans) {
                UpdateModOfficeDetails('MethodName=AddModOfficeDetails&title=' + $("#txtOfficeTitle").val() + '&phone=' + $("#txtOfficePhone").val() + '&fax=' + $("#txtOfficeFax").val() + '&address=' + $("#txtOfficeAddress").val() + '&city=' + $("#txtOfficeCity").val() + '&state=' + $("#ddlOfficeState").val() + '&zip=' + $("#txtOfficeZip").val());
                return false;
            } else {
                return false;
            }
        }
    });

    function UpdateModOfficeDetails(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#hdnOfficeID").val("0");
                    $("#txtOfficeTitle").val("");
                    $("#txtOfficePhone").val("");
                    $("#txtOfficeFax").val("");
                    $("#txtOfficeAddress").val("");
                    $('#txtOfficeCity').val("");
                    $("#txtOfficeZip").val("");
                    //$("#ddlOfficeState").empty();
                    $("#divModOffice").css("display", "block");
                    $("#aAddOffice").css("display", "block");
                    $("#divModifyModOffice").css("display", "none");
                    BindModOfficeList('MethodName=GetModOfficeList');
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    $('#lblModOfficeMainLbl').html(data.message);
                    $("#lblModOfficeMainLbl").css("display", "block");
                    $("#divModOffice").css("display", "none");
                    $("#aAddOffice").css("display", "none");
                    $("#divModifyModOffice").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
                return false;
            },
        });
        return false;
    }

    $("#aAddOffice").on("click", function() {

        BindStateListforOffice('MethodName=GetStateListforOffice');

        $("#hdnOfficeID").val("0");
        $("#txtOfficeTitle").val("");
        $("#txtOfficePhone").val("");
        $("#txtOfficeFax").val("");
        $("#txtOfficeAddress").val("");
        $('#txtOfficeCity').val("");
        $("#txtOfficeZip").val("");

        $("#divModOffice").css("display", "none");
        $("#aAddOffice").css("display", "none");
        $("#divModifyModOffice").css("display", "block");
        $("#divOfficeHeaderText").html("");
        $("#divOfficeHeaderText").html("New Office");

        return false;
    });

    $("#btnCancelOffice").on("click", function() {
        jQuery(".formError").remove();
        $("#divModOffice").css("display", "block");
        $("#aAddOffice").css("display", "block");
        $("#divModifyModOffice").css("display", "none");
        $("#divOfficeHeaderText").html("");
        $("#divOfficeHeaderText").html("Offices");
        return false;
    });

    function BindStateListforOffice(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $("#ddlOfficeState").empty();
                $.each(data.stateArray, function(key, value) {
                    $("#ddlOfficeState").append($("<option value='" + key + "'>" + key + "</option>"));
                });
                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                navigator.notification.alert(
                    errorThrown, alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        });
    }

    $('#amodOfficeBack').on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });
});

myApp.onPageInit('tasktypes', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    BindTaskTypeList('MethodName=GetTaskTypeList');

    function BindTaskTypeList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                $("#divTasktypeHeaderText").html("");
                $("#divTasktypeHeaderText").html("Task Types");

                $("#divTaskType").css("display", "block");
                $("#aAddTaskType").css("display", "block");
                $("#divModifyTaskType").css("display", "none");

                var data = $.parseJSON(data);
                $('#lstTaskType').empty();
                var items = [];

                if (data.strTaskTypeList != null) {
                    $('#lstTaskType').append(data.strTaskTypeList);
                } else {
                    $('#lblTaskType').html(data.message);
                    $("#lblTaskType").css("display", "block");
                }

                $(".aEditTaskType").on("click", function() {

                    var splits_id = this.id.split('_');
                    var editTaskTypeID = splits_id[1];
                    $("#hdnTaskTypeID").val(editTaskTypeID);
                    BindTaskTypeDDL('MethodName=getAllTaskTypeForDDl');
                    BindTaskTypeDetailForEDIT('MethodName=GetTaskTypeDetailForEDIT&taskTypeID=' + editTaskTypeID);
                    return false;
                });

                $(".aRemoveTaskType").on("click", function() {
                    if (confirm('Are you sure?')) {

                        var splits_id = this.id.split('_');
                        var deleteTaskTypeID = splits_id[1];

                        DeleteTaskType('MethodName=DeleteTaskType&taskTypeID=' + deleteTaskTypeID);
                        return false;
                    } else {
                        return false;
                    }
                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindTaskTypeList(data);
            }
        })
    }

    function BindTaskTypeDetailForEDIT(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $("#divModifyTaskType").css("display", "block");
                $("#divTaskType").css("display", "none");
                $("#aAddTaskType").css("display", "none");

                if (data.taskDetails.length > 0) {
                    for (var i = 0; i < data.taskDetails.length; i++) {

                        $("#divTasktypeHeaderText").html("");
                        $("#divTasktypeHeaderText").html("Edit Task Type");
                        $("#hdnTaskTypeID").val(data.taskDetails[i].task_type_id);
                        $("#txtTaskTypeName").val(data.taskDetails[i].task);
                        $("#txtTaskTypeColor").val(data.taskDetails[i].color);
                    }
                    //$("#divAutoCreateTaskList").empty();
                    $("#auto-create-tasks").empty();
                    if (data.strAutoCreateTask != null) {
                        $("#auto-create-tasks").append(data.strAutoCreateTask);

                        $(".aRemoveAutoTaskType").on("click", function() {

                            if (confirm('Are you sure?')) {
                                var splits_id = this.id.split('_');
                                var deleteTaskID = splits_id[1];
                                var parentTaskID = $("#hdnTaskTypeID").val();
                                DeleteAutoCreateTaskById('MethodName=DeleteAutoCreateTaskById&deleteTaskID=' + deleteTaskID + '&parentId=' + parentTaskID);
                                return false;
                            } else { return false; }

                        });

                    }
                    return false;
                } else {
                    navigator.notification.alert(
                        "There are some error, please try again later.", alertDismissed, "An error occured", "Done"
                    );
                    return false;
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                navigator.notification.alert(
                    errorThrown, alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        });
    }

    function DeleteTaskType(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    BindTaskTypeList('MethodName=GetTaskTypeList');
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnSaveTaskType").on("click", function() {

        //alert($("#auto-create-tasks li").length);

        var selected = new Array();
        $("#auto-create-tasks li").each(function() {
            selected.push($(this).val());
        });

        var retval = false;
        var ans = check_itemsvalidate('#divAddTaskType input');
        var taskTypeID = $("#hdnTaskTypeID").val();

        if (taskTypeID != 0) {
            if (ans) {
                UpdateTaskTypeDetails('MethodName=UpdateTaskTypeDetails&id=' + taskTypeID + '&taskName=' + $("#txtTaskTypeName").val() + '&taskColor=' + $("#txtTaskTypeColor").val() + '&taskList=' + selected);
                return false;
            } else {
                return false;
            }
        } else {
            if (ans) {
                UpdateTaskTypeDetails('MethodName=AddTaskTypeDetails&taskName=' + $("#txtTaskTypeName").val() + '&taskColor=' + $("#txtTaskTypeColor").val() + '&taskList=' + selected);
                return false;
            } else {
                return false;
            }
        }
    });

    function UpdateTaskTypeDetails(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#hdnTaskTypeID").val("0");
                    $("#txtTaskTypeName").val("");
                    $("#txtTaskTypeColor").val("");
                    //$("#ddlAutoCreateTasks").empty();

                    $("#divTaskType").css("display", "block");
                    $("#aAddTaskType").css("display", "block");
                    $("#divModifyTaskType").css("display", "none");
                    BindTaskTypeList('MethodName=GetTaskTypeList');
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    $('#lblTaskTypeMainLbl').html(data.message);
                    $("#lblTaskTypeMainLbl").css("display", "block");

                    $("#divTaskType").css("display", "none");
                    $("#aAddTaskType").css("display", "none");
                    $("#divModifyTaskType").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
                return false;
            },
        });
        return false;
    }

    $("#aAddTaskType").on("click", function() {

        BindTaskTypeDDL('MethodName=getAllTaskTypeForDDl');

        $("#hdnTaskTypeID").val("0");
        $("#txtTaskTypeName").val("");
        $("#txtTaskTypeColor").val("");
        //$("#ddlAutoCreateTasks").empty();

        $("#divTaskType").css("display", "none");
        $("#aAddTaskType").css("display", "none");
        $("#divModifyTaskType").css("display", "block");
        $("#divTasktypeHeaderText").html("");
        $("#divTasktypeHeaderText").html("Add Task Type");

        $("#auto-create-tasks").empty();
        return false;
    });

    $("#btnCancelTaskType").on("click", function() {
        jQuery(".formError").remove();
        BindTaskTypeList('MethodName=GetTaskTypeList');
        $("#divTaskType").css("display", "block");
        $("#aAddTaskType").css("display", "block");
        $("#divModifyTaskType").css("display", "none");
        $("#divTasktypeHeaderText").html("");
        $("#divTasktypeHeaderText").html("Task Types");
        return false;
    });

    function BindTaskTypeDDL(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $("#ddlAutoCreateTasks").empty();

                if (data.taskTypes.length > 0) {
                    var currentTaskID = $("#hdnTaskTypeID").val();
                    //$("#ddlAutoCreateTasks").append($("<option color='' value=''></option>"));
                    for (var i = 0; i < data.taskTypes.length; i++) {
                        if (data.taskTypes[i].task_type_id != currentTaskID) {
                            $("#ddlAutoCreateTasks").append($("<option color='" + data.taskTypes[i].color + "' value='" + data.taskTypes[i].task_type_id + "'>" + data.taskTypes[i].task + "</option>"));
                        }
                    }
                }
                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                navigator.notification.alert(
                    errorThrown, alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        });
    }

    function DeleteAutoCreateTaskById(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "post",
            data: data,
            cache: false,
            success: function(data, textstatus, jqxhr) {

                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    var ID = $("#hdnTaskTypeID").val();
                    BindTaskTypeDetailForEDIT('MethodName=GetTaskTypeDetailForEDIT&taskTypeID=' + ID);
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textstatus, errormessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnAddTaskType").on("click", function() {

        var selected = new Array();
        $("#auto-create-tasks li").each(function() {
            selected.push($(this).val());
        });

        var autoTaskID = $("#ddlAutoCreateTasks").val();
        //var taskColor = $("#ddlAutoCreateTasks").find(':selected').attr('color');
        var taskColor = $("#ddlAutoCreateTasks option:selected").attr('color');
        var taskName = $("#ddlAutoCreateTasks option:selected").text();
        var TaskIds = selected;
        var IsExist = TaskIds.indexOf(parseInt(autoTaskID));

        if (IsExist == "-1") {
            var strAutoCreateTask = "<li class=liRunTimeCreateTask  value=" + autoTaskID + "  style=padding-bottom:5px;><a id=aLinkRunTimeCreateTask_" + autoTaskID + " class='aLinkRunTimeCreateTask'  href='javascript:;'><i class=icon-remove></i></a><i class=icon-circle style='color: " + taskColor + "'></i>" + taskName + "</li>";
            $("#auto-create-tasks").append(strAutoCreateTask);
            $(".aLinkRunTimeCreateTask").on("click", function() {
                if (confirm('Are you sure?')) {
                    $(this).closest('li').remove();
                    return false;
                } else { return false; }
            });

            return false;
        } else {
            navigator.notification.alert(
                "Auto-create Tasks already Exist", alertDismissed, "Unsuccessful", "Done"
            );
            return false;
        }

        return false;
    });

    $('#aTaskTypesBack').on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });
});

myApp.onPageInit('system-admin', function(page) {

    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    BindAccoutData();
    $('#txtact_Search').val("");

    function BindAccoutData() {

        var data = 'MethodName=GetFilterAccountlist&name=' + $("#txtact_Search").val() + '&limit=' + $("#hdnoffset").val();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                Result = $.parseJSON(data);

                $('#tblactlist').empty();
                $('#divactpaging').empty();
                var strrow = '<tr><th>Account Name</th><th>Contact</th><th>Email</th><th>Reg.Date</th><th>Phone</th><th>Active</th><th></th></tr>';
                strrow += Result.stracc;
                $('#tblactlist').append(strrow);

                $('.chkactiveact').on("click", function() {
                    var splits_id = this.id.split('_');
                    var accountId = splits_id[1];
                    var checkedValue = 0;
                    if (this.checked) {
                        checkedValue = 1;
                    }
                    BindUpdatedAccountDetails('MethodName=UpdateAccountDetailByID&AccountID=' + accountId + '&IsActive=' + checkedValue);
                    return false;
                });

                $('.aEditAccount').on("click", function() {
                    var splits_id = this.id.split('_');
                    var accountId = splits_id[1];
                    mainView.loadPage("addUpdateAccount.html?id=" + accountId);
                    return false;
                });

                $('.aaccountDelete').on("click", function() {
                    if (confirm('Are you sure?')) {
                        var splits_id = this.id.split('_');
                        var accountId = splits_id[1];

                        BindUpdatedAccountDetails('MethodName=DeleteAccountByID&AccountID=' + accountId);
                    }
                    return false;
                });

                var currentOffset = Result.currentOffset;

                if (Result.listcount > 0) {
                    currentOffset = parseInt(currentOffset) + parseInt(Result.listcount);

                    var items = [];
                    if (Result.totalacc > 10) {
                        items.push('<center><a id="btnprev" class="button_small"  style="visibility:hidden;cursor:pointer;"> </a>&nbsp;&nbsp;');
                        items.push('<label>Showing: ' + (parseInt(Result.currentOffset) + 1) + " - " + currentOffset + " of " + parseInt(Result.totalacc) + '</label>');
                        items.push('&nbsp;&nbsp;<a id="btnnext" style="cursor:pointer;" class="button_small" > </a></center>');

                        $('#divactpaging').append(items.join(''));

                        $('#btnnext').on("click", function() {
                            getnextaccounts();
                        });
                        $('#btnprev').on("click", function() {
                            getprevaccounts();
                        });
                        if (parseInt(Result.totalacc) == parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'hidden';
                            document.getElementById('btnprev').style.visibility = 'visible';
                        } else if (parseInt(Result.totalacc) < parseInt(currentOffset)) {
                            document.getElementById('btnnext').style.visibility = 'visible';
                            document.getElementById('btnprev').style.visibility = 'Hidden';
                        } else {
                            if (parseInt(currentOffset) == 10) {
                                document.getElementById('btnprev').style.visibility = 'Hidden';
                            } else {
                                document.getElementById('btnnext').style.visibility = 'visible';
                                document.getElementById('btnprev').style.visibility = 'visible';
                            }
                        }
                    }
                } else {
                    var strrow = '<tr><td colspan="8"><center><b>No Jobs Found</b> </center></tr>';
                    $('#tblactlist').append(strrow);
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                BindAccoutData();
            }
        });

    }
    $('#txtact_Search').keyup(function() {
        BindAccoutData();
    });

    function getnextaccounts() {
        var offset = 0;
        var x = $("#hdnoffset").val();
        offset = parseInt(x) + 10;

        $("#hdnoffset").val(offset)

        BindAccoutData();
    }

    function getprevaccounts() {
        var offset = 0;
        var x = $("#hdnoffset").val();

        offset = parseInt(x) - 10;

        $("#hdnoffset").val(offset)

        BindAccoutData();
    }

    function BindUpdatedAccountDetails(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    BindAccoutData();
                    return false;
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    $('#aAddNewact').on("click", function() {
        mainView.loadPage("addUpdateAccount.html?id=0");
        return false;
    });
});

myApp.onPageInit('addUpdateAccount', function(page) {
    var $ = jQuery.noConflict();
    $body = $("body");

    jQuery(".formError").remove();

    var acctImagePath = '';
    var AccountID = page.query.id;
    BindGetStateList('MethodName=getListOfStateForAccount');

    if (AccountID > 0) {
        BindGetStateList('MethodName=getListOfStateForAccount');
        BindAccountDetailsForEdit('MethodName=GetAccountDetailsForEdit&AccountID=' + AccountID);
        $("#divOfficeHeaderText").html("Edit Account");

        $('#txtAcctFirstName').attr("class", "");
        $('#divAccFN').css("display", "none");
        $('#txtAcctLastName').attr("class", "");
        $('#divAccLN').css("display", "none");
        $('#txtAcctUserName').attr("class", "");
        $('#divAccUC').css("display", "none");
        $('#txtAcctPwd').attr("class", "");
        $('#txtAcctPwd').attr("class", "");
        $('#divAccPWD').css("display", "none");

    } else {

        $('#divAccFN').css("display", "block");
        $('#txtAcctFirstName').addClass("form_input form-control validation validate[required[Enter First Name]]");
        $('#divAccLN').css("display", "block");
        $('#txtAcctLastName').addClass("form_input form-control validation validate[required[Enter Last Name]]");
        $('#divAccUC').css("display", "block");
        $('#txtAcctUserName').addClass("form_input form-control validation validate[required[Enter Usernames]]");
        $('#divAccPWD').css("display", "block");
        $('#txtAcctPwd').addClass("form_input form-control validation validate[required[Enter Password]]");

        $('#txtAcctTitle').val('');
        $('#txtAcctPriContact').val('');
        $('#txtAcctFirstName').val('');
        $('#txtAcctLastName').val('');
        $('#txtAcctUserName').val('');
        $('#txtAcctPwd').val('');
        $('#txtAcctEmail').val('');
        $('#txtAcctPhone').val('');
        $('#txtAcctAddress').val('');
        $('#txtAcctCity').val('');
        $('#txtAcctZip').val('');
        $('#txtAcctJobUnit').val('');
        $("#divOfficeHeaderText").html("Add Account");
    }

    $('#btnAddAccount').on("click", function() {
        try {
            var $ = jQuery.noConflict();
            $body = $("body");

            var retval = false;
            var ans = check_itemsvalidate('#divAddUpdateAccount input');

            if (ans) {

                $body.addClass("loading");
                if (AccountID > 0) {
                    AddUpdateAccountDetails('MethodName=UpdateAccountDetails&AccountID=' + AccountID + '&Title=' + $("#txtAcctTitle").val() + '&PContact=' + $("#txtAcctPriContact").val() + '&Email=' + $("#txtAcctEmail").val() + '&Phone=' + $("#txtAcctPhone").val() + '&Address=' + $("#txtAcctAddress").val() + '&City=' + $("#txtAcctCity").val() + '&State=' + $("#ddlAcctState").val() + '&Zip=' + $("#txtAcctZip").val() + '&JobUnit=' + $("#txtAcctJobUnit").val() + '&licenseLimit=' + $("#txtLicenseLimit").val());
                } else {
                    //AddUpdateAccountDetails('MethodName=AddNewAccountFromSysAdmin&Title=' + $("#txtAcctTitle").val() + '&PContact=' + $("#txtAcctPriContact").val() + '&Email=' + $("#txtAcctEmail").val() + '&Phone=' + $("#txtAcctPhone").val() + '&Address=' + $("#txtAcctAddress").val() + '&City=' + $("#txtAcctCity").val() + '&State=' + $("#ddlAcctState").val() + '&Zip=' + $("#txtAcctZip").val() + '&JobUnit=' + $("#txtAcctJobUnit").val() + '&licenseLimit=' + $("#txtLicenseLimit").val());
                    AddUpdateAccountDetails('MethodName=AddNewAccountFromSysAdmin&Title=' + $("#txtAcctTitle").val() + '&PContact=' + $("#txtAcctPriContact").val() + '&Email=' + $("#txtAcctEmail").val() + '&Phone=' + $("#txtAcctPhone").val() + '&Address=' + $("#txtAcctAddress").val() + '&City=' + $("#txtAcctCity").val() + '&State=' + $("#ddlAcctState").val() + '&Zip=' + $("#txtAcctZip").val() + '&JobUnit=' + $("#txtAcctJobUnit").val() + '&licenseLimit=' + $("#txtLicenseLimit").val() + '&firstname=' + $("#txtAcctFirstName").val() + '&lastname=' + $("#txtAcctLastName").val() + '&username=' + $("#txtAcctUserName").val() + '&password=' + $("#txtAcctPwd").val());
                }

            } else {
                return false;
            }
        } catch (e) {
            navigator.notification.alert(
                e, alertDismissed, "Unsuccessful", "Done"
            );
        }
    });

    $('#btnCancelAddAccount').on("click", function() {
        jQuery(".formError").remove();
        mainView.loadPage("systemadmin.html");
        return false;
    });

    function AddUpdateAccountDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    $body.removeClass("loading");
                    var acctId = data.AccountID;
                    uploadSystemAdminAccountFile(acctId);
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    mainView.loadPage("systemadmin.html");
                    return false;
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    $body.removeClass("loading");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function BindAccountDetailsForEdit(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];
                $("#spnUserAcctImg").empty();
                if (data.status == "1") {

                    if (data.userAcctDetail.length > 0) {

                        for (var i = 0; i < data.userAcctDetail.length; i++) {
                            items.push($("#txtAcctTitle").val(data.userAcctDetail[i].account_name) + $("#txtAcctPriContact").val(data.userAcctDetail[i].primary_contact) + $("#txtAcctEmail").val(data.userAcctDetail[i].email) + $("#txtAcctPhone").val(data.userAcctDetail[i].phone) + $("#txtAcctAddress").val(data.userAcctDetail[i].address) + $("#txtAcctCity").val(data.userAcctDetail[i].city) + $('#ddlAcctState').val(data.userAcctDetail[i].state).attr("selected", "selected") + $("#txtAcctZip").val(data.userAcctDetail[i].zip) + $("#txtAcctJobUnit").val(data.userAcctDetail[i].job_unit) + $("#txtLicenseLimit").val(data.userAcctDetail[i].license_limit));
                            items.push($("#spnUserAcctImg").append("<img height=64 width=64 src='https://xactbid.pocketofficepro.com/logos/" + data.userAcctDetail[i].logo + "' />"));
                            //$("#spnSidePanelLogo").empty();
                            //$("#spnSidePanelLogo").append("<img height=125 width=280 id=imgSysCompanyLogo src='https://xactbid.pocketofficepro.com/logos/" + data.userAcctDetail[i].logo + "' />");
                            ////;
                        }
                    } else {
                        return false;
                    }
                } else {
                    $("#lblAddUpdateAccount").html("");
                    $("#lblAddUpdateAccount").html(data.message);
                    $("#lblAddUpdateAccount").css("display", "block");
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function BindGetStateList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $("#ddlAcctState").empty();
                $.each(data.stateArray, function(key, value) {
                    $("#ddlAcctState").append($("<option value='" + key + "'>" + key + "</option>"));
                });
                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                navigator.notification.alert(
                    errorThrown, alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        });
    }


    // Below Code for the Upload image from the Galley - Starts Here

    $('#lnkcapturegallery').on("click", function() {
        var $ = jQuery.noConflict();
        $body = $("body");

        var retval = false;
        var ans = check_itemsvalidate('#divAddAccount input');

        if (ans) {
            uploadFromGalley(pictureSource.PHOTOLIBRARY);
        } else {
            return false;
        }
    });

    function uploadFromGalley(source) {
        try {
            // Retrieve image file location from specified source
            navigator.camera.getPicture(onPhotoURISuccess, onFail, {
                quality: 50,
                destinationType: destinationType.FILE_URI,
                sourceType: source
            });
        } catch (e) {
            navigator.notification.alert(
                e, alertDismissed, "Unsuccessful", "Done"
            );
        }
    }

    function onFail(message) {
        // alert('Failed because: ' + message);
        var mymsg = 'Failed because: ' + message;
        navigator.notification.alert(
            mymsg, alertDismissed, "Unsuccessful", "Done"
        );
    }

    function onPhotoURISuccess(imageURI) {
        acctImagePath = imageURI;
        var filename = imageURI.substr(imageURI.lastIndexOf('/') + 1);
        $("#lblAcctLogo").text(filename + '.jpg');

        //SaveUploadFileForAccount(imageURI, '7');
    }

    function uploadSystemAdminAccountFile(acctId) {
        if ($('#fluSysLogo').val() != "") {
            var retval = false;
            var ans = check_itemsvalidate('#divSysCompanyProfile input');

            if (ans) {
                var files = $('#fluSysLogo')[0].files;
                var form_data = new FormData();
                var file_data = $('#fluSysLogo').prop('files')[0];
                form_data.append('flag', '8');
                // form_data.append('filename', files[0].name);
                form_data.append('filename', $('#fluSysLogo').val());
                form_data.append('file', file_data);
                form_data.append('accountId', acctId)

                $.when($.ajax({
                    url: 'https://xactbid.pocketofficepro.com/fileuploader.php', // point to server-side PHP script
                    dataType: 'text', // what to expect back from the PHP script, if anything
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function(php_script_response) {
                        //alert('Logo successfully modified');

                        // display response from the PHP script, if any
                    },
                    error: function(data) {
                        //alert('err' + data);
                        var myerr = 'err' + data;
                        navigator.notification.alert(
                            myerr, alertDismissed, "An error occured", "Done"
                        );
                    },
                }).then(function() {
                    BindAccountDetailsForEdit('MethodName=GetAccountDetailsForEdit&AccountID=' + AccountID);
                }));

            }
        }

    }

    //function SaveUploadFileForAccount(imageURI, flag) {

    //    try {
    //        $body.addClass("loading");
    //        var form_data = new FormData();
    //        form_data.append('filepath', imageURI);
    //        var filename = imageURI.substr(imageURI.lastIndexOf('/') + 1);;
    //        form_data.append('filename', filename);
    //        form_data.append('flag', '7');
    //        form_data.append('AccountID', AccountID);
    //        form_data.append('Title', $('#txtAcctTitle').val());
    //        form_data.append('PContact', $('#txtAcctPriContact').val());
    //        form_data.append('Email', $('#txtAcctEmail').val());
    //        form_data.append('Phone', $('#txtAcctPhone').val());
    //        form_data.append('Address', $('#txtAcctAddress').val());
    //        form_data.append('City', $('#txtAcctCity').val());
    //        form_data.append('State', $('#ddlAcctState').val());
    //        form_data.append('Zip', $('#txtAcctZip').val().trim());
    //        form_data.append('JobUnit', $('#txtAcctJobUnit').val().trim());
    //        form_data.append('licenseLimit', $('#txtLicenseLimit').val().trim());

    //        $.when($.ajax({
    //            url: 'https://xactbid.pocketofficepro.com/fileuploader.php',
    //            dataType: 'text',
    //            cache: false,
    //            contentType: false,
    //            processData: false,
    //            data: form_data,
    //            type: 'post',
    //            success: function (message) {
    //                alert(message);
    //                $body.removeClass("loading");


    //            },
    //            error: function (data) {
    //                alert('err' + data);
    //            },
    //        }).then(function () {
    //            mainView.loadPage("systemadmin.html");
    //        }));


    //    } catch (e) {
    //        alert(e);
    //    }

    //}
    // Below Code for the Upload image from the Galley - Ends Here

});

myApp.onPageInit('switchuser', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();

    BindUserList('MethodName=getSwitchUserList');

    function BindUserList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $("#ddlSwitchUser").empty();

                if (data.userlist.length > 0) {
                    for (var i = 0; i < data.userlist.length; i++) {
                        var isSelected = "";

                        if (data.userlist[i].user_id == data.currentUser) {
                            isSelected = "selected";

                        }
                        $("#ddlSwitchUser").append($("<option value='" + data.userlist[i].user_id + "'  " + isSelected + ">" + data.userlist[i].lname + ", " + data.userlist[i].fname + " (" + data.userlist[i].account_name + ") " + " - " + data.userlist[i].level + "</option>"));
                    }

                }

                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                navigator.notification.alert(
                    errorThrown, alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        });
    }

    $('#btnResetSwitchUser').on("click", function() {
        BindUserList('MethodName=getSwitchUserList');
        return false;
    });

    $('#btnSaveSwitchUser').on("click", function() {
        var userId = $('#ddlSwitchUser').val();
        SwitchUser('MethodName=SwitchUser&selVal=' + userId);
        return false;
    });

    function SwitchUser(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {


                var obj = $.parseJSON(data);

                if (obj.status == "1") {

                    sessionStorage.clear();
                    localStorage.clear();
                    window.localStorage.clear();
                    $("#lblmsg").css('display', 'none');
                    window.localStorage.setItem("sessLoginId", obj.User.ao_userid);
                    window.localStorage.setItem("sessUserName", obj.User.ao_username);
                    window.localStorage.setItem("sessLevel", obj.User.ao_level);
                    window.localStorage.setItem("sessAccountId", obj.User.ao_accountid);
                    window.localStorage.setItem("sessFname", obj.User.ao_fname);
                    window.localStorage.setItem("sessLname", obj.User.ao_lname);
                    window.localStorage.setItem("ao_founder", obj.User.ao_founder);
                    window.localStorage.setItem("ao_password", obj.User.ao_password);
                    if (obj.modules != "0")
                        window.localStorage.setItem("ao_module_access", JSON.stringify(obj.modules));
                    if (obj.navigations != "0")
                        window.localStorage.setItem("ao_nav_access", JSON.stringify(obj.navigations));

                    // alert("Successfully switched to " + obj.User.ao_fname + " " + obj.User.ao_lname + " ( " + obj.User.ao_accountname + ")");
                    var myalert = "Successfully switched to " + obj.User.ao_fname + " " + obj.User.ao_lname + " ( " + obj.User.ao_accountname + ")";
                    navigator.notification.alert(
                        myalert, alertDismissed, "Successful", "Done"
                    );
                    location.href = "home.html";
                } else {
                    $("#lblmsg").css('display', 'block');
                    $("#lblmsg").text(obj.message);

                    return false;
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert("There was an error. Try again please!", alertDismissed, "An error occured", "Done");
            }
        })
    }
});

myApp.onPageInit('schedule', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    BindScheduleUserAndTypeList('MethodName=GetScheduleUserAndTypeList');

    BindScheduleDetails('MethodName=GetSchedulerDetails&view=weekview&userId=0&typeId=');

    function BindScheduleUserAndTypeList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);


                $("#ddlScheduleUser").empty();
                $("#ddlScheduleUser").append($("<option value='0'>User</option>"));

                $.each(data.userList, function(key, value) {
                    $("#ddlScheduleUser").append($("<option value='" + value.user_id + "'>" + value.lname + ', ' + value.fname + "</option>"));
                });

                $("#ddlScheduleType").empty();
                if (data.typeList.length > 0) {
                    $("#ddlScheduleType").append($("<option value=''>Type</option>"));
                    $("#ddlScheduleType").append($("<option value='appointment'>Appointment</option>"));
                    $("#ddlScheduleType").append($("<option value='delivery'>Delivery</option>"));
                    $("#ddlScheduleType").append($("<option value='event'>Event</option>"));
                    $("#ddlScheduleType").append($("<option value='repair'>Repair</option>"));
                    for (var i = 0; i < data.typeList.length; i++) {
                        $("#ddlScheduleType").append($("<option value='" + data.typeList[i].task_type_id + "'>" + data.typeList[i].task + "</option>"));
                    }
                }

            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function BindScheduleDetails(data) {
        $body = jQuery("body");
        $body.addClass("loading");
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $("#spnScheduleDetail").empty();
                if (data.status == "1") {
                    if (data.strSchedule != null) {
                         $body.removeClass("loading");
                        $("#spnScheduleDetail").append(data.strSchedule);
                        $("#alinkPrev").on("click", function() {

                            var ws = $(this).attr('data-ws');
                            var year = $(this).attr('data-year');
                            var month = $(this).attr('data-month');
                            BindScheduleDetails('MethodName=GetSchedulerDetails&view=weekview&m=' + month + '&Y=' + year + '&userId=' + $("#ddlScheduleUser").val() + '&typeId=' + $("#ddlScheduleType").val() + '&ws=' + ws);
                            return false;
                        });
                        $("#alinkNext").on("click", function() {

                            var ws = $(this).attr('data-ws');
                            var year = $(this).attr('data-year');
                            var month = $(this).attr('data-month');
                            BindScheduleDetails('MethodName=GetSchedulerDetails&view=weekview&m=' + month + '&Y=' + year + '&userId=' + $("#ddlScheduleUser").val() + '&typeId=' + $("#ddlScheduleType").val() + '&ws=' + ws);
                            return false;
                        });
                        return false;
                    } else {
                        $("#lblSchedule").html("No Schedule Details Found!");
                        $("#lblSchedule").css("display", "block");
                        return false;
                    }
                } else {
                     $body.removeClass("loading");
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                $body.removeClass("loading");
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnWeekView").on("click", function() {
        BindScheduleDetails('MethodName=GetSchedulerDetails&view=weekview&userId=' + $("#ddlScheduleUser").val() + '&typeId=' + $("#ddlScheduleType").val());
        return false;
    });

    $("#btnScheduleClearFilter").on("click", function() {
        $('#ddlScheduleUser').val(0);
        $('#ddlScheduleType').val('');
        BindScheduleDetails('MethodName=GetSchedulerDetails&view=weekview&userId=' + $("#ddlScheduleUser").val() + '&typeId=' + $("#ddlScheduleType").val());
        return false;
    });

    $("#btnMonthView").on("click", function() {
        BindScheduleViewByMonth('MethodName=GetSchedulerMonthView&view=monthview&userId=' + $("#ddlScheduleUser").val() + '&typeId=' + $("#ddlScheduleType").val());
        return false;
    });

    function BindScheduleViewByMonth(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $("#spnScheduleDetail").empty();
                if (data.status == "1") {
                    if (data.strScheduleMonth != null) {
                        $("#spnScheduleDetail").append(data.strScheduleMonth);

                        $("#alinkPrevMonth").on("click", function() {
                            var year = $(this).attr('data-year');
                            var month = $(this).attr('data-month');
                            BindScheduleViewByMonth('MethodName=GetSchedulerMonthView&view=monthview&userId=' + $("#ddlScheduleUser").val() + '&typeId=' + $("#ddlScheduleType").val() + '&m=' + month + '&y=' + year);
                            return false;
                        });
                        $("#alinkNextMonth").on("click", function() {
                            var year = $(this).attr('data-year');
                            var month = $(this).attr('data-month');
                            BindScheduleViewByMonth('MethodName=GetSchedulerMonthView&view=monthview&userId=' + $("#ddlScheduleUser").val() + '&typeId=' + $("#ddlScheduleType").val() + '&m=' + month + '&y=' + year);
                            return false;
                        });
                        return false;
                    } else {
                        $("#lblSchedule").html("No Schedule Details Found!");
                        $("#lblSchedule").css("display", "block");
                        return false;
                    }
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    //$("#btnSendInvitation").on("click",function () {
    //    var from_name = "Workflow";
    //    var from_address = "virbhadra@renustechnologix.com";
    //    var to_name = "Veer";
    //    var to_address = "virbhadra.renus@gmail.com";
    //    var startTime = "10/12/2015 10:00:00";
    //    var endTime = "12/12/2015 19:00:00";
    //    var subject = "Testing Invitation Functionality";
    //    var description = "My Awesome Description";
    //    var location = "Los angelos, CA, USA";
    //    sendInvitation('MethodName=sendIcalEvent&from_name='+ from_name +'&from_address='+ from_address +'&to_name='+ to_name +'&to_address='+ to_address +'&startTime='+ startTime +'&endTime='+ endTime +'&subject='+ subject +'&description='+ description +'&location=' + location);
    //});


    function sendInvitation(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "post",
            data: data,
            cache: false,
            success: function(data, textstatus, jqxhr) {

                var obj = $.parseJSON(data);
                navigator.notification.alert(
                    obj, alertDismissed, "Successful", "Done"
                );
            },
            error: function(jqxhr, textstatus, errormessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }


});

myApp.onPageInit('modmaterials', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    BindSysModMaterialsCatList('MethodName=GetSysModMaterialsCatList');

    function BindSysModMaterialsCatList(data) {
        try {
            $.ajax({
                url: "https://xactbid.pocketofficepro.com/workflowservice.php",
                type: 'POST',
                data: data,
                cache: false,
                success: function(data, textStatus, jqxhr) {

                    var data = $.parseJSON(data);
                    $('#lstModMaterialCat').empty();
                    $('#divModMaterialHeader').html("");
                    $('#divModMaterialHeader').html("Materials");
                    var items = [];
                    if (data.categoryList.length > 0) {
                        items.push('<table class="table"><tr><th>Material Category</th></tr>');
                        for (var i = 0; i < data.categoryList.length; i++) {
                            items.push('<tr><td><a href="javascript:;"  id=MatCatId_' + data.categoryList[i].category_id + ' class="MatCategory">' + data.categoryList[i].category + '</a></td></tr>');
                        }
                        items.push('</table>');

                    } else {
                        items.push('<table class="table"><tr><th>Material Category</th></tr>');
                        items.push('<tr><td class="acenter">No Record Found!</td></tr>');
                        items.push('</table>');
                        //$('#lblModMaterialCat').html("No Categories Found");
                        //$("#lblModMaterialCat").css("display", "block");
                    }
                    $('#lstModMaterialCat').append(items.join(''));
                    $(".MatCategory").on("click", function() {

                        var splits_id = this.id.split('_');
                        var MaterialCatID = splits_id[1];
                        BindSysModMatDetailForCategory('MethodName=GetSysModMatDetailForCategory&CategoryID=' + MaterialCatID);
                        return false;
                    });
                },
                error: function(jqxhr, textStatus, errorMessage) {
                    navigator.notification.alert(
                        errorMessage, alertDismissed, "An error occured", "Done"
                    );
                }
            })
        } catch (e) {

        }
    }

    function BindSysModMatDetailForCategory(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $("#divModMaterialCat").css("display", "none");
                $("#divaModMaterialsBack").css("display", "none");
                $("#divModMaterialDetail").css("display", "block");
                $('#lstModMaterialDetail').empty();
                $('#divModMaterialHeader').html("");
                $('#divModMaterialHeader').html("Materials");
                var items = [];
                items.push('<table class="table"><th>Materials</th>');
                if (data.categoryArray.length > 0) {
                    for (var i = 0; i < data.categoryArray.length; i++) {
                        $('#hdnModMatCatId').val(data.categoryArray[i].category_id);
                        items.push('<tr><td>' + data.categoryArray[i].category + '</td></tr>');
                    }
                    items.push('</table>');

                }
                //else {
                //    alert("category not found");
                //}
                $('#lstModMaterialDetail').append(items.join(''));

                $('#lstModMateBrandDetail').empty();
                var items3 = [];
                items3.push('<table class="table">')
                var BrandId = 0;
                if (data.matDetailSql.length > 0) {

                    for (var i = 0; i < data.matDetailSql.length; i++) {
                        if (data.matDetailSql[i].brand_id != "0") {
                            if (BrandId == data.matDetailSql[i].brand_id) {
                                items3.push('<tr style="padding-bottom:10px;"><td  class="mod_icon"><a id="aLinkDelModMaterial_' + data.matDetailSql[i].material_id + '" href="javascript:;" class="aLinkDelModMaterial"><i class="icon-remove"></i></a><a id="aLinkEditModMaterial_' + data.matDetailSql[i].material_id + '" href="javascript:;" class="aLinkEditModMaterial"><i class="icon-pencil"></i></a></td><td>' + data.matDetailSql[i].material + '</td><td>' + data.matDetailSql[i].info + '<br /><b>Unit: </b>' + data.matDetailSql[i].unit + '<br /><b>Price: </b>' + data.matDetailSql[i].price + '</td></tr>');
                            } else {
                                BrandId = data.matDetailSql[i].brand_id;
                                var brandCNT = "";
                                if (data.brandcount.length > 0) {
                                    for (var k = 0; k < data.brandcount.length; k++) {
                                        if (data.brandcount[k].brand_id == "-1") {
                                            brandCNT = " (" + data.brandcount[k].count + ")";
                                        } else if (data.brandcount[k].brand_id == BrandId) {
                                            brandCNT = " (" + data.brandcount[k].count + ")";
                                        }
                                    }
                                }
                                if (data.matDetailSql[i].brand_id == "-1") {
                                    items3.push('<tr style="padding-bottom:10px;"><td class="mod_icon" colspan="3" style=" font-weight:bold;">Varies ' + brandCNT + '</td></tr><tr><td><a id="aLinkDelModMaterial_' + data.matDetailSql[i].material_id + '" href="javascript:;" class="aLinkDelModMaterial"><i class="icon-remove"></i></a><a id="aLinkEditModMaterial_' + data.matDetailSql[i].material_id + '" href="javascript:;" class="aLinkEditModMaterial"><i class="icon-pencil"></i></a></td><td>' + data.matDetailSql[i].material + '</td><td>' + data.matDetailSql[i].info + '<br /><b>Unit: </b>' + data.matDetailSql[i].unit + '<br /><b>Price: </b>' + data.matDetailSql[i].price + '</td></tr>');
                                    brandCNT = "";
                                } else {
                                    items3.push('<tr><td colspan="3" style="font-weight:bold;">' + data.matDetailSql[i].brand + '' + brandCNT + '</td></tr><tr style="padding-bottom:10px;"><td class="mod_icon"><a id="aLinkDelModMaterial_' + data.matDetailSql[i].material_id + '" href="javascript:;" class="aLinkDelModMaterial"><i class="icon-remove"></i></a><a id="aLinkEditModMaterial_' + data.matDetailSql[i].material_id + '" href="javascript:;" class="aLinkEditModMaterial"><i class="icon-pencil"></i></a></td><td>' + data.matDetailSql[i].material + '</td><td>' + data.matDetailSql[i].info + '<br /><b>Unit: </b>' + data.matDetailSql[i].unit + '<br /><b>Price: </b>' + data.matDetailSql[i].price + '</td></tr>');
                                    brandCNT = "";
                                }
                            }
                        }
                    }
                    items3.push('</table>');
                }
                //else {
                //    alert("category not found");
                //}
                $('#lstModMateBrandDetail').append(items3.join(''));
                $('.aLinkDelModMaterial').on("click", function() {
                    if (confirm('Are you sure?')) {
                        var splits_id = this.id.split('_');
                        var MatID = splits_id[1];

                        DeleteSysModMaterial('MethodName=DeleteSysModMaterial&materialID=' + MatID);
                        return false;
                    } else { return false; }
                });
                $('.aLinkEditModMaterial').on("click", function() {
                    $('#divModMaterialHeader').html("");
                    $('#divModMaterialHeader').html("Edit Material");

                    var splits_id = this.id.split('_');
                    var MatID = splits_id[1];
                    BindModMaterialDDl('MethodName=GetMaterialsBrand_Category_Unit');
                    BindModMaterialEditDetailByID('MethodName=GetModMaterialEditDetailByID&materialID=' + MatID);
                    $('#hdnModMaterialID').val(MatID);
                    $('#divParentModMaterials').css("display", "none");
                    $('#divAddEditModMaterial').css("display", "block");
                    $('#divEditMatDetails').css("display", "block");
                    $('#divAddNewMatItem').css("display", "none");
                    return false;
                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#aLinkModMateBack').on("click", function() {
        $('#hdnModMatCatId').val("0");
        $("#divModMaterialCat").css("display", "block");
        $("#divaModMaterialsBack").css("display", "block");
        $("#divModMaterialDetail").css("display", "none");
        BindSysModMaterialsCatList('MethodName=GetSysModMaterialsCatList');
    });

    function DeleteSysModMaterial(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    var MaterialCatID = $('#hdnModMatCatId').val();
                    BindSysModMatDetailForCategory('MethodName=GetSysModMatDetailForCategory&CategoryID=' + MaterialCatID);
                    return false;
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#aLinkEditMatBrand').on("click", function() {
        var modMateCatId = $("#hdnModMatCatId").val();
        //alert(modMateCatId);
        mainView.loadPage("modify_materials_brand_category.html?flag=0");
        return false;
    });

    $('#aLinkEditMatCategory').on("click", function() {
        var modMateCatId = $("#hdnModMatCatId").val();
        //alert(modMateCatId);
        mainView.loadPage("modify_materials_brand_category.html?flag=1");
        return false;
    });

    $('#aLinkAddNewMaterial').on("click", function() {
        $('#divModMaterialHeader').html("");
        $('#divModMaterialHeader').html("Add Material");
        $('#divParentModMaterials').css("display", "none");
        $('#divAddEditModMaterial').css("display", "block");
        $('#divAddNewMatItem').css("display", "block");
        $('#divEditMatDetails').css("display", "none");


        $('#txtAddMaterialName').val("");
        $('#txtAddMaterialPrice').val("");
        $('#txtAddMatDescription').val("");

        BindModMaterialDDl('MethodName=GetMaterialsBrand_Category_Unit');
        $('#hdnModMaterialID').val("0");
        return false;
    });

    $('#btnCancelAddModMaterial').on("click", function() {
        jQuery(".formError").remove();
        $('#divModMaterialHeader').html("");
        $('#divModMaterialHeader').html("Materials");
        $('#divParentModMaterials').css("display", "block");
        $('#divAddEditModMaterial').css("display", "none");
        return false;
    });

    $('#btnSaveAddModMaterial').on("click", function() {
        var retval = false;
        var ans = check_itemsvalidate('#divAddNewMatItem input');
        //var modMatCatId = $("#hdnModMatCatId").val();
        //var modMateId = $("#hdnModMaterialID").val();
        //alert("Category ID " + modMatCatId);
        //alert("Material ID " + modMateId);
        if (ans) {
            AddNewModMaterialItem('MethodName=AddNewModMaterialItem&CatId=' + $('#ddlAddMaterialCat').val() + '&BrandID=' + $('#ddlAddMaterialBrand').val() + '&UnitID=' + $('#ddlAddMaterialUnit').val() + '&MatName=' + $('#txtAddMaterialName').val() + '&desc=' + $('#txtAddMatDescription').val() + '&Price=' + $('#txtAddMaterialPrice').val());
            return false;
        } else {
            return false;
        }
        return false;
    });

    function AddNewModMaterialItem(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $('#divModMaterialHeader').html("");
                    $('#divModMaterialHeader').html("Materials");
                    $('#divParentModMaterials').css("display", "block");
                    $('#divAddEditModMaterial').css("display", "none");
                    var MaterialCatID = $('#hdnModMatCatId').val();
                    BindSysModMatDetailForCategory('MethodName=GetSysModMatDetailForCategory&CategoryID=' + MaterialCatID);
                    return false;
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#btnCancelEditModMaterial').on("click", function() {

        $('#txtEditModMatTitle').val("");
        //$('#ddlEditModMatBrand').val("");
        //$('#ddlEditModMatCat').val("");
        $('#txtEditModMatPrice').val("");
        //$('#ddlEditModMatUnit').val("");
        $('#txtEditModMatDescription').val("");
        $('#txtEditModMatAddColor').val("");
        jQuery(".formError").remove();

        $('#divModMaterialHeader').html("");
        $('#divModMaterialHeader').html("Materials");
        $('#divParentModMaterials').css("display", "block");
        $('#divAddEditModMaterial').css("display", "none");
        return false;
    });

    $('#btnSaveEditModMaterial').on("click", function() {
        $("#txtEditModMatAddColor").removeClass("form_input form-control validation validate[required[Enter Color]] ");
        var retval = false;
        var ans = check_itemsvalidate('#divEditMatDetails input');
        //var modMatCatId = $("#hdnModMatCatId").val();
        var modMateId = $("#hdnModMaterialID").val();
        //alert("Category ID " + modMatCatId);
        //alert("Material ID " + modMateId);
        if (ans) {
            var IsActive = '0';

            if ($('#chkEditModMatActive').is(":checked")) {
                IsActive = '1';
            }

            UpdateModMaterialDetails('MethodName=UpdateModMaterialDetails&MaterialID=' + modMateId + '&CatId=' + $('#ddlEditModMatCat').val() + '&BrandID=' + $('#ddlEditModMatBrand').val() + '&UnitID=' + $('#ddlEditModMatUnit').val() + '&MatName=' + $('#txtEditModMatTitle').val() + '&desc=' + $('#txtEditModMatDescription').val() + '&Price=' + $('#txtEditModMatPrice').val() + '&IsActive=' + IsActive);
            return false;
        } else {
            return false;
        }
        return false;
    });

    function UpdateModMaterialDetails(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $('#divModMaterialHeader').html("");
                    $('#divModMaterialHeader').html("Materials");
                    $('#divParentModMaterials').css("display", "block");
                    $('#divAddEditModMaterial').css("display", "none");
                    var MaterialCatID = $('#hdnModMatCatId').val();
                    BindSysModMatDetailForCategory('MethodName=GetSysModMatDetailForCategory&CategoryID=' + MaterialCatID);
                    return false;
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#btnAddModMatColor').on("click", function() {
        $("#txtEditModMatAddColor").addClass("form_input form-control validation validate[required[Enter Color]] ");
        var retval = false;
        var ans = check_itemsvalidate('#divAddMatColor input');
        if (ans) {

            //var modMatCatId = $("#hdnModMaterialID").val();
            var modMateId = $("#hdnModMaterialID").val();
            //alert("Category ID " + modMatCatId);
            //alert("Material ID " + modMateId);
            AddModMaterialColor('MethodName=AddModMaterialColor&MaterialID=' + modMateId + '&color=' + $("#txtEditModMatAddColor").val());
            $("#txtEditModMatAddColor").val('');
            $("#txtEditModMatAddColor").removeClass("form_input form-control validation validate[required[Enter Color]] ");
            return false;
        } else {
            return false;
        }
    });

    function AddModMaterialColor(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    var MaterialCatID = $('#hdnModMatCatId').val();
                    var modMateId = $("#hdnModMaterialID").val();
                    //BindSysModMatDetailForCategory('MethodName=GetSysModMatDetailForCategory&CategoryID=' + MaterialCatID);
                    BindModMaterialEditDetailByID('MethodName=GetModMaterialEditDetailByID&materialID=' + modMateId);
                    return false;
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function BindModMaterialEditDetailByID(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                var items = [];
                if (data.matDetails.length > 0) {
                    for (var i = 0; i < data.matDetails.length; i++) {
                        items.push($("#txtEditModMatTitle").val(data.matDetails[i].material) + $('#ddlEditModMatBrand').val(data.matDetails[i].brand_id).attr("selected", "selected") + $('#ddlEditModMatCat').val(data.matDetails[i].category_id).attr("selected", "selected") + $('#ddlEditModMatUnit').val(data.matDetails[i].unit_id).attr("selected", "selected") + $("#txtEditModMatDescription").val(data.matDetails[i].info) + $("#txtEditModMatPrice").val(data.matDetails[i].price));
                        if (data.matDetails[i].active == "1") {
                            $('#chkEditModMatActive').attr('checked', true);
                        }
                    }
                    $('#ulMaterialColorList').empty();
                    if (data.strColor != null) {
                        $('#ulMaterialColorList').append(data.strColor);

                        $('.aRemovelinkMatColor').on("click", function() {
                            var splits_id = this.id.split('_');
                            var ColorId = splits_id[1];
                            DeleteModMaterialColor('MethodName=DeleteModMaterialColor&colorId=' + ColorId);
                            return false;
                        });
                    }
                    return false;
                } else {
                    navigator.notification.alert(
                        "There are some error in fetching Insured detail, please try again later.", alertDismissed, "An error occured", "Done"
                    );
                    return false;
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                navigator.notification.alert(
                    "Error", alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        });
    }

    function BindModMaterialDDl(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                $("#ddlEditModMatBrand").empty();
                $("#ddlAddMaterialBrand").empty();
                if (data.brands.length > 0) {
                    $("#ddlEditModMatBrand").append($("<option value='-1'>Varies</option>"));
                    $("#ddlAddMaterialBrand").append($("<option value='-1'>Varies</option>"));
                    for (var i = 0; i < data.brands.length; i++) {
                        $("#ddlEditModMatBrand").append($("<option value='" + data.brands[i].brand_id + "'>" + data.brands[i].brand + "</option>"));
                        $("#ddlAddMaterialBrand").append($("<option value='" + data.brands[i].brand_id + "'>" + data.brands[i].brand + "</option>"));
                    }
                }

                $("#ddlEditModMatCat").empty();
                $("#ddlAddMaterialCat").empty();
                if (data.categories.length > 0) {
                    for (var i = 0; i < data.categories.length; i++) {
                        $("#ddlEditModMatCat").append($("<option value='" + data.categories[i].category_id + "'>" + data.categories[i].category + "</option>"));
                        $("#ddlAddMaterialCat").append($("<option value='" + data.categories[i].category_id + "'>" + data.categories[i].category + "</option>"));

                    }
                }

                $("#ddlEditModMatUnit").empty();
                $("#ddlAddMaterialUnit").empty();
                if (data.units.length > 0) {
                    for (var i = 0; i < data.units.length; i++) {
                        $("#ddlEditModMatUnit").append($("<option value='" + data.units[i].unit_id + "'>" + data.units[i].unit + "</option>"));
                        $("#ddlAddMaterialUnit").append($("<option value='" + data.units[i].unit_id + "'>" + data.units[i].unit + "</option>"));
                    }
                }

                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                navigator.notification.alert(
                    errorThrown, alertDismissed, "An error occured", "Done"
                );
                return false;
            }
        });
    }

    function DeleteModMaterialColor(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    //var MaterialCatID = $('#hdnModMatCatId').val();
                    var modMateId = $("#hdnModMaterialID").val();
                    //BindSysModMatDetailForCategory('MethodName=GetSysModMatDetailForCategory&CategoryID=' + MaterialCatID);
                    BindModMaterialEditDetailByID('MethodName=GetModMaterialEditDetailByID&materialID=' + modMateId);
                    return false;
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#aModMaterialsBack').on("click", function() {
        mainView.loadPage("system.html");
        return false;
    });
});

myApp.onPageInit('modifyMaterialsBrandCategory', function(page) {
    var flag = page.query.flag;
    if (flag == "0") {
        BindMaterialsBrandList('MethodName=GetBrandAndCategoryListforModifyMaterial');
    } else {
        BindMaterialsCategoryList('MethodName=GetBrandAndCategoryListforModifyMaterial');
    }

    function BindMaterialsBrandList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $('#lblModifyMaterialPageMainHeader').html("");
                $('#lblModifyMaterialPageMainHeader').html("Modify Brands");
                $("#divmodifyMaterialsBrand").css("display", "block");
                $("#divmodifyMaterialsCategory").css("display", "none");

                $('#divMatBrandList').empty();
                var items = [];
                if (data.brands_array.length > 0) {
                    items.push("<table class='table'><tr><th class='acenter' colspan='4'>Brands</th></tr>");
                    for (var i = 0; i < data.brands_array.length; i++) {
                        items.push('<tr><td class="acenter"><a class="aDeleteMatBrand" href="javascript:;" id="aDeleteMatBrand_' + data.brands_array[i].brand_id + '"><i class="icon-remove"></i></a></td><td><input class="form_input form-control validation validate[required[Title cannot be empty]]" type="text" value="' + data.brands_array[i].brand + '" id="matBrand_' + data.brands_array[i].brand_id + '" /></td><td class="acenter"><a class="aEditMatBrand" href="javascript:;" id="aEditMatBrand_' + data.brands_array[i].brand_id + '"><i class="icon-pencil"></i></a></td></tr>');
                    }
                    items.push('</table>');
                    $('#divMatBrandList').append(items.join(''));
                } else {
                    items.push("<table class='table'><tr><th class='acenter' colspan='4'>Brands</th></tr>");
                    items.push('<tr><td class="acenter">No record found!</td></tr>');
                    items.push('</table>');
                    $('#divMatBrandList').append(items.join(''));
                    return false;
                }



                $(".aDeleteMatBrand").on("click", function() {
                    if (confirm('Are you sure?')) {
                        var splits_id = this.id.split('_');
                        var deleteBrandID = splits_id[1];
                        DeleteMaterialBrandByID('MethodName=deleteBrandFromModifyMaterial&brandID=' + deleteBrandID);
                        return false;
                    } else { return false; }
                });

                $(".aEditMatBrand").on("click", function() {
                    var splits_id = this.id.split('_');
                    var editBrandID = splits_id[1];

                    var ans = check_itemsvalidate('#divMatBrandList input');
                    if (ans) {
                        EditMaterialBrandByID('MethodName=updateBrandFromModifyMaterial&brandID=' + editBrandID + '&title=' + $("#matBrand_" + editBrandID).val());
                        return false;
                    } else { return false; }

                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function EditMaterialBrandByID(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divmodifyMaterialsBrand").css("display", "block");
                    BindMaterialsBrandList('MethodName=GetBrandAndCategoryListforModifyMaterial');

                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyMaterialsBrandCategory').html(data.message);
                    //$("#lblModifyMaterialsBrandCategory").css("display", "block");
                    $("#divmodifyMaterialsBrand").css("display", "block");

                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function DeleteMaterialBrandByID(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divmodifyMaterialsBrand").css("display", "block");
                    BindMaterialsBrandList('MethodName=GetBrandAndCategoryListforModifyMaterial');
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyMaterialsBrandCategory').html(data.message);
                    //$("#lblModifyMaterialsBrandCategory").css("display", "block");
                    $("#divmodifyMaterialsBrand").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnCancelMatBrand").on("click", function() {
        mainView.loadPage("modmaterials.html");
    });

    $("#aModMatBrandCatBack").on("click", function() {
        mainView.loadPage("modmaterials.html");
    });

    $("#btnAddMatBrand").on("click", function() {

        var ans = check_itemsvalidate('#divAddMaterialBrand input');
        if (ans) {

            AddMaterialBrand('MethodName=insertBrandFromModifyMaterial&title=' + $("#txtAddMatBrandTitle").val());
            $("#txtAddMatBrandTitle").val("");
            return false;
        } else {
            return false;
        }
        return false;
    });

    function AddMaterialBrand(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divmodifyMaterialsBrand").css("display", "block");

                    BindMaterialsBrandList('MethodName=GetBrandAndCategoryListforModifyMaterial');
                    //$('#lblModifyMaterialsBrandCategory').html(obj.message);
                    //$("#lblModifyMaterialsBrandCategory").css("display", "block");
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyMaterialsBrandCategory').html(obj.message);
                    //$("#lblModifyMaterialsBrandCategory").css("display", "block");
                    $("#divmodifyMaterialsBrand").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    "There was an error while Add Group. Try again please!", alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }


    function BindMaterialsCategoryList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $('#lblModifyMaterialPageMainHeader').html("");
                $('#lblModifyMaterialPageMainHeader').html("Modify Categories");
                $("#divmodifyMaterialsBrand").css("display", "none");
                $("#divmodifyMaterialsCategory").css("display", "block");

                $('#divMatCategoryList').empty();
                var items = [];
                if (data.categories.length > 0) {
                    items.push("<table class='table'><tr><th class='acenter' colspan='4'>Categories</th></tr>");
                    for (var i = 0; i < data.categories.length; i++) {
                        items.push('<tr><td class="acenter"><a class="aDeleteMatCategory" href="javascript:;" id="aDeleteMatCategory_' + data.categories[i].category_id + '"><i class="icon-remove"></i></a></td><td><input class="form_input form-control validation validate[required[Title cannot be empty]]" type="text" value="' + data.categories[i].category + '" id="matCategory_' + data.categories[i].category_id + '" /></td><td class="acenter"><a class="aEditMatCategory" href="javascript:;" id="aEditMatCategory_' + data.categories[i].category_id + '"><i class="icon-pencil"></i></a></td></tr>');
                    }
                    items.push('</table>');
                    $('#divMatCategoryList').append(items.join(''));
                } else {
                    items.push("<table class='table'><tr><th class='acenter' colspan='4'>Categories</th></tr>");
                    items.push('<tr><td class="acenter">No record found!</td></tr>');
                    items.push('</table>');
                    $('#divMatCategoryList').append(items.join(''));
                    return false;
                }



                $(".aDeleteMatCategory").on("click", function() {
                    if (confirm('Are you sure?')) {
                        var splits_id = this.id.split('_');
                        var deleteCatID = splits_id[1];
                        DeleteMaterialCategoryByID('MethodName=deleteCategoryFromModifyMaterial&catID=' + deleteCatID);
                        return false;
                    } else { return false; }
                });

                $(".aEditMatCategory").on("click", function() {
                    var splits_id = this.id.split('_');
                    var editCatID = splits_id[1];

                    var ans = check_itemsvalidate('#divMatCategoryList input');
                    if (ans) {
                        EditMaterialCategoryByID('MethodName=updateCategoryFromModifyMaterial&catID=' + editCatID + '&title=' + $("#matCategory_" + editCatID).val());
                        return false;
                    } else { return false; }

                });
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function EditMaterialCategoryByID(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divmodifyMaterialsCategory").css("display", "block");
                    BindMaterialsCategoryList('MethodName=GetBrandAndCategoryListforModifyMaterial');

                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyMaterialsBrandCategory').html(data.message);
                    //$("#lblModifyMaterialsBrandCategory").css("display", "block");
                    $("#divmodifyMaterialsCategory").css("display", "block");

                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function DeleteMaterialCategoryByID(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divmodifyMaterialsCategory").css("display", "block");
                    BindMaterialsCategoryList('MethodName=GetBrandAndCategoryListforModifyMaterial');
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyMaterialsBrandCategory').html(data.message);
                    //$("#lblModifyMaterialsBrandCategory").css("display", "block");
                    $("#divmodifyMaterialsCategory").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnCancelMatCategory").on("click", function() {
        mainView.loadPage("modmaterials.html");
    });

    $("#btnAddMatCategory").on("click", function() {

        var ans = check_itemsvalidate('#divAddMaterialCategory input');
        if (ans) {

            AddMaterialCategory('MethodName=insertCategoryFromModifyMaterial&title=' + $("#txtAddMatCategoryTitle").val());
            $("#txtAddMatCategoryTitle").val("");
            return false;
        } else {
            return false;
        }
        return false;
    });

    function AddMaterialCategory(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var obj = $.parseJSON(data);

                if (obj.status == "1") {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Successful", "Done"
                    );
                    $("#divmodifyMaterialsCategory").css("display", "block");

                    BindMaterialsCategoryList('MethodName=GetBrandAndCategoryListforModifyMaterial');
                    //$('#lblModifyMaterialsBrandCategory').html(obj.message);
                    //$("#lblModifyMaterialsBrandCategory").css("display", "block");
                    return false;
                } else {
                    navigator.notification.alert(
                        obj.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblModifyMaterialsBrandCategory').html(obj.message);
                    //$("#lblModifyMaterialsBrandCategory").css("display", "block");
                    $("#divmodifyMaterialsCategory").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

});

myApp.onPageInit('get_insurance_detail', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    var insuranceType = page.query.type;
    var insExpireDate = page.query.dt;
    var insuredUserId = page.query.id;
    var insuranceTitle = '';

    bindUserInsuranceDetails('MethodName=GetUserInsuranceDetails&userId=' + insuredUserId);

    function bindUserInsuranceDetails(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];

                if (data.userDetails.length > 0) {

                    if (insuranceType == 1) {
                        insuranceTitle = 'General liability insurance';
                        items.push($("#lblInsuredUserName").text(data.userDetails[0].lname + ", " + data.userDetails[0].fname) + $("#lblInsuranceExpDt").text(data.userDetails[0].generalins) + $("#lblInsuranceType").text(insuranceTitle));
                    } else if (insuranceType == 2) {
                        insuranceTitle = 'Workers Compensations insurance';
                        items.push($("#lblInsuredUserName").text(data.userDetails[0].lname + ", " + data.userDetails[0].fname) + $("#lblInsuranceExpDt").text(data.userDetails[0].workerins) + $("#lblInsuranceType").text(insuranceTitle));
                    }

                    return false;
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#btnViewApptBack').on("click", function() {
        mainView.loadPage("schedule.html");
        return false;
    })

});

myApp.onPageInit('adduser', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    BindOffice_AccessLevel_SmsCarrierDDL('MethodName=getUsersOffice_Level_SmsCarrier');

    function BindOffice_AccessLevel_SmsCarrierDDL(data) {
        var $ = jQuery.noConflict();
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);

                $("#ddlSmsCarrier").empty();
                $("#ddlSmsCarrier").append("<option value='0'>NO SMS</option>");
                for (var i = 0; i < data.carriersArray.length; i++) {
                    $("#ddlSmsCarrier").append($("<option value='" + data.carriersArray[i].sms_id + "'>" + data.carriersArray[i].carrier + "</option>"));
                }
                $("#ddlAccessLevel").empty();
                for (var i = 0; i < data.userLevelsArray.length; i++) {
                    $("#ddlAccessLevel").append($("<option value='" + data.userLevelsArray[i].level_id + "'>" + data.userLevelsArray[i].level + "</option>"));
                }
                $("#ddlOffice").empty();
                $("#ddlOffice").append("<option value='0'>Default</option>");
                for (var i = 0; i < data.officesArray.length; i++) {
                    $("#ddlOffice").append($("<option value='" + data.officesArray[i].office_id + "'>" + data.officesArray[i].title + "</option>"));
                }

                return false;
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
        return false;
    }

    $("#btnCancelUser").on("click", function() {
        jQuery(".formError").remove();
        mainView.loadPage("users.html");
        return false;
    });
    $("#btnSaveUser").on("click", function() {
        var ans = check_itemsvalidate('#divAddUser input');
        var filter = /^[A-Za-z0-9]+$/;
        if (ans) {
            if (filter.test($("#txtUserName").val())) {
                if ($("#txtUserName").val().length >= 6) {

                    var chkFounder = "0";
                    if ($('#chkFounder').is(":checked")) { chkFounder = 1; }

                    AddNewUser('MethodName=InsertNewUser&fname=' + $("#txtFirstName").val() + '&lname=' + $("#txtLastName").val() + '&uname=' + $("#txtUserName").val() + '&Dba=' + $("#txtDBA").val() + '&email=' + $("#txtEmail").val() + '&phone=' + $("#txtPhone").val() + '&smsCarrier=' + $("#ddlSmsCarrier").val() + '&Notes=' + $("#txtNotes").val() + '&accessLevel=' + $("#ddlAccessLevel").val() + '&office=' + $("#ddlOffice").val() + '&founder=' + chkFounder);
                    return false;
                } else {
                    navigator.notification.alert(
                        "Username Must be at least 6 characters", alertDismissed, "Unsuccessful", "Done"
                    );
                    $("#txtUserName").focus();
                    return false;
                }
            } else {
                navigator.notification.alert(
                    "Username can only be letters or numbers", alertDismissed, "Unsuccessful", "Done"
                );
                $("#txtUserName").focus();
                return false;
            }
        } else { return false; }

    });

    function AddNewUser(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);
                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    mainView.loadPage("users.html");
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    //$('#lblAddUser').html(data.message);
                    //$("#lblAddUser").css("display", "block");
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

});

myApp.onPageInit('add_event', function(page) {
    var srcPage = page.query.srcPage;
    var date = page.query.date;
    var eventID = page.query.id;
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();

    bindEventTimeList('MethodName=getEventTimeList');
    if (eventID > 0 && eventID != "undefined") {
        $('#divHeaderForAddEvent').html("");
        $('#divHeaderForAddEvent').html("View Event Detail");
        $('#divAddEvent').css("display", "none");
        $('#divViewEventDetail').css("display", "block");
        $('#divGuestInvitation').css("display", "none");
        BindEventDetailsById('MethodName=GetEventDetailsById&Id=' + eventID);
    } else {
        $('#divHeaderForAddEvent').html("");
        $('#divHeaderForAddEvent').html("Add Event");
        $('#divAddEvent').css("display", "block");
        $('#divViewEventDetail').css("display", "none");
        $('#txtEventStartDate').val(date);
        $('#txtEventEndDate').val(date);
        $('#divGuestInvitation').css("display", "block");
    }



    DateCallJS();

    function DateCallJS() {
        var $ = jQuery.noConflict();
        $('.datestamp').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: "1925:2999",
            onClose: function() {
                if (this.value != '') {
                    $ = jQuery.noConflict();
                    $.validationEngine.loadValidation('.datestamp');
                }
            }
        });
    }


    $('#btnSaveEvent').on("click", function() {


        $("#txtAddGuest").removeClass("form_input form-control validation validate[required[Enter Email]] validate[funcCall[validateCustomEmailAddress[Invalid E-mail]]]");

        var eventTitle = $('#txtEventTitle').val();
        var eventDesc = $('#txtEventDesc').val();

        var isGlobal = 0;

        var groupId = $('#ddlUserGroup').val();

        var StartDate = $('#txtEventStartDate').val();
        var EndDate = $('#txtEventEndDate').val();
        var StartTime = $('#ddlEventStartTime select[name=time]').val();
        var EndTime = $('#ddlEventEndTime select[name=time]').val();

        var ans = check_itemsvalidate('#divAddEvent input');
        if (ans) {
            if (StartDate > EndDate) {
                navigator.notification.alert(
                    "End Date must be greater than Start Date", alertDismissed, "Unsuccessful", "Done"
                );
                return false;
            } else if (StartDate == EndDate) {
                if (StartTime >= EndTime) {
                    navigator.notification.alert(
                        "End Time must be greater than Start Time", alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                } else {
                    if (eventID > 0) {
                        if ($('#chkEditEventGlobal').is(":checked")) {
                            isGlobal = 1;
                        }
                        addEventForScheduleDate('MethodName=UpdateEventDetailsForScheduleDate&eventID=' + eventID + '&title=' + $('#txtEventTitle').val() + '&startDt=' + StartDate + '&endDt=' + EndDate + '&startTime=' + StartTime + '&endTime=' + EndTime + '&global=' + isGlobal + '&groupId=' + $('#ddlUserGroup').val() + '&Desc=' + $('#txtEventDesc').val());
                    } else {
                        if ($('#chkEventGlobal').is(":checked")) {
                            isGlobal = 1;
                        }

                        addEventForScheduleDate('MethodName=addEventForScheduleDate&title=' + $('#txtEventTitle').val() + '&startDt=' + StartDate + '&endDt=' + EndDate + '&startTime=' + StartTime + '&endTime=' + EndTime + '&global=' + isGlobal + '&groupId=' + $('#ddlUserGroup').val() + '&Desc=' + $('#txtEventDesc').val());
                    }
                    return false;
                }
            } else {
                if (eventID > 0) {
                    if ($('#chkEditEventGlobal').is(":checked")) {
                        isGlobal = 1;
                    }
                    addEventForScheduleDate('MethodName=UpdateEventDetailsForScheduleDate&eventID=' + eventID + '&title=' + $('#txtEventTitle').val() + '&startDt=' + StartDate + '&endDt=' + EndDate + '&startTime=' + StartTime + '&endTime=' + EndTime + '&global=' + isGlobal + '&groupId=' + $('#ddlUserGroup').val() + '&Desc=' + $('#txtEventDesc').val());
                } else {
                    if ($('#chkEventGlobal').is(":checked")) {
                        isGlobal = 1;
                    }
                    addEventForScheduleDate('MethodName=addEventForScheduleDate&title=' + $('#txtEventTitle').val() + '&startDt=' + StartDate + '&endDt=' + EndDate + '&startTime=' + StartTime + '&endTime=' + EndTime + '&global=' + isGlobal + '&groupId=' + $('#ddlUserGroup').val() + '&Desc=' + $('#txtEventDesc').val());
                }
                return false;
            }
        } else { return false; }
    });

    function addEventForScheduleDate(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);

                if (data.status == "1") {

                    var StartDate = $('#txtEventStartDate').val();
                    var EndDate = $('#txtEventEndDate').val();
                    var StartTime = $('#ddlEventStartTime select[name=time]').val();
                    var EndTime = $('#ddlEventEndTime select[name=time]').val();

                    var selected = new Array();
                    $("#ulInvitedGuestList li").each(function() {
                        selected.push($(this).text());
                    });
                    //sendInvitation('MethodName=sendIcalEvent&to_name=' + $(this).text() + '&to_address=' + $(this).text() + '&startDate=' + StartDate + '&endDate=' + EndDate + '&startTime=' + StartTime + '&endTime=' + EndTime + '&subject=' + $('#txtEventTitle').val() + '&description=' + $('#txtEventDesc').val() + '&location=location' + '&guestList='+ selected);
                    if (selected.length > 0) {
                        $("#ulInvitedGuestList li").each(function() {
                            sendInvitation('MethodName=sendIcalEvent&to_name=' + $(this).text() + '&to_address=' + $(this).text() + '&startDate=' + StartDate + '&endDate=' + EndDate + '&startTime=' + StartTime + '&endTime=' + EndTime + '&subject=' + $('#txtEventTitle').val() + '&description=' + $('#txtEventDesc').val() + '&location=location' + '&guestList=' + selected);
                        });
                    }

                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    $('#txtEventTitle').val('');
                    $('#txtEventStartDate').val('');
                    $('#txtEventEndDate').val('');
                    $('#ddlEventStartTime').val('');
                    $('#ddlEventEndTime').val('');
                    $('#txtEventDesc').val('');
                    mainView.loadPage("schedule.html");
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#btnCancelEvent').on("click", function() {
        jQuery(".formError").remove();
        $('#txtEventTitle').val('');
        $('#txtEventStartDate').val('');
        $('#txtEventEndDate').val('');
        $('#ddlEventStartTime').val('');
        $('#ddlEventEndTime').val('');
        $('#txtEventDesc').val('');

        mainView.loadPage("schedule.html");

        return false;
    });

    function bindEventTimeList(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data) {
                var data = $.parseJSON(data);
                $("#ddlEventStartTime").empty();
                $("#ddlEventEndTime").empty();
                if (data.timeList.length > 0) {
                    $("#ddlEventStartTime").append(data.timeList);
                    $("#ddlEventEndTime").append(data.timeList);


                    $("#ddlUserGroup").empty();
                    for (var i = 0; i < data.groups.length; i++) {
                        $("#ddlUserGroup").append($("<option value='" + data.groups[i].usergroup_id + "'>" + data.groups[i].label + "</option>"));
                    }

                    return false;
                } else {
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                bindEventTimeList(data);
            }
        })
    }

    $("#chkEventGlobal").change(function() {
        if (this.checked) {
            $('#divEventUserGroup').css("display", "block");
        } else {
            $('#divEventUserGroup').css("display", "none");
        }
    });

    function BindEventDetailsById(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];
                var global_str = 'No';
                if (data.event.length > 0) {
                    for (var i = 0; i < data.event.length; i++) {
                        if (data.event[i].global == 1) {
                            global_str = 'Yes';
                        }
                        items.push($("#lblEventTitle").text(data.event[i].title) + $("#lblEventCreator").text(data.event[i].lname + ", " + data.event[i].fname) + $("#lblEventStartDt").text(data.event[i].StartDate) + $("#lblEventEndDt").text(data.event[i].EndDate) + $("#lblEventStartTime").text(data.event[i].startTime) + $("#lblEventEndTime").text(data.event[i].EndTime) + $("#lblEventGlobal").text(global_str) + $("#lblUserGroups").text(data.user_groups_str) + $("#lblEventDesc").text(data.event[i].text));
                    }

                    if (data.canEditEvent == false) {
                        $('#tdEditEvent').css("display", "none");
                    }
                    return false;
                } else {
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $('#btnCancelEventDetail').on("click", function() {
        jQuery(".formError").remove();
        if (srcPage != null && srcPage != "" && srcPage != "undefined") {
            mainView.loadPage(srcPage);
        } else {
            mainView.loadPage("schedule.html");
        }
        return false;
    });

    $('#btnDelEventDetail').on("click", function() {
        if (confirm('Are you sure?')) {
            DeleteEventDetailByID('MethodName=DeleteEventDetailByID&Id=' + eventID);
            return false;
        } else { return false; }
    });

    $('#btnEditEventDetail').on("click", function() {
        $('#divHeaderForAddEvent').html("");
        $('#divHeaderForAddEvent').html("Edit Event Detail");
        $('#divAddEvent').css("display", "block");
        $('#divViewEventDetail').css("display", "none");
        $('#divAddNotifyGroup').css("display", "none");
        $('#divEditNotifyGroup').css("display", "block");
        $('#divGuestInvitation').css("display", "none");
        BindEventDetailForEditByID('MethodName=GetEventDetailsById&Id=' + eventID);
        return false;
    });

    function BindEventDetailForEditByID(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                var items = [];
                var global_str = 'No';
                if (data.event.length > 0) {
                    for (var i = 0; i < data.event.length; i++) {
                        if (data.event[i].global == 1) {
                            global_str = 'Yes';
                        }

                        items.push($("#txtEventTitle").val(data.event[i].title) + $("#txtEventStartDate").val(data.event[i].EditStartDate) + $("#txtEventEndDate").val(data.event[i].EditEndDate) + $("#ddlEventStartTime select[name=time]").val(data.event[i].EditStartTime) + $("#ddlEventEndTime select[name=time]").val(data.event[i].EditEndTime) + $("#txtEventDesc").val(data.event[i].text));
                        if (data.event[i].global == 1) {
                            $('#chkEditEventGlobal').attr('checked', true);
                        }
                    }
                    return false;
                } else {
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    function DeleteEventDetailByID(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "POST",
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {

                var data = $.parseJSON(data);

                if (data.status == "1") {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Successful", "Done"
                    );
                    mainView.loadPage("schedule.html");
                } else {
                    navigator.notification.alert(
                        data.message, alertDismissed, "Unsuccessful", "Done"
                    );
                    return false;
                }
            },
            error: function(jqxhr, textStatus, errorMessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

    $("#btnAddGuest").on("click", function() {
        var retval = false;
        var ans = check_itemsvalidate('#divAddGuestToList input');

        if (ans) {

            var selected = new Array();
            $("#ulInvitedGuestList li").each(function() {
                selected.push($(this).text());
            });

            var guestEmail = $("#txtAddGuest").val();
            var guestLst = selected;
            var IsExist = guestLst.indexOf(guestEmail);

            if (IsExist == "-1") {
                var strAutoCreateTask = "<li class=liRunTimeCreateList  value=" + guestEmail + "  style='display: inline-block; padding: 10px 0px;'><a id=aLinkRunTimeCreateList_" + guestEmail + " class='aLinkRunTimeCreateList'  href='javascript:;'><i class=icon-remove></i></a><i class=icon-circle style='color: #50c1e9;'></i>" + guestEmail + "</li>";
                $("#ulInvitedGuestList").append(strAutoCreateTask);
                $(".aLinkRunTimeCreateList").on("click", function() {
                    if (confirm('Are you sure?')) {
                        $(this).closest('li').remove();
                        return false;
                    } else { return false; }
                });
                $("#txtAddGuest").val('');
                return false;
            } else {
                navigator.notification.alert(
                    "This Email Address Already Exist", alertDismissed, "Unsuccessful", "Done"
                );
                return false;
            }
            return false;

        } else { return false; }
    });

    function sendInvitation(data) {

        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: "post",
            data: data,
            cache: false,
            success: function(data, textstatus, jqxhr) {
                var obj = $.parseJSON(data);

            },
            error: function(jqxhr, textstatus, errormessage) {
                navigator.notification.alert(
                    errorMessage, alertDismissed, "An error occured", "Done"
                );
            }
        })
    }

});

myApp.onPageInit('journals', function(page) {
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();
    CheckUserLogin();
    GetJournals('MethodName=GetJournals');

    function GetJournals(data) {
        $.ajax({
            url: "https://xactbid.pocketofficepro.com/workflowservice.php",
            type: 'POST',
            data: data,
            cache: false,
            success: function(data, textStatus, jqxhr) {
                var data = $.parseJSON(data);
                $('#lstInbox').empty();
                //Inbox
                var items = [];
                $("#divjournals").empty();
                items.push('<table class="table"><tr><th>Job Number</th><th>Subject</th><th>From</th><th>Timestamp</th></tr>');
                if (data.status == '1') {

                    if (data.Post != null) {

                        for (var i = 0; i < data.Post.length; i++) {
                            if (data.Post[i].job_id != "")
                                items.push('<tr style="cursor: pointer;" class=linkToUserJobDetail id="linkToUserJobDetail_' + data.Post[i].job_id + '" title=Journal><td>' + data.Post[i].job_number + '</td><td>' + data.Post[i].subject + '</td><td>' + data.Post[i].lname + ', ' + data.Post[i].fname.charAt(0) + '</td><td>' + data.Post[i].formatDt + '</td></tr>');
                        }
                    }

                } else {
                    items.push("<tr><td colspan='4'>No journals found</td></tr>");
                }
                items.push('</table>');
                $('#divjournals').append(items.join(''));

                $('.linkToUserJobDetail').on("click", function() {
                    var splits_id = this.id.split('_');
                    var job_id = splits_id[1];
                    mainView.loadPage("jobtabs.html?JId=" + job_id);

                    return false;
                });


            },
            error: function(jqxhr, textStatus, errorMessage) {
                GetJournals(data);
            }
        });

    }
});

$$(document).on('pageInit', function(e) {

    var page = e.detail.page;
    var $ = jQuery.noConflict();
    jQuery(".formError").remove();

    $('.masked-phone').inputmask('(999) 999-9999', { placeholder: ' ' });

    $$(page.container).find("script").each(function(el) {
        eval($(this).text());
    });

    $(".swipebox").swipebox();
    //$(".videocontainer").fitVids();

    $("#ContactForm").validate({
        submitHandler: function(form) {
            ajaxContact(form);
            return false;
        }
    });


    $(".posts li").hide();
    size_li = $(".posts li").size();
    x = 3;
    $('.posts li:lt(' + x + ')').show();
    $('#loadMore').on("click", function() {
        x = (x + 1 <= size_li) ? x + 1 : size_li;
        $('.posts li:lt(' + x + ')').show();
        if (x == size_li) {
            $('#loadMore').hide();
            $('#showLess').show();
        }
    });



    $("a.switcher").bind("click", function(e) {
        e.preventDefault();

        var theid = $(this).attr("id");
        var theproducts = $("ul#photoslist");
        var classNames = $(this).attr('class').split(' ');


        if ($(this).hasClass("active")) {
            // if currently clicked button has the active class
            // then we do nothing!
            return false;
        } else {
            // otherwise we are clicking on the inactive button
            // and in the process of switching views!

            if (theid == "view13") {
                $(this).addClass("active");
                $("#view11").removeClass("active");
                $("#view11").children("img").attr("src", "images/switch_11.png");

                $("#view12").removeClass("active");
                $("#view12").children("img").attr("src", "images/switch_12.png");

                var theimg = $(this).children("img");
                theimg.attr("src", "images/switch_13_active.png");

                // remove the list class and change to grid
                theproducts.removeClass("photo_gallery_11");
                theproducts.removeClass("photo_gallery_12");
                theproducts.addClass("photo_gallery_13");

            } else if (theid == "view12") {
                $(this).addClass("active");
                $("#view11").removeClass("active");
                $("#view11").children("img").attr("src", "images/switch_11.png");

                $("#view13").removeClass("active");
                $("#view13").children("img").attr("src", "images/switch_13.png");

                var theimg = $(this).children("img");
                theimg.attr("src", "images/switch_12_active.png");

                // remove the list class and change to grid
                theproducts.removeClass("photo_gallery_11");
                theproducts.removeClass("photo_gallery_13");
                theproducts.addClass("photo_gallery_12");

            } else if (theid == "view11") {
                $("#view12").removeClass("active");
                $("#view12").children("img").attr("src", "images/switch_12.png");

                $("#view13").removeClass("active");
                $("#view13").children("img").attr("src", "images/switch_13.png");

                var theimg = $(this).children("img");
                theimg.attr("src", "images/switch_11_active.png");

                // remove the list class and change to grid
                theproducts.removeClass("photo_gallery_12");
                theproducts.removeClass("photo_gallery_13");
                theproducts.addClass("photo_gallery_11");

            }

        }

    });

    document.addEventListener('touchmove', function(event) {
        if (event.target.parentNode.className.indexOf('navbarpages') != -1 || event.target.className.indexOf('navbarpages') != -1) {
            event.preventDefault();
        }
    }, false);
})

function alertDismissed() {
    // do nothing
}

