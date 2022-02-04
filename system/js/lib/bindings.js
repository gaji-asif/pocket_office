BINDINGS = {
	//BINDINGS.bindToWindowSizeChange
    //bind window size change to resize the main container and iframe
    //params:
    //return:
	bindToWindowSizeChange: function(){
        console.log('BINDINGS.bindToWindowSizeChange', arguments);

		$(window).resize(function(){
			UI.refactorLayout();
		});
		//trigger resize on initial bind
		UI.refactorLayout();
	},
	bindToRelEditAccountLink: function(){
		console.log('BINDINGS.bindToRelEditAccountLink', arguments);

		$(document).on('click', '[rel="edit-account-link"]', function(e){
			e.preventDefault();
			var form_id = $(this).data('form-id');
			$('#' + form_id).submit();
		})
	}
};