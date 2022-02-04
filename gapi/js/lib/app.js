//create app object
var APP = {};

//disable console if needed
if(CONFIG.APP.debug !== true)
{
	var console = {
		log: function(){}
	}
}

//document ready
$(document).ready(function() {
	console.log('document ready');

	//pre select elements
	CONFIG.preSelectElements();

	//bindings
	BINDINGS.bindToRelEditAccountLink();

	//suppliers
//	UI.fetchJsonAndBuildTemplate(GLOBALS.api_dir + '/list/suppliers', 'suppliers-tab', 'lists/suppliers', {
//		post_vars: {limit_start: 0, limit_num: 5},
//		show_spinner: true
//	});
});

//window load
$(window).load(function() {
	console.log('window load');

	//bindings
	BINDINGS.bindToWindowSizeChange();
});
