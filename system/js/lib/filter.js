var FILTER = {
    //FILTER.filterList
    //
    //params: (string)source, (string)target, (string)url, (boolean)clear_list
    //return:
	filterList: function(source, target, url, clear_list) {
        console.log('FILTER.filterList');

		//get source element
		var source = $('#' + source);

		if(source.length != 0)
		{
			//reset all filter list inputs if needed
			var query_string_params = '';
			if(clear_list === true)
			{
				FILTER.resetAllFilterListInputs(source);
			}
			//get all filter input values
			else
			{
				var values = {};
				$('.list-filter-input', source).each(function(){
					values[$(this).attr('id')] = $(this).val();
				});
				query_string_params = $.param(values)
			}

			//get filtered list
			UI.fetchAndLoadAjaxContent(url + '?' + query_string_params, target);
		}
	},

	//FILTER.resetAllFilterListInputs
    //
    //params: (string)source
    //return:
	resetAllFilterListInputs: function(source) {
        console.log('FILTER.filterList ' + source);

		//iterate through all list filter inputs and reset
		$('.list-filter-input', $(source)).each(function(){
			if(!$(this).hasClass('ignore-filter-reset'))
			{
				if($(this).is('select'))
				{
					$(this).prop('selectedIndex', 0);
					$(this).change();
				}
				else
				{
					$(this).val('');
				}
			}
		});
	}
};