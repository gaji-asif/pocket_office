var Data = {
    last: null,
            
    destroy: function(url, payload, success, error) {
        this.go('DELETE', url, payload, success, error);
    },
    
    get: function(url, payload, success, error) {
        this.go('GET', url, payload, success, error);
    },
            
    post: function(url, payload, success, error) {
        this.go('POST', url, payload, success, error);
    },
            
    put: function(url, payload, success, error) {
        this.go('PUT', url, payload, success, error);
    },
    
    go: function(type, url, payload, success, error) {
        var that = this;
        $.ajax({
            data: payload,
            type: type,
            url: url,
            error: function(data) {
               that.callback(error, data);
            },
            success: function(data) {
               that.callback(success, data, true);
            }
        });
    },
    
    callback: function(callback, data, storeLast) {
        //store last result
        if(storeLast) {
            this.last = data;
        }
        
        //validate and call
        if(typeof callback === 'function') {
            callback(data);
        }
    }
};