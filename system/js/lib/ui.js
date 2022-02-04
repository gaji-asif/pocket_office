UI = {

	//UI.refactorLayout
    //
    //params:
    //return:
	refactorLayout: function(){
        console.log('UI.refactorLayout');
	},

	//UI.fetchAndLoadAjaxContent
    //fetch ajax content and load into given element with id target
    //params: (string)url, (string)target
    //return:
	fetchAndLoadAjaxContent: function(url, target, spinner){
        console.log('UI.fetchAndLoadAjaxContent: ' + target);

		//apply spinner
		if(spinner !== false)
		{
			UI.showSpinner(target);
		}

		//get content
		DATA.ajax(url, function(data){
			UI.loadAjaxContent(data, target);
		});
	},

	//UI.loadAjaxContent
    //load ajax html data into given element with id target
    //params: (mixed)data, (string)target
    //return:
	loadAjaxContent: function(data, target){
        console.log('UI.loadAjaxContent: ' + target);

		//hide ajax loader
		UI.hideSpinner();

		//load data into element
		$('#' + target).html(data);

		//select init
		$('select').selectpicker();
	},

	//UI.fetchJsonAndBuildTemplate
    //
    //params:
    //return:
	fetchJsonAndBuildTemplate: function(url, target, template, options){
        console.log('UI.buildTemplateFromJson: ' + target);

		//show spinner
		if(options.show_spinner && options.show_spinner == true)
		{
			UI.showSpinner(target);
		}

		//get post vars
		var post_vars = {};
		if(options.post_vars)
		{
			post_vars = options.post_vars;
		}

		//get content
		DATA.post(url, post_vars, function(data) {
			UI.buildTemplate(data, target, template);
		});
	},

	//UI.buildTemplate
    //load notes into notes container element
    //params: (string)type, (string)id
    //return:
	buildTemplate: function(data, target, template) {
		//hide ajax loader
		//UI.hideSpinner();

		//get template
		var compiled_template = Handlebars.getTemplate(template);

		//load data into element
		$('#' + target).html(compiled_template(data));
	},

	//UI.alert
    //show alert
    //params: (string)message, (function)callback
    //return:
	alert: function(message, callback)
	{
        console.log('UI.alert ' + message);

		//show alert and execute callback
		bootbox.alert(message, function() {
			FUNCTIONS.executeCallback(callback);
		});
	},

	//UI.confirm
    //show confirm
    //params: (string)message, (function)success, (function)failure
    //return:
	confirm: function(message, success, failure)
	{
        console.log('UI.confirm ' + message);

		bootbox.confirm(message, function(result) {
			if(result === true)
			{
				FUNCTIONS.executeCallback(success);
			}
			else
			{
				FUNCTIONS.executeCallback(failure);
			}
		});
	}
};