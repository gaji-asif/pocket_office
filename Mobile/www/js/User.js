
var $ = jQuery.noConflict();
function check_itemsvalidate(divid, startdate, enddate, message, flag) {
    jQuery(".formError").remove();
    var mystring = '';
    jQuery(divid).each(function () {
        mystring = mystring + jQuery.validationEngine.loadValidation(jQuery(this)) + "|";
        jQuery.validationEngine.loadValidation(jQuery(this));
    });
    var divselect = divid.replace('input', 'select');
    jQuery(divselect).each(function () {
        mystring = mystring + jQuery.validationEngine.loadValidation(jQuery(this)) + "|";
        jQuery.validationEngine.loadValidation(jQuery(this));
    });
    var divtextarea = divid.replace('input', 'textarea');
    jQuery(divtextarea).each(function () {
        mystring = mystring + jQuery.validationEngine.loadValidation(jQuery(this)) + "|";
        jQuery.validationEngine.loadValidation(jQuery(this));
     
    });
    jQuery(".formError").on("click", function () {
        // REMOVE BOX ON CLICK
        jQuery(this).fadeOut(150, function () { jQuery(this).remove() })
    })
    if (mystring.indexOf("true") != "-1") {
        return false;
    }
    else {
        if (startdate != null) {
            if (flag == undefined) {
                flag = "";
            }
            return (CheckDate(startdate, enddate, message, flag))
        }
        else {
            return true;
        }
    }
    return false;
}
function CallJSForMaxLengthAjaxCombobox(divid, message) {
    // var divid = '#<%= ComboBox2.ClientID%> input';

    jQuery(divid).each(function () {
        if (this.type == 'text') {
            this.setAttribute("MaxLength", message);
        }
    });
}

function CallJSForAjaxCombobox(divid, message) {
    // var divid = '#<%= ComboBox2.ClientID%> input';
    jQuery(divid).each(function () {
        if (this.type == 'text') {
            this.setAttribute("class", message);
        }
    });
}

function CallJSForResetAjaxCombobox(divid) {
    // var divid = '#<%= ComboBox2.ClientID%> input';
    jQuery(divid).each(function () {
        if (this.type == 'text') {
            this.value = '';
        }
    });
}

function CallJSForAjaxComboboxSetEvent(divid, eventname, functionname) {
    // var divid = '#<%= ComboBox2.ClientID%> input';
    jQuery(divid).each(function () {
        if (this.type == 'text') {
            this.setAttribute(eventname, functionname.trim());
        }
    });
}

function CallJSForAjaxComboboxGetTextBoxName(divid) {
    // var divid = '#<%= ComboBox2.ClientID%> input';
    var id = '';
    jQuery(divid).each(function () {
        if (this.type == 'text') {
            id = this.id;
        }
    });



    return id;
}

function CallJSForAjaxComboboxSetCssTable(divid) {

    if (divid.indexOf(',') > -1) {
        var arrayOfStrings = divid.split(",");
        for (var k = 0; k < arrayOfStrings.length; k++) {
            var divid1 = arrayOfStrings[k].trim();
            jQuery(divid1).each(function () {
                var table = this;
                for (var i = 0; i < table.rows.length; i++) {
                    for (var j = 0; j < table.rows[i].cells.length; j++) {
                        table.rows[i].cells[j].style.border = 'none';
                    }
                }
            });
        }

    }
    else {
        jQuery(divid).each(function () {
            for (var i = 0; i < this.rows.length; i++) {
                for (var j = 0; j < this.rows[i].cells.length; j++) {
                    this.rows[i].cells[j].style.border = 'none';
                }
            }
        });
    }
}

function RemoveJSValidation() {
    jQuery(".formError").remove();
}

jQuery(document).ready(function () {
    jQuery('.validation').blur(function () {
        if (this.value != '') {
            var mystring = '';
            mystring = mystring + jQuery.validationEngine.loadValidation(jQuery(this)) + "|";
            jQuery.validationEngine.loadValidation(jQuery(this));
            if (mystring.indexOf("true") != "-1") {
                return false;
            } else { return true; }
        }
    });
});

function SelectDropDown(dropdownid, selectedvalue) {
    var el = document.getElementById(dropdownid);
    for (var i = 0; i < el.options.length; i++) {
        if (el.options[i].text.toLowerCase() == selectedvalue.toLowerCase()) {
            el.selectedIndex = i;
            break;
        }
        else {
            el.selectedIndex = 0;
        }
    }
}



