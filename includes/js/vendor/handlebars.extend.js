//Handlebars.getTemplate
//get template from file and load into template array if not previously loaded
//params: (string)name
//return: (object)template
Handlebars.getTemplate = function(name) {
    if (Handlebars.templates === undefined || Handlebars.templates[name] === undefined) {
        $.ajax({
            url : '/workflow/includes/js/templates/' + name + '.html',
            success : function(data) {
                if (Handlebars.templates === undefined) {
                    Handlebars.templates = {};
                }
                Handlebars.templates[name] = Handlebars.compile(data);
            },
            async : false,
            cache: false
        });
    }
    return Handlebars.templates[name];
};

Handlebars.renderTemplate = function(name, data) {
    var template =  Handlebars.getTemplate(name);
    return template(data);
}