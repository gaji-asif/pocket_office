(function($) {
    $.fn.validationEngineLanguage = function() {};
    $.validationEngineLanguage = {
        newLang: function() {
            $.validationEngineLanguage.allRules = {
                "required": { // Add your regex rules here, you can take telephone as an example
                    "regex": "none"
                },
                "length": {
                    "regex": "none",
                    "alertText": "*Between ",
                    "alertText2": " and ",
                    "alertText3": " characters allowed"
                },
                "validate_defa": {
                    "regex": "none"
                },
                "maxCheckbox": {
                    "regex": "none",
                    "alertText": "* Checks allowed Exceeded"
                },
                "selectval": {
                    "regex": "none",
                    "alertText": "* Please Select"
                },
                "minCheckbox": {
                    "regex": "none",
                    "alertText": "* Please select ",
                    "alertText2": " options"
                },
                "confirm": {
                    "regex": "none",
                    "alertText": "* Your field is not matching"
                },
                "telephone": {
                    "regex": "/^[0-9\-\(\)\ ]+$/",
                    "alertText": "* Invalid phone number"
                },
                "email": {
                    /*"regex":"/^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/",*/
                    "regex": "/^.+@.+\..{2,3}$/",
                    "alertText": "Invalid email address"
                },
                "date": {
                    "regex": "/^[0-9]{4}\-\[0-9]{1,2}\-\[0-9]{1,2}$/",
                    "alertText": "* Invalid date, must be in YYYY-MM-DD format"
                },
                "ausdate": {
                    "regex": "/^[0-9]{4}\[/]{1}\[0-9]{1,2}\[/]{1}\[{1,0-9]2}$/",
                    "alertText": "* Invalid date, must be in YYYY/MM/DD format"
                },
                "usdate": {
                    "regex": "/^(0[1-9]|1[012])\[/]{1}\(0[1-9]|[12][0-9]|3[01])\[/]{1}\[0-9]{4}$/",
                    /* "regx": "/^(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)\d\d$/",*/
                    "alertText": "Invalid date, Date must be in MM/DD/YYYY format"
                },
                "onlyNumber": {
                    "regex": "/^[0-9\ ]+$/",
                    "alertText": "Enter Numbers only"
                },
                "onlyDecimal": {
                    "regex": "/^[0-9\ ]+$/",
                    "alertText": "* Numbers only"
                },
                "noSpecialCaracters": {
                    "regex": "/^[0-9a-zA-Z]+$/",
                    "alertText": "* No special caracters allowed"
                },
                "ajaxUser": {
                    "file": "form_validate/validateUser.php",
                    "extraData": "name=eric",
                    "alertTextOk": "* This User ID is available",
                    "alertTextLoad": "* Loading, please wait",
                    "alertText": "* This User ID is already taken"
                },
                "ajaxEmail": {
                    "file": "form_validate/validateEmail.php",
                    "extraData": "email=noreply@bob.com.ay",
                    "alertTextOk": "* This E-mail address is available",
                    "alertTextLoad": "* Loading, please wait",
                    "alertText": "* This email is already in use"
                },
                "ajaxGuestEmail": {
                    "file": "form_validate/validateGuestEmail.php",
                    "extraData": "email=noreply@bob.com.ay",
                    "alertTextOk": "* This E-mail address is available",
                    "alertTextLoad": "* Loading, please wait",
                    "alertText": "* This email is already in use"
                },
                "ajaxName": {
                    "file": "validateUser.php",
                    "alertText": "* This User ID is already taken",
                    "alertTextOk": "* This User ID is available",
                    "alertTextLoad": "* Loading, please wait"
                },
                "onlyLetter": {
                    "regex": "/^[a-zA-Z\ \']+$/",
                    "alertText": "* Letters only"
                },

                "validate2fields": {
                    "nname": "validate2fields",
                    "alertText": ""
                },
                "validateConfEmail": {
                    "nname": "validateConfEmail"
                },
                "validatetext": {
                    "nname": "validatetext"
                },
                "validateUsPhoneOrFax": {
                    "nname": "validateUsPhoneOrFax"
                },
                "validateCustomEmailAddress": {
                    "nname": "validateCustomEmailAddress"
                },

                "validateVideoURL": {
                    "nname": "validateVideoURL"
                },

                "OnlyImage": {
                    "nname": "OnlyImage"
                },
                "OnlyPDF": {
                    "nname": "OnlyPDF"
                },
                "validatemastercardno": {
                    "nname": "validatemastercardno"
                },
                "validatevisacardno": {
                    "nname": "validatevisacardno"
                },
                "validateamexcardno": {
                    "nname": "validateamexcardno"
                },
                "validateEmail1": {
                    "nname": "validateEmail1"
                },
                "validatePassword": {
                    "nname": "validatePassword"
                },
                "validatePasswordteacher": {
                    "nname": "validatePasswordteacher"
                },

                "validatePasswordstaff": {
                    "nname": "validatePasswordstaff"
                },
                "validatePasswordparent": {
                    "nname": "validatePasswordparent"
                },
                "validateEmailVerifyTeacher": {
                    "nname": "validateEmailVerifyTeacher"
                },
                "validateTextArea": {
                    "nname": "validateTextArea"
                },
                "RequiredStaffImage": {
                    "nname": "RequiredStaffImage"
                },
                "validateHighweek": {
                    "nname": "validateHighweek"
                },
                "chk_trn_val": {
                    "nname": "chk_trn_val"
                },
                "chk_alpha_num": {
                    "nname": "chk_alpha_num"
                },
                "chk_site_url": {
                    "nname": "chk_site_url"
                },
                "chk_phone_mobile": {
                    "nname": "chk_phone_mobile"
                },
                "chk_phone_blank": {
                    "nname": "chk_phone_blank"
                },
                "chk_mobile": {
                    "nname": "chk_mobile"
                },
                "chk_mobile1": {
                    "nname": "chk_mobile1"
                },
                "validateEmail": {
                    "nname": "validateEmail"
                },
                "Onlydigits": {
                    "nname": "Onlydigits"
                },
                "validateregPasswordteacher": {
                    "nname": "validateregPasswordteacher"
                },
                "customRecValidate": {
                    "nname": "customRecValidate"
                },
                "GiftAmountRest": {
                    "nname": "GiftAmountRest"
                },
                "Onlydigitsex0": {
                    "nname": "Onlydigitsex0"
                },
                "MinusIntValueOnly": {
                    "nname": "MinusIntValueOnly"
                },
                "MinusIntValueWithGraterZeroOnly": {
                    "nname": "MinusIntValueWithGraterZeroOnly"
                },
                "MinusIntValueWithAmount8Digit": {
                    "nname": "MinusIntValueWithAmount8Digit"
                },
                "MinusIntValueWithAmount6digit": {
                    "nname": "MinusIntValueWithAmount6digit"
                },
                "MinusDecimalValueOnly": {
                    "nname": "MinusDecimalValueOnly"
                },
                "GreaterValue0": {
                    "nname": "GreaterValue0"
                },
                "UsDateFormat": {
                    "nname": "UsDateFormat"
                },
                "FullDateFormat": {
                    "nname": "FullDateFormat"
                },
                "MMddYYDateFormat": {
                    "nname": "MMddYYDateFormat"
                },
                "YYMMddDateFormat": {
                    "nname": "YYMMddDateFormat"
                },
                "Amount5digit": {
                    "nname": "Amount5digit"
                },
                "Amount1digit": {
                    "nname": "Amount1digit"
                },

                "Amount2digit": {
                    "nname": "Amount2digit"
                },
                "Amount3digit": {
                    "nname": "Amount3digit"
                },
                "Amount10digit": {
                    "nname": "Amount10digit"
                },
                "Amount6digit": {
                    "nname": "Amount6digit"
                },
                "Amount7digit": {
                    "nname": "Amount7digit"
                },
                "Amount4digit": {
                    "nname": "Amount4digit"
                },
                "Amount8digit": {
                    "nname": "Amount8digit"
                },
                "Amount12digit": {
                    "nname": "Amount12digit"
                },
                "Amount74digit": {
                    "nname": "Amount74digit"
                },
                "ValidYear": {
                    "nname": "ValidYear"
                },
                "validMultiLine": {
                    "nname": "validMultiLine"
                },
                "validateZipCode": {
                    "nname": "validateZipCode"
                },
                "validatePhoneORFaxNumber": {
                    "nname": "validatePhoneORFaxNumber"
                },
                "GreaterValue0wtihDecimalPoint": {
                    "nname": "GreaterValue0wtihDecimalPoint"
                },
                "validatePhoneORFaxNumberWithFormat": {
                    "nname": "validatePhoneORFaxNumberWithFormat"
                },


                "defaultval": {
                    "nname": "chkdefault",
                    "alertText": ""
                }

            }

        }
    }
})(jQuery);