function CheckDate(txtcontactdate, txtfollowupdate, message, flag) {

    var value1 = '';
    var Value2 = '';

    if (txtcontactdate != null && txtfollowupdate != null) {
        if (txtcontactdate != '') {
            if (document.getElementById(txtcontactdate) != null) {
                txtcontactdate = document.getElementById(txtcontactdate).value;
            }
            else { txtcontactdate = txtcontactdate.value; }
            value1 = new Date(txtcontactdate);
        }
        if (txtfollowupdate != '') {
            if (document.getElementById(txtfollowupdate) != null) {
                txtfollowupdate = document.getElementById(txtfollowupdate).value;
            }
            else { txtfollowupdate = txtfollowupdate.value; }
            value2 = new Date(txtfollowupdate);

        }

        if (flag == undefined) {
            flag = "";
        }
        if (value1 != null && value2 != null && value1 != '' && value2 != '') {
            if (flag == "less") {
                if (value1 < value2) {
                    navigator.notification.alert(
                        message, alertDismissed,"Successful","Done"             
                    );
                    return false;
                }
                else {
                    return true;
                }
            }
            else {
                if (value1 < value2) {
                    navigator.notification.alert(
                        message, alertDismissed,"Successful","Done"             
                    );
                    return false;
                }
                else {
                    return true;
                }
            }
        }
        else {
            return true;
        }
    }
    else {
        return true;
    }
}


function HideShowAccordionPane1(divid1, divid2) {
    var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
    var IE6 = parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE") + 5)) == 6;
    var IE7 = parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE") + 5)) == 7;
    if (document.getElementById(divid1).className == "divcollapse") {
        if (IE6 || IE7)
            document.getElementById(divid1).setAttribute((document.all ? 'className' : 'class'), 'divvisible');
        else document.getElementById(divid1).setAttribute('class', 'divvisible');

        if (IE6 || IE7)
            document.getElementById(divid2).setAttribute((document.all ? 'className' : 'class'), 'divcollapse');
        else document.getElementById(divid2).setAttribute('class', 'divcollapse');
    }
    else {
        if (IE6 || IE7)
            document.getElementById(divid1).setAttribute((document.all ? 'className' : 'class'), 'divcollapse');
        else document.getElementById(divid1).setAttribute('class', 'divcollapse');
    }
}
function HideShowAccordionPane(divid1, divid2) {
    if (document.getElementById(divid1) != null && document.getElementById(divid2) != null) {
        var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
        var IE6 = parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE") + 5)) == 6;
        var IE7 = parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE") + 5)) == 7;

        if (IE6 || IE7)
            document.getElementById(divid1).setAttribute((document.all ? 'className' : 'class'), 'divvisible');
        else document.getElementById(divid1).setAttribute('class', 'divvisible');

        if (IE6 || IE7)
            document.getElementById(divid2).setAttribute((document.all ? 'className' : 'class'), 'divcollapse');
        else document.getElementById(divid2).setAttribute('class', 'divcollapse');
    }
    else {
        if (IE6 || IE7)
            document.getElementById(divid1).setAttribute((document.all ? 'className' : 'class'), 'divvisible');
        else document.getElementById(divid1).setAttribute('class', 'divvisible');
    }

}

function HideShowAccordionPaneFor3(divid1, divid2, divid3) {
    if (document.getElementById(divid1) != null && document.getElementById(divid2) != null && document.getElementById(divid3) != null) {
        var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
        var IE6 = parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE") + 5)) == 6;
        var IE7 = parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE") + 5)) == 7;

        if (IE6 || IE7)
            document.getElementById(divid1).setAttribute((document.all ? 'className' : 'class'), 'divvisible');
        else document.getElementById(divid1).setAttribute('class', 'divvisible');

        if (IE6 || IE7)
            document.getElementById(divid2).setAttribute((document.all ? 'className' : 'class'), 'divcollapse');
        else document.getElementById(divid2).setAttribute('class', 'divcollapse');
        if (IE6 || IE7)
            document.getElementById(divid3).setAttribute((document.all ? 'className' : 'class'), 'divcollapse');
        else document.getElementById(divid3).setAttribute('class', 'divcollapse');
    }
    else {
        if (IE6 || IE7)
            document.getElementById(divid1).setAttribute((document.all ? 'className' : 'class'), 'divvisible');
        else document.getElementById(divid1).setAttribute('class', 'divvisible');
    }

}


function validateNumbersOnly(e) {
    var unicode = e.charCode ? e.charCode : e.keyCode;
    if ((unicode == 8) || (unicode == 9) || (unicode > 47 && unicode < 58)) {
        return true;
    }
    else {

        window.alert("Enter Numbers Only.");
        return false;
    }
}


$(document).ready(function () {
  
    //// hide #back-top first
    //$("#back-top").hide();

    //// fade in #back-top
    //$(function () {
    //    $(window).scroll(function () {
    //        if ($(this).scrollTop() > 100) {
    //            $('#back-top').fadeIn();
    //        } else {
    //            $('#back-top').fadeOut();
    //        }
    //    });

    //    // scroll body to 0px on click
    //    $('#back-top a').click(function () {
    //        $('body,html').animate({
    //            scrollTop: 0
    //        }, 800);
    //        return false;
    //    });
    //});

});

