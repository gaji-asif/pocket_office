var Request = {
//    MakeRequest: function(url, targetElementId, loadingIndication, loadResponse, callback) {
    make: function(url, targetElementId, loadingIndication, loadResponse, callback) {
        var myDiv = $('#' + targetElementId);

        if(loadingIndication) {
            this.addLoaderToElement(myDiv);
        }

        $.ajax({
            url: addHeightToUrl(url),
            success: function(data) {
                if(loadResponse) {
                    myDiv.html(data);
                }
                Bindings.setupUI();
            },
            error: function() {
                this.removeLoaderFromElement(targetElementId);
                Bindings.setupUI();
            },
            complete: function(data) {
                if(typeof callback == 'function') {
                    callback();
                }
                Bindings.setupUI();
            }
        });
    },
    
//    MakeParentRequest: function(url, div, loadingIndicator, loadResponse) {
    makeParent: function(url, div, loadingIndicator, loadResponse) {
        this.make(url, div, loadingIndicator, loadResponse);
    },

//    MakeModalRequest: function(url, targetElementId, loadingIndicator, loadResponse, deleteOverlay, callback) {
    makeModal: function(url, targetElementId, loadingIndicator, loadResponse, deleteOverlay, callback) {
        var targetElement = $('#' + targetElementId, window.parent.document);

        if(loadingIndicator){
            this.addLoaderToElement(targetElement);
        }

        $.ajax({
            url: addHeightToUrl(url),
            success: function(data) {
                if(loadResponse) {
                    targetElement.html(data);
                }
            },
            error: function() {
                this.removeLoaderFromElement(div);
    //			createNotification('Oops, Something went wrong!', 'error');
            },
            complete: function(data) {
                if(typeof callback === 'function') {
                    callback();
                }

                parent.Bindings.setupUI();

                if(deleteOverlay) {
                    parent.deleteOverlay();
                }
            }
        });
    },
    
    addLoaderToElement: function(element) {
        if(element.is('tbody')) {
            var colSpan = element.closest('table').find('th').length;
            element.html('<tr><td colspan="' + colSpan + '"><div class="ajax-loader-spinner"></div></td></tr>');
            return;
        }
        element.html('<div class="ajax-loader-spinner"></div>');
    },
            
    removeLoaderFromElement: function(elementId) {
        $('#' + elementId).children('.ajax-loader-spinner-dynamic').remove();
    }
};