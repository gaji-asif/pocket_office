var DATA = {
    CACHE: {},

    //DATA.ajax
    //ajax data request
    //params: (string)url, (function)success, (function)error, (function)complete
    //return:
    ajax: function(url, success, error, complete) {
        console.log('DATA.ajax: ' + url);

        $.ajax
        ({
            url: url,
            dataType: CONFIG.DATA.global_data_type,
            success: function(data){
                console.log('DATA.ajax success: ' + url);
                FUNCTIONS.executeCallback(success, data);
            },
            error: function(data){
                console.log('DATA.ajax error: ' + url);
                FUNCTIONS.executeCallback(error, data);
            },
            complete: function(data){
                FUNCTIONS.executeCallback(complete, data);
            }
        });
    },

    //DATA.post
    //post data request
    //params: (string)url, (object)post_data, (function)success, (function)error, (function)complete
    //return:
    post: function(url, post_data, success, error, complete) {
        console.log('DATA.post: ' + url);

        $.ajax
        ({
            type: 'POST',
            url: url,
            data: post_data,
            dataType: CONFIG.DATA.global_data_type,
            success: function(data){
                console.log('DATA.post success: ' + url);
                FUNCTIONS.executeCallback(success, data);
            },
            error: function(data){
                console.log('DATA.post error: ' + url);
                FUNCTIONS.executeCallback(error, data);
            },
            complete: function(data){
                FUNCTIONS.executeCallback(complete, data);
            }
        });
    },

    //DATA.storeDataInCache
    //store data in cache object
    //params: (string)location, (mixed)data
    //return:
    storeDataInCache: function(location, data) {
        console.log('DATA.storeDataInCache: ' + location + ' => ' + data);

        DATA.CACHE[location] = data;
    },

    //DATA.getStoredData
    //get stored data from cache
    //params: (string)location
    //return:
    getStoredData: function(location) {
        console.log('DATA.getStoredData: ' + location);

        return DATA.CACHE[location];
    }
};