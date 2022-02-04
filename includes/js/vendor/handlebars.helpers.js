Handlebars.registerHelper('ifCond', function(v1, operator, v2, options) {
    switch (operator) {
        case '==':
            return (v1 == v2) ? options.fn(this) : options.inverse(this);
            break;
        case '!=':
            return (v1 != v2) ? options.fn(this) : options.inverse(this);
            break;
        case '===':
            return (v1 === v2) ? options.fn(this) : options.inverse(this);
            break;
        case '!==':
            return (v1 !== v2) ? options.fn(this) : options.inverse(this);
            break;
        case '<':
            return (v1 < v2) ? options.fn(this) : options.inverse(this);
            break;
        case '<=':
            return (v1 <= v2) ? options.fn(this) : options.inverse(this);
            break;
        case '>':
            return (v1 > v2) ? options.fn(this) : options.inverse(this);
            break;
        case '>=':
            return (v1 >= v2) ? options.fn(this) : options.inverse(this);
            break;
        case '+':
            return (v1 + v2);
            break;
        default:
            return options.inverse(this);     
            break;
    }
});

Handlebars.registerHelper('add', function(arg1, arg2) {
    return arg1 + arg2;
});

Handlebars.registerHelper('getArrayValue', function(array, index) {
    return array[index];
});

Handlebars.registerHelper('fromNow', function(timestamp) {
    return moment(timestamp).fromNow();
});

Handlebars.registerHelper('getChatMessageName', function(firstName, userId) {
    if(userId == GLOBALS.ao_userid) {
        return 'Me';
    }
    return firstName;
});

Handlebars.registerHelper('for', function(from, to, incr, block) {
    var accum = '';
    for(var i = from; i < to; i += incr)
        accum += block.fn(i);
    return accum;
});