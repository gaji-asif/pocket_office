var Functions = {
    //Functions.executeCallback
    //execute callback if valid
    //params: (function)callback, (mixed)argument
    //return:
    executeCallback: function(callback, argument) {
        //validate callback as method
        if(callback && typeof callback == 'function') {
            callback(argument);
        }
    },

    //Functions.filterDataByObjectProperty
    //filter data set by property value
    //params: (object)data, (string)property, (mixed)value
    //return: (object)filtered_object_array
    filterDataByObjectProperty: function(data, property, value) {
        //filtered object array
        var filtered_object_array = {};

        //validate data as object
        if(typeof data == 'object') {
            $.each(data, function(i, object){
                if(object[property] && object[property] == value) {
                    filtered_object_array[i] = object;
                }
            });
        }

        return filtered_object_array;
    },

    //Functions.numberOfKeys
    //get number of bags based on coverage and current square footage
    //params: (float)coverage
    //return: (int)number_of_bags
    numberOfKeys: function(object) {
        var count = 0;
        for(var prop in object) {
            count++;
        }

        return count;
    }
};

//add trim function if in non-modern browsers
if(typeof String.prototype.trim !== 'function') {
    String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g, '');
    }
}