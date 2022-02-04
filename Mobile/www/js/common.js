 
var $ = jQuery.noConflict();
if (sessionStorage["sessLoginId"] != undefined) {
    loginuserid = sessionStorage["sessLoginId"];

    //  SetLoginUserName(loginuserid);
    //  GetUserListCount(loginuserid)
    $("#alogo").attr("href", "myaccount.html");
    var logoutTag = "<a href='javascript:;' onclick='logoutClick();' style='margin-left:3px;color:yellow' title='Logout'>Logout</a>"
    var followingCount = "<br/><a id='afollowing' href='#' title='' style='color:white'></a>"
    $(".navbar-header").append(logoutTag);
    $(".navbar-header").append(followingCount);
    if (sessionStorage["sessUserName"] != undefined) {
        loginusername = sessionStorage["sessUserName"];
        $('#headerUserName').text('Welcome,  ' + loginusername)
    }
    if (sessionStorage["sessFname"] != undefined) {
        loginfname = sessionStorage["sessFname"];
    }
    if (sessionStorage["sessLname"] != undefined) {
        loginlname = sessionStorage["sessLname"];
    }
    if (sessionStorage["sessLevel"] != undefined) {
        loginlevelid = sessionStorage["sessLevel"];
    }
    if (sessionStorage["sessAccountId"] != undefined) {
        loginaccountid = sessionStorage["sessAccountId"];
    }
    if (sessionStorage["ao_module_access"] != undefined) {
        ao_module_access = sessionStorage["ao_module_access"];
    }
    if (sessionStorage["ao_nav_access"] != undefined) {
        ao_nav_access = sessionStorage["ao_nav_access"];
    }
    if (sessionStorage["ao_founder"] != undefined) {
        ao_founder = sessionStorage["ao_founder"];
    }
    
}
else {
    // $("#alogo").attr("href", "#");
    location.href = "index.html";

}
 
function logoutClick() {
    localStorage.clear();
    sessionStorage.clear();
   // $("#alogo").attr("href", "#");
    location.href = "index.html";
}
function SetLoginUserName(loginuserid) {
    var WhereCondition = "Id=" + loginuserid;
    $.ajax({
        //url: "../../CosmoService.svc/GetCoulmnValueById",
        url: "http://cosmythology.com/CosmoService.svc/GetCoulmnValueById",
        data: JSON.stringify({ columnName: "UserName", tableName: "users", whereCondition: WhereCondition }),
        type: "POST",
        dataType: "json",
        headers: { "Content-Type": "application/json; charset=UTF-8" },
        success: function (data) {
            var obj = JSON.parse(data);
            if (obj != null) {
                if (obj.isSuccess == "1") {
                    loginusername = obj.retId.toString();
                    $("#lblLoginUserName").text("Welcome " + loginusername);
                }
                else {
                    loginusername = "";
                    return false;
                }
            }
            else {
                loginusername = "";
                return false;
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            loginusername = "";
            return false;
        }
    });

}
function GetUserListCount(loginuserid) {
    $.ajax({
        //url: "../../CosmoService.svc/GetFollowingUserCount",
        url: "http://cosmythology.com/CosmoService.svc/GetFollowingUserCount",
        data: JSON.stringify({ UserId: loginuserid }),
        type: "POST",
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        success: function (data) {
            var count = JSON.parse(data).UserCountListCount;
            if (count != null && count != "") {
                $("#afollowing").text("Following " + count + " People");
                $("#afollowing").prop('title', 'Following ' + count + ' People');
                $("#afollowing").attr("href", "UserCountList.html");

            }
            else {
                $("#afollowing").attr("style", "display:none;");
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            navigator.notification.alert(
                textStatus, alertDismissed,"An error occured","Done"             
            );
        }
    });
}
function deserialize(UsersDetails) {

    var UserList = UsersDetails.UserList.map(function (UserList) {
        return new UserList1(UserList.Id);
    });

    var success = UsersDetails.isSuccess;
    //var comments = payload.comments.map(function (comment) {
    //    return new Comment(comment.id, comment.text, comment.postId);
    //});
    return new DataFinal(UserList, success);
}

function UserList1(Id, Question, Name, QuestionDate, CountMale, CountFemale) {
    // custom type checking here...
    this.Id = Id;
    this.Question = Question;
    this.Name = Name;
    this.QuestionDate = QuestionDate;
    this.CountMale = CountMale;
    this.CountFemale = CountFemale;
}
function DataFinal(UserList, Success) {
    // custom type checking here...
    this.UserList = UserList;
    this.Success = Success;
}