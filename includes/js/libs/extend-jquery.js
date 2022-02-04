(function ($) {
    jQuery.fn.jsonSerialize = function() {
        var obj = {};
        var form = $(this[0]);

        if (form.prop('tagName') !== 'FORM') {
            return '';
        }

        form.find('input, select, textarea').each(function () {
            obj[this.name] = $(this).val();
        });

        return JSON.stringify(obj);
    };
    
    //selecting text in an element
    jQuery.fn.selText = function() {
        var obj = this[0];
        if ($.browser.msie) {
            var range = obj.offsetParent.createTextRange();
            range.moveToElementText(obj);
            range.select();
        } else if ($.browser.mozilla || $.browser.opera) {
            var selection = obj.ownerDocument.defaultView.getSelection();
            var range = obj.ownerDocument.createRange();
            range.selectNodeContents(obj);
            selection.removeAllRanges();
            selection.addRange(range);
        } else if ($.browser.safari) {
            var selection = obj.ownerDocument.defaultView.getSelection();
            selection.setBaseAndExtent(obj, 0, obj, 1);
        }
        return this;
    };
    
}(jQuery));