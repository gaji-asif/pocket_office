var Search = {
    
    init: function() {
        this.suggestionList = $('#global-search-suggest');
        this.input = $('#global-search-input');
        this.input.on('keyup', _.debounce(this.initGlobalSearchSuggest, 500));
        $(document).on('click', this.resetSuggestions);
        $(document).on('click', '[rel="select-search-suggestions"]', function(e) {
            e.stopPropagation();

            var url = $(this).data('url');
            setFrameLocation(url);
            Search.resetSearch();
        });
    },
    
    initGlobalSearchSuggest: function(e) {
        e.stopPropagation();
        
        var el = $(this),
            searchTerm = el.val();
        if(searchTerm.length < 3 || e.keyCode === 27) {
            Search.resetSuggestions();
            return;
        }
        
        Search.fetchSuggestions(searchTerm);
    },
    
    resetSuggestions: function(e) {
        Search.suggestionList.hide().empty();
        if(parent) {
            parent.Search.suggestionList.hide().empty();
        }
    },
    
    fetchSuggestions: function(searchTerm) {
        Request.addLoaderToElement(this.suggestionList);
        this.suggestionList.show().load(GLOBALS.ajax_dir + '/global_search_suggest.php?s=' + escape(searchTerm));
    },
            
    resetSearch: function() {
        this.input.val('').trigger('keyup');
    }
    
};