var $jq1 = jQuery.noConflict();
$jq1(document).ready(function() {
    //alert($jq1.validationEngineLanguage);

    $jq1.validationEngineLanguage.newLang();
    //mycheck();
});

function validMultiLine(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        return false;
    } else {
        return true;
    }
}

function Onlydigitsex0(caller) {
    var str = $jq1(caller).val();

    if (str != '' && str.length == 5) {
        //var filter = /^[1-9][0-9]*$/
        var filter = /^[0-9]*$/
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function MinusIntValueOnly(caller) {


    var str = $jq1(caller).val();
    if (str != '') {
        //var filter = /^[1-9][0-9]*$/
        var filter = /^[-]?[0-9]*$/
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function MinusIntValueWithGraterZeroOnly(caller) {


    var str = $jq1(caller).val();
    if (str != '') {
        //var filter = /^[1-9][0-9]*$/
        var filter = /^[-]?[1-9][0-9]*$/
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function MinusDecimalValueOnly(caller) {


    var str = $jq1(caller).val();
    if (str != '') {
        //var filter = /^[1-9][0-9]*$/
        var filter = /^[-]?[0-9]+(?:\.[0-9]+)?$/
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function MinusIntValueWithAmount8Digit(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        //  var filter = /^(\d{0,8})?([\.]{1})?(\d{0,2})$/
        var filter = /^[-]?\d{0,9}(\.\d{0,2})?$/;
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function MinusIntValueWithAmount6digit(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        //var filter = /^\d{0,6}(\.\d{0,2})?$/;
        var filter = /^[-]?\d{0,7}(\.\d{0,2})?$/;
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function GreaterValue0(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        var filter = /^[1-9][0-9]*$/
            //var filter = /^[0-9]*$/
        if (filter.test(str)) { return false; } else { return true; }
    }



}

function MMddYYDateFormat(caller) {
    var str = $jq1(caller).val();
    if (str != '') {

        var filter = new RegExp(/^((0?[1-9]|1[012])[- /.](0?[1-9]|[12][0-9]|3[01])[- /.](19|20)?[0-9]{2})*$/);
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function YYMMddDateFormat(caller) {
    var str = $jq1(caller).val();
    if (str != '') {

        var filter = new RegExp(/^\d{4}-\d{2}-\d{2}$/);
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function UsDateFormat(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        // "regex": "/^(0[1-9]|1[012])\[/]{1}\(0[1-9]|[12][0-9]|3[01])\[/]{1}\[0-9]{4}$/",
        //var filter = new RegExp(/\b\d{1,2}[\/-]\d{1,2}[\/-]\d{4}\b/);

        //var filter = new RegExp(/^(0[1-9]|1[012])\[/]{1}\(0[1-9]|[12][0-9]|3[01])\[/]{1}\[0-9]{4}$/);
        //Current Working For MM-DD-yyy Formate
        //var filter= new RegExp(/^(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)\d\d+$/);
        // curr var filter = new RegExp(/\b\d{1,2}[\/-]\d{1,2}[\/-]\d{4}\b/);
        /////////////////1	  var filter= new RegExp(/^((0?[1-9]|1[012])[- /.](0?[1-9]|[12][0-9]|3[01])[- /.](19|20)?[0-9]{4})*$/);
        ////////2 var filter = new RegExp(/^[0,1]?\d{1}\/(([0-2]?\d{1})|([3][0,1]{1}))\/(([1]{1}[9]{1}[9]{1}\d{1})|([2-9]{1}\d{3}))$/);
        var filter = new RegExp(/(((0[13578]|10|12)([-./])(0[1-9]|[12][0-9]|3[01])([-./])(\d{4}))|((0[469]|11)([-./])([0][1-9]|[12][0-9]|30)([-./])(\d{4}))|((2)([-./])(0[1-9]|1[0-9]|2[0-8])([-./])(\d{4}))|((2)(\.|-|\/)(29)([-./])([02468][048]00))|((2)([-./])(29)([-./])([13579][26]00))|((2)([-./])(29)([-./])([0-9][0-9][0][48]))|((2)([-./])(29)([-./])([0-9][0-9][2468][048]))|((2)([-./])(29)([-./])([0-9][0-9][13579][26])))/);

        //Original
        // var filter = new RegExp(/\b\d{1,2}[\/-]\d{1,2}[\/-]\d{4}\b/);

        // var filter = new RegExp(/^(0[1-9]|1[012])\[/]{1}\(0[1-9]|[12][0-9]|3[01])\[/]{1}\[0-9]{4}$/);
        //var filter = /^[0-9]*$/

        //var filter = new RegExp(/^(0[1-9]|1[012])\[/]{1}\(0[1-9]|[12][0-9]|3[01])\[/]{1}\[0-9]{4}$/);
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function FullDateFormat(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        // "regex": "/^(0[1-9]|1[012])\[/]{1}\(0[1-9]|[12][0-9]|3[01])\[/]{1}\[0-9]{4}$/",
        //var filter = new RegExp(/\b\d{1,2}[\/-]\d{1,2}[\/-]\d{4}\b/);

        //var filter = new RegExp(/^(0[1-9]|1[012])\[/]{1}\(0[1-9]|[12][0-9]|3[01])\[/]{1}\[0-9]{4}$/);
        //Current Working For MM-DD-yyy Formate 0?[1-9]|[12]\d|3[01]
        // var filter= new RegExp(/(Sun|Mon|Tue|Wed|Thu|Fri|Sat|Sun), (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) [1-3]{1}[1-9]{0,1},\d{4}/);
        var filter = new RegExp(/(Sun|Mon|Tue|Wed|Thu|Fri|Sat|Sun), (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) ([1-9]|[12][0-9]|3[01]),\d{4}/);

        //	   var filter= new RegExp(/^(0?[1-9]|[12][0-9]|3[01])[ \/.-](0?[1-9]|1[012])[ \/.-](19|20)\d\d$/);

        //Original
        // var filter = new RegExp(/\b\d{1,2}[\/-]\d{1,2}[\/-]\d{4}\b/);

        // var filter = new RegExp(/^(0[1-9]|1[012])\[/]{1}\(0[1-9]|[12][0-9]|3[01])\[/]{1}\[0-9]{4}$/);
        //var filter = /^[0-9]*$/

        //var filter = new RegExp(/^(0[1-9]|1[012])\[/]{1}\(0[1-9]|[12][0-9]|3[01])\[/]{1}\[0-9]{4}$/);
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function Amount5digit(caller) {
    var str = $jq1(caller).val();

    if (str != '') {
        // var filter = /^(\d{0,5})?([\.]{1})?(\d{0,2})$/
        var filter = /^\d{0,5}(\.\d{0,2})?$/;
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function ValidYear(caller) {
    var str = $jq1(caller).val();
    if (str < 1900) { return true; } else { return false; }
}

function Amount3digit(caller) {
    var str = $jq1(caller).val();

    if (str != '') {
        // var filter = /^(\d{0,3})?([\.]{1})?(\d{0,2})$/
        var filter = /^\d{0,3}(\.\d{0,2})?$/;
        if (filter.test(str)) { return false; } else { return true; }
    }



}

function Amount10digit(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        //var filter = /^(\d{0,10}\.)?\d{1,10}$/
        //        var filter = /^(\d{0,10})?([\.]{1})?(\d{0,2})$/
        // var filter = /^(\d{0,10})?([\.]{1})?(\d{0,2})$/
        var filter = /^\d{0,10}(\.\d{0,2})?$/;
        //var filter=/^\d{1,8}$|(?=^.{1,10}$)^\d+\.\d{1,2}$/
        //^\d{1,17}$|^\d{1,13}(?=.{1,5}$)\d*\.\d*\d$
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function Amount12digit(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        //var filter = /^(\d{0,10}\.)?\d{1,10}$/
        //        var filter = /^(\d{0,10})?([\.]{1})?(\d{0,2})$/
        // var filter = /^(\d{0,10})?([\.]{1})?(\d{0,2})$/
        var filter = /^\d{0,12}(\.\d{0,2})?$/;
        //var filter=/^\d{1,8}$|(?=^.{1,10}$)^\d+\.\d{1,2}$/
        //^\d{1,17}$|^\d{1,13}(?=.{1,5}$)\d*\.\d*\d$
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function Amount74digit(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        //var filter = /^(\d{0,10}\.)?\d{1,10}$/
        //        var filter = /^(\d{0,10})?([\.]{1})?(\d{0,2})$/
        // var filter = /^(\d{0,10})?([\.]{1})?(\d{0,2})$/
        var filter = /^\d{0,3}(\.\d{0,4})?$/;
        //var filter=/^\d{1,8}$|(?=^.{1,10}$)^\d+\.\d{1,2}$/
        //^\d{1,17}$|^\d{1,13}(?=.{1,5}$)\d*\.\d*\d$
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function Amount1digit(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        // var filter = /^(\d{0,2})?([\.]{1})?(\d{0,2})$/
        var filter = /^\d{0,1}(\.\d{0,1})?$/;
        if (filter.test(str)) { return false; } else { return true; }
    }

}

function Amount2digit(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        // var filter = /^(\d{0,2})?([\.]{1})?(\d{0,2})$/
        var filter = /^\d{0,2}(\.\d{0,2})?$/;
        if (filter.test(str)) { return false; } else { return true; }
    }

}

function Amount6digit(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        //  var filter = /^(\d{0,6})?([\.]{1})?(\d{0,2})$/
        var filter = /^\d{0,6}(\.\d{0,2})?$/;
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function Amount7digit(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        // var filter = /^(\d{0,7})?([\.]{1})?(\d{0,2})$/
        var filter = /^\d{0,7}(\.\d{0,2})?$/;
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function Amount4digit(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        // var filter = /^(\d{0,7})?([\.]{1})?(\d{0,2})$/
        var filter = /^\d{0,4}(\.\d{0,2})?$/;
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function Amount8digit(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        //  var filter = /^(\d{0,8})?([\.]{1})?(\d{0,2})$/
        var filter = /^\d{0,9}(\.\d{0,2})?$/;
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function customRecValidate(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        // var filter = /^[0-3]\+$/
        var filter = /^[0-3]{0,1}$/;
        if (filter.test(str)) { return false; } else {
            return true;
        }
    }
}

function GiftAmountRest(caller) {
    var str = $jq1(caller).val();

    if (str != '') {
        var filter = /^[0-9]\d{0,9}(\.\d{1,2})?%?$/
        if (filter.test(str)) { return false; } else { return true; }
    }



}

function validate2fields(caller) {
    if ($jq1("#first_name").val() == "" || $jq1("#last_name").val() == "") {
        return true;
    } else {
        return false;
    }
}

function validateConfEmail(caller) {
    if ($jq1("#email").val() != $jq1("#confirm_email_address").val()) {
        return true;
    } else {
        return false;
    }
}

function validatePassword(caller) {
    if ($jq1(".newpwd").val() != $jq1(".cnfpwd").val()) {
        return true;
    } else {

        return false;
    }
}

function validatePasswordteacher(caller) {
    if ($jq1("#ctl00_ContentPlaceHolder1_txtnasnewpassword").val() != $jq1("#ctl00_ContentPlaceHolder1_txtnasconfirmpassword").val()) {
        return true;
    } else {
        return false;
    }
}

function validateregPasswordteacher(caller) {
    if ($jq1("#ctl00_ContentPlaceHolder1_txtnasnewpassword1").val() != $jq1("#ctl00_ContentPlaceHolder1_txtnasConfirmpassword1").val()) {
        return true;
    } else {
        return false;
    }
}

function validatePasswordstaff(caller) {
    if ($jq1("#ctl00_ContentPlaceHolder1_txtstaffNewpassword").val() != $jq1("#ctl00_ContentPlaceHolder1_txtstaffConfirmpassword").val()) {
        return true;
    } else {
        return false;
    }
}

function validatePasswordparent(caller) {
    if ($jq1("#ctl00_ContentPlaceHolder1_txtpPassword").val() != $jq1("#ctl00_ContentPlaceHolder1_txtpConfirmPassword").val()) {
        return true;
    } else {
        return false;
    }
}

function validateTextArea(caller) {

    if ($.trim($jq1(caller).val()) != "") {
        return false;
    } else { return true; }
}

function validateEmailVerifyTeacher(caller) {
    if ($jq1("#ctl00_ContentPlaceHolder1_txtnasEmail").val() != $jq1("#ctl00_ContentPlaceHolder1_txtconfirmEmail").val()) {
        return true;
    } else {
        return false;
    }
}

function validateCustomEmailAddress(caller) {
    var str = $jq1(caller).val();
    /* var filter=/^[a-zA-Z0-9]+[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,3}$/*/
    if (str != '') {
        var filter = /^.+@.+\..{2,3}$/
        if (filter.test(str)) { return false; } else {
            return true;
        }
    }
}


function validateVideoURL(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        var filter = /((http|https):)\/\/(player.|www.)?(vimeo\.com|youtu(be\.com|\.be))\/(video\/|embed\/|watch\?v=)?([A-Za-z0-9._%-]*)(\&\S+)?/
        if (filter.test(str)) { return false; } else {
            return true;
        }
    }
}

function validatetext(caller) {
    var str = $jq1(caller).val();
    /* var filter=/^[a-zA-Z0-9]+[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,3}$/*/
    if (str != '') {
        var filter = /^([^\\<\\>]*)$/
        if (filter.test(str)) { return false; } else {
            return true;
        }
    }
}

function validateUsPhoneOrFax(caller) {
    var str = $jq1(caller).val();
    if (str == '(___) ___-____') {
        str = '';
    }
    /* var filter=/^[a-zA-Z0-9]+[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,3}$/*/
    if (str.trim() != '') {
        var filter = /^\([0-9]{3}\) [0-9]{3}-[0-9]{4}$/
            // var filter = /^([^\\<\\>]*)$/
        if (filter.test(str)) { return false; } else {
            return true;
        }
    }
}

function validatemastercardno(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        var filter = /^5[1-5][0-9]{14}$/
        if (filter.test(str)) { return false; } else {
            return true;
        }
    }
}

function validatevisacardno(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        var filter = /^4[0-9]{12}(?:[0-9]{3})?$/
        if (filter.test(str)) { return false; } else {
            return true;
        }
    }
}

function validateamexcardno(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        var filter = /^3[47][0-9]{13}$/
        if (filter.test(str)) { return false; } else {
            return true;
        }
    }
}

function validateEmail(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,3}$/

        if (filter.test(str)) { return false; } else {
            return true;
        }
    }
}

function validateEmail1(caller) {
    var str = $jq1(caller).val();
    var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,3}$/
    if (filter.test(str)) { return false; } else {
        return true;
    }
}

function chk_trn_val(caller) {
    var str = $jq1(caller).val();
    var filter = /^\s*(\+|-)?((\d+(\.\d\d)?)|(\.\d\d))\s*$/;
    if (filter.test(str)) { return false; } else {
        return true;
    }
}

function chk_alpha_num(caller) {
    var str = $jq1(caller).val();
    var filter = /^([a-zA-Z0-9]+)$/;
    if (filter.test(str)) {
        var filter1 = /^([a-zA-z]+)$/
        if (filter1.test(str)) {
            return true
        } else {
            var filter2 = /^([a-zA-Z0-9_-]+)$/;
            if (filter2.test(str)) {
                return false;
            } else {
                return true;
            }
        }
    } else {
        return true
    }
    /*
    var ValidChars = "0123456789.";
    var IsNumber=true;
    var Char;


    for (i = 0; i < sText.length && IsNumber == true; i++)
    {
    Char = sText.charAt(i);
    if (ValidChars.indexOf(Char) == -1)
    {
    IsNumber = false;
    }
    }
    if(IsNumber == true) {
    return false;
    }else{
    return true;
    }*/
}

function chk_site_url(caller) {
    var str = $jq1(caller).val();
    var filter = /^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/;

    if (filter.test(str) || str == "") {
        return false;
    } else {
        return true;
    }
}

function validateHighweek(caller) {
    //alert(document.getElementById('bdtl_trn_mnt_hg_week').value + ' <=>' + document.getElementById('bdtl_trn_mnt_lw_week').value);
    if (document.getElementById('bdtl_trn_mnt_hg_week').value != '' && document.getElementById('bdtl_trn_mnt_lw_week').value != '') {
        if (document.getElementById('bdtl_trn_mnt_hg_week').value == document.getElementById('bdtl_trn_mnt_lw_week').value) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function chk_phone_mobile(caller) {

    if (document.getElementById('mobile_number').value == '') {
        if (document.getElementById('contact_phone_number').value == '' && document.getElementById('prefix_no').value == '') {
            $jq1.validationEngine.buildPrompt('#contact_phone_number', '* Enter Phone no.', 'error');
            return true;
        } else if (document.getElementById('contact_phone_number').value != '' && document.getElementById('prefix_no').value == '') {
            $jq1.validationEngine.buildPrompt('#prefix_no', '* Enter Prefix no.', 'error');
            return true;
        } else if (document.getElementById('prefix_no').value == '') {
            $jq1.validationEngine.buildPrompt('#prefix_no', '* Enter Prefix no.', 'error');
            return true;
        }
        return false;
    }
}

function chk_mobile(caller) {
    if (document.getElementById('mobile_number').value == '') {
        //|| document.getElementById('prefix_no').value == ''
        if (document.getElementById('contact_phone_number').value == '') {
            $jq1.validationEngine.buildPrompt('#mobile_number', '* Enter Mobile no.', 'error');
            return true;
        }
    }
}

function chk_mobile1(caller) {
    if (document.getElementById('mobile_number').value != '') {
        str = mobile_num = document.getElementById('mobile_number').value;
        mobile_num_length = document.getElementById('mobile_number').value.length;

        var filter = /^[0-9]*$/
        if (filter.test(str)) { flag = false; } else { flag = true }

        if (mobile_num_length != 10 || flag == true) {
            $jq1.validationEngine.buildPrompt('#mobile_number', '* Mobile no. must be 10 digits', 'error');
            return true;
        }
    }
}

function chk_phone_blank(caller) {

    if (document.getElementById('prefix_no').value == '') {
        $jq1.validationEngine.buildPrompt('#prefix_no', '* Enter Prefix no.', 'error');
        return true;
    } else {
        return false;
    }
    /*if(document.getElementById('contact_phone_number').value != '' && document.getElementById('prefix_no').value == ''){
    $jq1.validationEngine.buildPrompt('#prefix_no','* Enter Prefix no.','error');
    return true;
    }else if(document.getElementById('contact_phone_number').value == '' && document.getElementById('prefix_no').value != ''){
    $jq1.validationEngine.buildPrompt('#contact_phone_number','* Enter Phone no.','error');
    return true;
    }else{
    return false;
    }*/
}

function Onlydigits(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        var filter = /^[0-9]*$/
        if (filter.test(str)) { return false; } else { return true }
    }
}


function OnlyImage(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        var filter = /^.+(.jpg|.JPG|.gif|.GIF|.png|.PNG|.jpeg|.JPEG|.bmp|.BMP)$/
        if (filter.test(str)) { return false; } else { return true }
    }
}

function RequiredStaffImage(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        return false;
    } else {
        return true;
    }
}

function OnlyPDF(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        var filter = /^.+(.pdf|.PDF)$/
        if (filter.test(str)) { return false; } else { return true }
    }
}

function validateZipCode(caller) {
    var str = $jq1(caller).val();
    if (str != '') {

        var filter = new RegExp(/(^\d{5}$)|(^\d{5}-\d{4}$)/);
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function validatePhoneORFaxNumber(caller) {
    var str = $jq1(caller).val();
    if (str != '') {

        var filter = new RegExp(/(^\d{10}$)|(^\d{10}-\d{9}$)/);
        if (filter.test(str)) { return false; } else { return true; }
    }
}

function GreaterValue0wtihDecimalPoint(caller) {
    var str = $jq1(caller).val();
    if (str != '') {
        //var filter = /^[1-9][0-9]*$/
        var filter = /^\s*(?=.*[1-9])\d*(?:\.\d{1,2})?\s*$/;
        //var filter = /^[0-9]*$/
        if (filter.test(str)) { return false; } else { return true; }
    }
}


function validatePhoneORFaxNumberWithFormat(caller) {
    var str = $jq1(caller).val();
    if (str != '') {

        //var filter = new RegExp(/(^((\(\d{3}\) ?)|(\d{3}-))?\d{3}-\d{4}$)/);
        var filter = new RegExp(/(^((\(\d{3}\) ?))?\d{3}-\d{4}$)/);
        if (filter.test(str)) { return false; } else { return true; }
    }
}
//      function checkDate(date) {
//        	var str = $jq1(caller).val();
//
//            var dd = new Date(str).getDate();
//            var month = new Date(str).getMonth() + 1;
//            var yr = new Date(str).getFullYear();
//			var isLeap = false;

//
//            alert(dd);
//            alert(month);
//            alert(yr);

//			    if (yr % 400 == 0 || (yr % 100 != 0 && yr % 4 == 0)) {
//                    if(dd > 28 && month == 2)
//                    {
//                        alert("in 2");
//				        isLeap = false;
//			        }
//                    else {
//                        alert("in 3");
//				        isLeap = true;
//			        }
//                return isLeap;
//            }
//		}
