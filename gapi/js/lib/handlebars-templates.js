//Handlebars.getTemplate
//get template from file and load into template array if not previously loaded
//params: (string)name
//return: (object)template
Handlebars.getTemplate = function(name) {
	if (Handlebars.templates === undefined || Handlebars.templates[name] === undefined) {
		$.ajax({
			url : CONFIG.PATHS.templates + '/' + name + '.html',
			success : function(data) {
				if (Handlebars.templates === undefined) {
					Handlebars.templates = {};
				}
				Handlebars.templates[name] = Handlebars.compile(data);
			},
			async : false
		});
	}
	return Handlebars.templates[name];
};