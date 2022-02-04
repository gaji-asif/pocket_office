/**
 * 
 * @author cmitchell
 */
(function($) {
    $.fn.tssSelectSearch = function(options){
        //set default options
        var defaultOptions = {
            placeholder: 'No Option Selected',
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
            
            //check if has already had this plugin applied
            if (inputElement.siblings('div.tss-select-search-btn').length !== 0) {
                return inputElement;
            }
            
            //hide
            inputElement.hide();
            
            //options
            var allOptions = [];
            getAllOptions();

            //wrap input
            inputElement.wrap('<div class="tss-select-search" />');
            
            //so body click will close only if not within the unordered list
            inputElement.parent().click(function(e){
                e.stopPropagation();
            });
            
            //add button
            var toggleButton = $('<div class="tss-select-search-btn" />');
            inputElement.after(toggleButton);
            
            //create options list
            var optionsList = $('<ul />');
            var searchField = $('<li class="search-field"><input placeholder="' + options.inputPlaceholder + '" /><div class="icons"><i class="icon-remove"></i></div></li>').appendTo(optionsList);
            var clearAllIcon = searchField.find('i.icon-remove');
            searchField = searchField.find('input');
            buildOptionsList();
            toggleButton.after(optionsList);
            
            //get options list
            var listItems = optionsList.find('li').not('.search-field');
            
            setBindings();
            
            //functions
            function updateSelectedValue() {
                //set button text
                setButtonText();
                
                //remove selected class from all list items
                listItems.removeClass('selected');
                
                //style selected value
                optionsList.find('[data-value="' + inputElement.val() + '"]').addClass('selected');
                
                //close list
                optionsList.hide();
            }
            
            function setButtonText() {
                var selectedOptionEl = optionsList.find('[data-value="' + inputElement.val() + '"]'),
                    text = options.displayOptionText ? selectedOptionEl.text() : inputElement.val();
                
                //if nothing selected, use placeholder
                if (!text.length) {
                    text = options.placeholder;
                }
                
                //set it
                toggleButton.text(text).attr('title', text);
            }
            
            function clearAll() {
                inputElement.val('').trigger('change');
                optionsList.hide();
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
            }
            
            function setBindings() {
                //update selected value
                inputElement.change(updateSelectedValue).change();

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
                        inputElement.val('');
                    }
                    //add if doesn't
                    else {
                        inputElement.val(item.data('value'));
                    }

                    inputElement.change();
                    optionsList.hide();

                });

                //search field keyup
                searchField.on('keyup', function(e){
                    //tab, enter, up, down
                    if (e.keyCode === 9 || e.keyCode === 13 || e.keyCode === 38 || e.keyCode === 40) {
                        return false;
                    } else if(e.keyCode === 27) {
                        hideAll();
                        return false;
                    }

                    //get value
                    var searchBy = searchField.val();

                    //search
                    searchOptions(searchBy);
                });

                //tab and enter on options list
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
                            hideAll();
                            return false;
                            break;
                    };
                });

                //bind to select all
                clearAllIcon.click(clearAll);
                
                //refresh options
                inputElement.on('refresh-options', refreshOptions);
            }
        });
        
        function hideAll() {
            $('.tss-multiselect-search ul, .tss-select-search ul').hide();
        }
    };
}(jQuery));