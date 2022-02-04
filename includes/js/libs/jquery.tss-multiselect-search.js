/**
 * 
 * @author cmitchell
 */
(function($) {
    $.fn.tssMultiSelectSearch = function(options){
        //set default options
        var defaultOptions = {
            placeholder: 'No Options Selected',
            displayLimit: 0,
            displayOptionText: true,
            inputPlaceholder: 'Filter'
        };
        
        //merge with passed options
        options = $.extend(defaultOptions, (typeof options === 'object' ? options : {}));
        
        //close on body click
        $('html').click(function() {
            hideAll();
        });
        
        return this.each(function(){
            //get input into scoped variable
            var inputElement = $(this);
            
            //check if has already had this plugin applied or is missing multiple attribute
            if (inputElement.siblings('div.tss-multiselect-search-btn').length !== 0) {
                return inputElement;
            }
            
            inputElement.addClass('is-tss-multiselect-search');
            
            //hide
            inputElement.hide();
            
            //options
            var allOptions = [];
            getAllOptions();
            
            //get values as array
            var selectedValues = inputElement.val() ? JSON.parse(inputElement.val()) : [];

            //wrap input
            inputElement.wrap('<div class="tss-multiselect-search" />');
            
            //so body click will close only if not within the unordered list
            inputElement.parent().click(function(e){
                e.stopPropagation();
            });
            
            //add button
            var toggleButton = $('<div class="tss-multiselect-search-btn" />');
            inputElement.after(toggleButton);
            
            //create options list
            var optionsList = $('<ul />');
            var searchField = $('<li class="search-field"><input placeholder="' + options.inputPlaceholder + '" /><div class="icons"><i class="icon-ok"></i><i class="icon-remove"></i></div></li>').appendTo(optionsList);
            var selectAllIcon = searchField.find('i.icon-ok');
            var clearAllIcon = searchField.find('i.icon-remove');
            searchField = searchField.find('input');
            buildOptionsList();
            toggleButton.after(optionsList);
            
            //get options list
            var listItems = optionsList.find('li').not('.search-field');
            
            //update selected values
            updateSelectedValues();
            
            setBindings();
            
            //functions
            function handleChange() {
                //update
                selectedValues = inputElement.val();
                if(!_.isArray(selectedValues)) {
                    selectedValues = [selectedValues];
                }
                updateSelectedValues();
            }
            function updateSelectedValues() {
                //set button text
                setButtonText();
                
                //remove selected class from all list items
                listItems.removeClass('selected');
                
                //iterate through all list items and add select class if in selectedValues array
                listItems.each(function(i, item){
                    item = $(item);
                    if (selectedValues.contains(item.data('value'))) {
                        item.addClass('selected');
                    } 
                });
            }
            
            function setButtonText() {
                var buttonTextValues = [];
				var icon = '<i class="icon-remove"></i>';
                $.each(selectedValues, function(i, value){
					buttonTextValues.push("<div class='removeuser'>");
                    //show text value
                    if(options.displayOptionText) {
                        buttonTextValues.push(inputElement.find('[value="'+value+'"]').text()+icon);
                    }
					
                    //show raw option value
                    else {
                        buttonTextValues.push(value+icon);
                    }
					buttonTextValues.push("</div>");
                });
                
                //concat
                var text = buttonTextValues.join('');
                
                //if display limit set
                if (options.displayLimit !== 0) {
                    var tempSelectedValues = [];
                    $.each(buttonTextValues, function(i, value){
                        if (value.length - 3 > options.displayLimit) {
                            tempSelectedValues.push(
                                value.substr(0, options.displayLimit - 3) +icon+ '...'
                            );
                        } else {
                            tempSelectedValues.push(value+icon);
                        }
                    });
                    text = tempSelectedValues.join(', ');
                }
                
                //if nothing selected, use placeholder
                if (text.length === 0) {
                    //text = options.placeholder;
                }
                
                //set it
				//alert(text+'!='+icon);
				if(text!="<div class='removeuser'>"+icon+"</div>"){
                	//toggleButton.html(text).attr('title', text);
					$('.testuser').html(text);
				} else {
					//toggleButton.html(options.placeholder).attr('title', options.placeholder);
					$('.testuser').html(options.placeholder);
				}
				
				
            }
            
            function clearAll() {
                listItems.each(function() {
                    selectedValues.splice(selectedValues.indexOf($(this).data('value')), 1);
                });
                updateSelectedValues();
                inputElement.val(selectedValues).trigger('change');
                searchField.trigger('keyup').focus();
            }
            
            function selectAll() {
                selectedValues = [];
                listItems.each(function() {
                    if(!$(this).hasClass('optgroup')) {
                        selectedValues.push($(this).data('value'));
                    }
                });
                updateSelectedValues();
                inputElement.val(selectedValues).trigger('change');
                searchField.trigger('keyup').focus();
            }
            
            function removeFromSelectValuesArray(value) {
                selectedValues.splice(selectedValues.indexOf(value), 1);
                inputElement.val(selectedValues).trigger('change');
                searchField.focus();
            }
            
            function addValueToSelectValuesArray(value) {
                selectedValues.push(value);
                inputElement.val(selectedValues).trigger('change');
                searchField.focus();
            }
        
            function searchOptions(searchBy) {
                var results = $.grep(allOptions, function(option) {
                    //find the index of the search string
                    return $(option).text().toLowerCase().indexOf(searchBy) !== -1;
                });
                buildOptionsList(results);
            }
        
            function buildOptionsList(data) {
                data = data || allOptions;

                //empty options
                emptyOptionsList();

                //populate options list
                var lastOptGroup = null;
                $.each(data, function() {
                    var option = $(this),
                        value = option.attr('value');
                        
                    if(option.is('option')) {
                        if(!value || !value.length) { return; }
                        $('<li data-value="' + option.attr('value') + '" data-group="' + lastOptGroup + '">' + option.text() + '</li>').appendTo(optionsList);
                    } else {
                        lastOptGroup = option.attr('label');
                        $('<li class="optgroup">' + lastOptGroup + '</li>').appendTo(optionsList);
                    }
                });

                //get new options list
                listItems = optionsList.find('li').not('.search-field');

                //if only one, focus
                if (listItems.length === 1) {
                    listItems.first().addClass('focused');
                }

                updateSelectedValues();
            }

            function emptyOptionsList() {
                if (listItems) {
                    listItems.remove();
                }
            }
            
            function traverseList(event) {
                //get currently focused option
                var focusedOption = $('li.focused', optionsList);
                focusedOption.removeClass('focused');
                
                if (focusedOption.length === 0) {
                    listItems.first().addClass('focused');
                }
                //up
                else if (event.keyCode === 38) {
                    //get prev option
                    var prevOption = focusedOption.prev();
                    
                    //if prev option, add focused class
                    if (prevOption.length !== 0 && !prevOption.hasClass('search-field')) {
                        prevOption.addClass('focused');
                    }
                    //else start at end
                    else {
                        listItems.last().addClass('focused');
                    }
                }
                //down
                else {
                    //get next option
                    var nextOption = focusedOption.next();
                    
                    //if next option, add focused class
                    if (nextOption.length !== 0) {
                        nextOption.addClass('focused');
                    }
                    //else start at beginning
                    else {
                        listItems.first().addClass('focused');
                    }
                }
                
                if($('li.focused', optionsList).hasClass('optgroup')) {
                    traverseList(event);
                }
            }
            
            function getAllOptions() {
                allOptions = $(inputElement.find('option, optgroup'));
            }
            
            function refreshOptions() {
                inputElement.val('');
                getAllOptions();
                buildOptionsList();
                listItems.removeClass('focused').removeClass('selected');
                searchField.val('');
                optionsList.hide();
                clearAll();
            }
            
            function setBindings() {
                //toggle show/hide options list
                toggleButton.click(function(e){
                    e.preventDefault();
                    if (optionsList.is(':visible')) {
                        optionsList.hide();
                    } else {
                        //first hide all others
                        hideAll();

                        //then show
                        optionsList.show();

                        //focus on search
                        searchField.focus().selText().select();
                        searchField.selText().select();
                    }
                });
                //select all text when clicking the input
                searchField.on('click', function(){
                    searchField.selText().select();
                });
                //click an option
                optionsList.on('click', 'li:not(.optgroup)', function(e){
                    e.preventDefault();
                    var item = $(this);

                    //ignore the search field
                    if ($(this).hasClass('search-field')) {
                        return false;
                    }

                    //remove if has class selected
                    if (item.hasClass('selected')) {
                        removeFromSelectValuesArray(item.data('value'));
                    }
                    //add if doesn't
                    else {
                        addValueToSelectValuesArray(item.data('value'));
                    }

                    //update
                    updateSelectedValues();
                });
                searchField.on('keyup', function(e){
                    //tab, enter, up, down
                    if (e.keyCode === 9 || e.keyCode === 13 || e.keyCode === 38 || e.keyCode === 40) {
                        return false;
                    } else if(e.keyCode === 27) {

                        return false;
                    }

                    //get value
                    var searchBy = searchField.val();

                    //search
                    searchOptions(searchBy);
                });
                //bind to tab and enter
                optionsList.on('keydown', 'li', function(e){
                    switch (e.keyCode) {
                        //tab
                        case 9:
                            break;
                        //enter
                        case 13:
                            //get focused option
                            var option = optionsList.find('.focused');

                            //trigger click
                            optionsList.find('.focused').trigger('click');

                            break;
                        //up
                        case 38:
                            //traverse
                            traverseList(e);
                            return false;
                            break;
                        //down
                        case 40:
                            //traverse
                            traverseList(e);
                            return false;
                            break;
                        //escape
                        case 27:

                            return false;
                            break;
                    };
                });

                //bind to select all
                clearAllIcon.click(clearAll);

                //bind to select all
                selectAllIcon.click(selectAll);

                //bind to change
                inputElement.change(handleChange);

                //refresh options
                inputElement.on('refresh-options', refreshOptions);
            }
        });
        
        function hideAll() {
            $('.tss-multiselect-search ul, .tss-select-search ul').hide();
        }
    };
}(jQuery));